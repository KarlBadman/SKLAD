<?php

function Makcrx_OnOrderSaveNew_WriteItemQuantities( $order)
{
    /** @var Order $order */

    if ($order->isNew()) {

        if (Bitrix\Main\Loader::IncludeModule("catalog")) {

            $quantities = array();
            $preorder = true;

            $basket = $order->getBasket();
            foreach ($basket as $item) {
                $productID = $item->getProductId();
                $arProduct = CCatalogProduct::GetByID($productID);
                $quantities[$productID] = $arProduct["QUANTITY"];
                if ($arProduct["QUANTITY"] > 0)
                    $preorder = false;
            }

            $propertyCollection = $order->getPropertyCollection();
            $ar = $propertyCollection->getArray();
            $propertyID = array();
            foreach ($ar['properties'] as $property) {
                $propertyID[$property["CODE"]] = $property["ID"];
            }

            if (count($propertyID) > 0) {
                if (count($quantities) > 0) {
                    if (array_key_exists("QUANTITIES", $propertyID)) {
                        $propValue = $propertyCollection->getItemByOrderPropertyId($propertyID["QUANTITIES"]);
                        /* пока отменил изменения, ибо нужно, чтобы измененный заказ выгружался в retailcrm, а функция retailCrmBeforeOrderSend отрабатывает при сохранении. Возможно, можно просто повесить эту функцию на событие OnSaleOrderBeforeSaved вместо OnSaleOrderSaved как сейчас */
                        $propValue->setValue(json_encode($quantities));
                        $save = true; // будут заново вызваны события связанные с изменением заказа
                    }
                }

                if ($preorder) {
                    if (array_key_exists("PREORDER", $propertyID)) {
                        $propValue = $propertyCollection->getItemByOrderPropertyId($propertyID["PREORDER"]);
                        $propValue->setValue('Y');
                        $save = true; // будут заново вызваны события связанные с изменением заказа
                    }
                }
            }
        }

        if(array_search(end($order->getDeliverySystemId()),DELIVERY_PICKUP_ID) !== false) {
            $propertyCollection = $order->getPropertyCollection();
            foreach ($propertyCollection as $prop) {
                $arProp = $prop->getProperty();
                if($arProp['CODE'] == 'F_ADDRESS' || $arProp['CODE'] == 'U_ADDRESS') {
                    $propAddress = $arProp['ID'];
                }
                if ($arProp['CODE'] == 'ADDRESS_TERMINAL') {
                    $newAddres = $prop->getValue();
                    $save = true;
                }
            }
            if(!empty($propAddress) && !empty($newAddres) ){
                $propertyCollection->getItemByOrderPropertyId($propAddress)->setValue($newAddres);
                $save = true;
            }
        }else{
            $propertyCollection = $order->getPropertyCollection();
            foreach ($propertyCollection as $prop) {
                $arProp = $prop->getProperty();
                if($arProp['CODE'] == 'F_ADDRESS' || $arProp['CODE'] == 'U_ADDRESS') {
                    $propAddress = $arProp['ID'];
                    $address = $prop->getValue();
                }
                if($arProp['CODE'] == 'F_ROOM' || $arProp['CODE'] == 'U_ROOM') {
                    $room = $prop->getValue();
                }

                if(!empty($propAddress) && !empty($address) && !empty($room)){
                    $propertyCollection->getItemByOrderPropertyId($propAddress)->setValue($address.', кв'.$room);
                    $save = true;
                }

                if($arProp['CODE'] == 'DPD_TERMINAL_CODE'){
                    $prop->setValue('');
                    $save = true;
                    break;
                }
            }

        }

        if ($save) {
            $order->save();
        }

        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/order_ip.log', print_r($_SERVER, true), FILE_APPEND);
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/order_ip.log', print_r($order, true), FILE_APPEND);
    }
}