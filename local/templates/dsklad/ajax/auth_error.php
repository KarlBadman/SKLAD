<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $APPLICATION, $USER;

$APPLICATION->IncludeComponent(
    "bitrix:main.register",
    "register_top",
    array(
        "AUTH" => "Y",
        "REQUIRED_FIELDS" => array(
            0 => "EMAIL",
        ),
        "SET_TITLE" => "N",
        "SHOW_FIELDS" => array(
            0 => "EMAIL",
            1 => "NAME",
            2 => "PERSONAL_PHONE",
        ),
        "SUCCESS_PAGE" => "",
        "USER_PROPERTY" => array(
        ),
        "USER_PROPERTY_NAME" => "",
        "USE_BACKURL" => "N",
        "COMPONENT_TEMPLATE" => "register_top"
    ),
    false
);