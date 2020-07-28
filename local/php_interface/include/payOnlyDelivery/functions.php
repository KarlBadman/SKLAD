<?php

use Dsklad\Config;

function payOnlyDelivery($order)
{
   if($order->isNew()) {

       $paymentCollection = $order->getPaymentCollection();

       if (count($paymentCollection) == 1 && $paymentCollection[0]->getPaySystem()->getField('CODE') == 'PAY_ONLY_DELIVERY') {
           \Bitrix\Main\Loader::includeModule('highloadblock');
           $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(Config::getParam('hl/settings'))->fetch();
           $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
           $strEntityDataClass = $obEntity->getDataClass();
           $arSettings = $strEntityDataClass::getList(['select' => ['ID','UF_PREPAYMENT','UF_PAY_PRECENT']])->fetch();

           $prepayment = false;
           foreach ($order->getBasket()->getBasketItems() as $basketItem) {
               if(\Bitrix\Catalog\ProductTable::getById($basketItem->getField('PRODUCT_ID'))->fetch()['QUANTITY'] < $basketItem->getField('QUANTITY')){
                   $prepayment = true;
               }
           }
           if(!$prepayment  || !$arSettings['UF_PREPAYMENT']) {
               foreach ($paymentCollection as $payment) {
                   if ($payment->getSum() == $order->getPrice()) {
                       $payment->setField('SUM',  $order->getDeliveryPrice());
                       $newPayment = $paymentCollection->createItem();
                       $newPayment->setField('PAY_SYSTEM_ID', 3);
                       $newPayment->setField('PAY_SYSTEM_NAME', 'При получении');
                       $newPayment->setField('SUM', $order->getPrice() - $order->getDeliveryPrice());
                   }
               }
           }
       }
   }
}