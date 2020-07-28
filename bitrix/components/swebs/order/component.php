<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @var string $parentComponentPath */
/** @var string $parentComponentName */
/** @var string $parentComponentTemplate */

$arResult = array();
global $USER;

if ($this->getStatus() == 'preparation') {
    // promo
    $this->SetCoupon();
    $arResult['COUPON']=$this->SetCoupon();

    // basket
    $arResult['SERVICES'] = $this->getServices();
    $arResult['DELIVERY_SERVICES'] = $this->getDeliveryServices();
    $arExcept = array_keys($arResult['SERVICES']);

    foreach ($arResult['DELIVERY_SERVICES'] as $arItem) {
        $arExcept[] = $arItem['ID'];
    }

    $arBasketItems = $this->getBasketItems($arExcept);
    $arResult['BASKET_ITEMS'] = $arBasketItems['SHIPMENT'];

    foreach ($arResult['SERVICES'] as $intID => $arItem) {
        if (array_search($intID, $arBasketItems['SERVICES']) === false) {
            $arResult['SERVICES'][$intID]['CHECK'] = false;
        } else {
            $arResult['SERVICES'][$intID]['CHECK'] = true;
        }
    }

    foreach ($arResult['DELIVERY_SERVICES'] as $intSort => $arItem) {
        if (array_search($arItem['ID'], $arBasketItems['SERVICES']) === false) {
            $arResult['DELIVERY_SERVICES'][$intSort]['CHECK'] = false;
        } else {
            $arResult['DELIVERY_SERVICES'][$intSort]['CHECK'] = true;
        }
    }

    // coast
    $intCoast = $this->getDPDCoast($arResult['BASKET_ITEMS']);
    $intCoast += $this->intDeliveryServicesSum;
    $arResult['~COAST'] = $intCoast;
//echo '<pre style="display:none;">';print_r($arResult['BASKET_ITEMS']);echo '</pre>';
    $arResult['COAST'] = number_format($arResult['~COAST'], 0, '', ' ') . '.–';

    // dpd
    $arData = $this->getDPDTerminals();
    $arResult['mapParams'] = $arData['mapParams'];
    $arResult['TERMINAL'] = array();
    foreach ($arData['TERMINAL'] as $arItem) {
        if (!empty($arItem['schedule']['operation'])) {
            $arItem['schedule'] = array($arItem['schedule']);
        }
        $arItem['address']['terminalAddress'] = 'г. ' . $arItem['address']['cityName'] . ', ';
        $arItem['address']['terminalAddress'] .= $arItem['address']['streetAbbr'] . ', ';
        $arItem['address']['terminalAddress'] .= $arItem['address']['street'] . ', ';
        $arItem['address']['terminalAddress'] .= $arItem['address']['houseNo'];
        $arResult['TERMINAL'][] = $arItem;
    }
    $arTerm = $this->getDPDTerminalsNew();
    foreach ($arTerm as $arItem) {
        $arResult['TERMINAL'][] = array(
            'code' => $arItem['terminal']['terminalCode'],
            'address' => array(
                'countryCode' => $arItem['city']['countryCode'],
                'regionCode' => $arItem['city']['regionCode'],
                'regionName' => $arItem['city']['regionName'],
                'cityCode' => $arItem['city']['cityCode'],
                'cityName' => $arItem['city']['cityName'],
                'index' => '',
                'street' => '',
                'streetAbbr' => '',
                'houseNo' => '',
                'descript' => '',
                'terminalAddress' => $arItem['terminal']['terminalAddress']
            ),
            'schedule' => array(
                array(
                    'operation' => 'SelfDelivery',
                    'timetable' => $arItem['terminal']['workingTime']
                )
            )
        );
    }

    //__($arResult['TERMINAL']);

    // delivery
    $arDelivery = $this->getDelivery();
    $arResult['DELIVERY'] = array();
    $arResult['FIRST_DELIVERY_ID'] = 0;

    $i = 0;
    foreach ($arDelivery as $arItem) {
        if (empty($arResult['TERMINAL']) && $arItem['ID'] == 7) {
            continue; // если нет терминалов, самовывоз ненужен
        }
        $arResult['DELIVERY'][$arItem['SORT']] = array(
            'ID' => $arItem['ID'],
            'NAME' => $arItem['NAME'],
            'CLASS' => trim($arItem['DESCRIPTION']),
            'ACTIVE' => ($i == 0) ? true : false
        );
        if ($i == 0) {
            $arResult['FIRST_DELIVERY_ID'] = $arItem['ID'];
        }
        $i++;
    }

    // pay systems
    $arPayment = $this->getPaySystems();
    $arResult['PAYMENT'] = array();

    $arPayMap = array(
        2 => 'card2',
        3 => 'wallet',
        4 => 'bank'
    );

    foreach ($arPayment as $i => $arItem) {
        $arResult['PAYMENT'][$arItem['SORT']] = array(
            'ID' => $arItem['ID'],
            'NAME' => $arItem['~NAME'],
            'CLASS' => $arPayMap[$arItem['ID']],
            'ACTIVE' => ($i == 0) ? true : false
        );
    }

    // prices
    $this->intTotalSum -= $this->intDeliveryServicesSum;
    $arResult['~TOTAL_SUMM'] = $this->intTotalSum;
    $arResult['TOTAL_SUMM'] = number_format($arResult['~TOTAL_SUMM'], 0, '', ' ') . '.–';
    $arResult['~TOTAL_RESULT'] = $arResult['~TOTAL_SUMM'] + $arResult['~COAST'];
    $arResult['TOTAL_RESULT'] = number_format($arResult['~TOTAL_RESULT'], 0, '', ' ') . '.–';
   // $arResult['TOTAL_DISCOUNT'];
} elseif ($this->getStatus() == 'order') {

    $arResult['DEBUG_RES'] = $this->doOrder();
    $arResult['STATUS'] = 'success';
}

$this->getUserInfo();

$this->IncludeComponentTemplate();