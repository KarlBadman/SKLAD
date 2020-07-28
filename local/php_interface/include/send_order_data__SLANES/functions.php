<?php
function send_order_data__SLANES($event, &$eventName, &$arFields) {

    $order = \Bitrix\Sale\Order::load($event);
    $orderUserId = $order->getUserId();
    $rsUser = CUser::GetByID($orderUserId);
    if($arUser = $rsUser->Fetch()){
        if(1 == intVal($arUser['UF_NOSEND_EMAIL__SMS']) || strpos($arUser['EMAIL'], '@crm.com') !== false){
            $eventName = '';
            $arFields = array();
        }
    }

    if ($order->getDateInsert()->getTimestamp() < strtotime('01.10.2018')) {
        $eventName = '';
        $arFields = array();
    }

}