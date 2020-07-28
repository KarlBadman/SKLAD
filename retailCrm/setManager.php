<?php
	define('COUNT_ORDER_MANAGER', 3);
	require_once( $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php" );
    if (!CModule::IncludeModule("intaro.retailcrm")) return 0;

    $api_by = 'id';
    $api_host = 'https://analitika-online.retailcrm.ru';//COption::GetOptionString("intaro.retailcrm", "api_host");
    $api_site = 'analitika-online-ru';
    $api_key = '2zI5qhS4XLijcjZg1g2W6BL82GGCL5tC';//COption::GetOptionString("intaro.retailcrm", "api_key");
    $client = new \RetailCrm\ApiClient($api_host, $api_key);



$response = $client->ordersList(['customFields' => ['status_manager' => 'no_set']], null, 100);
$orderNoManager = array_reverse($response->orders);


$params = ['isManager' => 1, 'active' => 1, 'online' => 1,  'status' => 'free' ];
	$response = $client->usersList($params);
	$users = $response->users;
	$idManagers = [];

	$tempsman = [];
	foreach ($users as $user) {
		$arIdManagers[] = $user['id'];
		$idManagers[$user['id']] = ['managerId' => $user['id'], 'countOrder' => 0];
	}



	$params = ['managers' => $arIdManagers, 'extendedStatus' => array('new')];
	$response  = $client->ordersList($params, null, 100);
	$orders = $response->orders;

	foreach ($orders as $order) {
		if(!isset($idManagers[$order['managerId']])) {
			$idManagers[$order['managerId']] = array('managerId' => $order['managerId'], 'countOrder' => 1);
		}	else {
			$idManagers[$order['managerId']]['countOrder']++;
		}
	}



	$resMan = [];
	foreach ($idManagers as $key => $value) {
		if($value['countOrder'] < COUNT_ORDER_MANAGER)
			$resMan[] = $value['managerId'];
	}



$ordersForEdit = [];
if(count($orderNoManager) >= count($resMan)){
	for ($i = 0; $i < count($resMan); $i++) {
		$params = [
			'id' => $orderNoManager[$i]['id'],
			'managerId' => $resMan[$i],
			'customFields' => ['status_manager' => 'set']
		];
		$response = $client->ordersEdit($params, $api_by, $api_site);
	}
}