<?php

require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";
// require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
require_once __DIR__ . '/../local/php_interface/include/classes/dpd_service.class.php';

if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
	

	$api_by = 'id';
    $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
    $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
    $client = new \RetailCrm\ApiClient($api_host, $api_key);
    
    echo "<pre>";
    
    $filter = [
        'extendedStatus' => ['send-to-delivery'],
        'deliveryTypes' => ['self-delivery'],
        'sites' => ['dsklad-ru'],
    ];
    $page = 1;
    $limit = 100;
    $response = $client->ordersList($filter, $page, $limit);
    
    
    $dpd = new DPD_service_my();
    
    while ($response->isSuccessful() && count($response->orders) > 0) {
        $response = $client->ordersList($filter, $page, $limit);
        
        $count = 0;
        foreach ($response->orders as $order) {
            if ($order['externalId'] && !$order['customFields']['send_dpd_sms']) {
                $orderId = $order['externalId'];
                
                
                $res = $dpd->getStatesByClientOrder($orderId);
                
                if ($res && count($res['states']) > 0) {
                    
                    $newStates = [];
                    $terminalCode = '';
                    
                    if ($res['states'][0]) {
                        foreach ($res['states'] as $state) {
                            if ($state['transitionTime'] >= $tt) {
                                if ($state['transitionTime'] > $tt) {
                                    $tt = $state['transitionTime'];
                                    $newStates = [$state['newState']];
                                } else {
                                    $newStates[] = $state['newState'];
                                }
                                
                                if ($state['newState'] == 'OnTerminalDelivery')
                                    $terminalCode = $state['terminalCode'];
                            }
                        }
                    } else {
                        $state = $res['states'];
                        if ($state['newState'] == 'OnTerminalDelivery')
                            $terminalCode = $state['terminalCode'];
                        $tt = $state['transitionTime'];
                        $newStates = [$state['newState']];
                    }
                    
                    
                    
                    if (in_array("OnTerminalDelivery", $newStates)) {
                        if ($terminalCode && ($terminalCode == $order['customFields']['dpd_terminal_code']))
                        {
                            $statesLog[$orderId] = implode(',', $newStates);
                            
                            $order = [
                                'externalId' => $orderId,
                                'customFields' => [
                                    'send_dpd_sms' => true,
                                ],
                            ];
                            
                            // var_dump($res['states']);
                            // var_dump($order);
                            
                            $client->ordersEdit($order, 'externalId', 'dsklad-ru');
                        }
                    }

                }
            }
        }
        
        $page += 1;
    }
    
    // var_dump($statesLog);
    extra_log([
        "entity_type" => "retailcrmapi",
        "entity_id" => "120021",
        "exception_type" => "retailcrmapi_event",
        "exception_entity" => "retailcrmapi_sms_worker_dpd",
        "exception_text" => "[dsklad.ru] Отработка по SMS терминалам DPD" . print_r($statesLog, true),
        "mail_comment" => "[dsklad.ru] Отработка по SMS терминалам DPD" . print_r($statesLog, true)
    ]);