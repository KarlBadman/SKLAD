<?

    // Get payed ordders list
    if (empty($_SERVER["DOCUMENT_ROOT"])) $_SERVER['DOCUMENT_ROOT'] = __DIR__."/../../";
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

    set_time_limit(60);
    @ini_set('memory_limit', '2048M');
    @ini_set('output_buffering', 'off');

    // Use classes
    use \Bitrix\Main\Loader;
    use \Bitrix\Main\Application;
    use \Bitrix\Sale;
    use \Bitrix\Sale\Cashbox\Internals\CashboxCheckTable;

    // Include modules
    Loader::includeModule('sale');
    Loader::IncludeModule("catalog");

    // globals section
    global $DB, $APPLICATION;
    $arOrders = array();

    // REQUEST
    $REQUEST = array(
        'NOTPAYED' => Application::getInstance()->getContext()->getRequest()->getQuery('NOTPAYED') == 'Y' ? 'Y' : 'N',
        'NOTPAYEDWITHOUTCASHBOX' => Application::getInstance()->getContext()->getRequest()->getQuery('NOTPAYEDWITHOUTCASHBOX') == 'Y' ? 'Y' : 'N',
        'PAYEDWITHOUTCASHBOX' => Application::getInstance()->getContext()->getRequest()->getQuery('PAYEDWITHOUTCASHBOX') == 'Y' ? 'Y' : 'N',
    );

    $arFilter = array(
        ">=DATE_INSERT" => date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), mktime(0, 0, 0, date("n"), date("j") -1, date("Y"))),
        "<DATE_INSERT" => date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), mktime(0, 0, 0, date("n"), date("j"), date("Y"))),
        "!PAY_SYSTEM_ID" => array(2, 4, 6)
    );
    $arFilter["PAYED"] = (($REQUEST['NOTPAYED'] == "Y" || $REQUEST['NOTPAYEDWITHOUTCASHBOX'] == "Y") && $REQUEST['PAYEDWITHOUTCASHBOX'] != "Y" ? "N" : "Y");
    $rsOrders = CSaleOrder::GetList(
        array("DATE_INSERT" => "DESC"),
        $arFilter
        // , false
        // , array("nTopCount" => 100)
    );
    while ($obOrder = $rsOrders->Fetch()) {
        $order = Sale\Order::load($obOrder['ID']); $cashboxAvailabel = false;
        $arPayments = array(); $payments = $order->getPaymentCollection();
        foreach ($payments as $payment) {
            $cashbox = getCheck($payment->getId());
            if ($REQUEST['NOTPAYEDWITHOUTCASHBOX'] == "Y" || $REQUEST['PAYEDWITHOUTCASHBOX'] == "Y") {
                $cashboxAvailabel = empty($cashbox) && !$cashboxAvailabel ? true : false;
            } else {
                $cashboxAvailabel = !empty($cashbox) && is_array($cashbox) && !$cashboxAvailabel ? true : false;
            }
            $arPayments[] = array(
                "PAYMENT_ID" => $payment->getId(),
                "PAY_SYSTEM_PRICE" => $payment->getSum(),
                "PAY_SYSTEM_ID" => $payment->getPaymentSystemId(),
                "PAY_SYSTEM_NAME" => $payment->getPaymentSystemName(),
                "PAY_SYSTEM_CURRENCY" => $obOrder['CURRENCY'],
                "PAY_PAYED" => $payment->isPaid() ? "Y" : "N",
                "PAY_RETURNED" => $payment->isReturn() ? "Y" : "N",
                "PAY_SUMM" => $payment->getSum(),
                'CASHBOXES' => $cashbox
            );
        }

        if ($cashboxAvailabel)
            $arOrders[$obOrder['ID']] = array(
                'ID' => $obOrder['ID'],
                'DATE_INSERT' => $obOrder['DATE_INSERT'],
                'DATE_UPDATE' => $obOrder['DATE_UPDATE'],
                'PAYED' => $obOrder['PAYED'],
                'PRICE' => $obOrder['PRICE'],
                'PAYMENTS' => $arPayments,
                'CURRENCY' => $obOrder['CURRENCY'],
            );
    }

    function getCheck($payment_id) {
        global $DB; $arResult = [];
        $user_table = CashboxCheckTable::getTableName();
        $sqlQuery = "SELECT {$user_table}.* FROM {$user_table} WHERE {$user_table}.PAYMENT_ID={$payment_id} AND {$user_table}.STATUS='Y'";
        $queryResult = $DB->query($sqlQuery, FALSE);
        while ($arField = $queryResult->fetch()) {
            $arField['LINK_PARAMS'] = unserialize($arField['LINK_PARAMS']);
            $arResult[] = $arField;
        }

        return $arResult;
    }

    // var_dump($arOrders);die();
    echo json_encode($arOrders);

?>
