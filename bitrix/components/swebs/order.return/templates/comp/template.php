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
    <form action="" class="send_return_form">
        <input type="hidden" name="order_id" value="<?= $arResult['ID'] ?>">
        <div class="heading">
            <h2>Возврат по заказу № <?= $arResult['ID'] ?></h2>
            <p>Выберите товары и их количество к возврату, а также заполните данные для подачи заявления, которое вы сможете скачать на следующем шаге.</p>
        </div>
        <div class="goods">
            <? foreach ($arResult['BASKET'] as $arItem): ?>
                <div class="item">
                    <div class="image"><img src="<?= $arItem['IMAGE'] ?>" alt="<?= $arItem['NAME'] ?>"/></div>
                    <div class="info">
                        <div class="category"><a href="<?= $arItem['SECTION_URL'] ?>"><?= $arItem['SECTION_NAME'] ?></a></div>
                        <div class="name"><a href="<?= $arItem['NAME_URL'] ?>"><?= $arItem['NAME'] ?></a></div>
                        <div class="data">
                            <p class="article"><span>Артикул:</span> <?= $arItem['ARTICLE'] ?></p>
                            <p class="value"><span>Кол-во:</span> <?= $arItem['QUANTITY'] ?> шт.</p>
                            <p class="color"><span>Цвет:</span> <?= $arItem['COLOR'] ?></p>
                        </div>
                    </div>
                    <div class="count select_count_return">
                        <div data-min="1" data-measure=" шт." class="counter__widget">
                            <a href="#" class="minus_count_return" data-add="-1">-</a>
                            <div class="input">
                                <input type="tel" name="good[<?= $arItem['ID'] ?>]" autocomplete="off" readonly value="<?= $arItem['QUANTITY'] ?> шт."/>
                            </div>
                            <a href="#" class="plus_count_return" data-add="1">+</a>
                        </div>
                        <input type="hidden" value="<?= $arItem['QUANTITY']?>" class="max_count_return">
                        <? $strPriceItem = str_replace(" ","",$arItem['PRICE']); ?>
                        <input type="hidden" value="<?= (integer)$strPriceItem?>" class="price_count_return">
                        <input type="hidden" value="<?= $arItem['QUANTITY']?>" class="inp_select_count_return">
                    </div>
                    <div class="total"><span>Всего:</span> <b><?= $arItem['FINAL_PRICE'] ?></b></div>
                </div>
            <? endforeach ?>
            <div class="money">Cумма возврата: <span><?= $arResult['TOTAL_SUMM'] ?></span></div>
        </div>
        <fieldset>
            <div class="legend">Причина возврата:</div>
            <div class="field__widget type-inline">
                <label for="input-return-comment" class="label">Кратко опишите причину:</label>
                <div class="field">
                    <div class="input">
                        <textarea id="input-return-comment" name="comment" rows="3"
                                  placeholder="Напр. не подошел цвет, не устроило качество товара, дефект товара."></textarea>
                    </div>
                </div>
            </div>
        </fieldset>
        <fieldset>
            <div class="legend">Реквизиты для возврата:</div>
            <div class="field__widget type-inline">
                <label for="input-return-name" class="label">ФИО владельца счёта:</label>
                <div class="field">
                    <div class="input">
                        <input id="input-return-name" type="text" name="name" placeholder="Напр. Иванов Иван Иванович" autocomplete="off"/>
                    </div>
                </div>
            </div>
            <div class="field__widget type-inline">
                <label for="input-return-bank" class="label">Наименование банка:</label>
                <div class="field">
                    <div class="input">
                        <input id="input-return-bank" type="text" name="bank" placeholder="Напр. Напр. ООО “Сбербанк”" autocomplete="off"/>
                    </div>
                </div>
            </div>
            <div class="field__widget type-inline field-bik">
                <label for="input-return-bik" class="label">БИК банка:</label>
                <div class="field">
                    <div class="input">
                        <input id="input-return-bik" type="tel" name="bik" autocomplete="off"/>
                    </div>
                </div>
            </div>
            <div class="field__widget type-inline field-operating-account">
                <label for="input-return-operating-account" class="label">Расчетный счёт:</label>
                <div class="field">
                    <div class="input">
                        <input id="input-return-operating-account" type="tel" name="operating-account" autocomplete="off"/>
                    </div>
                    <p>Для юридических лиц</p>
                </div>
            </div>
            <div class="field__widget type-inline field-card">
                <label for="input-return-card" class="label">№ банковской карты:</label>
                <div class="field">
                    <div class="input">
                        <input id="input-return-card" type="tel" name="card" autocomplete="off"/>
                    </div>
                    <span class="icon__help">
            <svg>
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#help"></use>
            </svg></span>
                </div>
            </div>
        </fieldset>
        <div class="submit">
            <button type="submit" class="btn_send_return_form button type-blue fill size-41">Отправить</button>
        </div>
    </form>
</div>