<?php
function retailCrmBeforeOrderSave($order)
{
    if ($order['customFields']['no_upload']) {
        return false;
    } elseif(!$order['externalId'] && !$order['create']) {

        CModule::IncludeModule("intaro.retailcrm");
        $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
        $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
        $api_site = "dsklad-ru";
        $client = new \RetailCrm\ApiClient($api_host, $api_key, $api_site);
        $response = $client->ordersGet($order['id'], 'id', $api_site);
        if ($response->isSuccessful()) {
            if (!empty($dataOrder = $response->order)) {
                $order = $dataOrder;
            }
        } else {
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/intaro.retailcrm/log/retailCrmBeforeOrderSave.log', print_r($order, true), FILE_APPEND);
        }

        // file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/order.log', print_r($order, true), FILE_APPEND);
    }

    return $order;
}

//slanes:begin
function retailCrmBeforeOrderSend($order, $arFields)
{
    // file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/order-properties.log', print_r($arFields, true), FILE_APPEND);


    $orderObject = Bitrix\Sale\Order::load($order['number']);
    if(array_search(end($orderObject->getDeliverySystemId()),DELIVERY_PICKUP_ID) === false) {
        $shipmentCollection = $orderObject->getShipmentCollection();
        $stores = \Bitrix\Sale\Delivery\ExtraServices\Manager::getExtraServicesList(end($orderObject->getDeliverySystemId()));
        foreach ($shipmentCollection as $shipment) {

            if ($shipment->isSystem())
                continue;

            $extra = $shipment->getExtraServices();
            if (!empty($extra)) {
                foreach ($extra as $key => $value) {
                    if ($value == 'Y') {
                        switch ($stores[$key]['CODE']) {
                            case 'D_UP_LIFT':
                                $order['customFields']['podiem_na_etazh'] = true;
                                break;
                            case 'D_WEEKEND_1':
                                $order['customFields']['dostavka_v_vihodnoi'] = true;
                                $order['customFields']['dostavka_v_subbotu'] = true;
                                break;
                            case 'D_WEEKEND_2':
                                $order['customFields']['dostavka_v_vihodnoi'] = true;
                                $order['customFields']['dostavka_v_voskresenie'] = true;
                                break;
                            case 'D_WEEKEND_3':
                                $order['customFields']['dostavka_v_vihodnoi'] = true;
                                break;
                            case 'D_EVENING':
                                $order['customFields']['dostavka_v_vechernee_vremia'] = true;
                                break;
                        }
                    }
                }
            }
        }
    }

    foreach ($arFields['PROPS']['properties'] as $property) {
        if ($property['CODE'] == 'U_INN' && !empty($property['VALUE'][0])) {
            $order['contragent']['INN'] = $property['VALUE'][0];
        }
        if ($property['CODE'] == 'U_COMPANY' && !empty($property['VALUE'][0])) {
            $order['contragent']['legalName'] = $property['VALUE'][0];
        }
        if ($property['CODE'] == 'ROISTAT_ID' && !empty($property['VALUE'][0])) {
            $order['customFields']['roistat'] = $property['VALUE'][0];
        }

        // makcrx:begin

        // Отправляем в RetailCRM код терминала DPD
        if ($property['CODE'] == 'DPD_TERMINAL_CODE' && is_array($property['VALUE']) ) {
            $order['customFields']['dpd_terminal_code'] = $property['VALUE'][0];
        }

        // auth token F
        if ($property['CODE'] == 'F_TOKEN' && is_array($property['VALUE']) ) {
            $order['customFields']['user_token'] = $property['VALUE'][0];
        }

        // auth token U
        if ($property['CODE'] == 'U_TOKEN' && is_array($property['VALUE']) ) {
            $order['customFields']['user_token'] = $property['VALUE'][0];
        }

        // pay sms send F
       if ($property['CODE'] == 'F_PAYSMSSEND' && is_array($property['VALUE']) ) {
           $order['customFields']['paysmssend'] = $property['VALUE'][0];
       }

        // pay sms send F
       if ($property['CODE'] == 'U_PAYSMSSEND' && is_array($property['VALUE']) ) {
           $order['customFields']['paysmssend'] = $property['VALUE'][0];
       }

        // Отправляем в RetailCRM код тарифа DPD
        if ($property['CODE'] == 'DPD_CODE' || $property['CODE'] == 'U_DPD_CODE') {
            $order['customFields']['dpd_code'] = strtolower(str_replace(' ', '_', $property['VALUE'][0]));
        }

        // Send promocode to CRM
        if ($property['CODE'] == 'F_PROMO' || $property['CODE'] == 'U_PROMO') {
            if (!empty($property['VALUE'][0])) {
                $order['customFields']['promo'] = $property['VALUE'][0];
            }
        }

        // кто оплачивает: получатель/отправитель
        if ($property['CODE'] == 'PAYER' && $property['VALUE'][0] != "") {
            $order['customFields']['payer'] = strtolower($property['VALUE'][0]);
        }

        // Отправляем заказ с типом "Предзаказ", если в нём все товары по предзаказу
        if ($property['CODE'] == 'PREORDER' && $property['VALUE'][0] == 'Y' ) {
            $order['orderMethod'] = 'pre-order';
        }

        // Помечаем товары по предзаказу
        if ($property['CODE'] == 'QUANTITIES' && !empty($property['VALUE'][0]) ) {
            $quantities = json_decode($property['VALUE'][0]);
            foreach ($quantities as $productId => $quantity) {
                if ($quantity <= 0) {
                    foreach ($order["items"] as $i => $item) {
                        if ($item['offer']['externalId'] == $productId) {
                            $product = CCatalogProduct::getByIdEx($productId);
                            $arrivalDate = $product["PROPERTIES"]["ARRIVAL_DATE"]["VALUE"];
                            if (!$arrivalDate)
                                $arrivalDate = 'Да';
                            $order["items"][$i]["properties"]['preorder'] = array(
                                'name'  => 'Предзаказ',
                                'value' => $arrivalDate
                            );
                        }
                    }
                }
            }
        }

        // отправляем поле "Позвонить"
        if (($property['CODE'] == 'F_NOT_CALL' || $property['CODE'] == 'U_NOT_CALL') && !empty($property['VALUE'][0])) {
            if ($property['VALUE'][0] == 'Y') {
                $order['customFields']['no_call'] = true;
            } else {
                $order['customFields']['no_call'] = false;
            }
        }

        // Отправляем в RetailCRM код терминала DPD
        if ($property['CODE'] == 'DPD_TERMINAL_CODE' && is_array($property['VALUE']) ) {
            $order['customFields']['dpd_terminal_code'] = $property['VALUE'][0];
        }

        // Отправляем в RetailCRM покупатель и получатель одно лицо
        if ($property['CODE'] == 'BUYER_AND_RECEIVER_THE_SAME' && is_array($property['VALUE']) ) {
            if ($property['VALUE'][0] == 'Y') {
                $order['customFields']['buyer_and_receiver_the_same'] = true;
            } else {
                $order['customFields']['buyer_and_receiver_the_same'] = false;
            }
        }

        // Отправляем в RetailCRM имя получателя
        if ($property['CODE'] == 'RECEIVER_NAME' && is_array($property['VALUE']) ) {
            $order['customFields']['receiver_name'] = $property['VALUE'][0];
        }

        // Отправляем в RetailCRM телефон получателя
        if ($property['CODE'] == 'RECEIVER_PHONE' && is_array($property['VALUE']) ) {
            $order['customFields']['receiver_phone'] = $property['VALUE'][0];
        }

        // Отправляем в RetailCRM квартиру/офис
        if (!empty($property['VALUE'][0]) && ($property['CODE'] == 'F_ROOM' || $property['CODE'] == 'U_ROOM')){
            $order['delivery']['address']['text'] = $order['delivery']['address']['text'].', кв '.$property['VALUE'][0];
            //$room = $property['VALUE'][0];
        }


        // makcrx:end
    }

    // Заказ в 1 клик
    if ($arFields['USER_DESCRIPTION'] == 'Заказ в 1 клик') {
        $order['orderMethod'] = 'one-click';
    }

    // Передавать адрес доставки из битрикс в retailCRM в доп.инфу
    $order['delivery']['address']['notes'] = $order['delivery']['address']['text'];

    #file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/makcrx.log', print_r($arFields, true));
    #file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/makcrx.log', print_r($order, true), FILE_APPEND);

    return $order;
}

function retailCrmAfterOrderSave($order)
{
    AddMessage2Log(
        __FILE__ . PHP_EOL .
        __FUNCTION__ . PHP_EOL .
        'arOrder: '. print_r($order, true) . PHP_EOL .
        'DEBUGTRACE: '. print_r(debug_backtrace(), true)
    );


    if (!empty($order['customFields']['track_number']) && $order['externalId']) {
        CSaleOrder::Update($order['externalId'], array('TRACKING_NUMBER' => $order['customFields']['track_number']));
    }

    if ($order['externalId']) {
        $orderB = \Bitrix\Sale\Order::load($order['externalId']);
        $propertyCollection = $orderB->getPropertyCollection();
        $save = false;

        // кто оплачивает: получатель/отправитель
        if ($order['customFields']['payer']) {
            $somePropValue = $propertyCollection->getItemByOrderPropertyId(26);
            if (!$somePropValue)
                $somePropValue = $propertyCollection->getItemByOrderPropertyId(27);

            if ($somePropValue) {
                $somePropValue->setValue(strtoupper($order['customFields']['payer']));
                $save = true;
            }
        }

        // метод оплаты нал/безнал

        if ($order['customFields']['payment_method']) {
            $somePropValue = $propertyCollection->getItemByOrderPropertyId(28);
            if (!$somePropValue)
                $somePropValue = $propertyCollection->getItemByOrderPropertyId(29);

            if ($somePropValue) {
                $somePropValue->setValue(strtoupper($order['customFields']['payment_method']));
                $save = true;
            }
        }

        // НДС: с НДС/без НДС
        if ($order['customFields']['vat']) {
            $somePropValue = $propertyCollection->getItemByOrderPropertyId(32);
            if (!$somePropValue)
                $somePropValue = $propertyCollection->getItemByOrderPropertyId(33);

            if ($somePropValue) {
                switch ($order['customFields']['vat']) {
                    case 'no-nds':
                        $somePropValue->setValue('без НДС');
                        break;
                    case 'nds':
                        $somePropValue->setValue('с НДС');
                        break;
                }
                $save = true;
            }
        }

        // Источник
        if ($order['customFields']['source']) {
            $source = explode('.', $order['customFields']['source']);
            if (count($source) == 2)
                $source = $source[1];
            else
                $source = $order['customFields']['source'];
            $somePropValue = $propertyCollection->getItemByOrderPropertyId(34);
            if (!$somePropValue)
                $somePropValue = $propertyCollection->getItemByOrderPropertyId(35);

            if ($somePropValue) {
                $somePropValue->setValue($source);
                $save = true;
            }
        }

        // Канал
        if ($order['customFields']['medium']) {
            $medium = explode('.', $order['customFields']['medium']);
            if (count($medium) == 2)
                $medium = $medium[1];
            else
                $medium = $order['customFields']['medium'];
            $somePropValue = $propertyCollection->getItemByOrderPropertyId(36);
            if (!$somePropValue)
                $somePropValue = $propertyCollection->getItemByOrderPropertyId(37);

            if ($somePropValue) {
                $somePropValue->setValue($medium);
                $save = true;
            }
        }

        // Кампания
        if ($order['customFields']['campaign']) {
            $campaign = explode('.', $order['customFields']['campaign']);
            if (count($campaign) == 2)
                $campaign = $campaign[1];
            else
                $campaign = $order['customFields']['campaign'];
            $somePropValue = $propertyCollection->getItemByOrderPropertyId(38);
            if (!$somePropValue)
                $somePropValue = $propertyCollection->getItemByOrderPropertyId(39);

            if ($somePropValue) {
                $somePropValue->setValue($campaign);
                $save = true;
            }
        }

        // Покупатель и получатель одно лицо
        if (isset($order['customFields']['buyer_and_receiver_the_same'])) {
            $val = $order['customFields']['buyer_and_receiver_the_same'];
            $somePropValue = $propertyCollection->getItemByOrderPropertyId(40);
            if (!$somePropValue)
                $somePropValue = $propertyCollection->getItemByOrderPropertyId(41);

            if ($somePropValue) {
                if ($val)
                    $somePropValue->setValue('Y');
                else
                    $somePropValue->setValue('N');
                $save = true;
            }
        }

        // Покупатель и получатель одно лицо
        if (isset($order['customFields']['receiver_name'])) {
            $val = $order['customFields']['receiver_name'];
            $somePropValue = $propertyCollection->getItemByOrderPropertyId(42);
            if (!$somePropValue)
                $somePropValue = $propertyCollection->getItemByOrderPropertyId(43);

            if ($somePropValue) {
                $somePropValue->setValue($val);
                $save = true;
            }
        }

        // Покупатель и получатель одно лицо
        if (isset($order['customFields']['receiver_phone'])) {
            $val = $order['customFields']['receiver_phone'];
            $somePropValue = $propertyCollection->getItemByOrderPropertyId(44);
            if (!$somePropValue)
                $somePropValue = $propertyCollection->getItemByOrderPropertyId(45);

            if ($somePropValue) {
                $somePropValue->setValue($val);
                $save = true;
            }
        }

        // Код услуги DPD
        if ($order['customFields']['dpd_code']) {
            $somePropValue = $propertyCollection->getItemByOrderPropertyId(46);
            if (!$somePropValue)
                $somePropValue = $propertyCollection->getItemByOrderPropertyId(47);

            if ($somePropValue) {
                switch ($order['customFields']['dpd_code']) {
                    case 'dpd_economy':
                        $somePropValue->setValue('DPD ECONOMY');
                        break;
                    case 'dpd_online_classic':
                        $somePropValue->setValue('DPD ONLINE CLASSIC');
                        break;
                    case 'dpd_online_express':
                        $somePropValue->setValue('DPD ONLINE EXPRESS');
                        break;
                    case 'dpd_online_max':
                        $somePropValue->setValue('DPD ONLINE MAX');
                        break;
                    case 'dpd_economy_cu':
                        $somePropValue->setValue('DPD ECONOMY CU');
                        break;
                    case 'dpd_optimum':
                        $somePropValue->setValue('DPD OPTIMUM');
                        break;
                }
                $save = true;
            }
        }

        // token auth
        if ($order['customFields']['user_token']) {
            $somePropValue = $propertyCollection->getItemByOrderPropertyId(63);
            if (!$somePropValue)
                $somePropValue = $propertyCollection->getItemByOrderPropertyId(64);

            if ($somePropValue) {
                $somePropValue->setValue($order['customFields']['user_token']);
                $save = true;
            }
        }

        // sms
        if ($order['customFields']['paysmssend']) {
            $somePropValue = $propertyCollection->getItemByOrderPropertyId(65);
            if (!$somePropValue)
                $somePropValue = $propertyCollection->getItemByOrderPropertyId(66);

            if ($somePropValue) {
                $somePropValue->setValue($order['customFields']['paysmssend']);
                $save = true;
            }
        }

        // Promocode GET INTO CRM
        if ($order['customFields']['uf_promo']) {
            $val = $order['customFields']['uf_promo'];
            $somePropValue = $propertyCollection->getItemByOrderPropertyId(51);
            if (!$somePropValue)
                $somePropValue = $propertyCollection->getItemByOrderPropertyId(52);

            if ($somePropValue) {
                $somePropValue->setValue($val);
                $save = true;
            }
        }

        if ($save)
            $orderB->save();
    }

    // способ оформления
    if ($order['orderMethod']) {
        if ($order['externalId']) {
            $orderId = $order['externalId'];
        } else {
            $dbOrders = CSaleOrder::GetList(array("ID" => "DESC"));
            $arOrder = $dbOrders->fetch();
            $orderId = $arOrder["ID"];
        }
        $orderB = \Bitrix\Sale\Order::load($orderId);
        $propertyCollection = $orderB->getPropertyCollection();

        $somePropValue = $propertyCollection->getItemByOrderPropertyId(30);
        if (!$somePropValue)
            $somePropValue = $propertyCollection->getItemByOrderPropertyId(31);

        if ($somePropValue) {
            $somePropValue->setValue($order['orderMethod']);
            $orderB->save();
        }
    }

    // дата отгрузки
    if ($order['shipmentDate']) {
        $orderId = $order['externalId'];
        $orderB = \Bitrix\Sale\Order::load($orderId);
        $shipmentCollection = $orderB->getShipmentCollection();
        // var_dump($order['shipmentDate']);
        // var_dump($orderId);
        // var_dump($shipmentCollection);
        foreach ($shipmentCollection as $shipment) {
            try {
                $shipment->setField('DELIVERY_DOC_DATE', \Bitrix\Main\Type\DateTime::createFromPhp(\DateTime::createFromFormat('Y-m-d', $order['shipmentDate'])));
            } catch (Exception $e) {
                // var_dump("Error set delivery date");
            }
        }
        $orderB->save();
    }

    return $order;
}

//slanes:begin
/**
 *   @author slanes
 *   отправляем дополнительные данные в срм по клиенту
 */
function retailCrmBeforeCustomerSend($customer)
{
    $userId = intVal($customer['externalId']);
    if($userId > 0){
        $rsUser = CUser::GetByID($userId);
        if($arUser = $rsUser->Fetch()){
            if(1 == intVal($arUser['UF_NOSEND_EMAIL__SMS'])){
                $customer['customFields']['nosend_sms_and_email_in_bitrix'] = 1;
            } else {
                $customer['customFields']['nosend_sms_and_email_in_bitrix'] = 0;
            }
        }
    }

    return $customer;
}
//slanes:end


/**
 *   @author slanes
 *   получаем дополнительные данные из срм по клиенту и пишем в битрикс
 */
function retailCrmAfterCustomerSave($customer)
{
    if(CModule::IncludeModule("intaro.retailcrm")){
        $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
        $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
        $client = new \RetailCrm\ApiClient($api_host, $api_key);

        $response = $client->customersList(array('ids' => array($customer['id'])));

        try {
            if(1 == count($response['customers'])){
                $nosend_sms_and_email_in_bitrix = intVal($response['customers'][0]['customFields']['nosend_sms_and_email_in_bitrix']);

                $userId = intVal($customer['externalId']);
                if($userId > 0){
                    $rsUser = CUser::GetByID($userId);
                    if($arUser = $rsUser->Fetch()){
                        if(intVal($arUser['UF_NOSEND_EMAIL__SMS']) != $nosend_sms_and_email_in_bitrix){
                            $user = new CUser;
                            $user->Update($userId, array("UF_NOSEND_EMAIL__SMS" => $nosend_sms_and_email_in_bitrix));
                        }
                    }
                }
            }
        } catch (Exception $e) {}
    }

    return;
}
//slanes:end
