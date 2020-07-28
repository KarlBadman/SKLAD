<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

require_once $_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/classes/dpd_service.class.php';

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
Loader::includeModule('statistic');

/**
 * Class COrderBasket
 */
class COrderBasket extends CBitrixComponent
{
    public $intTotalSum = 0;
    public $intTotalSumServices = 0;
    public $intTotalVolume = 0;
    protected $arElements = array();
    protected $intWeight = 0;
    protected $intDeliveryServicesSum = 0;
    protected $arServices = array();
    protected $arDeliveryServices = array();
    protected $strSourceTerminals = 'DPD'; // DPD - get terminals from DPD site, HL - get terminals from HL-block

    private $weightByVolume = [];

    public $kkk_count = 0;
    public $needed = 0;
    public $packedArray = [];
    public $unterChairs = [];

    /**
     * @return int
     */
    public function getBasketItemsCount()
    {
        $obContext = Context::getCurrent();
        $obBasket = Basket::loadItemsForFUser(Fuser::getId(), $obContext->getSite());
        return count($obBasket->getBasketItems());
    }

    /**
     * @param array $arExcept
     * @return array
     */
    public function getBasketItems($arExcept = array())
    {
        $arBasketItems = array(
            'SERVICES' => array(),
            'SHIPMENT' => array(),
            'CATALOG' => array()
        );

        $obreshetkaCount = 0;

        $obContext = Context::getCurrent();
        $this->obBasket = $obBasket = Basket::loadItemsForFUser(Fuser::getId(), $obContext->getSite());
        $arProductID = array();
		$payment = $obContext->getRequest()['payment'] ? : $this->defaultPayment;
		if (!empty($payment)) {
			$arPaySystem = $this->getPaySystems();
			$strPayName = '';
			foreach ($arPaySystem as $arItem) {
				if ($arItem['ID'] == $payment) {
					$strPayName = str_replace('<br>', '', $arItem['~NAME']);
				}
			}
			$paymentCollection = $this->order->getPaymentCollection();
			$extPayment = $paymentCollection->createItem();
			$extPayment->setFields(array(
				'PAY_SYSTEM_ID' => $payment,
				'PAY_SYSTEM_NAME' => $strPayName,
				'SUM' => $this->order->getPrice()
			));
		}
        // $order = Order::create(SITE_ID);
        $order = $this->order;
        $order->setBasket($obBasket);
        $obBasket = $order->getBasket();

        // White Power fix
        foreach ($obBasket->getBasketItems() as $basketItem) {
            if ($this->getArticle($basketItem->getProductId()) == '0100' && strtolower($this->getColor($basketItem->getProductId())) !== 'белый') {
                $this->kkk_count += $basketItem->getQuantity();
                if ($basketItem->getQuantity() > 0)
                    for ($i = 1; $basketItem->getQuantity() >= $i; $i++)
                        $this->unterChairs[] = $basketItem->getProductID();
            }
        }

        // UNTER PRICE FIX BY COLORED
        if ($this->kkk_count >= 1 && count($this->unterChairs) > 0) {
            $arDiscount = []; $arDouble = []; $l = 1; $m = 1;
            $count = (intdiv($this->kkk_count, 4)*4); $restrictCount = 0;

            foreach ($this->unterChairs as $k => $val) {
                if ($k < $count) {
                    $arDiscount[$val] = $l++;
                    if ($val != $this->unterChairs[$k+1]) $l=1;
                } else {
                    $arDouble[$val] = $m++;
                    if ($val != $this->unterChairs[$k+1]) $m=1;
                }
            }

            foreach ($arDouble as $item => $quantity)
                $restrictCount += $quantity;

            if (count($restrictCount) > 0 && $this->kkk_count <= 12) {
                if ($restrictCount == 3) $this->needed = 1;
                if ($restrictCount == 2) $this->needed = 2;
                if ($restrictCount == 1) $this->needed = 3;
            }

        }

        foreach ($obBasket as $obBasketItem) {
            // услуги доставки
            $arFullDeliveryServices = $this->getDeliveryServices();
            $arDeliveryServices = array();
            foreach ($arFullDeliveryServices as $arService) {
                $arDeliveryServices[] = $arService['ID'];
            }
            // Получим цену с учетом всех скидок зависящих от количества однотипных товаров в корзине
            //$arPrice = CCatalogProduct::GetOptimalPrice($obBasketItem->getProductId(), $obBasketItem->getQuantity());
            $optimalPrice = $obBasketItem->getPrice();// Цена с учетом всех скидок

            if (array_search($obBasketItem->getProductId(), $arDeliveryServices) !== false) {
                $this->intDeliveryServicesSum += $optimalPrice * $obBasketItem->getQuantity();
            }

            $this->intTotalSum += $optimalPrice * $obBasketItem->getQuantity();
            if (array_search($obBasketItem->getProductId(), $arExcept) !== false) {
                $this->intTotalSumServices =+ $optimalPrice * $obBasketItem->getQuantity();
                $arBasketItems['SERVICES'][] = $obBasketItem->getProductId();
                continue;
            }

            $obreshetka = $this->getSectionObreshetka($obBasketItem->getProductId());
            $quantity = $obBasketItem->getQuantity();

            $arProductXmlId[] = $this->getProductXmlId($obBasketItem->getProductId());

            $arFields = array(
                'ID' => $obBasketItem->getId(),
                'PRODUCT_ID' => $obBasketItem->getProductId(),
                'NAME' => $this->getElementName($obBasketItem->getProductId()),// $obBasketItem->getField('NAME'),
                'NAME_URL' => $this->getNameURL($obBasketItem->getProductId()),
                'QUANTITY' => $quantity,
                'PRICE' => number_format($optimalPrice, 0, '', ' ') . '.–',
                'NO_FORMAT_PRICE' => $optimalPrice,
                'FINAL_PRICE' => number_format($optimalPrice * $obBasketItem->getQuantity(), 0, '', ' ') . '.–',
                'IMAGE' => $this->getImage($obBasketItem->getProductId()),
                'ARTICLE' => $this->getArticle($obBasketItem->getProductId()),
                'COLOR' => $this->getColor($obBasketItem->getProductId()),
                'SIZE' => $this->getSize($obBasketItem->getProductId()),
                'SECTION' => $this->getSectionName($obBasketItem->getProductId()),
                'SECTION_OBRESHETKA' => $obreshetka,
                'SECTION_URL' => $this->getSectionURL($obBasketItem->getProductId()),
                'PARENT' => $this->getParentElement($obBasketItem->getProductId()),
                'ARRIVAL_DATE' => $this->getArrivalDate($obBasketItem->getProductId()),
                'PACK' => GetPackData($this->getProductXmlId($obBasketItem->getProductId()))
            );


            if (isset($this->packedArray[json_encode($arFields['PACK'])]))
                $this->packedArray[json_encode($arFields['PACK'])] = [
                    'QUANTITY' => intval($this->packedArray[json_encode($arFields['PACK'])]['QUANTITY']) + intval($arFields['QUANTITY']),
                    'PACK' => $arFields['PACK'],
                    'PRODUCT_ID' => $arFields['PRODUCT_ID']
                ];
            else
                $this->packedArray[json_encode($arFields['PACK'])] = [
                    'QUANTITY' => intval($arFields['QUANTITY']),
                    'PACK' => $arFields['PACK'],
                    'PRODUCT_ID' => $arFields['PRODUCT_ID']
                ];

            if ($obreshetka) {
                $obreshetkaCount = (int)$obreshetkaCount + (int)$quantity;
            }
            $arProductID[] = $arFields['PRODUCT_ID'];
            $arBasketItems['SHIPMENT'][] = $arFields;
        }

        if($this->intTotalSumServices  <= 0){
            $this->intTotalSumServices = $this->intTotalSum / 10;
        }else{
            $this->intTotalSumServices = ($this->intTotalSum - $this->intTotalSumServices) / 10;
        }

        $arBasketItems['OBRESHETKA_COUNT'] = $obreshetkaCount;
        //Получаем базовую цену товаров
        $arBasePrice = array();
        $rsBasePrice = \CPrice::GetList(
            array(),
            array('PRODUCT_ID' => $arProductID, 'BASE' => 'Y')
        );
        while ($ar_res = $rsBasePrice->Fetch()) {
            $arBasePrice[$ar_res['PRODUCT_ID']] = $ar_res;
        }
        foreach ($arBasketItems['SHIPMENT'] as $key => $arItem) {
            $arDiscount = array();
            if (!empty($arBasePrice[$arItem['PRODUCT_ID']])) {
                $arDiscount['BASE_PRICE'] = $arBasePrice[$arItem['PRODUCT_ID']]['PRICE'];
                $arDiscount['DISCOUNT'] = $arBasePrice[$arItem['PRODUCT_ID']]['PRICE'] - $arBasketItems['SHIPMENT'][$key]['NO_FORMAT_PRICE'];
                $arDiscount['DISCOUNT_FORMAT'] = number_format($arDiscount['DISCOUNT'], 0, '', ' ') . '.–';
                $arDiscount['DISCOUNT_PERCENTAGE'] = number_format($arDiscount['DISCOUNT'] / ($arBasePrice[$arItem['PRODUCT_ID']]['PRICE'] / 100));
            }

            $arBasketItems['SHIPMENT'][$key]['DISCOUNT_PRODUCT'] = $arDiscount;
        }

        //Получаем количество товара на складе
        $rsCatProd = \CCatalogProduct::GetList(
            array(),
            array('ID' => $arProductID),
            false,
            false,
            array('ID', 'QUANTITY')
        );
        while ($ar_res = $rsCatProd->Fetch()) {
            $arBasketItems['CATALOG'][$ar_res['ID']] = $ar_res;
        }

        return $arBasketItems;
    }

    /**
     * @return array
     */
    public function getServices()
    {
        if (!empty($this->arServices)) {
            return $this->arServices;
        }

        $arFilter = array(
            'IBLOCK_ID' => \Dsklad\Config::getParam('iblock/basket_services'),
            'ACTIVE' => 'Y'
        );
        $arSelect = array(
            'ID', 'NAME', 'CODE', 'PREVIEW_TEXT', 'PROPERTY_SVG_SPRITE', 'PROPERTY_SVG_ANCHOR', 'CATALOG_GROUP_1'
        );
        $dbElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        $arServices = array();
        while ($arFields = $dbElement->GetNext()) {
            $arServices[$arFields['ID']] = array(
                'NAME' => $arFields['NAME'],
                'CODE' => $arFields['CODE'],
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

    /**
     * @return array
     * @throws Exception
     */
    public function getDeliveryServices($pref = "")
    {
        if (!empty($this->arDeliveryServices)) {
            return $this->arDeliveryServices;
        }
        $arFilter = array(
            'IBLOCK_ID' => \Dsklad\Config::getParam('iblock/delivery_services'),
            'ACTIVE' => 'Y'
        );
        $arSelect = array(
            'ID', 'NAME', 'SORT', 'CATALOG_GROUP_1', 'XML_ID', "CODE"
        );
        $dbElement = \CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        $arServices = array();
        while ($arFields = $dbElement->GetNext()) {
            $arServices[$arFields[$pref] ? : $arFields['SORT']] = array(
                'ID' => $arFields['ID'],
                'NAME' => $arFields['NAME'],
                'XML_ID' => $arFields['XML_ID'],
                '~PRICE' => $arFields['CATALOG_PRICE_1'],
                'PRICE' => number_format($arFields['CATALOG_PRICE_1'], 0, '', ' ') . ' руб.',
                'CODE' => $arFields['CODE']
            );
        }

        $this->arDeliveryServices = $arServices;

        return $arServices;
    }

    /**
     * @param $intID
     * @return mixed
     */
    protected function getElementName($intID)
    {
        $parentEl = $this->getElement($this->getParentElement($intID));
        return $parentEl['FIELDS']['NAME'];
    }

    /**
     * @param $intID
     * @return bool
     */
    protected function getArticle($intID)
    {
        $arProperties = $this->getElement($intID)['PROPERTIES'];
        if (empty($arProperties['CML2_LINK'])) {
            return false;
        }
        $arProperties = $this->getElement($arProperties['CML2_LINK']['VALUE'])['PROPERTIES'];

        return $arProperties['CML2_ARTICLE']['VALUE'];
    }

    /**
     * @param $intID
     * @return bool
     */
    protected function getArrivalDate($intID)
    {
        $arProperties = $this->getElement($intID)['PROPERTIES'];
        if (!empty($arProperties['ARRIVAL_DATE']['VALUE'])) {
            return $arProperties['ARRIVAL_DATE']['VALUE'];
        } else {
            if (empty($arProperties['CML2_LINK'])) {
                return false;
            }
            $arProperties = $this->getElement($arProperties['CML2_LINK']['VALUE'])['PROPERTIES'];
            return $arProperties['ARRIVAL_DATE']['VALUE'];
        }
    }

    /**
     * @param $intID
     * @return mixed
     * @throws Exception
     */
    protected function getColor($intID)
    {
        // colors
        $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/color'))->fetch();
        $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();

        $arColors = array();
        $rsData = $strEntityDataClass::getList();
        while ($arItem = $rsData->fetch()) {
            $arColors[$arItem['UF_1C_CODE']] = $arItem;
        }

        // reference
        $arProperties = $this->getElement($intID)['PROPERTIES'];
        $color = $arProperties['KOD_TSVETA']['VALUE'];
        $color = explode('#', $color)[1];
        return $arColors[$color]['UF_NAME'];
    }

    /**
     * @param $intID
     * @return mixed
     */
    protected function getSize($intID)
    {
        $arProperties = $this->getElement($intID)['PROPERTIES'];
        $arSize = $arProperties['RAZMER_STOLESHNITSY']['VALUE'];
        return $arSize;
    }

    /**
     * @param $intID
     * @return bool
     * @throws Exception
     */
    protected function getImage($intID)
    {
        $arFields = $this->getElement($intID)['FIELDS'];

        if (empty($arFields['DETAIL_PICTURE'])) {
            $arProperties = $this->getElement($intID)['PROPERTIES'];
            $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
            $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
            $strEntityDataClass = $obEntity->getDataClass();

            $rsData = $strEntityDataClass::getList(array(
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
        $arImage = \CFile::ResizeImageGet(
            $arFields['DETAIL_PICTURE'],
            $arSize,
            BX_RESIZE_IMAGE_PROPORTIONAL,
            false,
            array('name' => 'sharpen', 'precision' => 1),
            false,
            100
        );

        return $arImage['src'];
    }

    /**
     * @param $intID
     * @return bool
     */
    protected function getNameURL($intID)
    {
        $arProperties = $this->getElement($intID)['PROPERTIES'];
        if (empty($arProperties['CML2_LINK'])) {
            return false;
        }
        $arFields = $this->getElement($arProperties['CML2_LINK']['VALUE'])['FIELDS'];

        return $arFields['DETAIL_PAGE_URL'];
    }

    /**
     * @param $intID
     * @return bool
     */
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
        $dbSection = \CIBlockSection::GetByID($arFields['IBLOCK_SECTION_ID']);
        if ($arFields = $dbSection->GetNext()) {
            return $arFields['NAME'];
        }

        return false;
    }

    /**
     * @param $intID
     * @return bool
     */
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
        $dbSection = \CIBlockSection::GetByID($arFields['IBLOCK_SECTION_ID']);
        if ($arFields = $dbSection->GetNext()) {
            return $arFields['SECTION_PAGE_URL'];
        }

        return false;
    }

    /**
     * @param $intID
     * @return bool
     */
    protected function getSectionObreshetka($intID)
    {
        $arProperties = $this->getElement($intID)['PROPERTIES'];
        if (empty($arProperties['CML2_LINK'])) {
            return false;
        }
        $arFields = $this->getElement($arProperties['CML2_LINK']['VALUE'])['FIELDS'];
        if (empty($arFields['IBLOCK_SECTION_ID'])) {
            return false;
        }
        $dbSection = \CIBlockSection::GetList(
            array(),
            array(
                'IBLOCK_ID' => $arFields['IBLOCK_ID'],
                '=ID' => $arFields['IBLOCK_SECTION_ID']
            ),
            false,
            array('UF_OBRESHETKA')
        );
        if ($arFields = $dbSection->GetNext()) {
            return $arFields['UF_OBRESHETKA'];
        }

        return false;
    }

    /**
     * @param $intID
     * @return mixed
     */
    protected function getProductXmlId($intID)
    {
        $arFields = $this->getElement($intID);
        return $arFields['FIELDS']['XML_ID'];
    }

    /**
     * @param $intID
     * @return array|bool|mixed
     */
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

    /**
     * @param $intID
     * @return array
     */
    public function getVolume($intID)
    {
        $ID = $intID;
        $mxResult = \CCatalogSku::GetProductInfo($intID);
        if (is_array($mxResult)) {
            $ID = $mxResult["ID"];
        }
        $arProperty = $this->getElement($ID)['PROPERTIES'];

        $arVolume = array();
        foreach ($arProperty as $key => $value) {
            if ((strpos($key, 'UPAKOVKA_') !== false) && !empty($value['~VALUE'])){
                $arPropKey = explode('_', $key); // $arPropKey[1] - quantity
                $arVolume[$arPropKey[1]] = $value["~VALUE"];
            }
        }
        return $arVolume;
    }

    /**
     * @param $intID
     * @return mixed
     */
    protected function getParentElement($intID)
    {
        $arProperty = $this->getElement($intID)['PROPERTIES'];
        return $arProperty['CML2_LINK']['~VALUE'];
    }

    /**
     * @return array
     */
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

    public function GetOrderCoupon($filter = array(), $all = false) {
        $arCoupons = array();
        $arCoupons = DiscountCouponsManager::get(true, $filter, $all);
        return end($arCoupons);
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

            $res = \CSaleBasket::UpdateBasketPrices(Fuser::getId(), $obContext->getSite());
            AddMessage2Log($res);
        }
    }

    /**
     * @return array
     */
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

    /**
     * @param $arItems
     * @return int
     */
    public function getObreshetka($arItems)
    {
        $c = 0;
        foreach ($arItems as $arItem) {
            if ($arItem['SECTION_OBRESHETKA'])
                $c = $c + $arItem['QUANTITY'];
        }
        return $c;
    }

    /**
     * @param $arItems
     * @param bool $bSelfDelivery
     * @param int $intLocationID
     * @return float|int
     * @throws Exception
     */
    public function getDPDCoast($arItems, $bSelfDelivery = false, $intLocationID = 0, $allData = false)
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

        //$sumWeight = $this->getWeight($sumVolume);
        $this->intWeight = $sumWeight;

        $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/dpd_cities'))->fetch();
        $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();

        if ($intLocationID == 0) {

            if (empty($_SESSION['DPD_CITY'])) {
                $arGeo = ALX_GeoIP::GetAddr();
                $cityObj = new CCity();
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

            $arData = array(
                'pickup' => array(
                    'cityId' => 49694167 //'Санкт-Петербург'
                ),
                'delivery' => array(
                    'cityId' => (int)$intCityId
                ),
                'serviceCode' => 'MXO',
                'selfPickup' => false,
                'selfDelivery' => true,
                'parcel' => $arParcel,
            );

            $obRequest = Context::getCurrent()->getRequest();

            if ($arItem['UF_COUNTRYCODE'] == 'KZ')
                $arData['serviceCode'] = 'ECU';

            if ($this->isPCLtarif)
                $arData['serviceCode'] = 'PCL';

            $arData['selfDelivery'] = false;

            if ($obRequest->get('delivery') == 8 || $bSelfDelivery === true) { // доставка до двери
                $arData['selfDelivery'] = false;
            } else {
                $arData['selfDelivery'] = true;
            }

            // фиксированные данные для Москвы и Питера
            if (($intCityId == 49694167 || $intCityId == 49694102) && $allData === false && $this->isPCLtarif === false) {
                if ($arData['selfDelivery']) {
                    $intPrice = PICKUP_COAST;
                } else {
                    $intPrice = DELIVERY_COAST;
                }
            } else {
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

                $intPrice = ceil($arResult['cost'] / 10) * 10;

                if ((($intCityId == 49694167 || $intCityId == 49694102) && $obRequest->get('delivery') == 7) && $this->isPCLtarif === false) {
                    if ($arData['selfDelivery']) {
                        $intPrice = PICKUP_COAST;
                    } else {
                        $intPrice = DELIVERY_COAST;
                    }
                }

                if ($allData !== false && $intPrice > 0) {
                    $arResult['cost'] = $intPrice;
                }

            }

            return (!empty($arResult) && $arResult !== false && $allData !== false) ? $arResult : $intPrice;
        }
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        $obRequest = Context::getCurrent()->getRequest();
        $strStatus = 'preparation';
        if ($obRequest->get('go') == 'Y' and $this->getBasketItemsCount() > 0) {
            $strStatus = 'order';
        }

        return $strStatus;
    }

    /**
     * @param $propertyCollection
     * @param $code
     * @return mixed
     */
    protected function getPropertyByCode($propertyCollection, $code)
    {
        foreach ($propertyCollection as $property) {
            if ($property->getField('CODE') == $code)
                return $property;
        }
    }

    /**
     * @return bool
     */
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

    /**
     * @return \Bitrix\Main\Entity\AddResult|\Bitrix\Main\Entity\UpdateResult|\Bitrix\Sale\Result|mixed
     * @throws Exception
     */
    public function doOrder()
    {
        if ($this->getBasketItemsCount() < 1) {
            return false;
        }
        global $USER;

        $obContext = Context::getCurrent();
        $obRequest = $obContext->getRequest();
        $intSiteId = $obContext->getSite();

        $intLegal = trim($obRequest->get('legal'));
        $strRegion = trim($obRequest->get('region_name'));
        $strCity = trim($obRequest->get('city_name'));
        $strDPD_CODE = trim($obRequest->get('dpd_code'));
        $strPromoCode = trim($obRequest->get('promo'));
        $strName = trim($obRequest->get('name'));
        $strEmail = trim($obRequest->get('email'));
        $strPhone = trim($obRequest->get('phone'));
        $intDeliveryID = trim($obRequest->get('delivery'));
        $intDeliveryPrice = (float)trim($obRequest->get('delivery_price')) !== (float)$_REQUEST['delivery_price'] && (float)$_REQUEST['delivery_price'] != 0 ? (float)$_REQUEST['delivery_price'] : (float)trim($obRequest->get('delivery_price'));
        $intPaymentID = trim($obRequest->get('payment'));
        $point = $intDeliveryID != 8 ? trim($obRequest->get('point')) : '';
        $strINN = '';

        $_SESSION['ORDER']['FIELDS']['region'] = $strRegion;
        $_SESSION['ORDER']['FIELDS']['city'] = $strCity;

        if ($strRegion && in_array($strCity, array('Москва', 'Санкт-Петербург'))) {
            $strRegion = '';
        }

        /*
        if (empty($strPhone) || empty($strName) || empty($intDeliveryID) || empty($intPaymentID)) {
            return false; // это обязательные поля
        }
        */

        if (empty($strEmail)) {
            $strEmail = 'anonym@dsklad.ru';
        }

        $intPersonType = 1; // физлицо

        if ($intLegal == 1) {
            $intPersonType = 2; // юрлицо
            $strINN = trim($obRequest->get('vat'));
        }

        $strCurrencyCode = Option::get('sale', 'default_currency', 'RUB');

        DiscountCouponsManager::init();

        $intUserID = 0;

        // address
        $strAddress = '';
        if ($intDeliveryID == 7) { // самовывоз
            $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/dpd_terminals'))->fetch();
            $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
            $strEntityDataClass = $obEntity->getDataClass();

            $rsData = $strEntityDataClass::getList(array(
                'select' => array('UF_CITYCODE', 'UF_CITYID', 'UF_DATA_SOURCE'),
                'filter' => array(
                    'UF_TERMINALCODE' => $_REQUEST['point']
                ),
            ));
            if ($arItem = $rsData->fetch()) {
                $data_pio = unserialize($arItem['UF_DATA_SOURCE']);
                echo $strAddress = $data_pio['address']['streetAbbr'] . ' ' . $data_pio['address']['street'] . ' ' . $data_pio['address']['houseNo'];
            }

            $dserv = $obRequest->get('dserv_blv');
        } else {
            echo $strAddress = $obRequest->get('address');
            $dserv = $obRequest->get('dserv');
        }

        if ($USER->GetID() == NULL) {
            $rsUser = CUser::GetByLogin($strEmail);
            if ($arUser = $rsUser->Fetch()) {
                // Пользователь существует
                $intUserID = $arUser['ID'];
            } else {
                // makcrx:begin проверка по телефону
                $arUser = array();
                $arUser['ID'] = 0;
                if ($strPhone != '') {
                    $arUser = Bitrix\Main\UserTable::getRow(array(
                        'filter' => array(
                            "=PERSONAL_PHONE" => $strPhone,
                            "EXTERNAL_AUTH_ID" => ''
                        ),
                        'select' => array('ID')
                    ));
                }

                if (intval($arUser['ID']) > 0) {
                    // Пользователь существует
                    $intUserID = $arUser['ID'];
                } else {

                    // makcrx:end

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
            }
            //$USER->Authorize($intUserID); // AK: Баг, позволяющий авторизоваться юзеру только по email при покупке.
        } else {
            $intUserID = $USER->GetID();
        }

        if (!$intUserID) {
            $intUserID = \CSaleUser::GetAnonymousUserID();
        } else {
            $this->updateUserFields($intUserID, $strName, $strPhone, $strCity, $strAddress);
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
            'BASE_PRICE_DELIVERY' => $intDeliveryPrice,
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
            'SUM' => $obOrder->getDeliveryPrice()+$this->order->getPrice()
        ));

        // property
        $obOrder->doFinalAction(true);
        $propertyCollection = $obOrder->getPropertyCollection();

        if ($strRegion) {
            $strRegion .= ', ';
        }

        $isNotCall = ($obRequest->get('not-call') == '1') ? 'Y' : 'N';

        if ($intPersonType == 1) {// физлицо
            // город
            $obProperty = $this->getPropertyByCode($propertyCollection, 'F_CITY');
            $obProperty->setValue($strCity);
            // DPD_CODE
            $obProperty = $this->getPropertyByCode($propertyCollection, 'DPD_CODE');
            $obProperty->setValue($strDPD_CODE);
            // PROMOCODE
            $obProperty = $this->getPropertyByCode($propertyCollection, 'uf_promo');
            $obProperty->setValue($strPromoCode);
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
            $obProperty->setValue($strRegion.$strCity.', '.$strAddress);
            // комментарий курьеру
            $obProperty = $this->getPropertyByCode($propertyCollection, 'F_ADDRESS_COMMENT');
            $obProperty->setValue(trim($obRequest->get('delivery_comment')));
            // код терминала
            $obProperty = $this->getPropertyByCode($propertyCollection, 'DPD_TERMINAL_CODE');
            $obProperty->setValue($point);
            //Не звонить для проверки заказа
            $obProperty = $this->getPropertyByCode($propertyCollection, 'F_NOT_CALL');
            $obProperty->setValue($isNotCall);

            $dserv_prop_name = 'dop_services';
        } else {// юрлицо
            // город
            $obProperty = $this->getPropertyByCode($propertyCollection, 'U_CITY');
            $obProperty->setValue($strCity);
            // DPD_CODE
            $obProperty = $this->getPropertyByCode($propertyCollection, 'U_DPD_CODE');
            $obProperty->setValue($strDPD_CODE);
            // PROMOCODE


            $obProperty = $this->getPropertyByCode($propertyCollection, 'uuf_promo');
            $obProperty->setValue($strPromoCode);
            // контактное лицо
            $obProperty = $this->getPropertyByCode($propertyCollection, 'U_NAME');
            $obProperty->setValue($strName);
            // email
            $obProperty = $this->getPropertyByCode($propertyCollection, 'U_EMAIL');
            $obProperty->setValue($strEmail);
            // телефон
            $obProperty = $this->getPropertyByCode($propertyCollection, 'U_PHONE');
            $obProperty->setValue($strPhone);
            // инн
            $obProperty = $this->getPropertyByCode($propertyCollection, 'U_INN');
            $obProperty->setValue($strINN);
            // адрес
            $obProperty = $this->getPropertyByCode($propertyCollection, 'U_ADDRESS');
            $obProperty->setValue($strRegion.$strCity.', '.$strAddress);
            // комментарий курьеру
            $obProperty = $this->getPropertyByCode($propertyCollection, 'U_ADDRESS_COMMENT');
            $obProperty->setValue(trim($obRequest->get('delivery_comment')));
            // код терминала
            $obProperty = $this->getPropertyByCode($propertyCollection, 'DPD_TERMINAL_CODE');
            $obProperty->setValue($point);
            //Не звонить для проверки заказа
            $obProperty = $this->getPropertyByCode($propertyCollection, 'U_NOT_CALL');
            $obProperty->setValue($isNotCall);

            $dserv_prop_name = 'u_dop_services';
        }

        //записываем доп. услуги
        if (is_array($dserv) && !empty($dserv)) {
            $dserv_vals = array();
            foreach ($dserv as $val) {
                $dserv_vals[] = $val;
            }
            $obProperty = $this->getPropertyByCode($propertyCollection, $dserv_prop_name);
            $obProperty->setValue($dserv_vals);
        }
        // Сохраняем Roistat ID в заказе для последующей передачи в RetailCRM.
        if (!empty($_COOKIE['roistat_visit'])) {
            $obProperty = $this->getPropertyByCode($propertyCollection, 'ROISTAT_ID');
            $obProperty->setValue(trim($_COOKIE['roistat_visit']));
        }

        $obOrder->setField('CURRENCY', $strCurrencyCode);
        $obOrder->setField('USER_DESCRIPTION',
            $obRequest->get('dpd_not_availabel') != 'Y' ? trim($obRequest->get('order-comment')) : trim($obRequest->get('order-comment')) . " \r\n Сервис расчета доставки DPD, в данный момент недоступен"
        );
        $_SESSION['ORDER_PAY_SYSTEM_ID'] = $obOrder->getPaymentSystemId();
        $_SESSION['ORDER_DATA']['PRICE'] = $obOrder->getPrice();
        $_SESSION['ORDER_DATA']['PRICE_DELIVERY'] = $obOrder->getDeliveryPrice();
        $_SESSION['ORDER_DATA']['TAX_VALUE'] = $obOrder->getTaxPrice();
        $obRes = $obOrder->save();
        $_SESSION['ORDER_ID'] = $obOrder->GetId();
        $_SESSION['ORDER_EMAIL'] = $strEmail;
        $_SESSION['DELIVERY_DATE'] = in_array($strCity, array('Санкт-Петербург', 'Москва'))? date('Y-m-d', strtotime("+1 week")): date('Y-m-d', strtotime("+12 days"));

        /*
        // mail
        if (is_numeric($_SESSION['ORDER_ID']) && $_SESSION['ORDER_ID'] > 0) {
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
        }
        */

        return $obRes;
    }

    /**
     * @param string $strSource
     */
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

    /**
     * Заполняет данные пользователя, если они были пустыми
     * @param $userId
     * @param $strName
     * @param $strPhone
     * @param $strCity
     * @param $strAddress
     */
    private function updateUserFields($userId, $strName, $strPhone, $strCity, $strAddress)
    {
        $rsUser = \Bitrix\Main\UserTable::getList(array(
            'filter' => array(
                'ID' => $userId
            ),
            'select' => array(
                'ID',
                'NAME',
                'PERSONAL_PHONE',
                'PERSONAL_CITY',
                'PERSONAL_STREET'
            )
        ));

        if ($arUser = $rsUser->fetch()) {
            $newUserFields = array();
            if (empty($arUser['NAME'])) {
                $newUserFields['NAME'] = $strName;
            }
            if (empty($arUser['PERSONAL_PHONE'])) {
                $newUserFields['PERSONAL_PHONE'] = $strPhone;
            }
            if (empty($arUser['PERSONAL_CITY'])) {
                $newUserFields['PERSONAL_CITY'] = $strCity;
            }
            if (empty($arUser['PERSONAL_STREET'])) {
                $newUserFields['PERSONAL_STREET'] = $strAddress;
            }

            if (!empty($newUserFields)) {
                $user = new \CUser;
                $user->Update($userId, $newUserFields);
            }
        }
    }
}
