<?php
    require_once __DIR__ . '/../local/libs/vendor/autoload.php';
    
    $_SERVER['DOCUMENT_ROOT'] = '/home/bitrix/www';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
    
    if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
    
    $URL_RETAILCRM = COption::GetOptionString("intaro.retailcrm", "api_host");
	$KEYAPI_RETAILCRM = COption::GetOptionString("intaro.retailcrm", "api_key");
	$client = new \RetailCrm\ApiClient($URL_RETAILCRM, $KEYAPI_RETAILCRM);
    
    
    $rsOrders = CSaleOrder::GetList(
        Array("ID"=>"DESC"),
        Array(
            'DATE_FROM' => \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime('yesterday')),
            'DATE_TO' => \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime('today')),
        )
    );
    
    $bitrixOrders = [];
    while ($order = $rsOrders->fetch()) {
        $bitrixOrders[] = $order;
    }
    
            
    // Условие
    $filter = array(
        'createdAtFrom' => date('Y-m-d', strtotime('yesterday')),
        'createdAtTo' => date('Y-m-d', strtotime('yesterday')),
        'sites' => ['dsklad-ru'],
    );
    
    
    $retailcrmOrders = [];
    
    $page = 1;
    while (True) {
        $orders = loadOrdersFromRetailcrm($client, $filter, $page);
        
        if (count($orders) == 0)
            break;
        
        $retailcrmOrders = array_merge($retailcrmOrders, $orders);
        
        $page += 1;
    }
    
    foreach ($retailcrmOrders as $order) {
        if ($order['externalId']) {
            $retailcrmOrders2[$order['externalId']] = $order;
        } else {
            $retailcrmOrders2['R' . $order['id']] = $order;
        }
    }
    
    $bitrixCount = count($bitrixOrders);
    $retailcrmCount = count($retailcrmOrders2);
    
    if ($bitrixCount != $retailcrmCount){
    
    $html = <<<MESSAGE
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
MESSAGE;
    $html = "Всего заказов в битриксе: <b>$bitrixCount</b><br>";
    $html .= "Всего заказов в retailCRM: <b>$retailcrmCount</b><br><br>";
    
    $html .= '<table border=1 cellpadding=5 cellspacing=0 style="text-align:center;">';
    $html .= '<tr><td>Заказ в битриксе</td><td>Заказ в retailCRM</td></tr>';
    foreach ($bitrixOrders as $order) {
        $orderId = $order['ID'];
        $orderLink = "https://www.dsklad.ru/bitrix/admin/sale_order_view.php?ID=$orderId&filter=Y&set_filter=Y&lang=ru";
        $anchor = "<a href=\"$orderLink\">$orderId</a>";
        $td = "<td>$anchor</td>";
        
        $html .= '<tr>';
        $html .= $td;
        
        if ($retailcrmOrders2[$orderId]) {
            $retailOrder = $retailcrmOrders2[$orderId];
            $retailOrderId = $retailOrder['id'];
            $retailOrderNumber = $retailOrder['number'];
            $retailOrderLink = "https://dsklad.retailcrm.ru/orders/$retailOrderId/edit";
            $retailAnchor = "<a href=\"$retailOrderLink\">$retailOrderNumber</a>";
            $td = "<td>$retailAnchor</td>";
            
            $html .= $td;
            unset($retailcrmOrders2[$orderId]);
        } else {
            $html .= '<td style="background-color:#f66"></td>';
        }
        $html .= '</tr>';
    }
    foreach ($retailcrmOrders2 as $retailOrder) {
        $html .= '<tr>';
        $html .= '<td style="background-color:#f66"></td>';
        $retailOrderId = $retailOrder['id'];
        $retailOrderNumber = $retailOrder['number'];
        $retailOrderLink = "https://dsklad.retailcrm.ru/orders/$retailOrderId/edit";
        $retailAnchor = "<a href='$retailOrderLink'>$retailOrderNumber</a>";
        $td = "<td>$retailAnchor</td>";
        
        $html .= $td;
    }
    $html .= "</table></body></html>\r\n";
    
    echo $html;
    
    $transport = new Swift_SmtpTransport('localhost', 25);
    
    $mailer = new Swift_Mailer($transport);

    $receiver = 'dev@ooott.ru';
    $message = (new Swift_Message('отчет по выгруженным заказам в retailCRM'))
        ->setFrom(['info@dsklad.ru' => 'info@dsklad.ru'])
        ->setTo([$receiver => $receiver])
        ->setBody($html, 'text/html');
        
    $result = $mailer->send($message, $error);
    }else{
        $result = true;
    }

    
    
            
    function loadOrdersFromRetailcrm($client, $filter = array(), $page = 1) {
        // $page = 1;
        $limit = 100;
        
        $result = array();
        
        $res = $client->ordersList($filter, $page, $limit);
        // $res = $client->customersList($filter, $page, $limit);
        
        
        if (!$res->isSuccessful()) {
            var_dump($res);
            die();
        }
        
        foreach ($res->orders as $order) {
        // foreach ($res->customers as $order) {
            $result[$order['id']] = $order;
        }
            
        
        return $result;
    }