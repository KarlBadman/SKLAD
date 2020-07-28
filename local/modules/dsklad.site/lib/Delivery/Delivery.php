<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 21.01.19
 * Time: 16:01
 */

namespace Dsklad;

use \Bitrix\Highloadblock\HighloadBlockTable;
use \Bitrix\Main\Context;
use \Bitrix\Main\Mail\Event;

class Delivery
{
    static $warrantyProductId = null;
    static $arSectionParentsCache = [];

    static $weightByVolume = [];

    public $intTotalSum = 0;
    public $intTotalSumServices = 0;
    public $intTotalVolume = 0;
    protected $arElements = array();
    protected $intWeight = 0;
    protected $intDeliveryServicesSum = 0;
    protected $arServices = array();
    protected $arDeliveryServices = array();
    protected $strSourceTerminals = 'DPD'; // DPD - get terminals from DPD site, HL - get terminals from HL-block


    private function getCoeffPrice($volume,$price)
    {
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
            $weightByVolume['VOLUME_BIG'] = $arItem['UF_VOLUME_BIG'];
            $weightByVolume['VOLUME_MEDIUM'] = $arItem['UF_VOLUME_MEDIUM'];
            $weightByVolume['VOLUME_SMALL'] = $arItem['UF_VOLUME_SMALL'];
            $weightByVolume['COEFF_BIG'] = $arItem['UF_COEFF_BIG_PRICE'];
            $weightByVolume['COEFF_MEDIUM'] = $arItem['UF_COEFF_MEDIUM_PRIC'];
            $weightByVolume['COEFF_SMALL'] = $arItem['UF_COEFF_SMALL_PRICE'];
        }

        $priceDel = 0;

        if ($volume >= $weightByVolume['VOLUME_BIG']) {
            $priceDel =  $price * $weightByVolume['COEFF_BIG'];
        } elseif ($volume >= $weightByVolume['VOLUME_MEDIUM']) {
            $priceDel = $price * $weightByVolume['COEFF_MEDIUM'];
        } else {
            $priceDel = $price * $weightByVolume['COEFF_SMALL'];
        }

        return $priceDel;
    }

    public function getDPDCoast($arItems=array(),$totalPrice=0,$conf = array(),$noski = false, $intLocationID = 0, $allData = false)
    {
        $sumVolume = 0;
        $arParcel = array();
        foreach ($arItems as $arProduct) {
            if(empty($arProduct['PACK'])){
                global $DB;
                $exception_type = 'dpd_delivery';
                $exception_entity = 'gabarits_isempty_incart';
                $strSql = 'SELECT id FROM xtra_log WHERE entity_id = ' . $arProduct['PRODUCT_ID'] . ' AND exception_type="' . $exception_type . '" AND exception_entity="' . $exception_entity . '"';
                $exist = $DB->Query($strSql);
                if(!$exist->Fetch()){
                    $arFields = array(
                        "entity_type"             => "'" . 'product' . "'",
                        "entity_id"                    => $arProduct['PRODUCT_ID'],
                        "exception_type"                 => "'" . $exception_type . "'",
                        "exception_entity"                  => "'" . $exception_entity . "'"
                    );
                    $LOG_ID = $DB->Insert("xtra_log", $arFields);

                    if(intval($LOG_ID)){
                        $obContext = Context::getCurrent();
                        // goes to email
                        $arFields['ID'] = $LOG_ID;
                        $arFields['COMMENT'] = 'Исключение зафиксировано в getDeliveryCoast в корзине пользователя! У товара нет габаритов.';
                        $arMailFields = array(
                            'EVENT_NAME' => 'LOGGING',
                            'LID' => $obContext->getSite(),
                            'C_FIELDS' => $arFields
                        );
                        Event::send($arMailFields);
                    }
                }
                return '-';
            }
            $notPackQuantity = $arProduct['QUANTITY'];

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
        }

        $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/dpd_cities'))->fetch();
        $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();

        if ($intLocationID == 0) {

            if (empty($_SESSION['DPD_CITY'])) {

                \Bitrix\Main\Loader::includeModule('altasib.geoip');
                $arGeo = \ALX_GeoIP::GetAddr();
                $cityObj = new \CCity();
                $arThisCity = $cityObj->GetFullInfo();

                $arResult['mapParams']['yandex_lat'] = $arGeo['lat'];
                $arResult['mapParams']['yandex_lon'] = $arGeo['lng'];

                $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/dpd_cities'))->fetch();
                $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
                $strEntityDataClass = $obEntity->getDataClass();

                $rsData = $strEntityDataClass::getList(array(
                    'select' => array('UF_CITYCODE', 'UF_CITYID'),
                    'filter' => array(
                        'UF_CITYNAME' => $arThisCity['CITY_NAME']['VALUE']
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

            if($noski || $arItem['UF_COUNTRYCODE'] == 'BY' || $arItem['UF_COUNTRYCODE'] == 'KZ'){
                $conf['MAIN']['DPD_COD'] = 'PCL';
            }

            $arData = array(
                'pickup' => array(
                    'cityId' => DPD_CITY_ID_FROM // отправка из города 'Москва'
                ),
                'delivery' => array(
                    'cityId' => (int)$intCityId
                ),
                'serviceCode' => $conf['MAIN']['DPD_COD'],
                'selfPickup' => false,
                'selfDelivery' => true,
                'parcel' => $arParcel,
            );
    
            if ($conf['MAIN']['DOOR'] != 'Y') { // доставка до двери
                $arData['selfDelivery'] = true;
            } else {
                $arData['selfDelivery'] = false;
            }

            // фиксированные данные для Москвы и Питера

            if ($intCityId == 49694167 && $conf['MAIN']['NEW_COF'] != 'Y' && !$noski) {
                $pickupCost = $conf['MAIN']['PRICE_SPB'];
                $deliveryCost = $conf['MAIN']['PRICE_SPB_CUR'];
                $deyDelivery = $conf['MAIN']['DEY_SPB'];
                $serviceName = 'DPD Online Max';
            }elseif ($intCityId == 49694102 && $conf['MAIN']['NEW_COF'] != 'Y' && !$noski) {
                $pickupCost = $conf['MAIN']['PRICE_MOS'];
                $deliveryCost = $conf['MAIN']['PRICE_MOS_CUR'];
                $deyDelivery = $conf['MAIN']['DEY_MOS'];
                $serviceName = 'DPD Online Max';
            }

            if (($intCityId == 49694167 || $intCityId == 49694102) && $allData === false && $conf['MAIN']['NEW_COF'] != 'Y' && !$noski) {
                if ($arData['selfDelivery']) {
                    $intPrice = $pickupCost;
                } else {
                    $intPrice = $deliveryCost;
                }
            } else {
                $arResult = $oDPD->getServiceCost($arData);

                if (!empty($arResult)) {
                    $arResult['cost'] = Delivery::getCoeffPrice($sumVolume, $arResult['cost']);

                    if ($conf['MAIN']['NEW_COF'] == 'Y') {
                        $arResult['cost'] = $arResult['cost'] + ($totalPrice * UR_DELIVERY_COEF_PERCENT) + UR_DELIVERY_COEF;
                    }
                }
                if (!$arResult) {
                    unset($arData['serviceCode']);
                    $arResult = $oDPD->getServiceCost($arData);

                    if ($arResult && !empty($arResult[0])) {
                        foreach ($arResult as $deliveryServices) {
                            if (!isset($min_price)) {
                                $min_price = $deliveryServices['cost'];
                                $min_days = $deliveryServices['days'];
                                $serviceName = $deliveryServices['serviceName'];
                            } else if ($deliveryServices['cost'] < $min_price) {
                                $min_price = $deliveryServices['cost'];
                                $min_days = $deliveryServices['days'];
                                $serviceName = $deliveryServices['serviceName'];
                            }
                        }

                        $arResult['cost'] = $min_price;

                        $arResult['cost'] = Delivery::getCoeffPrice($sumVolume, $arResult['cost']);

                        if($conf['MAIN']['NEW_COF'] == 'Y' && $arResult['cost'] > 0){
                            $arResult['cost'] = $arResult['cost'] + ($totalPrice * UR_DELIVERY_COEF_PERCENT) + UR_DELIVERY_COEF;
                        }
                        $arResult['days'] = $min_days;
                        $arResult['serviceName'] = $serviceName;
                        $arResult['dpd_status'] = 1;
                    }
                }

                $intPrice = ceil($arResult['cost'] / 10) * 10;

                if ($intCityId == 49694167 || $intCityId == 49694102) {
                    if ($arData['selfDelivery']) {
                        $intPrice = $pickupCost;
                    } else {
                        $intPrice = $deliveryCost;
                    }
                }else{
                    $intPrice = (round($intPrice) % 10 === 0) ? round($intPrice) : round(($intPrice + 10 / 2) / 10) * 10;
                    $arResult['cost'] = (round($arResult['cost']) % 10 === 0) ? round($arResult['cost']) : round(($arResult['cost'] + 10 / 2) / 10) * 10;
                }

                if ($allData !== false && !empty($arResult['cost'])) {
                    $arResult['cost'] = $intPrice;
                }
                
            }

            if(empty($arResult)){
                $arResult = array('cost'=>$intPrice,'days'=>$deyDelivery,'serviceName'=>$serviceName, 'dpd_status' => 0);
            }
    
            if ($intCityId == 49694167 || $intCityId == 49694102) {
                $arResult['dpd_status'] = 1;
            }
    
            return (!empty($arResult) && $arResult !== false) ? $arResult : $intPrice;
        }
    }
}
