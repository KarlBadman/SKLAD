<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<div aria-expanded="false" class="item"><a href="">Перезвоните мне</a>
    <form id="callback-form" action="" novalidate method="get">
        <input type="hidden" name="component" value="callback">
        <input type="hidden" name="template" value=".default">
        <div class="field__widget type-block">
            <label for="input-callback-phone" class="label">Телефон</label>
            <div class="field">
                <div class="input">
                    <input id="input-callback-phone" name="<?= $arResult['FIELDS']['PHONE'] ?>" type="tel" autocomplete="off"
                           data-phonemask>
                </div>
            </div>
        </div>
        <div class="field__widget type-block">
            <label for="input-callback-name" class="label">Имя</label>
            <div class="field">
                <div class="input">
                    <input id="input-callback-name" name="<?= $arResult['FIELDS']['NAME'] ?>" type="text" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-submit">
            <div class="field">
                <button type="submit" id="CallbackFrm-btn-id" autocomplete="off" class="button type-blue fill size-30 g-recaptcha" data-sitekey="6LfhxiEUAAAAAETT-QoNcUjpNs3hP1QDmQhZxN9F" data-callback='onSubmitCallbackFrm' >
                    Отправить заявку
                </button>
            </div>
        </div>
    </form>
    <script>
        function onSubmitCallbackFrm(token) {
            console.log(token);
            document.getElementById("callback-form").submit();
        }
    </script>
</div>