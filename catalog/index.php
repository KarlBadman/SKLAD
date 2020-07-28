<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
use Bitrix\Main\Page\Asset;

$APPLICATION->SetPageProperty("description", "Интернет-магазин дизайнерской мебели «Дизайн Склад» предлагает купить оригинальные дизайнерскую мебель с доставкой по Санкт Петербургу и России. Заказать современную мебель онлайн — это просто! Выгодная цена и качество!");
$APPLICATION->SetPageProperty('keywords', 'стулья для кухни стулья для кафе купить стулья оптом барные стулья столы обеденные доставка по россии');
$APPLICATION->SetPageProperty('title', 'Купить дизайнерскую мебель со скидкой в Спб: цена | Акции на современные столы и стулья в "Дизайн Склад"');
$APPLICATION->SetTitle("Каталог");?>

<?$APPLICATION->IncludeComponent(
    "bitrix:catalog",
    "catalogDsklad",
    Array(
        "ACTION_VARIABLE" => "action",
        "ADD_ELEMENT_CHAIN" => "Y",
        "ADD_PROPERTIES_TO_BASKET" => "Y",
        "ADD_SECTIONS_CHAIN" => "Y",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "BASKET_URL" => "/personal/basket.php",
        "BIG_DATA_RCM_TYPE" => "personal",
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "36000000",
        "CACHE_TYPE" => $GLOBALS['COMPONENT_CACHE'],
        "COMMON_ADD_TO_BASKET_ACTION" => "ADD",
        "COMMON_SHOW_CLOSE_POPUP" => "N",
        "COMPATIBLE_MODE" => "Y",
        "COMPOSITE_FRAME_MODE" => "A",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "CONVERT_CURRENCY" => "N",
        "DETAIL_ADD_DETAIL_TO_SLIDER" => "N",
        "DETAIL_ADD_TO_BASKET_ACTION" => array("BUY"),
        "DETAIL_BACKGROUND_IMAGE" => "-",
        "DETAIL_BRAND_USE" => "N",
        "DETAIL_BROWSER_TITLE" => "-",
        "DETAIL_CHECK_SECTION_ID_VARIABLE" => "N",
        "DETAIL_DETAIL_PICTURE_MODE" => array("IMG"),
        "DETAIL_DISPLAY_NAME" => "Y",
        "DETAIL_DISPLAY_PREVIEW_TEXT_MODE" => "E",
        "DETAIL_IMAGE_RESOLUTION" => "16by9",
        "DETAIL_META_DESCRIPTION" => "-",
        "DETAIL_META_KEYWORDS" => "-",
        "DETAIL_OFFERS_FIELD_CODE" => array("",""),
        "DETAIL_OFFERS_PROPERTY_CODE" => array(
            0 => 'FOTOGRAFIYA_5',
            1 => 'FOTOGRAFIYA_6',
            2 => 'TSVET_NOZHEK',
            3 => 'MATERIAL_STOLESHNITSY',
            4 => 'RAZMER_SH_KH_G_KH_V',
            5 => 'MATERIAL_STOLESHNITSY_1',
            6 => 'FOTOGRAFIYA_1',
            7 => 'KOD_TSVETA',
            8 => 'CML2_ATTRIBUTES',
            9 => 'UPAKOVKA_1_1',
            10 => 'TSVET_STOLESHNITSY',
            11 => 'FOTOGRAFIYA_2',
            12 => 'FOTOGRAFIYA_3',
            13 => 'FOTOGRAFIYA_4',
            14 => 'UPAKOVKA_2_1',
            15 => 'UPAKOVKA_3_1',
            16 => 'UPAKOVKA_4_1',
            17 => 'MATERIAL_NOZHEK',
            18 => 'MATERIAL_NOZHEK_1',
            19 => 'MATERIAL_SEDLA',
            20 => 'TSVET_NOZHEK_1',
            21 => 'TSVET_SEDLA',
            22 => 'MATERIAL_NOZHEK_2',
            23 => 'TOLSHCHINA_STOLESHNITSY_1',
            24 => 'TIP_POVERKHNOSTI',
            25 => 'TIP_POVERKHNOSTI_1',
            26 => 'TSVET_STOLESHNITSY_1',
            27 => 'TSVET_NOZHEK_2',
            28 => 'DIAMETR_STOLESHNITSY',
            29 => 'STRANA_PROISKHOZHDENIYA',
            30 => 'RAZMER_SH_KH_G_KH_V_1',
            31 => 'VYSOTA_DO_SIDENYA',
            32 => 'MAKSIMALNAYA_NAGRUZKA',
            33 => 'MAKSIMALNAYA_NAGRUZKA_1',
            34 => 'VYSOTA_DO_SIDENYA_1',
            35 => 'MATERIAL_NOZHEK_3',
            36 => 'RAZMER_SH_KH_G_KH_V_2',
            37 => 'VYSOTA_PODLOKOTNIKOV',
            38 => 'VYSOTA_PODLOKOTNIKOV_1',
            39 => 'DIAMETR_STOLESHNITSY_1',
            40 => 'RAZMER_STOLESHNITSY',
            41 => 'VES',
            42 => 'VYSOTA_STOLESHNITSY',
            43 => 'VYSOTA_STOLESHNITSY_1',
            44 => 'VYSOTA_SIDENYA_2',
            45 => 'RAZMER_STOLESHNITSY_1',
            46 => 'NOZHKI',
            47 => 'GABARITY_SH_KH_G_KH_V',
            48 => 'MATERIAL',
            49 => 'OBRESHETKA',
            50 => 'OBRESHETKA_1'
        ),
        "DETAIL_PRODUCT_INFO_BLOCK_ORDER" => "sku,props",
        "DETAIL_PRODUCT_PAY_BLOCK_ORDER" => "rating,price,priceRanges,quantityLimit,quantity,buttons",
        "DETAIL_PROPERTY_CODE" => array(
            0 => 'TSVET_NOZHEK',
            1 => 'MATERIAL_STOLESHNITSY',
            2 => 'RAZMER_SH_KH_G_KH_V',
            3 => 'MATERIAL_STOLESHNITSY_1',
            4 => 'MATERIAL_SEDLA_1',
            5 => 'TSVET_STOLESHNITSY',
            6 => 'TOLSHCHINA_STOLESHNITSY',
            7 => 'MATERIAL_NOZHEK',
            8 => 'MATERIAL_NOZHEK_1',
            9 => 'MATERIAL_SEDLA',
            10 => 'TSVET_NOZHEK_1',
            11 => 'TSVET_SEDLA',
            12 => 'MATERIAL_NOZHEK_2',
            13 => 'TOLSHCHINA_STOLESHNITSY_1',
            14 => 'TIP_POVERKHNOSTI',
            15 => 'TIP_POVERKHNOSTI_1',
            16 => 'TSVET_STOLESHNITSY_1',
            17 => 'TSVET_NOZHEK_2',
            18 => 'DIAMETR_STOLESHNITSY',
            19 => 'STRANA_PROISKHOZHDENIYA',
            20 => 'RAZMER_SH_KH_G_KH_V_1',
            21 => 'VYSOTA_DO_SIDENYA',
            22 => 'MAKSIMALNAYA_NAGRUZKA',
            23 => 'MAKSIMALNAYA_NAGRUZKA_1',
            24 => 'VYSOTA_DO_SIDENYA_1',
            25 => 'MATERIAL_NOZHEK_3',
            26 => 'RAZMER_SH_KH_G_KH_V_2',
            27 => 'VYSOTA_PODLOKOTNIKOV',
            28 => 'VYSOTA_PODLOKOTNIKOV_1',
            29 => 'DIAMETR_STOLESHNITSY_1',
            30 => 'RAZMER_STOLESHNITSY',
            31 => 'VES',
            32 => 'VYSOTA_STOLESHNITSY',
            33 => 'VYSOTA_STOLESHNITSY_1',
            34 => 'GABARITY',
            35 => 'VYSOTA_SIDENYA',
            36 => 'VYSOTA_SIDENYA_2',
            37 => 'MATERIAL_1',
            38 => 'NOZHKI_1',
            39 => 'GABARITY_SH_KH_G_KH_V_1',
            40 => 'DIAMETR_ABAZHURA',
            41 => 'LAMPA',
            42 => 'LAMPA_1',
            43 => 'NOZHKI',
            44 => 'MATERIAL',
            45 => 'VYSOTA',
            46 => 'VYSOTA_1',
            47 => 'MATERIAL_ABAZHURA',
            48 => 'SHNUR',
            49 => 'TSVET_OSNOVANIYA',
            50 => 'TSVET',
            51 => 'RAZMER_SH_KH_G_KH_V_3',
            52 => 'RAZMER_SH_KH_G_KH_V_4',
            53 => 'DIAMETR_ABAZHURA_1',
            54 => 'NOGI',
            55 => 'GABARITY_1',
            56 => 'VYSOTA_2',
            57 => 'GABARITY_SH_KH_G_KH_V_2',
            58 => 'RAZMER_STOLESHNITSY_1',
            59 => 'GABARITY1',
            60 => 'GABARITY_SH_KH_G_KH_V',
            61 => 'INTERIOR',
            63 => 'MIN_CHECK',
            65 => 'MIN_PRICE',
            66 => 'RAZMER_STOLA_SH_KH_G_KH_V',
            67 => 'RAZMER_STULA_SH_KH_G_KH_V',
            68 => 'MATERIALY_STULEV',
            69 => 'MATERIALY_STOLA',
            70 => 'RAZMER_D_KH_SH',
            71 => 'OBRESHETKA',
            72 => 'OBRESHETKA_1'
        ),
        "DETAIL_SET_CANONICAL_URL" => "N",
        "DETAIL_SET_VIEWED_IN_COMPONENT" => "T",
        "DETAIL_SHOW_POPULAR" => "Y",
        "DETAIL_SHOW_SLIDER" => "N",
        "DETAIL_SHOW_VIEWED" => "Y",
        "DETAIL_STRICT_SECTION_CHECK" => "N",
        "DETAIL_USE_COMMENTS" => "N",
        "DETAIL_USE_VOTE_RATING" => "N",
        "DISABLE_INIT_JS_IN_COMPONENT" => "N",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "DISPLAY_TOP_PAGER" => "N",
        "ELEMENT_SORT_FIELD" => "sort",
        "ELEMENT_SORT_FIELD2" => "id",
        "ELEMENT_SORT_ORDER" => "asc",
        "ELEMENT_SORT_ORDER2" => "desc",
        "FILTER_HIDE_ON_MOBILE" => "N",
        "FILTER_VIEW_MODE" => "VERTICAL",
        "GIFTS_DETAIL_BLOCK_TITLE" => "Выберите один из подарков",
        "GIFTS_DETAIL_HIDE_BLOCK_TITLE" => "N",
        "GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => "3",
        "GIFTS_DETAIL_TEXT_LABEL_GIFT" => "Подарок",
        "GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE" => "Выберите один из товаров, чтобы получить подарок",
        "GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE" => "N",
        "GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => "4",
        "GIFTS_MESS_BTN_BUY" => "Выбрать",
        "GIFTS_SECTION_LIST_BLOCK_TITLE" => "Подарки к товарам этого раздела",
        "GIFTS_SECTION_LIST_HIDE_BLOCK_TITLE" => "N",
        "GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT" => "4",
        "GIFTS_SECTION_LIST_TEXT_LABEL_GIFT" => "Подарок",
        "GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
        "GIFTS_SHOW_IMAGE" => "Y",
        "GIFTS_SHOW_NAME" => "Y",
        "GIFTS_SHOW_OLD_PRICE" => "Y",
        "HIDE_NOT_AVAILABLE" => "N",
        "HIDE_NOT_AVAILABLE_OFFERS" => "N",
        "IBLOCK_ID" => \Dsklad\Config::getParam('iblock/catalog'),
        "IBLOCK_TYPE" => "1c_catalog",
        "INCLUDE_SUBSECTIONS" => "Y",
        "INSTANT_RELOAD" => "Y",
        "LAZY_LOAD" => "N",
        "LINE_ELEMENT_COUNT" => "3",
        "LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
        "LINK_IBLOCK_ID" => "",
        "LINK_IBLOCK_TYPE" => "",
        "LINK_PROPERTY_SID" => "",
        "LIST_BROWSER_TITLE" => "-",
        "LIST_META_DESCRIPTION" => "-",
        "LIST_META_KEYWORDS" => "-",
        "LIST_OFFERS_FIELD_CODE" => array("",""),
        "LIST_OFFERS_LIMIT" => "",
        "LIST_OFFERS_PROPERTY_CODE" => array(
            0 => 'TSVET_NOZHEK',
            1 => 'MATERIAL_STOLESHNITSY',
            2 => 'RAZMER_SH_KH_G_KH_V',
            3 => 'MATERIAL_STOLESHNITSY_1',
            4 => 'MATERIAL_SEDLA_1',
            5 => 'TSVET_STOLESHNITSY',
            6 => 'TOLSHCHINA_STOLESHNITSY',
            7 => 'MATERIAL_NOZHEK',
            8 => 'MATERIAL_NOZHEK_1',
            9 => 'MATERIAL_SEDLA',
            10 => 'TSVET_NOZHEK_1',
            11 => 'TSVET_SEDLA',
            12 => 'MATERIAL_NOZHEK_2',
            13 => 'TOLSHCHINA_STOLESHNITSY_1',
            14 => 'TIP_POVERKHNOSTI',
            15 => 'TIP_POVERKHNOSTI_1',
            16 => 'TSVET_STOLESHNITSY_1',
            17 => 'TSVET_NOZHEK_2',
            18 => 'DIAMETR_STOLESHNITSY',
            19 => 'STRANA_PROISKHOZHDENIYA',
            20 => 'RAZMER_SH_KH_G_KH_V_1',
            21 => 'VYSOTA_DO_SIDENYA',
            22 => 'MAKSIMALNAYA_NAGRUZKA',
            23 => 'MAKSIMALNAYA_NAGRUZKA_1',
            24 => 'VYSOTA_DO_SIDENYA_1',
            25 => 'MATERIAL_NOZHEK_3',
            26 => 'RAZMER_SH_KH_G_KH_V_2',
            27 => 'VYSOTA_PODLOKOTNIKOV',
            28 => 'VYSOTA_PODLOKOTNIKOV_1',
            29 => 'DIAMETR_STOLESHNITSY_1',
            30 => 'RAZMER_STOLESHNITSY',
            31 => 'VES',
            32 => 'VYSOTA_STOLESHNITSY',
            33 => 'VYSOTA_STOLESHNITSY_1',
            34 => 'GABARITY',
            35 => 'VYSOTA_SIDENYA',
            36 => 'VYSOTA_SIDENYA_2',
            37 => 'MATERIAL_1',
            38 => 'NOZHKI_1',
            39 => 'GABARITY_SH_KH_G_KH_V_1',
            40 => 'DIAMETR_ABAZHURA',
            41 => 'LAMPA',
            42 => 'LAMPA_1',
            43 => 'NOZHKI',
            44 => 'MATERIAL',
            45 => 'VYSOTA',
            46 => 'VYSOTA_1',
            47 => 'MATERIAL_ABAZHURA',
            48 => 'SHNUR',
            49 => 'TSVET_OSNOVANIYA',
            50 => 'TSVET',
            51 => 'RAZMER_SH_KH_G_KH_V_3',
            52 => 'RAZMER_SH_KH_G_KH_V_4',
            53 => 'DIAMETR_ABAZHURA_1',
            54 => 'NOGI',
            55 => 'GABARITY_1',
            56 => 'VYSOTA_2',
            57 => 'GABARITY_SH_KH_G_KH_V_2',
            58 => 'RAZMER_STOLESHNITSY_1',
            59 => 'GABARITY1',
            60 => 'GABARITY_SH_KH_G_KH_V',
            61 => 'INTERIOR',
            63 => 'MIN_CHECK',
            65 => 'MIN_PRICE',
            66 => 'RAZMER_STOLA_SH_KH_G_KH_V',
            67 => 'RAZMER_STULA_SH_KH_G_KH_V',
            68 => 'MATERIALY_STULEV',
            69 => 'MATERIALY_STOLA',
            70 => 'RAZMER_D_KH_SH',
            71 => 'OBRESHETKA',
            72 => 'OBRESHETKA_1'
        ),
        "LIST_PROPERTY_CODE" => array("LABLE_CATALOG","NEW","HIT","SALE"),
        "LOAD_ON_SCROLL" => "N",
        "MESSAGE_404" => "",
        "MESS_BTN_ADD_TO_BASKET" => "В корзину",
        "MESS_BTN_BUY" => "Купить",
        "MESS_BTN_COMPARE" => "Сравнение",
        "MESS_BTN_DETAIL" => "Подробнее",
        "MESS_BTN_SUBSCRIBE" => "Подписаться",
        "MESS_COMMENTS_TAB" => "Комментарии",
        "MESS_DESCRIPTION_TAB" => "Описание",
        "MESS_NOT_AVAILABLE" => "Нет в наличии",
        "MESS_PRICE_RANGES_TITLE" => "Цены",
        "MESS_PROPERTIES_TAB" => "Характеристики",
        "OFFERS_CART_PROPERTIES" => array(
            0 => 'UPAKOVKA_1_1',
            1 => 'UPAKOVKA_2_1',
            2 => 'UPAKOVKA_3_1',
            3 => 'UPAKOVKA_4_1',
            4 => 'TIP_POVERKHNOSTI',
            5 => 'RAZMER_STOLESHNITSY',
        ),
        "OFFERS_SORT_FIELD" => "sort",
        "OFFERS_SORT_FIELD2" => "CATALOG_AVAILABLE",
        "OFFERS_SORT_ORDER" => "asc",
        "OFFERS_SORT_ORDER2" => "desc",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => "catalogDsklad",
        "PAGER_TITLE" => "Товары",
        "PAGE_ELEMENT_COUNT" => "16",
        "PARTIAL_PRODUCT_PROPERTIES" => "N",
        "PRICE_CODE" => array("Базовая цена продажи","Основная цена продажи"),
        "PRICE_VAT_INCLUDE" => "Y",
        "PRICE_VAT_SHOW_VALUE" => "N",
        "PRODUCT_ID_VARIABLE" => "id",
        "PRODUCT_PROPERTIES" => array(
            0 => 'MATERIAL_STOLESHNITSY',
            1 => 'RAZMER_STOLESHNITSY',
        ),
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
        "PRODUCT_SUBSCRIPTION" => "N",
        "SEARCH_CHECK_DATES" => "Y",
        "SEARCH_NO_WORD_LOGIC" => "Y",
        "SEARCH_PAGE_RESULT_COUNT" => "50",
        "SEARCH_RESTART" => "N",
        "SEARCH_USE_LANGUAGE_GUESS" => "Y",
        "SECTIONS_SHOW_PARENT_NAME" => "Y",
        "SECTIONS_VIEW_MODE" => "LIST",
        "SECTION_ADD_TO_BASKET_ACTION" => "ADD",
        "SECTION_BACKGROUND_IMAGE" => "-",
        "SECTION_COUNT_ELEMENTS" => "Y",
        "SECTION_ID_VARIABLE" => "SECTION_ID",
        "SECTION_TOP_DEPTH" => "2",
        "SEF_FOLDER" => "/catalog/",
        "SEF_MODE" => "Y",
        "SEF_URL_TEMPLATES" => Array(
            "compare"=>"compare.php?action=#ACTION_CODE#",
            "element"=>"#SECTION_CODE#/#ELEMENT_CODE#/",
            "elementOffer"=>"#SECTION_CODE#/#ELEMENT_CODE#/([0-9]+)/",
            "section"=>"#SECTION_CODE#/",
            "sections"=>"",
            "smart_filter"=>"#SECTION_CODE_PATH#/filter/#SMART_FILTER_PATH#/apply/"
        ),
        "SET_LAST_MODIFIED" => "N",
        "SET_STATUS_404" => "Y",
        "SET_TITLE" => "Y",
        "SHOW_404" => "Y",
        "SHOW_DEACTIVATED" => "N",
        "SHOW_DISCOUNT_PERCENT" => "N",
        "SHOW_MAX_QUANTITY" => "N",
        "SHOW_OLD_PRICE" => "N",
        "SHOW_PRICE_COUNT" => "1",
        "SHOW_TOP_ELEMENTS" => "Y",
        "SIDEBAR_DETAIL_SHOW" => "N",
        "SIDEBAR_PATH" => "",
        "SIDEBAR_SECTION_SHOW" => "Y",
        "TEMPLATE_THEME" => "blue",
        "TOP_ADD_TO_BASKET_ACTION" => "ADD",
        "TOP_ELEMENT_COUNT" => "9",
        "TOP_ELEMENT_SORT_FIELD" => "sort",
        "TOP_ELEMENT_SORT_FIELD2" => "id",
        "TOP_ELEMENT_SORT_ORDER" => "asc",
        "TOP_ELEMENT_SORT_ORDER2" => "desc",
        "TOP_LINE_ELEMENT_COUNT" => "3",
        "TOP_OFFERS_FIELD_CODE" => array("",""),
        "TOP_OFFERS_LIMIT" => "5",
        "TOP_OFFERS_PROPERTY_CODE" => array("",""),
        "TOP_PROPERTY_CODE" => array("",""),
        "USER_CONSENT" => "N",
        "USER_CONSENT_ID" => "0",
        "USER_CONSENT_IS_CHECKED" => "Y",
        "USER_CONSENT_IS_LOADED" => "N",
        "USE_ALSO_BUY" => "N",
        "USE_BIG_DATA" => "Y",
        "USE_COMMON_SETTINGS_BASKET_POPUP" => "N",
        "USE_COMPARE" => "N",
        "USE_ELEMENT_COUNTER" => "Y",
        "USE_ENHANCED_ECOMMERCE" => "N",
        "USE_FILTER" => "N",
        "USE_GIFTS_DETAIL" => "Y",
        "USE_GIFTS_MAIN_PR_SECTION_LIST" => "Y",
        "USE_GIFTS_SECTION" => "Y",
        "USE_MAIN_ELEMENT_SECTION" => "N",
        "USE_PRICE_COUNT" => "Y",
        "USE_PRODUCT_QUANTITY" => "Y",
        "USE_SALE_BESTSELLERS" => "Y",
        "USE_STORE" => "N",
        'OFFERS_SELECT_PROPERTY'=>['KOD_TSVETA','RAZMER_STOLESHNITSY', 'RAZMER_STOLESHNITSY_1', 'VID_KH_KA'],
        'OFFERS_PROPERTY_TYPE_IMAGES_LINK'=>['KOD_TSVETA'],
        'BALANCE_ON_STOCK'=>'BALANCE_ON_STOCK',
        'OFFERS_PROPERTY_TYPE_SM'=>['RAZMER_STOLESHNITSY','RAZMER_STOLESHNITSY_1'],
        'SECTION_TOP_SECTION_USER_FIELDS'=>['UF*'],
        'SECTION_TOP_SECTION_FIELDS'=>["ID", "CODE", "NAME", "PICTURE", "DETAIL_PICTURE", ""],
        'SECTION_TOP_FILTER_NAME'=>"filterSectionCatalog",
        'BANNER_CATALOG_SECTION'=>"in_index_1",
        'BANNER_MOBILE_CATALOG_SECTION'=>"in_index_mobail_1"
    )
);?>

<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>