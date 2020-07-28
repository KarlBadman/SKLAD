<?php

class crutchForOrder
{

    public function setCoupon($coupon = '',$sessionId=''){ // обработчик купонов
        if(!empty($coupon) && bitrix_sessid() == $sessionId) {
            if($arCoupon = CCatalogDiscountCoupon::SetCoupon($coupon)) {
                return 'Y';
            }else{
                return $arCoupon;
            }
        }else{
            return 'N';
        }
    }

    public function ubdateUserSpan($check,$sessionId){ // узнаем подписан ли user на рассылку
        if(bitrix_sessid() == $sessionId) {
            $user = new CUser;
            if ($check == 'Y') {
                echo $user->Update($user->GetID(), array('UF_SPAM' => 'Y'));
            }else{
                echo $user->Update($user->GetID(), array('UF_SPAM' => 'N'));
            }
        }else{
            echo 'N';
        }
    }

    public function quantityChange($quantity=0,$productId=0,$del='N',$sessionId='') { // обработчик изменения количества товаров

        if(!empty($productId) && bitrix_sessid() == $sessionId) {

            settype($quantity, 'integer');
            settype($productId, 'integer');

            if($quantity < 1 && $del!='Y')$quantity = 1;

            if($quantity > 500 && $del!='Y')$quantity = 500;

            $basket = \Bitrix\Sale\Basket::loadItemsForFUser(
                \Bitrix\Sale\Fuser::getId(),
                \Bitrix\Main\Context::getCurrent()->getSite()
            );

            if($quantity == 0){
                $basket->getItemById($productId)->delete();
            }else {
                $basket->getItemById($productId)->setField('QUANTITY', $quantity);
            }

            foreach ($basket as $basketItem) {

               if(CIBlockElement::GetIBlockByID($basketItem->getProductId()) == \Dsklad\Config::getParam('iblock/basket_services')){
                   $basketItem->setFields(array(
                       'PRICE' => ($basket->getPrice()-$basketItem->getField('PRICE'))/10,
                   ));
               };
            }

            $basket->save();

            return 'Y';

        }else{

            return 'N';
        }
    }

    public function basketServiceСhange($productId=0,$sessionId=''){ // обработчик добавления услуг в корзине
        if(!empty($productId) && bitrix_sessid() == $sessionId) {

            \Bitrix\Main\Loader::includeModule("catalog");

            $basket = \Bitrix\Sale\Basket::loadItemsForFUser(
                \Bitrix\Sale\Fuser::getId(),
                \Bitrix\Main\Context::getCurrent()->getSite()
            );

            if ($service = $basket->getExistsItem('catalog', $productId)) {
                $service->delete();
                $basket->save();
                return 'Y';
            }else{
                $item = $basket->createItem('catalog', $productId);
                $item->setFields(array(
                    'QUANTITY' => 1,
                    'CURRENCY' => Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                    'LID' => Bitrix\Main\Context::getCurrent()->getSite(),
                    'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
                    'PRICE' => $basket->getPrice()/10,
                    'CUSTOM_PRICE' => 'Y',
                ));

                $basket->save();

                return 'Y';
            }
        }else{
            return 'N';
        }
    }

    public function changeTerminal($terminal='',$sessionId='',$minPack = 0,$intCityCode = 0){ // обработчик терминала

        if(!empty($sessionId) && bitrix_sessid() == $sessionId) {

            if($minPack) return 'N';

            \Bitrix\Main\Loader::includeModule('dsklad.site');
            \Bitrix\Main\Loader::includeModule('highloadblock');

            $cityParams = \Dsklad\Order::getCityParams($intCityCode);

            if (!in_array($cityParams['CODE'], array(77000000000, 78000000000))) return 'N';

            $arHLBlockTerm = \Bitrix\Highloadblock\HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/dpd_terminals'))->fetch();
            $obEntityTerm = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlockTerm);
            $strEntityTermDataClass = $obEntityTerm->getDataClass();

            $rsTermData = $strEntityTermDataClass::getList([
                'select' => [
                    'UF_GABAPIT_MAX',
                    'UF_MAX_SUM',
                    'UF_MAX_VES_OTPAVKI',
                    'UF_MAX_VES_UPAKOVKI',
                    'UF_TERMINALCODE'
                ],
                'order' => ['UF_GABAPIT_MAX' => 'DESC', 'ID' => 'ASC'],
                'filter' => ['UF_CITYCODE' => $cityParams['CODE'], 'UF_TERMINALCODE' => $terminal]
            ]);

            $terminalToogle = $cityParams['CODE'];

            while ($arResTerm = $rsTermData->Fetch()) {
                if (($arResTerm['UF_GABAPIT_MAX'] == "без ограничений" || $arResTerm['UF_GABAPIT_MAX'] == "") AND ($arResTerm['UF_MAX_SUM'] == '' || $arResTerm['UF_MAX_SUM'] == 'без ограничений') AND ($arResTerm['UF_MAX_VES_OTPAVKI'] == '' || $arResTerm['UF_MAX_VES_OTPAVKI'] == 'без ограничений')) {
                    $terminalToogle = 'N';
                }
            }

            return $terminalToogle;

        }else{
            return 'NO!';
        }

    }

    public function propTerminal($arParamsTerminal = array(), $arPersonType = array()){ // свойство для заказа терминала
        $terminals = array();
        foreach ($arParamsTerminal as $value){
            $code = explode("_", $value);
            $terminals[$code[0]] = $code[1];
        }

        foreach ($arPersonType as $value){
            if($value['CHECKED'] == 'Y'){
                $propTerminal = $terminals[$value['ID']];
            }
        }

        return $propTerminal;

    }

    private static function basketService($iblockDopId = 37){ // Получаем услуги для корзины

        $arResult['SERVICE_TO_BASKET'] = array();
        $arFilter = array('IBLOCK_ID' => $iblockDopId,'ACTIVE'=>'Y');
        $arBasketServicesId = array(); //масив id услуг корзины
        $arBasketServices = array();
        $arSelect = array('ID', 'NAME','PROPERTY_SVG_SPRITE','PROPERTY_SVG_ANCHOR','PREVIEW_TEXT');
        $res = CIBlockElement::getList(array(), $arFilter, false, false, $arSelect);
        while ($row = $res->fetch()) {
            if (!empty('PREVIEW_TEXT')) $row['PROPERTY_SVG_SPRITE'] = CFile::GetPath($row["PROPERTY_SVG_SPRITE"]);
            $arBasketServices[$row['ID']] = $row;
            $arBasketServicesId[] = $row['ID'];
        }

        return array('arId'=>$arBasketServicesId, 'arService'=>$arBasketServices);
    }

    public static function  GetTerminals($basket = array(),$idParams = 0,$idPaySistem = 0,$price = 0){ // получаем терминалы

        \Bitrix\Main\Loader::includeModule('dsklad.site');
        $arData = \Dsklad\Order::getDPDTerminals($basket);

        $arResult['mapParams'] = $arData['mapParams'];
        $arResult['TERMINAL'] = array();

        foreach ($arData['TERMINAL'] as $arItem) {
            if(array_search($idPaySistem,$idParams) !==false && $arItem['npp_sum'] < $price) continue; // убираем терминалы где нет наложенного платежа
            if (!empty($arItem['schedule']['operation'])) {
                $arItem['schedule'] = array($arItem['schedule']);
            }
            $arItem['address']['terminalAddress'] = 'г. ' . $arItem['address']['cityName'] . ', ';
            $arItem['address']['terminalAddress'] .= $arItem['address']['streetAbbr'] . ', ';
            $arItem['address']['terminalAddress'] .= $arItem['address']['street'] . ', ';
            $arItem['address']['terminalAddress'] .= (!empty($arItem['address']['houseNo'])) ? $arItem['address']['houseNo'] : $arItem['address']['ownership'];
            $arResult['TERMINAL'][] = $arItem;
        }
        return $arResult;
    }

    private function noMapActiveDelivery($mapDelivery,$arDelivery){ // убираем карту если нет терминалов

        foreach ($mapDelivery as $valDel){
          unset($arDelivery[$valDel]);
        }

        return array('DELIVERY'=>$arDelivery,'DELIVERY_ID_ACTIVE'=>$activeId);
    }

    private function getFotografia($arId){ // получаем фото торговых предложений
        $arHLBlockTerm = \Bitrix\Highloadblock\HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
        $obEntityTerm = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlockTerm);
        $strEntityTermDataClass = $obEntityTerm->getDataClass();

        $arFoto = array();

        $rsTermData = $strEntityTermDataClass::getList([
            'select' => [
                'UF_FILE',
                'UF_XML_ID'
            ],
            'filter' => ['UF_XML_ID' => $arId]
        ]);

        while ($arResTerm = $rsTermData->Fetch()) {
            $arFoto[] = array('ID'=>$arResTerm['UF_XML_ID'],'SRC'=>CFile::GetPath($arResTerm["UF_FILE"]));
        }

        return $arFoto;
    }

    private static function getUserSpam(){ // узнаем подписан ли user на рассылку
        global $USER;
        $rsUser = CUser::GetByID($USER->GetID());
        $arUser = $rsUser->Fetch();
        return $arUser['UF_SPAM'];
    }

    private function showDelivery($arDelivery = array(),$showDelivery = array()){ // убираем доставки ненужные пользователю

        $arNewDelivery = array();
        foreach ($showDelivery as $val){
            if(!empty($arDelivery[$val])) $arNewDelivery[$val] = $arDelivery[$val];
        }

        return $arNewDelivery;
    }

    private function getDeliveryServices($delivery = array(),$city = array()){ // Дополнительные услуги доставки

        if(!empty($city['CONDITIONS'])){
            $services = (array)json_decode($city['CONDITIONS']);
        }else{
            $services = false;
        }

        if($services) {

            if($services['D_WEEKEND_1'] == 'Y' && $services['D_WEEKEND_2'] == 'Y'){
                $services['D_WEEKEND_1'] = 'N';
                $services['D_WEEKEND_2'] = 'N';
                $services['D_WEEKEND_3'] = 'Y';
            }else{
                $services['D_WEEKEND_3'] = 'N';
            }

            foreach ($delivery as $keyDelivery => $valDelivery) {
                if(!empty($valDelivery['EXTRA_SERVICES'])){
                    foreach ($valDelivery['EXTRA_SERVICES'] as $keyServices => $valServises){
                        $code = $valServises->getCode();
                        if($code !='NO_TERMINAL') {
                            if ($services[$code] == 'N') {
                                unset($delivery[$keyDelivery]['EXTRA_SERVICES'][$keyServices]);
                            }
                        }
                    }
                }
            }
        }else{
            foreach ($delivery as $keyDelivery => $valDelivery) {
                if(!empty($valDelivery['EXTRA_SERVICES'])){
                    foreach ($valDelivery['EXTRA_SERVICES'] as $keyServices => $valServises){
                        $code = $valServises->getCode();
                        if($code =='D_WEEKEND_1' || $code =='D_WEEKEND_2' || $code == 'D_UP_LIFT' || $code == 'D_EVENING' || $code == 'D_WEEKEND_3') {
                            unset($delivery[$keyDelivery]['EXTRA_SERVICES'][$keyServices]);
                        }
                    }
                }
            }
        }

        return $delivery;
    }

    private function removeCash($payments = array(),$paymentsID = array(), $switch = 'N'){ // если у дпд нет наличного расчета, отключаем его


        if($switch == 'Y'){
            foreach ($payments as $key => $value){
                foreach ($paymentsID as $id){
                    if($value['ID'] == $id){
                        unset($payments[$key]);
                    }
                }
            }
        }

        return $payments;
    }

    private function  orderPropSort($arProp = array()) { //сортируем свойства в соответствии с индексом

        $arProp['GLOBAL_PROPS'] = array_merge($arProp['USER_PROPS_N'],$arProp['USER_PROPS_Y']);

        uasort($arProp['GLOBAL_PROPS'], function ($a, $b) {
            return (int)$a['SORT'] - (int)$b['SORT'];
        });

        return $arProp;
    }

    private function getPropsAction($arProp=array(),$arPost=array()){ // подставляет значение свойств из предыдущего типа плательщика

        $arProp = self::orderPropSort($arProp);

        if(!empty($arPost['PERSON_TYPE']) && $arPost['PERSON_TYPE'] != $arPost['PERSON_TYPE_OLD']) {

            $arOldProps = array();
            $db_props = CSaleOrderProps::GetList(array(), array(), false, false, array());

            while ($props = $db_props->Fetch()) {
                if ($props['PERSON_TYPE_ID'] == $arPost['PERSON_TYPE_OLD']) {
                    $newKey = str_replace(["U_","F_"], "", $props['CODE']);
                    foreach ($arPost as $key => $post) {
                        if (strripos($key, 'ORDER_PROP_') !== false) {
                            $newKeyPost = str_replace("ORDER_PROP_", "", $key);
                            if($newKeyPost == $props['ID']){
                                $arOldProps[$newKey] = $post;
                            }
                        }
                    }
                }
            }

            foreach ($arProp['GLOBAL_PROPS'] as $keyProp =>$prop){
                foreach ($arOldProps as $keyOldProp => $propOld){
                    if($keyOldProp == str_replace(["U_","F_"], "", $prop['CODE'])){
                        $arProp['GLOBAL_PROPS'][$keyProp]['VALUE'] = $propOld;
                    }
                }
            }
        }

       return $arProp;
    }

    private function getPaySystemAction($arPaySystem=array(),$arPost=array()){ // сохраняем активную платежную систему при смене типа плательщика

        uasort($arPaySystem, function ($a, $b) {
            return (int)$a['ID'] - (int)$b['ID'];
        });

        if(!empty($arPost['PERSON_TYPE']) && $arPost['PERSON_TYPE'] != $arPost['PERSON_TYPE_OLD'] && is_array($arPaySystem)) {
            $checked = false;

            foreach ($arPaySystem as $key => $pay){
                if($pay['ID'] == $arPost['PAY_SYSTEM_ID']){
                    $arPaySystem[$key]['CHECKED'] = 'Y';
                    $checked = true;
                }else{
                    unset($arPaySystem[$key]['CHECKED']);
                }
            }

            if(!$checked){
                $arPaySystem[0]['CHECKED'] = "Y";
            }


        }

        return $arPaySystem;

    }

    private function removeServicesBasket($services=array(),$accessories=false,$empty=false,$basket=false){
        if(is_object($basket) && is_array($services) && ($accessories || $empty)){
            foreach ($services as $id){
                $basket->getItemById($id)->delete();
                $basket->save();
            }
        }
    }



    public function  modificationArResult($arResult,$arParams,$iblockDopId = 37){

        $arIdBasket = array();  //  массив ID товаров корзины
        $arBasketTerminal = array(); // массив корзины для терминалов
        $arElement = array(); // массив элементов из корзины
        $arSectionId = array(); // массив ID разделов
        $totalDiscountPrice = 0; // сумма скидки с учетом диапазонов
        $arFoto = array(); // массив фотографий товаров
        $arFotoId = array(); // массив id фотографий
        $counBasket = 0; // количество товаров в корзине
        $servicesPrice = 0; //Цена доп. услуг корзины
        $arSfetoforQuantity = 0; // Количество товаров в светофоре
        $accessories = true; // Только если аксессуары в корзине
        $empty = true; // Только если услуги в корзине
        $arBasketServicesProductId = array(); // масив id выброных услуг корзины
        $padushkiCount = 0; // Количество подушек
        $noski = true; // Только ли носки
        $padushki = true; // Только подушки
        $basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(),\Bitrix\Main\Context::getCurrent()->getSite());

        $arBasketServices = self::basketService($iblockDopId); // услуги для корзины

        $i = 0;
        foreach ($arResult["GRID"]["ROWS"] as $k => $arData){
            if (array_search($arData['data']['PRODUCT_ID'], $arBasketServices['arId']) !== false) {
                $arBasketServices['arService'][$arResult["GRID"]["ROWS"][$k]['data']['PRODUCT_ID']]['CHECkED'] = 'Y';
                $arBasketServicesProductId[] = $arData['id'];

                $basketItems = $basket->getBasketItems();
                $item = $basketItems[$i];
                $item->setFields(array(
                    'PRICE' => ($basket->getPrice()-$item->getField('PRICE'))/10,
                ));
                $basket->save();

                $servicesPrice = $servicesPrice + ($basket->getPrice()-$item->getField('PRICE'))/10;

                unset($arResult["GRID"]["ROWS"][$k]);
            } else {

                $offer = CCatalogProduct::GetByIDEx($arData['data']["PRODUCT_ID"]);
                if (!$offer || !CCatalogProduct::GetByIDEx($offer['PROPERTIES']['CML2_LINK']['VALUE'])) {
                    $basket->getItemById($arData['id'])->delete();
                    $basket->save();
                    unset($arResult["GRID"]["ROWS"][$k]);//Удаляем из корзины не активные товары
                    continue;
                }

                $empty = false;

                $counBasket = $counBasket + $arData['data']['QUANTITY'];

                $sku = CCatalogSku::GetProductInfo($arData['data']['PRODUCT_ID']); // узнаем это торговое предложение или нет

                $arResult["GRID"]["ROWS"][$k]['data']['PARENT_ID'] = $sku['ID'];

                if(!empty($arData['data']['PROPERTY_FOTOGRAFIYA_1_VALUE'])){
                    $arFotoId[] = $arData['data']['PROPERTY_FOTOGRAFIYA_1_VALUE'];
                }

                if (is_array($sku)) {

                    $arBasketTerminal[$sku['ID']] = array( // добавляем товар для метода терминала
                        'ID' => $arData['id'],
                        'PRODUCT_ID' => $arData['data']['PRODUCT_ID'],
                        'QUANTITY' => $arData['data']['QUANTITY'],
                        'PARENT' => $sku['ID'],
                        'PACK' => GetPackData($arData['data']['PRODUCT_XML_ID']), // упаковки товара
                    );

                    $arIdBasket[$sku['ID']] = $sku['ID'];

                    $arElement[$sku['ID']] = array('ORDER_ID'=>$arData['id'],'ELEMENT_ID'=>$sku['ID']);

                } else {

                    $arBasketTerminal[$arData['data']['PRODUCT_ID']] = array( // добавляем товар для метода терминала
                        'ID' => $arData['id'],
                        'PRODUCT_ID' => $arData['data']['PRODUCT_ID'],
                        'QUANTITY' => $arData['data']['QUANTITY'],
                        'PACK' => GetPackData($arData['data']['PRODUCT_XML_ID']), // упаковки товара
                    );

                    $arIdBasket[$arData['data']['PRODUCT_ID']] = $arData['data']['PRODUCT_ID'];

                    $arElement[$arData['data']['PRODUCT_ID']] = array('ORDER_ID'=>$arData['id'],'ELEMENT_ID'=>$arData['data']['PRODUCT_ID']);
                }

                // Получаем количество товаров для светофора
                if($arParams['SVETOFOR_OK'] == 'Y' &&
                    array_search($arData['data']['PRODUCT_ID'],$arParams['SVETOFOR_NO_ID']) === false &&
                    array_search($arData['data']['PROPERTY_CML2_ARTICLE_VALUE'],$arParams['SVETOFOR_ARTICUL']) !== false){
                    $arSfetoforQuantity = $arSfetoforQuantity + $arData['data']['QUANTITY'];
                }

            }
            $i++;
        }

        if(empty($arResult["GRID"]["ROWS"]) && !empty($arResult["ORDER_ID"])){
            //формируем данные по товарам для страницы спасибо
            $arItems =  \Bitrix\Sale\Order::load($arResult["ORDER_ID"])->getBasket();
            foreach ($arItems as $basketItem) {
                $arResult["GRID"]["ROWS"][] = Array(
                    'id' => $basketItem->getField('PRODUCT_ID'),
                    'name'=>$basketItem->getField('NAME'),
                    'quantity'=>(int)$basketItem->getField('QUANTITY'),
                    'price'=>$basketItem->getField('PRICE'),
                );
            }
            $arResult["GRID"]["JSON_DATA"] = htmlentities(json_encode($arResult["GRID"]["ROWS"]), ENT_QUOTES, 'UTF-8');
        }

        $arFoto = self::getFotografia($arFotoId); // получаем фото торговых предложений

        $resultElement = \Bitrix\Iblock\ElementTable::getList(array( // Получаем элементы
            'select' => array('ID', 'IBLOCK_SECTION_ID', 'NAME'),
            'filter' => array('ID' => $arIdBasket),
        ));

        while ($row = $resultElement->fetch())
        {
            $arElement[$row['ID']] = array_merge($arElement[$row['ID']],$row);
            $arSectionId[] = $row['IBLOCK_SECTION_ID'];
            $arBasketTerminal[$row['ID']]['IBLOCK_SECTION_ID'] = $row['IBLOCK_SECTION_ID'];

        }

        $resultSection = CIBlockSection::GetList( // Получаем разделы
            array(),
            array('ID' => $arSectionId),
            false,
            array('ID', 'SECTION_PAGE_URL', 'NAME','CODE')
            );

        while ($row = $resultSection->GetNext())
        {
            $arSection[] = $row;

            $arItemBasket['SECTION'] = $row['NAME'];
            $arItemBasket['SECTION_URL'] = $row['SECTION_PAGE_URL'];
            $arItemBasket['CODE'] = $row['CODE'];

            foreach ($arBasketTerminal as $key => $value){ // Добавляем данные о разделах в массив для терминала
                if($value['IBLOCK_SECTION_ID'] == $row['ID']){
                    $arBasketTerminal[$key]['SECTION'] = $row['NAME'];
                    $arBasketTerminal[$key]['SECTION_URL'] = $row['SECTION_PAGE_URL'];
                    $arBasketTerminal[$key]['CODE'] = $row['CODE'];
                }
            }
        }

        $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
        $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $photoEntityDataClass = $obEntity->getDataClass();

        // Повторно проходимся по корзине, записывая полученные ранее данные
        foreach ($arResult["GRID"]["ROWS"] as $k => $arData){
            // проходимся по элементам

            // Проверяем есть ли что-то кроме подушек или носков
            if(strripos($arData['data']['NAME'],'носки') === false){
                $noski = false;
            }
            if(strripos($arData['data']['NAME'],'Подушка') !== false){
                $padushkiCount = $padushkiCount + $arData['data']['QUANTITY'];
            }else{
                $padushki = false;
            }

            foreach ($arElement as $keyElement => $valueElement){

                // заменяем название предложения на название элемента

                if($valueElement['ELEMENT_ID'] == $arData['data']['PARENT_ID']){

                    $arResult["GRID"]["ROWS"][$k]['data']['NAME'] = $valueElement['NAME'];
                }

                // Записываем информацию о разделах
                foreach ($arSection as $keySection => $valueSection){
                    if($valueSection['ID'] == $valueElement['IBLOCK_SECTION_ID'] && $valueElement['ELEMENT_ID'] == $arData['data']['PARENT_ID']){
                        $arResult["GRID"]["ROWS"][$k]['data']['SECTION'] = $valueSection;
                        //Прверяем является ли данный раздел аксесуаром
                        if($valueSection['CODE'] !='aksessuary'){
                            $accessories = false;
                        }
                        break;
                    }
                }
                
            }

            foreach ($arFoto as $foto) { // Добавляем фото к товарам
                if($arData['data']['PROPERTY_FOTOGRAFIYA_1_VALUE'] == $foto['ID'] && !empty($foto['SRC'])){
                    $rsData = $photoEntityDataClass::getList(array(
                        'select' => array('UF_FILE'),
                        'filter' => array('UF_XML_ID' => $arData['data']['PROPERTY_FOTOGRAFIYA_1_VALUE']),
                        'limit' => '50',
                    ));
                    if ($arItem = $rsData->fetch()) {
                        $arImage = CFile::ResizeImageGet($arItem['UF_FILE'], array('width' => 66, 'height' => 66), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
                        $arResult["GRID"]["ROWS"][$k]['data']['PREVIEW_PICTURE_SRC'] =  $arImage['src'];
                    } else {
                        $arResult["GRID"]["ROWS"][$k]['data']['PREVIEW_PICTURE_SRC'] =  '';
                    }
                }
            }

            $basePrise = CPrice::GetBasePrice($arData['data']['PRODUCT_ID']); // Получаем базавую цену
            // узнаем есть ли "скидка" через диапазон
            $basePrise['PRICE'] != $arData['data']['PRICE'] ? $arResult["GRID"]["ROWS"][$k]['data']['DISCOUNT_FLAG'] = true : $arResult["GRID"]["ROWS"][$k]['data']['DISCOUNT_FLAG'] = false;
            // узнаем есть ли товар в наличии
            $basePrise['PRODUCT_QUANTITY'] >= $arData['data']['QUANTITY'] ? $arResult["GRID"]["ROWS"][$k]['data']['AVAILABILITY_FLAG'] = true : $arResult["GRID"]["ROWS"][$k]['data']['AVAILABILITY_FLAG'] = false;
            // Узнаем скидку с диапазонами
            $totalDiscountPrice = $totalDiscountPrice + (($basePrise['PRICE'] - $arData['data']['PRICE'])*$arData['data']['QUANTITY']);
        }

        $city = \Dsklad\Order::getCityParams();

        if($arSfetoforQuantity < 4){ // узнаем сколько товаров нехватает для светофора
            $arSvetofor = 4 - $arSfetoforQuantity;
        }else{
            $arSvetofor = 4 - bcmod($arSfetoforQuantity,4);
        }

        // если у дпд нет наличного расчета, отключаем его, а также выбираем ту же платежную систему при смене типа плательщика
        $paySystem = self::getPaySystemAction(self::removeCash($arResult['PAY_SYSTEM'],$arParams['NAL'],$city['CASH_PAYMENT_OFF']),$_POST);

        self::removeServicesBasket($arBasketServicesProductId,$accessories,$empty,$basket); // удаляем услуги из корзины, если они больше не нужны

        if($noski || ($padushki && $padushkiCount==1)){
            $minPacketDelivery = true;
        }else{
            $minPacketDelivery = false;
        }

        $newArResult = array(
            'GRID'=>$arResult["GRID"], // измененная корзина
            'SERVICE_TO_BASKET'=>$arBasketServices['arService'], // Услуги корзины
            'DISCOUNT_PRICE'=>$totalDiscountPrice, // Сумма скидки с учетом диапазонов
            'TOTAL_PRICE'=> $arResult['PRICE_WITHOUT_DISCOUNT_VALUE'] + $totalDiscountPrice, // Цена без учетов диапазонов
            'PROP_TERMINAL' => self::propTerminal($arParams['PROP_TERMINAL'],$arResult['PERSON_TYPE']), // свойство для заказа терминала
            'SPAM'=>self::getUserSpam(),
            'COUNT_BASKET'=>$counBasket,
            'DELIVERY'=>self::getDeliveryServices(self::showDelivery($arResult['DELIVERY'],$arParams['SHOW_DELIVERY']),$city), // убираем доставки ненужные пользователю, а также оставляем доп услуги по ДПД
            'PAY_SYSTEM'=>$paySystem, // если у дпд нет наличного расчета, отключаем его
            'ORDER_PROP'=>self::getPropsAction($arResult["ORDER_PROP"],$_POST,$arResult["USER_VALS"]["PERSON_TYPE_ID"]), //сортируем свойства в соответствии с индексом
            'SERVICES_PRICE'=>$servicesPrice, // Цена доп. услуг
            'SVETOFOR'=>$arSvetofor, // сколько товаров нехватает для светофора
            'PROP_DPD_CODE'=>self::propTerminal($arParams['PROP_DPD_CODE'],$arResult['PERSON_TYPE']), // свойство для заказа код услуги дпд
            'PROP_NOT_CALL_CODE'=>self::propTerminal($arParams['PROP_NOT_CALL_CODE'],$arResult['PERSON_TYPE']), // свойство для заказа не звонить
            'NO_SERVICES_BASKET'=>$accessories, // Только аксесуары в корзине
            'MIN_PACK'=>$minPacketDelivery, // Отправка малой посылки
        );

        // Получаем терминалы и добавляем их в новый массив $arResult

        $arTerminal = self::GetTerminals($arBasketTerminal,$arParams['NAL'],$arResult['ORDER_DATA']['PAY_SYSTEM_ID'],$arResult['ORDER_TOTAL_PRICE']);

        if(empty($arTerminal['TERMINAL'])){
            $arDel = self::noMapActiveDelivery($arParams['MAP_DELIVERY'],$newArResult['DELIVERY']);
            $newArResult['DELIVERY'] = $arDel['DELIVERY'];
            if(!empty($arDel['DELIVERY_ID_ACTIVE'])) {
                $arResult['USER_VALS']['DELIVERY_ID'] = $arDel['DELIVERY_ID_ACTIVE'];
                $newArResult['USER_VALS'] = $arResult['USER_VALS'];
            }
        }

        $newArResult = array_merge($newArResult,$arTerminal);

        return $newArResult;
    }

}