<?php

declare(strict_types=1);

namespace console\models\forms;

use console\dtos\SignupFormDto;
use yii\base\Model;

class SignupForm extends Model
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
     * @return array<int, array<int|string, mixed>>
     */
    public function rules(): array
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 8],

            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'filter', 'filter' => 'trim', 'skipOnArray' => true],
            ['email', 'filter', 'filter' => 'strtolower', 'skipOnArray' => true],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributeLabels(): array
    {
        return [
            'password' => 'Пароль',
            'email' => 'Почта',
        ];
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getDto(): SignupFormDto
    {
        return new SignupFormDto(
            $this->email,
            $this->password
        );
    }
}
