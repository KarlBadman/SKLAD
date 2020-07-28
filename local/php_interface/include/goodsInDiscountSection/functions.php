<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 03.04.19
 * Time: 12:06
 */

function addDiscountsToSection(&$arFields){

    $mxResult = CCatalogSKU::GetInfoByOfferIBlock($arFields['IBLOCK_ID']);

    if (is_array($mxResult)){
        $dbProductPrice = CPrice::GetListEx(
            array(),
            array("PRODUCT_ID" => $arFields['ID']),
            false,
            false,
            array("ID", "CATALOG_GROUP_ID", "PRICE")
        );

        $discount = false;
        $one = array();
        $i = 0;
        while ($row = $dbProductPrice->fetch()) {
            if($i == 0) {
                $one = $row;
            }elseif($row['PRICE'] < $one['PRICE']){
                $discount = true;
            }
            $i++;
        }

        $productId = reset($arFields['PROPERTY_VALUES'][$mxResult['SKU_PROPERTY_ID']]);
        $resultElement  = \Bitrix\Iblock\SectionElementTable::getList(array(
            'select' => array('IBLOCK_SECTION_ID'),
            'filter' => array('IBLOCK_ELEMENT_ID' =>$productId['VALUE']),
        ))->fetchAll();
        $resultElement = array_map(function($item){return $item['IBLOCK_SECTION_ID'];}, $resultElement);

        if(is_array($resultElement) && $discount){
            $arSection = array_unique(array_merge($resultElement,array((string)\Dsklad\Config::getParam('section/discounts'))));
        }else if ($discount) {
            $arSection = array_unique(array($resultElement,(string)\Dsklad\Config::getParam('section/discounts')));
        } else {
            $arSection = array_unique($resultElement);
        }

        if(!$discount && array_search(218, $arSection) !== false){
            $discountIndex = array_search(218, $arSection);
            unset($arSection[$discountIndex]);
        }

        CIBlockElement::SetElementSection($productId['VALUE'], $arSection);

    }
}
