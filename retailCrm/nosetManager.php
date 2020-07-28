<?php
    $hash = trim(strip_tags($_GET['hash']));
    $orderIdCrm = abs((int)$_GET['orderId']);

    $managerId = 0;
    define('COUNT_ORDER_MANAGER', 2);

    if ('XLHbLjFXH2trQx' != $hash || 0 == $orderIdCrm) return;




    require_once( $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php" );
    if (!CModule::IncludeModule("intaro.retailcrm")) return 0;

    $api_by = 'id';
    $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
    $api_site = 'dsklad-ru';
    $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
    $client = new \RetailCrm\ApiClient($api_host, $api_key);

    $params = ['isManager' => 1, 'active' => 1, 'online' => 1,  'status' => 'free' ];
	$response = $client->usersList($params);
	$users = $response->users;
	$idManagers = [];
	foreach ($users as $user) {
		$idManagers[] = $user['id'];
	}

	$params = ['managers' => $idManagers, 'extendedStatus' => array('new')];
	$response  = $client->ordersList($params, 1, 100);
	$orders = $response->orders;

	$idManagers = [];
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


	//перемещиваем менеджеров
	shuffle($resMan);
	$managerId = intVal($resMan[0]);



    if($managerId){
    	$params = array(
            'id' => $orderIdCrm,
            'managerId' => $managerId
        );
    } else {
    	 $params = array(
            'id' => $orderIdCrm,
            'customFields' => array('manager' => time())
        );
    }

    $response = $client->ordersEdit($params, $api_by, $api_site);
    file_put_contents(__DIR__.'/nosetManager.log', print_r(array($params, $response, $_GET, $order), true));