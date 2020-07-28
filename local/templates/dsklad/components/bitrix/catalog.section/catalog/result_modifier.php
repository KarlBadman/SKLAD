<?php
#@TODO check usage

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
    if (!empty($arElement['OFFERS']) && $arElement['Bl_COUNT_OFFERS']) {
        $arElement['CONTAINER_CLASS'] = ' has-variants';
    }
    if ($arElement['PROPERTIES']['NO_VARIANTS']['VALUE_ENUM_ID'] == 1276) {
        $arElement['CONTAINER_CLASS'] = '';
    }

    // images
    if (!empty($arElement['DETAIL_PICTURE']['ID'])) {
        $arSize = array('width' => 264, 'height' => 264);
        $arImage = CFile::ResizeImageGet($arElement['DETAIL_PICTURE'], $arSize, BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
        $arElement['PREVIEW_PICTURE']['SRC'] = $arImage['src'];
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
			if ($arPrice['PRICE_ID'] != 2) continue;
			if ($arPrice['DISCOUNT_VALUE'] > $arElement['MIN_PRICE']['DISCOUNT_VALUE']) {
				$arElement['MIN_PRICE']['OLD_PRICE'] = number_format($arPrice['DISCOUNT_VALUE'], 0, '', ' ');
				$arElement['MIN_PRICE']['DISCOUNT_PERCENT'] = round(100 - $arElement['MIN_PRICE']['DISCOUNT_VALUE'] * 100 / $arPrice['DISCOUNT_VALUE']);
			}
    }

    $arOffersID = array();
    // offers images
    foreach ($arElement['OFFERS'] as $index => $arOffer) {
        $arOffersID[] = $arOffer['ID'];
        if (!empty($arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['~VALUE'])) {
            $rsData = $photoEntityDataClass::getList(array(
                'select' => array('UF_FILE'),
                'filter' => array('UF_XML_ID' => $arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['~VALUE']),
                'limit' => '50',
            ));
            if ($arItem = $rsData->fetch()) {
                $arImage = CFile::GetFileArray($arItem['UF_FILE']);
                $arImage = CFile::ResizeImageGet($arImage, array('width' => 68, 'height' => 68), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
                $arElement['OFFERS'][$index]['IMAGES'][1]['S']['SRC'] = $arImage['src'];
            }
        }
    }

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
    $arElement['OFFERS'] = $OffersInStock + $OffersNotInStock; // Подменяем стандартный массив с предложениями, на отсортированный
}
