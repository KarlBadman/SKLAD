<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
require_once $_SERVER['DOCUMENT_ROOT'].$templateFolder.'/functions.php';
?>

<style>
    .select .chosen-drop {max-height: 10000000px;}
    .select .chosen-results {    max-height: 300px; overflow-y: auto;}
    .payment .options label.cup:hover {  cursor: default!important;
        border: 1px solid #fff!important;
        color: #000;}

    .payment .options label.cup {  border: 1px solid #fff!important;}

    .payment .options label.cup:hover input  {pointer-events: none; }
    .payment .options label.cup:hover  b {    color: #000!important;}
    .options label.cup:hover .discript.new  {display: block!important;}
    .options label.cup:hover  svg {fill: #000!important;}
</style>

<?
// В $basketSum общая стоимость товаров в корзине
$basketSum = (int)$arResult['~TOTAL_SUMM'];

//criteo / torg Mail.ru
$itemsToMetrics = array();
$productsIdJSObject = '';
foreach ($arResult['BASKET_ITEMS'] as $mass_basket_criteo) {
    $pribavka += 1;

    $viewBasket_criteo .= '
		{ id: "'.$mass_basket_criteo['PRODUCT_ID'].'", price: '.$mass_basket_criteo['NO_FORMAT_PRICE'].', quantity: '.$mass_basket_criteo['QUANTITY'].' },
	';

    $itemsToMetrics[] = $mass_basket_criteo['PRODUCT_ID'];

    $zpt_google = ', ';
    if ($pribavka > count($arResult['BASKET_ITEMS']) - 1) {
        $zpt_google = '';
    }

    $spis_id_google .= $mass_basket_criteo['PRODUCT_ID'].$zpt_google;
    $summa_googletag += $mass_basket_criteo['NO_FORMAT_PRICE'] * $mass_basket_criteo['QUANTITY'];
}
$productsIdJSObject = json_encode($itemsToMetrics);

// <!-- Rating@Mail.ru counter dynamic remarketing appendix -->
/* mail counter temporary removed 01.11.18
$_SESSION['TARGET']['productid'] = $productsIdJSObject;
$_SESSION['TARGET']['totalvalue'] = $basketSum;
$APPLICATION->IncludeFile('/include_areas/mail_counter.php');
*/
// <!-- Rating@Mail.ru counter dynamic remarketing appendix -->
?>

<script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async></script>
<script type="text/javascript">
    window.criteo_q = window.criteo_q || [];
    var deviceType = /iPad/.test(navigator.userAgent) ? 't' : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? 'm' : 'd';
    window.criteo_q.push(
        { event: 'setAccount', account: 44817 },
        { event: 'setEmail', email: '<?= $_SESSION['SESS_AUTH']['EMAIL']?>' },
        { event: 'setSiteType', type: deviceType },
        { event: 'viewBasket', item: [
            <?= $viewBasket_criteo?>
        ]}
    );
    //END criteo
</script>

<script type="text/javascript">
    //google ecomm vit
    var google_tag_params = {
        ecomm_prodid: [<?= $spis_id_google ?>],
        ecomm_pagetype: 'cart',
        ecomm_totalvalue: <?= $summa_googletag?>
    };
    //END google ecomm vit
</script>

<div class="basket__page">
    <div class="default">
        <section class="heading">
            <?
            $APPLICATION->IncludeComponent(
                'bitrix:breadcrumb',
                'template',
                array(
                    'PATH' => '',
                    'SITE_ID' => 's1',
                    'START_FROM' => '0',
                    'COMPONENT_TEMPLATE' => 'template'
                ),
                false
            );
            ?>
            <div class="title">
                <h1 style="display: inline-block;">Оформление заказа</h1>
            </div>
        </section>

        <form action="" method="post" id="checkout-form">
            <section class="goods">
                <div class="basket__widget">
                    <div class="list" id="basket_items" data-order-page="basket-fieldset">
                        <?
                        $productsCount = 0;
                        $warrantyCost = 0;
                        foreach ($arResult['BASKET_ITEMS'] as $arItems) {
                            $productSection = mb_strtolower($arItems['SECTION']);
                            if ($productSection == 'другие модели') {
                                $productSection = mb_strtolower($arItems['NAME']);
                            }
                            if (strpos($productSection, 'стол') !== false) {
                                $warrantyCost += 400 * $arItems['QUANTITY'];
                            } else if (strpos($productSection, 'стул') !== false) {
                                $warrantyCost += 100 * $arItems['QUANTITY'];
                            }
                            $productsCount += $arItems['QUANTITY'];
                            ?>
                            <div class="item" data-order-page="basket-item-field" data-item-name="<?=$arItems['NAME']?>" data-item-id="<?=$arItems['ID']?>" data-item-price="<?=$arItems['NO_FORMAT_PRICE']?>" data-item-category="<?=$arItems['SECTION']?>">
                                <div class="image">
                                    <?
                                    if (!empty($arItems['IMAGE'])) {
                                        ?>
                                        <img src="<?= $arItems['IMAGE'] ?>" alt="<?= $arItems['NAME'] ?>">
                                        <?
                                    } else {
                                        ?>
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/images/no_photo.png" alt="<?= $arItems['NAME'] ?>">
                                        <?
                                    }
                                    ?>
                                </div>
                                <div class="whole-row">
                                    <div class="info-row">
                                        <div class="info">
                                            <div class="category">
                                                <a href="<?= $arItems['SECTION_URL'] ?>"><?= $arItems['SECTION'] ?></a>
                                            </div>
                                            <div class="name">
                                                <a href="<?= $arItems['NAME_URL'] ?>"><?= $arItems['NAME'] ?></a>
                                            </div>
                                            <div class="data">
                                                <p class="counter-fallback hidden-gt-s"><span>Кол-во:</span>
                                                    <del><?= $arItems['QUANTITY'] ?></del>
                                                </p>
                                                <p class="article"><span>Артикул:</span> <?= $arItems['ARTICLE'] ?></p>
                                                <?
                                                if (strlen($arItems['COLOR']) > 0) {
                                                    ?>
                                                    <p class="color"><span>Цвет:</span> <?= $arItems['COLOR'] ?></p>
                                                    <?
                                                }

                                                if (strlen($arItems['SIZE']) > 0) {
                                                    ?>
                                                    <p class="size"><span>Размер:</span> <?= $arItems['SIZE'] ?> см</p>
                                                    <?
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="price">
                                            <span class="hidden-l">Цена за шт.</span>
                                            <b><?= $arItems['PRICE'] ?></b>
                                            <span class="hidden-lte-m"> шт.</span>
                                            <?
                                            if (
                                                $arItems['DISCOUNT_PRODUCT']['DISCOUNT_PERCENTAGE'] != ''
                                                && $arItems['DISCOUNT_PRODUCT']['DISCOUNT_PERCENTAGE'] != 0
                                            ) {
                                                ?>
                                                <div class="sale__widget">%</div>
                                                <?
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="count">
                                        <div data-min="1" data-max="500" data-measure=" шт." class="counter__widget">
                                            <a data-add="-1">-</a>
                                            <div class="input">
                                                <input
                                                    type="tel"
                                                    max="500"
                                                    min="1"
                                                    name="good[<?= $arItems['PRODUCT_ID'] ?>]"
                                                    product_id="<?= $arItems['PRODUCT_ID'] ?>"
                                                    autocomplete="off"
                                                    value="<?= $arItems['QUANTITY'] ?> шт."
                                                    class="order_quantity"
                                                    data-gaproduct-quantity="<?= $arItems['QUANTITY'] ?>"
                                                    />
                                            </div>
                                            <a data-add="1">+</a>
                                        </div>
                                    </div>
                                    <div class="remove">
                                        <a
                                            href="<?= $arItems['PRODUCT_ID'] ?>"
                                            data-ga-analys-btn="basket-delete-item"
                                            class="remove_basket_items"
                                        >
                                            <span class="icon__cross">
                                                <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cross"></use></svg>
                                            </span>
                                        </a>
                                    </div>
                                    <div class="total">
                                        <p class="hidden-gt-s">Стоимость:</p>
                                        <p>
                                            <b><?= $arItems['FINAL_PRICE'] ?></b>
                                            <?
                                            if ($arResult['CATALOG'][$arItems['PRODUCT_ID']]['QUANTITY']>0) {
                                                ?>
                                                <span>В наличии</span>
                                                <?
                                            } elseif ($arItems['ARRIVAL_DATE'] != '') {
                                                ?>
                                                <span style="color:#ea1e31">Ожидается <?= $arItems['ARRIVAL_DATE'] ?> </span>
                                                <?
                                            } else {
                                                ?>
                                                <span style="color:#ea1e31">Нет в наличии</span>
                                                <?
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?
                        }
                        ?>
                    </div>
                    <div class="cart-add-more">
                        <?if ($arResult['NEEDED'] > 0) : ?>
                            <div class="cart_info">Добавь ещё <?=$arResult['NEEDED']?> <span class="red">Eames Style DSW</span> для уменьшения цены за штуку</div>
                        <?endif;?>
                    </div>
                    <div class="basket_total">
                        <div class="title total_summ_title">
                            <?
                            $q = 0;
                            foreach ($arResult['BASKET_ITEMS'] as $item) {
                                $q += $item['QUANTITY'];
                            }
                            ?>
                            Итого: <?= $q ?> <?= getWord3($q) ?>
                            на
                            сумму <?= $arResult['TOTAL_SUMM'] ?>
                        </div>
                    </div>
                    
                    <?
                    if (!empty($arResult['SERVICES'])) {
                        ?>
                        <div class="services">
                            <?
                            foreach ($arResult['SERVICES'] as $serviceId => $service) {
                                $price = $service['~PRICE'];
                                $sum = $arResult['TOTAL_SERVICES'];
                                ?>
                                <label class="label service_<?= $service['CODE'] ?>">
                                    <span class="icon">
                                        <span class="icon__<?= $service['CODE'] ?>">
                                            <img src="<?= $service['SVG_SPRITE'] ?><?= !empty($service['SVG_ANCHOR']) ? '#'.$service['SVG_ANCHOR'] : '' ?>" alt=""/>
                                        </span>
                                    </span>
                                    <span class="checkbox">
                                        <input
                                            type="checkbox"
                                            name="serv[]"
                                            class="order_service"
                                            value="<?= $serviceId ?>"
                                            autocomplete="off"
                                            <?= $service['CHECK'] ? 'checked="checked"' : '' ?>>
                                        <u class="square">
                                            <span class="icon__check">
                                                <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use></svg>
                                            </span>
                                        </u>
                                    </span>
                                    <span class="price"><span class="hidden-gt-s">Стоимость: </span><b><?= number_format($sum, 0, '', ' ').'.–' ?></b></span>
                                    <span class="title">
                                        <?= $service['TEXT'] ?>
                                    </span>
                                </label>
                                <?
                            }
                            ?>
                        </div>
                        <?
                    }
                    ?>
                </div>
            </section>

            <section class="order">
                <div class="heading">
                    <h3>Контактная информация</h3>
                    <?
                    if (!$USER->IsAuthorized()) {
                        ?>
                        <p>Мы вас не узнали. <a href="/login/" class="border-link">Войдите</a> для быстрого оформления заказа.</p>
                        <?
                    }
                    ?>
                </div>
                <div class="info">
                    <div class="fields">
                        <fieldset class="client">
                            <div class="legend">
                                <div class="title">
                                    <del class="hidden-s">Информация о клиенте</del>
                                    <del class="hidden-gt-s">Информация о клиенте</del>
                                </div>
                            </div>
                            <div class="field__widget type-inline">
                                <label for="input-about-city" class="label">Город доставки:</label>
                                <div class="field">
                                    <div class="input">
                                        <?
                                        $APPLICATION->IncludeComponent(
                                            'swebs:dpd.cities',
                                            'custom',
                                            array(
                                                'DPD_HL_ID' => \Dsklad\Config::getParam('hl/dpd_cities'),
                                                'COMPONENT_TEMPLATE' => '.default'
                                            ),
                                            false
                                        );
                                        ?>
                                        <input
                                            type="hidden"
                                            name="region_name"
                                            id="region_name"
                                            value="<?= (($_SESSION['ORDER']['FIELDS']['city'] == $_SESSION['DPD_CITY_NAME']) ? $_SESSION['ORDER']['FIELDS']['region'] : '') ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="field__widget type-inline">
                                <label for="input-about-name" class="label">
                                    <span class="switcher-legal">Контактное лицо:</span>
                                    <span class="switcher-private">Имя:</span>
                                </label>
                                <div class="field">
                                    <div class="input">
                                        <input
                                            id="input-about-name"
                                            class="session"
                                            type="text"
                                            name="name"
                                            placeholder="Напр. Иванов Иван"
                                            autocomplete="off"
                                            value="<?= $_SESSION['ORDER']['FIELDS']['name'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="field__widget type-inline field-email">
                                <label for="input-about-email" class="label">Электронная почта:</label>
                                <div class="field">
                                    <div class="input">
                                        <input
                                            id="input-about-email"
                                            class="session"
                                            type="email"
                                            name="email"
                                            placeholder="name@domain.ru"
                                            autocomplete="off"
                                            value="<?= $_SESSION['ORDER']['FIELDS']['email'] ?>">
                                    </div>
                                    <label class="label__widget">
                                        <span class="row">
                                            <span class="control">
                                                <input type="checkbox" name="subscrible" value="1" autocomplete="off" checked>
                                                <u class="square">
                                                    <span class="icon__check">
                                                        <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use></svg>
                                                    </span>
                                                </u>
                                            </span>
                                            <span class="label">Подписаться на акции <span class="hidden-lte-m">магазина</span></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="field__widget type-inline">
                                <label for="input-about-phone" class="label">Телефон:</label>
                                <div class="field">
                                    <div class="input">
                                        <input
                                            id="input-about-phone"
                                            class="session"
                                            type="tel"
                                            name="phone"
                                            placeholder="+79001234567"
                                            autocomplete="off"
                                            data-phonemask
                                            value="<?= $_SESSION['ORDER']['FIELDS']['phone'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="legal-block">
                                <div class="field__widget type-inline switcher-legal" style="display:none">
                                    <label for="input-about-vat" class="label"></label>
                                    <div class="field">
                                        <div class="input">
                                            <input
                                                id="input-about-vat"
                                                type="text"
                                                name="vat"
                                                placeholder="ИНН организации"
                                                autocomplete="off"
                                                value="<?= $_SESSION['ORDER']['FIELDS']['vat'] ?>">
                                        </div>
                                    </div>
                                </div>
                                <label class="label__widget">
                                    <span class="row">
                                        <span class="control">
                                            <input
                                                type="checkbox"
                                                name="legal"
                                                value="1"
                                                autocomplete="off"
                                                data-checked=".switcher-legal"
                                                data-unchecked=".switcher-private"
                                                class="switcher">
                                            <u class="square">
                                                <span class="icon-cross">
                                                    <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cross"></use></svg>
                                                </span>
                                                <span class="icon__check">
                                                    <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use></svg>
                                                </span>
                                            </u>
                                        </span>
                                        <span class="label">Юридическое лицо</span>
                                    </span>
                                </label>
                            </div>
                            <?
                            $APPLICATION->IncludeComponent(
                                'prmedia:sale.confirm.phone',
                                '.default',
                                array(
                                    'PAYMENTS_SELECTOR' => '.payment .options label',  //css-селектор кнопок выбора способа оплаты
                                    'PAYMENT_CONFIRM_SELECTOR' => '#l_3',  //css-селектор кнопки способа оплаты, при котором нужно подтверждать телефон
                                    'PHONE_INPUT_SELECTOR' => '#input-about-phone',  //css-селектор поля с номером телефона
                                    'WAIT_TIME' => \Dsklad\Config::getOption('UF_CONF_PHONE_TIME'),  //время до повторной отправки
                                    'LENGTH' => \Dsklad\Config::getOption('UF_CONF_PHONE_LENGTH'),  //длина кода
                                ),
                                false
                            );
                            ?>
                        </fieldset>
                        <hr>
                        <div id="delivery_payment_area">
                            <hr>
                            <fieldset class="payment" data-order-page="payment-fieldset">
                                <div class="legend">
                                    <div class="title">
                                        Выберите способ оплаты <br>
                                        <!--<font color="red" size="2">Скидка 10%, при оплате банковской картой на сайте</font>-->
                                    </div>
                                </div>
                                <div class="options">
                                    <?
                                    foreach ($arResult['PAYMENT'] as $arItem) {
                                        ?>
                                        <label
                                            id="l_<?= $arItem['ID'] ?>"
                                            class="
                                                <?= $arItem['ACTIVE'] ? 'active' : '' ?>
                                                <?= ($arItem['HIDE'] == 'Y') ? ' super-disabled' : ''?>
                                                <?= ($basketSum > NPP_SUMM_LIMIT && $arItem['ID'] == 3) ? ' super-disabled' : ''; ?>
                                            "
                                            data-order-page="payment-option-field"
                                            <?= ($arItem['HIDE'] == 'Y') ? ' disabled="disabled"' : ''?>
                                        >
                                            <?($coef = $arItem['ID'] == 3 ? (ceil(1.5*$arResult['DELIVERY_DAYS'])) : (1*$arResult['DELIVERY_DAYS']));?>
                                            <input
                                                data-dpc="<?=$coef . " " . getWord4($coef);?>"
                                                data-ga-analys-btn="order-payment-set"
                                                type="radio"
                                                name="payment"
                                                value="<?= $arItem['ID'] ?>"
                                                <?= $arItem['ACTIVE'] ? 'checked' : '' ?>
                                                <?if ($arItem['HIDE'] == 'Y') : ?>disabled="disabled"<?endif;?>
                                                >
                                            <span class="icon__<?= $arItem['CLASS'] ?>">
                                                <svg>
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#<?= $arItem['CLASS'] ?>"></use>
                                                </svg>
                                            </span>
                                            <b><?= $arItem['NAME'] ?></b>
                                            <?
                                            if ($arItem['ID'] == 2) {
                                                ?>
                                                <div class="discript">
                                                    Безопасная оплата через Яндекс.Кассу. Комиссия 0%.
                                                </div>
                                                <?
                                            }
                                            ?>
                                        </label>
                                        <?
                                    }
                                    ?>
                                </div>
                            </fieldset>
                            <hr>
                            <fieldset class="delivery">
                                <div class="legend legeng_delivery">
                                    <div class="title">
                                        Для вашего города <?= getWord2(count($arResult['DELIVERY'])) ?>
                                        <span><?= count($arResult['DELIVERY']) ?> <?= getWord(count($arResult['DELIVERY'])) ?> доставки</span>
                                    </div>
                                </div>
                                <div class="tabs__widget">
                                    <div class="tabs-handler">
                                        <ul data-order-page="delivery-fieldset">
                                            <?
                                            foreach ($arResult['DELIVERY'] as $arItem) {
                                                ?>
                                                <li<?= ($arItem['ACTIVE']) ? ' class="active"' : '' ?> data-order-page="delivery-option-field">
                                                    <a href="<?= $arItem['ID'] ?>" data-ga-analys-btn="order-delivery-set">
                                                        <span class="icon__<?= $arItem['CLASS'] ?>">
                                                            <svg>
                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#<?= $arItem['CLASS'] ?>"></use>
                                                            </svg>
                                                        </span>
                                                        <b><?= $arItem['NAME'] ?></b>
                                                    </a>
                                                </li>
                                                <?
                                            }
                                            ?>
                                        </ul>
                                    </div>

                                    <input type="hidden" name="basket_sum" value="<?= $basketSum ?>">

                                    <div class="tabs-content">
                                        <?
                                        if (!empty($arResult['TERMINAL'])) {
                                            ?>
                                            <div class="tab pickup">
                                                <div class="field__widget type-inline">
                                                    <label for="input-delivery-point" class="label">Пункт самовывоза ТК:</label>
                                                    <div class="field">
                                                        <div class="select">
                                                            <select id="input-delivery-point" name="point">
                                                                <?$isTerminalOptString = ''; $isPartnerPVZ = '';?>
                                                                <?
                                                                foreach ($arResult['TERMINAL'] as $arTerminal) {
                                                                    ?>

                                                                    <?
                                                                    $x = str_replace(
                                                                        array('ул.,', 'шоссе,', 'улица,', 'ул.  пр-кт', 'проезд'),
                                                                        array('ул. ', 'ш ', 'ул. ', 'пр-кт ', ' '),
                                                                        $arTerminal['address']['terminalAddress']
                                                                    );
                                                                    $x_me = str_replace(
                                                                        array('ул. пр-кт', 'ул.  проезд', 'ул.  пл', 'ул.  пер', 'ул.  ш', 'ул.  проспект', 'проспект', 'ул. проспект'),
                                                                        array('пр-кт', 'проезд', 'пл', 'пер', 'ш.', 'пр-кт', 'пр-кт', 'пр-кт'),
                                                                        $x
                                                                    );
                                                                    $x = str_replace('ул.  б-р', 'б-р', $x_me);
                                                                    if (strlen($x) > 15) {
                                                                        // echo $x;
                                                                    }
                                                                    ?>
                                                                    <?if ($arTerminal['is_terminal'] == 'Y') : ?>
                                                                        <?$isTerminalOptString .= '<option class="'.$arTerminal['css_class'].'" '.($arResult['CURRENT_DELIVERY_POINT_CODE'] == $arTerminal['terminalCode'] ? "selected=\"selected\"" : "").' value="'.$arTerminal['terminalCode'].'" cityId="'.$arTerminal['address']['cityId'].'" data-nppSum="'.$arTerminal['npp_sum'].'" data-IsTerminal="'.($arTerminal['is_terminal'] == 'Y' ? 'Y' : 'N').'">'. $x .'</option>';?>
                                                                    <?else : ?>
                                                                        <?$isPartnerPVZ .= '<option class="'.$arTerminal['css_class'].'" '.($arResult['CURRENT_DELIVERY_POINT_CODE'] == $arTerminal['terminalCode'] ? "selected=\"selected\"" : "").' value="'.$arTerminal['terminalCode'].'" cityId="'.$arTerminal['address']['cityId'].'" data-nppSum="'.$arTerminal['npp_sum'].'" data-IsTerminal="'.($arTerminal['is_terminal'] == 'Y' ? 'Y' : 'N').'">' . $x . '</option>';?>
                                                                    <?endif;?>
                                                                    <?
                                                                }?>
                                                                <optgroup label="Терминалы самовывоза"><?=$isTerminalOptString?></optgroup>
                                                                <optgroup label="Партнерские пункты самовывоза"><?=$isPartnerPVZ?></optgroup>
                                                            </select>
                                                            <div class="fallback">
                                                                <div>
                                                                    <span class="icon__darr">
                                                                        <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#darr"></use></svg>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="field__widget type-inline">
                                                    <label for="input-delivery-point" class="label">Режим работы:</label>
                                                    <?
                                                    foreach ($arResult['TERMINAL'] as $arTerminal) {
                                                        if ($arTerminal['css_class'] == 'npp_show') {
                                                            $mapParamsLimits['PLACEMARKS'][] = array(
                                                                'LON' => $arTerminal['geoCoordinates']['longitude'],
                                                                'LAT' => $arTerminal['geoCoordinates']['latitude'],
                                                                'TEXT' => $arTerminal['address']['descript'],
                                                                'TERMINAL' => $arTerminal['terminalCode'],
                                                                'CSS_CLASS' => $arTerminal['css_class'],
                                                                'NPP_SUM' => $arTerminal['npp_sum'],
                                                            );
                                                        }
                                                        ?>
                                                        <div class="terminal_address field" id="<?= $arTerminal['terminalCode'] ?>">
                                                            <?
                                                            foreach ($arTerminal['schedule'] as $arSchedule) {
                                                                if ($arSchedule['operation'] == 'SelfDelivery') {
                                                                    if (!array_key_exists('weekDays', $arSchedule['timetable'])) {
                                                                        foreach ($arSchedule['timetable'] as $arTimetable) {
                                                                            ?>
                                                                            <?= $arTimetable['weekDays'] ?>
                                                                            <?= $arTimetable['workTime'] ?>
                                                                            <br>
                                                                            <?
                                                                        }
                                                                    } else {
                                                                        ?>
                                                                        <?= $arSchedule['timetable']['weekDays'] ?>
                                                                        <?= $arSchedule['timetable']['workTime'] ?>
                                                                        <br>
                                                                        <?
                                                                    }
                                                                }
                                                            }

                                                            if ($arTerminal['schedule']['data-ml']) {
                                                                ?>
                                                                <?= $arTerminal['schedule']['data-ml'] ?>
                                                                <?
                                                            }
                                                            ?>
                                                        </div>
                                                        <?
                                                    }

                                                    $arResult['mapParamsLimits'] = $mapParamsLimits;
                                                    if ($arResult['mapParamsLimits']['yandex_lat'] == 0) {
                                                        $arResult['mapParamsLimits']['yandex_lat'] = $arResult['mapParams']['yandex_lat'];
                                                        $arResult['mapParamsLimits']['yandex_lon'] = $arResult['mapParams']['yandex_lon'];
                                                    }
                                                    ?>
                                                </div>

                                                <?
                                                if ($arResult['OBRESHETKA_COUNT'] ) {
                                                    ?>
                                                    <div class="field__widget type-inline field-additions fa_self">
                                                        <div class="label">Дополнительно:</div>
                                                        <div class="field">
                                                            <?
                                                            foreach ($arResult['DELIVERY_SERVICES'] as $arItem) {
                                                                if( $arItem['XML_ID'] != 'obreshetka' ) {
                                                                    continue;
                                                                }
                                                                $obrPrice = $arItem['~PRICE'];
                                                                ?>
                                                                <div class="obreshetka_inner">
                                                                    <span class="obrerror">
                                                                        Мы не рекомендуем убирать дополнительную защиту для столов, в этом случае мы не несём ответственность за их повреждение при перевозке, и в данном случае необходима 100% предоплата перед отправкой.
                                                                    </span>
                                                                    <span class="obrerror_after"></span>
                                                                    <label class="label__widget">
                                                                        <span class="row">
                                                                            <span class="control">
                                                                                <input
                                                                                    type="checkbox"
                                                                                    class="delivery_service<?= ($arItem['XML_ID'] == 'obreshetka') ? ' obreshetka_srv' : '' ?>"
                                                                                    name="dserv_blv[]"
                                                                                    value="<?= $arItem['ID'] ?>"
                                                                                    num="<?= $arItem['~PRICE'] ?>"
                                                                                    autocomplete="off"/>
                                                                                <u class="square">
                                                                                    <span class="icon__check">
                                                                                        <svg>
                                                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use>
                                                                                        </svg>
                                                                                    </span>
                                                                                </u>
                                                                            </span>
                                                                            <span class="label">
                                                                                <?= $arItem['NAME'] ?> + <?= $arItem['XML_ID'] == 'obreshetka' ? '<span class="obr_price">'.$arItem['PRICE'].'</span>' : $arItem['PRICE'] ?>
                                                                            </span>
                                                                        </span>
                                                                    </label>
                                                                </div>
                                                                <?
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <?
                                                }
                                                ?>
                                                <fieldset class="info">
                                                    <div class="field__widget type-inline">
                                                        <label for="input-about-order-comment" class="label">Дополнительная информация:</label>
                                                        <?if ($arResult['DELIVERY_DAYS']) : ?>
                                                            <div class="field">
                                                                <?if ($arResult['DELIVERY_PERIOD'] != 'static') : ?>
                                                                    <span>Ориентировочный срок доставки заказа <span data-dpc-days="dpc-str"><?=$arResult['DELIVERY_DAYS']?> <?=getWord4($arResult['DELIVERY_DAYS'])?></span> после передачи в транспортную компанию. При получении нужен паспорт. </span>
                                                                <?else : ?>
                                                                    <span>Ориентировочный срок доставки заказа <span><?=$arResult['DELIVERY_DAYS']?> дней</span> после передачи в транспортную компанию. При получении нужен паспорт. </span>
                                                                <?endif;?>
                                                            </div>
                                                        <?else : ?>
                                                            <div class="field">
                                                                <span>Расчет срока доставки в данный момент недоступен</span>
                                                            </div>
                                                        <?endif;?>
                                                    </div>
                                                </fieldset>
                                                <div class="map">
                                                    <?
                                                    // определяем пункты самовывоза, которые необходимо скрыть
                                                    function subtract_placemarks($from, $delete_this) {
                                                        $result = [];
                                                        foreach ($from as $p) {
                                                            $result[] = $p['TERMINAL'];
                                                        }
                                                        foreach ($delete_this as $placemark) {
                                                            foreach ($from as $i => $placemark2) {
                                                                if ($placemark['TERMINAL'] == $placemark2['TERMINAL'])
                                                                    unset($result[$i]);
                                                            }
                                                        }
                                                        return $result;
                                                    }
                                                    $arResult['placemarksToHide'] = subtract_placemarks(
                                                        $arResult['mapParams']['PLACEMARKS'],
                                                        $arResult['mapParamsLimits']['PLACEMARKS']
                                                    );
                                                    ?>

                                                        <div id="map" class="embed map_limitation">
                                                            <?$APPLICATION->IncludeComponent(
                                                                "dsklad:map.yandex",
                                                                "",
                                                                Array(
                                                                    "COMPOSITE_FRAME_MODE" => "A",
                                                                    "COMPOSITE_FRAME_TYPE" => "AUTO",
                                                                    "CONTROLS" => array("fullscreenControl", "geolocationControl", "zoomControl"),
                                                                    "INIT_MAP_TYPE" => "yandex#map",
                                                                    "MAP_HEIGHT" => "600",
                                                                    "MAP_ID" => "OrderMap",
                                                                    "MAP_WIDTH" => "100%",
                                                                    "PLACEMARKS" => $arResult['mapParams']['PLACEMARKS'],
                                                                    "INIT_MAP_SCALE"=>$arResult['mapParams']['yandex_scale'],
                                                                    "INIT_MAP_LON"=>$arResult['mapParams']['yandex_lon'],
                                                                    "INIT_MAP_LAT"=>$arResult['mapParams']['yandex_lat'],
                                                                    'CLASTER' => "Y",
                                                                    'CLASTER_SIZE'=>32,
                                                                    'CLASTER_ZOOM' => "N",
                                                                    'OPEN_BALLOON_CLASTER' => "N",
                                                                    'OPEN_BALLOON_OBJECT' => 'Y',
                                                                    'API_KEY'=>\Dsklad\Config::getParam('api_key/yandex_map'),
                                                                    'DMAP_DISABLE_POINT'=>'Y',
                                                                )
                                                            );?>
                                                        </div>
                                                </div>
                                            </div>
                                            <?
                                        }
                                        ?>

                                        <div class="tab delivery">
                                            <div class="field__widget type-inline">
                                                <label for="input-about-address" class="label">Адрес:</label>
                                                <div class="field">
                                                    <div class="input">
                                                        <input
                                                            id="input-about-address"
                                                            class="session"
                                                            type="text"
                                                            name="address"
                                                            placeholder="Укажите улицу и номер как можно точнее"
                                                            autocomplete="off"
                                                            value="<?= (($_SESSION['ORDER']['FIELDS']['city'] == $_SESSION['DPD_CITY_NAME']) ? $_SESSION['ORDER']['FIELDS']['address'] : '') ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="field__widget type-inline">
                                                <label for="input-about-courier-comment" class="label">Комментарий курьеру:</label>
                                                <div class="field">
                                                    <div class="input">
                                                        <textarea
                                                            id="input-about-courier-comment"
                                                            name="delivery_comment"
                                                            rows="3"
                                                            placeholder="Напишите уточнения для курьера"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <?if ($arResult['DELIVERY_SERVICES']) : ?>
                                                <div class="field__widget type-inline field-additions fa_courier">
                                                    <div class="label">Дополнительно:</div>
                                                    <?if ($arResult['PERSON_TYPE'] != 2) : ?>
                                                    <div class="field">
                                                        <?
                                                        foreach ($arResult['DELIVERY_SERVICES'] as $arItem) {
                                                            $strChecked = '';
                                                            if ($arItem['CHECK']) {
                                                                $strChecked = ' checked="checked"';
                                                            } elseif( $arItem['XML_ID'] == 'obreshetka' ) {
                                                                $obrPrice = $arItem['~PRICE'];
                                                                if (!$arResult['OBRESHETKA_COUNT'] ) {
                                                                    continue;
                                                                }
                                                                $strChecked = ' checked="checked"';
                                                                ?>
                                                                <div class="obreshetka_inner">
                                                                    <span class="obrerror">
                                                                        Мы не рекомендуем убирать дополнительную защиту для столов, в этом случае мы не несём ответственность за их повреждение при перевозке, и в данном случае необходима 100% предоплата перед отправкой.
                                                                    </span>
                                                                    <span class="obrerror_after"></span>
                                                                <?
                                                            }
                                                            ?>
                                                            <label class="label__widget">
                                                                <span class="row">
                                                                    <span class="control">
                                                                        <input
                                                                            type="checkbox"
                                                                            class="delivery_service<?= ($arItem['XML_ID'] == 'obreshetka') ? ' obreshetka_srv' : '' ?>"
                                                                            name="dserv[]"
                                                                            <?if ($arItem['CODE'] == 'delivery_up_lift' && $arItem['IS_KGT'] == 'Y') : ?>
                                                                                value="upkgt"
                                                                            <?else : ?>
                                                                                value="<?= $arItem['ID'] ?>"
                                                                            <?endif;?>
                                                                            num="<?if ($arResult['PERSON_TYPE'] == 1) : ?><?= $arItem['~PRICE'] ?><?else : ?>0<?endif;?>"
                                                                            autocomplete="off" <?= $strChecked ?>
                                                                            data-dserv-code="<?=$arItem['CODE']?>"
                                                                            <?if ($arItem['IS_KGT_REQ'] == 'Y') : ?>data-kgt-req-hidden="Y"<?endif;?>
                                                                            <?if ($arItem['CODE'] == 'delivery_up_lift') : ?>
                                                                                data-dserv-upliftkgt="<?if ($arItem['IS_KGT'] == 'Y') : ?>Y<?else : ?>N<?endif;?>"
                                                                            <?endif;?>
                                                                            />
                                                                        <u class="square">
                                                                            <span class="icon__check">
                                                                                <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use></svg>
                                                                            </span>
                                                                        </u>
                                                                    </span>
                                                                    <span class="label">
                                                                        <?= $arItem['NAME'] ?>
                                                                            <?if ($arResult['PERSON_TYPE'] == 1) : ?>
                                                                                — <?= $arItem['XML_ID'] == 'obreshetka' ? '<span class="obr_price">'.$arItem['PRICE'].'</span>' : $arItem['PRICE'] ?>–
                                                                            <?endif;?>

                                                                    </span>
                                                                </span>
                                                            </label>
                                                            <?
                                                            if ($arItem['XML_ID'] == 'obreshetka' ) {
                                                                ?>
                                                                </div>
                                                                <?
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <?else : ?>
                                                    <div class="field">
                                                        <?if (count($arResult['DELIVERY_SERVICES']) > 1) : ?>
                                                            <span>Стоимость указанных  опций уточняйте у менеджера : </span>
                                                            <?foreach ($arResult['DELIVERY_SERVICES'] as $arItem) : ?>
                                                                <?if ($arItem['XML_ID'] == 'obreshetka') continue; ?>
                                                                <div> — <?= $arItem['NAME'] ?></div>
                                                            <?endforeach;?>
                                                        <?endif;?>
                                                    </div>
                                                    <?endif;?>
                                                </div>
                                            <?endif;?>

                                            <div class="field__widget type-inline field-total">
                                                <div class="label">Общая стоимость доставки:</div>
                                                <div class="field">
                                                    <? if($arResult['cantCalculateDelivery']){ ?>
                                                        <b id="delivery_coast"></b>
                                                        <span style="font-weight: 700">Доставка осуществляется согласно тарифам транспортной компании</span>
                                                    <? } elseif ($arResult['DPDnotAvailable'] || !$arResult['DELIVERY_DAYS']) {?>

                                                        <?if ($arResult['DPDnotAvailable']) : ?>
                                                            <b id="delivery_coast"></b>
                                                            Стоимость доставки уточняйте у менеджеров <a class="tel_fan_link" href="tel:88007771274" class="tel">8 800 777-12-74</a>
                                                        <?endif;?>
                                                        <?if (!$arResult['DELIVERY_DAYS']) : ?>
<!--                                                            <br/><b></b>-->
<!--                                                            <span style="font-weight: 700">Расчет срока доставки в данный момент недоступен</span>-->
                                                        <?endif;?>
                                                    <?} elseif ($arResult['DELIVERY_DAYS']) { ?>
                                                        <b id="delivery_coast"><?= $arResult['COAST'] ?></b>
                                                        <?if ($arResult['DELIVERY_PERIOD'] != 'static') : ?>
                                                            <span>Ориентировочный срок доставки заказа <l data-dpc-days="dpc-str"><?=$arResult['DELIVERY_DAYS']?> <?=getWord4($arResult['DELIVERY_DAYS'])?></l> после передачи в транспортную компанию. При получении нужен паспорт. </span>
                                                        <?else : ?>
                                                            <span>Ориентировочный срок доставки заказа <l><?=$arResult['DELIVERY_DAYS']?> дней</l> после передачи в транспортную компанию. При получении нужен паспорт. </span>
                                                        <?endif;?>
                                                    <? } else {
                                                    ?>
                                                        <span style="font-weight: 700">Расчет срока доставки в данный момент недоступен</span>
                                                    <?} ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="comment">
                                <div class="field__widget type-inline">
                                    <label for="input-about-order-comment" class="label">Комментарий к заказу:</label>
                                    <div class="field">
                                        <div class="input">
                                            <textarea
                                                id="input-about-order-comment"
                                                name="order-comment"
                                                rows="3"
                                                placeholder="Напишите ваше пожелание к заказу"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="total" data-dpd-status="<?=!$arResult['DPDnotAvailable']?1:0;?>">
                                <div class="title total_summ_title">
                                    <?
                                    $q = 0;
                                    foreach ($arResult['BASKET_ITEMS'] as $item) {
                                        $q += $item['QUANTITY'];
                                    }
                                    ?>
                                    Итого: <?= $q ?> <?= getWord3($q) ?>
                                    на
                                    сумму <?= $arResult['TOTAL_SUMM'] ?>
                                </div>
                                <div class="digits">
                                    <div class="calculation">
                                        <ul id="total_summ">
                                            <?
                                            if (!empty($arResult['TOTAL_DISCOUNT'])) {
                                                ?>
                                                <li>Стоимость: <?= $arResult['TOTAL_SUM_NOT_DISCOUNT'] ?></li>
                                                <li class="dostavka_price" num="<?= $arResult['~COAST'] ?>" data-order-page="shipping-field">Доставка: <span><?= ($arResult['cantCalculateDelivery'] || $arResult['DPDnotAvailable'])? '*': $arResult['COAST']; ?></span></li>
                                                <li class="text-red">Общая скидка: <?= $arResult['TOTAL_DISCOUNT'] ?></li>
                                                <li class="sum_price" num="<?= $arResult['~TOTAL_RESULT'] ?>" data-order-page="summ-field">
                                                    <b>К оплате: <span><?= $arResult['TOTAL_RESULT'] ?></span></b>
                                                </li>
                                                <?
                                            } else {
                                                ?>
                                                <li>Стоимость: <?= $arResult['TOTAL_SUMM'] ?></li>
                                                <li class="dostavka_price" num="<?= $arResult['~COAST'] ?>">Доставка: <span><?= ($arResult['cantCalculateDelivery'] || $arResult['DPDnotAvailable'])? '*': $arResult['COAST']; ?></span></li>
                                                <li class="sum_price" num="<?= $arResult['~TOTAL_RESULT'] ?>" data-order-page="summ-field">
                                                    <b>К оплате: <span><?= $arResult['TOTAL_RESULT'] ?></span></b>
                                                </li>
                                                <?
                                            }
                                            ?>
                                            <input type="hidden" name="delivery_price" value="<?= $arResult['~COAST'] ?>">
                                        </ul>
                                        <? if($arResult['cantCalculateDelivery']){ ?> <input type="hidden" value="1" id="delivery_calculate"/><div class="legal_warning">*Доставка осуществляется согласно тарифам транспортной компании</div><? }?>
                                        <? if($arResult['DPDnotAvailable']){ ?> <div class="legal_warning">*Расчет стоимости доставки в данный момент недоступен <br/> **Стоимость доставки уточняйте у менеджеров</div><? }?>

                                        <div class="place clearfix">
                                            <div class="button-block clearfix">
                                                <div class="button-holder">
                                                    <button class="button type-blue fill size-38 btn_send_order_process" data-ga-analys-btn="order-submit">Оформить заказ</button>
                                                    <div class="discript">
                                                        Заполните все поля
                                                    </div>
                                                </div>
                                                <p>
                                                    Я принимаю условия <a class="border-link" href="/public_offer/">публичной оферты</a> и соглашаюсь на условия обработки <a class="border-link" href="/public_offer/">персональных данных</a>.
                                                </p>
                                            </div>
                                            <label class="label__widget">
                                                <span class="row">
                                                    <span class="control">
                                                        <input type="checkbox" name="not-call" value="1" autocomplete="off">
                                                        <u class="square">
                                                            <span class="icon__check">
                                                                <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use></svg>
                                                            </span>
                                                        </u>
                                                    </span>
                                                    <span class="label">Не звонить для проверки заказа</span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="promo">
                                        <div class="promo-text" <?if (!empty($arResult['ORDER_COUPON']['COUPON'])) :?>style="display:none;"<?endif;?>>Есть промокод?</div>
                                        <div class="promo-form" <?if (!empty($arResult['ORDER_COUPON']['COUPON'])) : ?>style="display:block;"<?endif;?>>
                                            <div class="input" id="promocode">
                                                <input type="text" name="promo" placeholder="Промокод" autocomplete="off" value="<?=$arResult['ORDER_COUPON']['COUPON']?>">
                                            </div>
                                            <button type="button" class="button type-blue" id="promo">Применить</button>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </section>
            <div class="obreshetka_price"><input type="hidden" name="obreshetka_price" value="<?= $obrPrice ?>"/></div>
            <?if ($arResult['IS_KZ'] == "Y") : ?>
                <input type="hidden" name="dpd_code" value="dpd_economy_cu"/>
            <?elseif ($arResult['IS_PCL'] == 'Y') : ?>
                <input type="hidden" name="dpd_code" value="dpd_online_classic"/>
            <?else : ?>
                <input type="hidden" name="dpd_code" value="dpd_online_max"/>
            <?endif;?>
            <input type="hidden" name="delivery" value="<?= $arResult['FIRST_DELIVERY_ID'] ?>"/>
            <?if ($arResult['DPDnotAvailable']) : ?>
                <input type="hidden" name="dpd_not_availabel" value="Y" />
            <?endif;?>
            <input type="hidden" name="go" value="Y"/>
        </form>
    </div>
</div>

<div class="sum-order__popup">
    <p>Заказы больше <?= number_format(\Dsklad\Config::getOption('UF_SUM_ORDER'), 0, '.', ' ') ?> рублей оформляются менеджером при телефонном звонке по номеру
        <span class="no-wrap">
            <?
            $APPLICATION->IncludeFile(
                SITE_TEMPLATE_PATH.'/include_areas/phone.php',
                array(),
                array(
                    'MODE' => 'text'
                )
            );
            ?>
        </span>
    </p>
</div>

<?
/* vk pixel temporary removed 01.11.18
$ids = '[';
foreach ($arResult['BASKET_ITEMS'] as $arItems) {
    if ($ids != '[') {
        $ids .= ',';
    }
    $ids .= '{"id":"' . $arItems['PRODUCT_ID'] . '"}';
}
$ids .= "]";
*/
?>

<script type='text/javascript'>
    BX.message({
        MAX_SUM_ORDER: <?=\Dsklad\Config::getOption('UF_SUM_ORDER') ? : "\"\"";?>
    });

    window.plasemarksHide = <?=\Bitrix\Main\Web\Json::encode($arResult['placemarksToHide'])?>;

</script>

<?/* vk pixel temporary removed 01.11.18
<script>
    $('.btn_send_order_process').on('click', function() {
        if (typeof VK !== 'undefined') {
            var params = {'products': <?= $ids ?>};
            VK.Retargeting.ProductEvent(<?= VK_PRICE_LIST_ID ?>, 'purchase', params);
        }
    });

    $(document).ready(function() {
        if (typeof VK !== 'undefined') {
            var params = {'products': <?= $ids ?>};
            VK.Retargeting.ProductEvent(<?= VK_PRICE_LIST_ID ?>, 'init_checkout', {'products': <?= $ids ?>});
        }
    });
</script>
*/?>
<?
if (count($arResult['DELIVERY']) == 1 && isset($arResult['DELIVERY'][20])) {
    ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.wrap_container_spinner').show();

            var id = 8;
            $("input[name='delivery']").val(id);

            obj = {};
            obj.url = '/include_areas/order.php';
            obj.data = {
                delivery: id
            };
            obj.nodes = [
                '#delivery_coast',
                '#total_summ',
                '#total_summ_title',
                '.field-total',
                '#debug',
                '.payment'
            ];
            obj.success = function() {
                $('.wrap_container_spinner').hide();
            };
            update(obj);
        });
    </script>
    <?
}
?>
