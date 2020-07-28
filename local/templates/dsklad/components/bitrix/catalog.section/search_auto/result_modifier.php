<?
#@TODO check usage
die;

################
use Bitrix\Main\Type\Collection;
use Bitrix\Currency\CurrencyTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

//добавляем название раздела
foreach($arResult['ITEMS'] as $key => $arItem){
	$res = CIBlockSection::GetByID($arItem["IBLOCK_SECTION_ID"]);
	if($ar_res = $res->GetNext()){
		$arResult['ITEMS'][$key]['SECTION_NAME_PARENT'] = $ar_res['NAME'];
	}
}

//добавляем уменьшенную картинку
foreach($arResult['ITEMS'] as $key => $arItem){
	$imgFile = CFile::ResizeImageGet($arItem['DETAIL_PICTURE'], array('width' => 132, 'height' => 132), BX_RESIZE_IMAGE_PROPORTIONAL, true, array("name" => "sharpen", "precision" => 15));
	$arResult['ITEMS'][$key]['IMG_SMALL'] = $imgFile;
}

//цены
foreach ($arResult['ITEMS'] as $key => &$arElement) {
    
    // HIDE2VIEW
    if (checkHitOnHideProductPosition($arElement['PROPERTIES']['HIDE2VIEW']['VALUE_XML_ID'])) {
        unset($arResult['ITEMS'][$key]); continue;
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
}