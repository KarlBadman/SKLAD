<?php
    // var_dump($_SERVER['DOCUMENT_ROOT']);
    // die();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
    use Bitrix\Sale\Order;
    
    if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
    
    $api_by = 'id';
    $URL_RETAILCRM = COption::GetOptionString("intaro.retailcrm", "api_host");
    $KEYAPI_RETAILCRM = COption::GetOptionString("intaro.retailcrm", "api_key");
    
    $client = new \RetailCrm\ApiClient($URL_RETAILCRM, $KEYAPI_RETAILCRM);
    
    $GLOBALS['RETAIL_CRM_HISTORY'] = true; // чтобы изменения не выгружались в ритейл
            
    // Условие
    $filter = array(
        'sites' => ['dev-dsklad-ru'],
        'createdAtFrom' => date('Y-m-d', strtotime('yesterday')),
        'createdAtTo' => date('Y-m-d', strtotime('yesterday')),
    );
    
    echo "<pre>";
    
    $statuses = array_flip(unserialize(COption::GetOptionString('intaro.retailcrm', 'pay_statuses_arr', 0)));
    
    
    $page = 1;
    while (True) {
        $orders = loadOrdersFromRetailcrm($client, $filter, $page);
        
        if (count($orders) == 0)
            break;
        
        // Действие
        
        echo "$page ";
        $i = 0;
        
        foreach ($orders as $order) {
            $newOrder = [
                'id' => $order['id'],
                'status' => 'test'
            ];
            $response = $client->ordersEdit($newOrder, 'id');
            
            var_dump($newOrder);
            var_dump($response);
        }
        
        $page += 1;
    }
    var_dump($i);
    die();
    
    
    
            
    function loadOrdersFromRetailcrm($client, $filter = array(), $page = 1) {
        // $page = 1;
        $limit = 100;
        
        $result = array();
        
        // while ($page == 1 or count($res->orders) > 0) {
            $res = $client->ordersList($filter, $page, $limit);
            
            
            if (!$res->isSuccessful()) {
                var_dump($res);
                die();
            }
            
            foreach ($res->orders as $order) {
                $result[$order['id']] = $order;
            }
            
            // $page += 1;
            // if ($page >= 150)
                // break;
        // }
        
        return $result;
    }