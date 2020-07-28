<?php
#@TODO check usage
die;

################
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

Loader::includeModule('highloadblock');

$arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
$obEntity = HighloadBlockTable::compileEntity($arHLBlock);
$photoEntityDataClass = $obEntity->getDataClass();

foreach ($arResult['ITEMS'] as &$arElement) {
    $arElement['Bl_COUNT_OFFERS'] = false;
    if(count($arElement['OFFERS']) > 1){
        $arElement['Bl_COUNT_OFFERS'] = true;
    }
    $arElement['INT_COUNT_OFFERS'] = false;
    if(count($arElement['OFFERS']) == 1){
        $arElement['INT_COUNT_OFFERS'] = true;
    }
    $arElement['CONTAINER_CLASS'] = '';

    if (count($arElement['OFFERS'])>1) {
        $arElement['CONTAINER_CLASS'] = ' has-variants';
    }

    if ($arElement['PROPERTIES']['NO_VARIANTS']['VALUE_ENUM_ID'] > 0) {
        $arElement['CONTAINER_CLASS'] = '';
    }

    // prices
    if (!empty($arElement['OFFERS'])) {
        $arElement['MIN_PRICE'] = array('DISCOUNT_VALUE' => 999999999);
        $arElement['ALL_PRICE'] = array();
        foreach ($arElement['OFFERS'] as $arOffer) {
            if ($arElement['MIN_PRICE']['DISCOUNT_VALUE'] > $arOffer['MIN_PRICE']['DISCOUNT_VALUE']) {
                $arElement['MIN_PRICE'] = $arOffer['MIN_PRICE'];
                $arElement['ALL_PRICES'] = $arOffer['PRICES'];
            }
        }
    }
    $arElement['MIN_PRICE']['PRINT_DISCOUNT_VALUE'] = number_format($arElement['MIN_PRICE']['DISCOUNT_VALUE'], 0, '', ' ');
    foreach ($arElement['ALL_PRICES'] as $arPrice) {
        if ($arPrice['PRICE_ID'] == 2) {
            continue;
        }
        if ($arPrice['DISCOUNT_VALUE'] > $arElement['MIN_PRICE']['DISCOUNT_VALUE']) {
            $arElement['MIN_PRICE']['OLD_PRICE'] = number_format($arPrice['DISCOUNT_VALUE'], 0, '', ' ');
            $arElement['MIN_PRICE']['DISCOUNT_PERCENT'] = round(100 - $arElement['MIN_PRICE']['DISCOUNT_VALUE'] * 100 / $arPrice['DISCOUNT_VALUE']);
        }
    }

    // offers images
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
                $arImage = CFile::ResizeImageGet($sourceImage, Array("width" => 68, "height" => 68), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
                $arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_3']['IMG']['SRC'] = $arImage['src'];
            }
        }
    }
}

//__($arResult['ITEMS'][2]['MIN_PRICE']);
//__($arResult['ITEMS'][2]['OFFERS'][0]);