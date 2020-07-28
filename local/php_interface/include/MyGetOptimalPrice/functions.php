<?php
function MyGetOptimalPrice($productID, $quantity = 1, $arUserGroups = array(), $renewal = "N", $priceList = array(), $siteID = 's1', $arDiscountCoupons = false)
{
    CModule::IncludeModule('iblock');
    CModule::IncludeModule('catalog');

    $productID = (int)$productID;
    if($productID <= 0) return false;
    $quantity = (int)$quantity;
    if($quantity <= 0) return false;

    $renewal = ($renewal == 'Y' ? 'Y' : 'N');

    $intIBlockID = (int)CIBlockElement::GetIBlockByID($productID);
    $resultCurrency = Bitrix\Currency\CurrencyManager::getBaseCurrency();
    if(empty($arUserGroups)){ global $USER; $arUserGroups = $USER->GetUserGroupArray(); }

    $arProps = array();
    $arProps["MIN_PRICE"] = 0;
    $db_props = CIBlockElement::GetProperty($intIBlockID, $productID, array("sort"=>"asc"), array("CODE"=>"MIN_PRICE"));
    while($ar_prop = $db_props->Fetch()){
        $arProps["MIN_PRICE"] = round($ar_prop["VALUE"],2);
    }

    // Получим товар по $productID ( SKU ID )
    $arProduct = CCatalogSku::GetProductInfo($productID);

    // Получим свойства MIN_CHECK MIN_QTY и MIN_PRICE товара
    $db_props = CIBlockElement::GetProperty($arProduct["IBLOCK_ID"], $arProduct["ID"], array("sort"=>"asc"), array("CODE"=>array("MIN_CHECK","MIN_QTY","MIN_PRICE")));
    while($ar_prop = $db_props->Fetch()){
        if($ar_prop["CODE"] == "MIN_CHECK") $arProps["MIN_CHECK"] = $ar_prop["VALUE_ENUM"];
        elseif($ar_prop["CODE"] == "MIN_QTY") $arProps["MIN_QTY"] = intval($ar_prop["VALUE"]);
        elseif($ar_prop["CODE"] == "MIN_PRICE" && $arProps["MIN_PRICE"] == 0) $arProps["MIN_PRICE"] = round($ar_prop["VALUE"],2);
    }

    // Если у товара ЕСТЬ СКИДКИ при покупки комплекта разных цветов!
    if($arProps["MIN_CHECK"] == 'да')
    {
        // Получим количество товара в корзине разных цветов
        $intProductInBasket = GetCountProductInBasketOffer($arProduct['ID'], $productID);
        if($quantity + $intProductInBasket < $arProps["MIN_QTY"]) $arProps["MIN_PRICE"] = 0;
    }else{
        $arProps["MIN_PRICE"] = 0;
    }

    if (!isset($priceList) || !is_array($priceList)) $priceList = array();

    if (empty($priceList))
    {
        $cacheKey = 'U'.implode('_', $arUserGroups);
        if (!isset($priceTypeCache[$cacheKey]))
        {
            $priceTypeCache[$cacheKey] = array();
            $priceIterator = CCatalogGroup::GetGroupsList(array('@GROUP_ID' => $arUserGroups, '=BUY' => 'Y'));
            while ($priceType = $priceIterator->Fetch())
            {
                $priceTypeId = (int)$priceType['CATALOG_GROUP_ID'];
                $priceTypeCache[$cacheKey][$priceTypeId] = $priceTypeId;
                unset($priceTypeId);
            }
            unset($priceType, $priceIterator);
        }
        if (empty($priceTypeCache[$cacheKey]))
            return false;

        $iterator = Bitrix\Catalog\PriceTable::getList(array(
            'select' => array('ID', 'CATALOG_GROUP_ID', 'PRICE', 'CURRENCY'),
            'filter' => array(
                '=PRODUCT_ID' => $productID,
                '@CATALOG_GROUP_ID' => $priceTypeCache[$cacheKey],
                array(
                    'LOGIC' => 'OR',
                    '<=QUANTITY_FROM' => $quantity,
                    '=QUANTITY_FROM' => null
                ),
                array(
                    'LOGIC' => 'OR',
                    '>=QUANTITY_TO' => $quantity,
                    '=QUANTITY_TO' => null
                )
            )
        ));
        while ($row = $iterator->fetch())
        {
            $row['ELEMENT_IBLOCK_ID'] = $intIBlockID;
            $priceList[] = $row;
        }
        unset($row, $iterator);
        unset($cacheKey);
    }
    else
    {
        foreach (array_keys($priceList) as $priceIndex)
            $priceList[$priceIndex]['ELEMENT_IBLOCK_ID'] = $intIBlockID;
        unset($priceIndex);
    }

    if (empty($priceList))
        return false;

    $maxPrice = 0;
    $minimalPrice = array();

    $isNeedDiscounts = CAllCatalogProduct::getUseDiscount();
//		$isNeedleToMinimizeCatalogGroup = CAllCatalogProduct::isNeedleToMinimizeCatalogGroup($priceList);

    foreach ($priceList as $priceData)
    {
        $currentPrice = $priceData['PRICE'];
        if ($priceData['CURRENCY'] != $resultCurrency)
            $currentPrice = CCurrencyRates::ConvertCurrency($currentPrice, $priceData['CURRENCY'], $resultCurrency);
        $currentPrice = roundEx($currentPrice, CATALOG_VALUE_PRECISION);

        if($maxPrice < $currentPrice) $maxPrice = $currentPrice;

        $result = array(
            'BASE_PRICE' => $currentPrice,
            'COMPARE_PRICE' => $currentPrice,
            'PRICE' => $currentPrice,
            'CURRENCY' => $resultCurrency,
            'DISCOUNT_LIST' => array(),
            'USE_ROUND' => true,
            'RAW_PRICE' => $priceData
        );

        if ($isNeedDiscounts)
        {
            $arDiscounts = CCatalogDiscount::GetDiscount(
                $productID,
                $intIBlockID,
                $priceData['CATALOG_GROUP_ID'],
                $arUserGroups,
                $renewal,
                $siteID,
                $arDiscountCoupons
            );

            $discountResult = CCatalogDiscount::applyDiscountList($currentPrice, $resultCurrency, $arDiscounts);
            unset($arDiscounts);
            if ($discountResult === false)
                return false;
            $result['PRICE'] = $discountResult['PRICE'];
            $result['COMPARE_PRICE'] = $discountResult['PRICE'];
            $result['DISCOUNT_LIST'] = $discountResult['DISCOUNT_LIST'];
            unset($discountResult);
        }

        $result['UNROUND_PRICE'] = $result['PRICE'];
        if ($result['USE_ROUND'])
        {
            $result['PRICE'] = Bitrix\Catalog\Product\Price::roundPrice(
                $priceData['CATALOG_GROUP_ID'],
                $result['PRICE'],
                $resultCurrency
            );
            $result['COMPARE_PRICE'] = $result['PRICE'];
        }

        if (empty($result['DISCOUNT_LIST']))
        {
            $result['BASE_PRICE'] = $result['PRICE'];
        }
        elseif (roundEx($result['BASE_PRICE'], 2) - roundEx($result['PRICE'], 2) < 0.01)
        {
            $result['BASE_PRICE'] = $result['PRICE'];
            $result['DISCOUNT_PRICE'] = array();
        }

        if (empty($minimalPrice) || $minimalPrice['COMPARE_PRICE'] > $result['COMPARE_PRICE'])
        {
            $minimalPrice = $result;
        }
        unset($currentPrice, $result);
    }
    unset($priceData);
    unset($vat);

    if($arProps["MIN_CHECK"] == 'да' && $arProps["MIN_PRICE"] > 0 && $arProps["MIN_PRICE"] < $minimalPrice['PRICE'])
        $minimalPrice['PRICE'] = $arProps['MIN_PRICE'];


    $discountValue = ($minimalPrice['BASE_PRICE'] > $minimalPrice['PRICE'] ? $minimalPrice['BASE_PRICE'] - $minimalPrice['PRICE'] : 0);

    $arResult = array(
        'PRICE' => $minimalPrice['RAW_PRICE'],
        'MAX_PRICE' => $maxPrice,
        'RESULT_PRICE' => array(
            'PRICE_TYPE_ID' => $minimalPrice['RAW_PRICE']['CATALOG_GROUP_ID'],
            'BASE_PRICE' => $minimalPrice['BASE_PRICE'],
            'DISCOUNT_PRICE' => $minimalPrice['PRICE'],
            'UNROUND_DISCOUNT_PRICE' => $minimalPrice['UNROUND_PRICE'],
            'CURRENCY' => $resultCurrency,
            'DISCOUNT' => $discountValue,
            'PERCENT' => (
            $minimalPrice['BASE_PRICE'] > 0 && $discountValue > 0
                ? roundEx((100*$discountValue)/$minimalPrice['BASE_PRICE'], CATALOG_VALUE_PRECISION)
                : 0
            ),
        ),
        'DISCOUNT_PRICE' => $minimalPrice['PRICE'],
        'DISCOUNT' => array(),
        'DISCOUNT_LIST' => array(),
        'PRODUCT_ID' => $productID
    );
    if (!empty($minimalPrice['DISCOUNT_LIST']))
    {
        reset($minimalPrice['DISCOUNT_LIST']);
        $arResult['DISCOUNT'] = current($minimalPrice['DISCOUNT_LIST']);
        $arResult['DISCOUNT_LIST'] = $minimalPrice['DISCOUNT_LIST'];
    }
    unset($minimalPrice);

    return $arResult;

}