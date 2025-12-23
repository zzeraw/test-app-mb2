<?php

declare(strict_types=1);

namespace console\controllers;

use console\services\SignupService;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\BaseConsole;

class UserController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly SignupService $signupService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * php yii user/create
     */
    public function actionCreate(
        string $email,
        string $password
    ): int {
        $this->stdout(
            sprintf(
                'Creating user: %s / %s',
                $email,
                $password,
            ),
            BaseConsole::NORMAL
        );
        $this->stdout(PHP_EOL);

        $signupResult = $this->signupService->signup(
            $email,
            $password,
        );

        if (true === $signupResult) {
            $this->stdout('OK', BaseConsole::BG_GREEN);
        } else {
            $this->stdout('FAIL', BaseConsole::BG_RED);
        }

        $this->stdout(PHP_EOL);

        return ExitCode::OK;
    }
}
