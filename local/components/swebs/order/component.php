<?
use Bitrix\Main\Context;
use Bitrix\Sale\Order;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$arResult = array();
global $USER;
$warrantyCoef = 0;

$arParams['ORDER_SUCCESS_PAGE'] = trim($arParams['ORDER_SUCCESS_PAGE']);
$arParams['CHECKOUT_PAGE_URL'] = trim($arParams['CHECKOUT_PAGE_URL']);

// HACK TODO
$this->order = Order::create(SITE_ID);

if ($this->getStatus() == 'preparation') {
    // basket
    $this->defaultPayment = 2;
    $obContext = Context::getCurrent();

    $arResult['SERVICES'] = $this->getServices();

    $arResult['DELIVERY_SERVICES'] = $this->getDeliveryServices('CODE');
    $arExcept = array_keys($arResult['SERVICES']);

    $cityParams = \Dsklad\Order::getCityParams();
    $cityConditions = $cityParams['CONDITIONS'] != false ? json_decode($cityParams['CONDITIONS']) : false;
    $arResult['IS_KZ'] = $cityParams['COUNTRY_CODE'] == 'KZ' ? "Y" : "N";
    $this->isPCLtarif = true;

    foreach ($arResult['DELIVERY_SERVICES'] as $arItem) {
        $arExcept[] = $arItem['ID'];
    }

    $arBasketItems = $this->getBasketItems($arExcept);

    // $this->packedArray = $arBasketItems['SHIPMENT'];

    /* Есть ли КГТ */
    $isKGTpackEnabled = false; $maxPackLength = $maxPackWidth = $maxPackHeight = $sumVolume = $maxPackWeight = 0;
    foreach ($this->packedArray as $arBasketItem) {

        if (empty($arBasketItem['PACK'])) continue;

        $notPackQuantity = $arBasketItem['QUANTITY'];
        while ($notPackQuantity > 0) {
            foreach ($arBasketItem['PACK'] as $pack) {
                if ($notPackQuantity >= $pack['QUANTITY']) {

                    $maxPackWeight = ($maxPackWeight < $pack['WEIGHT']) ? $pack['WEIGHT'] : $maxPackWeight;
                    $maxPackLength = $maxPackLength > $pack['LENGTH'] ? $maxPackLength : $pack['LENGTH'];
                    $maxPackWidth = $maxPackWidth > $pack['WIDTH'] ? $maxPackWidth : $pack['WIDTH'];
                    $maxPackHeight = $maxPackHeight > $pack['HEIGHT'] ? $maxPackHeight : $pack['HEIGHT'];

                    $itemVolume = $pack['LENGTH'] * $pack['WIDTH'] * $pack['HEIGHT'];
                    $sumVolume += $itemVolume;

                    $notPackQuantity -= $pack['QUANTITY'];

                    break;
                }
            }
        }

        $this->isPCLtarif = stripos($arBasketItem['NAME'], 'носки') !== false && $this->isPCLtarif ? $this->isPCLtarif : false;

    }

    $arResult['IS_PCL'] = $this->isPCLtarif ? 'Y' : 'N';

    // HACK KGT TODO
    $isKGTpackEnabled = ($maxPackWeight > KGT_WEIGHT ? true : false);

    /*подсчет скидки*/
    $totalDiscount = 0;
    $totalSumNotDiscount = 0;
    foreach ($arBasketItems['SHIPMENT'] as $arBasketItem) {
        if($arBasketItem['DISCOUNT_PRODUCT']['DISCOUNT'] != 0
            && $arBasketItem['DISCOUNT_PRODUCT']['DISCOUNT'] != ''
        ){
            $totalDiscount += $arBasketItem['DISCOUNT_PRODUCT']['DISCOUNT']*$arBasketItem['QUANTITY'];
            $totalSumNotDiscount += $arBasketItem['DISCOUNT_PRODUCT']['BASE_PRICE']*$arBasketItem['QUANTITY'];
        }
        else{
            $totalSumNotDiscount += $arBasketItem['NO_FORMAT_PRICE']*$arBasketItem['QUANTITY'];
        }
    }
    if($totalDiscount != 0){
        $arResult['TOTAL_SUM_NOT_DISCOUNT'] = number_format($totalSumNotDiscount, 0, '', ' ') . '.–';
        $arResult['TOTAL_DISCOUNT'] = number_format($totalDiscount, 0, '', ' ') . '.–';
    }
    $arResult['BASKET_ITEMS'] = $arBasketItems['SHIPMENT'];
    $arResult['CATALOG'] = $arBasketItems['CATALOG'];

    if ($APPLICATION->GetCurPage(false) == $arParams['ORDER_SUCCESS_PAGE'] && count($arResult['BASKET_ITEMS']) > 0)
        LocalRedirect($arParams['CHECKOUT_PAGE_URL'], 'refresh');

    foreach ($arResult['SERVICES'] as $intID => $arItem) {
        if (array_search($intID, $arBasketItems['SERVICES']) === false) {
            $arResult['SERVICES'][$intID]['CHECK'] = false;
        } else {
            // var_dump($this->intTotalSumServices);
            // $obBasket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), \Bitrix\Main\Context::getCurrent()->getSite());
            $item = $this->obBasket->getExistsItem('catalog', $intID);
            if (intVal($item->getField('PRICE')) - intVal($this->intTotalSumServices) > 0)
                $warrantyCoef = intVal($item->getField('PRICE')) - intVal($this->intTotalSumServices);
            $item->setField('PRICE',$this->intTotalSumServices);
            $arResult['SERVICES'][$intID]['CHECK'] = true;
            $this->obBasket->save();
        }
    }

    if ($cityConditions !== false) {

        if ($cityConditions->D_WEEKEND_1 == 'Y' && $cityConditions->D_WEEKEND_2 == 'Y') {
            unset($arResult['DELIVERY_SERVICES']['delivery_weekend_1']);
            unset($arResult['DELIVERY_SERVICES']['delivery_weekend_2']);
            // unset($arResult['DELIVERY_SERVICES']['delivery_up_lift']);
        }

        if ($cityConditions->D_WEEKEND_1 == 'Y' && $cityConditions->D_WEEKEND_2 != 'Y') {
            unset($arResult['DELIVERY_SERVICES']['delivery_weekend_2']);
            unset($arResult['DELIVERY_SERVICES']['delivery_weekend']);
            // unset($arResult['DELIVERY_SERVICES']['delivery_up_lift']);
        }

        if ($cityConditions->D_WEEKEND_1 != 'Y' && $cityConditions->D_WEEKEND_2 == 'Y') {
            unset($arResult['DELIVERY_SERVICES']['delivery_weekend_1']);
            unset($arResult['DELIVERY_SERVICES']['delivery_weekend']);
            // unset($arResult['DELIVERY_SERVICES']['delivery_up_lift']);
        }

        if ($cityConditions->D_WEEKEND_1 != 'Y' && $cityConditions->D_WEEKEND_2 != 'Y') {
            unset($arResult['DELIVERY_SERVICES']['delivery_weekend_1']);
            unset($arResult['DELIVERY_SERVICES']['delivery_weekend_2']);
            unset($arResult['DELIVERY_SERVICES']['delivery_weekend']);
        }

        if ($cityConditions->D_EVENING != 'Y') {
            unset($arResult['DELIVERY_SERVICES']['delivery_evening']);
        }

        if ($cityConditions->D_UP_LIFT != 'Y' && $isKGTpackEnabled !== false) {
            // unset($arResult['DELIVERY_SERVICES']['delivery_up_lift']);
            $arResult['DELIVERY_SERVICES']['delivery_up_lift']['IS_KGT'] = 'Y';
            $arResult['DELIVERY_SERVICES']['delivery_up_lift']['IS_KGT_REQ'] = 'Y';
        }

        if ($cityConditions->D_UP_LIFT == 'Y' && $isKGTpackEnabled !== false) {
            $arResult['DELIVERY_SERVICES']['delivery_up_lift']['IS_KGT'] = 'Y';
        }

    } else {
         unset($arResult['DELIVERY_SERVICES']);
    }

    foreach ($arResult['DELIVERY_SERVICES'] as $intSort => $arItem) {
        if (array_search($arItem['ID'], $arBasketItems['SERVICES']) === false) {
            $arResult['DELIVERY_SERVICES'][$intSort]['CHECK'] = false;
        } else {
            $arResult['DELIVERY_SERVICES'][$intSort]['CHECK'] = true;
        }

        if( $arItem['XML_ID'] == 'obreshetka' && $arBasketItems['OBRESHETKA_COUNT'] ){
            $arResult['DELIVERY_SERVICES'][$intSort]['PRICE'] = number_format($arBasketItems['OBRESHETKA_COUNT']*$arItem['~PRICE'], 0, '', ' ') . ' руб.';
            $arResult['DELIVERY_SERVICES'][$intSort]['~PRICE'] = $arBasketItems['OBRESHETKA_COUNT']*$arItem['~PRICE'];
        }
    }

    $obRequest = $obContext->getRequest();

    $arResult['PERSON_TYPE'] = 1;
    if ($obRequest->get('is_legal_entity') == 'true') { // зануление для юр лиц
        $intCoast = '0';
        // $arResult['cantCalculateDelivery'] = true;
        $arResult['PERSON_TYPE'] = 2;
    }

    // coast
    if (!in_array($cityParams['CODE'], array('78000000000', '77000000000')) || $this->isPCLtarif || (in_array($cityParams['CODE'], array('78000000000', '77000000000')) && $arResult['PERSON_TYPE'] == 2)) {
        $DPD_RESULT_COAST = $this->getDPDCoast($this->packedArray, false, 0, true);
    } else {
        $arResult['DELIVERY_PERIOD'] = 'static';
        $DPD_RESULT_COAST = [
            'days' => "1-3",
        ];

        if ($obRequest->get('delivery') == 8) {
            $DPD_RESULT_COAST['cost'] = DELIVERY_COAST;
        }
    }

    if (is_array($DPD_RESULT_COAST)) {
        $intCoast = $DPD_RESULT_COAST['cost'];

        // Delivery pariod
        if (!empty($DPD_RESULT_COAST['days']) && $DPD_RESULT_COAST['days'] !== 0)
            $arResult['DELIVERY_DAYS'] = $DPD_RESULT_COAST['days'];
        else
            $arResult['DELIVERY_DAYS'] = 3;

    } else {
        $intCoast = $DPD_RESULT_COAST;
    }

    if($intCoast === '-') {
        $arResult['cantCalculateDelivery'] = true;
    }

    $intCoast += $this->intDeliveryServicesSum;

    if ($obRequest->get('deliveryPointCode')) {
        $arResult['CURRENT_DELIVERY_POINT_CODE'] = $obRequest->get('deliveryPointCode');
    }

    $arResult['~COAST'] = $intCoast;
    $arResult['COAST'] = number_format($arResult['~COAST'], 0, '', ' ') . '.-';

    // dpd
    $this->setSourceTerminals('HL');

    \Bitrix\Main\Loader::includeModule('dsklad.site');
    $arData = \Dsklad\Order::getDPDTerminals($arResult['BASKET_ITEMS']);

    $arResult['mapParams'] = $arData['mapParams'];
    $arResult['TERMINAL'] = array();

    foreach ($arData['TERMINAL'] as $arItem) {
        if (!empty($arItem['schedule']['operation'])) {
            $arItem['schedule'] = array($arItem['schedule']);
        }
        $arItem['address']['terminalAddress'] = 'г. ' . $arItem['address']['cityName'] . ', ';
        $arItem['address']['terminalAddress'] .= $arItem['address']['streetAbbr'] . ', ';
        $arItem['address']['terminalAddress'] .= $arItem['address']['street'] . ', ';
        $arItem['address']['terminalAddress'] .= (!empty($arItem['address']['houseNo']))? $arItem['address']['houseNo'] : $arItem['address']['ownership'];
        $arResult['TERMINAL'][] = $arItem;
    }

    // delivery
    $arDelivery = $this->getDelivery();
    $arResult['DELIVERY'] = array();
    $arResult['FIRST_DELIVERY_ID'] = 0;

    $i = 0;
    foreach ($arDelivery as $arItem) {
        if (strpos($arItem['DESCRIPTION'], 'dellin') !== false) {
            continue; // Не выводить самовывоз и доставку ТК Деловые линии.
        }
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

    // Get order coupon !!! LAST listed
    $arResult['ORDER_COUPON'] = $this->GetOrderCoupon();

    // pay systems
    $arPayment = $this->getPaySystems();
    $arResult['PAYMENT'] = array();

    $arPayMap = array(
        2 => 'card2',
        3 => 'wallet',
        4 => 'bank'
    );

    $arResult['FIRST_PAYMENT_ID'] = $_REQUEST['payment'] ? : 2;
    foreach ($arPayment as $i => $arItem) {
        $arResult['PAYMENT'][$arItem['SORT']] = array(
            'ID' => $arItem['ID'],
            'NAME' => $arItem['~NAME'],
            'CLASS' => $arPayMap[$arItem['ID']],
            'ACTIVE' => $arItem['ID'] == $arResult['FIRST_PAYMENT_ID'] ? true : false
        );
    }

    $UF_DELIVERY_COEF = ((($this->intTotalSum - $warrantyCoef)*0.003)+UR_DELIVERY_COEF);

    // Костыль для фиксированной стоимости доставки для москвы и питера с учетом терминалов
    if ((in_array($cityParams['CODE'], array('78000000000', '77000000000')) AND $obRequest->get('delivery') !== '8') && $this->isPCLtarif === false) { // 500 рублей доставка для МСК и СПБ если не терминал, если терминал то 0

        if ($arResult['PERSON_TYPE'] == 2) {

            if ($cityParams['CODE'] == '78000000000') {
                $arResult['~COAST'] = $arResult['TERMINAL'][0]['is_terminal'] == 'Y' ? $intCoast+$UF_DELIVERY_COEF : UR_PICKUP_PVZ_COAST_SPB;
                // $arResult['~COAST'] = $arResult['TERMINAL'][0]['is_terminal'] == 'Y' ? UR_PICKUP_COAST_SPB : UR_PICKUP_PVZ_COAST_SPB;
            }
            if ($cityParams['CODE'] == '77000000000') {
                $arResult['~COAST'] = $arResult['TERMINAL'][0]['is_terminal'] == 'Y' ? $intCoast+$UF_DELIVERY_COEF : UR_PICKUP_PVZ_COAST_MSK;
                // $arResult['~COAST'] = $arResult['TERMINAL'][0]['is_terminal'] == 'Y' ? UR_PICKUP_COAST_MSK : UR_PICKUP_PVZ_COAST_MSK;
            }

        } else {

            if ($cityParams['CODE'] == '78000000000') {
                $arResult['~COAST'] = $arResult['TERMINAL'][0]['is_terminal'] == 'Y' ? PICKUP_COAST_SPB : PICKUP_PVZ_COAST_SPB;
            }
            if ($cityParams['CODE'] == '77000000000') {
                $arResult['~COAST'] = $arResult['TERMINAL'][0]['is_terminal'] == 'Y' ? PICKUP_COAST_MSK : PICKUP_PVZ_COAST_MSK;
            }

        }

        $arResult['cantCalculateDelivery'] = false;
        $arResult['COAST'] = number_format($arResult['~COAST'], 0, '', ' ') . '.-';
    } else {

        if ($arResult['PERSON_TYPE'] == 2) {
            $arResult['~COAST'] = ($arResult['~COAST']+$UF_DELIVERY_COEF);
            if ($obRequest->get('delivery') == '8') {
                $arResult['~COAST'] = $intCoast+$UF_DELIVERY_COEF;
            }
        }


    }


    if ($APPLICATION->DPDnotAvailable) {
        // if (!in_array($cityParams['CODE'], array('78000000000', '77000000000'))) {
            // $arResult['cantCalculateDelivery'] = true;
            $arResult['~COAST'] = 0;
        // }
        $arResult['DPDnotAvailable'] = $APPLICATION->DPDnotAvailable;
    }

    // Костыль если сумма доставки больше 4000
    if (
        ($this->intTotalSum >= 100000)
        || ($arResult['~COAST'] >= 4000 && !$arResult['DPDnotAvailable'])
        || ($obRequest->get('delivery') == '8'
            && (
                $cityParams['REGION_CODE'] == '77'
                || $cityParams['REGION_CODE'] == '50'
                || $cityParams['REGION_CODE'] == '47'
                || $cityParams['REGION_CODE'] == '78'
            )
        )
        || ($arResult['PERSON_TYPE'] == 2 || $cityParams['CASH_PAYMENT_OFF'] == 'Y')
    ) {
        $arResult['PAYMENT'] = array_map(function ($item) {
            if ($item['ID'] == '3')  {
                $item['ACTIVE'] = false;
                $item['HIDE'] = 'Y';
            }
            return $item;
        }, $arResult['PAYMENT']);
    }
    
    // prices
    $arResult['NEEDED'] = $this->needed;
    $this->intTotalSum -= $this->intDeliveryServicesSum;
    $arResult['TOTAL_SERVICES'] =  $this->intTotalSumServices;
    $arResult['~TOTAL_SUMM'] = $this->intTotalSum - $warrantyCoef;
    $arResult['TOTAL_SUMM'] = number_format($arResult['~TOTAL_SUMM'], 0, '', ' ') . '.–';
    $arResult['~TOTAL_RESULT'] = $arResult['~TOTAL_SUMM'] + $arResult['~COAST'];
    $arResult['TOTAL_RESULT'] = number_format($arResult['~TOTAL_RESULT'], 0, '', ' ') . '.–';

} elseif ($this->getStatus() == 'order') {

    $obContext = Context::getCurrent();
    $obRequest = $obContext->getRequest();
    $this->isPCLtarif = true;
    // var_dump($obRequest);die();
    $cityParams = \Dsklad\Order::getCityParams();
    $arResult['IS_KZ'] = $cityParams['COUNTRY_CODE'] == 'KZ' ? "Y" : "N";
    \Bitrix\Main\Loader::includeModule('dsklad.site');
    $arResult['DELIVERY_SERVICES'] = $this->getDeliveryServices('CODE');
    foreach ($arResult['DELIVERY_SERVICES'] as $arItem) {
        $arExcept[] = $arItem['ID'];
    }
    $arBasketItems = $this->getBasketItems($arExcept);

    $arData = \Dsklad\Order::getDPDTerminals($arBasketItems['SHIPMENT']);
    $arResult['TERMINAL'] = $arData['TERMINAL']; $key = 0;
    foreach ($arResult['TERMINAL'] as $k => $terminal) {
        if ($terminal['terminalCode'] == $obRequest->get('point'))
            $key = $k;
    }

    foreach ($arBasketItems['SHIPMENT'] as $arBasketItem) {
        if (empty($arBasketItem['PACK'])) continue;
        $this->isPCLtarif = stripos($arBasketItem['NAME'], 'носки') !== false && $this->isPCLtarif ? $this->isPCLtarif : false;
    }

    $arResult['IS_PCL'] = $this->isPCLtarif ? 'Y' : 'N';
    $arResult['PERSON_TYPE'] = 1;
    if ($obRequest->get("legal") == '1') {
        $arResult['PERSON_TYPE'] = 2;
    }

    if ((in_array($cityParams['CODE'], array('78000000000', '77000000000')) AND $obRequest->get('delivery') !== '8') && $this->isPCLtarif === false) { // 500 рублей доставка для МСК и СПБ если не терминал, если терминал то 0

        if ($arResult['PERSON_TYPE'] == 2) {

            if ($cityParams['CODE'] == '78000000000') {
                $_REQUEST['delivery_price'] = $arResult['TERMINAL'][$key]['is_terminal'] == 'Y' ? UR_PICKUP_COAST_SPB : 0;
                // $_REQUEST['delivery_price'] = $arResult['TERMINAL'][$key]['is_terminal'] == 'Y' ? UR_PICKUP_COAST_SPB : UR_PICKUP_PVZ_COAST_SPB;
            }
            if ($cityParams['CODE'] == '77000000000') {
                $_REQUEST['delivery_price'] = $arResult['TERMINAL'][$key]['is_terminal'] == 'Y' ? UR_PICKUP_COAST_MSK : 0;
                // $_REQUEST['delivery_price'] = $arResult['TERMINAL'][$key]['is_terminal'] == 'Y' ? UR_PICKUP_COAST_MSK : UR_PICKUP_PVZ_COAST_MSK;
            }

        } else {

            if ($cityParams['CODE'] == '78000000000') {
                $_REQUEST['delivery_price'] = $arResult['TERMINAL'][$key]['is_terminal'] == 'Y' ? PICKUP_COAST_SPB : PICKUP_PVZ_COAST_SPB;
            }
            if ($cityParams['CODE'] == '77000000000') {
                $_REQUEST['delivery_price'] = $arResult['TERMINAL'][$key]['is_terminal'] == 'Y' ? PICKUP_COAST_MSK : PICKUP_PVZ_COAST_MSK;
            }
        }
    }

    $UF_DELIVERY_COEF = ((($this->intTotalSum - $warrantyCoef)*0.003)+UR_DELIVERY_COEF);
    if ($arResult['PERSON_TYPE'] == 2) {
        $arResult['~COAST'] = ($_REQUEST['delivery_price']+$UF_DELIVERY_COEF);
    }

    try {
        $arResult['DEBUG_RES'] = $this->doOrder();
    } catch (Exception $e) {
        extra_log([
            'exception_type' => 'do_order_error',
            'exception_entity' => 'do_order_method_error',
            'mail_comment' => 'Ошибка в сохранении заказа',
            'entity_id' => $arResult['DEBUG_RES']->getID(),
            'entity_type' => 'DO ORDER ERROR',
            'exception_text' => $e,
        ]);
    }

    $arResult['STATUS'] = 'success';
}


$arResult['OBRESHETKA_COUNT'] = 0; //$arBasketItems['OBRESHETKA_COUNT'];

if(!$arResult['~COAST'] && in_array($arResult['TERMINAL'][0]['terminalCode'], 'LED', 'M13')) {
    foreach($arResult['DELIVERY'] as $key => $value) {
        if($value['ID'] == 7) {
            unset($arResult['DELIVERY'][$key]);
        }
    }
}



$this->getUserInfo();

$this->IncludeComponentTemplate();
