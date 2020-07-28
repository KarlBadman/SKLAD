<?php

    use Bitrix\Main\Localization\Loc;
    Loc::loadMessages(__FILE__);
    
    $isAvailable = \Bitrix\Sale\PaySystem\Manager::HANDLER_AVAILABLE_TRUE;
    $licensePrefix = \Bitrix\Main\Loader::includeModule("bitrix24") ? \CBitrix24::getLicensePrefix() : "";
    if (IsModuleInstalled("bitrix24") && !in_array($licensePrefix, ["ru"])) {
        
    	$isAvailable = \Bitrix\Sale\PaySystem\Manager::HANDLER_AVAILABLE_FALSE;
    	
    }
    
    $data = array(
    	'NAME' => Loc::getMessage("SALE_YANDEX_DATA"),
    	'SORT' => 100,
    	'IS_AVAILABLE' => $isAvailable,
    	'CODES' => array(
    	    "CHANGE_STATUS_PAY" => array(
    			"NAME" => Loc::getMessage("SALE_YANDEX_CHANGE_STATUS_PAY"),
    			'SORT' => 100,
    			'GROUP' => 'GENERAL_SETTINGS',
    			"INPUT" => array(
    				'TYPE' => 'Y/N'
    			),
    		),
    		"YANDEX_SHOP_ID" => array(
    			"NAME" => Loc::getMessage("SALE_YANDEX_SHOP_ID"),
    			'SORT' => 100,
    			'GROUP' => 'PAYMENT',
    		),
    		"YANDEX_SHOP_SECRET" => array(
    			"NAME" => Loc::getMessage("SALE_YANDEX_SHOP_SECRET"),
    			'SORT' => 200,
    			'GROUP' => 'PAYMENT',
    		),
    		"YANDEX_SHOP_BACK_URL" => array(
    			"NAME" => Loc::getMessage("SALE_YANDEX_SHOP_BACK_URL"),
    			'SORT' => 300,
    			'GROUP' => 'PAYMENT',
    		),
    		"YANDEX_SHOP_TRANSACTION_DESCRIPTION" => array(
    			"NAME" => Loc::getMessage("SALE_YANDEX_SHOP_TRANSACTION_DESCRIPTION"),
    			'SORT' => 400,
    			'GROUP' => 'PAYMENT',
    		),
    		"YANDEX_ORDER_PROPERTY_PHONE" => array(
    			"NAME" => Loc::getMessage("SALE_YANDEX_ORDER_PROPERTY_PHONE"),
    			'SORT' => 500,
    			'GROUP' => 'PAYMENT',
    		),
    		"YANDEX_ORDER_PHONE_SEND_TEXT" => array(
    			"NAME" => Loc::getMessage("SALE_YANDEX_ORDER_PHONE_SEND_TEXT"),
    			'SORT' => 600,
    			'GROUP' => 'PAYMENT',
    		),
    		"YANDEX_ORDER_PROPERTY_EMAIL" => array(
    			"NAME" => Loc::getMessage("SALE_YANDEX_ORDER_PROPERTY_EMAIL"),
    			'SORT' => 700,
    			'GROUP' => 'PAYMENT',
    		),
    		"YANDEX_ORDER_EMAIL_EVENT_TYPE" => array(
    			"NAME" => Loc::getMessage("SALE_YANDEX_ORDER_EMAIL_EVENT_TYPE"),
    			'SORT' => 800,
    			'GROUP' => 'PAYMENT',
    		),
    	)
    );