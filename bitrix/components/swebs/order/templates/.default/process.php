<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
require_once $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/functions.php';
?>

<div class="basket__page">
    <div class="default">
        <section class="heading">
            <? $APPLICATION->IncludeComponent(
                "bitrix:breadcrumb",
                "template",
                array(
                    "PATH" => "",
                    "SITE_ID" => "s1",
                    "START_FROM" => "0",
                    "COMPONENT_TEMPLATE" => "template"
                ),
                false
            ); ?>
            <div class="title">
                <h1 style="display: inline-block;">Оформление заказа</h1>
                <div class="remove"><a href="#" id="clean_basket">Очистить корзину</a><a href="#" class="remove_basket_items">&nbsp;<span class="icon__cross">
                                    <svg>
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/bitrix/templates/dsklad/images/sprite.svg#cross"></use>
  </svg>
                        </span>
                    </a>
                </div>
            </div>
        </section>

        <form action="" method="post">
            <section class="goods">
                <div class="basket__widget">
                    <div class="list" id="basket_items">
                        <?
                        //if ($USER->IsAdmin()){
                        ?>
                            <!--<div class="item"><div class="whole-row"><div class="remove"><a href="#" id="clean_basket" class="" style="
    text-decoration: none;
    border-bottom: 1px dotted #000;
    color: grey;
">Очистить корзину: </a><a href="#" class="remove_basket_items"><span class="icon__cross">
                                    <svg>
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/bitrix/templates/dsklad/images/sprite.svg#cross"></use>
  </svg></span></a></div></div></div>-->
                        <?
                       // }
                        ?>
                        <? foreach ($arResult['BASKET_ITEMS'] as $arItems): ?>
<!--                            --><?//ppp($arItems)?>
                            <div class="item">
                                <div class="image">
                                    <? if (!empty($arItems['IMAGE'])): ?>
                                        <img src="<?= $arItems['IMAGE'] ?>" alt="<?= $arItems['NAME'] ?>">
                                    <? else: ?>
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/images/no_photo.png"
                                             alt="<?= $arItems['NAME'] ?>">
                                    <? endif; ?>

                                </div>
                                <div class="whole-row">
                                    <div class="info-row">
                                        <div class="info">
                                            <div class="category"><a
                                                        href="<?= $arItems['SECTION_URL'] ?>"><?= $arItems['SECTION'] ?></a>
                                            </div>
                                            <div class="name"><a
                                                        href="<?= $arItems['NAME_URL'] ?>"><?= $arItems['NAME'] ?></a>
                                            </div>
                                            <div class="data">
                                                <p class="counter-fallback hidden-gt-s"><span>Кол-во:</span>
                                                    <del><?= $arItems['QUANTITY'] ?></del>
                                                </p>
                                                <p class="article"><span>Артикул:</span> <?= $arItems['ARTICLE'] ?></p>
                                                <? if (strlen($arItems['COLOR']) > 0): ?>
                                                    <p class="color"><span>Цвет:</span> <?= $arItems['COLOR'] ?></p>
                                                <? endif; ?>
                                                <? if (strlen($arItems['SIZE']) > 0): ?>
                                                    <p class="size"><span>Размер:</span> <?= $arItems['SIZE'] ?> см</p>
                                                <? endif; ?>
                                            </div>
                                        </div>
                                        <div class="price"><span class="hidden-l">Цена за шт.</span>
                                            <b><?= $arItems['PRICE'] ?></b><span
                                                    class="hidden-lte-m">/ шт.</span>
                                            <? /*<div class="sale__widget">10%</div>*/ ?>
                                        </div>
                                    </div>
                                    <span><? //=$arItems['QUANTITY']?></span>
                                    <div class="count">
                                        <div data-min="1" data-measure=" шт." class="counter__widget"><a href=""
                                                                                                         data-add="-1">-</a>

                                            <div class="input">
                                                <input type="tel" name="good[<?= $arItems['PRODUCT_ID'] ?>]"
                                                       product_id="<?= $arItems['PRODUCT_ID'] ?>" autocomplete="off"
                                                       value="<?= $arItems['QUANTITY'] ?> шт." class="order_quantity"/>
                                            </div>
                                            <a href="" data-add="1">+</a>
                                        </div>
                                    </div>
                                    <div class="remove"><a href="<?= $arItems['PRODUCT_ID'] ?>"
                                                           class="remove_basket_items"><span
                                                    class="icon__cross">
                                    <svg>
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cross"></use>
                                    </svg></span></a></div>
                                    <div class="total">
                                        <p class="hidden-gt-s">Стоимость:</p>

                                        <p><b><?= $arItems['FINAL_PRICE'] ?></b> <span>В наличии.</span></p>
                                    </div>
                                </div>
                            </div>
                        <? endforeach ?>
                    </div>
                    <? /*
        <div class="services">
            <? foreach ($arResult['SERVICES'] as $intID => $arItem): ?>
                <label class="label">
                                <span class="icon">
                                    <span class="icon__<?= $arItem['SVG_ANCHOR'] ?>">
                                        <svg>
                                            <use
                                                xlink:href="<?= $arItem['SVG_SPRITE'] ?>#<?= $arItem['SVG_ANCHOR'] ?>"></use>
                                        </svg>
                                    </span>
                                </span>
                                <span class="checkbox">
                                    <input type="checkbox" name="service[]" class="order_service" value="<?= $intID ?>"
                                           autocomplete="off"<? if ($arItem['CHECK']): ?> checked<? endif ?>>
                                    <u class="square">
                                        <span class="icon__check">
                                            <svg>
                                                <use
                                                    xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use>
                                            </svg>
                                        </span>
                                    </u>
                                </span>
                                <span class="price">
                                    <span class="hidden-gt-s">Стоимость: </span>
                                    <b><?= $arItem['PRICE'] ?></b>
                                </span>
                                <span class="title">
                                    <b><?= $arItem['NAME'] ?></b>
                                    <? if (!empty($arItem['TEXT'])): ?>
                                        <span><?= $arItem['TEXT'] ?></span>
                                    <? endif ?>
                                </span>
                </label>
            <? endforeach ?>
        </div>
        */ ?>
                </div>
            </section>
            <section class="order">
                <div class="heading">
                    <h3>Оформить заказ</h3>
                    <? if (!$USER->IsAuthorized()): ?>
                        <p>У вас уже есть аккаунт? <a href="" class="order_signin">Войдите</a> для быстрого оформления
                            заказа.
                        </p>
                    <? endif ?>
                </div>
                <div class="info">
                    <? $APPLICATION->IncludeFile(
                        SITE_TEMPLATE_PATH . '/include_areas/order-block1.php',
                        array(),
                        array(
                            'MODE' => 'php'
                        )
                    ); ?>
                    <div class="fields">
                        <fieldset class="client">
                            <div class="legend">
                                <div class="title">
                                    <del class="hidden-s">Информация о клиенте</del>
                                    <del class="hidden-gt-s">Клиент</del>
                                </div>
                                <label class="label__widget"><span class="row"><span class="control">
                                <input type="checkbox" name="legal" value="1" autocomplete="off"
                                       data-checked=".switcher-legal"
                                       data-unchecked=".switcher-private" class="switcher">
                                <u class="square"><span class="icon__check">
                                    <svg>
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use>
                                    </svg></span>
                                </u></span><span class="label">Юридическое лицо</span></span></label>
                            </div>
                            <div class="field__widget type-inline">
                                <label for="input-about-city" class="label">Ваш город:</label>

                                <div class="field">
                                    <div class="input">
                                        <? $APPLICATION->IncludeComponent(
                                            "swebs:dpd.cities",
                                            "",
                                            array(
                                                "DPD_HL_ID" => 22,
                                                "COMPONENT_TEMPLATE" => ".default"
                                            ),
                                            false
                                        ); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="field__widget type-inline">
                                <label for="input-about-name" class="label"><span
                                            class="switcher-legal">Контактное лицо:</span><span
                                            class="switcher-private">Ваше имя:</span></label>

                                <div class="field">
                                    <div class="input">
                                        <input id="input-about-name" class="session" type="text" name="name"
                                               placeholder="Напр. Иванов Иван"
                                               autocomplete="off" value="<?= $_SESSION['ORDER']['FIELDS']['name'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="field__widget type-inline field-email">
                                <label for="input-about-email" class="label">Электронная почта:</label>

                                <div class="field">
                                    <div class="input">
                                        <input id="input-about-email" class="session" type="email" name="email"
                                               placeholder="name@domain.ru"
                                               autocomplete="off" value="<?= $_SESSION['ORDER']['FIELDS']['email'] ?>">
                                    </div>
                                    <label class="label__widget"><span class="row"><span class="control">
                                  <input type="checkbox" name="subscrible" value="1" autocomplete="off" checked>
                                  <u class="square"><span class="icon__check">
                                      <svg>
                                          <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use>
                                      </svg></span>
                                  </u></span><span class="label">Подписаться на акции <span
                                                        class="hidden-lte-m">магазина</span></span></span></label>
                                </div>
                            </div>
                            <div class="field__widget type-inline">
                                <label for="input-about-phone" class="label">Мобильный телефон:</label>

                                <div class="field">
                                    <div class="input">
                                        <input id="input-about-phone" class="session" type="tel" name="phone"
                                               placeholder="+79001234567"
                                               autocomplete="off" data-phonemask
                                               value="<?= $_SESSION['ORDER']['FIELDS']['phone'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="field__widget type-inline switcher-legal">
                                <label for="input-about-company" class="label">Название компании:</label>

                                <div class="field">
                                    <div class="input">
                                        <input id="input-about-company" class="session" type="text" name="company"
                                               placeholder="Название компании:" autocomplete="off"
                                               value="<?= $_SESSION['ORDER']['FIELDS']['company'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="field__widget type-inline switcher-legal">
                                <label for="input-about-vat" class="label">ИНН:</label>

                                <div class="field">
                                    <div class="input">
                                        <input id="input-about-vat" class="session" type="text" name="vat"
                                               placeholder="000000000000"
                                               autocomplete="off" value="<?= $_SESSION['ORDER']['FIELDS']['vat'] ?>">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <hr>
                        <div id="delivery_payment_area">
                            <fieldset class="delivery">
                                <div class="legend">
                                    <div class="title">Для вашего города <?= getWord2(count($arResult['DELIVERY'])) ?>
                                        <span><?= count($arResult['DELIVERY']) ?> <?= getWord(count($arResult['DELIVERY'])) ?>
                                            доставки</span></div>
                                    <div class="warning-message">
                <span>
            Автоматический расчет стоимости доставки находится в тестовом режиме <br>и может работать некорректно. Заранее приносим свои извинения.
                </span>
                                    </div>
                                </div>

                                <div class="tabs__widget">
                                    <div class="tabs-handler">
                                        <ul>
                                            <? foreach ($arResult['DELIVERY'] as $arItem): ?>
                                                <li<? if ($arItem['ACTIVE']): ?> class="active"<? endif ?>>
                                                    <a href="<?= $arItem['ID'] ?>">
                                                        <span class="icon__<?= $arItem['CLASS'] ?>">
                                                            <svg>
                                                                <use
                                                                        xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#<?= $arItem['CLASS'] ?>"></use>
                                                            </svg>
                                                        </span>
                                                        <b><?= $arItem['NAME'] ?></b>
                                                    </a>
                                                </li>
                                            <? endforeach ?>
                                        </ul>
                                    </div>

                                    <div class="tabs-content">
                                        <? if (!empty($arResult['TERMINAL'])): ?>
                                            <div class="tab pickup">
                                                <div class="field__widget type-block">
                                                    <label for="input-delivery-point" class="label">Пункты
                                                        самовывоза ТК "DPD"</label>
                                                    <div class="field">
                                                        <div class="select">
                                                            <select id="input-delivery-point" name="point">

                                                                <? foreach ($arResult['TERMINAL'] as $arTerminal): ?>
                                                                    <option value="<?= $arTerminal['terminalCode'] ?>">
                                                                        <?= $arTerminal['address']['terminalAddress'] ?>
                                                                    </option>
                                                                <? endforeach ?>
                                                            </select>

                                                            <div class="fallback"><span></span>

                                                                <div><span class="icon__darr">
                                          <svg>
                                              <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#darr"></use>
                                          </svg></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="address">
                                                    <? foreach ($arResult['TERMINAL'] as $arTerminal): ?>
                                                        <div class="terminal_address"
                                                             id="<?= $arTerminal['terminalCode'] ?>">
                                                            <p>
                                                                <b>Адрес:</b>
                                                                <?= $arTerminal['address']['terminalAddress'] ?>
                                                                <? if (!empty($arTerminal['address']['descript'])): ?>
                                                                    <br>
                                                                    (<?= $arTerminal['address']['descript'] ?>)
                                                                <? endif ?>
                                                            </p>

                                                            <p>
                                                                <b>Режим работы:</b>
                                                                <? foreach ($arTerminal['schedule'] as $arSchedule): ?>
                                                                    <? if ($arSchedule['operation'] == 'SelfDelivery'): ?>
                                                                        <? if (!array_key_exists('weekDays', $arSchedule['timetable'])): ?>
                                                                            <? foreach ($arSchedule['timetable'] as $arTimetable): ?>
                                                                                <br><?= $arTimetable['weekDays'] ?>
                                                                                <?= $arTimetable['workTime'] ?>
                                                                            <? endforeach ?>
                                                                        <? else: ?>
                                                                            <br><?= $arSchedule['timetable']['weekDays'] ?>
                                                                            <?= $arSchedule['timetable']['workTime'] ?>
                                                                        <? endif ?>
                                                                    <? endif ?>
                                                                <? endforeach ?>
                                                            </p>
                                                        </div>
                                                    <? endforeach ?>
                                                    <? /*<a class="button type-blue fill size-41">Заберу отсюда</a>*/ ?>
                                                </div>
                                                <div class="map">
                                                    <div id="map" class="embed">
                                                        <? $APPLICATION->IncludeComponent(
                                                            "bitrix:map.yandex.view",
                                                            "order",
                                                            array(
                                                                "CONTROLS" => array(
                                                                    0 => "ZOOM",
                                                                    1 => "SCALELINE",
                                                                ),
                                                                "INIT_MAP_TYPE" => "MAP",
                                                                "MAP_DATA" => serialize($arResult["mapParams"]),
                                                                "MAP_HEIGHT" => "299",
                                                                "MAP_ID" => "",
                                                                "MAP_WIDTH" => "100%",
                                                                "OPTIONS" => array(
                                                                    0 => "ENABLE_SCROLL_ZOOM",
                                                                    1 => "ENABLE_DBLCLICK_ZOOM",
                                                                    2 => "ENABLE_DRAGGING",
                                                                ),
                                                                "COMPONENT_TEMPLATE" => "order"
                                                            ),
                                                            false
                                                        ); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <? endif ?>
                                        <div class="tab delivery">
                                            <div class="field__widget type-inline">
                                                <label for="input-about-address" class="label">Адрес:</label>

                                                <div class="field">
                                                    <div class="input">
                                                        <input id="input-about-address" class="session" type="text"
                                                               name="address"
                                                               placeholder="Укажите адрес доставки как можно точнее"
                                                               autocomplete="off"
                                                               value="<?= $_SESSION['ORDER']['FIELDS']['address'] ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="field__widget type-inline">
                                                <label for="input-about-courier-comment" class="label">Комментарий
                                                    курьеру:</label>

                                                <div class="field">
                                                    <div class="input">
                            <textarea id="input-about-courier-comment" name="delivery_comment"
                                      rows="3"
                                      placeholder="Напишите уточнения для курьера"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="field__widget type-inline field-additions">
                                                <div class="label">Дополнительно:</div>

                                                <div class="field">
                                                    <? foreach ($arResult['DELIVERY_SERVICES'] as $arItem): ?>
                                                        <?
                                                        $strChecked = '';
                                                        if ($arItem['CHECK']) {
                                                            $strChecked = ' checked="checked"';
                                                        }
                                                        ?>
                                                        <label class="label__widget">
                                                            <span class="row">
                                                                <span class="control">
                                                                    <input type="checkbox" class="delivery_service"
                                                                           name="dserv[]" value="<?= $arItem['ID'] ?>"
                                                                           autocomplete="off"<?= $strChecked ?>/>
                                                                    <u class="square"><span class="icon__check">
                                                                        <svg>
                                                                            <use
                                                                                    xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use>
                                                                        </svg></span>
                                                                    </u>
                                                                </span>
                                                                <span class="label"><?= $arItem['NAME'] ?>
                                                                    (+ <?= $arItem['PRICE'] ?>)</span>
                                                            </span>
                                                        </label>
                                                    <? endforeach ?>
                                                </div>
                                            </div>

                                            <div class="field__widget type-inline field-total">
                                                <div class="label">Общая стоимость доставки:</div>
                                                <div class="field">
                                                    <b id="delivery_coast"><?= $arResult['COAST'] ?></b>
                                                    <span>Ваш заказ будет доставлен в течение 5 дней. При получении при себе иметь паспорт.</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <hr>
                            <fieldset class="payment">

                                <div class="legend">
                                    <div class="title">Выберите способ оплаты</div>
                                </div>

                                <div class="options">
                                    <? foreach ($arResult['PAYMENT'] as $arItem): ?>
                                        <label>
                                            <input type="radio" name="payment"
                                                   value="<?= $arItem['ID'] ?>"<? if ($arItem['ACTIVE']): ?> checked<? endif ?>>
                                            <span class="icon__<?= $arItem['CLASS'] ?>">
                                                <svg>
                                                    <use
                                                            xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#<?= $arItem['CLASS'] ?>"></use>
                                                </svg>
                                            </span>
                                            <b><?= $arItem['NAME'] ?></b>
                                            <? /*<span class="discount switcher-private">-3%</span>*/ ?>
                                        </label>
                                    <? endforeach ?>
                                </div>

                            </fieldset>
                            <hr>
                            <fieldset class="comment">
                                <div class="field__widget type-inline">
                                    <label for="input-about-order-comment" class="label">Комментарий к заказу:</label>

                                    <div class="field">
                                        <div class="input">
                <textarea id="input-about-order-comment" name="order-comment" rows="3"
                          placeholder="Напишите уточнения к заказу"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="total">
                                <div class="title" id="total_summ_title">
<?
	$q = 0;
	foreach($arResult["BASKET_ITEMS"] as $item){
		$q += $item["QUANTITY"];
	}
?>
                                    Итого: <?=$q?> <?=getWord3($q)?>
                                    на
                                    сумму <?= $arResult['TOTAL_SUMM'] ?></div>
                                <div class="digits">
                                    <div class="promo">
                                        <div class="input">
                                            <input type="text" name="promo" placeholder="Промокод" autocomplete="off">
                                        </div>
                                        <button type="button" class="button type-blue" id="promo">Применить</button>
                                    </div>
                                    <? global $USER;
                                    if (($USER->GetID()) == 93):?>
                                        <? //print_r($arResult);
                                        // echo "</br>";
                                        //echo   $arResult['COUPON'];
                                        echo $_SESSION['COUPON'];
                                        echo "</br>";
                                        echo "</br>";
                                        echo $_SESSION['~OLD_PRICE'];
                                        echo "</br>";
                                        echo "</br>";
                                        echo $_SESSION['OLD_PRICE'];

                                        ?>
                                    <? endif; ?>
                                    <div class="calculation">
                                        <ul id="total_summ">
                                            <? if (!empty($arResult['TOTAL_DISCOUNT'])): ?>
                                                <li>Стоимость: <?= $_SESSION['OLD_PRICE'] ?></li>
                                                <li>Стоимость1: <?= $_SESSION['OLD_PRICE1'] ?></li>
                                                <li>Стоимость2: <?= $_SESSION['OLD_PRICE2'] ?></li>
                                                <li>Доставка: <?= $arResult['COAST'] ?></li>
                                                <li>Общая скидка: <?= $arResult['TOTAL_DISCOUNT'] ?></li>
                                                <li><b>К оплате: <?= $arResult['TOTAL_RESULT'] ?></b></li>
                                            <? else: ?>
                                                <li>Стоимость: <?= $arResult['TOTAL_SUMM'] ?></li>
                                                <li>Доставка: <?= $arResult['COAST'] ?></li>
                                                <li><b>К оплате: <?= $arResult['TOTAL_RESULT'] ?></b></li>
                                            <? endif ?>
                                            <input type="hidden" name="delivery_price"
                                                   value="<?= $arResult['~COAST'] ?>">
                                        </ul>
                                        <div class="place">
                                            <button class="button type-blue fill size-41 btn_send_order_process">
                                                ОФОРМИТЬ ЗАКАЗ
                                            </button>
                                            <p>Нажимая на кнопку &quot;Оформить заказ&quot;, вы принимаете условия <a href="/public_offer/">Публичной оферты</a></p>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </section>
            <input type="hidden" name="delivery" value="<?= $arResult['FIRST_DELIVERY_ID'] ?>"/>
            <input type="hidden" name="go" value="Y"/>
        </form>

        <? $APPLICATION->IncludeFile('/include_areas/index_recomm.php') ?>
    </div>
</div>
<script>
    $("#clean_basket").on("click", function(){
        console.log(123);
        var markers = { "markers": "123"};
        request = $.ajax({
            url: "<?= SITE_TEMPLATE_PATH ?>/ajax/small-basket.php",
            type: "post",
            data: markers,
            dataType  : 'json',
        });
        request.done(function (response, textStatus, jqXHR){
            // Log a message to the console
            console.log(response);
            location.reload();
            $("li.basket_area").load(location.href + "li.basket_area");
        });


    });
</script>