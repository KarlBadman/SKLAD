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
        'extendedStatus' => ['test'],
        // 'externalIds' => ['14854'],
    );

    // echo "<pre>";

    $statuses = array_flip(unserialize(COption::GetOptionString('intaro.retailcrm', 'pay_statuses_arr', 0)));


    $page = 1;
    while (True) {
        $orders = loadOrdersFromRetailcrm($client, $filter, $page);

        if (count($orders) == 0)
            break;

        // Действие

        echo "$page ";
        $i = 0;

        $orders = array_keys($orders);
        if (count($orders) <= 1)
            break;

        sort($orders);
        $resultOrder = array_shift($orders);

        // var_dump($resultOrder);
        // var_dump($orders);

        // объединяем заказы
        $orders_array = array_chunk($orders, 50);
        foreach ($orders_array as $orders) {
            foreach ($orders as $order) {

                try {
                    $res = $client->ordersCombine(['id' => $order], ['id' => $resultOrder]);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                if (!$res->isSuccessful()) {
                    // var_dump($res);
                    // die();
                }
                // var_dump($res);
            }
        }

        $page += 1;
    }
    // var_dump($i);
    // die();

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
