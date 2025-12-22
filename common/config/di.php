<?php

return [
    'definitions' => [
        common\public_services\UserAuthServiceInterface::class => common\services\UserAuthService::class,
        common\public_services\UserRoleServiceInterface::class => common\services\UserRoleService::class,
        common\public_services\UserServiceInterface::class => common\services\UserService::class,
        common\public_services\PasswordServiceInterface::class => common\services\PasswordService::class,
    ],
];