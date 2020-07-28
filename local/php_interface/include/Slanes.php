<?
	class Slanes
	{
		private static $key_buffer = 'QN7aRE1zAqv9ARcNhsWnzRMQQuWq8MOP';
		private static $shop_buffer = 'dsklad_buffer-ru';
		
		public function OnAfterIBlockElementAdd(&$arFields)
		{
			
			
			if ($arFields['IBLOCK_ID'] == 18 || $arFields['IBLOCK_ID'] == 19) {
				//Форма обратной связи
				$phone = trim(preg_replace('/[^0-9\-\+ ]/', '', $arFields['NAME']));
				$name = rtrim(mb_substr(trim(str_replace($phone, "", $arFields['NAME'])), 1), ')');
				
				$arData = array(
									'orderMethod' => 'feedback',
									'phone' => $phone,
									'firstName' => $name,
									'customerComment' => $arFields['DETAIL_TEXT'],
                                    'customFields' => array(
                                        'no_upload' => true
                                    )
								);

                // $response = self::sendToCRM($arData, self::$key_buffer, self::$shop_buffer);
								
                $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
				$api_site = 'dsklad-ru';
				$response = self::sendToCRM($arData, $api_key, $api_site);
			}
			
			if ($arFields['IBLOCK_ID'] == 16) {
				//перезвоните мне
				$phone = trim(preg_replace('/[^0-9\-\+ ]/', '', $arFields['NAME']));
				$name = trim(str_replace('(' . $phone . ')', "", $arFields['NAME']));
				
				$arData = array(
									'orderMethod' => 'call-me',
									'phone' => $phone,
									'firstName' => $name,
                                    'customFields' => array(
                                        'no_upload' => true
                                    )
								);
								
				// self::sendToCRM($arData, self::$key_buffer, self::$shop_buffer);
                
                $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
				$api_site = 'dsklad-ru';
				$response = self::sendToCRM($arData, $api_key, $api_site);
			}
			
			if ($arFields['IBLOCK_ID'] == 24) {
				//Нашли дешевле
				$temp = explode(' ', $arFields['NAME']);
				$email = $temp[0];
				$name = rtrim(str_replace($email . ' (', "",$arFields['NAME']), ')');
				
				$arData = array(
									'orderMethod' => 'price-decrease-request',
									'phone' => $arFields['PROPERTY_VALUES']['PHONE'],
									'email' => $email,
									'firstName' => $name,
									'customerComment' => 'Ссылка на товар: ' . $arFields['PROPERTY_VALUES']['LINK'] . "\n Стоимость: " . $arFields['PROPERTY_VALUES']['PRICE'],
									'items' => array(
														array(
																'offer' => array(
																					'quantity' => 1,
																					'externalId' => intVal($arFields['PROPERTY_VALUES']['SHIPMENT'])
																				)
															)
													),
                                    'customFields' => array(
                                        'no_upload' => true
                                    )
								);
				if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
				$api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
				$api_site = 'dsklad-ru';
				$response = self::sendToCRM($arData, $api_key, $api_site);
                
			}
			
			if ($arFields['IBLOCK_ID'] == 17) {
				//Задать вопрос
				$temp = explode(' ', $arFields['NAME']);
				$email = $temp[0];
				$name = rtrim(str_replace($email . ' (', "",$arFields['NAME']), ')');
				
				$arData = array(
									'orderMethod' => 'ask',
									'email' => $email,
									'firstName' => $name,
									'customerComment' => $arFields['DETAIL_TEXT'],
                                    'customFields' => array(
                                        'no_upload' => true
                                    )
								);
								
				// self::sendToCRM($arData, self::$key_buffer, self::$shop_buffer);
                
                $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
				$api_site = 'dsklad-ru';
				$response = self::sendToCRM($arData, $api_key, $api_site);
			}
            
            
            // makcrx: begin
            // передаём в GA событие order:success
            if ($response) {
                if ($response->isSuccessful()) {
                    ob_start();
                    
                    $orderId = (string)$response->id . 'A';
                    Makcrx::sendOrderSuccessEvent2ua($orderId);
                    
                    ob_end_clean();
                }
            }
            // makcrx: end
		}
		
		private static function sendToCRM($arFields, $api_key, $api_site)
		{
			if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
			$api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
			$client = new \RetailCrm\ApiClient($api_host, $api_key, $api_site);
			
			$response = $client->ordersCreate($arFields);
            return $response;
		}
	}