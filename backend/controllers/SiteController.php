<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\services\AppleManageService;
use common\public_services\UserRoleServiceInterface;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

class SiteController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly UserRoleServiceInterface $userRoleService,
        private readonly AppleManageService $appleManageService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'ajax-generate',
                            'ajax-fall-down',
                            'ajax-eat',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'ajax-generate' => ['post'],
                    'ajax-fall-down' => ['post'],
                    'ajax-eat' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    public function actionIndex(): string
    {
        $userId = $this->userRoleService->getCurrentUserId();

        $appleDtos = $this->appleManageService->findActiveDtosByUserId($userId);

        return $this->render('index', [
            'appleDtos' => $appleDtos,
            'userId' => $userId,
        ]);
    }

    public function actionAjaxGenerate(int $userId): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax) {
            Yii::$app->response->statusCode = 400;
            return [];
        }

        $responseDto = $this->appleManageService->generate($userId);

        return [
            'html' => $this->renderPartial('_apples_content', [
                'status' => $responseDto->getStatus(),
                'message' => $responseDto->getMessage(),
                'appleDtos' => $responseDto->getAppleDtos(),
                'userId' => $userId,
            ])
        ];
    }

    public function actionAjaxFallDown(int $userId, int $appleId): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax) {
            Yii::$app->response->statusCode = 400;
            return [];
        }

        $responseDto = $this->appleManageService->fallDown($userId, $appleId);

        return [
            'html' => $this->renderPartial('_apples_content', [
                'status' => $responseDto->getStatus(),
                'message' => $responseDto->getMessage(),
                'appleDtos' => $responseDto->getAppleDtos(),
                'userId' => $userId,
            ]),
        ];

    }

    public function actionAjaxEat(int $userId, int $appleId): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax) {
            Yii::$app->response->statusCode = 400;
            return [];
        }

        $size = Yii::$app->request->post('percent');

        $responseDto = $this->appleManageService->eat($userId, $appleId, (int)$size);

        return [
            'html' => $this->renderPartial('_apples_content', [
                'status' => $responseDto->getStatus(),
                'message' => $responseDto->getMessage(),
                'appleDtos' => $responseDto->getAppleDtos(),
                'userId' => $userId,
            ]),
        ];
    }
}
