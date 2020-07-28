<?php
    // var_dump($_SERVER['DOCUMENT_ROOT']);
    // die();
    $_SERVER['DOCUMENT_ROOT'] = '/home/bitrix/www';
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
        'sites' => ['dsklad-ru'],
        'extendedStatus' => ['complete'],
        'statusUpdatedAtFrom' => date('Y-m-d'),
        'statusUpdatedAtTo' => date('Y-m-d'),
    );
        
    
    echo "<pre>";
    
    $statuses = array_flip(unserialize(COption::GetOptionString('intaro.retailcrm', 'pay_statuses_arr', 0)));
    
    
    $page = 1;
    $allTotal = 0;
    while (True) {
        $orders = loadOrdersFromRetailcrm($client, $filter, $page);
        
        if (count($orders) == 0)
            break;
        
        // Действие
        
        echo "$page ";
        $i = 0;
        foreach ($orders as $order) {
            
            $allTotal += $order['totalSumm'];
            
        }
        
        $page += 1;
    }
    
    $customer = [];
    $customer['id'] = 64083;
    $customer['customFields']['today_revenue'] = $allTotal;
    $response = $client->customersEdit($customer, 'id');
    var_dump($customer);
    var_dump($response);
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