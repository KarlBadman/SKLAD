<?

namespace Dsklad;

use \Bitrix\Main\Context;
use \Bitrix\Main\Loader;
use \Bitrix\Sale\Basket;
use \Bitrix\Sale\BasketItem;
use \Bitrix\Sale\Fuser;
use \Bitrix\Highloadblock\HighloadBlockTable;

use Bitrix\Main\Diag\Debug;

class Order
{
    static $warrantyProductId = null;
    static $arSectionParentsCache = [];

    static $weightByVolume = [];

    /**
     * Установка количества товара дополнительной гарантии
     * @throws \Exception
     */
    public static function setWarrantyQuantity()
    {
        $obBasket = Basket::loadItemsForFUser(Fuser::getId(), Context::getCurrent()->getSite());

        $warrantyProductId = self::getWarrantyProductId();
        $warrantyItemQuantity = self::getWarrantyQuantity();

        if ($warrantyBasketItem = $obBasket->getItemById($warrantyProductId)) {
            $warrantyBasketItem->setField('QUANTITY', $warrantyItemQuantity);
            $obBasket->save();
        }
    }

    /**
     * Возвращает id самой верхней родительской категории
     * @param $elementId
     * @return int
     * @throws \Exception
     */
    private static function getProductParentId($elementId): int
    {
        if (isset(self::$arSectionParentsCache[$elementId])) {
            return self::$arSectionParentsCache[$elementId];
        }

        Loader::includeModule('catalog');
        Loader::includeModule('iblock');

        $curDepth = 999;
        $parentSection = 0;

        $rsSKU = \CIBlockElement::GetList(
            [],
            [
                '=IBLOCK_ID' => Config::getParam('iblock/offers'),
                '=ID' => $elementId
            ],
            false,
            false,
            ['ID', 'IBLOCK_SECTION_ID', 'PROPERTY_CML2_LINK']
        );

        $isSKU = ($rsSKU->SelectedRowsCount() > 0);

        if ($isSKU) {
            $arSku = $rsSKU->Fetch();
            $productId = (int)$arSku['PROPERTY_CML2_LINK_VALUE'];
        } else {
            $productId = $elementId;
        }

        $rsElement = \Bitrix\Iblock\ElementTable::getList([
            'select' => ['ID', 'NAME', 'IBLOCK_SECTION_ID'],
            'filter' => [
                '=IBLOCK_ID' => Config::getParam('iblock/catalog'),
                '=ID' => $productId
            ],
            'cache' => [
                'ttl' => 600,
            ]
        ]);

        if ($rsElement->getSelectedRowsCount() > 0) {
            $arElement = $rsElement->Fetch();
            $arSelect = [
                'DEPTH_LEVEL',
                'ID'
            ];
            $rsNav = \CIBlockSection::GetNavChain(0, $arElement['IBLOCK_SECTION_ID'], $arSelect);
            while ($nav = $rsNav->Fetch()) {
                if ($curDepth > $nav['DEPTH_LEVEL']) {
                    $curDepth = $nav['DEPTH_LEVEL'];
                    $parentSection = (int)$nav['ID'];
                }
            }

            self::$arSectionParentsCache[$elementId] = $parentSection;
        }else{
            //Если это не товар каталога и не торговое предложение
            self::$arSectionParentsCache[$elementId] = 0;
        }

        return $parentSection;
    }

    /**
     * Возвращает количество товаров для гарантии
     * @return int
     * @throws \Exception
     */
    public static function getWarrantyQuantity(): int
    {
        $obBasket = Basket::loadItemsForFUser(Fuser::getId(), Context::getCurrent()->getSite());

        $warrantyItemQuantity = 0;
        /** @var BasketItem $obBasketItem */
        foreach ($obBasket as $obBasketItem) {
            $parentId = self::getProductParentId($obBasketItem->getProductId());

            switch ($parentId) {
                case Config::getParam('section/chair') :
                    $warrantyItemQuantity += $obBasketItem->getQuantity();
                    break;
                case Config::getParam('section/table') :
                    $warrantyItemQuantity += $obBasketItem->getQuantity() * 4;
                    break;
                case Config::getParam('section/set') :
                    $warrantyItemQuantity += $obBasketItem->getQuantity() * 8;
                    break;
            };
        }
        return $warrantyItemQuantity;
    }

    /**
     * Вернём id товара дополнительной гарантии
     * @return int
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getWarrantyProductId(): int
    {
        Loader::includeModule('iblock');
        if(is_null(self::$warrantyProductId)) {
            $rsElement = \Bitrix\Iblock\ElementTable::getList([
                'select' => ['ID'],
                'filter' => [
                    '=IBLOCK_ID' => Config::getParam('iblock/basket_services'),
                    'CODE' => Config::getParam('order/warranty_code')
                ],
                'cache' => [
                    'ttl' => 600,
                ]
            ]);
            $arElement = $rsElement->fetch();

            self::$warrantyProductId = $arElement['ID'];
        }

        return self::$warrantyProductId;
    }

    /**
     * Определяем код и координаты города
     * @param int $intCityCode
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     * @throws \Exception
     */
    public static function getCityParams(int $intCityCode = 0): array
    {
        $cityParams = [];

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
            Loader::includeModule('altasib.geoip');
            Loader::includeModule('highloadblock');

            $arGeo = \ALX_GeoIP::GetAddr(0);
            $cityObj = new \CCity();
            $arThisCity = $cityObj->GetFullInfo();

            $cityParams['LAT'] = $arGeo['lat'];
            $cityParams['LON'] = $arGeo['lng'];

            $arHLBlock = HighloadBlockTable::getById(Config::getParam('hl/dpd_cities'))->fetch();
            $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
            $strEntityDataClass = $obEntity->getDataClass();

            $rsData = $strEntityDataClass::getList(array(
                'select' => array('UF_CITYCODE', 'UF_CITYID', "UF_CONDITIONS", "UF_CASHPAYMENTCANCEL", "UF_REGIONCODE", "UF_COUNTRYCODE"),
                'filter' => array(
                    '%=UF_CITYNAME' => $arThisCity['CITY_NAME']['VALUE']
                ),
            ));
            if ($arItem = $rsData->fetch()) {
                $strCityConditions = $arItem['UF_CONDITIONS'];
                $strCityCashPayment = $arItem['UF_CASHPAYMENTCANCEL'] ? 'Y' : 'N';
                $strCityRegionCode = $arItem['UF_REGIONCODE'];
                $strCityCountryCode = $arItem['UF_COUNTRYCODE'];
                $intCityCode = $arItem['UF_CITYCODE'];
                $_SESSION['DPD_CITY'] = $intCityCode;
            }

        } else {
            Loader::includeModule('highloadblock');

            $arHLBlock = HighloadBlockTable::getById(Config::getParam('hl/dpd_cities'))->fetch();
            $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
            $strEntityDataClass = $obEntity->getDataClass();

            $rsData = $strEntityDataClass::getList(array(
                'select' => array('UF_CITYCODE', 'UF_CITYID', "UF_CONDITIONS", "UF_CASHPAYMENTCANCEL", "UF_REGIONCODE", "UF_COUNTRYCODE"),
                'filter' => array(
                    'UF_CITYCODE' => $intCityCode
                ),
            ));
            if ($arItem = $rsData->fetch()) {
                $strCityConditions = $arItem['UF_CONDITIONS'];
                $strCityCashPayment = $arItem['UF_CASHPAYMENTCANCEL'] ? 'Y' : 'N';
                $strCityRegionCode = $arItem['UF_REGIONCODE'];
                $strCityCountryCode = $arItem['UF_COUNTRYCODE'];
            }
        }

        $cityParams['CODE'] = $intCityCode;
        $cityParams['CONDITIONS'] = $strCityConditions;
        $cityParams['CASH_PAYMENT_OFF'] = $strCityCashPayment;
        $cityParams['REGION_CODE'] = $strCityRegionCode;
        $cityParams['COUNTRY_CODE'] = $strCityCountryCode;

        return $cityParams;
    }

    /**
     * Расчет веса упаковки на основе объёма
     * @param $volume
     * @return float|int
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    private static function getWeight($volume)
    {
        self::initWeightCoeff();

        if ($volume >= self::$weightByVolume['VOLUME_BIG']) {
            return $volume * self::$weightByVolume['COEFF_BIG'];
        } elseif ($volume >= self::$weightByVolume['VOLUME_MEDIUM']) {
            return $volume * self::$weightByVolume['COEFF_MEDIUM'];
        } else {
            return $volume * self::$weightByVolume['COEFF_SMALL'];
        }
    }

    /**
     * Инициализация параметров расчета веса товара
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    private static function initWeightCoeff()
    {
        if (empty(self::$weightByVolume)) {
            Loader::includeModule('highloadblock');

            $arHLBlock = HighloadBlockTable::getById(Config::getParam('hl/settings'))->fetch();
            $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
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
                self::$weightByVolume['VOLUME_BIG'] = $arItem['UF_VOLUME_BIG'];
                self::$weightByVolume['VOLUME_MEDIUM'] = $arItem['UF_VOLUME_MEDIUM'];
                self::$weightByVolume['VOLUME_SMALL'] = $arItem['UF_VOLUME_SMALL'];
                self::$weightByVolume['COEFF_BIG'] = $arItem['UF_COEFF_BIG'];
                self::$weightByVolume['COEFF_MEDIUM'] = $arItem['UF_COEFF_MEDIUM'];
                self::$weightByVolume['COEFF_SMALL'] = $arItem['UF_COEFF_SMALL'];
            }
        }
    }

    /**
     * Метод возвращает список терминалов, удовлетворяющих параметрам доставки
     * @param array $arItems
     * @param int $intCityCode
     * @param string $strSourceTerminals
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     * @throws \Exception
     */
    public static function getDPDTerminals(array $arItems, int $intCityCode = 0, string $strSourceTerminals = 'HL'): array
    {
        $arResult = []; $pvzOnlyTerminal = false;

        $cityParams = self::getCityParams($intCityCode);

        $intCityCode = $cityParams['CODE'];

        // location and terminals
        $arResult['mapParams'] = [
            'yandex_lat' => !empty($cityParams['LAT']) ? $cityParams['LAT'] : 0,
            'yandex_lon' => !empty($cityParams['LON']) ? $cityParams['LON'] : 0,
            'yandex_scale' => 9,
            'PLACEMARKS' => []
        ];
/*
        if (empty($intCityCode)) {
            ShowError('Укажите местоположение');
        }
*/
        if ($strSourceTerminals != 'HL') {
            require_once $_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/classes/dpd_service.class.php';

            $oDPD = new \DPD_service_my;
            $arData = array(
                'cityCode' => $intCityCode
            );

            $arTerminals = $oDPD->getTerminal2($arData);
            $arTerminals = $arTerminals['terminal'];
        } else {
            // Load terminals from HL-block;
            $arHLBlockTerm = HighloadBlockTable::getById(Config::getParam('hl/dpd_terminals'))->fetch();
            $obEntityTerm = HighloadBlockTable::compileEntity($arHLBlockTerm);
            $strEntityTermDataClass = $obEntityTerm->getDataClass();

            $maxPackLength = $maxPackWidth = $maxPackHeight = $sumVolume = $maxPackWeight = 0;

            $notOneKindPack = false;

            foreach ($arItems as $arItem) {
                if (empty($arItem['PACK'])) {
                    continue;
                }

                if(empty($keyJson )) $keyJson = json_encode($arItem['PACK']);

                if($keyJson != json_encode($arItem['PACK'])) $notOneKindPack = true;

                // Если столы или комплекты, оставляем только терминалы
                if ($pvzOnlyTerminal === false) {
                    if (!empty($arItem['PARENT'])) {
                        $arItemSection = get_parent_section_by_element_ID ($arItem['PARENT']);
                    } elseif (!empty($arItem['IBLOCK_SECTION_ID'])) {
                        $arItemSection = get_parent_section_by_section_ID ($arItem['IBLOCK_SECTION_ID']);
                    } elseif (empty($arItem['PARENT'])) {
                        $arItemSection = get_parent_section_by_element_ID ($arItem['ID']);
                    }

                    if ($arItemSection['CODE'] == "stoly" || $arItemSection['CODE'] == "komplekty")
                        $pvzOnlyTerminal = true;
                }

                $notPackQuantity = $arItem['QUANTITY'];
                $ves = 0;
                $parcelStack = 0;


                while ($notPackQuantity > 0) {
                    foreach ($arItem['PACK'] as $pack) {
                        if ($notPackQuantity >= $pack['QUANTITY']) {
                            $maxPackWeight = ($maxPackWeight < $pack['WEIGHT']) ? $pack['WEIGHT'] : $maxPackWeight;

                            $maxPackLength = $maxPackLength > $pack['LENGTH'] ? $maxPackLength : $pack['LENGTH'];
                            $maxPackWidth = $maxPackWidth > $pack['WIDTH'] ? $maxPackWidth : $pack['WIDTH'];
                            $maxPackHeight = $maxPackHeight > $pack['HEIGHT'] ? $maxPackHeight : $pack['HEIGHT'];

                            $itemVolume = $pack['LENGTH'] * $pack['WIDTH'] * $pack['HEIGHT'];
                            $sumVolume += $itemVolume;

                            $notPackQuantity -= $pack['QUANTITY'];

                            $ves += $pack['WEIGHT'];

                            $parcelStack++;
                            break;
                        }
                    }
                }
            }

            //Вес всей корзины
            //$ves = self::getWeight($sumVolume);

            //по хорошему нужно зашивать в админку в каких единицах что хранится, писать класс конвертации и использовать нужное приведение единиц в зависиости от параметров.
            //но сейчас так: весь магазин в метрах, а доставка в сантиметрах, по этому
            //ковертируем если значение меньше 5 (логичное предположение, что если меньше 5 - это метры. Если больше, то сантиметры, проверка на случай если что-то проскочит в сантиметрах)
            $maxPackLength *= ($maxPackLength < 5 )? 100: 1;
            $maxPackWidth *= ($maxPackWidth < 5)? 100: 1;
            $maxPackHeight *= ($maxPackHeight < 5)? 100: 1;

            $max_gabapit = max($maxPackLength, $maxPackWidth, $maxPackHeight);
            $max_gabapit_summa = $maxPackLength + $maxPackWidth + $maxPackHeight;

            $rsTermData = $strEntityTermDataClass::getList([
                'select' => [
                    'UF_DATA_SOURCE',
                    'UF_GABAPIT_MAX',
                    'UF_MAX_SUM',
                    'UF_MAX_VES_OTPAVKI',
                    'UF_MAX_VES_UPAKOVKI',
                    'UF_TERMINALCODE',
                    'UF_TERMINALNAME',
                ],
                'order' => ['UF_GABAPIT_MAX' => 'DESC', 'ID' => 'ASC'],
                'filter' => ['UF_CITYCODE' => $intCityCode]
            ]);

            if(empty($parcelStack))$parcelStack = 1;

            while ($arResTerm = $rsTermData->Fetch()) {

                if (unserialize($arResTerm['UF_DATA_SOURCE'])) {

                    if (
                        (
                            $max_gabapit <= $arResTerm['UF_GABAPIT_MAX']
                            || $arResTerm['UF_GABAPIT_MAX'] == "без ограничений"
                            || $arResTerm['UF_GABAPIT_MAX'] == ""
                        )
                        AND
                        (
                            $max_gabapit_summa <= $arResTerm['UF_MAX_SUM']
                            || $arResTerm['UF_MAX_SUM'] == ''
                            || $arResTerm['UF_MAX_SUM'] == 'без ограничений'
                        )
                        AND
                        (
                            $ves <= $arResTerm['UF_MAX_VES_OTPAVKI']
                            || $arResTerm['UF_MAX_VES_OTPAVKI'] == ''
                            || $arResTerm['UF_MAX_VES_OTPAVKI'] == 'без ограничений'
                        )

                        AND
                        (
                            $maxPackWeight <= $arResTerm['UF_MAX_VES_UPAKOVKI']
                            || $arResTerm['UF_MAX_VES_UPAKOVKI'] == ''
                            || $arResTerm['UF_MAX_VES_UPAKOVKI'] == 'без ограничений'
                        )
                        AND
                        (
                            $arResTerm['UF_MAX_VES_OTPAVKI'] == $arResTerm['UF_MAX_VES_UPAKOVKI']
                            && $parcelStack == 1
                            && !$notOneKindPack
                            || $arResTerm['UF_MAX_VES_OTPAVKI'] !=$arResTerm['UF_MAX_VES_UPAKOVKI']
                            || $arResTerm['UF_MAX_VES_UPAKOVKI'] == 'без ограничений'
                        )
                        AND
                        $pvzOnlyTerminal === false
                    ) {
                        if (($arResTerm['UF_GABAPIT_MAX'] == "без ограничений" || $arResTerm['UF_GABAPIT_MAX'] == "") AND ($arResTerm['UF_MAX_SUM'] == '' || $arResTerm['UF_MAX_SUM'] == 'без ограничений') AND ($arResTerm['UF_MAX_VES_OTPAVKI'] == '' || $arResTerm['UF_MAX_VES_OTPAVKI'] == 'без ограничений')) {
                            $arTerminals[] = array_merge(array('is_terminal' => 'Y'), unserialize($arResTerm['UF_DATA_SOURCE']));
                        } else {
                            $arTerminals[] = array_merge(array('is_terminal' => 'N'), unserialize($arResTerm['UF_DATA_SOURCE']));
                        }
                    } elseif (
                        $pvzOnlyTerminal === true
                        AND
                        (
                            $arResTerm['UF_GABAPIT_MAX'] == "без ограничений"
                            || $arResTerm['UF_GABAPIT_MAX'] == ""
                        )
                        AND
                        (
                            $arResTerm['UF_MAX_SUM'] == ''
                            || $arResTerm['UF_MAX_SUM'] == 'без ограничений'
                        )
                        AND
                        (
                            $arResTerm['UF_MAX_VES_OTPAVKI'] == ''
                            || $arResTerm['UF_MAX_VES_OTPAVKI'] == 'без ограничений'
                        )

                    ) {
                        $arTerminals[] = array_merge(array('is_terminal' => 'Y'), unserialize($arResTerm['UF_DATA_SOURCE']));
                    }
                }
            }
        }

        $fLat = $fLon = $basketSum = $nppSum = $countTerminals = 0;

        if (!empty($arTerminals['address'])) {
            $arTerminals = [$arTerminals];
        }

        foreach ($arItems as $arItem) {
            $basketSum += $arItem['NO_FORMAT_PRICE'] * $arItem['QUANTITY'];
        }

        foreach ($arTerminals as $arItem) {
            // Найдем ограничение по наложке в пункте
            foreach ($arItem['extraService'] as $extraservice) {
                if ($extraservice['esCode'] == 'НПП') {
                    $nppSum = (int)$extraservice['params']['value'];
                }
            }

            // Если сумма корзины больше ограничения пункта или больше 100тр, подставим класс, по которому будем прятать пункты
            if ($nppSum < $basketSum || $basketSum >= NPP_SUMM_LIMIT) {
                $nppclass = 'npp_hide';
            } else {
                $nppclass = 'npp_show';
            }

            $arItem['css_class'] = $nppclass;
            $arItem['npp_sum'] = $nppSum;

            if ($arItem['address']['cityCode'] !== $intCityCode) {
                continue;
            }
            $arResult['TERMINAL'][] = $arItem;
        }

        foreach ($arResult['TERMINAL'] as $arItem) {

            $fLat += $arItem['geoCoordinates']['latitude'];
            $fLon += $arItem['geoCoordinates']['longitude'];
            $countTerminals++;

            $arItem['address']['descript'] = ($arItem['address']['descript'] == '-') ? '' : $arItem['address']['descript'];
            $houseNo = (!empty($arItem['address']['houseNo']))? $arItem['address']['houseNo'] : $arItem['address']['ownership'];



            $address  = $arItem['address']['streetAbbr'];
            $address .= ' '.$arItem['address']['street'] . ', ';
            $address .= (!empty($arItem['address']['houseNo'])) ? $arItem['address']['houseNo'] : $arItem['address']['ownership'];

            $arResult['mapParams']['PLACEMARKS'][] = array(
                'LON' => $arItem['geoCoordinates']['longitude'],
                'LAT' => $arItem['geoCoordinates']['latitude'],
                'TEXT' => '<b>' . $arItem['address']['cityName'] . ', ' . $arItem['address']['street'] . ', ' . $houseNo . '. </b>' . $arItem['address']['descript'],
                'TITLE'=>$arItem['terminalName'],
                'TERMINAL' => $arItem["terminalCode"],
                'CSS_CLASS' => $arItem["css_class"],
                'NPP_SUM' => $arItem["npp_sum"],
                'IS_TERMINAL' => $arItem['is_terminal'] == 'Y' ? 'Y' : 'N',
                'OPENING_HOURS'=>$arItem['schedule'][0]['timetable'],
                'ADDRESSES'=>$address
            );
        }

        if ($arResult['mapParams']['yandex_lat'] == 0) {
            $arResult['mapParams']['yandex_lat'] = $fLat / $countTerminals;
            $arResult['mapParams']['yandex_lon'] = $fLon / $countTerminals;
        }

        return $arResult;
    }
}
