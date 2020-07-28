<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';

use Bitrix\Main\Context;

$obContext = Context::getCurrent();
$obRequest = $obContext->getRequest();

if ($obRequest->isAjaxRequest() !== true) {
    exit;
}

$strName = $obRequest->get('name');
$strValue = $obRequest->get('value');

if (empty($strName) || empty($strValue)) {
    exit;
}

$_SESSION['ORDER']['FIELDS'][$strName] = $strValue;