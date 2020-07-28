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
		'EMAIL' => 'email',
        'TEXT' => 'text'
    )
);

$obContext = Context::getCurrent();
$obRequest = $obContext->getRequest();
$strName = $obRequest->get($arResult['FIELDS']['NAME']);
$strPhone = $obRequest->get($arResult['FIELDS']['PHONE']);
$strEmail = $obRequest->get($arResult['FIELDS']['EMAIL']);
$strText = $obRequest->get($arResult['FIELDS']['TEXT']);


if (!empty($strPhone)) {
    $strFieldName = $strPhone;
    if (!empty($strName)) {
        $strFieldName .= ' (' . $strName . ')';
    }
if (!empty($strEmail)) {
        $strFieldName .= ' ' . $strEmail . '';
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
			'EMAIL' => $strEmail,
            'TEXT' => $strText
        )
    );
    Event::send($arMailFields);
    $arResult['SHOW_SECCESS'] = 'Y';
}

$this->IncludeComponentTemplate();
