<?php

use backend\enums\ResponseStatusEnum;
use backend\dtos\response\AppleItemResponseDto;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var View $this */
/** @var ResponseStatusEnum $status */
/** @var null|string $message */
/** @var AppleItemResponseDto[] $appleDtos */
/** @var $userId int */

?>
<?php if (ResponseStatusEnum::FAIL === $status) : ?>
    <div class="alert alert-danger mb-0">
        <p>Ошибка</p>
        <?php if (null !== $message) : ?>
            <p><?= $message ?></p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (empty($appleDtos)): ?>
    <div class="alert alert-secondary mb-0">
        Яблок пока нет. Нажмите "Сгенерировать яблоки".
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($appleDtos as $appleDto): ?>
            <?php
            $id = $appleDto->getId();

            $statusLabel = $appleDto->getStatusLabel();
            $colorCode = $appleDto->getColorCode();
            $sizeLabel = $appleDto->getSize();

            $canFall = $appleDto->isCanFall();
            $canEat = $appleDto->isCanEat();
            $isSpoil = $appleDto->isSpoil();

            $spoilMessage = $appleDto->getSpoilMessage();

            $statusClass = $isSpoil
                ? 'text-bg-secondary'
                : ($canFall ? 'text-bg-success' : 'text-bg-warning');

            $colorStyle = 'color: ' . $colorCode . ';';

            $fallUrl = Url::to(['/apple/fall', 'userId' => $userId, 'appleId' => $id]);
            $eatUrl  = Url::to(['/apple/eat', 'userId' => $userId, 'appleId' => $id]);  // POST: appleId, percent
            ?>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body" data-apple_id="<?= Html::encode((string)$id) ?>">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div>
                                <div class="fw-semibold">
                                    <i class="bi bi-circle-fill fs-3" style="<?= Html::encode($colorStyle) ?>"></i>
                                </div>
                            </div>

                            <span class="badge <?= $statusClass ?>">
                                <?= Html::encode($statusLabel) ?>
                            </span>
                        </div>

                        <div class="small mb-2">
                            <?php if (null !== $spoilMessage): ?>
                                <div><span class="text-muted"><?= Html::encode($spoilMessage) ?></span></div>
                            <?php else: ?>
                                <div>&nbsp;</div>
                            <?php endif; ?>

                            <div>Размер: <span class="fw-semibold"><?= Html::encode($sizeLabel) ?></span></div>
                        </div>

                        <?php
                        $sizeNumeric = (float)str_replace('%', '', $sizeLabel);
                        $sizeNumeric = max(0.0, min(100.0, $sizeNumeric));
                        ?>

                        <div class="progress mb-3" role="progressbar"
                             aria-valuenow="<?= (int)round($sizeNumeric) ?>"
                             aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: <?= $sizeNumeric ?>%"></div>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <?= Html::beginForm($fallUrl, 'post', ['class' => 'js-apple-action']) ?>
                                <?= Html::hiddenInput('appleId', (string)$id) ?>
                                <button type="submit"
                                        class="btn btn-outline-warning btn-sm"
                                    <?= $canFall ? '' : 'disabled' ?>>
                                    Упасть
                                </button>
                            <?= Html::endForm() ?>

                            <?= Html::beginForm($eatUrl, 'post', ['class' => 'js-apple-action d-flex gap-2 align-items-center']) ?>
                                <?= Html::hiddenInput('appleId', (string)$id) ?>
                                <input type="number"
                                       name="percent"
                                       class="form-control form-control-sm"
                                       style="max-width: 110px"
                                       min="1"
                                       max="100"
                                       step="1"
                                       value="10"
                                    <?= $canEat ? '' : 'disabled' ?>
                                       placeholder="%">

                                <button type="submit"
                                        class="btn btn-outline-danger btn-sm"
                                    <?= $canEat ? '' : 'disabled' ?>>
                                    Съесть %
                                </button>
                            <?= Html::endForm() ?>
                        </div>

                        <?php if (!$canFall && !$canEat): ?>
                            <div class="mt-3 small text-muted">
                                Действия недоступны для текущего статуса/размера.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    </div>
<?php endif; ?>