<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\models\forms\LoginForm;
use backend\services\AuthService;
use common\public_services\UserRoleServiceInterface;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

class AuthController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly AuthService $authService,
        private readonly UserRoleServiceInterface $userRoleService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionLogin(): Response|string
    {
        if (!$this->userRoleService->isGuest()) {
            return $this->redirect($this->getSuccessRedirectUrl());
        }

        $loginForm = new LoginForm();

        if (Yii::$app->request->isPost) {
            $authResult = $this->authService->login(
                $loginForm,
                Yii::$app->request->post()
            );

            if (true === $authResult) {
                return $this->redirect($this->getSuccessRedirectUrl());
            }
        }

        return $this->render('login', [
            'model' => $loginForm,
        ]);
    }

    public function actionLogout(): Response
    {
        if ($this->userRoleService->isGuest()) {
            return $this->redirect($this->getLoginUrl());
        }

        $this->authService->logout();

        return $this->redirect($this->getLoginUrl());
    }

    private function getSuccessRedirectUrl(): string
    {
        $returnUrl = Yii::$app->user->getReturnUrl();

        if (null !== $returnUrl) {
            return $returnUrl;
        } else {
            return Url::to(['/site/index']);
        }
    }

    private function getLoginUrl(): string
    {
        return Url::to(['/auth/login']);
    }
}
