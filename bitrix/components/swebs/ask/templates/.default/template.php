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

<div aria-expanded="false" class="item"><a href="">Задать вопрос</a>
    <form id="ask-form" action="" novalidate method="post">
        <input type="hidden" name="component" value="ask">
        <div class="field__widget type-block">
            <label for="input-ask-question" class="label">Вопрос</label>
            <div class="field">
                <div class="input">
                                        <textarea id="input-ask-question" name="<?= $arResult['FIELDS']['TEXT'] ?>" rows="4"
                                                  autocomplete="off"></textarea>
                </div>
            </div>
        </div>
        <div class="field__widget type-block">
            <label for="input-ask-name" class="label">Имя</label>
            <div class="field">
                <div class="input">
                    <input id="input-ask-name" name="<?= $arResult['FIELDS']['NAME'] ?>" type="text" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="field__widget type-block">
            <label for="input-ask-email" class="label">Электронная почта</label>
            <div class="field">
                <div class="input">
                    <input id="input-ask-email" name="<?= $arResult['FIELDS']['EMAIL'] ?>" type="email" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-submit">
            <div class="field">
                <button type="submit" id="AskFrm-btn-id" autocomplete="off"  data-count="0"  class="button type-blue fill size-30 active g-recaptcha" data-sitekey="6LfhxiEUAAAAAETT-QoNcUjpNs3hP1QDmQhZxN9F" data-callback='onSubmitAskFrm'>
                    Отправить вопрос
                </button>

<!--                <button type="submit" autocomplete="off" class="button type-blue fill size-30 g-recaptcha" data-sitekey="6LfhxiEUAAAAAETT-QoNcUjpNs3hP1QDmQhZxN9F" data-callback="onSubmitCallbackFrm">-->
<!--                    Отправить заявку-->
<!--                </button>-->
            </div>
        </div>
        <script>
            function onSubmitAskFrm(token) {
                console.log(token);
                document.getElementById("ask-form").submit();
            }
        </script>
    </form>

</div>