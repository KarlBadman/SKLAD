<?
#@TODO check usage
die;

################
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

$arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
$obEntity = HighloadBlockTable::compileEntity($arHLBlock);
$photoEntityDataClass = $obEntity->getDataClass();

Loader::includeModule('highloadblock');

foreach ($arResult['ITEMS'] as &$arElement) {
    $arElement['Bl_COUNT_OFFERS'] = false;
    if (count($arElement['OFFERS']) > 1) {
        $arElement['Bl_COUNT_OFFERS'] = true;
    }

    $arElement['INT_COUNT_OFFERS'] = false;
    if (count($arElement['OFFERS']) == 1) {
        $arElement['INT_COUNT_OFFERS'] = true;
    }

    $arElement['CONTAINER_CLASS'] = '';
    if (count($arElement['OFFERS']) > 1) {
        $arElement['CONTAINER_CLASS'] .= ' has-variants';
    }

    if ($arElement['PROPERTIES']['NO_VARIANTS']['VALUE_ENUM_ID'] > 0) {
        $arElement['CONTAINER_CLASS'] = '';
    }

    // prices
    if (!empty($arElement['OFFERS'])) {
        foreach ($arElement['OFFERS'] as $arOffer) {
            $arPrice = array();
            $dbPrices = CPrice::GetList(array(), array('=PRODUCT_ID' => $arOffer['ID'], 'CAN_BUY'=> 'Y'));
            while ($ar_price = $dbPrices->fetch()) {
                if (!$arPrice['MAX_PRICE'] || $arPrice['MAX_PRICE'] < $ar_price['PRICE']) {
                    $arPrice['MAX_PRICE'] = $ar_price['PRICE'];
                }
                if (!$arPrice['MIN_PRICE'] || $arPrice['MIN_PRICE'] > $ar_price['PRICE']) {
                    $arPrice['MIN_PRICE'] = $ar_price['PRICE'];
                }
            }
            // скидка от кол-ва с комбинированием цветов
            if ($arElement['PROPERTIES']['MIN_CHECK']['VALUE'] == 'да') {
                if ($arPrice['MIN_PRICE'] > $arElement['PROPERTIES']['MIN_PRICE']['VALUE']) {
                    $arPrice['MIN_PRICE'] = $arElement['PROPERTIES']['MIN_PRICE']['VALUE'];
                }
                if ($arOffer['PROPERTIES']['MIN_PRICE']['VALUE'] > 0 && $arOffer['PROPERTIES']['MIN_PRICE']['VALUE'] < $arPrice['MIN_PRICE']) {
                    $arPrice['MIN_PRICE'] = $arOffer['PROPERTIES']['MIN_PRICE']['VALUE'];
                }
            }
            if (!$arElement['MAX_PRICE'] || $arElement['MAX_PRICE'] < $arPrice['MAX_PRICE']) {
                $arElement['MAX_PRICE'] = $arPrice['MAX_PRICE'];
            }
            if (!$arElement['MIN_PRICE'] || $arElement['MIN_PRICE'] > $arPrice['MIN_PRICE']) {
                $arElement['MIN_PRICE'] = $arPrice['MIN_PRICE'];
            }
            unset($dbPrices, $arPrice, $ar_price);
        }

        if ($arElement['MAX_PRICE'] > $arElement['MIN_PRICE']) {
            $arElement['OLD_PRICE'] = number_format($arElement['MAX_PRICE'], 0, '', ' ');
            $arElement['DISCOUNT_PERCENT'] = round(100 - $arElement['MIN_PRICE'] * 100 / $arElement['MAX_PRICE']);
        }

        $arElement['MIN_PRICE_NUMBER'] = $arElement['MIN_PRICE'];
        $arElement['MIN_PRICE'] = number_format($arElement['MIN_PRICE'], 0, '', ' ');
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
                $arImage = CFile::ResizeImageGet($sourceImage, array('width' => 264, 'height' => 264), BX_RESIZE_IMAGE_PROPORTIONAL, false, array('name' => 'sharpen', 'precision' => 15));
                $arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['IMG']['SRC'] = $arImage['src'];
                $arImage = CFile::ResizeImageGet($sourceImage, Array('width' => 68, 'height' => 68), BX_RESIZE_IMAGE_PROPORTIONAL);
                $arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_3']['IMG']['SRC'] = $arImage['src'];
            }
        }
    }

    if (empty($arElement['PREVIEW_PICTURE']) && !empty($arElement['OFFERS'][0])) {
        $arImage = \CFile::GetFileArray($arElement['OFFERS'][0]['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['IMG']['ID']);
        $arImage = \CFile::ResizeImageGet($arImage, array('width' => 264, 'height' => 264), BX_RESIZE_IMAGE_PROPORTIONAL, false, array('name' => 'sharpen', 'precision' => 15));
        $arElement['PREVIEW_PICTURE']['SRC'] = $arImage['src'];
    }
}
