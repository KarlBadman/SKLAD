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
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;
use gor\elementHelper;
Loader::includeModule('sale');
Loader::includeModule('iblock');
Loader::includeModule('highloadblock');

$arResult = array(
    'ITEMS' => array(),
    'QUANTITY' => 0
);

// services list for except
$arFilter = array(
    'IBLOCK_ID' => array(37, 38),
    'ACTIVE' => 'Y'
);
$dbElement = CIBlockElement::GetList(array(), $arFilter, false, false, array('ID'));
$arServices = array();
while ($arFields = $dbElement->GetNext()) {
    $arServices[] = $arFields['ID'];
}

$obBasket = Basket::loadItemsForFUser(Fuser::getId(), Context::getCurrent()->getSite());
foreach ($obBasket as $obBasketItem) {
    $intID = $obBasketItem->getProductId();

    if (array_search($intID, $arServices) !== false) {
        continue;
    }

    $dbElement = CIBlockElement::GetByID($intID);
    $obElement = $dbElement->GetNextElement();
    if (!$obElement) {
        $obBasketItem->delete();
        $obBasketItem->save();
        continue;
    }
    $arFields = $obElement->GetFields();
    $arProperties = $obElement->GetProperties();

    // DETAIL_PAGE_URL
    if (!empty($arProperties['CML2_LINK']['~VALUE'])) {
        $arFilter = array(
            'ID' => $arProperties['CML2_LINK']['~VALUE']
        );
        $arSelect = array('ID', 'IBLOCK_ID', 'DETAIL_PAGE_URL');
        $dbElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        if ($arElements = $dbElement->GetNext()) {
            //__($arElements);
            $arFields['DETAIL_PAGE_URL'] = $arElements['DETAIL_PAGE_URL'];
        }
    }

    if (!empty($arProperties['CML2_LINK']['~VALUE'])) {
        $res = elementHelper::getElement($arProperties['CML2_LINK']['~VALUE']);
        $arFields['NAME'] = $res['FIELDS']['NAME'];
    }

    // image
    /* old
     * $arSize = array(
        'width' => 66,
        'height' => 66
    );*/
    $arSize = array(
        'width' => 132,
        'height' => 132
    );
    $arImage = array();
    if (!empty($arFields['DETAIL_PICTURE'])) {
        $arImage = CFile::ResizeImageGet($arFields['DETAIL_PICTURE'], $arSize, BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
        $arImage = array_change_key_case($arImage, CASE_UPPER);
    } else {
        if (!empty($arProperties['FOTOGRAFIYA_1']['~VALUE'])) {
            $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
            $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
            $photoEntityDataClass = $obEntity->getDataClass();

            $rsData = $photoEntityDataClass::getList(array(
                'select' => array('UF_FILE'),
                'filter' => array('UF_XML_ID' => $arProperties['FOTOGRAFIYA_1']['~VALUE']),
                'limit' => '50',
            ));
            if ($arItem = $rsData->fetch()) {
                $arImage = CFile::ResizeImageGet($arItem['UF_FILE'], $arSize, BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
                $arImage = array_change_key_case($arImage, CASE_UPPER);
            }
        }
    }

    // colors
    /*
    $arHLBlock = HighloadBlockTable::getById(21)->fetch();
    $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
    $strEntityDataClass = $obEntity->getDataClass();

    $arColors = array();
    $arColorParam = array();
    $rsData = $strEntityDataClass::getList();
    while ($arItem = $rsData->Fetch()) {
        $arColors[$arItem['UF_1C_CODE']] = $arItem;
    }

    $intColorCode = explode('#',$arProperties['KOD_TSVETA']['~VALUE'])[1];
    if (!empty($intColorCode)) {
        $arColorParam = $arColors[$intColorCode];
    }
*/
    $arColorParam = elementHelper::getColor($intID);

    $arResult['QUANTITY'] += $obBasketItem->getQuantity();

    $arResult['ITEMS'][$intID] = array(
				'ID' => $obBasketItem->getField('ID'),
        'NAME' => $arFields['NAME'],
        'URL' => $arFields['DETAIL_PAGE_URL'],
        'PRICE' => number_format($obBasketItem->getFinalPrice(), 0, '', ' '),
        'IMAGE' => $arImage,
        'COLOR' => $arColorParam
    );
}

$this->IncludeComponentTemplate();
