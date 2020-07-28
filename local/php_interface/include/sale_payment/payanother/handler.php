<?

    namespace Sale\Handlers\PaySystem;

    use YandexCheckout\Client;
    use Bitrix\Main\Config;
    use Bitrix\Main\Error;
    use Bitrix\Main\Localization\Loc;
    use Bitrix\Main\Request;
    use Bitrix\Main\Result;
    use Bitrix\Main\Text\Encoding;
    use Bitrix\Main\Type\DateTime;
    use Bitrix\Main\Web\HttpClient;
    use Bitrix\Sale\Order;
    use Bitrix\Main;
    use Bitrix\Sale\PaySystem;
    use Bitrix\Sale\Payment;
    use Bitrix\Main\Mail\Event;
    use Bitrix\Sale\PriceMaths;

    Loc::loadMessages(__FILE__);

    class PayAnotherHandler extends PaySystem\ServiceHandler {

        const BX_HADLER_CODE = "YANDEX_CHECKOUT";

        const CMS_NAME = "api_1c-bitrix";

        public $paySystemResult;

        public $paymentData;

        public function initiatePay(Payment $payment, Request $request = null) {

            if ($request === null) {
			    $request = Main\Context::getCurrent()->getRequest();
		    }

		    \Bitrix\Main\Loader::includeModule('sms96ru.sms');
		    $this->paySystemResult = new PaySystem\ServiceResult();

		    $this->config = [
	            "CHANGE_STATUS_PAY" => $this->getBusinessValue($payment, 'CHANGE_STATUS_PAY'),
	            "YANDEX_SHOP_ID" => $this->getBusinessValue($payment, 'YANDEX_SHOP_ID'),
	            "YANDEX_SHOP_SECRET" => $this->getBusinessValue($payment, 'YANDEX_SHOP_SECRET'),
	            "YANDEX_SHOP_BACK_URL" => $this->getBusinessValue($payment, 'YANDEX_SHOP_BACK_URL'),
	            "YANDEX_SHOP_TRANSACTION_DESCRIPTION" => $this->getBusinessValue($payment, 'YANDEX_SHOP_TRANSACTION_DESCRIPTION'),
	            "YANDEX_ORDER_PROPERTY_PHONE" => $this->getBusinessValue($payment, 'YANDEX_ORDER_PROPERTY_PHONE'),
                "YANDEX_ORDER_PHONE_SEND_TEXT" => $this->getBusinessValue($payment, 'YANDEX_ORDER_PHONE_SEND_TEXT'),
	            "YANDEX_ORDER_PROPERTY_EMAIL" => $this->getBusinessValue($payment, 'YANDEX_ORDER_PROPERTY_EMAIL'),
	            "YANDEX_ORDER_EMAIL_EVENT_TYPE" => $this->getBusinessValue($payment, 'YANDEX_ORDER_EMAIL_EVENT_TYPE')
            ];

            $this->getOrderObjectByPaymentItem($payment);
            $this->getPropertiesByOrderData();

            $this->config['YANDEX_ORDER_PHONE_SEND_TEXT'] = $this->getDescriptionString($this->config['YANDEX_ORDER_PHONE_SEND_TEXT']);

            try {

                $response = $this->createYandexPaymentRequest($payment, $request);

            } catch (Exception $e) {

                $this->paySystemResult->addError(new Error(Loc::getMessage('SALE_YANDEX_HANDLER_ERROR_CREATE_PAYMENT_REQUEST') . " " . $e->getMessage()));

            }

            if (!empty($response->getId())) {

                $paymentData['TRANSACTION_DATA'] = [
                    "STATUS" => true,
                    "TRANSACTION_ID" => $response->getId(),
                    "TRANSACTION_STATUS" => $response->getStatus(),
                    "TRANSACTION_AMOUNT" => [
                        "CURRENCY" => $response->getAmount()->getCurrency(),
                        "VALUE" => $response->getAmount()->getValue(),
                    ],
                    "TRANSACTION_DESCRIPTION" => $response->getDescription(),
                    "TRANSACTION_TYPE" => $response->getPaymentMethod()->getType(),
                    "TRANSACTION_METADATA" => $response->getMetadata()->jsonSerialize(),
                    "CONFIRMATION_URL" => $response->getConfirmation()->getConfirmationUrl(),
                ];

                $paymentData['SMS_DATA'] = [
                    "SEND_STATUS" => $this->sendPaymentData2SMS($paymentData['TRANSACTION_DATA']),
                    "PHONE" => $this->orderPhone2SendTransactionData,
                    "PHONE_PROPERTY" => $this->config['YANDEX_ORDER_PROPERTY_PHONE'],
                    "SEND_TEXT" => $this->getSmsText2Send($this->config['YANDEX_ORDER_PHONE_SEND_TEXT'], $paymentData['TRANSACTION_DATA'])
                ];

                $paymentData['EMAIL_DATA'] = [
                    "STATUS_SEND" => $this->sendPaymentData2Email($paymentData['TRANSACTION_DATA']),
                    "EMAIL" => $this->orderEmail2SendTransactionData,
                    "EMAIL_PROPERTY" => $this->config['YANDEX_ORDER_PROPERTY_EMAIL'],
                    "EMAIL_EVENT_TYPE" => $this->config['YANDEX_ORDER_EMAIL_EVENT_TYPE']
                ];

            } else {

                $paymentData = [
                    "STATUS" => false,
                    "ERROR_MSG" => Loc::getMessage('SALE_YANDEX_HANDLER_ERROR_CREATE_PAYMENT_REQUEST')
                ];

            }

            if ($request->getPost("IS_JSON_REQUEST") == 'Y') {

                $this->json_response($paymentData);

            }

            $this->isSendedSet($paymentData);
            $this->setPaymentData($paymentData);
            return $this->showTemplate($payment, 'template');

        }

        public function createYandexPaymentRequest (Payment $payment, Request $request = null) {

            $this->client = new Client();
            $this->client->setAuth($this->config['YANDEX_SHOP_ID'], $this->config['YANDEX_SHOP_SECRET']);

            $requestArray = [
	            "amount" => [
	               "value" => $payment->getSum(),
	                "currency" => array_shift($this->getCurrencyList()),
                ],
                "payment_method_data" => [
                    "type" => "bank_card",
                ],
                'capture' => true, // VERY INPORTANT
                "confirmation" => [
                    "type" => "redirect",
                    "return_url" => $this->config['YANDEX_SHOP_BACK_URL'],
                ],
                "description" => $this->getDescriptionString($this->config['YANDEX_SHOP_TRANSACTION_DESCRIPTION']),
                "metadata" => [
    				"BX_PAYMENT_NUMBER" => $payment->getId(),
    				"BX_PAYSYSTEM_CODE" => $this->service->getField('ID'),
    				"BX_HANDLER" => self::BX_HADLER_CODE,
    				"cms_name" => self::CMS_NAME,
    			]
            ];

            PaySystem\Logger::addDebugInfo('Yandex.Checkout: request data: ' . $requestArray);
	        return $this->client->createPayment($requestArray, $this->getIdempotenceKey());

        }

        private function isSendedSet ($data = []) {

            if (!$this->order || $data) {
                return false;
            }

            if ($data['SMS_DATA']['SEND_STATUS'] /* == 'SUCCESS' */) { // TODO CHECK ON SUCCESS SEND STATUS

            }

            if ($data['EMAIL_DATA']['SEND_STATUS'] /* == 'SUCCESS' */) { // TODO CHECK ON SUCCESS SEND STATUS

            }
        }

        private function sendPaymentData2SMS ($data = []) {

            if ($this->config['YANDEX_ORDER_PROPERTY_PHONE'] && $data['CONFIRMATION_URL'] && !$this->smsOther) {
                $smsOb = new \Sms96ru\Sms\Sender();
    			$smsOb->eventName = "Payment aviso";
    			$response = $smsOb->sendSms($this->orderPhone2SendTransactionData, $this->getSmsText2Send($this->config['YANDEX_ORDER_PHONE_SEND_TEXT'], $data), 0);

                $this->setPropertySms();

                return $response->id ? "SUCCESS" : "FAIL";

            } else {

                return "FAIL";

            }

        }

        private function getSmsText2Send ($text = "", $params = []) {

            return $text . " " . ($params['CONFIRMATION_URL'] ? : "Sorry confirm url error, try later");

        }

        private function sendPaymentData2Email ($data = []) {

            if (
                $this->config['YANDEX_ORDER_EMAIL_EVENT_TYPE']
                && $this->config['YANDEX_ORDER_PROPERTY_EMAIL']
                && $data['CONFIRMATION_URL']) {

                return Event::send([
                    "EVENT_NAME" => $this->config['YANDEX_ORDER_EMAIL_EVENT_TYPE'],
                    "LID" => "s1",
                    "C_FIELDS" => [
                        "CONFIRMATION_URL" => $data['CONFIRMATION_URL'],
                        "EMAIL_TO" => $this->orderEmail2SendTransactionData,
                        "ORDER_ID" => $this->orderID,
                    ]
                ]) ? "SUCCESS" : "FAIL";

            } else {

                return "FAIL";

            }

        }

        private function setPaymentData ($data = []) {

            $this->paymentData = $data;
            return true;

        }

        private function getIdempotenceKey() {

    		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    			mt_rand(0, 0xffff), mt_rand(0, 0xffff),
    			mt_rand(0, 0xffff),
    			mt_rand(0, 0x0fff) | 0x4000,
    			mt_rand(0, 0x3fff) | 0x8000,
    			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    		);

    	}

    	public function getOrderObjectByPaymentItem (Payment $payment) {

	        $this->orderID = intVal($payment->getOrderID());
	        $this->orderPaymentID = $payment->getID();
	        $this->order = intVal($this->orderID) > 0 ? Order::load($this->orderID) : false;

	    }

	    public function getPropertiesByOrderData () {

	        if ($this->order && $propertyCollection = $this->order->getPropertyCollection()) {

	            $this->propertyArray = $propertyCollection->getArray();
	            $this->orderPhone = $propertyCollection->getPhone()->getValue();
	            $this->orderEmail = $propertyCollection->getUserEmail()->getValue();
	            $this->orderName = $propertyCollection->getPayerName()->getValue();

	        }

	        if (is_array($this->propertyArray['properties'])) {

                foreach ($this->propertyArray['properties'] as $property) {
                    if ("ORDER_PROP_" . $property['ID'] == $this->config['YANDEX_ORDER_PROPERTY_PHONE']) {
	                    $this->orderPhone2SendTransactionData = array_shift($property['VALUE']);
                    }
                    if ("ORDER_PROP_" . $property['ID'] == $this->config['YANDEX_ORDER_PROPERTY_EMAIL']) {
        	            $this->orderEmail2SendTransactionData = array_shift($property['VALUE']);
                    }

                    if($property['CODE'] == 'SMS_OTHER' && $property['VALUE'][0] == 'Y'){
                        $this->smsOther = true;
                    }
                }
	        }

	    }

        public function getDescriptionString ($string = "") {

            $descriptionVariables = [
                '#USER_EMAIL#' => $this->orderEmail,
                '#PAYMENT_NUMBER#' => $this->orderPaymentID,
                '#ORDER_NUMBER#' => $this->orderID,
                '#USER_NAME#' => $this->orderName,
            ];

            foreach ($descriptionVariables as $key => $var)
                $string = str_ireplace($key, $var, $string);

            return $string;
        }

        public function json_response ($response = []) {

            global $APPLICATION;
            $APPLICATION->RestartBuffer();
            echo self::encode($response);
            die();

        }

        public function getPaymentIdFromRequest(Request $request) {

            $inputStream = self::readFromStream();
    		if ($inputStream) {
    			$data = self::decode($inputStream);
    			if ($data === false) {
    				return false;
    			}
    			return $data['object']['metadata']['BX_PAYMENT_NUMBER'];
    		}

    		return false;

        }

        private static function readFromStream() {

		    return file_get_contents('php://input');

    	}

        private function decode($data) {
    		try {
    			return Main\Web\Json::decode($data);
    		} catch (Main\ArgumentException $exception) {
    			return false;
    		}
    	}

        private function encode(array $data) {

    		return Main\Web\Json::encode($data, JSON_UNESCAPED_UNICODE);

    	}

        public function getCurrencyList() {

            return array('RUB');

        }

        public function processRequest(Payment $payment, Request $request) {

            return true;

        }

        public function setPropertySms(){
            $propertyCollection = $this->order->getPropertyCollection();
            foreach ($propertyCollection as $prop){
                if($prop->getField('CODE') == 'SMS_OTHER'){
                    $prop->setField('VALUE', 'Y');
                    $this->order->save();
                }
            }
        }

    }
