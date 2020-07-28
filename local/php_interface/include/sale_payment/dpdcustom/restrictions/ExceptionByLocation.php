<?
    use Bitrix\Main\Loader;
    use Bitrix\Sale\Internals\Entity;
    use Bitrix\Highloadblock\HighloadBlockTable;
    
    class ExceptionByLocation extends Bitrix\Sale\Delivery\Restrictions\Base {
        
        public static function getClassTitle() {
            return 'по флвгу в DPDCITIES';
        }
    
        public static function getClassDescription() {
            return 'платежка будет выводится только для указанных городов из DPDCITIES';
        }
    
        public static function check($cityID, array $restrictionParams, $paymentID = 0) {
            
            Loader::includeModule('highloadblock');
            
            $arHLBlock = HighloadBlockTable::getList(['filter' => ['NAME' => 'DPDCITIES']])->fetch();
            $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
            $strEntityDataClass = $obEntity->getDataClass();
            
            if (!empty($_SESSION['DPD_CITY']))
                $city = $strEntityDataClass::getList(['filter' => ['UF_CITYCODE' => $_SESSION['DPD_CITY']]])->fetch();
                
            return $restrictionParams['ACTIVE'] == 'Y' && !empty($city['UF_CASHPAYMENTCANCEL']) ? false : true;
                
        }
        
        protected static function extractParams(Entity $payment) {
            
            return $payment;
        }
        
        public static function getParamsStructure($entityId = 0) {
            
            return array(
                "ACTIVE" => array(
                    'TYPE' => 'Y/N',
                    'LABEL' => 'Проверять DPDCITIES на НПП'
                ),
            );
            
        }
    }
?>