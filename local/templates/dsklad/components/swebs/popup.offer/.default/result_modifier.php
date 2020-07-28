<?
/*
// Сейчас этот блок выключен. Для использования нужно переписать под новую логику

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

function loadSku($intPropID)
{
    $arPropFilter = array(); // Фильтр
    $arPropFilter['ID'] = array(); // массив с ID свойств которые нужно выбрать
    $arSelect = array('CATALOG_GROUP_1','CATALOG_GROUP_2'); // что нужно выбрать
    $arFields = \CCatalogSKU::getOffersList($intPropID, 0, array('ACTIVE' => 'Y'), $arSelect, $arPropFilter);

    return $arFields;
}

if (!empty($arResult['WITH_THIS'])) {
    $wtIDs = array();
    foreach ( $arResult['WITH_THIS'] as $config ) {
        $wtIDs[] = $config['ID'];
    }

    $arFilter = array(
        'IBLOCK_ID' => $arResult['IBLOCK_ID'],
        'ID' => $wtIDs,
    );

    $arSelect = array('ID', 'NAME', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL');
    $dbElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    $arResult['WITH_THIS'] = array();
    while ($arFields = $dbElement->GetNext()) {
        $arOffers = loadSku($arFields['ID'])[$arFields['ID']];
        foreach ($arOffers as $arOtherOffer) {
            $arPriceOptimal = CCatalogProduct::GetOptimalPrice($arOtherOffer["ID"]);
            $minPrice = $arPriceOptimal["DISCOUNT_PRICE"];
            $maxPrice = $arPriceOptimal['MAX_PRICE'];
            if( $minPrice < $maxPrice ) {
                $arFields['DISCOUNT'] = 'Y';
                $arFields['DISCOUNT_PERCENT'] = round(100 - $minPrice * 100 / $maxPrice);
                $arFields['MIN_PRICE_NO_DISC'] = number_format($maxPrice, 0, '', ' ');
            }
        }
        $arFields['MIN_PRICE'] = number_format($minPrice, 0, '', ' ');

        // images
        if (!empty($arFields['DETAIL_PICTURE'])) {
            $arSize = array(
                'width' => 220,
                'height' => 220
            );
            $arImage = CFile::ResizeImageGet(
                $arFields['DETAIL_PICTURE'],
                $arSize,
                BX_RESIZE_IMAGE_PROPORTIONAL,
                false,
                array("name" => "sharpen", "precision" => 15)
            );
            $arFields['PICTURE'] = $arImage['src'];
        }

      $arResult['WITH_THIS'][] = $arFields;
    }
} else {
    $arWithThis = array();
    if (!empty($iProductID = (int)$arResult["ITEM"]["PROPERTY_CML2_LINK_VALUE"])) {
        $arProductOffers = CCatalogSKU::getOffersList($iProductID);
        $arOfferIDs = array_keys($arProductOffers[$iProductID]);

        $arOrderIDs = array();
        $dbOrderIDs = CSaleBasket::GetList(array(), array(">ORDER_ID" => "0", "PRODUCT_ID" => $arOfferIDs), false, false, array("ORDER_ID"));
        while ($arOrderID = $dbOrderIDs->GetNext()) {
            $arOrderIDs[] = $arOrderID["ORDER_ID"];
        }

        $arFilter = array("ORDER_ID" => $arOrderIDs, "!PRODUCT_ID" => $arOfferIDs);
        $arGroupBy = array("PRODUCT_ID", "SUM" => "QUANTITY");
        $arSelectFields = array("PRODUCT_ID");
        $dbBasketItems = CSaleBasket::GetList(array("QUANTITY" => "DESC"), $arFilter, $arGroupBy, false, $arSelectFields);
        // сортировка по сумме не работает :(

        while (($j++ < 8) && ($arBasketItem = $dbBasketItems->GetNext())) {
            $itemWithThis = CCatalogSku::GetProductInfo($arBasketItem["PRODUCT_ID"]);
            if (!empty($itemWithThis)) {
                $arWithThis[] = $itemWithThis["ID"];
            }
        }

        if (!empty($arWithThis)) {
            $arFilter = array(
                'ID' => $arWithThis
            );
            $arSelect = array(
                'ID', 'NAME', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL'
            );
            $dbElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
            while ($arWithFields = $dbElement->GetNext()) {
                $arOffers = loadSku($arWithFields['ID'])[$arWithFields['ID']];
                foreach ($arOffers as $arOtherOffer) {
                    $arPriceOptimal = CCatalogProduct::GetOptimalPrice($arOtherOffer["ID"]);
                    $minPrice = $arPriceOptimal['DISCOUNT_PRICE'];
                    $maxPrice = $arPriceOptimal['MAX_PRICE'];
                    if ($minPrice < $maxPrice) {
                        $arWithFields['DISCOUNT'] = 'Y';
                        $arWithFields['DISCOUNT_PERCENT'] = round(100 - $minPrice * 100 / $maxPrice);
                        $arWithFields['MIN_PRICE_NO_DISC'] = number_format($maxPrice, 0, '', ' ');
                        break;
                    }
                }
                $arWithFields['MIN_PRICE'] = number_format($minPrice, 0, '', ' ');

                // images
                if (!empty($arWithFields['DETAIL_PICTURE'])) {
                    $arSize = array(
                        'width' => 220,
                        'height' => 220
                    );
                    $arImage = CFile::ResizeImageGet($arWithFields['DETAIL_PICTURE'], $arSize, BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
                    $arWithFields['PICTURE'] = $arImage['src'];
                }

                $arResult['WITH_THIS'][] = $arWithFields;
            }
        }
    }
}

// выkлючаем "Подойдет к покупке"
$arResult['WITH_THIS'] = array();
*/