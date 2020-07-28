<?

namespace Sale\Handlers\Delivery;

use Bitrix\Sale\Delivery\CalculationResult;
use Bitrix\Sale\Delivery\Services\Base;
use Bitrix\Sale;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

class DpdcustomHandler extends Base
{

    public $error = false;
    public $result;
    public $order;
    public $basket;
    public $arPack = [];
    public $totalPrice = 0;
    public $noski = true;
    public $padushki = true;
    public $padushkiCount = 0;
    public $сache;
    public $arParams;
    public $arSections;
    public $isPCLBasket = false;
    public $excludedSectionByPCL = ['dekor', 'tekstil', 'aksessuary', 'posuda'];
    public $minPacketDelivery;

    public static function getClassTitle()
    {
        return 'ДПД доставка dsklad';
    }

    public static function getClassDescription()
    {
        return 'ДПД доставка dsklad';
    }

    protected function calculateConcrete(\Bitrix\Sale\Shipment $shipment)
    {
        \Bitrix\Main\Loader::includeModule('dsklad.site');

        $this->result = new CalculationResult();
        $this->order = $shipment->getCollection()->getOrder();
        $this->basket = $this->order->getBasket();
        $this->сache = \Bitrix\Main\Data\Cache::createInstance();
        $this->arParams = $this->config['MAIN'];

        $this->basketGetList();  // Проходимся по всем элементам корзины
        $this->checkSectionAndSetTariff();  //проверяем разделы и устанавливаем тариф PCL
        $this->setMinPack(); // Проверяем размер упаковке (есть ли носки и подушки)

        Loader::includeModule("highloadblock");

        $cacheId = crc32($shipment->getDeliveryId().$this->order->getPersonTypeId().$_SESSION['DPD_CITY'].json_encode($this->arPack).$this->arParams['PRICE_NULL']);

        if (\Bitrix\Main\Loader::includeModule('dsklad.site')){
            if($this->arParams['CACHE'] == 'Y') {
                if ($this->сache->initCache($this->arParams['CACHE_TIME'], $cacheId, '/dpdDelivery') && !empty($_SESSION['DPD_CITY'])) {
                    $delivery = $this->сache->getVars();
                } else {
                    $delivery = \Dsklad\Delivery::getDPDCoast($this->arPack, $this->totalPrice, $this->config, $this->minPacketDelivery);
                    if ($this->сache->startDataCache() && !empty($_SESSION['DPD_CITY']) && $delivery['cost'] > 0) {

                        if($this->arParams['PRICE_NULL'] == 'Y') $delivery = self::freeDelivery($delivery,$this->arParams['DOOR']);

                        $this->сache->endDataCache($delivery);
                    }else{
                        if($delivery['cost'] <= 0) $this->error = true;
                    }
                }
            }else{
                $delivery = \Dsklad\Delivery::getDPDCoast($this->arPack, $this->totalPrice, $this->config, $this->minPacketDelivery);
                if($delivery['cost'] <= 0) $this->error = true;
                if($this->arParams['PRICE_NULL'] == 'Y') $delivery = self::freeDelivery($delivery,$this->arParams['DOOR']);

            }
            $this->result->setDeliveryPrice(round($delivery['cost']));
            $this->result->setPeriodDescription($delivery['days']);
            $this->result->setDescription(json_encode(array('DPD_STATUS' => $delivery['dpd_status'], 'serviceName'=>$delivery['serviceName'],'pack'=>$this->arPack,'error'=>$this->error)));
        }

        return $this->result;
    }

    protected function getConfigStructure()
    {
        return array(
            "MAIN" => array(
                "TITLE" => 'Настройка обработчика',
                "DESCRIPTION" => 'Настройка обработчика',
                "ITEMS" => array(
                    "DPD_COD" => array(
                        "TYPE" => "STRING",
                        "MIN" => 0,
                        "NAME" => 'Код ДПД',
                    ),
                    "NEW_COF" => array(
                        "TYPE" => "Y/N",
                        "MIN" => 0,
                        "NAME" => 'Новый коэфецент',
                    ),
                    "PRICE_SPB" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Стоимость доставки для СПб',
                    ),
                    "PRICE_SPB_CUR" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Стоимость доставки для СПб курьером'
                    ),
                    "DEY_SPB" => array(
                        "TYPE" => "STRING",
                        "MIN" => 0,
                        "NAME" => 'Время доставки Спб'
                    ),
                    "PRICE_MOS" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Стоимость доставки для Москвы'
                    ),
                    "PRICE_MOS_CUR" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Стоимость доставки для Москва курьером'
                    ),
                    "DEY_MOS" => array(
                        "TYPE" => "STRING",
                        "MIN" => 0,
                        "NAME" => 'время доставки Москвы'
                    ),
                    "DOOR" => array(
                        "TYPE" => "Y/N",
                        "NAME" => 'доставка до двери',
                    ),
                    "CACHE" => array(
                        "TYPE" => "Y/N",
                        "NAME" => 'Кешировать доставку',
                    ),
                    "CACHE_TIME" => array(
                        "TYPE" => "NUMBER",
                        "NAME" => 'Время кеша в секундах',
                    ),
                    "PRICE_NULL" => array(
                        "TYPE" => "Y/N",
                        "NAME" => 'Обнулять стоимость доставки',
                    ),
                )

            )
        );
    }
    public static function whetherAdminExtraServicesShow()
    {
        return true;
    }

    function getHigloadBlock($id,$select = [],$filter = []){

        $hlblock = HL\HighloadBlockTable::getById($id)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();

        $rsData = $entity_data_class::getList(array(
            "select" => $select,
            "filter" => $filter
        ));

        return $rsData;
    }

    public function basketGetList(){ // Проходимся по всем элементам корзины
        foreach ($this->basket as $basketItem) {
            if(\CIBlockElement::GetIBlockByID($basketItem->getProductId()) != \Dsklad\Config::getParam('iblock/basket_services')) {
                $pack = GetPackData($basketItem->getField('PRODUCT_XML_ID'));
                $keyJson = json_encode($pack);
                $this->totalPrice = $this->totalPrice + $basketItem->getFinalPrice();

                // Get product sections
                $arElement = \CIBlockElement::GetById($basketItem->getField('PRODUCT_ID'))->Fetch();
                if ($arElement['IBLOCK_ID'] != CATALOG_IBLOCK_ID) {
                    $arElement = \CCatalogSku::GetProductInfo($basketItem->getField('PRODUCT_ID'));
                    $arElement = \CIBlockElement::GetById($arElement['ID'])->Fetch();
                }

                $rsSection = \CIBlockSection::GetNavChain(false, $arElement['IBLOCK_SECTION_ID']);
                while ($obSection = $rsSection->GetNext()) {
                    $this->arSections[$arElement['ID']][$obSection['ID']] = $obSection['CODE'];
                }

                $rsSections = \CIBlockElement::GetElementGroups($arElement['ID'], true);
                while ($obSection = $rsSections->Fetch()) {
                    $this->arSections[$arElement['ID']][$obSection['ID']] = $obSection['CODE'];
                }

                if(strripos(mb_strtolower($basketItem->getField('NAME')),'носки') === false){
                    $this->noski = false;
                }

                if(strripos(mb_strtolower($basketItem->getField('NAME')),'Подушка') === false){
                    $this->padushki = false;
                }else{
                    $this->padushkiCount =  $this->padushkiCount + $basketItem->getField('QUANTITY');
                }

                if(!empty($arPack[$keyJson])){
                    $this->arPack[$keyJson]['QUANTITY'] =   $this->arPack[$keyJson]['QUANTITY'] + $basketItem->getQuantity();
                    $this->arPack[$keyJson]['PRODUCT_ID'] = $basketItem->getProductId();
                }else{
                    $this->arPack[$keyJson] = array(
                        'PRODUCT_ID' => $basketItem->getProductId(),
                        'PACK' => $pack,
                        'QUANTITY' => $basketItem->getQuantity()
                    );
                }
            }
        }
    }

    public function checkSectionAndSetTariff(){  //проверяем разделы и устанавливаем тариф PCL
        if (!empty($this->arSections) && is_array($this->arSections)) {
            foreach ($this->arSections as $itemId => $arSection) {
                foreach ($arSection as $variant) {
                    if (in_array($variant, $this->excludedSectionByPCL)) {
                        $this->arSections[$itemId] = 'Y'; break;
                    } else {
                        $this->arSections[$itemId] = 'N';
                    }
                }
            }
        }

        if (array_search('N', $this->arSections) === false) {
            $this->arParams['MAIN']['DPD_COD'] = "PCL";
        }
    }

    public function setMinPack() { // Проверяем размер упаковке (есть ли носки и подушки)
        if(($this->noski || ($this->padushki && $this->padushkiCount==1))){
            if($this->arParams['PRICE_SPB_CUR'] > 0 && $this->arParams['PRICE_MOS_CUR'] > 0){
                $this->minPacketDelivery = true;
            }else{
                $this->minPacketDelivery = false;
            }
        }else{
            $this->minPacketDelivery = false;
        }
    }

    function freeDelivery($delivery,$door ='N')
    {

        Loader::includeModule("highloadblock");

        $rsData = self::getHigloadBlock(\Dsklad\Config::getParam('hl/dpd_cities'), array("UF_REGIONCODE", "UF_COUNTRYCODE", "ID", "UF_CITYCODE"), array("UF_CITYCODE" => $_SESSION['DPD_CITY']));

        if ($row = $rsData->fetch()) {
            $countryCode = $row['UF_COUNTRYCODE'];
            $cityCode = $row['UF_CITYCODE'];
        }

        if ($countryCode != 'RU') return $delivery;

        if ($door == 'Y') {

            $rsDataCityDiscount = self::getHigloadBlock(\Dsklad\Config::getParam('hl/discount_courier_delivery'), array("ID", "UF_DISCOUNT"), array("%UF_CITYCODE" => $cityCode));
            if ($row = $rsDataCityDiscount->fetch()) {
                $delivery['cost'] = $delivery['cost'] - ($delivery['cost'] / 100 * $row['UF_DISCOUNT']);
            }

            return $delivery;
        } else {
            if (!empty($cityCode)) {

                $rsDataCityDiscount = self::getHigloadBlock(\Dsklad\Config::getParam('hl/discount_delivery'), array("ID", "UF_DISCOUNT"), array("%UF_CITY" => $cityCode));

                if ($row = $rsDataCityDiscount->fetch()) {
                    $delivery['cost'] = $delivery['cost'] - round(($delivery['cost'] / 100 * $row['UF_DISCOUNT']));
                }
                return $delivery;
            }
        }
        return $delivery;
    }
}
?>
