<?php

declare(strict_types=1);

namespace console\models\forms;

use common\enums\TranslationCategoryEnum;
use console\dtos\SignupFormDto;
use Yii;
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
     * @inheritdoc
     */
    public function rules()
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => Yii::t(TranslationCategoryEnum::NAME->value, 'Password'),
            'email' => Yii::t(TranslationCategoryEnum::NAME->value, 'Email'),
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
