<?php

    namespace Sale\Handlers\PaySystem;

    use YandexCheckout\Client;
    use Bitrix\Main\Application;
    use Bitrix\Main\ArgumentException;
    use Bitrix\Main\Error;
    use Bitrix\Main\Localization\Loc;
    use Bitrix\Main\Request;
    use Bitrix\Main\Type\DateTime;
    use Bitrix\Sale\BusinessValue;
    use Bitrix\Sale\Payment;
    use Bitrix\Sale\Order;
    use Bitrix\Sale\OrderBase;
    use Bitrix\Sale\PaySystem;
    use Bitrix\Main;
    use Bitrix\Main\Localization;
    use Bitrix\Sale\PriceMaths;

    Loc::loadMessages(__FILE__);

    class SimpleKassaHandler extends PaySystem\ServiceHandler {

        private $client;

        private $config;

        private $orderID;

        private $order;

        private $amount;

        private $propertyArray;

        private $orderPhone;

        private $orderEmail;

        private $orderPaymentID;

        private $paymentRespond;

        private $paySystemResult;

        private $capturePaymentRespond;

        const VAT_RATE_PERCENT = 20;

        const CMS_NAME = "api_1c-bitrix";

        const BX_HADLER_CODE = "YANDEX_CHECKOUT_EXTRA";

        const PAYMENT_STATUS_WAITING_FOR_CAPTURE = 'waiting_for_capture';

    	const PAYMENT_STATUS_SUCCEEDED = 'succeeded';

    	const PAYMENT_STATUS_CANCELED = 'canceled';

    	const PAYMENT_STATUS_PENDING = 'pending';

	    public function initiatePay(Payment $payment, Request $request = null) {

            $this->isPaid = $payment->isPaid();
	        $this->config = [
	            "CHANGE_STATUS_PAY" => $this->getBusinessValue($payment, 'CHANGE_STATUS_PAY'),
	            "YANDEX_SHOP_ID" => $this->getBusinessValue($payment, 'YANDEX_SHOP_ID'),
	            "YANDEX_SHOP_SECRET" => $this->getBusinessValue($payment, 'YANDEX_SHOP_SECRET'),
	            "YANDEX_SHOP_ARTICLEID" => $this->getBusinessValue($payment, 'YANDEX_SHOP_ARTICLEID'),
	            "YANDEX_SHOP_BACK_URL" => $this->getBusinessValue($payment, 'YANDEX_SHOP_BACK_URL'),
	            "YANDEX_SHOP_TRANSACTION_DESCRIPTION" => $this->getBusinessValue($payment, 'YANDEX_SHOP_TRANSACTION_DESCRIPTION'),
	            "PS_MODE" => $this->service->getField('PS_MODE'),
	            "PRODUCTION_MERCHANTIDENTIFIER" => $this->getBusinessValue($payment, 'YANDEX_SHOP_APPLE_PAY_MERCHANTIDENTIFIER'),
	            "PRODUCTION_DOMAINNAME" => $this->getBusinessValue($payment, 'YANDEX_SHOP_APPLE_PAY_DOMAINNAME'),
	            "PRODUCTION_DISPLAYNAME" => $this->getBusinessValue($payment, 'YANDEX_SHOP_APPLE_PAY_DISPLAYNAME'),
	            "YANDEX_SHOP_APPLE_PAY_SLL_CRT_PATH" => $this->getBusinessValue($payment, 'YANDEX_SHOP_APPLE_PAY_SLL_CRT_PATH'),
	            "YANDEX_SHOP_APPLE_PAY_SSL_CRT_KEY_PATH" => $this->getBusinessValue($payment, 'YANDEX_SHOP_APPLE_PAY_SSL_CRT_KEY_PATH'),
	            "YANDEX_SHOP_APPLE_PAY_SSL_CRT_KEY_PASSWORD" => $this->getBusinessValue($payment, 'YANDEX_SHOP_APPLE_PAY_SSL_CRT_KEY_PASSWORD'),
            ];

	        if ($request === null) {
			    $request = Main\Context::getCurrent()->getRequest();
		    }

	        if ($request->getPost("ACTION") == "APPLEPAYVALIDATEMERCHANT") {

	           $this->getApplePayValidateMerchantData($request->getPost("validationURL"));

	        } else if ($request->getPost("ACTION") == "APPLEPAYAUTHORISEPAYMENT") {

                global $APPLICATION; $APPLICATION->RestartBuffer();
                $this->initiatePayInternal($payment, $request);
                echo json_encode(["Success" => true]);
                die();

	        } else {

	            $this->initiatePayInternal($payment, $request);
	            return $this->showTemplate($payment, 'template');

	        }

        }

	    public function initiatePayInternal (Payment $payment, Request $request = null) {

	        $this->paySystemResult = new PaySystem\ServiceResult();
            $this->client = new Client();
            $this->client->setAuth($this->config['YANDEX_SHOP_ID'], $this->config['YANDEX_SHOP_SECRET']);
            $this->getOrderObjectByPaymentItem($payment);
            $this->getPropertiesByOrderData($payment);
            $this->amount = $payment->getSum();

            try {

                $this->createYandexPaymentRequest($payment, $request);

            } catch (Exception $e) {

                $this->paySystemResult->addError(new Error(Loc::getMessage('SALE_YANDEX_HANDLER_ERROR_CREATE_PAYMENT_REQUEST') . " " . $e->getMessage()));

            }

            $this->paymentCallAction();

	    }

	    public function createYandexPaymentRequest (Payment $payment, Request $request) {

	        $requestArray = [
	            "amount" => [
	               "value" => $this->getAmount(),
	                "currency" => array_shift($this->getCurrencyList()),
                ],
                "payment_method_data" => [
                    "type" => $this->config['PS_MODE'],
                ],
                'capture' => true, // VERY INPORTANT
                "confirmation" => [
                    "type" => $this->getConfirmationTypeByPaymentMethod(),
                    "return_url" => $this->getDescriptionString($this->config['YANDEX_SHOP_BACK_URL']),
                ],
                "description" => $this->getDescriptionString($this->config['YANDEX_SHOP_TRANSACTION_DESCRIPTION']),
                "metadata" => [
    				"BX_PAYMENT_NUMBER" => $payment->getId(),
    				"BX_PAYSYSTEM_CODE" => $this->service->getField('ID'),
    				"BX_HANDLER" => self::BX_HADLER_CODE,
    				"cms_name" => self::CMS_NAME,
    			]
            ];

            if ($this->config['PS_MODE'] == "apple_pay") {

                if (!$cryptogram = $this->decode($request->getPost('cryptogram'))) {
                    PaySystem\Logger::addError('Yandex.Checkout Apple Pay return error not set cryptogram');
                    return false;
                }

                unset($requestArray['confirmation']);
                $requestArray['payment_method_data']['payment_data'] = base64_encode(json_encode($cryptogram['paymentData']));

                if ($request->getPost("ACTION") != "APPLEPAYAUTHORISEPAYMENT") {

                    PaySystem\Logger::addDebugInfo('Yandex.Checkout Apple Pay return');
                    return true;

                }

            } else if ($this->config['PS_MODE'] == "google_pay") { // NOT ACTIVE ON YANDEX

                unset($requestArray['confirmation']);
                $requestArray['payment_method_data']['google_transaction_id'] = "";
                $requestArray['payment_method_data']['payment_method_token'] = "";

            } else if ($this->config['PS_MODE'] == "qiwi") { // NOT ACTIVE ON SHOP

                $requestArray['payment_method_data']['phone'] = $this->orderPhone;

            } else if ($this->config["PS_MODE"] == "alfabank") {

                $requestArray['payment_method_data']['login'] = $this->orderPhone;

            } else if ($this->config['PS_MODE'] == 'b2b_sberbank') { // NOT ACTIVE ON SHOP

                unset($requestArray['confirmation']['return_url']);
                $requestArray['payment_method_data']['payment_purpose'] = $this->getDescriptionString($this->config['YANDEX_SHOP_TRANSACTION_DESCRIPTION']);
                $requestArray['payment_method_data']['vat_data'] = [
                    "type" => "calculated",
                    "rate" => self::VAT_RATE_PERCENT,
                    "amount" => [
                        "value" => 9, // TODO Расчитай сумму НДС тут стоит от балды
                        "currency" => array_shift($this->getCurrencyList()),
                    ],
                ];
                $requestArray['capture'] = true;

            } else if ($this->config['PS_MODE'] == 'mobile_balance') { // NOT ACTIVE ON SHOP

                unset($requestArray['confirmation']['return_url']);
                $requestArray['payment_method_data']['phone'] = $this->orderPhone;

            } else if ($this->config['PS_MODE'] == 'all_payments') {
                
                unset($requestArray['payment_method_data']);
                
            }

            PaySystem\Logger::addDebugInfo('Yandex.Checkout: request data: ' . $requestArray);
	        $this->paymentRespond = $this->client->createPayment($requestArray, $this->getIdempotenceKey());

	    }

	    public function createYandexCaptureRequest (Payment $payment, $paymentID = null) {

            if ($paymentID) {

                $this->capturePaymentRespond = $this->client->capturePayment([

                    "amount" => [
                        "value" => $payment->getSum(),
                        "currency" => array_shift($this->getCurrencyList())
                    ]

                ], $paymentID, $this->getIdempotenceKey());

                return true;

            }

            return false;

	    }

	    public function paymentCallAction () {

	        $confirmationMethod = $this->getConfirmationTypeByPaymentMethod();
	        if ($confirmationMethod == 'redirect') {

	            if ($this->paymentRespond->getConfirmation()->getConfirmationUrl()) {

					LocalRedirect($this->paymentRespond->getConfirmation()->getConfirmationUrl(), "refresh");

	            }

	        }

            return true;

	    }

	    public function getConfig ($key) {

	        return isset($this->config[$key]) ? $this->config[$key] : false;

	    }

	    public function getOrderID () {

	        return $this->orderID;

	    }

	    public function getAmount () {

	        return floatVal($this->amount);

	    }

	    public function getOrderPaymentID() {

	        return $this->orderPaymentID;

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

	        }

	    }

        public function getDescriptionString ($string = "") {

            $descriptionVariables = [
                '#USER_EMAIL#' => $this->orderEmail,
                '#PAYMENT_NUMBER#' => $this->orderPaymentID,
                '#ORDER_NUMBER#' => $this->orderID,
            ];

            foreach ($descriptionVariables as $key => $var)
                $string = str_ireplace($key, $var, $string);

            return $string;
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

	    private function getConfirmationTypeByPaymentMethod () {

	        $type = false;

            switch ($this->config['PS_MODE']) {
                case ('bank_card') :
                case ('all_payments') :
                case ('yandex_money') :
                case ('sberbank') :
                case ('qiwi') :
                case ('tinkoff_bank') :
                case ('b2b_sberbank') :
                case ('installments') : $type = "redirect";
                    break;
                case ('alfabank') :
                case ('mobile_balance') : $type = "external";
                    break;
                default : $type = "none";
                    break;
            }

	        return $type;
	    }

        public function getApplePayValidateMerchantData ($validation_url = null) {

            // $validation_url = "https://apple-pay-gateway-cert.apple.com/paymentservices/startSession";

            global $APPLICATION;
            $APPLICATION->RestartBuffer();$result = [];

            if(
                "https" == parse_url($validation_url, PHP_URL_SCHEME)
                && substr(parse_url($validation_url, PHP_URL_HOST), -10)  == ".apple.com"
            ) {

            	$ch = curl_init();
                $data = json_encode([
                    "merchantIdentifier" => $this->config['PRODUCTION_MERCHANTIDENTIFIER'],
                    "domainName" => $this->config['PRODUCTION_DOMAINNAME'],
                    "displayName" => $this->config['PRODUCTION_DISPLAYNAME']
                ]);

            	curl_setopt($ch, CURLOPT_URL, $validation_url);
                curl_setopt($ch, CURLOPT_POST, 1);
            	curl_setopt($ch, CURLOPT_SSLCERT, $this->config['YANDEX_SHOP_APPLE_PAY_SLL_CRT_PATH']);
            	curl_setopt($ch, CURLOPT_SSLKEY, $this->config['YANDEX_SHOP_APPLE_PAY_SSL_CRT_KEY_PATH']);
            	curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $this->config['YANDEX_SHOP_APPLE_PAY_SSL_CRT_KEY_PASSWORD']);
            	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            	if(!$result = curl_exec($ch)) {
                    echo json_encode(["STATUS" => false, "curlError" => curl_error($ch)]);
            	}

            	curl_close($ch);

            } else {
                echo json_encode(["STATUS" => false]);
            }

            die();
        }

	    private static function readFromStream() {

		    return file_get_contents('php://input');

    	}

	    public function getCurrencyList() {

	        return array('RUB');

	    }

	    public function processRequest(Payment $payment, Request $request) {

	        $this->paySystemResult = new PaySystem\ServiceResult();
            
            
            // Get new object for Yandex client
            $this->client = new Client();
            $this->client->setAuth($this->getBusinessValue($payment, 'YANDEX_SHOP_ID'), $this->getBusinessValue($payment, 'YANDEX_SHOP_SECRET'));

    		$inputStream = self::readFromStream();
    		PaySystem\Logger::addDebugInfo('Yandex.Checkout: inputStream: ' . $inputStream);
    		$data = self::decode($inputStream);

    		if ($data !== false) {

    			$response = $data['object'];
    			if ($response['status'] === self::PAYMENT_STATUS_SUCCEEDED) {

    				$fields = array(
    					"PS_STATUS_CODE" => substr($response['status'], 0, 5),
    					"PS_STATUS_DESCRIPTION" => Localization\Loc::getMessage('SALE_YANDEX_HANDLER_TRANSACTION_ADD') . " " . $response['id'],
    					"PS_SUM" => $response['amount']['value'],
    					"PS_STATUS" => 'N',
    					"PS_CURRENCY" => $response['amount']['currency'],
    					"PS_RESPONSE_DATE" => new Main\Type\DateTime()
    				);

    				if ($this->isSumCorrect($payment, $response)) {

    					$fields["PS_STATUS"] = 'Y';
    					PaySystem\Logger::addDebugInfo('Yandex.Checkout: CHANGE_STATUS_PAY=' . $this->getBusinessValue($payment, 'CHANGE_STATUS_PAY'));

    					if ($this->getBusinessValue($payment, 'CHANGE_STATUS_PAY') === 'Y') {
    						$this->paySystemResult->setOperationType(PaySystem\ServiceResult::MONEY_COMING);
    					}

    				} else {

    					$error = Localization\Loc::getMessage('SALE_YANDEX_HANDLER_WRONG_SUMM');
    					$fields['PS_STATUS_DESCRIPTION'] .= ' ' . $error;
    					$this->paySystemResult->addError(new Main\Error($error));

    				}

    				$this->paySystemResult->setPsData($fields);

    			} else if ($response['status'] === self::PAYMENT_STATUS_WAITING_FOR_CAPTURE) {

                        PaySystem\Logger::addDebugInfo('Yandex.Checkout: GET PAYMENT STATUS =' . self::PAYMENT_STATUS_WAITING_FOR_CAPTURE . ' and payment capturing');
                        $yandexPaymentID = $response['id'] ? : $response['payment_method']['id'];
                        if (!$this->createYandexCaptureRequest($payment, $yandexPaymentID)) {

                            $this->paySystemResult->addError(new Main\Error(Localization\Loc::getMessage('SALE_YANDEX_HANDLER_CHECKOUT_ERROR_CAPTURE_PAYMENT')));

                        }

                } else {

    				$this->paySystemResult->addError(new Main\Error(Localization\Loc::getMessage('SALE_YANDEX_HANDLER_CHECKOUT_ERROR_STATUS').': ' . $response['status']));

    			}

    		} else {

    			$this->paySystemResult->addError(new Main\Error('SALE_YANDEX_HANDLER_QUERY_ERROR'));

    		}

    		if (!$this->paySystemResult->isSuccess()) {

    			PaySystem\Logger::addError('Yandex.Checkout: processRequest: ' . join('\n', $this->paySystemResult->getErrorMessages()));

    		}

    		return $this->paySystemResult;

	    }

	    private function isSumCorrect(Payment $payment, array $paymentData) {

    		PaySystem\Logger::addDebugInfo("Yandex.Checkout: yandexSum=" . PriceMaths::roundPrecision($paymentData['amount']['value']) . "; paymentSum=" . PriceMaths::roundPrecision($payment->getSum()));
    		return PriceMaths::roundPrecision($paymentData['amount']['value']) === PriceMaths::roundPrecision($payment->getSum());
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

	    public static function getHandlerModeList() {

	        return [
	            'bank_card' => Loc::getMessage('SALE_YANDEX_HANDLER_MODEL_BANK_CARD'),
	            'all_payments' => Loc::getMessage('SALE_YANDEX_HANDLER_MODEL_ALL_PAYMENTS'),
	            'yandex_money' => Loc::getMessage('SALE_YANDEX_HANDLER_MODEL_YANDEX_MONEY'),
	            'sberbank' => Loc::getMessage('SALE_YANDEX_HANDLER_MODEL_SBERBANK'),
	            'apple_pay' => Loc::getMessage('SALE_YANDEX_HANDLER_MODEL_APPLE_PAY'),
	           // 'google_pay' => Loc::getMessage('SALE_YANDEX_HANDLER_MODEL_GOOGLE_PAY'),
	           // 'qiwi' => Loc::getMessage('SALE_YANDEX_HANDLER_MODEL_QIWI'),
	            'alfabank' => Loc::getMessage('SALE_YANDEX_HANDLER_MODEL_ALFABANK'),
	            'tinkoff_bank' => Loc::getMessage('SALE_YANDEX_HANDLER_MODEL_TINKOFF_BANK'),
	           // 'b2b_sberbank' => Loc::getMessage('SALE_YANDEX_HANDLER_MODEL_B2B_SBERBANK'),
	           // 'mobile_balance' => Loc::getMessage('SALE_YANDEX_HANDLER_MODEL_MOBILE_BALLANCE'),
	            'installments' => Loc::getMessage('SALE_YANDEX_HANDLER_MODEL_INSTALLMENTS'),
            ];

	    }

	    private function encode(array $data) {

    		return Main\Web\Json::encode($data, JSON_UNESCAPED_UNICODE);

    	}

	    private function decode($data) {
    		try {
    			return Main\Web\Json::decode($data);
    		} catch (Main\ArgumentException $exception) {
    			return false;
    		}
    	}

	    static function isMyResponse(Request $request, $paySystemId) {

            $inputStream = self::readFromStream();
    		if ($inputStream) {

                $data = self::decode($inputStream);
                if ($data['object']['id']) {
                    PaySystem\Logger::addDebugInfo('Yandex.Checkout: Check my response: paySystemId=' . $data['object']['id'] .' inputStream=' . $inputStream);
                }

    			if ($data === false) {
    				return false;
    			}

    			if (isset($data['object']['metadata']['BX_HANDLER'])
    				&& $data['object']['metadata']['BX_HANDLER'] === self::BX_HADLER_CODE
    				&& isset($data['object']['metadata']['BX_PAYSYSTEM_CODE'])
    				&& (int)$data['object']['metadata']['BX_PAYSYSTEM_CODE'] === (int)$paySystemId
    			) {

    				return true;
    			}
    		}

    		return false;

	    }

    }
