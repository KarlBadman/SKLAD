<?php
    $hash = trim(strip_tags($_GET['hash']));
	$orderIdCrm = abs((int)$_GET['orderId']);
    // $orderIdCrm = 25165;
	
	if ('XLHbLjFXH2trQx' != $hash || 0 == $orderIdCrm) return;
    
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
	
	
	
	if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
	

	$api_by = 'id';
    $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
    $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
    $client = new \RetailCrm\ApiClient($api_host, $api_key);

    // получаем заказ из retailcrm
    $response = $client->ordersGet($orderIdCrm, $api_by);
    $newOrder = [];
    if ($response->isSuccessful()) {
        $order = $response['order'];
        $newOrder['id'] = $order['id'];
        // var_dump($order);
        
        // добавляем 18% к товарам
        $newItems = [];
        foreach ($order['items'] as $item) {
            $item['vatRate'] = '18.00';
            $item['initialPrice'] = $item['initialPrice'] * 1.18;
            $newItems[] = $item;
        }
        $newOrder['items'] = $newItems;
        
        // добавляем 18% к доставке
        $newOrder['delivery']['vatRate'] = '18.00';
        $newOrder['delivery']['cost'] = $order['delivery']['cost'] * 1.18;
        
        // отправляем новые данные в retailcrm
        // var_dump($newOrder);
        $response = $client->ordersEdit($newOrder, $api_by);
        
        // var_dump($response);
        
    }