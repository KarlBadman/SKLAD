<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Sale\Order;
use Swebs\Helper;

Loader::includeModule('sale');
Loader::includeModule('swebs.helper');

$arResult = array();

if (!empty($arParams['ORDER_ID'])) {
    $obOrder = Order::load($arParams['ORDER_ID']);

    // basket
    $obBasket = $obOrder->getBasket();
    $arBasket = array();
    $intTotalSumm = 0;
    foreach ($obBasket as $obItem) {
        // services for basket
        if ($obItem->getProductId() == 14307) {
            continue;
        }

        if ($obItem->getProductId() == 14308) { // упаковка
            continue;
        }

        $arElementFields = $this->getElement($obItem->getProductId())['FIELDS'];

        // service for delivery
        if ($obItem->getProductId() == 14202) {
            continue;
        }
        if ($obItem->getProductId() == 14203) {
            continue;
        }


        // base shipment
        $arDiscount = $this->getDiscount($obItem->getProductId());
        $intTotalSumm += $obItem->getFinalPrice();
        $arBasket[] = array(
            'ID' => $obItem->getId(),
            'NAME' => $obItem->getField('NAME'),
            'NAME_URL' => $this->getNameURL($obItem->getProductId()),
            'QUANTITY' => $obItem->getQuantity(),
            'PRICE' => number_format($obItem->getPrice(), 0, '', ' ') . '.–',
            'FINAL_PRICE' => number_format($obItem->getFinalPrice(), 0, '', ' ') . '.–',
            'PERCENT' => $arDiscount['PERCENT'],
            'ARTICLE' => $this->getArticle($obItem->getProductId()),
            'IMAGE' => $this->getImage($obItem->getProductId()),
            'COLOR' => $this->getColor($obItem->getProductId()),
            'SECTION_NAME' => $this->getSectionName($obItem->getProductId()),
            'SECTION_URL' => $this->getSectionURL($obItem->getProductId()),
        );
    }

    $arResult = array(
        'ID' => $arParams['ORDER_ID'],
        'TOTAL_SUMM' => number_format($intTotalSumm, 0, '', ' ') . '.–',
        'BASKET' => $arBasket
    );
}

__($arResult);

/*echo '<pre>';
print_r($arResult);
echo '</pre>';*/

$this->IncludeComponentTemplate();
