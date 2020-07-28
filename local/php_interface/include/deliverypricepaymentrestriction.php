<?
    use Bitrix\Sale\Delivery\Restrictions;
    use Bitrix\Sale\Internals\Entity;

    class DeliveryPricePaymentRestriction extends Restrictions\Base {
        
        public static function getClassTitle() {
            return 'по стоимости доставки';
        }
    
        public static function getClassDescription() {
            return 'платежная система будет доступна только в определенном диапазоне стоимости доставки';
        }
    
        public static function check($deliveryPrice, array $restrictionParams, $paymentId = 0) {
            if ((float)$deliveryPrice < (float)$restrictionParams['MIN_DELIVERY_PRICE'] || (float)$deliveryPrice > (float)$restrictionParams['MAX_DELIVERY_PRICE'])
                return false;
        
            return true;
        }
        
        protected static function extractParams(Entity $shipment) {
            return 5000;
        }
        
        public static function getParamsStructure($entityId = 0) {
            return array(
                "MIN_DELIVERY_PRICE" => array(
                    'TYPE' => 'NUMBER',
                    'DEFAULT' => "",
                    'LABEL' => 'Минимальная цена доставки'
                ),
                "MAX_DELIVERY_PRICE" => array(
                    'TYPE' => 'NUMBER',
                    'DEFAULT' => "",
                    'LABEL' => 'Максимальная цена доставки'
                )
            );
        }
    }
?>