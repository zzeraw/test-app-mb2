<?php

use backend\enums\ResponseStatusEnum;
use backend\dtos\response\AppleItemResponseDto;
use yii\helpers\Url;
use yii\web\View;

/** @var View $this */
/** @var AppleItemResponseDto[] $appleDtos */
/** @var int $userId */

$this->title = 'My Yii Application';
?>
<div class="site-index" id="siteIndexContainer">

    <div class="jumbotron text-center bg-transparent mb-5">
        <h1 class="display-4">Яблоки</h1>
        <p class="lead">Сгенерируйте яблоки и управляйте каждым, нажимая "упасть" или "съесть".</p>
        <a id="generateApplesBtn" class="btn btn-lg btn-success" href="#">Сгенерировать яблоки</a>
    </div>

    <div class="body-content">
        <div id="applesContent">
            <?= $this->render('_apples_content', [
                'status' => ResponseStatusEnum::SUCCESS,
                'message' => null,
                'appleDtos' => $appleDtos,
                'userId' => $userId,
            ]) ?>
        </div>
    </div>
</div>

<?php

$loadingHtml = '<div class="d-flex justify-content-center align-items-center py-5">'
        . '<div class="spinner-border" role="status" aria-hidden="true"></div>'
        . '<span class="ms-3">Генерирую яблоки...</span>'
        . '</div>';
$this->registerJsVar('applesLoadingHtml', $loadingHtml);
$this->registerJsVar('ajaxGenerateUrl', Url::to(['/site/ajax-generate', 'userId' => 1]));

$js = <<<JS
(function () {
    let isGenerating = false;
    let previousHtml = null;

    $('#siteIndexContainer').on('click', '#generateApplesBtn', function (e) {
        e.preventDefault();
        if (isGenerating) {
            return;
        }

        isGenerating = true;

        previousHtml = $('#applesContent').html();
        
        $('#applesContent').html(applesLoadingHtml);
        
        let \$btn = $(this);
        \$btn.addClass('disabled');
        \$btn.attr('aria-disabled', 'true');

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajaxGenerateUrl
        })
        .done(function (data) {
            $('#applesContent').html(data.html);
        })
        .fail(function () {
            $('#applesContent').html(previousHtml || '');
            alert('Не удалось сгенерировать яблоки. Проверьте сеть и ответ сервера.');
        })
        .always(function () {
            isGenerating = false;
            \$btn.removeClass('disabled');
            \$btn.removeAttr('aria-disabled');
        });
    });
})();
JS;

$this->registerJs($js);