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
$images_array = array();
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
        if (in_array(true, $elementDisable)) {
            unset($arResult['ITEMS'][$itemId]);
        }
    }
    
    // HIDE2VIEW
    if (checkHitOnHideProductPosition($arElement['PROPERTIES']['HIDE2VIEW']['VALUE_XML_ID'])) {
        unset($arResult['ITEMS'][$itemId]); continue;
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

    if(count($arElement['OFFERS']) == 1){
        if(count($arElement['OFFERS'][0]['ITEM_PRICES']) > 1) $arElement['FROM'] = 'Y';
    }else{
        foreach ($arElement['OFFERS'] as $offers){
            if(count($offers['ITEM_PRICES']) > 1) {
                $arElement['FROM'] = 'Y';
                break;
            }
            if($arElement['OFFERS'][0]['MIN_PRICE']['VALUE'] != $offers['MIN_PRICE']['VALUE']){
                $arElement['FROM'] = 'Y';
                break;
            }
        }
    }

    if (!empty($arElement['OFFERS']) && $arElement['Bl_COUNT_OFFERS']) {
        $arElement['CONTAINER_CLASS'] = ' has-variants';
    }
    if ($arElement['PROPERTIES']['NO_VARIANTS']['VALUE_ENUM_ID'] == 1276) {
        $arElement['CONTAINER_CLASS'] = '';
    }

    // для сортировки
    $OffersInStock = array();
    $OffersNotInStock = array();
    // Сначала будут предложения в наличии, потом те, что отстутствуют
    foreach ($arElement['OFFERS'] as $arOffer) {
        $arOffer['NAME'] = $arElement['NAME'];
        if ($arOffer['CATALOG_QUANTITY'] > 0) {
            $OffersInStock[$arOffer['ID']] = $arOffer;
        } else {
            $OffersNotInStock[$arOffer['ID']] = $arOffer;
        }
    }
    $arElement['OFFERS'] = array_merge($OffersInStock, $OffersNotInStock); // Подменяем стандартный массив с предложениями, на отсортированный

    // prices
    foreach ($arElement['OFFERS'] as $intKey => $arOffer) {
        
        // HIDE2VIEW
        if (checkHitOnHideProductPosition($arOffer['PROPERTIES']['HIDE2VIEW']['VALUE_XML_ID'])) {
            unset($arElement['OFFERS'][$intKey]); continue;
        }
        
        $arPrice = array();
        $dbPrices = CPrice::GetList(array(),array('=PRODUCT_ID' => $arOffer["ID"], 'CAN_BUY'=> 'Y'));
        while ($ar_price = $dbPrices->fetch())
        {
            if(!$arPrice["MAX_PRICE"] || $arPrice["MAX_PRICE"] < $ar_price["PRICE"])
                $arPrice["MAX_PRICE"] = $ar_price["PRICE"];
            if(!$arPrice["MIN_PRICE"] || $arPrice["MIN_PRICE"] > $ar_price["PRICE"])
                $arPrice["MIN_PRICE"] = $ar_price["PRICE"];
        }
        // скидка от кол-ва с комбинированием цветов
        if($arElement['PROPERTIES']["MIN_CHECK"]["VALUE"] == 'да')
        {
            if($arPrice["MIN_PRICE"] > $arElement['PROPERTIES']["MIN_PRICE"]["VALUE"])
                $arPrice["MIN_PRICE"] = $arElement['PROPERTIES']["MIN_PRICE"]["VALUE"];
            if($arOffer['PROPERTIES']["MIN_PRICE"]["VALUE"] > 0 && $arOffer['PROPERTIES']["MIN_PRICE"]["VALUE"] < $arPrice["MIN_PRICE"])
                $arPrice["MIN_PRICE"] = $arOffer['PROPERTIES']["MIN_PRICE"]["VALUE"];
        }
        if(!$arElement["MAX_PRICE"] || $arElement["MAX_PRICE"] < $arPrice["MAX_PRICE"])
            $arElement["MAX_PRICE"] = $arPrice["MAX_PRICE"];
        if(!$arElement["MIN_PRICE"] || $arElement["MIN_PRICE"] > $arPrice["MIN_PRICE"])
            $arElement["MIN_PRICE"] = $arPrice["MIN_PRICE"];
        unset($dbPrices, $arPrice, $ar_price);

        //images
        $db_props = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $arOffer['IBLOCK_ID'], 'ID' => $arOffer['ID']), false, false, Array("PROPERTY_FOTOGRAFIYA_1", "PROPERTY_FOTOGRAFIYA_2"));
        $photosources = $db_props->Fetch();
        $arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['~VALUE'] = $photosources['PROPERTY_FOTOGRAFIYA_1_VALUE'];
        $arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_2']['~VALUE'] = $photosources ['PROPERTY_FOTOGRAFIYA_2_VALUE'];

        if (!empty($arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['~VALUE'])) {
            $images_array[$arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['~VALUE']][$itemId][] = array('position' => 1, 'offer_id' => $intKey);
        }
        if (!empty($arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_2']['~VALUE'])) {
            $images_array[$arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_2']['~VALUE']][$itemId][] = array('position' => 2, 'offer_id' => $intKey);
        }
    }
    if ($arElement["MAX_PRICE"] > $arElement['MIN_PRICE']) {
        $arElement['OLD_PRICE'] = number_format($arElement["MAX_PRICE"], 0, '', ' ');
        $arElement['DISCOUNT_PERCENT'] = round(100 - $arElement['MIN_PRICE'] * 100 / $arElement["MAX_PRICE"]);
    }
    $arElement['MIN_PRICE'] = number_format($arElement['MIN_PRICE'], 0, '', ' ');
}


$rsData = $photoEntityDataClass::getList(array(
    'select' => array('UF_FILE', 'UF_XML_ID'),
    'filter' => array('=UF_XML_ID' => array_keys($images_array))
));

while($arItem = $rsData->Fetch()){
    if($arItem['UF_FILE']){
        $sourceImage = CFile::GetFileArray($arItem['UF_FILE']);
        foreach($images_array[$arItem['UF_XML_ID']] as $k => $items){
            foreach($items as $item) {
                // 68x68
                $arImage = CFile::ResizeImageGet($sourceImage, Array('width' => 68, 'height' => 68), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
                $arResult['ITEMS'][$k]['OFFERS'][$item['offer_id']]['IMAGES'][$item['position']]['S']['SRC'] = $arImage['src'];
                // 264x264
                $arImage = CFile::ResizeImageGet($sourceImage, Array('width' => 264, 'height' => 264), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
                $arResult['ITEMS'][$k]['OFFERS'][$item['offer_id']]['IMAGES'][$item['position']]['R']['SRC'] = $arImage['src'];
            }
        }
    }
}
