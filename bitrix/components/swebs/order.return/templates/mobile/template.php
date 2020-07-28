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
<div class="cabinet__page">
    <form action="/personal/success_return_mob.php" method="post">
        <input type="hidden" name="order_id" value="<?= $arResult['ID'] ?>">
        <div class="default">
            <section class="heading">
                <div class="title">
                    <h1>Возврат по заказу №<?= $arResult['ID'] ?></h1>
                </div>
            </section>
        </div>
        <section class="data">
            <div class="tabs__widget">
                <div class="tabs-handler">
                    <ul class="default">
                        <li class="active"><a href=""><span class="hidden-s">История заказов</span> <span class="hidden-gt-s">Заказы</span></a></li>
                        <li><a href="/personal/?tab_like"><span class="hidden-s">Избранные товары</span> <span class="hidden-gt-s">Избранное</span></a></li>
                        <li><a href="/personal/?tab_settings"><span class="hidden-s">Личные настройки</span> <span class="hidden-gt-s">Настройки</span></a></li>
                    </ul>
                </div>
            </div>
        </section>
        <div class="default">
            <section class="goods">
                <div class="return-text">
                    <p>Выберите товары и их количество к возврату, а также заполните данные для подачи заявления, которое вы сможете скачать на следующем
                        шаге.</p>
                </div>
                <div class="basket__widget">
                    <div class="list">
                        <? foreach ($arResult['BASKET'] as $arItem): ?>
                            <div class="item">
                                <div class="image"><img src="<?= $arItem['IMAGE'] ?>" alt="<?= $arItem['NAME'] ?>"></div>
                                <div class="whole-row">
                                    <div class="info-row">
                                        <div class="info">
                                            <div class="category"><a href="<?= $arItem['SECTION_URL'] ?>"><?= $arItem['SECTION_NAME'] ?></a></div>
                                            <div class="name"><a href="<?= $arItem['NAME_URL'] ?>"><?= $arItem['NAME'] ?></a></div>
                                            <div class="data">
                                                <p class="counter-fallback hidden-gt-s"><span>Кол-во:</span>
                                                    <del><?= $arItem['QUANTITY'] ?></del>
                                                </p>
                                                <p class="article"><span>Артикул:</span> <?= $arItem['ARTICLE'] ?></p>
                                                <p class="color"><span>Цвет:</span> <?= $arItem['COLOR'] ?></p>
                                            </div>
                                        </div>
                                        <div class="price">
                                            <span class="hidden-l">Цена за шт.</span> <b><?= $arItem['PRICE'] ?></b><span class="hidden-lte-m">/ шт.</span>
                                            <? if (!empty($arItem['PERCENT'])): ?>
                                                <div class="sale__widget"><?= $arItem['PERCENT'] ?>%</div>
                                            <? endif ?>
                                        </div>
                                    </div>
                                    <div class="count">
                                        <div data-min="1" data-measure=" шт." class="counter__widget"><a href="" data-add="-1">-</a>
                                            <div class="input">
                                                <input type="tel" name="good[<?= $arItem['ID'] ?>]" autocomplete="off" value="<?= $arItem['QUANTITY'] ?> шт."/>
                                            </div>
                                            <a href="" data-add="1">+</a>
                                        </div>
                                    </div>
                                    <div class="total">
                                        <p class="hidden-gt-s">Стоимость:</p>
                                        <p><b><?= $arItem['FINAL_PRICE'] ?></b></p>
                                    </div>
                                </div>
                            </div>
                        <? endforeach ?>
                    </div>
                </div>
            </section>
            <section class="return">
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
                                <input id="input-return-name" type="text" name="name" placeholder="Напр. Иванов Иван Иванович" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="field__widget type-inline">
                        <label for="input-return-bank" class="label">Наименование банка:</label>
                        <div class="field">
                            <div class="input">
                                <input id="input-return-bank" type="text" name="bank" placeholder="Напр. Напр. ООО “Сбербанк”" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="field__widget type-inline">
                        <label for="input-return-bik" class="label">БИК банка:</label>
                        <div class="field">
                            <div class="input">
                                <input id="input-return-bik" type="tel" name="bik" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="field__widget type-inline">
                        <label for="input-return-operating-account" class="label">Расчетный счёт (для юр.лиц):</label>
                        <div class="field">
                            <div class="input">
                                <input id="input-return-operating-account" type="tel" name="operating-account" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="field__widget type-inline">
                        <label for="input-return-card" class="label">№ банковской карты:</label>
                        <div class="field">
                            <div class="input">
                                <input id="input-return-card" type="tel" name="card" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div class="submit">
                    <button type="submit" class="button type-blue fill size-41">Отправить</button>
                </div>
            </section>
        </div>
    </form>
</div>