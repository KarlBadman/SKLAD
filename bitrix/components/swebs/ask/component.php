<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Event;

Loader::includeModule('iblock');

$arResult = array(
    'FIELDS' => array(
        'NAME' => 'name',
        'EMAIL' => 'email',
        'TEXT' => 'text'
    )
);

$obContext = Context::getCurrent();
$obRequest = $obContext->getRequest();
$strName = $obRequest->get($arResult['FIELDS']['NAME']);
$strEmail = $obRequest->get($arResult['FIELDS']['EMAIL']);
$strText = $obRequest->get($arResult['FIELDS']['TEXT']);
$strComponent = $obRequest->get('component');
$gRecaptchaResponse=$obRequest->get('g-recaptcha-response');

$secret = '6LfhxiEUAAAAAIWJ7Jf3ffZcg-Hp4cd1O_8r_PNi';
include($_SERVER["DOCUMENT_ROOT"].'/local/php_interface/php_lib/autoload.php');
$recaptcha = new \ReCaptcha\ReCaptcha($secret);
$ReCaptchaRes = $recaptcha->verify($gRecaptchaResponse, $_SERVER['REMOTE_ADDR']);

if ($strComponent == 'ask' && !empty($strEmail) && strlen(trim($strText))>0 and $ReCaptchaRes->isSuccess()) {
    $strFieldName = $strEmail;
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

    // goes to email
    $arMailFields = array(
        'EVENT_NAME' => $arParams['EVENT_NAME'],
        'LID' => $obContext->getSite(),
        'C_FIELDS' => array(
            'NAME' => $strName,
            'EMAIL' => $strEmail,
            'TEXT' => $strText
        )
    );
    Event::send($arMailFields);
}

$this->IncludeComponentTemplate();
