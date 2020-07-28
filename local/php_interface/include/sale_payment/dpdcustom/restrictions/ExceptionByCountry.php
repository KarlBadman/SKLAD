<?
    use Bitrix\Main\Loader;
    use Bitrix\Sale\Internals\Entity;
    use Bitrix\Highloadblock\HighloadBlockTable;
    
    class ExceptionByCountry extends Bitrix\Sale\Delivery\Restrictions\Base {
        
        public static function getClassTitle() {
            return 'Только для этой страны';
        }
    
        public static function getClassDescription() {
            return 'Платежная система будет выводится только для указанныой строны';
        }
    
        public static function check($cityID, array $restrictionParams, $paymentID = 0) {
            
            Loader::includeModule('highloadblock');
            
            $arHLBlock = HighloadBlockTable::getList(['filter' => ['NAME' => 'DPDCITIES']])->fetch();
            $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
            $strEntityDataClass = $obEntity->getDataClass();
            
            if (!empty($_SESSION['DPD_CITY']))
                $countryId = $strEntityDataClass::getList(['filter' => ['UF_CITYCODE' => $_SESSION['DPD_CITY']]])->fetch()['UF_COUNTRYCODE'];
                
            return $restrictionParams['COUNTRY'] == $countryId && !empty($countryId) ? true : false;

        }
        
        protected static function extractParams(Entity $payment) {
            
            return $payment;
        }
        
        public static function getParamsStructure($entityId = 0) {

            return array(
                "COUNTRY" => array(
                    'TYPE' => 'STRING',
                    'LABEL' => 'ID Страны'
                ),
            );
            
        }
    }
?>