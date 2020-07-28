<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$APPLICATION->AddViewContent('page_type', 'data-page-type="order-base"');

$this->addExternalCss('/local/assets/css/dsklad-styles.css');
$this->addExternalJS(SITE_TEMPLATE_PATH.'/js/handlebars-v4.1.2.js');
$this->addExternalJS('/local/assets/js/jquery.inputmask.min.js');
$this->addExternalJS('/local/assets/js/jquery.inputmask-multi.js');
$this->addExternalJS('/local/assets/js/jquery.inputmask-conf.js');
$this->addExternalJS(SITE_TEMPLATE_PATH.'/js/parsley.js');
$this->addExternalJS(SITE_TEMPLATE_PATH.'/js/parsley-ru.js');
?>
<div class="ds-wrapper">
    <div class="ds-basket-header">
        <h1>Корзина</h1>
    </div>
    <div class="ds-basket-city">
        <div class="ds-basket-city__item" data-block-name="city_name">
            <?$APPLICATION->IncludeComponent(
                'swebs:dpd.current.city',
                '',
                array(
                    'DPD_HL_ID' => \Dsklad\Config::getParam('hl/dpd_cities'),
                    'COMPONENT_TEMPLATE' => 'new_order'
                ),
                false
            );?>
        </div>
        <div class="ds-basket-city__btn">
            <a href="<?= SITE_TEMPLATE_PATH ?>/ajax/region.php" class="js-ds-modal region" id="city_link" data-ds-modal-width="520">
                Изменить
            </a>
        </div>
        <div class="ds-basket-city__text">Доступность способов доставки и оплаты заказа зависит от выбранного города.</div>
    </div>
    <div class="spinner hidden"></div>
    <div class="ds-basket" data-order-page="basket-area">
        <?$APPLICATION->IncludeComponent(
            "bitrix:sale.basket.basket",
            "show",
            Array(
                "ACTION_VARIABLE" => "basketAction",
                "ADDITIONAL_PICT_PROP_35" => "-",
                "ADDITIONAL_PICT_PROP_36" => "-",
                "ADDITIONAL_PICT_PROP_37" => "-",
                "ADDITIONAL_PICT_PROP_38" => "-",
                "AUTO_CALCULATION" => "Y",
                "BASKET_IMAGES_SCALING" => "adaptive",
                "COLUMNS_LIST_EXT" => array("PREVIEW_PICTURE","DISCOUNT","DELETE","DELAY","TYPE","SUM","PROPERTY_FOTOGRAFIYA_1","PROPERTY_CML2_ARTICLE","PROPERTY_KOD_TSVETA","PROPERTY_WITH_THIS","PROPERTY_DOPOLNITELNAYA_GARANTIYA_OTSUTSTVUET"),
                "COLUMNS_LIST_MOBILE" => array("PREVIEW_PICTURE","DISCOUNT","DELETE","DELAY","TYPE","SUM"),
                "COMPATIBLE_MODE" => "Y",
                "COMPOSITE_FRAME_MODE" => "A",
                "COMPOSITE_FRAME_TYPE" => "AUTO",
                "CORRECT_RATIO" => "Y",
                "DEFERRED_REFRESH" => "N",
                "DISCOUNT_PERCENT_POSITION" => "bottom-right",
                "DISPLAY_MODE" => "extended",
                "EMPTY_BASKET_HINT_PATH" => "/",
                "GIFTS_BLOCK_TITLE" => "Выберите один из подарков",
                "GIFTS_CONVERT_CURRENCY" => "N",
                "GIFTS_HIDE_BLOCK_TITLE" => "N",
                "GIFTS_HIDE_NOT_AVAILABLE" => "N",
                "GIFTS_MESS_BTN_BUY" => "Выбрать",
                "GIFTS_MESS_BTN_DETAIL" => "Подробнее",
                "GIFTS_PAGE_ELEMENT_COUNT" => "4",
                "GIFTS_PLACE" => "BOTTOM",
                "GIFTS_PRODUCT_PROPS_VARIABLE" => "prop",
                "GIFTS_PRODUCT_QUANTITY_VARIABLE" => "quantity",
                "GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
                "GIFTS_SHOW_OLD_PRICE" => "N",
                "GIFTS_TEXT_LABEL_GIFT" => "Подарок",
                "HIDE_COUPON" => "N",
                "ID_IBLOCK_SERVICE" => 37,
                "ID_WARRANTY" => $arParams['ID_WARRANTY'],
                "LABEL_PROP" => array(),
                "OFFERS_PROPS" => array(),
                "PATH_TO_ORDER" => "/personal/order/make/",
                "PRICE_DISPLAY_MODE" => "Y",
                "PRICE_TYPE" => $arParams['PRICE_TYPE'],
                "PRICE_VAT_SHOW_VALUE" => "N",
                "PRODUCT_BLOCKS_ORDER" => "props,sku,columns",
                "QUANTITY_FLOAT" => "Y",
                "RECOMMENDED_ITEMS_IN_CART" => $arParams['RECOMMENDED_ITEMS_IN_CART'],
                "SET_TITLE" => "Y",
                "SHOW_DISCOUNT_PERCENT" => "Y",
                "SHOW_FILTER" => "N",
                "SHOW_RESTORE" => "N",
                "TEMPLATE_THEME" => "blue",
                "TOTAL_BLOCK_DISPLAY" => array("top"),
                "USE_DYNAMIC_SCROLL" => "Y",
                "USE_ENHANCED_ECOMMERCE" => "N",
                "USE_GIFTS" => "Y",
                "USE_PREPAYMENT" => "N",
                "USE_PRICE_ANIMATION" => "Y",
                "NO_WARANTY"=>"NO_WARANTY",
            )
        );?>
        <?if(!empty($GLOBALS['filterBasketRecommended']['ID'])):?>
            <div class="ds-basket__goods-recommend border">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:catalog.section",
                    "basket.recommended",
                    Array(
                        "ACTION_VARIABLE" => "action",
                        "ADD_PROPERTIES_TO_BASKET" => "Y",
                        "ADD_SECTIONS_CHAIN" => "N",
                        "ADD_TO_BASKET_ACTION" => "ADD",
                        "AJAX_MODE" => "N",
                        "AJAX_OPTION_ADDITIONAL" => "",
                        "AJAX_OPTION_HISTORY" => "N",
                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "Y",
                        "BACKGROUND_IMAGE" => "-",
                        "BASKET_URL" => "/personal/basket.php",
                        "BROWSER_TITLE" => "-",
                        "CACHE_FILTER" => "N",
                        "CACHE_GROUPS" => "N",
                        "CACHE_TIME" => "36000000",
                        "CACHE_TYPE" => "N",
                        "COMPATIBLE_MODE" => "Y",
                        "COMPOSITE_FRAME_MODE" => "A",
                        "COMPOSITE_FRAME_TYPE" => "AUTO",
                        "CONVERT_CURRENCY" => "N",
                        "CUSTOM_FILTER" => "",
                        "DETAIL_URL" => "",
                        "DISABLE_INIT_JS_IN_COMPONENT" => "N",
                        "DISPLAY_BOTTOM_PAGER" => "Y",
                        "DISPLAY_COMPARE" => "N",
                        "DISPLAY_TOP_PAGER" => "N",
                        "ELEMENT_SORT_FIELD" => "sort",
                        "ELEMENT_SORT_FIELD2" => "id",
                        "ELEMENT_SORT_ORDER" => "asc",
                        "ELEMENT_SORT_ORDER2" => "desc",
                        "ENLARGE_PRODUCT" => "STRICT",
                        "FILTER_NAME" => "filterBasketRecommended",
                        "HIDE_NOT_AVAILABLE" => "N",
                        "HIDE_NOT_AVAILABLE_OFFERS" => "N",
                        "IBLOCK_ID" => \Dsklad\Config::getParam('iblock/offers'),
                        "IBLOCK_TYPE" => "1c_catalog",
                        "INCLUDE_SUBSECTIONS" => "Y",
                        "LAZY_LOAD" => "N",
                        "LINE_ELEMENT_COUNT" => "3",
                        "LOAD_ON_SCROLL" => "N",
                        "MESSAGE_404" => "",
                        "MESS_BTN_ADD_TO_BASKET" => "В корзину",
                        "MESS_BTN_BUY" => "Купить",
                        "MESS_BTN_DETAIL" => "Подробнее",
                        "MESS_BTN_SUBSCRIBE" => "Подписаться",
                        "MESS_NOT_AVAILABLE" => "Нет в наличии",
                        "META_DESCRIPTION" => "-",
                        "META_KEYWORDS" => "-",
                        "OFFERS_CART_PROPERTIES" => array(),
                        "OFFERS_FIELD_CODE" => array("",""),
                        "OFFERS_LIMIT" => "5",
                        "OFFERS_PROPERTY_CODE" => array("",""),
                        "OFFERS_SORT_FIELD" => "sort",
                        "OFFERS_SORT_FIELD2" => "id",
                        "OFFERS_SORT_ORDER" => "asc",
                        "OFFERS_SORT_ORDER2" => "desc",
                        "PAGER_BASE_LINK_ENABLE" => "N",
                        "PAGER_DESC_NUMBERING" => "N",
                        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                        "PAGER_SHOW_ALL" => "N",
                        "PAGER_SHOW_ALWAYS" => "N",
                        "PAGER_TEMPLATE" => ".default",
                        "PAGER_TITLE" => "Товары",
                        "PAGE_ELEMENT_COUNT" => "100",
                        "PARTIAL_PRODUCT_PROPERTIES" => "N",
                        "PRICE_CODE" => array("Базовая цена продажи","Основная цена продажи"),
                        "PRICE_VAT_INCLUDE" => "Y",
                        "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
                        "PRODUCT_ID_VARIABLE" => "id",
                        "PRODUCT_PROPERTIES" => array(),
                        "PRODUCT_PROPS_VARIABLE" => "prop",
                        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                        "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
                        "PRODUCT_SUBSCRIPTION" => "Y",
                        "PROPERTY_CODE" => array("","FOTOGRAFIYA_1",""),
                        "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
                        "RCM_TYPE" => "personal",
                        "SECTION_CODE" => "",
                        "SECTION_ID" => "",
                        "SECTION_ID_VARIABLE" => "SECTION_ID",
                        "SECTION_URL" => "",
                        "SECTION_USER_FIELDS" => array("",""),
                        "SEF_MODE" => "N",
                        "SET_BROWSER_TITLE" => "Y",
                        "SET_LAST_MODIFIED" => "N",
                        "SET_META_DESCRIPTION" => "Y",
                        "SET_META_KEYWORDS" => "Y",
                        "SET_STATUS_404" => "N",
                        "SET_TITLE" => "Y",
                        "SHOW_404" => "N",
                        "SHOW_ALL_WO_SECTION" => "Y",
                        "SHOW_CLOSE_POPUP" => "N",
                        "SHOW_DISCOUNT_PERCENT" => "N",
                        "SHOW_FROM_SECTION" => "N",
                        "SHOW_MAX_QUANTITY" => "N",
                        "SHOW_OLD_PRICE" => "N",
                        "SHOW_PRICE_COUNT" => "1",
                        "SHOW_SLIDER" => "Y",
                        "TEMPLATE_THEME" => "blue",
                        "USE_ENHANCED_ECOMMERCE" => "N",
                        "USE_MAIN_ELEMENT_SECTION" => "N",
                        "USE_PRICE_COUNT" => "N",
                        "USE_PRODUCT_QUANTITY" => "N",
                        "NO_WARANTY"=>$arParams['NO_WARANTY'],
                    )
                );?>
            </div>
        <?endif;?>
    </div>
</div>
<?if(!$USER->IsAuthorized()):?>
    <div class="hide" data-name="phone_modal" id="phone_modal">
        <div class="ds-modal__body">
            <?$test = $APPLICATION->IncludeComponent(
                'dsklad:sale.confirm.phone',
                '',
                array(
                    'PAYMENTS_SELECTOR' => '.payment .options label',  //css-селектор кнопок выбора способа оплаты
                    'PAYMENT_CONFIRM_SELECTOR' => '.payconfirm',  //css-селектор кнопки способа оплаты, при котором нужно подтверждать телефон
                    'PHONE_INPUT_SELECTOR' => "input[data-name='is_phone']",  //css-селектор поля с номером телефона
                    'WAIT_TIME' => \Dsklad\Config::getOption('UF_CONF_PHONE_TIME'),  //время до повторной отправки
                    'LENGTH' => \Dsklad\Config::getOption('UF_CONF_PHONE_LENGTH'),  //длина кода
                    'RELOAD'=>'N', // Перегружать страницу после авторизации,
                    'NO_CONFORM_CODE' =>  \Dsklad\Config::getOption('UF_NO_CONFORM_CODE'), // коды телефонов для которых не нужно подтверждения
                ),
                false
            );?>
        </div>
    </div>
<?endif;?>
<script>
    window.dsBasket.autorized = '<?if($USER->IsAuthorized()){echo 'Y';}else{echo 'N';}?>';
</script>


