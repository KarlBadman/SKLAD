<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

Loader::includeModule('catalog');

CBitrixComponent::includeComponentClass('swebs:order');

class COrderDetail extends COrderBasket
{
    public function getDiscount($intID)
    {
        $arFilter = array(
            'PRODUCT_ID' => $intID,
            'CATALOG_GROUP_ID' => array(1, 2)
        );
        $arSelect = array(
            'CATALOG_GROUP_ID', 'PRICE'
        );
        $dbPrice = CPrice::GetList(array(), $arFilter, false, false, $arSelect);
        $intPercent = 0;
        $arPrices = array();
        while ($arFields = $dbPrice->GetNext()) {
            $arPrices[$arFields['CATALOG_GROUP_ID']] = $arFields['PRICE'];
        }
        if (!empty($arPrices[2])) {
            $intPercent = round(100 - $arPrices[2] * 100 / $arPrices[1]);
        }

        $arResult = array(
            'PERCENT' => $intPercent,
            'SUMM' => 0
        );

        if ($arPrices[2] > $arPrices[1]) {
            $arResult['SUMM'] = $arPrices[2] - $arPrices[1];
        }

        return $arResult;
    }
}