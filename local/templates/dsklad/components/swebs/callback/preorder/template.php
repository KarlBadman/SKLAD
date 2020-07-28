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
<div class="signup__popup" id="preorder-form-container">
    <h2>Сообщить о поступлении</h2>
    <p class="align-center">Оставьте телефон и наш менеджер свяжется с вами.</p>
    <form id="preorder-form" action="#" novalidate="novalidate" method="post">
        <input type="hidden" name="component" value="callback">
        <input type="hidden" name="template" value="preorder">
        <input id="preorder-good-id" type="hidden" name="good-id" value="0">
        <div class="field__widget type-block field-name">
            <label for="input-fastbuy-name" class="label">Имя:</label>
            <div class="field">
                <div class="input">
                    <input type="text" id="input-fastbuy-name" autocomplete="off" name="<?= $arResult['FIELDS']['NAME'] ?>"/>
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-phone">
            <label for="input-fastbuy-phone" class="label">Телефон:</label>
            <div class="field">
                <div class="input">
                    <input type="tel" id="input-fastbuy-phone" autocomplete="off" name="<?= $arResult['FIELDS']['PHONE'] ?>" data-phonemask="data-phonemask"/>
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-email">
            <label for="input-fastbuy-email" class="label">Электронная почта:</label>
            <div class="field">
                <div class="input">
                    <input type="text" id="input-fastbuy-email" autocomplete="off" name="email" required />
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-submit align-right">
            <div class="field">
                <button type="submit" id="PreorderFrm-btn-id" class="button type-blue fill size-41" data-sitekey="6LfhxiEUAAAAAETT-QoNcUjpNs3hP1QDmQhZxN9F" data-callback="onSubmitPreorderFrm">Отправить</button>
            </div>
        </div>
    </form>
    <script>
        function onSubmitPreorderFrm(token) {
            document.getElementById("preorder-form").submit();
        }
    </script>
</div>