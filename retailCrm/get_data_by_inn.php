<?php
    $hash = trim(strip_tags($_GET['hash']));
    $orderIdCrm = abs((int)$_GET['orderId']);
    $inn = trim(strip_tags($_REQUEST['inn']));
    
    if ('XLHbLjFXH2trQx' != $hash || 0 == $orderIdCrm || !$inn) return;

    require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';

    $url = ADDRESS_1C_SERVICES . '/UTDSklad/hs/getconsumeratinn/CustomerInfo/' . $inn;
    $username = 'webserv';
    $password = 'Nhjukjlbn4';

    $curlHandler = curl_init();
    curl_setopt($curlHandler, CURLOPT_URL, $url);
    curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curlHandler, CURLOPT_FAILONERROR, false);
    curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curlHandler, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curlHandler, CURLOPT_TIMEOUT, 30);
    curl_setopt($curlHandler, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($curlHandler, CURLOPT_USERPWD, $username . ":" . $password);  
    
    $responseBody = curl_exec($curlHandler);
    $statusCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
    $errno = curl_errno($curlHandler);
    $error = curl_error($curlHandler);
    
    
    if ($statusCode == 200) {
        
        $data = json_decode($responseBody, true);
        
        if ($data['ПравоваяФорма'] == 'Общества с ограниченной ответственностью') {
            $contragentType = 'legal-entity';
        }
        
        if ($contragentType) {
            if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
            
            $api_by = 'id';
            $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
            $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
            $client = new \RetailCrm\ApiClient($api_host, $api_key);
            
            $response = $client->ordersGet($orderIdCrm, $api_by);
            $contragent = $response->order['contragent'];
            $customer = $response->order['customer'];
            $contragent['INN'] = $inn;
            $contragent['contragentType'] = $contragentType;
            if ($data['КПП'])
                $contragent['KPP'] = $data['КПП'];
            if ($data['НаименованиеСокращенное'])
                $contragent['legalName'] = $data['НаименованиеСокращенное'];
            if ($data['ЮридическийАдрес']['Представление'])
                $contragent['legalAddress'] = $data['ЮридическийАдрес']['Представление'];
        
            $order = [
                'id' => $orderIdCrm,
                'contragent' => $contragent,
                'customFields' =>[
                    'changed_by_api' => true,
                ]
            ];
            
            $response = $client->ordersEdit($order, $api_by);
            
            $contragent = $customer['contragent'];
            $contragent['INN'] = $inn;
            $contragent['contragentType'] = $contragentType;
            if ($data['КПП'])
                $contragent['KPP'] = $data['КПП'];
            if ($data['НаименованиеСокращенное'])
                $contragent['legalName'] = $data['НаименованиеСокращенное'];
            if ($data['ЮридическийАдрес']['Представление'])
                $contragent['legalAddress'] = $data['ЮридическийАдрес']['Представление'];
            
            $newCustomer = [
                'id' => $customer['id'],
                'contragent' => $contragent
            ];
            
            $response = $client->customersEdit($newCustomer, $api_by);
        }

        echo "Done";

    }else{

        echo "Connection status is not 200 ok";

        extra_log(
            array(
                'entity_type' => 'get_data_by_inn',
                'entity_id' => strlen($hash),
                'exception_type' => 'get_data_by_inn_error',
                'exception_entity'=>'get_data_by_inn',
                "exception_text" => 'Недоступен сервис получения данных юрлиц по инн. Сервер ответил кодом '. $statusCode . ' ' . $responseBody,
                "mail_comment" => 'Недоступен сервис получения данных юрлиц по инн. Сервер ответил кодом '. $statusCode . ' ' . $responseBody
            )
        );
    }