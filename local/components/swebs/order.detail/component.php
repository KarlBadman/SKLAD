<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Sale\Delivery\Services\Table;
use Bitrix\Sale\Order;
use Swebs\Helper;
global $USER;


Loader::includeModule('sale');
Loader::includeModule('swebs.helper');

$arResult = array();

if (!empty($arParams['ORDER_ID'])) {
    $obOrder = Order::load($arParams['ORDER_ID']);

    if($obOrder->getUserId() != $USER->GetID()){
        LocalRedirect('/personal/', false, '301 Moved permanently');
    }

    // person type and property prefix
    $strPref = 'F';
    $servCode = 'dop_services';
    if ($obOrder->getPersonTypeId() == 2) {
        $strPref = 'U';
        $servCode = 'u_dop_services';
    }

    $dop_services = Helper\Sale\Order::GetPropertyValueByCode($obOrder, $servCode);

    if( !empty($dop_services) ){
        $obj = CIBlockElement::GetList(
            array('SORT'=>'ASC'),
            array('ID'=>$dop_services),
            false,
            false,
            array('NAME')
        );
        $dop_services_names = [];
        while( $res = $obj->Fetch() ){
            $dop_services_names[] = $res['NAME'];
        }
    }

    // status
    $arStatusFields = CSaleStatus::GetByID($obOrder->getField('STATUS_ID'));

    // delivery
    $dbDelivery = Table::getList(array(
            'filter' => array(
                'ID' => $obOrder->getDeliverySystemId()[0]
            ),
            'select' => array('NAME')
        )
    );
    $strDelivery = '';
    while ($arDeliveryFields = $dbDelivery->fetch()) {
        $strDelivery = strip_tags($arDeliveryFields['NAME']);
    }

    // pay system
    $arPayments = $obOrder->getPaymentSystemId();
    $arPayMap = array(
        2 => 'card2',
        3 => 'wallet',
        4 => 'bank'
    );
    $arFilter = array(
        'ID' => $arPayments[0]
    );
    $dbPayment = \CSalePaySystem::GetList(array(), $arFilter);
    $strPaymentType = '';
    $strPaymentID = '';
    if ($arPaymentFields = $dbPayment->GetNext()) {
        $strPaymentType = strip_tags($arPaymentFields['~NAME']);
        $strPaymentID = strip_tags($arPaymentFields['~ID']);
    }

    // payment action
    $isPayAction = false;
    foreach ($arPayments as $intPayID) {
        $dbPaySysAction = CSalePaySystemAction::GetList(
            array(),
            array(
                'PAY_SYSTEM_ID' => $intPayID,
                'PERSON_TYPE_ID' => $obOrder->getPersonTypeId()
            ),
            false,
            false,
            array('ACTION_FILE')
        );

        if ($arPaySysAction = $dbPaySysAction->Fetch()) {
            $arPath = explode('/', $arPaySysAction['ACTION_FILE']);
            if (array_pop($arPath) != 'cash') {
                $isPayAction = true;
            }
        }
    }

    // basket
    $obBasket = $obOrder->getBasket();
    $arBasket = array();
    $intTotalSumm = 0;
    $isRice = false;
    $arBasketService = array();
    $intDiscount = 0;
    $productsCount = 0;
    foreach ($obBasket as $obItem) {
        // service for delivery
        if ($obItem->getProductId() == 14307) { // подъём на этаж
            $isRice = true;
            continue;
        }

        if ($obItem->getProductId() == 14308) { // упаковка
            continue;
        }

        $arElementFields = $this->getElement($obItem->getProductId())['FIELDS'];

        // services for basket
        if ($obItem->getProductId() == 14202) {
            $arBasketService[] = $obItem->getProductId();
            continue;
        }
        if ($obItem->getProductId() == 14203) {
            $arBasketService[] = $obItem->getProductId();
            continue;
        }


        // base shipment
        $arDiscount = $this->getDiscount($obItem->getProductId());
        $intTotalSummItem = $obItem->getFinalPrice();
        $intTotalSumm += $obItem->getFinalPrice();
        $productsCount += $obItem->getQuantity();
        $arBasket[] = array(
            'NAME' => $obItem->getField('NAME'),
            'NAME_URL' => $this->getNameURL($obItem->getProductId()),
            'QUANTITY' => $obItem->getQuantity(),
            'PRICE' => number_format($obItem->getPrice(), 0, '', ' '),
            'FINAL_PRICE' => number_format($obItem->getFinalPrice(), 0, '', ' '),
            'PERCENT' => $arDiscount['PERCENT'],
            'ARTICLE' => $this->getArticle($obItem->getProductId()),
            'IMAGE' => $this->getImage($obItem->getProductId()),
            'COLOR' => $this->getColor($obItem->getProductId()),
            'SECTION_NAME' => $this->getSectionName($obItem->getProductId()),
            'SECTION_URL' => $this->getSectionURL($obItem->getProductId()),
        );
        $count = $obItem->getQuantity();
        $id = $obItem->getProductId();
        $ar_res = CPrice::GetBasePrice($id);
        $intDiscount += $ar_res["PRICE"]*$count - $intTotalSummItem;

    }

    $arAllBasketService = $this->getServices();
    $arServices = array();
    foreach ($arAllBasketService as $intID => $arService) {
        if (array_search($intID, $arBasketService) !== false) {
            $arServices[$intID] = $arService;

            if ($arService['CODE'] == \Dsklad\Config::getParam('order/warranty_code')) {
                $arServices[$intID]['~PRICE'] *= $productsCount;
                $arServices[$intID]['PRICE'] = number_format($arServices[$intID]['~PRICE'], 0, '', ' ');

                $intTotalSumm += $arServices[$intID]['~PRICE'];
            }
        }
    }

    // pay button
    $isPayNeed = false;
    if (!$obOrder->isPaid() && $isPayAction) {
        $isPayNeed = true;
    }

    $arResult['ORDER'] = array(
        'ID' => $arParams['ORDER_ID'],
        'STATUS' => array(
            'NAME' => $arStatusFields['NAME'],
            'COLOR' => Option::get('swebs.color_status', $obOrder->getField('STATUS_ID'))
        ),
        'DELIVERY' => $strDelivery,
        'DELIVERY_ID' => $obOrder->getDeliverySystemId(),
        'PAY_SYSTEM' => $strPaymentType,
        'PAY_SYSTEM_ID' => $strPaymentID,
        'PAY_SYSTEM_STATUS' => $obOrder->isPaid(),
        'PAY_SYSTEM_BUTTON' => $isPayNeed,
        'PAY_SYSTEM_ICON' => $arPayMap[$obOrder->getPaymentSystemId()[0]],
        'ADDRESS' => Helper\Sale\Order::GetPropertyValueByCode($obOrder, $strPref . '_ADDRESS'),
        'ADDRESS_COMMENT' => Helper\Sale\Order::GetPropertyValueByCode($obOrder, $strPref . '_ADDRESS_COMMENT'),
        'IS_RICE' => $isRice,
        'SERVICES' => $arServices,
        'BASKET' => $arBasket,
        'TOTAL_SUMM' => number_format($intTotalSumm + $obOrder->getDeliveryPrice(), 0, '', ' '),
        'TOTAL_PRICE' => number_format($intTotalSumm, 0, '', ' '),
        'DISCOUNT_PRICE' => number_format($obOrder->getDiscountPrice() + $intDiscount, 0, '', ' '),
        'DELIVERY_PRICE' => number_format($obOrder->getDeliveryPrice(), 0, '', ' '),
        'DOP_SERVICES' => $dop_services_names,
    );
}

$this->IncludeComponentTemplate();

