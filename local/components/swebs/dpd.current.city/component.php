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

$cityObj = new CCity();
$arThisCity = $cityObj ->GetFullInfo();

$arResult = array(
    'CITY' => ''
);

// $arData = ALX_GeoIP::GetAddr($arResult["CITY"]);

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
    'select' => array('UF_CITYNAME', 'UF_CITYCODE', 'UF_CITYID', 'UF_ABBREVIATION','UF_COUNTRYCODE'),
    'filter' => $arFilter
));
if ($arItem = $rsData->fetch()) {
    $arResult['CITY'] = $arItem['UF_CITYNAME'];
    $_SESSION['COUNTRYCODE'] = $arResult['COUNTRYCODE'] = $arItem['UF_COUNTRYCODE'];
}
else{
	//выбираем Питер если не определили город
	$arData = $strEntityDataClass::getList(array(
    	'select' => array('UF_CITYNAME', 'UF_CITYCODE', 'UF_CITYID', 'UF_ABBREVIATION'),
    	'filter' => array('UF_CITYID'=>'49694167')
	))->fetch();
	$arResult['CITY'] = $arData['UF_CITYNAME'];
	$_SESSION['DPD_CITY'] = $arData['UF_CITYCODE'];
    $_SESSION['COUNTRYCODE'] = $arResult['COUNTRYCODE'] = 'RU';
}

setcookie('DPD_CITY', $_SESSION['DPD_CITY'],time()+60*60*24*60, '/');
$_COOKIE['DPD_CITY'] = $_SESSION['DPD_CITY'];
$this->IncludeComponentTemplate();
