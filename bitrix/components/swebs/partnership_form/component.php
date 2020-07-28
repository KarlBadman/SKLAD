<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Event;

Loader::includeModule('iblock');

$arResult = array(
    'FIELDS' => array(
        'NAME' => 'name',
        'PHONE' => 'phone',
        'TEXT' => 'text'
    )
);

$obContext = Context::getCurrent();
$obRequest = $obContext->getRequest();
$strName = $obRequest->get($arResult['FIELDS']['NAME']);
$strPhone = $obRequest->get($arResult['FIELDS']['PHONE']);
$strText = $obRequest->get($arResult['FIELDS']['TEXT']);


if (!empty($strPhone)) {
    $strFieldName = $strPhone;
    if (!empty($strName)) {
        $strFieldName .= ' (' . $strName . ')';
    }

    $obElement = new CIBlockElement;
    $arFields = array(
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'NAME' => $strFieldName,
        'DETAIL_TEXT' => $strText,
        'DETAIL_TEXT_TYPE' => 'text'
    );
    $obResult = $obElement->Add($arFields);

    // goes to phone
    $arMailFields = array(
        'EVENT_NAME' => $arParams['EVENT_NAME'],
        'LID' => $obContext->getSite(),
        'C_FIELDS' => array(
            'NAME' => $strName,
            'PHONE' => $strPhone,
            'TEXT' => $strText
        )
    );
    Event::send($arMailFields);
}

$this->IncludeComponentTemplate();
