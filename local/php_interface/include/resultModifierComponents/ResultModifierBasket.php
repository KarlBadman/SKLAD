<?php
class ResultModifierBasket {

    public $totalOldPrice = 0;
    public $percentPriceWarranty = 10; // процент от стоимости за доп.гарантию
    public $arBasketServiceId = array(); // масив id услуг в корзине
    public $arResult = array();
    public $basket;
    public $arProductId = array();
    public $recommended = array();
    public $photoEntityDataClass;
    public $quantity = 0;
    public $maxQuantityProduct = 500;
    public $warrantyPrice = 0;
    public $arParams = array();
    public $basketWarrantyId;
    public $preorder = false;

    function __construct() {
        $this->basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(),\Bitrix\Main\Context::getCurrent()->getSite());
        $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
        $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $this->photoEntityDataClass = $obEntity->getDataClass();
    }

    function getRangePrice($priceType = 2){ // Получаем диапазоны цен
        $arPrice = array();
        if(!empty($this->arProductId)){
            $dbProductPrice = CPrice::GetListEx(
                array(),
                array("PRODUCT_ID" => $this->arProductId,'CATALOG_GROUP_ID'=>$priceType),
                false,
                false,
                array('ID','PRODUCT_ID','PRICE','QUANTITY_FROM')
            );

            while ($row = $dbProductPrice->fetch()) {
                $arPrice[$row['PRODUCT_ID']][$row['QUANTITY_FROM']] = $row;
            }
        }
        return $arPrice;
    }

    function getProductId($arItems = array()){ // Массив id товаров
        $productId = array();
        if(!empty($arItems)) {
            foreach ($arItems as $key => $item) {
                $productId[] = $item['PRODUCT_ID'];
            }
        }
        return $productId;
    }

    function getRecommended($iblock_id,$element_id,$propertyCode){ // получаем рекомендованные товары
        $VALUES = array();
        if(!empty($propertyCode)) {
            $res = CIBlockElement::GetProperty($iblock_id, $element_id, array(), array("CODE" => $propertyCode));
            while ($ob = $res->GetNext()) {
                if (array_search($ob['VALUE'], $this->arProductId) == false)
                    $VALUES[$ob['VALUE']] = $ob['VALUE'];
            }
        }
        return $VALUES;
    }

    function getListItems($arItems=array(),$arPrice = array()){ // проходимся по всем товарам в корзине
        $totalOldPrice = 0;
        foreach ($arItems as $key => $item){
            if (array_search($item['PRODUCT_ID'], $this->arBasketServiceId) !== false) {
                $this->arResult['BASKET_SERVICE'][$item['PRODUCT_ID']]['CHECkED'] = 'Y';
                if($item['PRODUCT_ID'] == $this->arParams['ID_WARRANTY']) {
                    $this->basketWarrantyId = $item['ID'];
                    $this->arResult['SERVICE_OK'] = 'Y';
                }
                unset($arItems[$key]);
            }else{
                // Удаляем не активные товары
                $offer = CCatalogProduct::GetByIDEx($item["PRODUCT_ID"]);
                if (!$offer || !CCatalogProduct::GetByIDEx($offer['PROPERTIES']['CML2_LINK']['VALUE'])) {
                    $this->basket->getItemById($item['ID'])->delete();
                    $this->basket->save();
                    unset($arItems[$key]);
                    continue;
                }
                if ($item['AVAILABLE_QUANTITY'] <= 0) $this->preorder = true;
                $this->quantity += $item['QUANTITY'];
                $productPrice = $arPrice[$item['PRODUCT_ID']];
                $arItems[$key]['OLD_PRICE'] = CPrice::GetBasePrice($item['PRODUCT_ID'])['PRICE'] * $item['QUANTITY'];
                $totalOldPrice += $arItems[$key]['OLD_PRICE'];
                $arItems[$key]['QUANTITY_BEFORE_DISCOUNT'] = false;
                foreach ($productPrice as $quantity => $price) {
                    if ($item['QUANTITY'] < $quantity && $item['BASE_PRICE'] > $price['PRICE']) {
                        $arItems[$key]['QUANTITY_BEFORE_DISCOUNT'] = $quantity - $item['QUANTITY'];
                        $arItems[$key]['DIOPOSE_DISCOUNT_PRICE'] = ($item['BASE_PRICE'] - $price['PRICE']) * $quantity;
                        break;
                    }
                }

                if(!empty($item['PROPERTY_KOD_TSVETA_VALUE'])){
                    $arItems[$key]['PROPERTY_KOD_TSVETA_VALUE'] = explode('#',$item['PROPERTY_KOD_TSVETA_VALUE'])[0];
                }
                if(!empty($item['PROPERTY_FOTOGRAFIYA_1_VALUE'])){
                    $arItems[$key]['PHOTO_PRODUCT'] = self::getPhoto($item['PROPERTY_FOTOGRAFIYA_1_VALUE']);
                }

                $this->recommended = array_merge($this->recommended,$this->getRecommended(\Dsklad\Config::getParam('iblock/offers'),$item['PRODUCT_ID'],$this->propertyCodeRecommended));


                if($item['PROPERTY_DOPOLNITELNAYA_GARANTIYA_OTSUTSTVUET_VALUE']) {
                    $arItems[$key]['NO_WARANTY'] = true;
                }else{
                    $this->warrantyPrice = $this->warrantyPrice + round($item['SUM_VALUE'] / 100 * $this->percentPriceWarranty);
                }
            }
        }
        $this->totalOldPrice = $totalOldPrice;
        return $arItems;
    }

    function getPhoto($photoId){ // Получаем изображение товара
        $rsData = $this->photoEntityDataClass::getList(array(
            'select' => array('UF_FILE'),
            'filter' => array('UF_XML_ID' => $photoId),
            'limit' => '50',
        ));
        if ($arItem = $rsData->fetch()) {
            $arImage = CFile::GetFileArray($arItem['UF_FILE']);
            $arImage = CFile::ResizeImageGet($arImage, array('width' => 140, 'height' => 140), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
            return  $arImage['src'];
        }
    }

    function basketService($iBlockBasketService ){ // Получаем услуги для корзины
        $arResult['SERVICE_TO_BASKET'] = array();
        $arFilter = array('IBLOCK_ID' => $iBlockBasketService,'ACTIVE'=>'Y');
        $arBasketServices = array();
        $arBasketServiceId = array();
        $arSelect = array('ID', 'NAME','PROPERTY_SVG_SPRITE','PROPERTY_SVG_ANCHOR','PREVIEW_TEXT');
        $res = CIBlockElement::getList(array(), $arFilter, false, false, $arSelect);
        while ($row = $res->fetch()) {
            if (!empty('PREVIEW_TEXT')) $row['PROPERTY_SVG_SPRITE'] = CFile::GetPath($row["PROPERTY_SVG_SPRITE"]);
           // if($row['ID'] == $warrantyId) $row['PRICE'] = $warrantyPrice;
            $arBasketServices[$row['ID']] = $row;
            $arBasketServiceId[] = $row['ID'];
        }
        $this->arBasketServiceId = $arBasketServiceId;
        return $arBasketServices;
    }

    function basketServicePrice(){ // устанавливаем цену на доп гарантию
        foreach ($this->arResult['BASKET_SERVICE'] as $key => $servis){
            if($servis['ID'] == $this->arParams['ID_WARRANTY']){
                $this->arResult['BASKET_SERVICE'][$key]['PRICE'] = $this->warrantyPrice;
                if($this->arResult['SERVICE_OK'] == 'Y' && $this->basketWarrantyId > 0){
                    $itemBasket = $this->basket->getItemById($this->basketWarrantyId);
                    if($itemBasket->getPrice() != $this->warrantyPrice) {
                        $itemBasket->setFields(array('PRICE' => $this->warrantyPrice));
                        $this->basket->save();
                    }
                }
            }

        }
    }

    public function quantityChange($quantity=0,$productId=0,$del='N',$sessionId='') { // обработчик изменения количества товаров

        if(bitrix_sessid() != $sessionId)  return 'ERROR: ошибка сессии';

        settype($quantity, 'integer');
        settype($productId, 'integer');

        if($quantity < 1 && $del!='Y')$quantity = 1;

        if($quantity > $this->maxQuantityProduct && $del!='Y')$quantity = $this->maxQuantityProduct;

        if($quantity == 0){
            $this->basket->getItemById($productId)->delete();
        }else {
            $this->basket->getItemById($productId)->setField('QUANTITY', $quantity);
        }

        foreach ($this->basket as $basketItem) {

            if(CIBlockElement::GetIBlockByID($basketItem->getProductId()) == \Dsklad\Config::getParam('iblock/basket_services')){
                $basketItem->setFields(array(
                    'PRICE' => ($this->basket->getPrice()-$basketItem->getField('PRICE'))/100 * $this->percentPriceWarranty,
                ));
            };
        }

        $this->basket->save();

        return 'Y';
    }

    public function addProduct($productId,$quantity,$sessionId){ // Добовление товара в корзине

        \Bitrix\Main\Loader::includeModule("catalog");

        if(bitrix_sessid() != $sessionId)  return 'ERROR: ошибка сессии';
        
        if ($item = $this->basket->getExistsItem('catalog', $productId)) {
            $intQuantity = (int)$item->getField('QUANTITY') + $quantity;
            $item->setField('QUANTITY', $intQuantity);
            $result = $this->basket->save();
        } else {
            $result = \Bitrix\Catalog\Product\Basket::addProduct(array('PRODUCT_ID' => $productId,'QUANTITY' => $quantity));
        }
        
        if (!$result->isSuccess()) {
            var_dump($result->getErrorMessages());
        }else{
            return 'Y';
        }


    }

    public function basketServiceСhange($productId=0,$sessionId=''){ // обработчик добавления услуг в корзине

        if(bitrix_sessid() != $sessionId)  return 'ERROR: ошибка сессии';

        if ($service = $this->basket->getExistsItem('catalog', $productId)) {
            $service->delete();
            $this->basket->save();
            return 'Y';
        }else{
            $item = $this->basket->createItem('catalog', $productId);
            $product = CCatalogProduct::GetByIDEx($productId);
            $item->setFields(array(
                "NAME" => $product['NAME'],
                'QUANTITY' => 1,
                'CURRENCY' => Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                'LID' => Bitrix\Main\Context::getCurrent()->getSite(),
                'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
                'PRICE' => $this->basket->getPrice()/100 * $this->percentPriceWarranty,
                'CUSTOM_PRICE' => 'Y',
            ));

            $this->basket->save();

            return 'Y';
        }
    }

    function getUser($phone){ // Получаем пользователя по телефону
        $resUser = \Bitrix\Main\UserTable::getList(array(
            'filter' => array(array("LOGIC" => "OR", array('PERSONAL_PHONE'=>$phone), array('PERSONAL_PHONE'=>'+'.$phone))),
            'select'=>array('ID'),
            'order'=>array('ID'=>'ASC')
        ));
        return $resUser->fetch()['ID'];
    }

    function userAdd($name,$phone){ // Регистрируем нового пользователя пользователя
        $newUser = new CUser;
        $password = self::getPassword();
        $userId = $newUser->Add(array(
            'LOGIN'=>$phone,
            'NAME'=>$name,
            'PERSONAL_PHONE'=>$phone,
            'GROUP_ID'=>array(3,4),
            'PASSWORD'=> $password,
            'CONFIRM_PASSWORD'=>$password,
        ));

        if (intval($userId) > 0) {
            return $userId;
        }else {
            return 'ERROR: '.$newUser->LAST_ERROR;
        }
    }

    function getPassword($max = 6){ // Генерируем пароль
        $chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
        $size=StrLen($chars)-1;
        $password=null;
        while($max--) {
            $password .= $chars[rand(0, $size)];
        }
        return $password;
    }

    static function phoneMask($phone){ // Изменяем номер по маске
        $phone = preg_replace(array('/\D/'), '', $phone);

        if (  preg_match( '/^\d(\d{3})(\d{3})(\d{2})(\d{2})$/', $phone,  $matches ) ) {
            $result = '+7 '.$matches[1] . ' ' .$matches[2] . '-' . $matches[3]. '-' . $matches[4];
            return $result;
        } elseif ( preg_match( '/^(\d{3})(\d{2})(\d{3})(\d{2})(\d{2})$/', $phone,  $matches ) ) {
            $result = '+' . $matches[1] . ' ' .$matches[2] . ' ' . $matches[3]. '-' . $matches[4]. '-' . $matches[5];
            return $result;
        } else{
            return false;
        }
    }

    function getTerminal($basket){ // Получаем терминалы

        $arBasketTerminals = array();

        foreach ($basket as $item){
            $sku = CCatalogSku::GetProductInfo($item->getProductId());
            if(CIBlockElement::GetIBlockByID($item->getProductId()) != \Dsklad\Config::getParam('iblock/basket_services')) {
                $arBasketTerminals[] = array(
                    'ID' => $item->getId(),
                    'PRODUCT_ID' => $item->getProductId(),
                    'QUANTITY' => $item->getQuantity(),
                    'PARENT' => $sku['ID'],
                    'PACK' => GetPackData($item->getField('PRODUCT_XML_ID')), // упаковки товара
                );
            }
        }

        $arTerminal = \Dsklad\Order::getDPDTerminals($arBasketTerminals);

        return $arTerminal;
    }

    function getProperty(){ // Получаем свойства
        $db_props = CSaleOrderProps::GetList(array(), array('PERSON_TYPE_ID'=>1), false, false, array('ID','CODE'));
        $arProp = array();
        while ($props = $db_props->Fetch()) {
            $newKey = str_replace(["U_","F_"], "", $props['CODE']);
            $arProp[$newKey] = $props['ID'];
        }

        return $arProp;
    }

    function orderAdd($userId,$name,$phone){ // Создаем новый заказ

        \Bitrix\Main\Loader::includeModule('dsklad.site');

        $siteId = \Bitrix\Main\Context::getCurrent()->getSite();

        $basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(),$siteId);

        if(count($basket->getQuantityList()) == 0) return 'ERROR: корзина пуста';;

        $order =  \Bitrix\Sale\Order::create($siteId, $userId);
        $order->setBasket($basket);

        $terminals = $this->getTerminal($basket)['mapParams']['PLACEMARKS'];

        if(!empty($terminals)){
            $deliveryId =  \Dsklad\Config::getParam('on_click/delivery')['point'];
        }else{
            $deliveryId =  \Dsklad\Config::getParam('on_click/delivery')['courier'];
        }

        $shipmentCollection = $order->getShipmentCollection();
        $shipment = $shipmentCollection->createItem();
        $service = \Bitrix\Sale\Delivery\Services\Manager::getById($deliveryId);
        $shipment->setFields(array(
            'DELIVERY_ID' => $service['ID'],
            'DELIVERY_NAME' => $service['NAME'],
        ));
        $shipmentItemCollection = $shipment->getShipmentItemCollection();
        foreach ($order->getBasket() as $item){
            $shipmentItem = $shipmentItemCollection->createItem($item);
            $shipmentItem->setQuantity($item->getQuantity());
        }

        $paymentCollection = $order->getPaymentCollection();
        $payment = $paymentCollection->createItem();
        $paySystemService = \Bitrix\Sale\PaySystem\Manager::getObjectById(\Dsklad\Config::getParam('on_click/payment'));
        $payment->setFields(array(
            'PAY_SYSTEM_ID' => \Dsklad\Config::getParam('on_click/payment'),
            'PAY_SYSTEM_NAME' => $paySystemService->getField("NAME"),
            'SUM' => $order->getPrice(),
            'CURRENCY' => $order->getCurrency(),
        ));

        $propertyCollection = $order->getPropertyCollection();
        $propertyCollection->getPhone()->setValue($phone);
        $propertyCollection->getPayerName()->setValue($name);

        $arProps = $this->getProperty();

        $propertyCollection->getItemByOrderPropertyId($arProps['CITY'])->setValue($_SESSION['DPD_CITY_NAME']);
        $propertyCollection->getItemByOrderPropertyId($arProps['PAYMENT_METHOD'])->setValue('NAL');

        if(!empty($terminals)){
            $terminal = reset($terminals);
            $propertyCollection->getItemByOrderPropertyId($arProps['DPD_TERMINAL_CODE'])->setValue($terminal['TERMINAL']);
        }

        $order->setField('USER_DESCRIPTION', 'Заказ в 1 клик');
        $order->setField('PERSON_TYPE_ID', 1);

        $order->doFinalAction(true);
        $order->save();

        if (intval($order->getId()) > 0) {
            return $order->getId();
        }else{
            return 'ERROR: не удалось создать заказ';
        }
    }

    function  initOrder($name,$phone,$session,$basket = false, $productId = false, $quantity = false){ // Запускаем процедуру добавление нового заказа

        $noFormatphone = $phone;

        if(bitrix_sessid() != $session)  return 'ERROR: ошибка сессии';

        if(empty($name) || empty($phone)) return 'ERROR: не заполнены все поля';

        if(!$phone = self::phoneMask($phone)) return 'ERROR: не удалось разобрать телефон';

        if($basket === true){
            if(!is_numeric($productId) || !is_numeric($quantity)) return 'ERROR: нет данных о товаре';
            CSaleBasket::DeleteAll( CSaleBasket::GetBasketUserID());
            self::addProduct($productId,$quantity,$session);
        }

        global $USER;

        \Bitrix\Main\Loader::includeModule("sale");
        \Bitrix\Main\Loader::includeModule("catalog");

        if($USER->isAuthorized()){
            $userId = $USER->GetID();
        }else{
            if(empty($userId = self::getUser($noFormatphone))){
                if (!is_numeric($userId = self::userAdd($name,$noFormatphone))) {
                    return $userId;
                }
            }
        }

        $orderId = self::orderAdd($userId,$name,$noFormatphone);

        return $orderId;

    }

    function modificationArResult($arResult,$arParams){
        $this->arResult = $arResult;
        $this->arParams = $arParams;
        $this->propertyCodeRecommended = $arParams['RECOMMENDED_ITEMS_IN_CART'];
        $this->arProductId = $this->getProductId($this->arResult['GRID']['ROWS']);// Массив id товаров
        $arPrice = $this->getRangePrice($arParams['PRICE_TYPE']);// Получаем диопозон цен
        $this->arResult['BASKET_SERVICE'] = $this->basketService(\Dsklad\Config::getParam('iblock/basket_services'));// Получаем услуги для корзины
        $this->arResult['ITEMS']['AnDelCanBuy'] = $this->getListItems($this->arResult['GRID']['ROWS'],$arPrice);
        $this->basketServicePrice();
        $this->arResult['WARRANTY_PRICE'] = $this->warrantyPrice;
        $this->arResult['TOTAL_SUM_DISCOUNT'] = $this->totalOldPrice - $this->arResult['allSum']; // общая скидка включая диапазоны цен
        $this->arResult['OLD_TOTAL_PRICE'] = $this->totalOldPrice;
        $this->arResult['PRODUCT_QUANTITY'] = $this->quantity;
        $this->arResult['AR_PRODUCT_ID'] = $this->arProductId;
        $this->arResult['PREORDER'] = $this->preorder;
        $_SESSION['filterBasketRecommended'] =$GLOBALS['filterBasketRecommended']['ID'] = array_filter($this->recommended, function($element) {return !empty($element);});
        if(empty($this->arResult['GRID']['ROWS'])) $this->arResult['BASKET_EMPTY'] = true;
        return $this->arResult;
    }
}