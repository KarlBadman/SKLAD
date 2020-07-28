<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $epilogFile */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */

use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;
use Swebs\Helper;

Loader::includeModule('sale');
Loader::includeModule('iblock');
Loader::includeModule('swebs.helper');

$obRequest = Context::getCurrent()->getRequest();
$intProductId = $obRequest->get('product_id');
$intQuantity = $obRequest->get('quantity');

if (empty($intQuantity)) {
    $intQuantity = 1;
}

$obBasket = Basket::loadItemsForFUser(Fuser::getId(), Context::getCurrent()->getSite());
if ($obItem = $obBasket->getExistsItem('catalog', $intProductId)) {
    $obItem->setField('QUANTITY', $obItem->getQuantity() + $intQuantity);
} else {
    $strProductXmlId = Helper\Iblock\Element::getFieldsByID($intProductId, 'XML_ID');
    $intSectionID = Helper\Iblock\Element::getFieldsByID($intProductId, 'IBLOCK_SECTION_ID');
    $strCatalogXmlId = Helper\Iblock\Section::getFieldsByID($intSectionID, 'XML_ID');
    $obItem = $obBasket->createItem('catalog', $intProductId);
    $obItem->setFields(array(
        'QUANTITY' => $intQuantity,
        'CURRENCY' => CurrencyManager::getBaseCurrency(),
        'LID' => Context::getCurrent()->getSite(),
        'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
        'PRODUCT_XML_ID' => $strProductXmlId,
        'CATALOG_XML_ID' => $strCatalogXmlId
    ));
}
$res = $obBasket->save();