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
    <pre>
    <?// print_r($_REQUEST) ?>
</pre>

<? if (!empty($arResult['ERROR'])): ?>
    <?= $arResult['ERROR'] ?>
<? else: ?>
    <div class="cabinet__page">
        <div class="default">
            <section class="heading">
                <div class="title">
                    <h1>Заказ № <?= $arResult['ORDER_ID'] ?></h1>
                </div>
            </section>
        </div>
        <section class="data">
            <div class="tabs__widget">
                <div class="tabs-handler">
                    <ul class="default">
                        <li class="active"><a href=""><span class="hidden-s">История заказов</span> <span
                                    class="hidden-gt-s">Заказы</span></a></li>
                        <li><a href="/personal/?tab_like"><span class="hidden-s">Избранные товары</span> <span
                                    class="hidden-gt-s">Избранное</span></a></li>
                        <li><a href="/personal/?tab_settings""><span class="hidden-s">Личные настройки</span> <span
                                class="hidden-gt-s">Настройки</span></a></li>
                    </ul>
                </div>
            </div>
        </section>
        <div class="default">
            <section class="goods">
                <div class="return-text">
                    <p>Заявление на возврат было отправлено на вашу электронную почту <a
                            href="mailto:<?= $arResult['CLIENT_EMAIL'] ?>"><?= $arResult['CLIENT_EMAIL'] ?></a>
                        подпишите его (если вы Юридическое лицо, поставьте также печать), и пришите нам скан подписанного
                        заявления на адрес электронной почты: <a href="mailto:<?= $arParams['EMAIL'] ?>"><?= $arParams['EMAIL'] ?></a></p>

                    <div class="result"><span class="icon__download">
                      <svg>
                          <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#download"></use>
                      </svg></span>

                        <p>Заявление на возврат по заказу №<?= $arResult['ORDER_ID'] ?> <br>Документ PDF (<?= $arResult['FILE_SIZE'] ?> кб)</p>
                        <a href="<?= $arResult['FILE_URL'] ?>" class="button type-blue fill size-41">Скачать заявление</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
<? endif ?>