<?php

use backend\enums\ResponseStatusEnum;
use backend\dtos\response\AppleItemResponseDto;
use yii\helpers\Url;
use yii\web\View;

/** @var View $this */
/** @var AppleItemResponseDto[] $appleDtos */
/** @var int $userId */

$this->title = 'Яблоки';
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

<div class="toast-container position-fixed top-0 end-0 p-3" id="appToasts"
     style="z-index: 1080;">
</div>

<?php

$loadingHtml = '<div class="d-flex justify-content-center align-items-center py-5">'
        . '<div class="spinner-border" role="status" aria-hidden="true"></div>'
        . '<span class="ms-3">Загрузка...</span>'
        . '</div>';

$this->registerJsVar('applesLoadingHtml', $loadingHtml);
$this->registerJsVar('ajaxGenerateUrl', Url::to(['/site/ajax-generate', 'userId' => $userId]));

$js = <<<JS
(function () {
    let isBusy = false;
    let previousHtml = null;

    function setBusy(state) {
        isBusy = state;
        
        const \$root = $('#siteIndexContainer');

        if (state) {
            \$root.find('button, a.btn').addClass('disabled').attr('aria-disabled', 'true');
        } else {
            \$root.find('button, a.btn').removeClass('disabled').removeAttr('aria-disabled');
        }
    }

    function renderLoading() {
        previousHtml = $('#applesContent').html();
        $('#applesContent').html(applesLoadingHtml);
    }

    function renderHtmlOrRollback(data) {
        if (data && data.html) {
            $('#applesContent').html(data.html);
        } else {
            $('#applesContent').html(previousHtml || '');
            showToast('Некорректный ответ сервера.', 'danger');
        }
    }

    function showToast(message, type = 'danger', delay = 4000) {
        const toastId = 'toast-' + Date.now();
    
        const toastHtml = `
            <div id="\${toastId}" class="toast align-items-center text-bg-\${type} border-0 mb-2"
                 role="alert" aria-live="assertive" aria-atomic="true"
                 data-bs-delay="\${delay}">
                <div class="d-flex">
                    <div class="toast-body">
                        \${message}
                    </div>
                    <button type="button"
                            class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"
                            aria-label="Close"></button>
                </div>
            </div>
        `;
    
        const \$container = $('#appToasts');
        const \$toast = $(toastHtml);
    
        \$container.append(\$toast);
    
        const toast = new bootstrap.Toast(\$toast[0]);
        toast.show();

        \$toast.on('hidden.bs.toast', function () {
            \$toast.remove();
        });
    }
    
    function showErrorFromXhr(xhr) {
        if (
            xhr &&
            xhr.responseJSON &&
            typeof xhr.responseJSON.message === 'string' &&
            xhr.responseJSON.message.trim() !== ''
        ) {
            showToast(xhr.responseJSON.message, 'danger');
        } else {
            showToast('Ошибка запроса. Проверьте сеть и ответ сервера.', 'danger');
        }
    }
    
    function requestAndUpdate(options) {
        if (isBusy) {
            return;
        }

        setBusy(true);
        renderLoading();

        $.ajax(options)
            .done(function (data) {
                renderHtmlOrRollback(data);
            })
            .fail(function (xhr) {
                $('#applesContent').html(previousHtml || '');
                
                try {
                    const resp = xhr.responseJSON;
                    if (resp && resp.html) {
                        $('#applesContent').html(resp.html);
                        return;
                    }
                } catch (e) {}

                showErrorFromXhr(xhr);
            })
            .always(function () {
                setBusy(false);
            });
    }

    $('#siteIndexContainer').on('click', '#generateApplesBtn', function (e) {
        e.preventDefault();

        requestAndUpdate({
            type: 'POST',
            dataType: 'json',
            url: ajaxGenerateUrl
        });
    });

    $('#siteIndexContainer').on('submit', 'form.js-apple-fall', function (e) {
        e.preventDefault();

        const \$form = $(this);

        requestAndUpdate({
            type: 'POST',
            dataType: 'json',
            url: \$form.attr('action'),
            data: \$form.serialize()
        });
    });

    $('#siteIndexContainer').on('submit', 'form.js-apple-eat', function (e) {
        e.preventDefault();

        const \$form = $(this);

        requestAndUpdate({
            type: 'POST',
            dataType: 'json',
            url: \$form.attr('action'),
            data: \$form.serialize()
        });
    });
})();
JS;

$this->registerJs($js);