<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Event;

$arResult = array(
    'NAME' => ''
);

Loader::includeModule('iblock');

$dbElement = CIBlockElement::GetByID($arParams['ELEMENT_ID']);
if ($arFields = $dbElement->GetNext()) {
    $arResult['NAME'] = $arFields['NAME'];
}

$this->IncludeComponentTemplate();
