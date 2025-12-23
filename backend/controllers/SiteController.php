<?php

declare(strict_types=1);

namespace backend\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ErrorAction;

class SiteController extends Controller
{
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionAjaxGenerate(int $userId): array
    {
        
    }

    public function actionAjaxFallDown(int $userId, int $appleId): array
    {

    }

    public function actionAjaxEat(int $userId, int $appleId, int $size): array
    {

    }
}
