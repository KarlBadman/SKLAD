<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;
use Bitrix\Sale\Delivery\Services\Table;
use Bitrix\Sale\Order;

Loader::includeModule('sale');

global $USER;

$arResult = array();

$arFilter = array(
    'USER_ID' => $USER->GetID()
);

$arSelect = array(
    'ID', 'PRICE', 'DELIVERY_ID', 'DATE_INSERT', 'STATUS_ID', 'TRACKING_NUMBER'
);

$dbOrder = \CSaleOrder::GetList(array('ID' => 'DESC'), $arFilter, false, false, $arSelect);
while ($arFields = $dbOrder->GetNext()) {
    // delivery
    $obOrder = Order::load($arFields['ID']);
    $deliveryID = $obOrder->getDeliverySystemId()[0];

    $dbDelivery = Table::getList(array(
        'filter' => array(
            'ID' => $deliveryID
        ),
        'select' => array('NAME')
    ));
    $strDelivery = '';
    while ($arDeliveryFields = $dbDelivery->fetch()) {
        $strDelivery = strip_tags($arDeliveryFields['NAME']);
        if (!empty($arFields['TRACKING_NUMBER'])) {
            $param = 'delivery/'. $deliveryID;
            try {
                $deliveryCompany = ' “' . \Dsklad\Config::getParam($param)['company_name'] . '“';
                $trackingLink = \Dsklad\Config::getParam($param)['tracking_link'];
            } catch (Exception $e) {
                $deliveryCompany = $trackingLink = '';
            }
            $trackingLink = str_replace('#TRACK_NUMBER#', $arFields['TRACKING_NUMBER'] , $trackingLink);
        }
    }

    // time
    $objDateTime = new DateTime($arFields['DATE_INSERT']);

    // city
    $dbProp = \CSaleOrderPropsValue::GetOrderProps($arFields['ID']);
    while ($arPropFields = $dbProp->GetNext()) {
        if ($arPropFields['ORDER_PROPS_ID'] == 1 || $arPropFields['ORDER_PROPS_ID'] == 5) {
            if (!empty($arPropFields['VALUE'])) {
                $strDelivery .= '&nbsp;в&nbsp;' . $arPropFields['VALUE'];
                break;
            }
        }
    }

    // status
    $arStatusFields = CSaleStatus::GetByID($arFields['STATUS_ID']);

    $arResult['ORDERS'][] = array(
        'ID' => $arFields['ID'],
        'SORT' => $arStatusFields['SORT'],
        'PRICE' => number_format($arFields['PRICE'], 0, '', ' '),
        'DELIVERY' => $strDelivery,
        'DELIVERY_ID' => $deliveryID,
        'DATE_INSERT' => $arFields['DATE_INSERT'],
        'DATE_SHORT' => $objDateTime->format('d.m.y'),
        'DATE_LONG' => $objDateTime->format('d.m.Y'),
        'STATUS' => array(
            'NAME' => $arStatusFields['NAME'],
            'COLOR' => Option::get('swebs.color_status', $arFields['STATUS_ID'])
        ),
        'TRACKING_NUMBER' => ($arStatusFields['NAME'] !=  \Dsklad\Config::getParam('order/finished_status')) ? $arFields['TRACKING_NUMBER']: null,
        'DELIVERY_COMPANY' => $deliveryCompany,
        'TRACKING_LINK' => $trackingLink

    );
}

function orderCompStatus($a, $b) {
    if ($a['SORT'] == $b['SORT']) {
        if ($a['ID'] == $b['ID']) {
            return 0;
        }
        return ($a['ID'] < $b['ID']) ? 1 : -1;
    }
    return ($a['SORT'] < $b['SORT']) ? -1 : 1;
}

uasort($arResult['ORDERS'], 'orderCompStatus');

$this->IncludeComponentTemplate();
