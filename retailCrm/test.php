<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
    
    
    $GLOBALS['RETAIL_CRM_HISTORY'] = true; // чтобы изменения не выгружались в ритейл
    if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
	

	$api_by = 'id';
    $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
    $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
    $client = new \RetailCrm\ApiClient($api_host, $api_key);
    
    echo "<pre>";

    $filter = [
        'extendedStatus' => ['preorder-wait'],
        'createdAtTo' => '2018-06-30'
    ];
    $page = 1;
    $limit = 100;
    while (is_null($orders) || count($orders) > 0) {
        $response = $client->ordersList($filter, $page, $limit);
        $orders = $response->orders;
        
        foreach ($orders as $order) {
            $newOrder['id'] = $order['id'];
            $newOrder['items'] = $order['items'];
            
            foreach ($newOrder['items'] as $i => $item) {
                $newOrder['items'][$i]['vatRate'] = 18;
            }
            
            $response = $client->ordersEdit($newOrder, 'id');
            
            // var_dump($response);
            // die();
        }
        
        $page += 1;
    }
    
    die();