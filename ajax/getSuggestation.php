<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;

Loader::includeModule('highloadblock');

$obRequest = Context::getCurrent()->getRequest();

if (!$obRequest->isAjaxRequest()) {
    echo 'error';
    die;
}

$intIblockID = $obRequest->get('HL_ID');
$strQuery = $obRequest->get('query');

if (empty($intIblockID) || empty($strQuery)) {
    die;
}

if (strlen($strQuery) < 3) {
    die;
}

$arHLBlock = HighloadBlockTable::getById($intIblockID)->fetch();
$obEntity = HighloadBlockTable::compileEntity($arHLBlock);
$strEntityDataClass = $obEntity->getDataClass();

$arResult = array(
    'query' => 'Unit',
    'suggestions' => array()
);
$rsData = $strEntityDataClass::getList(array(
    'order' => array('UF_SORT' => 'ASC', 'UF_CITYNAME' => 'ASC'),
    'select' => array('UF_CITYNAME', 'UF_CITYCODE', 'UF_REGIONNAME', 'UF_ABBREVIATION'),
    'filter' => array(
        '%=UF_CITYNAME' => $strQuery . '%'
    ),
));
while ($arItem = $rsData->fetch()) {
    $arResult['suggestions'][] = array(
        'value' => $arItem['UF_ABBREVIATION'] . '. ' . $arItem['UF_CITYNAME'].($arItem['UF_REGIONNAME'] ? sprintf(' (%s)', $arItem['UF_REGIONNAME']) : ''),
        'data' => $arItem['UF_CITYCODE']
    );
}

header('Content-Type: text/json; charset=utf-8;');
echo json_encode($arResult);