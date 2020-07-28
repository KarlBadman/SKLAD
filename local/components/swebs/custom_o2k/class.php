<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/classes/dpd_service.class.php';

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Event;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\DiscountCouponsManager;
use Bitrix\Sale\Fuser;
use Bitrix\Sale\Order;

Loader::includeModule('sale');
Loader::includeModule('iblock');
Loader::includeModule('catalog');
Loader::includeModule('highloadblock');
Loader::includeModule('altasib.geoip');

class COrderBasket extends CBitrixComponent
{
    public $intTotalSum = 0;
    public $intTotalVolume = 0;
    protected $arElements = array();
    protected $intWeight = 0;
    protected $intDeliveryServicesSum = 0;
    protected $arServices = array();
    protected $arDeliveryServices = array();
    protected $strSourceTerminals = 'DPD'; // DPD - get terminals from DPD site, HL - get terminals from HL-block

    private $weightByVolume = [];

    public function getBasketItemsCount(){
        $obContext = Context::getCurrent();
        $obBasket = Basket::loadItemsForFUser(Fuser::getId(), $obContext->getSite());
        return count(Basket);
    }

    public function getBasketItems($arExcept = array())
    {
        $arBasketItems = array(
            'SERVICES' => array(),
            'SHIPMENT' => array()
        );

        $obContext = Context::getCurrent();
        $obBasket = Basket::loadItemsForFUser(Fuser::getId(), $obContext->getSite());

        foreach ($obBasket as $obBasketItem) {
            // услуги доставки
            $arFullDeliveryServices = $this->getDeliveryServices();
            $arDeliveryServices = array();
            foreach ($arFullDeliveryServices as $arService) {
                $arDeliveryServices[] = $arService['ID'];
            }

            if (array_search($obBasketItem->getProductId(), $arDeliveryServices) !== false) {
                $this->intDeliveryServicesSum += $obBasketItem->getPrice() * $obBasketItem->getQuantity();
            }

            $this->intTotalSum += $obBasketItem->getPrice() * $obBasketItem->getQuantity();
            if (array_search($obBasketItem->getProductId(), $arExcept) !== false) {
                $arBasketItems['SERVICES'][] = $obBasketItem->getProductId();
                continue;
            }

            $arFields = array(
                'ID' => $obBasketItem->getId(),
                'PRODUCT_ID' => $obBasketItem->getProductId(),
                'NAME' => $this->getElementName($obBasketItem->getProductId()),// $obBasketItem->getField('NAME'),
                'NAME_URL' => $this->getNameURL($obBasketItem->getProductId()),
                'QUANTITY' => $obBasketItem->getQuantity(),
                'PRICE' => number_format($obBasketItem->getPrice(), 0, '', ' ') . '.–',
                'FINAL_PRICE' => number_format($obBasketItem->getFinalPrice(), 0, '', ' ') . '.–',
                'IMAGE' => $this->getImage($obBasketItem->getProductId()),
                'ARTICLE' => $this->getArticle($obBasketItem->getProductId()),
                'COLOR' => $this->getColor($obBasketItem->getProductId()),
                'SIZE' => $this->getSize($obBasketItem->getProductId()),
                'SECTION' => $this->getSectionName($obBasketItem->getProductId()),
                'SECTION_URL' => $this->getSectionURL($obBasketItem->getProductId()),
                'VOLUME' => $this->getVolume($obBasketItem->getProductId()),
                'PARENT' => $this->getParentElement($obBasketItem->getProductId())
            );
            $arBasketItems['SHIPMENT'][] = $arFields;
        }

        return $arBasketItems;
    }

    public function getServices()
    {
        if (!empty($this->arServices)) {
            return $this->arServices;
        }

        $arFilter = array(
            'IBLOCK_ID' => 37,
            'ACTIVE' => 'Y'
        );
        $arSelect = array(
            'ID', 'NAME', 'PREVIEW_TEXT', 'PROPERTY_SVG_SPRITE', 'PROPERTY_SVG_ANCHOR', 'CATALOG_GROUP_1'
        );
        $dbElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        $arServices = array();
        while ($arFields = $dbElement->GetNext()) {
            $arServices[$arFields['ID']] = array(
                'NAME' => $arFields['NAME'],
                'SVG_ANCHOR' => $arFields['PROPERTY_SVG_ANCHOR_VALUE'],
                'SVG_SPRITE' => $arFields['PROPERTY_SVG_SPRITE_VALUE'],
                '~PRICE' => $arFields['CATALOG_PRICE_1'],
                'PRICE' => number_format($arFields['CATALOG_PRICE_1'], 0, '', ' ') . '.–',
                'TEXT' => $arFields['PREVIEW_TEXT'],
            );
        }

        $this->arServices = $arServices;

        return $arServices;
    }

    public function getDeliveryServices()
    {
        if (!empty($this->arDeliveryServices)) {
            return $this->arDeliveryServices;
        }
        $arFilter = array(
            'IBLOCK_ID' => 38,
            'ACTIVE' => 'Y'
        );
        $arSelect = array(
            'ID', 'NAME', 'SORT', 'CATALOG_GROUP_1'
        );
        $dbElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        $arServices = array();
        while ($arFields = $dbElement->GetNext()) {
            $arServices[$arFields['SORT']] = array(
                'ID' => $arFields['ID'],
                'NAME' => $arFields['NAME'],
                '~PRICE' => $arFields['CATALOG_PRICE_1'],
                'PRICE' => number_format($arFields['CATALOG_PRICE_1'], 0, '', ' ') . ' руб.'
            );
        }

        $this->arDeliveryServices = $arServices;

        return $arServices;
    }


    protected function getElementName($intID){
        $parentEl=$this->getElement($this->getParentElement($intID));
        return $parentEl['FIELDS']['NAME'];
    }

    protected function getArticle($intID)
    {
        $arProperties = $this->getElement($intID)['PROPERTIES'];
        if (empty($arProperties['CML2_LINK'])) {
            return false;
        }
        $arProperties = $this->getElement($arProperties['CML2_LINK']['VALUE'])['PROPERTIES'];

        return $arProperties['CML2_ARTICLE']['VALUE'];
    }

    protected function getColor($intID)
    {
        // colors
        $arHLBlock = HighloadBlockTable::getById(21)->fetch();
        $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();

        $arColors = array();
        $rsData = $strEntityDataClass::getList();
        while ($arItem = $rsData->fetch()) {
            $arColors[$arItem['UF_1C_CODE']] = $arItem;
        }

        // reference
        $arProperties = $this->getElement($intID)['PROPERTIES'];
        $color=$arProperties['KOD_TSVETA']['VALUE'];
        $color=explode('#',$color)[1];
        return $arColors[$color]['UF_NAME'];
    }

    protected function getSize($intID){
        $arProperties = $this->getElement($intID)['PROPERTIES'];
        $arSize = $arProperties['RAZMER_STOLESHNITSY']['VALUE'];
        return $arSize;
    }

    protected function getImage($intID)
    {
        $arFields = $this->getElement($intID)['FIELDS'];

        if (empty($arFields['DETAIL_PICTURE'])) {
            $arProperties = $this->getElement($intID)['PROPERTIES'];

            $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
            $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
            $photoEntityDataClass = $obEntity->getDataClass();
            $rsData = $photoEntityDataClass::getList(array(
                'select' => array('UF_FILE'),
                'filter' => array('UF_XML_ID' => $arProperties['FOTOGRAFIYA_1']['~VALUE']),
                'limit' => '50',
            ));
            if ($arItem = $rsData->fetch()) {
                $arFields['DETAIL_PICTURE'] = $arItem['UF_FILE'];
            }
            if (empty($arFields['DETAIL_PICTURE'])) {
                return false;
            }
        }

        $arSize = array(
            'width' => 132,
            'height' => 132
        );
        $arImage = \CFile::ResizeImageGet($arFields['DETAIL_PICTURE'], $arSize, BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 1), false, 100);

        return $arImage['src'];
    }

    protected function getNameURL($intID)
    {
        $arProperties = $this->getElement($intID)['PROPERTIES'];
        if (empty($arProperties['CML2_LINK'])) {
            return false;
        }
        $arFields = $this->getElement($arProperties['CML2_LINK']['VALUE'])['FIELDS'];

        return $arFields['DETAIL_PAGE_URL'];
    }

    protected function getSectionName($intID)
    {
        $arProperties = $this->getElement($intID)['PROPERTIES'];
        if (empty($arProperties['CML2_LINK'])) {
            return false;
        }
        $arFields = $this->getElement($arProperties['CML2_LINK']['VALUE'])['FIELDS'];
        if (empty($arFields['IBLOCK_SECTION_ID'])) {
            return false;
        }
        $dbSection = CIBlockSection::GetByID($arFields['IBLOCK_SECTION_ID']);
        if ($arFields = $dbSection->GetNext()) {
            return $arFields['NAME'];
        }

        return false;
    }

    protected function getSectionURL($intID)
    {
        $arProperties = $this->getElement($intID)['PROPERTIES'];
        if (empty($arProperties['CML2_LINK'])) {
            return false;
        }
        $arFields = $this->getElement($arProperties['CML2_LINK']['VALUE'])['FIELDS'];
        if (empty($arFields['IBLOCK_SECTION_ID'])) {
            return false;
        }
        $dbSection = CIBlockSection::GetByID($arFields['IBLOCK_SECTION_ID']);
        if ($arFields = $dbSection->GetNext()) {
            return $arFields['SECTION_PAGE_URL'];
        }

        return false;
    }

    protected function getElement($intID)
    {
        if (!empty($this->arElements[$intID])) {
            return $this->arElements[$intID];
        }

        $dbElement = \CIBlockElement::GetByID($intID);
        if (!$obElement = $dbElement->GetNextElement()) {
            return false;
        }
        $arElement = array(
            'FIELDS' => $obElement->GetFields(),
            'PROPERTIES' => $obElement->GetProperties()
        );

        $this->arElements[$intID] = $arElement;

        return $arElement;
    }

    public function getVolume($intID)
    {
        $ID = $intID;
        $mxResult = CCatalogSku::GetProductInfo($intID);
        if (is_array($mxResult)) {
            $ID = $mxResult["ID"];
        }
        $arProperty = $this->getElement($ID)['PROPERTIES'];
        $arVolume = array();
        foreach($arProperty as $key => $value){
            if ((strpos($key, 'UPAKOVKA_') !== false) && !empty($value['~VALUE'])){
                $arPropKey = explode('_', $key);
                $arVolume[$arPropKey[1]] = $value["~VALUE"];
            }
        }

        return $arVolume;
    }

    protected function getParentElement($intID)
    {
        $arProperty = $this->getElement($intID)['PROPERTIES'];
        return $arProperty['CML2_LINK']['~VALUE'];
    }

    public function getDelivery()
    {
        $arDelivery = array();
        $dbDelivery = Delivery\Services\Table::getlist(array(
                'filter' => array(
                    'ACTIVE' => 'Y'
                )
            )
        );
        while ($arFields = $dbDelivery->fetch()) {
            if ($arFields['ID'] == 1) {
                continue;
            }
            if (!isset($arFields['CONFIG']['MAIN']['PRICE'])) {
                $arFields['CONFIG']['MAIN']['PRICE'] = 0;
            }
            $arFields['~PRICE'] = $arFields['CONFIG']['MAIN']['PRICE'];
            $arFields['PRICE'] = \CCurrencyLang::CurrencyFormat($arFields['CONFIG']['MAIN']['PRICE'], $arFields['CONFIG']['MAIN']['CURRENCY']);
            $arDelivery[] = $arFields;
        }

        return $arDelivery;
    }

    public function SetCoupon()
    {
        $obContext = Context::getCurrent();
        $obRequest = $obContext->getRequest();

        $strCoupon = $obRequest->get('promo');

        if (!empty($strCoupon)) {
            DiscountCouponsManager::add($strCoupon);
            $_SESSION['COUPON'] = $strCoupon;
            $_SESSION['~OLD_PRICE'] = $this->intTotalSum;
            $_SESSION['~OLD_PRICE'] -= $this->intDeliveryServicesSum;
            $_SESSION['OLD_PRICE'] = number_format($_SESSION['~OLD_PRICE'], 0, '', ' ') . '.–';
            // $_SESSION['OLD_PRICE1']= $this->intTotalSum;
            // $_SESSION['OLD_PRICE2']= $this->intDeliveryServicesSum;
            \CSaleBasket::UpdateBasketPrices(Fuser::getId(), $obContext->getSite());
        }
    }

    public function getPaySystems()
    {
        $arOrder = array(
            'SORT' => 'ASC'
        );
        $arFilter = array(
            'LID' => SITE_ID,
            'ACTIVE' => 'Y'
        );
        $dbPayment = \CSalePaySystem::GetList($arOrder, $arFilter);
        $arPayment = array();
        while ($arFields = $dbPayment->GetNext()) {
            if ($arFields['DESCRIPTION'] == 'hide') {
                continue;
            }
            $arPayment[] = $arFields;
        }

        return $arPayment;
    }

    public function getDPDCoast($arProduct, $bSelfDelivery = false, $intLocationID = 0)
    {
        $sumVolume = 0;
        $notPackQuantity = $arProduct['QUANTITY'];
        $arParcel = array();
        $sumWeight = 0;
        while ($notPackQuantity > 0) {
            foreach ($arProduct['PACK'] as $pack) {
                if ($notPackQuantity >= $pack['QUANTITY']) {
                    $sumVolume += $pack['LENGTH'] * $pack['WIDTH'] * $pack['HEIGHT'];
                    $notPackQuantity -= $pack['QUANTITY'];
                    $sumWeight += $pack['WEIGHT'];
                    $arParcel[] = array(
                        'weight'=> floatval($pack['WEIGHT']),
                        'length'=> floatval($pack['LENGTH']) * 100,
                        'width'=> floatval($pack['WIDTH']) * 100,
                        'height'=> floatval($pack['HEIGHT']) * 100,
                        'quantity'=>1,
                    );
                    break;
                }
            }
        }

        //$sumWeight = $this->getWeight($sumVolume);
        $this->intWeight = $sumWeight;

        $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/dpd_cities'))->fetch();
        $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();

        if ($intLocationID == 0) {
            if (empty($_SESSION['DPD_CITY'])) {
                $arGeo = ALX_GeoIP::GetAddr();
                $cityObj = new CCity();

                $arResult['mapParams']['yandex_lat'] = $arGeo['lat'];
                $arResult['mapParams']['yandex_lon'] = $arGeo['lng'];

                $arHLBlock = HighloadBlockTable::getById(22)->fetch();
                $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
                $strEntityDataClass = $obEntity->getDataClass();

                $rsData = $strEntityDataClass::getList(array(
                    'select' => array('UF_CITYCODE', 'UF_CITYID'),
                    'filter' => array(
                        'UF_CITYNAME' => $arGeo['city']
                    ),
                ));
                if ($arItem = $rsData->fetch()) {
                    $intCityCode = $arItem['UF_CITYCODE'];
                    $_SESSION['DPD_CITY'] = $intCityCode;
                }
            }

            $intLocationID = $_SESSION['DPD_CITY'];
        }

        $rsData = $strEntityDataClass::getList(array(
            'select' => array('UF_CITYID', 'UF_COUNTRYCODE'),
            'filter' => array(
                'UF_CITYCODE' => $intLocationID
            ),
        ));

        if ($arItem = $rsData->fetch()) {
            $intCityId = $arItem['UF_CITYID'];

            $oDPD = new DPD_service_my;

            $arData = array(
                'pickup' => array(
                    'cityId' => 49694167 //'Санкт-Петербург'
                ),
                'delivery' => array(
                    'cityId' => (int)$intCityId
                ),
                'serviceCode' => $arItem['UF_COUNTRYCODE'] == 'KZ' ? 'ECU' : 'MXO',
                'selfPickup' => false,
                'selfDelivery' => true,
                'parcel' => $arParcel,
            );

            $obRequest = Context::getCurrent()->getRequest();

            $arData['selfDelivery'] = false;

            if ($obRequest->get('delivery') == 8 || $bSelfDelivery === true) { // доставка до двери
                $arData['selfDelivery'] = false;
            }else{
                $arData['selfDelivery'] = true;
            }

            $arResult = $oDPD->getServiceCost($arData);

            if (!empty($arResult))
                $arResult['cost'] = $this->getCoeffPrice($sumVolume, $arResult['cost']);
            if (!$arResult) {

                unset($arData['serviceCode']);
                $arResult = $oDPD->getServiceCost($arData);
                if ($arResult && !empty($arResult[0])) {
                    foreach ($arResult as $deliveryServices) {
                        if (!isset($min_price)) {
                            $min_price = $deliveryServices['cost'];
                            $min_days = $deliveryServices['days'];
                        } else if ($deliveryServices['cost'] < $min_price) {
                            $min_price = $deliveryServices['cost'];
                            $min_days = $deliveryServices['days'];
                        }
                    }
                    $arResult['cost'] = $min_price;
                    $arResult['cost'] = $this->getCoeffPrice($sumVolume, $arResult['cost']);
                    $arResult['days'] = $min_days;
                }
            }

            // фиксированные данные для Москвы и Питера
            if ($intCityId == 49694167 || $intCityId == 49694102) {
                if ($arData['selfDelivery']) {
                    $intPrice = PICKUP_COAST;
                } else {
                    $intPrice = $intCityId == 49694167 ? DELIVERY_COAST_SPB : DELIVERY_COAST_MSK;
                }
            } else {
                // $arResult = $oDPD->getServiceCost($arData);
                $intPrice = ceil($arResult['cost'] / 10) * 10;

            }

            return $intPrice;
        }
    }

    public function getStatus()
    {
        $obRequest = Context::getCurrent()->getRequest();
        $strStatus = 'preparation';
        if ($obRequest->get('go') == 'Y' and $this->getBasketItemsCount()>0) {
            $strStatus = 'order';
        }

        return $strStatus;
    }

    protected function getPropertyByCode($propertyCollection, $code)
    {
        foreach ($propertyCollection as $property) {
            if ($property->getField('CODE') == $code)
                return $property;
        }
    }

    public function getUserInfo()
    {
        global $USER;

        if (!$USER->IsAuthorized()) {
            return true;
        }

        $dbUser = CUser::GetByID($USER->GetID());
        $arUser = $dbUser->Fetch();

        if (empty($_SESSION['ORDER']['FIELDS']['name'])) {
            $_SESSION['ORDER']['FIELDS']['name'] = $arUser['NAME'];
        }

        if (empty($_SESSION['ORDER']['FIELDS']['email'])) {
            $_SESSION['ORDER']['FIELDS']['email'] = $arUser['EMAIL'];
        }

        if (empty($_SESSION['ORDER']['FIELDS']['phone'])) {
            $_SESSION['ORDER']['FIELDS']['phone'] = $arUser['PERSONAL_PHONE'];
        }

        return true;
    }

    public function setSourceTerminals($strSource = '')
    {
        if ($strSource != 'HL') {
            $this->strSourceTerminals = 'DPD';
        } else {
            $this->strSourceTerminals = 'HL';
        }
    }

    /**
     * Инициализация параметров расчета веса товара
     */
    private function initWeightCoeff()
    {
        if (empty($this->weightByVolume)) {
            \Bitrix\Main\Loader::includeModule('highloadblock');

            $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(SETTINGS_HL_ID)->fetch();
            $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
            $strEntityDataClass = $obEntity->getDataClass();

            $rsData = $strEntityDataClass::getList([
                'select' => [
                    'ID',
                    'UF_VOLUME_BIG',
                    'UF_VOLUME_MEDIUM',
                    'UF_VOLUME_SMALL',
                    'UF_COEFF_BIG',
                    'UF_COEFF_MEDIUM',
                    'UF_COEFF_SMALL',
                ]
            ]);
            if ($arItem = $rsData->fetch()) {
                $this->weightByVolume['VOLUME_BIG'] = $arItem['UF_VOLUME_BIG'];
                $this->weightByVolume['VOLUME_MEDIUM'] = $arItem['UF_VOLUME_MEDIUM'];
                $this->weightByVolume['VOLUME_SMALL'] = $arItem['UF_VOLUME_SMALL'];
                $this->weightByVolume['COEFF_BIG'] = $arItem['UF_COEFF_BIG'];
                $this->weightByVolume['COEFF_MEDIUM'] = $arItem['UF_COEFF_MEDIUM'];
                $this->weightByVolume['COEFF_SMALL'] = $arItem['UF_COEFF_SMALL'];
            }
        }
    }

    /**
     * Инициализация параметров расчета веса товара для пересчета учитывая коофицент
     */

    private function initWeightCoeffPrice()
    {
        if (empty($this->weightByVolume)) {
            \Bitrix\Main\Loader::includeModule('highloadblock');

            $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(SETTINGS_HL_ID)->fetch();
            $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
            $strEntityDataClass = $obEntity->getDataClass();

            $rsData = $strEntityDataClass::getList([
                'select' => [
                    'ID',
                    'UF_VOLUME_BIG',
                    'UF_VOLUME_MEDIUM',
                    'UF_VOLUME_SMALL',
                    'UF_COEFF_BIG_PRICE',
                    'UF_COEFF_MEDIUM_PRIC',
                    'UF_COEFF_SMALL_PRICE',
                ]
            ]);
            if ($arItem = $rsData->fetch()) {
                $this->weightByVolume['VOLUME_BIG'] = $arItem['UF_VOLUME_BIG'];
                $this->weightByVolume['VOLUME_MEDIUM'] = $arItem['UF_VOLUME_MEDIUM'];
                $this->weightByVolume['VOLUME_SMALL'] = $arItem['UF_VOLUME_SMALL'];
                $this->weightByVolume['COEFF_BIG'] = $arItem['UF_COEFF_BIG_PRICE'];
                $this->weightByVolume['COEFF_MEDIUM'] = $arItem['UF_COEFF_MEDIUM_PRIC'];
                $this->weightByVolume['COEFF_SMALL'] = $arItem['UF_COEFF_SMALL_PRICE'];
            }
        }
    }

    /**
     * Расчет веса упаковки на основе объёма
     * @param $volume
     * @return float|int
     */
    private function getWeight($volume)
    {
        $this->initWeightCoeff();

        if ($volume >= $this->weightByVolume['VOLUME_BIG']) {
            return $volume * $this->weightByVolume['COEFF_BIG'];
        } elseif ($volume >= $this->weightByVolume['VOLUME_MEDIUM']) {
            return $volume * $this->weightByVolume['COEFF_MEDIUM'];
        } else {
            return $volume * $this->weightByVolume['COEFF_SMALL'];
        }
    }

    /**
     * Расчет стоимости доставки учитывая наш коэффициент цены
     * @param $volume
     * @param $price
     * @return float|int
     */
    private function getCoeffPrice($volume,$price)
    {
        $this->initWeightCoeffPrice();

        if ($volume >= $this->weightByVolume['VOLUME_BIG']) {
            return $price * $this->weightByVolume['COEFF_BIG'];
        } elseif ($volume >= $this->weightByVolume['VOLUME_MEDIUM']) {
            return $price * $this->weightByVolume['COEFF_MEDIUM'];
        } else {
            return $price * $this->weightByVolume['COEFF_SMALL'];
        }
    }
}
