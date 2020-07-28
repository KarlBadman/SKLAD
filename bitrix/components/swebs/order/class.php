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
            $photoEntityDataClass = $obEntity->getDataClass();;
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
				if(is_array($mxResult)){
					$ID = $mxResult["ID"];
				}	
        $arProperty = $this->getElement($ID)['PROPERTIES'];
				
        // $arVolume = array(
            // 1 => $arProperty['UPAKOVKA_1_2']['~VALUE'],
            // 2 => $arProperty['UPAKOVKA_1_2']['~VALUE']
        // );
        // if (!empty($arProperty['UPAKOVKA_3_5']['~VALUE'])) {
            // $arVolume[3] = $arProperty['UPAKOVKA_3_5']['~VALUE'];
            // $arVolume[4] = $arProperty['UPAKOVKA_3_5']['~VALUE'];
            // $arVolume[5] = $arProperty['UPAKOVKA_3_5']['~VALUE'];
        // } elseif (!empty($arProperty['UPAKOVKA_3_4']['~VALUE'])) {
            // $arVolume[3] = $arProperty['UPAKOVKA_3_4']['~VALUE'];
            // $arVolume[4] = $arProperty['UPAKOVKA_3_4']['~VALUE'];
        // }
        $arVolume = array();
				foreach($arProperty as $key => $value){
					$E = (substr($key, -2) == '_1');
					$str = substr($key, 0, -2);
					$pos = strpos($str,'UPAKOVKA_');
					if($pos !== false && $pos == 0 && $E && $value["~VALUE"]){
						$code = str_replace('UPAKOVKA_','',$str);
						$arVolume[$code] = $value["~VALUE"];
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

    public function getDPDTerminals($intCityCode = 0)
    {
        $arResult = array();
        $obRequest = Context::getCurrent()->getRequest();

        // location and terminals
        $arResult['mapParams'] = array(
            'yandex_lat' => 0,
            'yandex_lon' => 0,
            'yandex_scale' => 9,
            'PLACEMARKS' => array()
        );
        if ($intCityCode == 0) {
            $intCityCode = $obRequest->get('intLocationID');
        }
        if (empty($intCityCode)) {
            $intCityCode = $obRequest->get('city_id');
        }
        if (empty($intCityCode)) {
            $intCityCode = $_SESSION['DPD_CITY'];
        } else {
            $_SESSION['DPD_CITY'] = $intCityCode;
        }
        if (empty($intCityCode)) {
            $arGeo = ALX_GeoIP::GetAddr();


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

        if (empty($intCityCode)) {
            ShowError('Укажите местоположение');
        }

        $oDPD = new DPD_service_my;
        $arData = array(
            'cityCode' => $intCityCode
        );

        $arTerminals = $oDPD->getTerminal2($arData);
//        $arTerminals = $arTerminals['parcelShop'];
        $arTerminals = $arTerminals['terminal'];
        $fLat = 0;
        $fLon = 0;
        if (!empty($arTerminals['address'])) {
            $arTerminals = array($arTerminals);
        }

        foreach ($arTerminals as $arItem) {
            if ($arItem['limits']['maxShipmentWeight'] < $this->intWeight) {
                continue;
            }
            if ($arItem['address']['cityCode'] !== $intCityCode) {
                continue;
            }
            $arResult['TERMINAL'][] = $arItem;
        }
 
        $countTerminals=0;
        foreach ($arResult['TERMINAL'] as $arItem) {
            $fLat += $arItem['geoCoordinates']['latitude'];
            $fLon += $arItem['geoCoordinates']['longitude'];
            $countTerminals+=1;
            /*if ($arItem['state'] != 'Open') {
                continue;
            }*/

            $arResult['mapParams']['PLACEMARKS'][] = array(
                'LON' => $arItem['geoCoordinates']['longitude'],
                'LAT' => $arItem['geoCoordinates']['latitude'],
                'TEXT' => $arItem['address']['descript']
            );
        }

        if ($arResult['mapParams']['yandex_lat'] == 0) {
            $arResult['mapParams']['yandex_lat'] = $fLat / $countTerminals;
            $arResult['mapParams']['yandex_lon'] = $fLon / $countTerminals;
        }
        return $arResult;
    }

    public function getDPDTerminalsNew($intCityCode = 0)
    {
        $arResult = array();
        $obRequest = Context::getCurrent()->getRequest();
        if ($intCityCode == 0) {
            $intCityCode = $obRequest->get('intLocationID');
        }
        if (empty($intCityCode)) {
            $intCityCode = $obRequest->get('city_id');
        }
        if (empty($intCityCode)) {
            $intCityCode = $_SESSION['DPD_CITY'];
        } else {
            $_SESSION['DPD_CITY'] = $intCityCode;
        }
        if (empty($intCityCode)) {
            $arGeo = ALX_GeoIP::GetAddr();
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

        if (empty($intCityCode)) {
            ShowError('Укажите местоположение');
        }

        $oDPD = new DPD_service_my;
        $arTerminals = $oDPD->getTerminal2();

//        $arTerminals = $obTerminals->return;

        if (!empty($arTerminals['city'])) {
            $arTerminals = array($arTerminals);
        }

        foreach ($arTerminals as $arItem) {
            if ($arItem['city']['cityCode'] != $intCityCode) {
                continue;
            }
            $arResult[] = $arItem;
        }

        return $arResult;
    }

    public function getDPDCoast($arItems, $bSelfDelivery = false, $intLocationID = 0)
    {
        $arData = array();
        $intVolume = 0;
        foreach ($arItems as $arItem) {
          if (empty($arData[$arItem['PARENT']])) {
              $arData[$arItem['PARENT']] = 0;
          }
					
					krsort($arItem['VOLUME']);
					if(empty($arItem['VOLUME'][$arItem['QUANTITY']])){
						foreach($arItem['VOLUME'] as $q => $val){
							if($arItem['QUANTITY'] == 0)
								break;
							while($arItem['QUANTITY'] >= $q){
								$arData[$arItem['PARENT']] += $arItem['VOLUME'][$q];
								$arItem['QUANTITY'] = $arItem['QUANTITY'] - $q;
							}	
						}	
					}
					else {
						$arData[$arItem['PARENT']] += $arItem['VOLUME'][$arItem['QUANTITY']];
					}					
					// $arItem['QUANTITY']					
					
					
            // if (empty($arItem['VOLUME'][$arItem['QUANTITY']])) {
                // $arData[$arItem['PARENT']] += $arItem['VOLUME'][1] * $arItem['QUANTITY'];
            // } else {
                // $arData[$arItem['PARENT']] += $arItem['VOLUME'][$arItem['QUANTITY']];
            // }
        }
        foreach ($arData as $intValue) {
            $intVolume += $intValue;
        }
				
        // $intWeight = $intVolume * 250;
        $intWeight = $intVolume;
        $intVolume = $intVolume / 250;
				
        $this->intWeight = $intWeight;

        $arHLBlock = HighloadBlockTable::getById(22)->fetch();
        $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();

        if ($intLocationID == 0) {

            if (empty($_SESSION['DPD_CITY'])) {
                $arGeo = ALX_GeoIP::GetAddr();


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
            'select' => array('UF_CITYID'),
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
                    'cityId' => $intCityId
                ),
                'serviceCode' => 'ECN',
                'selfPickup' => false,
                'selfDelivery' => true,
                'weight' => $intWeight,
                'volume' => $intVolume
            );
            $obRequest = Context::getCurrent()->getRequest();

            $arData['selfDelivery'] = false;

            if ($obRequest->get('delivery') == 8 || $bSelfDelivery === true) { // доставка до двери
                $arData['selfDelivery'] = false;
            }else{
                $arData['selfDelivery'] = true;
            }

            /*echo '<div id="debug">bla';
            echo $obRequest->get('delivery') . '<pre>';
            print_r($arData);
            echo '</pre></div>';*/

            // фиксированные данные для Москвы и Питера
            if ($intCityId == 49694167 || $intCityId == 49694102) {
                if ($arData['selfDelivery']) {
                    $intPrice = PICKUP_COAST;
                } else {
                    $intPrice = DELIVERY_COAST;
                }
            } else {
                $arResult = $oDPD->getServiceCost($arData);
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

    public function doOrder()
    {
        if( $this->getBasketItemsCount()<1){
            return false;
        }
        global $USER;

        $obContext = Context::getCurrent();
        $obRequest = $obContext->getRequest();
        $intSiteId = $obContext->getSite();

        $intLegal = trim($obRequest->get('legal'));
        $strCity = trim($obRequest->get('city_name'));
        $strName = trim($obRequest->get('name'));
        $strEmail = trim($obRequest->get('email'));
        $strPhone = trim($obRequest->get('phone'));
        $intDeliveryID = trim($obRequest->get('delivery'));
        $intDeliveryPrice = trim($obRequest->get('delivery_price'));
        $intPaymentID = trim($obRequest->get('payment'));
        $strCompany = '';
        $strINN = '';

        /*if (empty($strPhone) || empty($strName) || empty($intDeliveryID) || empty($intPaymentID)) {
            return false; // это обязательные поля
        }*/

        if (empty($strEmail)) {
            $strEmail = 'anonym@dsklad.ru';
        }

        $intPersonType = 1; // физлицо

        if ($intLegal == 1) {
            $intPersonType = 2; // юрлицо
            $strCompany = trim($obRequest->get('company'));
            $strINN = trim($obRequest->get('vat'));
        }

        $strCurrencyCode = Option::get('sale', 'default_currency', 'RUB');

        DiscountCouponsManager::init();

        $intUserID = 0;

        // address
        $strAddress = '';
        if ($intDeliveryID == 7) { // самовывоз
            $arData = $this->getDPDTerminals();
            foreach ($arData['TERMINAL'] as $arItem) {
                if ($arItem['terminalCode'] == $obRequest->get('point')) {
                    $strAddress = $arItem['address']['streetAbbr'] . ' ' . $arItem['address']['street'] . ' ' . $arItem['address']['houseNo'];
                }
            }
        } else {
            $strAddress = $obRequest->get('address');
        }


        if ($USER->GetID() == NULL) {
            $rsUser = CUser::GetByLogin($strEmail);
            if ($arUser = $rsUser->Fetch()) {
                // Пользователь существует
                $intUserID = $arUser['ID'];
            } else {
                // Пользователь новый
                $strPasswd = uniqid();
                $arFields = array(
                    'EMAIL' => $strEmail,
                    'NAME' => $strName,
                    'LOGIN' => $strEmail,
                    'PERSONAL_PHONE' => $strPhone,
                    'PERSONAL_STREET' => $strAddress,
                    'PASSWORD' => $strPasswd,
                    'CONFIRM_PASSWORD' => $strPasswd
                );
                $intUserID = $USER->Add($arFields);
                // mail
                if ($intUserID !== false) {
                    $arMailFields = array(
                        'EVENT_NAME' => 'NEW_SALE_USER',
                        'LID' => Context::getCurrent()->getSite(),
                        'C_FIELDS' => array(
                            'NAME' => $strName,
                            'EMAIL' => $strEmail,
                            'LOGIN' => $strEmail,
                            'PASSWORD' => $strPasswd
                        )
                    );
                    Event::send($arMailFields);
                }
            }
            $USER->Authorize($intUserID);
        } else {
            $intUserID = $USER->GetID();
        }

        if (!$intUserID) {
            $intUserID = \CSaleUser::GetAnonymousUserID();
        }

        $obOrder = Order::create($intSiteId, $intUserID);
        $obOrder->setPersonTypeId($intPersonType);
        $obBasket = Basket::loadItemsForFUser(Fuser::getId(), $intSiteId)->getOrderableItems();
        $obOrder->setBasket($obBasket);

        // delivery
        $arDelivery = $this->getDelivery();
        $strDeliveryName = '';
        foreach ($arDelivery as $arItem) {
            if ($arItem['ID'] == $intDeliveryID) {
                $strDeliveryName = $arItem['NAME'];
                break;
            }
        }

        // Shipment
        $shipmentCollection = $obOrder->getShipmentCollection();
        $shipment = $shipmentCollection->createItem();
        $shipment->setFields(array(
            'DELIVERY_ID' => $intDeliveryID,
            'DELIVERY_NAME' => $strDeliveryName,
            'CURRENCY' => $obOrder->getCurrency(),
            'BASE_PRICE_DELIVERY' => $intDeliveryPrice
        ));

        $shipmentItemCollection = $shipment->getShipmentItemCollection();

        foreach ($obOrder->getBasket() as $item) {
            $shipmentItem = $shipmentItemCollection->createItem($item);
            $shipmentItem->setQuantity($item->getQuantity());
        }

        // Payment
        $arPaySystem = $this->getPaySystems();
        $strPayName = '';
        foreach ($arPaySystem as $arItem) {
            if ($arItem['ID'] == $intPaymentID) {
                $strPayName = str_replace('<br>', '', $arItem['~NAME']);
            }
        }
        $paymentCollection = $obOrder->getPaymentCollection();
        $extPayment = $paymentCollection->createItem();
        $extPayment->setFields(array(
            'PAY_SYSTEM_ID' => $intPaymentID,
            'PAY_SYSTEM_NAME' => $strPayName,
            'SUM' => $obOrder->getPrice()
        ));

        // property
        $obOrder->doFinalAction(true);
        $propertyCollection = $obOrder->getPropertyCollection();

        if ($intPersonType == 1) {// физлицо
            // город
            $obProperty = $this->getPropertyByCode($propertyCollection, 'F_CITY');
            $obProperty->setValue($strCity);
            // имя
            $obProperty = $this->getPropertyByCode($propertyCollection, 'F_NAME');
            $obProperty->setValue($strName);
            // email
            $obProperty = $this->getPropertyByCode($propertyCollection, 'F_EMAIL');
            $obProperty->setValue($strEmail);
            // телефон
            $obProperty = $this->getPropertyByCode($propertyCollection, 'F_PHONE');
            $obProperty->setValue($strPhone);
            // адрес
            $obProperty = $this->getPropertyByCode($propertyCollection, 'F_ADDRESS');
            $obProperty->setValue($strAddress);
            // комментарий курьеру
            $obProperty = $this->getPropertyByCode($propertyCollection, 'F_ADDRESS_COMMENT');
            $obProperty->setValue(trim($obRequest->get('delivery_comment')));
        } else {// юрлицо
            // город
            $obProperty = $this->getPropertyByCode($propertyCollection, 'U_CITY');
            $obProperty->setValue($strCity);
            // контактное лицо
            $obProperty = $this->getPropertyByCode($propertyCollection, 'U_NAME');
            $obProperty->setValue($strName);
            // email
            $obProperty = $this->getPropertyByCode($propertyCollection, 'U_EMAIL');
            $obProperty->setValue($strEmail);
            // телефон
            $obProperty = $this->getPropertyByCode($propertyCollection, 'U_PHONE');
            $obProperty->setValue($strPhone);
            // компания
            $obProperty = $this->getPropertyByCode($propertyCollection, 'U_COMPANY');
            $obProperty->setValue($strCompany);
            // инн
            $obProperty = $this->getPropertyByCode($propertyCollection, 'U_INN');
            $obProperty->setValue($strINN);
            // адрес
            $obProperty = $this->getPropertyByCode($propertyCollection, 'U_ADDRESS');
            $obProperty->setValue($strAddress);
            // комментарий курьеру
            $obProperty = $this->getPropertyByCode($propertyCollection, 'U_ADDRESS_COMMENT');
            $obProperty->setValue(trim($obRequest->get('delivery_comment')));
        }

        $obOrder->setField('CURRENCY', $strCurrencyCode);
        $obOrder->setField('USER_DESCRIPTION', trim($obRequest->get('order-comment')));
        $_SESSION['ORDER_PAY_SYSTEM_ID']= $obOrder->getPaymentSystemId();
        $_SESSION['ORDER_DATA']['PRICE'] = $obOrder->getPrice();
        $_SESSION['ORDER_DATA']['PRICE_DELIVERY'] = $obOrder->getDeliveryPrice();
        $_SESSION['ORDER_DATA']['TAX_VALUE'] = $obOrder->getTaxPrice();
        $obRes = $obOrder->save();
        $_SESSION['ORDER_ID'] = $obOrder->GetId();


        // mail
        /*if (is_numeric($_SESSION['ORDER_ID']) && $_SESSION['ORDER_ID'] > 0) {
            $obBasket = $obOrder->getBasket();
            $arOrderList = array();
            foreach ($obBasket as $obBasketItem) {
                $arOrderList[] = $obBasketItem->getField('NAME');
            }
            $arMailFields = array(
                'EVENT_NAME' => 'SALE_NEW_ORDER',
                'LID' => Context::getCurrent()->getSite(),
                'C_FIELDS' => array(
                    'ORDER_ID' => $_SESSION['ORDER_ID'],
                    'ORDER_DATE' => new \Bitrix\Main\Type\DateTime(),
                    'ORDER_USER' => $strName,
                    'PRICE' => $obOrder->getPrice(),
                    'EMAIL' => $strEmail,
                    'ORDER_LIST' => implode('<br>', $arOrderList),
                    'SALE_EMAIL' => 'info@dsklad.ru'
                )
            );
            Event::send($arMailFields);
        }*/

        return $obRes;
    }
}