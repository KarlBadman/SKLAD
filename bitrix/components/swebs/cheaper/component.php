<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Event;

Loader::includeModule('iblock');

$arResult = array();

$obContext = Context::getCurrent();
$obRequest = $obContext->getRequest();
$strName = $obRequest->get('name');
$strPhone = $obRequest->get('phone');
$strEmail = $obRequest->get('email');
$strLink = $obRequest->get('link');
$strPrice = $obRequest->get('price');
$intID = $obRequest->get('id');
$strComponent = $obRequest->get('component');


if ($strComponent == 'cheaper' && $arParams['ELEMENT_ID'] == $intID) {
    $strFieldName = $strEmail;
    if (!empty($strName)) {
        $strFieldName .= ' (' . $strName . ')';
    }

    $obElement = new CIBlockElement;
    $arFields = array(
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'NAME' => $strFieldName,
        'PROPERTY_VALUES' => array(
            'SHIPMENT' => $intID,
            'LINK' => $strLink,
            'PRICE' => $strPrice,
            'PHONE' =>$strPhone
        )
    );
    $obResult = $obElement->Add($arFields);

    $strShipment = '';
    $dbShipment = CIBlockElement::GetByID($intID);
    if ($arShipment = $dbShipment->GetNext()) {
        $strShipment = $arShipment['NAME'] . '(' . $arShipment['DETAIL_PAGE_URL'] . ')';
    }

    // goes to email
    $arMailFields = array(
        'EVENT_NAME' => $arParams['EVENT_NAME'],
        'LID' => $obContext->getSite(),
        'C_FIELDS' => array(
            'NAME' => $strName,
            'PHONE' => $strPhone,
            'EMAIL' => $strEmail,
            'SHIPMENT' => $strShipment,
            'LINK' => $strLink,
            'PRICE' => $strPrice
        )
    );
    Event::send($arMailFields);
}

$this->IncludeComponentTemplate();
