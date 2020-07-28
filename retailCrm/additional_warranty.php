<?php
    $hash = trim(strip_tags($_GET['hash']));
	$orderIdCrm = abs((int)$_GET['orderId']);
    $hash = 'XLHbLjFXH2trQx';
    $orderIdCrm = 33905;
	
	if ('XLHbLjFXH2trQx' != $hash || 0 == $orderIdCrm) return;
    
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
	
	
	
	if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
	

	$api_by = 'id';
    $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
    $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
    $client = new \RetailCrm\ApiClient($api_host, $api_key);
    
    // получаем группы товаров
    $tableGroupIds = [339];
    $chairGroupIds = [340];
    $setGroupIds = [338];
    
    $filter = ["sites" => ["dsklad-ru"]];
    $response = $client->storeProductsGroups($filter, 1, 100);
    foreach ($response->productGroup as $productGroup) {
        if (in_array($productGroup['parentId'], $tableGroupIds)) {
            $tableGroupIds[] = $productGroup['id'];
        }
        if (in_array($productGroup['parentId'], $chairGroupIds)) {
            $chairGroupIds[] = $productGroup['id'];
        }
        if (in_array($productGroup['parentId'], $setGroupIds)) {
            $setGroupIds[] = $productGroup['id'];
        }
    }
    
    
    // получаем услуги
    $filter = ['groups' => ['369']];
    $response = $client->storeProducts($filter, 1, 100);
    foreach ($response->products as $product) {
        $services[$product['offers'][0]['externalId']] = $product;
    }
    echo "<pre>";

    // получаем заказ из retailcrm
    $response = $client->ordersGet($orderIdCrm, $api_by);
    $newOrder = [];
    $warrantyId = 19408;
    if ($response->isSuccessful()) {
        $order = $response['order'];
        $newOrder['id'] = $order['id'];
        $newOrder['items'] = [];
        echo "<pre>";
        foreach ($order['items'] as $item) {
            if ($item['offer']['externalId'] == $warrantyId) {
                $warrantyItem = $item;
            } else {
                if (!array_key_exists($item['offer']['externalId'], $services)) {
                    $newOrder['items'][] = $item;
                }
            }
        }
        
        // if ($order['customFields']['additional_warranty'])
        {
            $newOrderItemIds = array_map( function($item) { return $item['offer']['id']; }, $newOrder['items']);
            $filter = ['offerIds' => $newOrderItemIds];
            $response = $client->storeProducts($filter, 1, 100);
            $tables = [];
            $chairs = [];
            $sets = [];
            foreach ($response->products as $product) {
                foreach ($product['groups'] as $group) {
                    if (in_array($group['id'], $tableGroupIds)) {
                        $tables[$product['id']] = $product;
                    }
                    if (in_array($group['id'], $chairGroupIds)) {
                        $chairs[$product['id']] = $product;
                    }
                    if (in_array($group['id'], $setGroupIds)) {
                        $sets[$product['id']] = $product;
                    }
                }
            }
            
            
            $tablesCount = 0;
            $chairsCount = 0;
            $setsCount = 0;
            foreach ($newOrder['items'] as $item) {
                $offerId = $item['offer']['id'];

                foreach ($chairs as $productId => $product) {
                    foreach ($product['offers'] as $offer) {
                        if ($offerId == $offer['id']) {
                            $chairsCount += $item['quantity'];
                            continue 3;
                        }
                    }
                }
                
                foreach ($tables as $productId => $product) {
                    foreach ($product['offers'] as $offer) {
                        if ($offerId == $offer['id']) {
                            $tablesCount += $item['quantity'];
                            continue 3;
                        }
                    }
                }
                
                foreach ($sets as $productId => $product) {
                    foreach ($product['offers'] as $offer) {
                        if ($offerId == $offer['id']) {
                            $setsCount += $item['quantity'];
                            continue 3;
                        }
                    }
                }
            }
            // $newOrder['items'][] = $warrantyItem;
            var_dump("tables count: $tablesCount");
            var_dump("chairs count: $chairsCount");
            var_dump("sets count: $setsCount");
            $warrantyCost = $setsCount * 800 + $tablesCount * 400 + $chairsCount * 100;
            var_dump("warranty: $warrantyCost");
            
            if (!$warrantyItem) {
                $warrantyItem = [
                    'quantity' => '1',
                    'offer' => [
                        'externalId' => $warrantyId
                    ],
                ];
            }
            
            if ($warrantyCost > 0) {
                $warrantyItem['initialPrice'] = $warrantyCost;
                $newOrder['items'][] = $warrantyItem;
            }
        }
        
        var_dump($newOrder);
        
        if (compare_items($newOrder['items'], $order['items']) == false) {
            $response = $client->ordersEdit($newOrder, 'id');
            var_dump($response);
        }
    }


    
function compare_items($items1, $items2) {
    usort($items1, function($item1, $item2) { return $item1['id'] < $item2['id']; });
    usort($items2, function($item1, $item2) { return $item1['id'] < $item2['id']; });
    
    $str1 = '';
    foreach ($items1 as $item) {
        $keys = array_keys($item);
        sort($keys);
        foreach ($keys as $key) {
            $value = $item[$key];
            $str1 .= "$key:$value;";
        }
    }
    $str2 = '';
    foreach ($items2 as $item) {
        $keys = array_keys($item);
        sort($keys);
        foreach ($keys as $key) {
            $value = $item[$key];
            $str2 .= "$key:$value;";
        }
    }
    
    return $str1 == $str2;
}
