<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->IncludeComponent(
    "bitrix:sale.basket.basket",
    "json",
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
        "ID_WARRANTY" => '14202',
        "LABEL_PROP" => array(),
        "OFFERS_PROPS" => array(),
        "PATH_TO_ORDER" => "/personal/order/make/",
        "PRICE_DISPLAY_MODE" => "Y",
        "PRICE_TYPE" => '2',
        "PRICE_VAT_SHOW_VALUE" => "N",
        "PRODUCT_BLOCKS_ORDER" => "props,sku,columns",
        "QUANTITY_FLOAT" => "Y",
        "RECOMMENDED_ITEMS_IN_CART" => 'RECOMMENDED_ITEMS_IN_CART',
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
    )
);
