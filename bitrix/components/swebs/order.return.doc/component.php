<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Sale\Order;
use Swebs\Helper;

Loader::includeModule('sale');
Loader::includeModule('swebs.helper');

$arResult = array(
    'ORDER_ID' => 0,
    'ERROR' => ''
);

$obRequest = Context::getCurrent()->getRequest();

$intOrderID = $obRequest->get('order_id');

if (is_numeric($intOrderID)) {
    $arResult['ORDER_ID'] = $intOrderID;
    $strComment = $obRequest->get('comment');
    $strName = $obRequest->get('name');
    $strBank = $obRequest->get('bank');
    $strBik = $obRequest->get('bik');
    $strAccount = $obRequest->get('operating-account');
    $strCard = $obRequest->get('card');
    $arGoods = $obRequest->get('good');

    $obOrder = Order::load($intOrderID);
    $obDateInsert = $obOrder->getDateInsert();
    $obBasket = $obOrder->getBasket();
    $arBasketLines = array();
    $intBasketSum = 0;
    foreach ($arGoods as $intID => $strQuantity) {
        $obItem = $obBasket->getItemById($intID);
        $arBasketLines[] = $obItem->getField('NAME') . ' - ' . $strQuantity;
        $intBasketSum += $obItem->getPrice() * $strQuantity;
    }
    $strBasketSum = number_format($intBasketSum, 0, '', ' ') . ' (' . Helper\Others\Strings::GetStringOfNum($intBasketSum) . ')';

    $strPhone = Helper\Sale\Order::GetPropertyValueByCode($obOrder, 'F_PHONE');
    if (empty($strPhone)) {
        $strPhone = Helper\Sale\Order::GetPropertyValueByCode($obOrder, 'U_PHONE');
    }

    $strHTML = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $componentPath . '/return_order.html');
    $strHTML = str_replace('{ORDER_ID}', $intOrderID, $strHTML);
    $strHTML = str_replace('{ORDER_DATE}', $obDateInsert->format('Y-m-d H:i:s'), $strHTML);
    $strHTML = str_replace('{GOODS_ITEMS}', implode('<br>', $arBasketLines), $strHTML);
    $strHTML = str_replace('{RETURN_TXT}', $strComment, $strHTML);
    $strHTML = str_replace('{ORDER_PRICE}', $strBasketSum, $strHTML);
    $strHTML = str_replace('{NAME_USER}', $strName, $strHTML);
    $strHTML = str_replace('{NAME_BANK}', $strBank, $strHTML);
    $strHTML = str_replace('{BIK_BANK}', $strBik, $strHTML);
    $strHTML = str_replace('{NUMBER_ORDER_BANK}', $strAccount, $strHTML);
    $strHTML = str_replace('{NUMBER_CARD_BANK}', $strCard, $strHTML);
    $strHTML = str_replace('{PHONE_USER}', $strPhone, $strHTML);

    $strPdfFileName = $_SERVER['DOCUMENT_ROOT'] . '/upload/blank_' . $intOrderID . '.pdf';
    $arResult['FILE_URL'] = '/upload/blank_' . $intOrderID . '.pdf';
    $strHtmlFileName = $_SERVER['DOCUMENT_ROOT'] . '/blank_' . $intOrderID . '.html';

    file_put_contents($strHtmlFileName, $strHTML);

    if (is_file($strPdfFileName)) {
        unlink($strPdfFileName);
    }
    $strCommand = 'sh ' . $_SERVER['DOCUMENT_ROOT'] . '/pdf.sh ' . $strHtmlFileName . ' ' . $strPdfFileName . ' > /dev/null';
    system($strCommand);

    $arResult['FILE_SIZE'] = number_format((filesize($strPdfFileName) / 1024), 2, ',', ' ');

    $arResult['CLIENT_EMAIL'] = Helper\Sale\Order::GetPropertyValueByCode($obOrder, 'F_EMAIL');
    if (empty($arResult['CLIENT_EMAIL'])) {
        $arResult['CLIENT_EMAIL'] = Helper\Sale\Order::GetPropertyValueByCode($obOrder, 'U_EMAIL');
    }

    // mail
    $arEventFields = array(
        'CLIENT_EMAIL' => $arResult['CLIENT_EMAIL'],
        'SALE_EMAIL' => $arParams['EMAIL']
    );
    CEvent::Send('RETURN_DOC', SITE_ID, $arEventFields, 'Y', '', array($strPdfFileName));

    unlink($strHtmlFileName);
} else {
    $arResult['ERROR'] = 'Не указан ID заказа';
}

$this->IncludeComponentTemplate();
