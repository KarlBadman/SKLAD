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
        'extendedStatus' => ['pickpoint', 'service', 'nowait', 'callback', 'nevostrebovan', 'vozvrat', 'vernulsya', 'preorder-notify-client'],
        // 'externalIds' => ['14854'],
    );
    
    file_put_contents(__DIR__ . '/all-orders.log', "");
    
    
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
            // if ($i >= 3)
                // break;
            
            if (is_numeric($order['externalId'])) {
                file_put_contents(__DIR__ . '/all-orders.log', $order['externalId'] . " " . $order['status'] . "\n", FILE_APPEND);
                
                $orderB = Order::load($order['externalId']);
                if ($orderB) {
                    $currentStatus = $orderB->getField('STATUS_ID');
                    if ($currentStatus != $statuses[$order['status']]) {
                        // var_dump($orderB);
                        // var_dump($statuses[$order['status']]);
                        $orderB->setField('STATUS_ID', $statuses[$order['status']]);
                        $orderB->save();
                        // die();
                        $i++;
                    }
                }
                // var_dump($GLOBALS['RETAIL_CRM_HISTORY']);
                
                // var_dump($orderB);
                // die();
            }
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