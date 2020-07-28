<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Новый заказ");
if ($_REQUEST['ORDER_ID'] && $_REQUEST['ORDER_AUTH']) {
    authorizeUserByToken($_REQUEST['ORDER_ID'], $_REQUEST['ORDER_AUTH']);
}
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.order.ajax",
	"show",
	Array(
		"ACTION_VARIABLE" => "action",
		"ADDITIONAL_PICT_PROP_35" => "-",
		"ADDITIONAL_PICT_PROP_36" => "-",
		"ADDITIONAL_PICT_PROP_37" => "-",
		"ADDITIONAL_PICT_PROP_38" => "-",
		"ADDITIONAL_PICT_PROP_46" => "-",
		"ADDITIONAL_PICT_PROP_47" => "-",
		"ALLOW_APPEND_ORDER" => "Y",
		"ALLOW_AUTO_REGISTER" => "N",
		"ALLOW_NEW_PROFILE" => "N",
		"ALLOW_USER_PROFILES" => "N",
		"BASKET_IMAGES_SCALING" => "adaptive",
		"BASKET_POSITION" => "after",
		"COMPATIBLE_MODE" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"DELIVERIES_PER_PAGE" => "8",
		"DELIVERY_FADE_EXTRA_SERVICES" => "N",
		"DELIVERY_NO_AJAX" => "Y",
		"DELIVERY_NO_SESSION" => "Y",
		"DELIVERY_TO_PAYSYSTEM" => "p2d",
		"DISABLE_BASKET_REDIRECT" => "N",
		"DISCOUNT_PERCENT_10" => "бесплатная доставка",
		"DISCOUNT_PERCENT_11" => "бесплатная доставка",
		"DISCOUNT_PERCENT_2" => "бесплатная доставка",
		"DISCOUNT_PERCENT_3" => "",
		"DISCOUNT_PERCENT_4" => "бесплатная доставка",
		"DISCOUNT_PERCENT_6" => "бесплатная доставка",
		"DISCOUNT_PERCENT_7" => "бесплатная доставка",
		"DISCOUNT_PERCENT_8" => "бесплатная доставка",
		"DISCOUNT_PERCENT_9" => "бесплатная доставка",
        "DISCOUNT_PERCENT_12" => "бесплатная доставка",
        "DISCOUNT_PERCENT_13" => "бесплатная доставка",
        "EMPTY_BASKET_HINT_PATH" => "/",
		"FREE_DELIVERY" => "Y",
		"ID_WARRANTY" => "14202",
		"MAP_DELIVERY" => array("14","15"),
		"NAL" => array("3"),
		"RETAIL_PAYMENT" => array("14"),
		"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
		"PARAMS_DELIVERY" => array("11","12","13","14","21"),
		"PARAMS_USER" => array("1","2","3","4","5","6","7","8","10"),
		"PATH_TO_AUTH" => "/auth/",
		"PATH_TO_BASKET" => "/basket/",
		"PATH_TO_PAYMENT" => "payment.php",
		"PATH_TO_PERSONAL" => "/personal/order/",
		"PAYS_ANOTHER_CODE" => "PAYS_ANOTHER",
		"PAY_FROM_ACCOUNT" => "N",
		"PAY_SYSTEMS_PER_PAGE" => "8",
		"PAY_SYSTEM_INSTALLMENTS" => "YANDEX_INSTALLMENTS",
		"PICKUPS_PER_PAGE" => "5",
		"PRODUCT_COLUMNS_HIDDEN" => array(),
		"PRODUCT_COLUMNS_VISIBLE" => array("PREVIEW_PICTURE","PROPS","PROPERTY_VID_KH_KA","PROPERTY_FOTOGRAFIYA_1","PROPERTY_CML2_ARTICLE","PROPERTY_KOD_TSVETA"),
		"PROPS_FADE_LIST_1" => array(),
		"PROPS_FADE_LIST_2" => array(),
		"PROP_DPD_CODE" => array("2_47","1_46"),
		"PROP_NOT_CALL_CODE" => array("2_49","1_48"),
		"PROP_TERMINAL" => array("2_25","1_24"),
		"SEND_NEW_USER_NOTIFY" => "N",
		"SERVICES_IMAGES_SCALING" => "adaptive",
		"SET_TITLE" => "Y",
		"SHOW_BASKET_HEADERS" => "N",
		"SHOW_COUPONS_BASKET" => "Y",
		"SHOW_COUPONS_DELIVERY" => "Y",
		"SHOW_COUPONS_PAY_SYSTEM" => "Y",
		"SHOW_DELIVERY" => array("7","14","15","16","17","22","23"),
		"SHOW_DELIVERY_INFO_NAME" => "Y",
		"SHOW_DELIVERY_LIST_NAMES" => "Y",
		"SHOW_DELIVERY_PARENT_NAMES" => "Y",
		"SHOW_MAP_IN_PROPS" => "N",
		"SHOW_NEAREST_PICKUP" => "N",
		"SHOW_NOT_CALCULATED_DELIVERIES" => "L",
		"SHOW_ORDER_BUTTON" => "final_step",
		"SHOW_PAYMENT_SERVICES_NAMES" => "Y",
		"SHOW_PAY_SYSTEM_INFO_NAME" => "Y",
		"SHOW_PAY_SYSTEM_LIST_NAMES" => "Y",
		"SHOW_STORES_IMAGES" => "Y",
		"SHOW_TOTAL_ORDER_BUTTON" => "N",
		"SHOW_VAT_PRICE" => "Y",
		"SKIP_USELESS_BLOCK" => "Y",
		"SPOT_LOCATION_BY_GEOIP" => "Y",
		"SVETOFOR_ARTICUL" => array("0100",""),
		"SVETOFOR_NO_ID" => array("19700",""),
		"SVETOFOR_OK" => "Y",
		"TEMPLATE_LOCATION" => "popup",
		"TEMPLATE_THEME" => "site",
		"TYPE_DELIVERY_COURIER" => array("16","17","23"),
		"TYPE_DELIVERY_POINT" => array("14","15","22"),
		"TYPE_DELIVERY_STOCK" => array("7"),
		"USER_CONSENT" => "N",
		"USER_CONSENT_ID" => "0",
		"USER_CONSENT_IS_CHECKED" => "Y",
		"USER_CONSENT_IS_LOADED" => "N",
		"USE_CUSTOM_ADDITIONAL_MESSAGES" => "N",
		"USE_CUSTOM_ERROR_MESSAGES" => "N",
		"USE_CUSTOM_MAIN_MESSAGES" => "N",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"USE_PHONE_NORMALIZATION" => "Y",
		"USE_PRELOAD" => "N",
		"USE_PREPAYMENT" => "N",
		"USE_YM_GOALS" => "N",
        "PAYMENT_NEW_URL"=>array(12,7),
        "PAYS_ONLY_DELIVERY_CODE"=>'PAY_ONLY_DELIVERY',
        "SHOW_PROMO"=>'Y',
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>