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
    <h2>Предзаказ</h2>
    <p class="align-center">Оставьте телефон и наш менеджер свяжется с вами для уточнения деталей предзаказа.</p>
    <form id="preorder-form" action="" novalidate="novalidate" method="post">
        <input type="hidden" name="component" value="callback">
        <input type="hidden" name="template" value="preorder">
        <input id="preorder-good-id" type="hidden" name="good-id" value="0">
        <div class="field__widget type-block field-name">
            <label for="input-fastbuy-name" class="label">Имя:</label>
            <div class="field">
                <div class="input">
                    <input type="text" id="input-fastbuy-name" autocomplete="off" name="<?= $arResult['FIELDS']['PHONE'] ?>"/>
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-phone">
            <label for="input-fastbuy-phone" class="label">Телефон:</label>
            <div class="field">
                <div class="input">
                    <input type="tel" id="input-fastbuy-phone" autocomplete="off" name="<?= $arResult['FIELDS']['NAME'] ?>" data-phonemask="data-phonemask"/>
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-submit align-right">
            <div class="field">
                <button type="submit" id="PreorderFrm-btn-id" autocomplete="off" class="button type-blue fill size-41" data-sitekey="6LfhxiEUAAAAAETT-QoNcUjpNs3hP1QDmQhZxN9F" data-callback="onSubmitPreorderFrm">Отправить</button>
            </div>
        </div>
    </form>
    <script>
        function onSubmitPreorderFrm(token) {
            console.log(token);
            document.getElementById("preorder-form").submit();
        }
    </script>
</div>
<?/*
<div aria-expanded="false" class="item"><a href="">Перезвоните мне</a>
    <form action="" novalidate method="post">
        <input type="hidden" name="component" value="callback">
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
                <button type="submit" autocomplete="off" class="button type-blue fill size-30">
                    Отправить заявку
                </button>
            </div>
        </div>
    </form>
</div>