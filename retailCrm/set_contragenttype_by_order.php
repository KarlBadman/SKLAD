<?php
	$hash = trim(strip_tags($_GET['hash']));
    $orderIdCrm = abs((int)$_GET['orderId']);
    $inn = trim(strip_tags($_REQUEST['inn']));
    
    if ('XLHbLjFXH2trQx' != $hash || 0 == $orderIdCrm || !$inn) return;

    


    require_once( $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php" );
    if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
            
            $api_by = 'id';
            $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
            $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
            $client = new \RetailCrm\ApiClient($api_host, $api_key);
            
            $response = $client->ordersGet($orderIdCrm, $api_by);
            $contragent = $response->order['contragent'];
            
            if(strlen($inn) == 10){
            	$contragent['contragentType'] = 'legal-entity';
            }

            if(strlen($inn) == 12){
            	$contragent['contragentType'] = 'enterpreneur';
            }

            $order = [
                'id' => $orderIdCrm,
                'contragent' => $contragent,
            ];
            
			/*file_put_contents(__DIR__.'/set_contragenttype_by_order.log', print_r(array($_GET, $order), true));*/
            
           $response = $client->ordersEdit($order, $api_by);
    