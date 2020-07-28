<?php
function Makcrx_OnSalePayOrder_SendTransactionId($orderId, $payed) {
    $orderData = CSaleOrder::GetByID($orderId);

    if ($orderData["PAY_SYSTEM_ID"] == 2) { // Yandex Kassa

        // инициализация модуля загрузки в RetailCRM
        if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
        $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
        $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
        $api_site = "dsklad-ru";
        $client = new \RetailCrm\ApiClient($api_host, $api_key, $api_site);

        // отправляем ID транзакции
        $desc = explode(';', $orderData["PS_STATUS_DESCRIPTION"]);
        $transaction = explode(':', $desc[0]);
        if (count($transaction) > 1) {
            $transactionId = trim($transaction[1]);
            $datePayed = $orderData["DATE_PAYED"];

            $arOrder = array(
                "externalId" => $orderId,
                "customFields" => array(
                    "ntrans" => $transactionId
                ),
            );

            $client->ordersEdit($arOrder);

            // получаем инфу о платежах
            $response = $client->ordersGet($orderId);
            if ($response['order']) {
                $payments = $response['order']['payments'];
                if ($payments) {
                    foreach ($payments as $id => $payment) {
                        if ($payment['type'] == 'bank-card') {
                            $paymentId = $payment['id'];
                            break;
                        }
                    }
                }
            }

            // отправляем время оплаты в платёж $paymentId
            if ($paymentId && $datePayed) {
                $p = array(
                    'id' => $paymentId,
                    'paidAt' => $datePayed,
                );
                $client->ordersPaymentEdit($p);
            }
        }
    }
}