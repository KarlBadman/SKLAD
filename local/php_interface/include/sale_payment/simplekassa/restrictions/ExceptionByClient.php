<?php

    use Bitrix\Main\Loader;
    use Bitrix\Sale\Internals\Entity;
    use Sinergi\BrowserDetector\Browser;
    use Sinergi\BrowserDetector\Os;
    use Sinergi\BrowserDetector\Device;

    class ExceptionByClient extends Bitrix\Sale\Delivery\Restrictions\Base {
        
       public static function check($client, $params) {
           
           $browser = new Browser(); $os = new Os(); $device = new Device();
           $client = [
               "BROWSER" => [
                   "NAME" => $browser->getName(),
                   "VERSION" => $browser->getVersion(),
               ],
               "OS" => [
                   "NAME" => $os->getName(),
                   "VERSION" => $os->getVersion(),
               ],
               "DEVICE" => [
                   "NAME" => $device->getName()
               ],
               "IS_MOBILE" => $os->isMobile()
           ];
           
           if (
                $client['OS']['NAME'] === Os::IOS
                && $client['BROWSER']['NAME'] === Browser::SAFARI
                && ($client["DEVICE"]['NAME'] === Device::IPHONE || $client["DEVICE"]['NAME'] === Device::IPAD)
            ) {
               
               return true;
               
            }
           
            return false;
           
       }
    
       public static function extractParams(Entity $payment) {
           
           return $payment;
           
       }
    
       public static function getClassTitle() {
           
          return 'По версии IOS клиента';
          
       }
    
       public static function getClassDescription() {
           
          return 'Проверка на версию клиента IOS';
          
       }
    
       public static function getParamsStructure($ios_client = "") {
           
          return [
              "IOS_CLIENTS_VERSION" => [
                  "TYPE" => "STRING",
                  "MULTIPLE" => "Y",
                  "LABEL" => "IOS версия",
              ]
          ];
          
       }
    
    }
     