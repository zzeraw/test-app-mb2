<?php

declare(strict_types=1);

namespace common\models;

use common\dtos\models\UserDto;
use common\enums\TranslationCategoryEnum;
use common\enums\UserStatusEnum;
use common\public_interfaces\UserDtoInterface;
use DateTimeImmutable;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * @property int $id
 * @property string $email
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'users';
    }

    /**
     * @return array<int, array<int|string, mixed>>
     */
    public function rules(): array
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],

            ['auth_key', 'required'],
            ['auth_key', 'string', 'max' => 32],

            ['password_hash', 'required'],
            ['password_hash', 'string', 'max' => 255],

            ['password_reset_token', 'string', 'max' => 255],

            ['status', 'integer'],
            ['status', 'default', 'value' => UserStatusEnum::ACTIVE->value],
            ['status', 'in', 'range' => array_column(UserStatusEnum::cases(), 'value')],

            ['created_at', 'required'],
            ['created_at', 'string'],

            ['updated_at', 'required'],
            ['updated_at', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t(TranslationCategoryEnum::NAME->value, 'ID'),
            'email' => Yii::t(TranslationCategoryEnum::NAME->value, 'Email'),
            'auth_key' => Yii::t(TranslationCategoryEnum::NAME->value, 'Auth Key'),
            'password_hash' => Yii::t(TranslationCategoryEnum::NAME->value, 'Password Hash'),
            'password_reset_token' => Yii::t(TranslationCategoryEnum::NAME->value, 'Reset Password Hash'),
            'status' => Yii::t(TranslationCategoryEnum::NAME->value, 'Status'),
            'created_at' => Yii::t(TranslationCategoryEnum::NAME->value, 'Created At'),
            'updated_at' => Yii::t(TranslationCategoryEnum::NAME->value, 'Updated At'),
        ];
    }

    public function getDto(): UserDtoInterface
    {
        return new UserDto(
            $this->id,
            $this->email,
            $this->auth_key,
            $this->password_hash,
            $this->password_reset_token,
            UserStatusEnum::from($this->status),
            new DateTimeImmutable($this->created_at),
            new DateTimeImmutable($this->updated_at),
        );
    }

    /**
     * Setters
     */

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function setAuthKey(string $authKey): void
    {
        $this->auth_key = $authKey;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->password_hash = $passwordHash;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->created_at = $createdAt;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updated_at = $updatedAt;
    }

    public function setPasswordResetToken(?string $passwordResetToken): void
    {
        $this->password_reset_token = $passwordResetToken;
    }

    /**
     * implements IdentityInterface
     */

    /**
     * @param int|string $id
     *
     * @return User|IdentityInterface|null
     */
    public static function findIdentity($id)
    {
        return static::findOne([
            'id' => $id,
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }
}
