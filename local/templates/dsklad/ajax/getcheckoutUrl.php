<?
    include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
    
    use \Bitrix\Main\Application,
    \Bitrix\Main\Web\Uri,
    \Bitrix\Main\Web\HttpClient, 
    \Bitrix\Sale\OrderBase, 
    \Bitrix\Sale\Order;
    
    $answer['checkoutUrl'] = false;
    $options = array(
        "redirect" => true,
        "waitResponse" => true,
        "compress" => false,
        "disableSslVerification" => false,
    );
    
    if (!empty($_REQUEST['orderID'])) {
        
        $orderID = intVal(htmlspecialchars(trim($_REQUEST['orderID'])));
        $order = Order::load($orderID);
        if (!$order) {
            echo json_encode($answer);
            return false;
        }
        
        $paymentCollection = $order->loadPaymentCollection();
        foreach ($paymentCollection as $payment) {
            $payments[] = $payment->getId();
        }
        
        $paysystemID = is_array($order->getPaymentSystemId()) && count($order->getPaymentSystemId()) == 1 ? array_shift($order->getPaymentSystemId()) : $order->getPaymentSystemId()[0];
        $fUser = Bitrix\Sale\Fuser::getId();
        $connection = Application::getConnection();
        $sqlHelper = $connection->getSqlHelper();
        $sql = "SELECT PROVIDER_VALUE FROM b_sale_bizval WHERE (CODE_KEY = 'YANDEX_CHECKOUT_SHOP_ID' AND CONSUMER_KEY = 'PAYSYSTEM_" . $paysystemID . "') LIMIT 1";
        $getShopID = $connection->query($sql)->fetch();
        $sql = "SELECT PROVIDER_VALUE FROM b_sale_bizval WHERE (CODE_KEY = 'YANDEX_CHECKOUT_SECRET_KEY' AND CONSUMER_KEY = 'PAYSYSTEM_" . $paysystemID . "') LIMIT 1";
        $getShopSecret = $connection->query($sql)->fetch();
        
        $user = $getShopID['PROVIDER_VALUE'] ? : "";
        $pass = $getShopSecret['PROVIDER_VALUE'] ? : "";
        
        if (!empty($user) && !empty($pass)) {
            
            $httpClient = new HttpClient($options);
            $httpClient->setHeader("Idempotence-Key", md5(mt_rand()), true);
            $httpClient->setHeader("Content-Type", "application/json", true);
            $httpClient->setAuthorization($user, $pass);
            
            $httpClient->query("POST", "https://payment.yandex.net/api/v3/payments", json_encode([
                "amount" => [
                    "value" => $order->getPrice(),
                    "currency" => "RUB"
                ],
                "payment_method_data" => [
                    "type" => "installments"
                ],
                "confirmation" => [
                    "type" => "redirect",
                    "return_url" => "https://www.dsklad.ru"
                ],
                "description" => "Оплата №" . $payments[0] . " заказа №" . $orderID . " для " . $fUser,
                "capture" => true,
            ]));
            
            $result = $httpClient->getResult();
            try {
                $result = json_decode($result);
            } catch (Exception $e) {
                $answer["errorMessage"] = $e->getMessage();
            }
        } else {
            echo json_encode($answer);
            return false;
        }
        
        $answer['checkoutUrl'] = $result->confirmation->confirmation_url ? : "";
        
    }
    
    echo json_encode($answer);
    
?>