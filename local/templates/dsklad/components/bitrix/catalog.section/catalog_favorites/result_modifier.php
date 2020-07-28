<?php
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

Loader::includeModule('highloadblock');

$arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
$obEntity = HighloadBlockTable::compileEntity($arHLBlock);
$photoEntityDataClass = $obEntity->getDataClass();

foreach ($arResult['ITEMS'] as $itemId => &$arElement) {

    global $USER, $EXCLUDEDGROUPID;
    if (in_array($EXCLUDEDGROUPID, $USER->GetUserGroupArray())) {
        $object = new CIBlockElementRights($arParams['IBLOCK_ID'], $arElement['ID']);
        $arRights = $object->GetRights();
        $elementDisable = array_map(function ($item) {
            global $EXCLUDEDGROUPID, $EXCLUDEDRIGHTID;
            if ($item['GROUP_CODE'] == 'G'.$EXCLUDEDGROUPID && $item['TASK_ID'] == $EXCLUDEDRIGHTID)
                return true;
        }, $arRights);

        if (in_array(true, $elementDisable))
            unset($arResult['ITEMS'][$itemId]);
    }

    $arElement['Bl_COUNT_OFFERS'] = false;
    if(count($arElement['OFFERS']) > 1){
        $arElement['Bl_COUNT_OFFERS'] = true;
    }
    $arElement['INT_COUNT_OFFERS'] = false;
    if(count($arElement['OFFERS']) == 1){
        $arElement['INT_COUNT_OFFERS'] = true;
    }

    $arElement['CONTAINER_CLASS'] = '';
    if (!empty($arElement['OFFERS']) && $arElement['Bl_COUNT_OFFERS']) {
        $arElement['CONTAINER_CLASS'] = ' has-variants';
    }

    // prices
    if (!empty($arElement['OFFERS'])){
        $arElement['MIN_PRICE'] = array('DISCOUNT_VALUE' => 999999999);
        $arElement['ALL_PRICE'] = $arElement['OFFERS'][0]['PRICES'];
        foreach ($arElement['OFFERS'] as $arOffer) {
            if( $arElement['MIN_PRICE']['DISCOUNT_VALUE'] > $arOffer['MIN_PRICE']['DISCOUNT_VALUE']) {
                $arElement['MIN_PRICE'] = $arOffer['MIN_PRICE'];
                $arElement['ALL_PRICES'] = $arOffer['PRICES'];
            }
        }
    }
    if($arElement['PROPERTIES']["MIN_CHECK"]["VALUE"] == 'да')
        $arElement['MIN_PRICE']['DISCOUNT_VALUE'] = $arElement['PROPERTIES']["MIN_PRICE"]["VALUE"];
    $arElement['MIN_PRICE']['PRINT_DISCOUNT_VALUE'] = number_format($arElement['MIN_PRICE']['DISCOUNT_VALUE'], 0, '', ' ');
    foreach ($arElement['ALL_PRICES'] as $arPrice) {
        if ($arPrice['PRICE_ID'] == 2) continue;
        if ($arPrice['DISCOUNT_VALUE'] > $arElement['MIN_PRICE']['DISCOUNT_VALUE']) {
            $arElement['MIN_PRICE']['OLD_PRICE'] = number_format($arPrice['DISCOUNT_VALUE'], 0, '', ' ');
            $arElement['MIN_PRICE']['DISCOUNT_PERCENT'] = round(100 - $arElement['MIN_PRICE']['DISCOUNT_VALUE'] * 100 / $arPrice['DISCOUNT_VALUE']);
        }
    }

    // offers images
    $OffersInStock = array();
    $OffersNotInStock = array();
    foreach ($arElement['OFFERS'] as &$arOffer) {
        if (!empty($arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['~VALUE'])) {
            $rsData = $photoEntityDataClass::getList(array(
                'select' => array('UF_FILE'),
                'filter' => array('UF_XML_ID' => $arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['~VALUE']),
                'limit' => '50',
            ));
            if ($arItem = $rsData->fetch()) {
                $sourceImage = CFile::GetFileArray($arItem['UF_FILE']);
                $arImage = CFile::ResizeImageGet($sourceImage, array('width' => 264, 'height' => 264), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
                $arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['IMG']['SRC'] = $arImage['src'];
                $arImage = CFile::ResizeImageGet($sourceImage, array('width' => 68, 'height' => 68), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
                $arOffer['DISPLAY_PROPERTIES']['S']['IMG']['SRC'] = $arImage['src'];
            }
        }

        $arOffer['NAME'] = $arElement['NAME'];
        if ($arOffer['CATALOG_QUANTITY'] > 0) {
            $OffersInStock[$arOffer['ID']] = $arOffer;
        } else {
            $OffersNotInStock[$arOffer['ID']] = $arOffer;
        }
    }

    $arElement['OFFERS'] = $OffersInStock + $OffersNotInStock;
}
