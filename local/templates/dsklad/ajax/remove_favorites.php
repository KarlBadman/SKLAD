<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
use Bitrix\Main\Context;

$obRequest = Context::getCurrent()->getRequest();

if (!$obRequest->isAjaxRequest()) {
    exit;
}

$intItemID = $obRequest->get('idRemoveFavorites');

if($intItemID == 'removeAll'){
    unset($_SESSION['FAVORITES']);
    exit;
}

if (!is_numeric($intItemID)) {
    exit;
}

$mixIndex = array_search($intItemID, $_SESSION['FAVORITES']);

if ($mixIndex !== false) {
    unset($_SESSION['FAVORITES'][$mixIndex]);
}

echo $intItemID;


