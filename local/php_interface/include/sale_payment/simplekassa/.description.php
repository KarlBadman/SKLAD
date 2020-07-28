<?php

    use Bitrix\Main\Localization\Loc;
    Loc::loadMessages(__FILE__);
    
    $isAvailable = \Bitrix\Sale\PaySystem\Manager::HANDLER_AVAILABLE_TRUE;

    $licensePrefix = \Bitrix\Main\Loader::includeModule("bitrix24") ? \CBitrix24::getLicensePrefix() : "";
    if (IsModuleInstalled("bitrix24") && !in_array($licensePrefix, ["ru"]))
    {
    	$isAvailable = \Bitrix\Sale\PaySystem\Manager::HANDLER_AVAILABLE_FALSE;
    }
    
    $data = array(
    	'NAME' => Loc::getMessage("SALE_YANDEX_DATA"),
    	'SORT' => 500,
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
    		"YANDEX_SHOP_ARTICLEID" => array(
    			"NAME" => Loc::getMessage("SALE_YANDEX_SHOP_ARTICLEID"),
    			'SORT' => 300,
    			'GROUP' => 'PAYMENT',
    		),
    		"YANDEX_SHOP_BACK_URL" => array(
    			"NAME" => Loc::getMessage("SALE_YANDEX_SHOP_BACK_URL"),
    			'SORT' => 400,
    			'GROUP' => 'PAYMENT',
    		),
    		"YANDEX_SHOP_TRANSACTION_DESCRIPTION" => array(
    			"NAME" => Loc::getMessage("SALE_YANDEX_SHOP_TRANSACTION_DESCRIPTION"),
    			'SORT' => 500,
    			'GROUP' => 'PAYMENT',
    		),
    		
    		"YANDEX_SHOP_APPLE_PAY_MERCHANTIDENTIFIER" => array(
    			"NAME" => Loc::getMessage("YANDEX_SHOP_APPLE_PAY_MERCHANTIDENTIFIER"),
    			'SORT' => 600,
    			'GROUP' => 'PAYMENT',
    		),
    		"YANDEX_SHOP_APPLE_PAY_DOMAINNAME" => array(
    			"NAME" => Loc::getMessage("YANDEX_SHOP_APPLE_PAY_DOMAINNAME"),
    			'SORT' => 700,
    			'GROUP' => 'PAYMENT',
    		),
    		"YANDEX_SHOP_APPLE_PAY_DISPLAYNAME" => array(
    			"NAME" => Loc::getMessage("YANDEX_SHOP_APPLE_PAY_DISPLAYNAME"),
    			'SORT' => 800,
    			'GROUP' => 'PAYMENT',
    		),
    		"YANDEX_SHOP_APPLE_PAY_SLL_CRT_PATH" => array(
    			"NAME" => Loc::getMessage("YANDEX_SHOP_APPLE_PAY_SLL_CRT_PATH"),
    			'SORT' => 900,
    			'GROUP' => 'PAYMENT',
    		),
    		"YANDEX_SHOP_APPLE_PAY_SSL_CRT_KEY_PATH" => array(
    			"NAME" => Loc::getMessage("YANDEX_SHOP_APPLE_PAY_SSL_CRT_KEY_PATH"),
    			'SORT' => 1000,
    			'GROUP' => 'PAYMENT',
    		),
    		"YANDEX_SHOP_APPLE_PAY_SSL_CRT_KEY_PASSWORD" => array(
    			"NAME" => Loc::getMessage("YANDEX_SHOP_APPLE_PAY_SSL_CRT_KEY_PASSWORD"),
    			'SORT' => 1100,
    			'GROUP' => 'PAYMENT',
    		),
    	)
    );