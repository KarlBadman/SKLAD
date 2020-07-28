<?php
    $_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/..';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';

    use \Bitrix\Sale\Cashbox\Internals\CashboxCheckTable;
    use \Bitrix\Main\Mail\Event;

    if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
	

	$api_by = 'id';
    $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
    $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
    $client = new \RetailCrm\ApiClient($api_host, $api_key);
    
    
    $filter = array(
        'sites' => ['dsklad-ru'],
        'extendedStatus' => ['paid'],
        'paymentTypes' => ['bank-card'],
    );
    $limit = 100;
    $ordersExclude = array('26090');
    
    $response = $client->ordersList($filter, 1, $limit);
    
    file_put_contents(__DIR__ . '/logs/orders_without_check.log', "Номера заказов без чека:\n");
    while (count($response->orders) > 0) {
        $pagination = $response->pagination;
        $orders = $response->orders;

        $ordersWithoutBill = '';
        foreach ($orders as $order) {
            if (is_numeric($order['number']) && !in_array($order['number'], $ordersExclude)) {
                $check = CashboxCheckTable::getList(array('filter' => array('ORDER_ID' => $order['number'])));
                if (!$check->fetch()) {
                    file_put_contents(__DIR__ . '/logs/orders_without_check.log', $order['number'] . "\n", FILE_APPEND);
                    $ordersWithoutBill .= $order['number'] . "\n";
                }
            }
        }

        // send email
        if (!empty($ordersWithoutBill)) {
            $arFields['COMMENT'] = "Номера заказов без чека:\n" . $ordersWithoutBill;
            $arMailFields = array(
                'EVENT_NAME' => 'ORDERS_WITHOUT_BILL',
                'LID' => "s1",
                'C_FIELDS' => $arFields
            );
            Event::send($arMailFields);
        }

        $response = $client->ordersList($filter, $pagination['currentPage'] + 1, $limit);
        if (!$response->isSuccessful())
            break;
    }
    
    die('Success');