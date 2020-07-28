<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @var string $parentComponentPath */
/** @var string $parentComponentName */
/** @var string $parentComponentTemplate */

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

Loader::includeModule('highloadblock');
Loader::includeModule('statistic');

$arResult = array(
    'HTML_ID' => uniqid()
);

$cityObj = new CCity();
$arThisCity = $cityObj ->GetFullInfo();

$arHLBlock = HighloadBlockTable::getById($arParams['DPD_HL_ID'])->fetch();
$obEntity = HighloadBlockTable::compileEntity($arHLBlock);
$strEntityDataClass = $obEntity->getDataClass();

if (!empty($_SESSION['DPD_CITY'])) {
    $arFilter = array(
        'UF_CITYCODE' => $_SESSION['DPD_CITY']
    );
} else {
    $arFilter = array(
        '%=UF_CITYNAME' => $arThisCity['CITY_NAME']['VALUE']
    );
}

$rsData = $strEntityDataClass::getList(array(
    'order' => array('UF_SORT' => 'ASC'),
    'select' => array('UF_CITYNAME', 'UF_CITYCODE', 'UF_CITYID'),
    'filter' => $arFilter
));
if ($arItem = $rsData->fetch()) {
    $arResult['LOCATION'] = array(
        'VALUE' => $arItem['UF_CITYNAME'],
        'DATA' => $arItem['UF_CITYCODE']
    );
    $_SESSION['DPD_CITY'] = $arItem['UF_CITYCODE'];
    $_SESSION['DPD_CITY_NAME'] = $arItem['UF_CITYNAME'];
}
$this->IncludeComponentTemplate();
