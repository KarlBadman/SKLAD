<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
use Bitrix\Main\Context;

$obRequest = Context::getCurrent()->getRequest();

if (!$obRequest->isAjaxRequest()) {
    echo 'error';
    die;
}
?>
<?
$APPLICATION->IncludeComponent(
    'swebs:review.add',
    '.default',
    array(
        'COMPONENT_TEMPLATE' => '.default',
        'IBLOCK_TYPE' => 'communication',
        'IBLOCK_ID' => '39',
        'ELEMENT_ID' => $obRequest->get('id')
    ),
    false
);
?>
