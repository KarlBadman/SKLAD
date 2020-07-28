<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';

use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;
use Swebs\Helper;

Loader::includeModule('sale');
Loader::includeModule('swebs.helper');

$obContext = Context::getCurrent();
$obRequest = $obContext->getRequest();

if (!$obRequest->isAjaxRequest()) {
    echo 'error';
    exit;
}

$intProductId = intval($obRequest->get('id'));
$intQuantity = intval($obRequest->get('q'));

if (empty($intProductId)) {
    echo 'error';
    exit;
}

if (empty($intQuantity)) {
    $intQuantity = 1;
}

$obBasket = Basket::loadItemsForFUser(Fuser::getId(), $obContext->getSite());

$bFindItem = false;
foreach ($obBasket as $obBasketItem) {
//    ppp($obBasketItem);
    if($obBasketItem->getField('PRODUCT_ID') == $intProductId ){
        $obBasketItem->setField('QUANTITY', $intQuantity);
        $bFindItem = true;
        $obBasket->save();
    }
}

if(!$bFindItem){
    $obItem = $obBasket->createItem('catalog', $intProductId);
    $obItem->setFields(array(
        'QUANTITY' => $intQuantity,
        'CURRENCY' => CurrencyManager::getBaseCurrency(),
        'LID' => $obContext->getSite(),
        'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
        'PRODUCT_XML_ID' => Helper\Iblock\Element::getFieldsByID($intProductId, 'XML_ID')
    ));
    $obBasket->save();
}
/*
if ($obItem = $obBasket->getExistsItem('catalog', $intProductId)) {
    $obItem->setField('QUANTITY', $intQuantity);
    echo 'yea';
} else {
    $obItem = $obBasket->createItem('catalog', $intProductId);
    $obItem->setFields(array(
        'QUANTITY' => $intQuantity,
        'CURRENCY' => CurrencyManager::getBaseCurrency(),
        'LID' => $obContext->getSite(),
        'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
        'PRODUCT_XML_ID' => Helper\Iblock\Element::getFieldsByID($intProductId, 'XML_ID')
    ));
    echo 'fack';
}
$obBasket->save();
*/
echo 'ok';