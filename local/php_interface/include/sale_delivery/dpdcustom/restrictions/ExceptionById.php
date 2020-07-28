<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Internals\Entity;
use Bitrix\Sale\Shipment;

Loc::loadMessages(__FILE__);


class ExceptionByID extends Bitrix\Sale\Delivery\Restrictions\Base
{
    public static function getClassTitle()
    {
        return 'Только эти товары по ID';
    }

    public static function getClassDescription()
    {
        return 'Только эти товары по ID';
    }

    public static function check($categoriesList, array $restrictionParams, $deliveryId = 0)
    {
        if(
            empty($categoriesList)
            || !is_array($categoriesList)
            || empty($restrictionParams["CATEGORIES"])
            || !is_array($restrictionParams["CATEGORIES"]))
        {
            return true;
        }

        $product = 0;
        foreach($categoriesList as $category)
        {
            $categoryPath = self::getCategoriesPath($category);

            if(array_intersect($categoryPath, $restrictionParams["CATEGORIES"])) {
                $product ++;
            }
        }

        if($product != count($categoriesList)){
            return true;
        }else{
            return false;
        }
    }

    protected static function getCategoriesPath($categoryId)
    {
        $result = array($categoryId);

        $nav = \CIBlockSection::GetNavChain(false, $categoryId);

        while($arSectionPath = $nav->GetNext())
            if(!in_array($arSectionPath['ID'], $result))
                $result[] = $arSectionPath['ID'];

        return $result;
    }

    public static function extractParams(Entity $entity)
    {
        if (!$entity instanceof Shipment)
        {
            return array();
        }

        if(!\Bitrix\Main\Loader::includeModule('iblock'))
            return array();

        if(!\Bitrix\Main\Loader::includeModule('catalog'))
            return array();

        $productIds = array();

        /** @var \Bitrix\Sale\ShipmentItem $shipmentItem */
        foreach($entity->getShipmentItemCollection() as $shipmentItem)
        {
            /** @var \Bitrix\Sale\BasketItem $basketItem */
            $basketItem = $shipmentItem->getBasketItem();

            if($basketItem->getField('MODULE') != 'catalog')
                continue;

            $productId = intval($basketItem->getField('PRODUCT_ID'));
            $iblockId = (int)\CIBlockElement::getIBlockByID($productId);
            $info = \CCatalogSKU::getProductInfo($productId, $iblockId);

            if(!empty($info['ID']))
                $candidate = $info['ID'];
            else
                $candidate = $productId;

            if(!in_array($candidate, $productIds))
                $productIds[] = $candidate;
        }

        return  self::getGroupsIds($productIds);
    }

    protected static function getGroupsIds(array $productIds)
    {
        $groupsIds = array();

        $res = \CIBlockElement::GetElementGroups($productIds, true, array('ID'));

        while($group = $res->Fetch())
            if(!in_array($group['ID'], $groupsIds))
                $groupsIds[] = $group['ID'];

        return $groupsIds;
    }

    public static function getParamsStructure($deliveryId = 0)
    {
        return array(

            "ID" => array(
                'TYPE' => 'NUMBER',
                'DEFAULT' => "0",
                'MIN' => 0,
                'LABEL' => 'ID Товара',
                'MULTIPLE' => 'Y',
            ),
        );

        return $result;
    }
}