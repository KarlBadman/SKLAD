<?
    use Bitrix\Main\Loader;
    use Bitrix\Sale\Internals\Entity;
    use Bitrix\Highloadblock\HighloadBlockTable;
    
    class ExceptionByDeliveryPrice extends Bitrix\Sale\Delivery\Restrictions\Base {
        
        public static function getClassTitle() {
            return 'По стоимости доставки';
        }
    
        public static function getClassDescription() {
            return 'По стоимости доставки';
        }
    
        public static function check(Entity $entity, array $restrictionParams, $paymentID = 0) {

            $order = $entity->getCollection()->getOrder();

            return $order->getDeliveryPrice() > $restrictionParams['PRICE_DELIVERY']? false: true;

        }
        
        protected static function extractParams(Entity $payment) {
            
            return $payment;
        }
        
        public static function getParamsStructure($entityId = 0) {
            
            return array(
                "PRICE_DELIVERY" => array(
                    'TYPE' => 'STRING',
                    'LABEL' => 'Стоимость доставки до'
                ),
            );
            
        }
    }
?>