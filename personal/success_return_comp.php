<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
global $APPLICATION;
?>

<? $APPLICATION->IncludeComponent(
    "swebs:order.return.doc",
    "comp_popup",
    array(
        'EMAIL' => 'info@dsklad.ru'
    ),
    false
); ?>
