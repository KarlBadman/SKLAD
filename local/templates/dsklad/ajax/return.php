<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
global $APPLICATION;
?>
<? $APPLICATION->IncludeComponent(
    "swebs:order.return",
    "comp",
    array(
        "ORDER_ID" => $_REQUEST['ORDER_ID'],
    ),
    false
); ?>