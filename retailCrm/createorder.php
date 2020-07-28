<?php

$orderIdCrm = abs((int)$_GET['orderId']);
$userN = abs((int)$_GET['user']);

if (!$orderIdCrm) return;


// require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";

use Bitrix\Main\Loader;

    
    if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
	

	$api_by = 'id';
    $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
    $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
    $client = new \RetailCrm\ApiClient($api_host, $api_key);
    
    
    $response = $client->ordersGet($orderIdCrm, $api_by);
    
    if ($response->isSuccessful()) {
        for ($i=0; $i<=0; $i++) {
        $GLOBALS['RETAIL_CRM_HISTORY'] = true;
        
        $order = $response->order;
        
        if (isset($order['externalId']))
            die();
        // unset($order['externalId']);
        // unset($order['customer']['externalId']);
        
        // $order['phone'] = "+733333333$userN";
        // $order['email'] = "makcrx$userN@analitika.online";
        
        // $order['customer']['email'] = $order['email'];
        // $order['customer']['phones'][0]['number'] = $order['phone'];
        
        // echo "<pre>";
        // var_dump($order);
        // die();
        
        if (!CModule::IncludeModule("iblock")) {
            // RCrmActions::eventLog('RetailCrmHistory::customerHistory', 'iblock', 'module not found');

            return false;
        }
        if (!CModule::IncludeModule("sale")) {
            // RCrmActions::eventLog('RetailCrmHistory::customerHistory', 'sale', 'module not found');

            return false;
        }
        if (!CModule::IncludeModule("catalog")) {
            // RCrmActions::eventLog('RetailCrmHistory::customerHistory', 'catalog', 'module not found');

            return false;
        }
        
        
        global $MODULE_ID;
        global $CRM_PAYMENT;
        global $CRM_PAYMENT_TYPES;
        
        $MODULE_ID = 'intaro.retailcrm';
        $CRM_API_HOST_OPTION = 'api_host';
        $CRM_API_KEY_OPTION = 'api_key';
        $CRM_ORDER_TYPES_ARR = 'order_types_arr';
        $CRM_DELIVERY_TYPES_ARR = 'deliv_types_arr';
        $CRM_PAYMENT_TYPES = 'pay_types_arr';
        $CRM_PAYMENT_STATUSES = 'pay_statuses_arr';
        $CRM_PAYMENT = 'payment_arr'; //order payment Y/N
        $CRM_ORDER_LAST_ID = 'order_last_id';
        $CRM_SITES_LIST = 'sites_list';
        $CRM_ORDER_PROPS = 'order_props';
        $CRM_LEGAL_DETAILS = 'legal_details';
        $CRM_CUSTOM_FIELDS = 'custom_fields';
        $CRM_CONTRAGENT_TYPE = 'contragent_type';
        $CRM_ORDER_FAILED_IDS = 'order_failed_ids';
        $CRM_ORDER_HISTORY = 'order_history';
        $CRM_CUSTOMER_HISTORY = 'customer_history';
        $CRM_CATALOG_BASE_PRICE = 'catalog_base_price';
        $CRM_ORDER_NUMBERS = 'order_numbers';
        $CRM_CANSEL_ORDER = 'cansel_order';
        $CRM_CURRENCY = 'currency';

        // const CANCEL_PROPERTY_CODE = 'INTAROCRM_IS_CANCELED';
        
        
        $optionsOrderTypes = unserialize(COption::GetOptionString($MODULE_ID, $CRM_ORDER_TYPES_ARR, 0));
        $optionsDelivTypes = array_flip(unserialize(COption::GetOptionString($MODULE_ID, $CRM_DELIVERY_TYPES_ARR, 0)));
        $optionsPayStatuses = array_flip(unserialize(COption::GetOptionString($MODULE_ID, $CRM_PAYMENT_STATUSES, 0))); // --statuses
        $optionsOrderProps = unserialize(COption::GetOptionString($MODULE_ID, $CRM_ORDER_PROPS, 0));
        $optionsLegalDetails = unserialize(COption::GetOptionString($MODULE_ID, $CRM_LEGAL_DETAILS, 0));        
        $optionsSitesList = unserialize(COption::GetOptionString($MODULE_ID, $CRM_SITES_LIST, 0));
        $optionsOrderNumbers = COption::GetOptionString($MODULE_ID, $CRM_ORDER_NUMBERS, 0);
        $optionsCanselOrder = unserialize(COption::GetOptionString($MODULE_ID, $CRM_CANSEL_ORDER, 0));
        $optionsCurrency = COption::GetOptionString($MODULE_ID, $CRM_CURRENCY, 0);
        $currency = $optionsCurrency ? $optionsCurrency : \Bitrix\Currency\CurrencyManager::getBaseCurrency();

                if (!isset($order['externalId'])) {
                    if (!isset($order['customer']['externalId']) || !is_numeric($order['customer']['externalId']) || strpos($order['customer']['externalId'], 'excel') !== false) {
                        if (!isset($order['customer']['id'])) {
                            continue;
                        }

                        $registerNewUser = true;

                        if (!isset($order['customer']['email']) || $order['customer']['email'] == '') {
                            $login = $order['customer']['email'] = uniqid('user_' . time()) . '@crm.com';
                        } else {
                            $dbUser = CUser::GetList(($by = 'ID'), ($sort = 'ASC'), array('=EMAIL' => $order['email']));
                            switch ($dbUser->SelectedRowsCount()) {
                                case 0:
                                    $login = $order['customer']['email'];
                                    break;
                                case 1:
                                    $arUser = $dbUser->Fetch();
                                    $registeredUserID = $arUser['ID'];
                                    $registerNewUser = false;
                                    break;
                                default:
                                    $login = uniqid('user_' . time()) . '@crm.com';
                                    break;
                            }
                        }
                        
                        echo "<pre>";
                        var_dump("registerNewUser");
                        var_dump($registerNewUser);
                        var_dump($registeredUserID);

                        if ($registerNewUser === true) {
                            $userPassword = uniqid();

                            $newUser = new CUser;
                            $arFields = array(
                                "NAME"              => RCrmActions::fromJSON($order['customer']['firstName']),
                                "LAST_NAME"         => RCrmActions::fromJSON($order['customer']['lastName']),
                                "SECOND_NAME"       => RCrmActions::fromJSON($order['customer']['patronymic']),
                                "EMAIL"             => $order['customer']['email'],
                                "LOGIN"             => $login,
                                "ACTIVE"            => "Y",
                                "PASSWORD"          => $userPassword,
                                "CONFIRM_PASSWORD"  => $userPassword
                            );
                            if ($order['customer']['phones'][0]) {
                                $arFields['PERSONAL_PHONE'] = $order['customer']['phones'][0]['number'];
                            }
                            if ($order['customer']['phones'][1]) {
                                $arFields['PERSONAL_MOBILE'] = $order['customer']['phones'][1];
                            }

                            $registeredUserID = $newUser->Add($arFields);
                            var_dump("registeredUserID");
                            var_dump($registeredUserID);
                            if ($registeredUserID === false) {
                                var_dump($arFields);
                                $errortext = $newUser->LAST_ERROR . "\n";
                                $errortext .= print_r($arFields, true);
                                RCrmActions::eventLog("ajax/retailcrm/createorder.php; id=$orderIdCrm", 'CUser::Register', 'Error register user');
                                var_dump($errortext);
                                extra_log([
                                    "entity_type" => "retailcrmapi",
                                    "entity_id" => "120015",
                                    "exception_type" => "retailcrmapi_event",
                                    "exception_entity" => "retailcrmapi_createuser_error",
                                    "exception_text" => "[dsklad.ru] Ошибка создания пользователя при выгрузке заказа из retailcrm " . $errortext,
                                    "mail_comment" => "[dsklad.ru] Ошибка создания пользователя при выгрузке заказа из retailcrm " . $errortext
                                ]);

                                continue;
                            }

                            if(RCrmActions::apiMethod($client, 'customersFixExternalIds', __METHOD__, array(array('id' => $order['customer']['id'], 'externalId' => $registeredUserID))) == false) {
                                $innerId = $order['customer']['id'];
                                $errortext = "ID: $innerId, externalId: $registeredUserID";
                                extra_log([
                                    "entity_type" => "retailcrmapi",
                                    "entity_id" => "120017",
                                    "exception_type" => "retailcrmapi_event",
                                    "exception_entity" => "retailcrmapi_external_userid_error",
                                    "exception_text" => "[dsklad.ru] Ошибка проставления внешнего ID пользователю при выгрузке заказа из retailcrm " . $errortext,
                                    "mail_comment" => "[dsklad.ru] Ошибка проставления внешнего ID пользователю при выгрузке заказа из retailcrm " . $errortext
                                ]);
                                continue;
                            }
                        }
                        
                        if (!$order['customer']['externalId']) {
                            if(RCrmActions::apiMethod($client, 'customersFixExternalIds', __METHOD__, array(array('id' => $order['customer']['id'], 'externalId' => $registeredUserID))) == false) {
                                $innerId = $order['customer']['id'];
                                $errortext = "[2] ID: $innerId, externalId: $registeredUserID";
                                extra_log([
                                    "entity_type" => "retailcrmapi",
                                    "entity_id" => "120019",
                                    "exception_type" => "retailcrmapi_event",
                                    "exception_entity" => "retailcrmapi_external_userid_error",
                                    "exception_text" => "[dsklad.ru] Ошибка проставления внешнего ID пользователю при выгрузке заказа из retailcrm " . $errortext,
                                    "mail_comment" => "[dsklad.ru] Ошибка проставления внешнего ID пользователю при выгрузке заказа из retailcrm " . $errortext
                                ]);
                                continue;
                            }
                        }
                        
                        $newOrder['id'] = $order['id'];
                        $newOrder['customer'] = $order['customer'];
                        
                        $newOrder['customer']['externalId'] = $registeredUserID;
                        $order['customer']['externalId'] = $registeredUserID;
                        // $newOrder['customFields']['customer_created'] = true;
                        
                        // file_put_contents(__DIR__ . '/createorder.log', print_r($order, true));
                        
                        // if (function_exists('retailCrmAfterCustomerSave')) {
                            // $customer = $order['customer'];
                            // $customer['customFields'] = $order['customFields'];
                            // $customer['dont_need_request_to_retailcrm'] = true;
                            // retailCrmAfterCustomerSave($customer);
                        // }

                        $response = $client->ordersEdit($newOrder, $api_by);
                        var_dump($response);
                        // file_put_contents(__DIR__ . '/createorder.log', print_r($response, true), FILE_APPEND);
                        // die();
                    }
                    
                    if ($optionsSitesList) {
                        $site = array_search($order['site'], $optionsSitesList);
                    } else {
                        $site = CSite::GetDefSite();
                    }
                    if (empty($site)) {
                        RCrmActions::eventLog("ajax/retailcrm/createorder.php; id=$orderIdCrm", 'Bitrix\Sale\Order::create', 'Site = ' . $order['site'] . ' not found in setting. Order crm id=' . $order['id']);
                        
                        continue;
                    }

                    $newOrder = Bitrix\Sale\Order::create($site, (string)$order['customer']['externalId'], $currency);
                    
                    if (!is_object($newOrder) || !$newOrder instanceof \Bitrix\Sale\Order) {
                        RCrmActions::eventLog("ajax/retailcrm/createorder.php; id=$orderIdCrm", 'Bitrix\Sale\Order::create', 'Error order create');

                        continue;
                    }
                    
                    $externalId = $newOrder->getId();
                    $order['externalId'] = $externalId;
                }

                if (isset($order['externalId'])) {
                    $itemUpdate = false;

                    if ($order['externalId']) {
                        try {
                            $newOrder = Bitrix\Sale\Order::load($order['externalId']);
                        } catch (Bitrix\Main\ArgumentNullException $e) {
                            RCrmActions::eventLog("ajax/retailcrm/createorder.php; id=$orderIdCrm", 'Bitrix\Sale\Order::load', $e->getMessage() . ': ' . $order['externalId']);

                            continue;
                        }
                    }

                    if ($newOrder === null) {
                        RCrmActions::eventLog("ajax/retailcrm/createorder.php; id=$orderIdCrm", 'Bitrix\Sale\Order::load', 'Error order load number=' . $order['number']);

                        continue;
                    }

                    if ($optionsSitesList) {
                        $site = array_search($order['site'], $optionsSitesList);
                    } else {
                        $site = CSite::GetDefSite();
                    }

                    if (empty($site)) {
                        RCrmActions::eventLog("ajax/retailcrm/createorder.php; id=$orderIdCrm", 'Bitrix\Sale\Order::edit', 'Site = ' . $order['site'] . ' not found in setting. Order number=' . $order['number']);
                        
                        continue;
                    }

                    $personType = $newOrder->getField('PERSON_TYPE_ID');
                    if (isset($order['orderType']) && $order['orderType']) { 
                        $nType = array();
                        $tList = RCrmActions::OrderTypesList(array(array('LID' => $site)));
                        foreach($tList as $type){
                            if (isset($optionsOrderTypes[$type['ID']])) {
                                $nType[$optionsOrderTypes[$type['ID']]] = $type['ID'];
                            }
                        }
                        $newOptionsOrderTypes = $nType;

                        if ($newOptionsOrderTypes[$order['orderType']]) {
                            if ($personType != $newOptionsOrderTypes[$order['orderType']] && $personType != 0) {
                                $propsRemove = true;
                            }
                            $personType = $newOptionsOrderTypes[$order['orderType']];
                            $newOrder->setField('PERSON_TYPE_ID', $personType);
                        } elseif ($personType == 0) {
                            RCrmActions::eventLog("ajax/retailcrm/createorder.php; id=$orderIdCrm", 'orderType not found', 'PERSON_TYPE_ID = 0');
                        }
                    }
                    
                    
                    var_dump("status");
                    //status
                    if ($optionsPayStatuses[$order['status']]) {
                        $newOrder->setField('STATUS_ID', $optionsPayStatuses[$order['status']]);
                        if (in_array($optionsPayStatuses[$order['status']], $optionsCanselOrder)) {
                            unreserveShipment($newOrder);
                            $newOrder->setFieldNoDemand('CANCELED', 'Y');
                        } else {
                            $newOrder->setFieldNoDemand('CANCELED', 'N');
                        }
                    }


                    if (array_key_exists('statusComment', $order)) {
                        setProp($newOrder, RCrmActions::fromJSON($order['statusComment']), 'REASON_CANCELED');
                    }

                    var_dump("props");
                    //props
                    $propertyCollection = $newOrder->getPropertyCollection();
                    $propertyCollectionArr = $propertyCollection->getArray();
                    $nProps = array();
                    foreach ($propertyCollectionArr['properties'] as $orderProp) {
                        if ($orderProp['ID'][0] == 'n') {
                            $orderProp['ID'] = substr($orderProp['ID'], 1);
                            $orderProp['ID'] = $propertyCollection->getItemById($orderProp['ID'])->getField('ORDER_PROPS_ID');
                        }
                        $nProps[] = $orderProp;
                    }
                    $propertyCollectionArr['properties'] = $nProps;
                    
                    if ($propsRemove) {//delete props
                        foreach ($propertyCollectionArr['properties'] as $orderProp) {
                            if ($orderProp['PROPS_GROUP_ID'] == 0) {
                                $somePropValue = $propertyCollection->getItemByOrderPropertyId($orderProp['ID']);
                                setProp($somePropValue);
                            }
                        }
                        // $orderCrm['order'] = RCrmActions::apiMethod($api, 'orderGet', __METHOD__, $order['id']);

                        // $orderDump = $order;
                        // $order = $orderCrm['order'];
                    }
                    
                    $propsKey = array();
                    foreach ($propertyCollectionArr['properties'] as $prop) {
                        if ($prop['PROPS_GROUP_ID'] != 0) {
                            $propsKey[$prop['CODE']]['ID'] = $prop['ID'];
                            $propsKey[$prop['CODE']]['TYPE'] = $prop['TYPE'];
                        }
                    }
                    
                    var_dump("fio");
                    //fio
                    if ($order['firstName'] || $order['lastName'] || $order['patronymic']) {
                        $fio = '';
                        foreach ($propertyCollectionArr['properties'] as $prop) {
                            if (in_array($optionsOrderProps[$personType]['fio'], $prop)) {
                                $getFio = $newOrder->getPropertyCollection()->getItemByOrderPropertyId($prop['ID']);
                                if (method_exists($getFio, 'getValue')) {
                                    $fio = $getFio->getValue();
                                }
                            }
                        }

                        $fio = RCrmActions::explodeFIO($fio);
                        $newFio = array();
                        if ($fio) {
                            $newFio[] = isset($order['lastName']) ? RCrmActions::fromJSON($order['lastName']) : (isset($fio['lastName']) ? $fio['lastName'] : '');
                            $newFio[] = isset($order['firstName']) ? RCrmActions::fromJSON($order['firstName']) : (isset($fio['firstName']) ? $fio['firstName'] : '');
                            $newFio[] = isset($order['patronymic']) ? RCrmActions::fromJSON($order['patronymic']) : (isset($fio['patronymic']) ? $fio['patronymic'] : '');
                            $order['fio'] = trim(implode(' ', $newFio));
                        } else {
                            $newFio[] = isset($order['lastName']) ? RCrmActions::fromJSON($order['lastName']) : '';
                            $newFio[] = isset($order['firstName']) ? RCrmActions::fromJSON($order['firstName']) : '';
                            $newFio[] = isset($order['patronymic']) ? RCrmActions::fromJSON($order['patronymic']) : '';
                            $order['fio'] = trim(implode(' ', $newFio));
                        }
                    }
                    
                    var_dump("optionsOrderProps");
                    //optionsOrderProps
                    if ($optionsOrderProps[$personType]) {
                        foreach ($optionsOrderProps[$personType] as $key => $orderProp) {
                            if (array_key_exists($key, $order)) {
                                $somePropValue = $propertyCollection->getItemByOrderPropertyId($propsKey[$orderProp]['ID']);
                                if ($key == 'fio') {
                                    setProp($somePropValue, $order[$key]);
                                } else {
                                    setProp($somePropValue, RCrmActions::fromJSON($order[$key]));
                                }
                            } elseif (array_key_exists($key, $order['delivery']['address'])) {
                                if ($propsKey[$orderProp]['TYPE'] == 'LOCATION') {
                                    $order['delivery']['address'][$key] = trim($order['delivery']['address'][$key]);
                                    if(!empty($order['delivery']['address'][$key])){
                                        $parameters = array();
                                        $loc = explode('.', $order['delivery']['address'][$key]);
                                        if (count($loc) == 1) {
                                            $parameters['filter']['PHRASE'] = RCrmActions::fromJSON(trim($loc[0]));
                                        } elseif (count($loc) == 2) {
                                            $parameters['filter']['PHRASE'] = RCrmActions::fromJSON(trim($loc[1]));
                                        } else{
                                            RCrmActions::eventLog("ajax/retailcrm/createorder.php; id=$orderIdCrm", 'RetailCrmHistory::setProp', 'Error location. ' . $order['delivery']['address'][$key] . ' not found add in order number=' . $order['number']);
                                            continue;
                                        }
                                        $parameters['filter']['NAME.LANGUAGE_ID'] = 'ru';
                                        $location = \Bitrix\Sale\Location\Search\Finder::find($parameters, array('USE_INDEX' => false, 'USE_ORM' => false))->fetch();

                                        $somePropValue = $propertyCollection->getItemByOrderPropertyId($propsKey[$orderProp]['ID']);
                                        setProp($somePropValue, $location['CODE']);
                                    }  else {
                                        RCrmActions::eventLog("ajax/retailcrm/createorder.php; id=$orderIdCrm", 'RetailCrmHistory::setProp', 'Error location. ' . $order['delivery']['address'][$key] . ' is empty in order number=' . $order['number']);

                                        continue;
                                    }
                                } else {
                                    $somePropValue = $propertyCollection->getItemByOrderPropertyId($propsKey[$orderProp]['ID']);
                                    setProp($somePropValue, RCrmActions::fromJSON($order['delivery']['address'][$key]));
                                }
                            }
                        }
                    }

                    var_dump("optionsLegalDetails");
                    //optionsLegalDetails
                    if ($optionsLegalDetails[$personType]) {
                        foreach ($optionsLegalDetails[$personType] as $key => $orderProp) {
                            if (array_key_exists($key, $order['contragent'])) {
                                $somePropValue = $propertyCollection->getItemByOrderPropertyId($propsKey[$orderProp]['ID']);
                                setProp($somePropValue, $order['contragent'][$key]);
                            }
                        }
                    }

                    if ($propsRemove) {
                        $order = $orderDump;
                    }

                    var_dump("comments");
                    //comments
                    if (array_key_exists('customerComment', $order)) {
                        setProp($newOrder, RCrmActions::fromJSON($order['customerComment']), 'USER_DESCRIPTION');
                    }
                    if (array_key_exists('managerComment', $order)) {
                        setProp($newOrder, RCrmActions::fromJSON($order['managerComment']), 'COMMENTS');
                    }

                    var_dump("items");
                    //items
                    $basket = $newOrder->getBasket();

                    if (!$basket) {
                        $basket = Bitrix\Sale\Basket::create($site);
                        $newOrder->setBasket($basket);
                    }

                    $fUserId = $basket->getFUserId(true);

                    if ($fUserId === null || $fUserId == 0) {
                        $fUserId = Bitrix\Sale\Fuser::getIdByUserId($order['customer']['externalId']);
                        $basket->setFUserId($fUserId);
                    }

                    if (isset($order['items'])) {
                        $itemUpdate = true;

                        foreach ($order['items'] as $product) {
                            var_dump("getExistsItem");
                            $item = getExistsItem($basket, 'catalog', $product['offer']['externalId']);

                            if (!$item) {
                                if ($product['delete']) {
                                    continue;
                                }

                                $item = $basket->createItem('catalog', $product['offer']['externalId']);

                                if ($item instanceof \Bitrix\Sale\BasketItem) {
                                    $elem = getInfoElement($product['offer']['externalId']);
                                    $item->setFields(array(
                                        'CURRENCY' => $newOrder->getCurrency(),
                                        'LID' => $site,
                                        'BASE_PRICE' => $product['initialPrice'],
                                        'NAME' => $product['offer']['name'] ? RCrmActions::fromJSON($product['offer']['name']) : $elem['NAME'],
                                        'DETAIL_PAGE_URL' => $elem['URL'],
                                        'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
                                        'DIMENSIONS' => $elem['DIMENSIONS'],
                                        'WEIGHT' => $elem['WEIGHT'],
                                        'NOTES' => GetMessage('PRICE_TYPE'),
                                        'PRODUCT_XML_ID' => $elem["XML_ID"],
                                        'CATALOG_XML_ID' => $elem["IBLOCK_XML_ID"]
                                    ));
                                } else {
                                    RCrmActions::eventLog("ajax/retailcrm/createorder.php; id=$orderIdCrm", 'createItem', 'Error item add');

                                    continue;
                                }
                            }

                            if ($product['delete']) {
                                $item->delete();
                                
                                continue;
                            }

                            if ($product['quantity']) {
                                $item->setFieldNoDemand('QUANTITY', $product['quantity']);
                            }

                            if (array_key_exists('discountTotal', $product)) {
                                $itemCost = $item->getField('BASE_PRICE');
                                if (isset($itemCost) && $itemCost >= 0) {
                                    $item->setField('CUSTOM_PRICE', 'Y');
                                    $item->setField('PRICE', $itemCost - $product['discountTotal']);
                                    $item->setField('DISCOUNT_PRICE', $product['discountTotal']);
                                    $item->setField('DISCOUNT_NAME', '');
                                    $item->setField('DISCOUNT_VALUE', '');
                                }
                            }
                        }
                    }

                    $orderSumm = 0;
                    foreach ($basket as $item) {
                        $orderSumm += $item->getFinalPrice(); 
                    }

                    if (array_key_exists('cost', $order['delivery'])) {
                        $deliverySumm = $order['delivery']['cost'];
                    } else {
                        $deliverySumm = $newOrder->getDeliveryPrice();
                    }

                    $orderSumm += $deliverySumm;

                    $order['summ'] = $orderSumm;

                    var_dump("payment");
                    //payment
                    $newHistoryPayments = array();

                    if (array_key_exists('payments', $order)) {
                        if (!isset($orderCrm)) {
                            $orderCrm['order'] = $order;
                        }
                        if ($orderCrm) {
                            var_dump("paymentsUpdate");
                            RetailcrmHistory::paymentsUpdate($newOrder, $orderCrm['order'], $newHistoryPayments);
                        }
                    }

                    var_dump("delivery");
                    //delivery
                    if (array_key_exists('delivery', $order)) {
                        $itemUpdate = true;
                        //delete empty
                        if (!isset($orderCrm)) {
                            $orderCrm['order'] = $order;
                        }
                        if ($orderCrm) {
                            var_dump("deliveryUpdate");
                            RetailcrmHistory::deliveryUpdate($newOrder, $optionsDelivTypes, $orderCrm['order']);
                        }
                    }

                    if ($itemUpdate === true) {
                        var_dump("shipmentItemReset");
                        RetailcrmHistory::shipmentItemReset($newOrder);
                    }

                    if (isset($orderCrm)) {
                        unset($orderCrm); 
                    }
                    
                    $newOrder->setField('PRICE', $orderSumm);
                    var_dump($newOrder->save());
                    
                    // if ($registerNewUser === true) {
                        // var_dump("New order id: " . $newOrder->getId());
                        // $counter = 0;
                        // while ($counter < 20) {
                            // $testnewOrder = Bitrix\Sale\Order::load($newOrder->getId());
                            // var_dump("Second: $counter; Loaded order id: " . $testnewOrder->getId());
                            // die();
                            // $counter++;
                            // sleep(1);
                        // }
                    // }
                    
                    // if ($registerNewUser === true) {
                        // var_dump("STOP");
                        // die();
                    // }

                    if ($optionsOrderNumbers == 'Y' && isset($order['number'])) {
                        $searchFilter = array(
                            'filter' => array('ACCOUNT_NUMBER' => $order['number']),
                            'select' => array('ID'),
                        );
                        $searchOrder = \Bitrix\Sale\OrderTable::GetList($searchFilter)->fetch();
                        if (!empty($searchOrder)) {
                            if ($searchOrder['ID'] != $order['externalId']) {
                                RCrmActions::eventLog("ajax/retailcrm/createorder.php; id=$orderIdCrm", 'setField("ACCOUNT_NUMBER")', 'Error order load id=' . $order['externalId']) . '. Number ' . $order['number'] . ' already exists';
                            
                                continue;
                            }
                        }

                        $newOrder->setField('ACCOUNT_NUMBER', $order['number']);
                        var_dump($newOrder->save());
                    }

                    if (!empty($newHistoryPayments)) {
                        foreach ($newOrder->getPaymentCollection() as $orderPayment) {
                            if (array_key_exists($orderPayment->getField('XML_ID'), $newHistoryPayments)) {
                                $paymentExternalId = $orderPayment->getId();

                                if ($paymentExternalId) {
                                    $newHistoryPayments[$orderPayment->getField('XML_ID')]['externalId'] = $paymentExternalId;
                                    RCrmActions::apiMethod($client, 'paymentEditById', __METHOD__, $newHistoryPayments[$orderPayment->getField('XML_ID')]);
                                    \Bitrix\Sale\Internals\PaymentTable::update($paymentExternalId, array('XML_ID' => ''));
                                }  
                            }
                        }
                    }

                    if (!$order['externalId']) {
                        $params = array(array('id' => $order['id'], 'externalId' => $newOrder->getId()));
                        $test = RCrmActions::apiMethod($client, 'ordersFixExternalIds', __METHOD__, $params);
                        var_dump($params);
                        var_dump($test);
                        if($test == false){
                            $order['externalId'] = $newOrder->getId();
                        }
                    }

                    // if (function_exists('retailCrmAfterOrderSave')) {
                        // retailCrmAfterOrderSave($order);
                    // }
                    
                    if (function_exists('retailCrmAfterCustomerSave')) {
                        $customer = $order['customer'];
                        $customer['customFields'] = $order['customFields'];
                        $customer['dont_need_request_to_retailcrm'] = true;
                        retailCrmAfterCustomerSave($customer);
                    }
                    // var_dump($newOrder->save());
                    var_dump("Done");
                }
                
                unset($newOrder);
        }
    }
    
    
    function unreserveShipment($order)
    {
        $shipmentCollection = $order->getShipmentCollection();

        foreach ($shipmentCollection as $shipment) {
            if (!$shipment->isSystem()) {
                try {
                    $shipment->tryUnreserve();
                } catch (Main\ArgumentOutOfRangeException $ArgumentOutOfRangeException) {
                    RCrmActions::eventLog('RetailCrmHistory::unreserveShipment', '\Bitrix\Sale\Shipment::tryUnreserve()', $ArgumentOutOfRangeException->getMessage());

                    return false;
                } catch (Main\NotSupportedException $NotSupportedException) {
                    RCrmActions::eventLog('RetailCrmHistory::unreserveShipment', '\Bitrix\Sale\Shipment::tryUnreserve()', $NotSupportedException->getMessage());

                    return false;
                }
            }
        }
    }

    function setProp($obj, $value = '', $prop = '')
    {
        if (!isset($obj)) {
            return false;
        }
        if ($prop && $value) {
            $obj->setField($prop, $value);
        } elseif ($value && !$prop) {
            $obj->setValue($value);
        } elseif (!$value && !$prop) {
            $obj->delete();
        }

        return true;
    }
    
    function paymentsUpdate($order, $paymentsCrm, &$newHistoryPayments = array())
    {
        global $MODULE_ID;
        global $CRM_PAYMENT;
        global $CRM_PAYMENT_TYPES;
        
        $optionsPayTypes = array_flip(unserialize(COption::GetOptionString($MODULE_ID, $CRM_PAYMENT_TYPES, 0)));
        $optionsPayment = array_flip(unserialize(COption::GetOptionString($MODULE_ID, $CRM_PAYMENT, 0)));
        $allPaymentSystems = RCrmActions::PaymentList();
        foreach ($allPaymentSystems as $allPaymentSystem) {
            $arPaySysmems[$allPaymentSystem['ID']] = $allPaymentSystem['NAME'];
        }
        $paymentsList = array();
        $paymentColl = $order->getPaymentCollection();
        foreach ($paymentColl as $paymentData) {
            $data = $paymentData->getFields()->getValues();
            $paymentsList[$data['ID']] = $paymentData;
        }
        
        //data from crm
        $paySumm = 0;
        foreach ($paymentsCrm['payments'] as $paymentCrm) {
            if (isset($paymentCrm['externalId']) && !empty($paymentCrm['externalId'])) {
                //find the payment
                $nowPayment = $paymentsList[$paymentCrm['externalId']];
                //update data
                if ($nowPayment instanceof \Bitrix\Sale\Payment) {
                    $nowPayment->setField('SUM', $paymentCrm['amount']);
                    if ($optionsPayTypes[$paymentCrm['type']] != $nowPayment->getField('PAY_SYSTEM_ID')) {
                        $nowPayment->setField('PAY_SYSTEM_ID', $optionsPayTypes[$paymentCrm['type']]);
                        $nowPayment->setField('PAY_SYSTEM_NAME', $arPaySysmems[$optionsPayTypes[$paymentCrm['type']]]);
                    }
                    if (isset($optionsPayment[$paymentCrm['status']])) {
                        $nowPayment->setField('PAID', $optionsPayment[$paymentCrm['status']]);
                    }

                    unset($paymentsList[$paymentCrm['externalId']]);
                }
            } else {
                $newHistoryPayments[$paymentCrm['id']] = $paymentCrm;
                $newPayment = $paymentColl->createItem();
                $newPayment->setField('SUM', $paymentCrm['amount']);
                $newPayment->setField('PAY_SYSTEM_ID', $optionsPayTypes[$paymentCrm['type']]);
                $newPayment->setField('PAY_SYSTEM_NAME', $arPaySysmems[$optionsPayTypes[$paymentCrm['type']]]);
                $newPayment->setField('PAID', $optionsPayment[$paymentCrm['status']] ? $optionsPayment[$paymentCrm['status']] : 'N');
                $newPayment->setField('CURRENCY', $order->getCurrency());
                $newPayment->setField('IS_RETURN', 'N');
                $newPayment->setField('PRICE_COD', '0.00');
                $newPayment->setField('EXTERNAL_PAYMENT', 'N');
                $newPayment->setField('UPDATED_1C', 'N');
                $newPayment->setField('XML_ID', $paymentCrm['id']);

                $newPaymentId = $newPayment->getId();

                unset($paymentsList[$newPaymentId]);
            }

            if ($optionsPayment[$paymentCrm['status']] == 'Y') {
                $paySumm += $paymentCrm['amount'];
            }
        }
        foreach ($paymentsList as $payment) {
            if ($payment->isPaid()) {
                $payment->setPaid("N");
            }
            $payment->delete();
        }
        
        if ($paymentsCrm['totalSumm'] == $paySumm) {
            $order->setFieldNoDemand('PAYED', 'Y');
        } else {
            $order->setFieldNoDemand('PAYED', 'N');
        }
    }
    
    
    function deliveryUpdate(Bitrix\Sale\Order $order, $optionsDelivTypes, $orderCrm)
    {
        if (!$order instanceof Bitrix\Sale\Order) {
            return false;
        }

        if ($order->getId()) {
            $update = true;
        } else {
            $update = false;
        }

        $crmCode = isset($orderCrm['delivery']['code']) ? $orderCrm['delivery']['code'] : false;
        $noDeliveryId = \Bitrix\Sale\Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId();

        if ($crmCode === false || !isset($optionsDelivTypes[$crmCode])) {
            $deliveryId = $noDeliveryId;
        } else {
            $deliveryId = $optionsDelivTypes[$crmCode];

            if (isset($orderCrm['delivery']['service']['code'])) {
                $deliveryCode = \Bitrix\Sale\Delivery\Services\Manager::getCodeById($deliveryId);

                if ($deliveryCode) {
                    try {
                        $deliveryService = \Bitrix\Sale\Delivery\Services\Manager::getObjectByCode($deliveryCode . ':' . $orderCrm['delivery']['service']['code']);
                    } catch (Bitrix\Main\SystemException $systemException) {
                        RCrmActions::eventLog('RetailCrmHistory::deliveryEdit', '\Bitrix\Sale\Delivery\Services\Manager::getObjectByCode', $systemException->getMessage());
                    }

                    if (isset($deliveryService)) {
                        $deliveryId = $deliveryService->getId();
                    }
                }
            }
        }

        $delivery = \Bitrix\Sale\Delivery\Services\Manager::getObjectById($deliveryId);
        $shipmentColl = $order->getShipmentCollection();

        if ($delivery) {
            if (!$update) {
                    $shipment = $shipmentColl->createItem($delivery);
                    $shipment->setFields(array(
                        'BASE_PRICE_DELIVERY' => $orderCrm['delivery']['cost'],
                        'CURRENCY' => $order->getCurrency(),
                        'DELIVERY_NAME' => $delivery->getName(),
                        'CUSTOM_PRICE_DELIVERY' => 'Y'
                    ));
            } else {
                foreach ($shipmentColl as $shipment) {
                    if (!$shipment->isSystem()) {
                        $shipment->setFields(array(
                            'BASE_PRICE_DELIVERY' => $orderCrm['delivery']['cost'],
                            'CURRENCY' => $order->getCurrency(),
                            'DELIVERY_ID' => $deliveryId,
                            'DELIVERY_NAME' => $delivery->getName(),
                            'CUSTOM_PRICE_DELIVERY' => 'Y'
                        ));
                    }
                }
            }
        }
    }


    function shipmentItemReset($order)
    {
        $shipmentCollection = $order->getShipmentCollection();
        $basket = $order->getBasket();

        foreach ($shipmentCollection as $shipment) {
            if (!$shipment->isSystem()) {
                $reserved = false;

                if ($shipment->needReservation()) {
                    $reserved = true;
                }

                $shipmentItemColl = $shipment->getShipmentItemCollection();

                if ($reserved === true) {
                    $shipment->tryUnreserve();
                }

                try {
                    $shipmentItemColl->resetCollection($basket);

                    if ($reserved === true) {
                        $shipment->tryReserve();
                    }
                } catch (\Bitrix\Main\NotSupportedException $NotSupportedException) {
                    RCrmActions::eventLog('RetailCrmHistory::shipmentItemReset', '\Bitrix\Sale\ShipmentItemCollection::resetCollection()', $NotSupportedException->getMessage());

                    return false;
                }
            }
        }
    }
    
    function getExistsItem($basket, $moduleId, $productId)
    {
        foreach ($basket as $basketItem) {
            $itemExists = ($basketItem->getField('PRODUCT_ID') == $productId && $basketItem->getField('MODULE') == $moduleId);

            if ($itemExists) {
                return $basketItem;
            }
        }

        return false;
    }
    
    function getInfoElement($offerId)
    {
        $elementInfo = CIBlockElement::GetByID($offerId)->fetch();
        $url = CAllIBlock::ReplaceDetailUrl($elementInfo['DETAIL_PAGE_URL'], $elementInfo, false, 'E');
        $catalog = CCatalogProduct::GetByID($offerId);

        $info = array(
            'NAME' => $elementInfo['NAME'],
            'URL' => $url,
            'DIMENSIONS' => serialize(array(
                'WIDTH' => $catalog['WIDTH'],
                'HEIGHT' => $catalog['HEIGHT'],
                'LENGTH' => $catalog['LENGTH'],
            )),
            'WEIGHT' => $catalog['WEIGHT'],
            'XML_ID' => $elementInfo["XML_ID"],
            'IBLOCK_XML_ID' => $elementInfo["IBLOCK_EXTERNAL_ID"]
        );
        
        return $info;
    }