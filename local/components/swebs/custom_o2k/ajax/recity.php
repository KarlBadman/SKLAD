<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';

use Bitrix\Main\Context;

$obContext = Context::getCurrent();
$obRequest = $obContext->getRequest();

if ($obRequest->isAjaxRequest() !== true) {
    echo 'error';
    exit;
}

$intLocationID = $obRequest->get('intLocationID');

if (!is_numeric($intLocationID)) {
    echo 'error';
    exit;
}

$_SESSION['DPD_CITY'] = $intLocationID;