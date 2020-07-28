<?php

$hash = trim(strip_tags($_GET['hash']));
$orderIdCrm = abs((int)$_GET['orderId']);

if ('XLHbLjFXH2trQx' != $hash || 0 == $orderIdCrm) return;


require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';

ob_start();

// echo "<pre>";
    
    if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
	

	$api_by = 'id';
    $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
    $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
    $client = new \RetailCrm\ApiClient($api_host, $api_key);
    
    $response = $client->ordersGet($orderIdCrm, 'id');
    
    if (!$response->isSuccessful()) {
        var_dump($response);
        file_put_contents(__DIR__ . '/create_two_payments.log', "[" . date('Y-m-d H:i:s') . "]\n" . ob_get_contents() . "\n\n", FILE_APPEND);
        die();
    }
    
    $order = $response['order'];
    $payments = $order['payments'];

    
    if (count($payments) == 0) {
        if ($order['summ'] > 0) {
            $payment = [
                'amount' => $order['summ'],
                'order' => ['id' => $order['id']],
                'type' => 'bank-transfer',
            ];
            $response = $client->ordersPaymentCreate($payment);
            var_dump('Create');
            var_dump($payment);
            var_dump($response);
        }
    }
    
    if (count($payments) == 1) {
        $existPayment = $payments[array_keys($payments)[0]];
        $paymentId = $existPayment['id'];
        $payment = [
            'id' => $paymentId,
            'amount' => $order['summ'],
        ];
        $response = $client->ordersPaymentEdit($payment);
        var_dump('Edit');
        var_dump($payment);
        var_dump($response);
    }
    
    if ($order['delivery']['cost'] > 0) {
        $payment = [
            'amount' => $order['delivery']['cost'],
            'order' => ['id' => $order['id']],
            'type' => 'bank-transfer',
            'status' => 'not-paid',
            'comment' => 'Оплата доставки'
        ];
        $response = $client->ordersPaymentCreate($payment);
        var_dump('Create');
        var_dump($payment);
        var_dump($response);
    }
    
file_put_contents(__DIR__ . '/create_two_payments.log', "[" . date('Y-m-d H:i:s') . "]\n" . ob_get_contents() . "\n\n", FILE_APPEND);
    