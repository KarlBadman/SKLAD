<?php

use Dsklad\Config;

function PrepaymentOnPreorder($order)
{
   if($order->isNew()) {
       $paymentCollection = $order->getPaymentCollection();
       \Bitrix\Main\Loader::includeModule('highloadblock');
       $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(Config::getParam('hl/settings'))->fetch();
       $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
       $strEntityDataClass = $obEntity->getDataClass();
       $arSettings = $strEntityDataClass::getList(['select' => ['ID','UF_PREPAYMENT','UF_PAY_PRECENT']])->fetch();

       if (count($paymentCollection) == 1 && $arSettings['UF_PREPAYMENT']) {
           $prepayment = 0;
           foreach ($order->getBasket()->getBasketItems() as $basketItem) {
               if(\Bitrix\Catalog\ProductTable::getById($basketItem->getField('PRODUCT_ID'))->fetch()['QUANTITY'] < $basketItem->getField('QUANTITY')){
                   $prepayment = $prepayment + ($basketItem->getFinalPrice() / 100 * $arSettings['UF_PAY_PRECENT']);
               }
           }
           if($prepayment > 0) {
               foreach ($paymentCollection as $payment) {
                   if ($payment->getSum() == $order->getPrice()) {
                       $payment->setField('SUM', $order->getPrice() -$prepayment);
                       $newPayment = $paymentCollection->createItem();
                       $newPayment->setField('PAY_SYSTEM_ID', 3);
                       $newPayment->setField('PAY_SYSTEM_NAME', 'При получении');
                       $newPayment->setField('SUM', $prepayment);
                   }
               }
           }
       }
   }
}