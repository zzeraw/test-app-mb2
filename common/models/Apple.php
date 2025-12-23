<?php

namespace common\models;

use common\dtos\models\AppleDto;
use common\enums\AppleColorEnum;
use common\enums\AppleStatusEnum;
use DateTimeImmutable;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $user_id
 * @property string $color
 * @property int $size_percent
 * @property string $status
 * @property string $appeared_at
 * @property string|null $fell_at
 * @property string $created_at
 * @property string $updated_at
 * @property int $is_archive
 */
class Apple extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'apples';
    }

    /**
     * @return array<int, array<int|string, mixed>>
     */
    public function rules(): array
    {
        return [
            ['user_id', 'required'],
            ['user_id', 'integer'],
            [
                'user_id',
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => [
                    'user_id' => 'id',
                ],
            ],

            ['color', 'required'],
            ['color', 'string', 'max' => 32],
            ['color', 'in', 'range' => array_column(AppleColorEnum::cases(), 'value')],

            ['size_percent', 'required'],
            ['size_percent', 'integer', 'min' => 0, 'max' => 100],

            ['status', 'required'],
            ['status', 'string', 'max' => 10],
            ['status', 'in', 'range' => array_column(AppleStatusEnum::cases(), 'value')],

            ['appeared_at', 'required'],
            ['appeared_at', 'safe'],

            ['fell_at', 'safe'],

            ['created_at', 'required'],
            ['created_at', 'safe'],

            ['updated_at', 'required'],
            ['updated_at', 'safe'],

            ['is_archive', 'required'],
            ['is_archive', 'integer'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'color' => 'Цвет',
            'size_percent' => 'Размер (в процентах)',
            'status' => 'Статус',
            'appeared_at' => 'Дата появления яблока',
            'fell_at' => 'Дата падения яблока',
            'created_at' => 'Дата создания записи',
            'updated_at' => 'Дата редактирования записи',
            'is_archive' => 'Запись в архиве',
        ];
    }

    public function setUserId(int $userId): void
    {
        $this->user_id = $userId;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function setSizePercent(int $sizePercent): void
    {
        $this->size_percent = $sizePercent;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setAppearedAt(string $appearedAt): void
    {
        $this->appeared_at = $appearedAt;
    }

    public function setFellAt(?string $fellAt): void
    {
        $this->fell_at = $fellAt;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->created_at = $createdAt;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updated_at = $updatedAt;
    }

    public function setIsArchive(bool $isArchive): void
    {
        $this->is_archive = (int)$isArchive;
    }

    public function getDto(): AppleDto
    {
        return new AppleDto(
            $this->id,
            $this->user_id,
            AppleColorEnum::from($this->color),
            $this->size_percent,
            AppleStatusEnum::from($this->status),
            new DateTimeImmutable($this->appeared_at),
            empty($this->fell_at) ? null : new DateTimeImmutable($this->fell_at),
            new DateTimeImmutable($this->created_at),
            new DateTimeImmutable($this->updated_at),
            (bool)$this->is_archive,
        );
    }
}
