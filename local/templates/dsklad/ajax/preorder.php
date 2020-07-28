<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
 $APPLICATION->IncludeComponent(
    "swebs:callback",
    "preorder",
    array(
        "IBLOCK_TYPE" => "communication",
        "IBLOCK_ID" => "40",
        "EVENT_NAME" => "PREORDER",
        "COMPONENT_TEMPLATE" => "preorder"
    ),
    false
);
 ?>

