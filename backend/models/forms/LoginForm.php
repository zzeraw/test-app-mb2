<?php

namespace backend\models\forms;

use backend\dtos\LoginFormDto;
use common\enums\TranslationCategoryEnum;
use Yii;
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
     * {@inheritdoc}
     */
    public function rules()
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t(TranslationCategoryEnum::NAME->value, 'Email'),
            'password' => Yii::t(TranslationCategoryEnum::NAME->value, 'Password'),
            'rememberMe' => Yii::t(TranslationCategoryEnum::NAME->value, 'Remember Me'),
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
