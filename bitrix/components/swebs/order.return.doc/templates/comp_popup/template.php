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
$this->setFrameMode(false);
?>
<div class="return__popup">
    <? if (!empty($arResult['ERROR'])): ?>
        <?= $arResult['ERROR'] ?>
    <? else: ?>
        <div class="default">

            <div class="return-text">
                <div class="heading">
                    <h2>Возврат по заказу № <?= $arResult['ORDER_ID'] ?></h2>
                <p>Заявление на возврат было отправлено на вашу электронную почту <a
                        href="mailto:<?= $arResult['CLIENT_EMAIL'] ?>"><?= $arResult['CLIENT_EMAIL'] ?></a>
                    подпишите его (если вы Юридическое лицо, поставьте также печать), и пришите нам скан
                    подписанного
                    заявления на адрес электронной почты: <a
                        href="mailto:<?= $arParams['EMAIL'] ?>"><?= $arParams['EMAIL'] ?></a></p>
                </div>

                <div class="result"><span class="icon__download">
                      <svg>
                          <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#download"></use>
                      </svg></span>

                    <p>Заявление на возврат по заказу №<?= $arResult['ORDER_ID'] ?> <br>Документ PDF
                        (<?= $arResult['FILE_SIZE'] ?> кб)</p>
                    <a href="<?= $arResult['FILE_URL'] ?>" class="button type-blue fill size-41">Скачать
                        заявление</a>
                </div>
            </div>

        </div>
    <? endif ?>
</div>
