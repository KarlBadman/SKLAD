<?php
if (!$ACCEPT_BUTTON_LABEL) {
    $ACCEPT_BUTTON_LABEL = 'Оформить заказ';
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
?>
<?
$APPLICATION->IncludeComponent(
    'swebs:popup.offer',
    '.default',
    array(
        'COMPONENT_TEMPLATE' => '.default',
        'ACCEPT_BUTTON_LABEL' => $ACCEPT_BUTTON_LABEL
    ),
    false
);
?>
<!-- Conversion tracking code: purchases. Step 1: Product added to cart -->