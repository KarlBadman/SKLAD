<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Internals\Entity;
use Bitrix\Sale\Shipment;

Loc::loadMessages(__FILE__);

class ExceptionByTerminals extends Bitrix\Sale\Delivery\Restrictions\Base
{
    public static function getClassTitle()
    {
        return 'Только если есть терминалы';
    }

    public static function getClassDescription()
    {
        return 'Только если есть терминалы';
    }

    public static function check($arData, array $restrictionParams, $deliveryId = 0)
    {
        if(!empty($arData['TERMINAL'])) {
            return true;
        }else{
            return false;
        }
    }


    public static function extractParams(Entity $entity)
    {
        \Bitrix\Main\Loader::includeModule('dsklad.site');

        $order = $entity->getCollection()->getOrder();
        $basket = $order->getBasket();
        $arBasketTerminals = array();

        foreach ($basket as $item){
            $sku = CCatalogSku::GetProductInfo($item->getProductId());
            if(CIBlockElement::GetIBlockByID($item->getProductId()) != \Dsklad\Config::getParam('iblock/basket_services')) {
                $arBasketTerminals[] = array(
                    'ID' => $item->getId(),
                    'PRODUCT_ID' => $item->getProductId(),
                    'QUANTITY' => $item->getQuantity(),
                    'PARENT' => $sku['ID'],
                    'PACK' => GetPackData($item->getField('PRODUCT_XML_ID')), // упаковки товара
                );
            }
        }

        \Bitrix\Main\Loader::includeModule('dsklad.site');
        $arData = \Dsklad\Order::getDPDTerminals($arBasketTerminals);

        return $arData;
    }


    public static function getParamsStructure($deliveryId = 0)
    {
        return array(
            "ACTIVE" => array(
                'TYPE' => 'Y/N',
                'LABEL' => 'Только если есть терминалы',
            ),
        );

        return $result;
    }
}