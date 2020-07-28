<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include.php';
?>
<!-- Conversion tracking code: purchases. Step 2: Order started -->
<? $APPLICATION->IncludeComponent(
    "swebs:order",
    ".default",
    array(
        'CHECKOUT_PAGE_URL' => '/order/',
        'ORDER_SUCCESS_PAGE'=>"/order/thankyou/",
        'IS_SUCCESS_PAGE' => SHOW_THANK_YOU,
    ),
    false
); ?>
