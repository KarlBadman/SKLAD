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
<form action="" method="post">
    <div class="legend">Форма обратной связи</div>
    <fieldset>
        <div class="field__widget type-inline">
            <label for="input-contacts-name" class="label">Контактное лицо:</label>
            <div class="field">
                <div class="input">
                    <div class="placeholder">Напр. Иванов Иван</div>
                    <input id="input-contacts-name" name="<?= $arResult['FIELDS']['NAME'] ?>" type="text" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="field__widget type-inline">
            <label for="input-contacts-phone" class="label">Телефон</label>
            <div class="field">
                <div class="input">
                    <div class="placeholder">+7 900 000-00-00</div>
                    <input id="input-contacts-phone" name="<?= $arResult['FIELDS']['PHONE'] ?>" type="tel" autocomplete="off"
                           data-phonemask>
                </div>
            </div>
        </div>
        <div class="field__widget type-inline field-question">
            <label for="input-question" class="label">Текст сообщения:</label>
            <div class="field">
                <div class="input">
                    <textarea id="input-question" name="<?= $arResult['FIELDS']['TEXT'] ?>" rows="3"
                              autocomplete="off"></textarea>
                </div>
            </div>
        </div>
        <div class="field__widget type-inline field-submit">
            <div class="field">
                <button type="submit" autocomplete="off" class="button type-blue fill size-41">Отправить
                </button>
            </div>
        </div>
    </fieldset>
</form>