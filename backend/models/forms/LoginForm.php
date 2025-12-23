<?php

namespace backend\models\forms;

use backend\dtos\LoginFormDto;
use yii\base\Model;

class LoginForm extends Model
{
    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */

    public $password;

    /**
     * @var bool
     */
    public $rememberMe = true;

    /**
     * @return array<int, array<int|string, mixed>>
     */
    public function rules(): array
    {
        return [
            ['password', 'required'],

            ['email', 'required'],
            ['email', 'email'],
            ['email', 'filter', 'filter' => 'trim', 'skipOnArray' => true],
            ['email', 'filter', 'filter' => 'strtolower', 'skipOnArray' => true],

            ['rememberMe', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributeLabels(): array
    {
        return [
            'email' => 'Почта',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить меня',
        ];
    }

    public function getDto(): LoginFormDto
    {
        return new LoginFormDto(
            $this->email,
            $this->password,
            $this->rememberMe,
        );
    }
}
