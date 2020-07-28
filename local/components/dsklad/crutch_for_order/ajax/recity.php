<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';

use Bitrix\Main\Context;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

$obContext = Context::getCurrent();
$obRequest = $obContext->getRequest();

Loader::includeModule('highloadblock');

$arHLBlock = HighloadBlockTable::getById("22")->fetch();
$obEntity = HighloadBlockTable::compileEntity($arHLBlock);
$strEntityDataClass = $obEntity->getDataClass();

if ($obRequest->isAjaxRequest() !== true) {
    echo 'error';
    exit;
}

$intLocationID = $obRequest->get('intLocationID');
$rsData = $strEntityDataClass::getList(array(
    'order' => array('UF_SORT' => 'ASC', 'UF_CITYNAME' => 'ASC'),
    'select' => array('UF_CITYNAME', 'UF_CITYCODE', 'UF_REGIONNAME', 'UF_ABBREVIATION'),
    'filter' => array(
        'UF_CITYCODE' => $intLocationID
    ),
))->fetch();

if (!is_numeric($intLocationID) || !$rsData) {
    echo 'error';
    exit;
}

$_SESSION['DPD_CITY'] = $intLocationID;
setcookie('DPD_CITY',$intLocationID,time()+2628000,'/');