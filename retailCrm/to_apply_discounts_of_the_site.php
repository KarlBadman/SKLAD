<?
	$hash = trim(strip_tags($_GET['hash']));
	$orderIdCrm = abs((int)$_GET['orderId']);
	
	if ('XLHbLjFXH2trQx' != $hash || 0 == $orderIdCrm) return;
	
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
	
	
	
	if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
	if (!CModule::IncludeModule("catalog")) return 0;
	

	$api_by = 'id';
    $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
    $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
    $client = new \RetailCrm\ApiClient($api_host, $api_key);
    
    $response = $client->ordersGet($orderIdCrm, $api_by);
	$orderCrm = $response->order;
	
    foreach ($orderCrm['items'] as $key => $item) {
		$optimalPrice = CCatalogProduct::GetOptimalPrice($item['offer']['externalId'], $item['quantity']);
		$orderCrm['items'][$key]['initialPrice'] = $optimalPrice['PRICE']['PRICE'];
        $orderCrm['items'][$key]['discount'] = 0;
        $orderCrm['items'][$key]['discountPercent'] = 0;
    }

	$client->ordersEdit($orderCrm, $api_by, $api_site);