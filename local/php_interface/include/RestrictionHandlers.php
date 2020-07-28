<?
    // Подключения класс доп условий доставки
    $eventManager = \Bitrix\Main\EventManager::getInstance();
    $eventManager->addEventHandler('sale', 'onSaleDeliveryRestrictionsClassNamesBuildList', 'myDeliveryRestrictions');
    
    function myDeliveryRestrictions() {
    
    	return new \Bitrix\Main\EventResult( \Bitrix\Main\EventResult::SUCCESS,  [
    
    		//'\ExceptionBySection' => '/local/php_interface/include/sale_delivery/dpdcustom/restrictions/ExceptionBySection.php',
    		//'\ExceptionById' => '/local/php_interface/include/sale_delivery/dpdcustom/restrictions/ExceptionById.php',
            '\ExceptionByTerminals' => '/local/php_interface/include/sale_delivery/dpdcustom/restrictions/ExceptionByTerminals.php',
    
    	]);
    }
    
    // Add payment restrictions
    Bitrix\Main\EventManager::getInstance()->addEventHandler('sale', 'onSalePaySystemRestrictionsClassNamesBuildList', 'myPaymentRestrictions');
    
    function myPaymentRestrictions () {
        
        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS,  [

            '\ExceptionByDeliveryPrice' => '/local/php_interface/include/sale_payment/dpdcustom/restrictions/ExceptionByDeliveryPrice.php',
            '\ExceptionByCountry' => '/local/php_interface/include/sale_payment/dpdcustom/restrictions/ExceptionByCountry.php',
            '\ExceptionByLocation' => '/local/php_interface/include/sale_payment/dpdcustom/restrictions/ExceptionByLocation.php',
            '\ExceptionByClient' => '/local/php_interface/include/sale_payment/simplekassa/restrictions/ExceptionByClient.php',
        ]);
    }
    
?>