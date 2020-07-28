<?
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);

    $_SERVER["DOCUMENT_ROOT"] = realpath(__DIR__ . "/../../");
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");


    // Use classes
    use \Bitrix\Main\Loader;
    use \Bitrix\Main\Application;
    use \Bitrix\Sale;
    use \Bitrix\Sale\Cashbox\Internals\CashboxCheckTable;
    use \Bitrix\Highloadblock\HighloadBlockTable;

    // Include modules
    Loader::includeModule('sale');
    Loader::IncludeModule("catalog");
    Loader::IncludeModule("sms96ru.sms");

    // globals section
    global $DB, $APPLICATION;
    $arOrders = array();
    
    $hlblock = HighloadBlockTable::getList(['filter' => ['=NAME' => 'DPDTERMINALS']])->fetch();
    $hlClassName = (HighloadBlockTable::compileEntity($hlblock))->getDataClass();
    
    $arFilter = array(
        ">=DATE_INSERT" => date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), mktime(0, 0, 0, date("n"), date("j")-1, date("Y"))), // TODO
        "<DATE_INSERT" => date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), mktime(0, 0, 0, date("n"), date("j"), date("Y"))),
        // "!PAY_SYSTEM_ID" => array(2, 4, 6),
        "STATUS_ID" => "PP",
        "PAYED" => "N",
        "PERSON_TYPE_ID" => 1
    );
    $rsOrders = CSaleOrder::GetList(
        array("DATE_INSERT" => "DESC"),
        $arFilter
        // , false
        // , array("nTopCount" => 100)
    );

    while ($obOrder = $rsOrders->Fetch()) {


        $order = Sale\Order::load($obOrder['ID']);
        $shipmentCollection = $order->getShipmentCollection();
        foreach ($shipmentCollection as $shipment) {
            if (empty($trackNumber)) $trackNumber = $shipment->getField("TRACKING_NUMBER");
        }
        $DPD_TERMINAL = getPropertyByCode($order->getPropertyCollection(), "DPD_TERMINAL_CODE")->getValue();
        $TEXT = "Заказ №".$obOrder['ID']." (". $trackNumber .") готов к выдаче. Подробнее: https://www.dsklad.ru/pickup/" . $DPD_TERMINAL;
        $phone = str_ireplace('-', '', str_ireplace(' ', '', $order->getPropertyCollection()->getPhone()->getValue()));
        $is_send = (array)json_decode(getPropertyByCode($order->getPropertyCollection(), "F_PAYSMSSEND")->getValue()) ? : [];

        // var_dump("DEBUG");die(); // DEBUGING TODO
        if ((!isset($is_send['readyissue']) || $is_send['readyissue'] == 'N') && !empty($phone)) {

            $smsOb = new \Sms96ru\Sms\Sender();
    		$smsOb->eventName = "Payment aviso";
    		$response = $smsOb->sendSms($phone, $TEXT . $authToken, 0);

           if ($response && !isset($response->error)) {

                $paySmsSend = getPropertyByCode($order->getPropertyCollection(), "F_PAYSMSSEND");
                $paySmsSend->setValue(json_encode(array_merge($is_send, ['readyissue' => "Y"])));
                $order->save();

            } else {

                $paySmsSend = getPropertyByCode($order->getPropertyCollection(), "F_PAYSMSSEND");
                $paySmsSend->setValue(json_encode(array_merge($is_send, ['readyissue' => "N"])));
                $order->save();

                // Exception
                extra_log([
                    'entity_type' => 'dont_send_sms_to_user',
                    'entity_id' => time(),
                    'exception_type' => 'dont_send_sms_to_user',
                    'exception_entity' => 'sendSmsToUserByOrderWaiting',
                    "exception_text" => 'Пользователю не отправлена СМС по расписанию с данными по заказу. Заказ - ' . $obOrder['ID'] . ' телефон - '. $phone,
                    "mail_comment" => 'Пользователю не отправлена СМС по расписанию с данными по заказу. Заказ - ' . $obOrder['ID'] . ' телефон - '. $phone,
                ]);

            }

        }

    }

    function getPropertyByCode($propertyCollection, $code)  {
        foreach ($propertyCollection as $property) {
            if($property->getField('CODE') == $code) {
                return $property;
            }
        }
    }

?>
