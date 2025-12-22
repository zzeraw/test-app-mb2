<?php

$db = [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=' . env('ENV_PARAM_DB_HOST') . ';port=' . env('ENV_PARAM_DB_PORT') . ';dbname=' . env('ENV_PARAM_DB_NAME'),
    'username' => env('ENV_PARAM_DB_USER'),
    'password' => env('ENV_PARAM_DB_PASS'),
    'charset' => 'utf8',
];

return $db;