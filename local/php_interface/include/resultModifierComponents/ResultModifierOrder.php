<?php
class ResultModifierOrder{

    public $arResult;
    public $arParams;
    public $city;
    public $basket;
    public $servicePrice = 0;
    public $arBasketTerminal;
    public $totalDiscountPrice = 0;
    public $countProduct = 0;
    public $countWarranty = 0;
    public $totalProductPrice = 0;
    public $arJsParams = array();

    function __construct() {
        $this->city = \Dsklad\Order::getCityParams();
        $this->basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(),\Bitrix\Main\Context::getCurrent()->getSite());
        $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
        $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $this->photoEntityDataClass = $obEntity->getDataClass();
    }

    // Изменение свойств
    function orderPropSort() { //сортируем свойства в соответствии с индексом

        $this->arResult["ORDER_PROP"]['GLOBAL_PROPS'] = array_merge($this->arResult["ORDER_PROP"]['USER_PROPS_N'],$this->arResult["ORDER_PROP"]['USER_PROPS_Y']);
        uasort($this->arResult["ORDER_PROP"]['GLOBAL_PROPS'], function ($a, $b) {
            return (int)$a['SORT'] - (int)$b['SORT'];
        });
    }

    // Хэш авторизации
    function getAuthorizeHash () {

        global $USER;

        if ($USER->IsAuthorized() && empty($this->arResult['CUSTOM_PROPS']['TOKEN']['VALUE'])) {
            $rsUser = CUser::GetByID($USER->GetId());
            $arUser = $rsUser->Fetch();
            $this->arResult['CUSTOM_PROPS']['TOKEN']['VALUE'] = md5($arUser['ID'] . $arUser['LOGIN'] . $arUser['BX_USER_ID'] . time());
        }
    }
    //

    function getPropUserAuth(){ // получаем свойтво телефон
        foreach ($this->arResult['ORDER_PROP']['GLOBAL_PROPS'] as $prop){
            if($prop['PERSON_TYPE_ID'] == $this->arResult['USER_VALS']['PERSON_TYPE_ID']){
                $this->arResult['CUSTOM_PROPS'][str_replace(["U_","F_"], "", $prop['CODE'])] = $prop;
                $this->arJsParams['PROP'][str_replace(["U_","F_"], "", $prop['CODE'])] = "ORDER_PROP_".$prop['ID'];
            }
        }
     }

    function getPropsAction(){ // подставляет значение свойств из предыдущего типа плательщика

        if(!empty($_POST['PERSON_TYPE']) && $_POST['PERSON_TYPE'] != $_POST['PERSON_TYPE_OLD']) {

            $arOldProps = array();
            $db_props = CSaleOrderProps::GetList(array(), array(), false, false, array());

            while ($props = $db_props->Fetch()) {
                if ($props['PERSON_TYPE_ID'] == $_POST['PERSON_TYPE_OLD']) {
                    $newKey = str_replace(["U_","F_"], "", $props['CODE']);
                    foreach ($_POST as $key => $post) {
                        if (strripos($key, 'ORDER_PROP_') !== false) {
                            $newKeyPost = str_replace("ORDER_PROP_", "", $key);
                            if($newKeyPost == $props['ID']){
                                $arOldProps[$newKey] = $post;
                            }
                        }
                    }
                }
            }

            foreach ($this->arResult["CUSTOM_PROPS"] as $keyProp =>$prop){
                foreach ($arOldProps as $keyOldProp => $propOld){
                    if($keyOldProp == $keyProp){
                        $this->arResult['CUSTOM_PROPS'][$keyProp]['VALUE'] = $propOld;
                    }
                }
            }
        }
    }

    function getPhone(){ // Добавляем номер телефона в свойство заказа если не задан
        global  $USER;
        $srtCount = iconv_strlen($this->arResult['CUSTOM_PROPS']['PHONE']['VALUE']);

        if($USER->IsAuthorized()
           && empty($this->arResult['CUSTOM_PROPS']['PHONE']['VALUE'])
        ) {
            $rsUser = CUser::GetByID($USER->GetId());
            $arUser = $rsUser->Fetch();
            $this->arResult['CUSTOM_PROPS']['PHONE']['VALUE'] = $arUser["PERSONAL_PHONE"];
        }elseif ($srtCount == 11){
            $this->arResult['CUSTOM_PROPS']['PHONE']['VALUE'] = substr($this->arResult['CUSTOM_PROPS']['PHONE']['VALUE'],0);
        }
    }
    //

    // Доставка
    function showDelivery(){ // убираем доставки ненужные пользователю и сортируем их

        $arNewDelivery = array();
        foreach ($this->arParams['SHOW_DELIVERY'] as $val){
            if(!empty($this->arResult['DELIVERY'][$val])) $arNewDelivery[$val] = $this->arResult['DELIVERY'][$val];
        }

        $arSortDelivery = array();
        foreach ($arNewDelivery as $delivery){
           if(empty($arSortDelivery[$delivery['SORT']])){
               $arSortDelivery[$delivery['SORT']] = $delivery;
           }else{
               $arSortDelivery[] = $delivery;
           }
        }

        ksort($arSortDelivery);

        $this->arResult['DELIVERY'] = $arSortDelivery;
    }

    function getDeliveryServices(){ // Дополнительные услуги доставки

        if(!empty($this->city['CONDITIONS'])){
            $services = (array)json_decode($this->city['CONDITIONS']);
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
    
            foreach ($this->arResult['DELIVERY'] as $keyDelivery => $valDelivery) {
                if(!empty($valDelivery['EXTRA_SERVICES'])){
                    foreach ($valDelivery['EXTRA_SERVICES'] as $keyServices => $valServises){
                        
                        $kgtWeight = false;
                        $packsData = json_decode($valDelivery['CALCULATE_DESCRIPTION'])->pack;
                        if (!empty($packsData) && !$kgtWeight) {
                            foreach ($packsData as $packKey => $packValue) {
                                $kgtWeight = array_map(function ($packItem) {
                                    return $packItem->WEIGHT >= 30 ? true : false;
                                }, $packValue->PACK);
                            }
                            $kgtWeight = array_shift($kgtWeight);
                        }
                        
                        if ($valServises->getCode() == "D_UP_KGT" && (!$kgtWeight || $services['D_UP_LIFT'] == 'N')) {
                            continue;
                        } else if ($valServises->getCode() == "D_UP_LIFT" && ($kgtWeight || $services['D_UP_LIFT'] == 'N')) {
                            continue;
                        }
                        
                        $arExtraServices = array(
                            'ID'=>$keyServices,
                            'CODE'=>$valServises->getCode(),
                            'NAME'=>$valServises->getName(),
                            'DESCRIPTION'=>$valServises->getDescription(),
                            'PARAMS'=>$valServises->getParams(),
                        );
                        if($_POST['DELIVERY_EXTRA_SERVICES'][$valDelivery['ID']][$keyServices] == 'Y'){
                            $arExtraServices['CHECKED'] = 'Y';
                            $this->arResult['DELIVERY'][$keyDelivery]['SERVICE_NAME_OK'] = $this->arResult['DELIVERY'][$keyDelivery]['SERVICE_NAME_OK'].' '.$arExtraServices['NAME'].',';
                            $this->arResult['DELIVERY'][$keyDelivery]['SERVICES_OK'] = true;
                        }
                        $this->arResult['DELIVERY'][$keyDelivery]['EXTRA_SERVICES_ARR'][$keyServices] = $arExtraServices;
                        $code = $valServises->getCode();
                        if($code !='NO_TERMINAL') {
                            if ($services[$code] == 'N') {
                                unset($this->arResult['DELIVERY'][$keyDelivery]['EXTRA_SERVICES_ARR'][$keyServices]);
                            }
                        }else{
                            $arExtraServices['ID_DELIVERY'] = $valDelivery['ID'];
                            foreach ($arExtraServices['PARAMS']['PRICES'] as $keyParams => $valueParams){
                                $arExtraServices['PARAMS']['PRICES'][$keyParams]['ID'] = $keyParams;
                            }
                            $this->arResult['SERVICE_TERMINAL'] = $arExtraServices; //
                        }
                    }

                    $this->arResult['DELIVERY'][$keyDelivery]['SERVICE_NAME_OK'] =substr($this->arResult['DELIVERY'][$keyDelivery]['SERVICE_NAME_OK'],0,-1);
                }
            }
        }else{
            foreach ($this->arResult['DELIVERY'] as $keyDelivery => $valDelivery) {
                if(!empty($valDelivery['EXTRA_SERVICES'])){
                    foreach ($valDelivery['EXTRA_SERVICES'] as $keyServices => $valServises) {
                        
                        $kgtWeight = false;
                        $packsData = json_decode($valDelivery['CALCULATE_DESCRIPTION'])->pack;
                        if (!empty($packsData) && !$kgtWeight) {
                            foreach ($packsData as $packKey => $packValue) {
                                $kgtWeight = array_map(function ($packItem) {
                                    return $packItem->WEIGHT >= 30 ? true : false;
                                }, $packValue->PACK);
                            }
                            
                            $kgtWeight = array_shift($kgtWeight);
                        }
                        
                        if ($valServises->getCode() == "D_UP_KGT" && !$kgtWeight) {
                            continue;
                        } else if ($valServises->getCode() == "D_UP_LIFT" && $kgtWeight) {
                            continue;
                        }
                        
                        if ($valServises->getCode() == 'D_UP_LIFT') {
                            $code = $valServises->getCode();
                            $arExtraServices = array(
                                'ID' => $keyServices,
                                'CODE' => $valServises->getCode(),
                                'NAME' => $valServises->getName(),
                                'DESCRIPTION' => $valServises->getDescription(),
                                'PARAMS' => $valServises->getParams(),
                            );
                            if ($_POST['DELIVERY_EXTRA_SERVICES'][$valDelivery['ID']][$keyServices] == 'Y') {
                                $arExtraServices['CHECKED'] = 'Y';
                                $this->arResult['DELIVERY'][$keyDelivery]['SERVICE_NAME_OK'] = $this->arResult['DELIVERY'][$keyDelivery]['SERVICE_NAME_OK'] . ' ' . $arExtraServices['NAME'];
                                $this->arResult['DELIVERY'][$keyDelivery]['SERVICES_OK'] = true;
                            }
                            $this->arResult['DELIVERY'][$keyDelivery]['EXTRA_SERVICES_ARR'][$keyServices] = $arExtraServices;
                            if ($code == 'D_WEEKEND_1' || $code == 'D_WEEKEND_2' || $code == 'D_UP_LIFT' || $code == 'D_EVENING' || $code == 'D_WEEKEND_3') {
                                unset($this->arResult['DELIVERY'][$keyDelivery]['EXTRA_SERVICES'][$keyServices]);
                            }
                        }
                    }
                }
            }
        }
    }
    
    function setDeliveryCalculateStatus() { // Проверяем доступен ли сейчас расчет доставок
        foreach ($this->arResult['DELIVERY'] as $key => $value){
            $dscrtp = json_decode($value['CALCULATE_DESCRIPTION']);
            $this->arResult['DELIVERY'][$key]['DPD_STATUS'] = $dscrtp->DPD_STATUS;
        }
        
        $this->arResult['CUSTOM_PROPS']['ADDRESS_COMMENT']['VALUE'] = !$dscrtp->DPD_STATUS ? "Расчет доставки недоступен!" : "";
    }

    function getErrorDelivery() { // Получаем есть ли ошибка доставки
        foreach ($this->arResult['DELIVERY'] as $key => $value){
            $dscrtp = json_decode($value['CALCULATE_DESCRIPTION']);
            $this->arResult['DELIVERY'][$key]['ERROR'] = $dscrtp->error;
            if($value['CHECKED'] == 'Y' && $dscrtp->error) {
                $this->arResult['ERROR_DELIVERY'] = 'Y';
                $this->arResult['CUSTOM_PROPS']['ADDRESS_COMMENT']['VALUE'] = "Ошибка стоимости доставки!";
            }
        }
    }
    
    function setTypeDelivery(){ // отмечаем тип доставки
        foreach ($this->arResult['DELIVERY'] as $key => $value){
            $this->arResult['DELIVERY'][$key]['NAME'] = explode("|", $this->arResult['DELIVERY'][$key]['NAME'])[0];
            if(array_search($value['ID'],$this->arParams['TYPE_DELIVERY_POINT'])!==false){
                $this->arResult['DELIVERY'][$key]['TYPE'] = 'POINT';
            }elseif (array_search($value['ID'],$this->arParams['TYPE_DELIVERY_COURIER'])!==false){
                $this->arResult['DELIVERY'][$key]['TYPE'] = 'COURIER';
            }elseif (array_search($value['ID'],$this->arParams['TYPE_DELIVERY_STOCK'])!==false){
                $this->arResult['DELIVERY'][$key]['TYPE'] = 'STOCK';
            }
        }
    }

    function getCourierDelivery(){ //получаем курьерскую доставку
        foreach ($this->arResult['DELIVERY'] as $key => $value){
            if($value['TYPE'] == 'COURIER') $this->arResult['DELIVERY_COURIER'] = $this->arResult['DELIVERY'][$key];
        }
    }

    function modifierDeliveryPoint(){ // Снимаем отметку о выборе доставки, если не выбран терминал и записываем инфу по выбранному терминалу и проставляем их количество
        foreach ($this->arResult['DELIVERY'] as $key => $value) {
            if ($value['TYPE'] == 'POINT') {
                $this->arResult['DELIVERY'][$key]['DESCRIPTION'] = str_replace('#COUNT#',count($this->arResult['TERMINAL']),$this->arResult['DELIVERY'][$key]['DESCRIPTION']);
                if ($value['CHECKED'] == 'Y' && empty($this->arResult['CUSTOM_PROPS']['DPD_TERMINAL_CODE']['VALUE'])) {
                    unset($this->arResult['DELIVERY'][$key]['CHECKED']);
                    continue;
                }else{
                    if(!empty($this->arResult['TERMINAL'])){
                        foreach ($this->arResult['TERMINAL'] as $terminal){
                            if($this->arResult['CUSTOM_PROPS']['DPD_TERMINAL_CODE']['VALUE'] == $terminal['terminalCode']){
                                $this->arResult['DELIVERY'][$key]['TERMINAL_INFO'] = array(
                                    'ADDRESS' => $terminal['address']['cityName'].', '.$terminal['address']['streetAbbr'].' '.$terminal['address']['street'].', '.$terminal['address']['houseNo'],
                                    'IS_TERMINAL' => $terminal['is_terminal'],
                                );
                                if(empty($terminal['schedule'][0]['timetable'][0])){
                                    $this->arResult['DELIVERY'][$key]['TERMINAL_INFO']['DATA'][0] = $terminal['schedule'][0]['timetable'];
                                }else{
                                    $this->arResult['DELIVERY'][$key]['TERMINAL_INFO']['DATA'] = $terminal['schedule'][0]['timetable'];
                                }
                                continue;
                            }
                        }
                    }
                    continue;
                }
            }
        }
    }

    function getDpdRateAndPac(){ // получаем код услуги ДПД и упаковки товаров
        $checked = false;

        foreach ($this->arResult['DELIVERY'] as $delivery){
            if($delivery['CHECKED'] == 'Y' && !empty($delivery['CALCULATE_DESCRIPTION'])){
                $arCalculate = json_decode($delivery['CALCULATE_DESCRIPTION']);
                $checked = true;
                continue;
            }
        }

        if(!$checked){
            foreach ($this->arResult['DELIVERY'] as $delivery){
                if($delivery['TYPE'] == 'POINT' && !empty($delivery['CALCULATE_DESCRIPTION'])){
                    $arCalculate = json_decode($delivery['CALCULATE_DESCRIPTION']);
                    continue;
                }
            }
        }

        if(!empty($arCalculate)) {

            $this->arResult['CUSTOM_PROPS']['DPD_CODE']['VALUE'] = $arCalculate->serviceName;
            $quantityPack = 0;
            $weight = 0;
            $weightMax = 0;
            $arPack = [];
            foreach ((array)$arCalculate->pack as $packPoint) {
                $notPackQuantity = $packPoint->QUANTITY;
                if(!empty($packPoint->PACK)) {
                    while ($notPackQuantity > 0) {
                        foreach ($packPoint->PACK as $pack) {
                            if ($notPackQuantity >= $pack->QUANTITY) {
                                $notPackQuantity -= $pack->QUANTITY;
                                $quantityPack++;
                                $weight = $weight + $pack->WEIGHT;
                                $dimensions = $pack->WIDTH . 'x' . $pack->HEIGHT . 'x' . $pack->LENGTH;
                                if (empty($arPack[$dimensions])) {
                                    $arPack[$dimensions] = ['WEIGHT' => $pack->WIDTH * 100, 'HEIGHT' => $pack->HEIGHT * 100, 'LENGTH' => $pack->LENGTH * 100, 'QUANTITY' => 1, 'NAME' => self::endOfBox(1)];
                                } else {
                                    $arPack[$dimensions]['QUANTITY'] = $arPack[$dimensions]['QUANTITY'] + 1;
                                    $arPack[$dimensions]['NAME'] = self::endOfBox($arPack[$dimensions]['QUANTITY']);
                                }
                                if ($weightMax < $pack->WEIGHT) $weightMax = $pack->WEIGHT;

                                break;
                            }
                        }
                    }
                }
           }

            $this->arResult['PACK'] = array(
                'QUANTITY'=>$quantityPack,
                'WEIGHT'=>$weight,
                'WEIGHT_MAX'=>$weightMax,
                'ARR_PACK'=>$arPack,
                'NAME'=>self::endOfBox($quantityPack),
            );
        }
    }

    function choosePickupDefolt(){ // При первом заказе выбирает самовывоз
        if($_POST['is_ajax_post'] != 'Y' || ($_POST['PERSON_TYPE'] != $_POST['PERSON_TYPE_OLD'] && !empty($_POST['PERSON_TYPE_OLD']))){
            foreach ($this->arResult['DELIVERY'] as $key => $value){
                if($value['TYPE'] == 'POINT'){
                    $this->arResult['DELIVERY'][$key]['CHECKED'] = 'Y';
                    $checkedOk = true;
                    if(empty($this->arResult['CUSTOM_PROPS']['DPD_TERMINAL_CODE']['VALUE'])) {
                        $terminal = reset($this->arResult['TERMINAL']);
                    }else{
                        $terminal = $this->arResult['TERMINAL'][$this->arResult['CUSTOM_PROPS']['DPD_TERMINAL_CODE']['VALUE']];
                    }
                    $this->arResult['CUSTOM_PROPS']['DPD_TERMINAL_CODE']['VALUE'] = $terminal['terminalCode'];
                    $this->arResult['DELIVERY'][$key]['TERMINAL_INFO'] = array(
                        'ADDRESS' => $terminal['address']['cityName'] . ', ' . $terminal['address']['streetAbbr'] . ' ' . $terminal['address']['street'] . ', ' . $terminal['address']['houseNo'],
                        'IS_TERMINAL' => $terminal['is_terminal'],
                    );
                    $this->arResult['RELOAD'] = 'N';
                    if(empty($terminal['schedule'][0]['timetable'][0])){
                        $this->arResult['DELIVERY'][$key]['TERMINAL_INFO']['DATA'][0] = $terminal['schedule'][0]['timetable'];
                    }else{
                        $this->arResult['DELIVERY'][$key]['TERMINAL_INFO']['DATA'] = $terminal['schedule'][0]['timetable'];
                    }
                }else{
                    $this->arResult['DELIVERY'][$key]['CHECKED'] = false;
                }
            }

            if(!$checkedOk){
                foreach ($this->arResult['DELIVERY'] as $key => $value){
                    if($value['TYPE'] == 'COURIER'){
                        $this->arResult['DELIVERY'][$key]['CHECKED'] = 'Y';
                        if(empty($this->arResult['CUSTOM_PROPS']['ADDRESS']['VALUE'])){
                            $this->arResult['NO_ADDRESSES'] = true;
                        }
                    }
                }
            }
        }
    }

    function noAddress(){ // Если выбрана доставка курьером, но не заполнен адрес
        foreach ($this->arResult['DELIVERY'] as $key => $value){
            if($value['TYPE'] == 'COURIER' && $value['CHECKED'] == 'Y' && empty($this->arResult['CUSTOM_PROPS']['ADDRESS']['VALUE'])){
                $this->arResult['NO_ADDRESSES'] = true;
            }
        }
    }

    function chooseDelivery(){ // Выбираем тот же тип доставки, что и раньше

        if($_POST['is_ajax_post'] == 'Y') {

            $deliveryOrderId=$_POST['DELIVERY_ID'];
            $noChecked = true;

            foreach ($this->arResult['DELIVERY'] as $delivery){
                 if($delivery['ID'] == $_POST['DELIVERY_ID']){
                     $noChecked = false;
                 }
            }

            if($noChecked){
                if (array_search($deliveryOrderId, $this->arParams['TYPE_DELIVERY_POINT']) !== false) {
                    $type = 'POINT';
                } elseif (array_search($deliveryOrderId, $this->arParams['TYPE_DELIVERY_COURIER']) !== false) {
                    $type = 'COURIER';
                } elseif (array_search($deliveryOrderId, $this->arParams['TYPE_DELIVERY_STOCK']) !== false) {
                    $type = 'STOCK';
                }
            }

            if($noChecked) {
                foreach ($this->arResult['DELIVERY'] as $key => $value) {
                    if ($type == $value['TYPE']) {
                        $this->arResult['DELIVERY'][$key]['CHECKED'] = 'Y';
                        if($this->arResult['DELIVERY'][$key]['TYPE'] == 'POINT'){
                            if($this->arResult['TERMINAL'][$this->arResult['CUSTOM_PROPS']['DPD_TERMINAL_CODE']['VALUE']]['is_terminal'] != 'Y'){
                                foreach ($this->arResult['SERVICE_TERMINAL']['PARAMS']['PRICES'] as $extra){
                                    if($_SESSION['DPD_CITY'] == $extra['TITLE']){
                                        $this->arResult['DELIVERY'][$key]['PRICE'] = $this->arResult['DELIVERY'][$key]['PRICE']+$extra['PRICE'];
                                        $this->arResult['ORDER_DATA']['PRICE_DELIVERY'] =  $this->arResult['ORDER_DATA']['PRICE_DELIVERY']+$extra['PRICE'];
                                    }
                                }
                                $this->arResult['SERVICE_TERMINAL']['CHECKED'] = 'Y';
                            };
                        }
                    } else {
                        $this->arResult['DELIVERY'][$key]['CHECKED'] = false;
                    }
                }
            }
        }
    }

    function  checkedTerminal(){ // Выбираем первый терминал, если не задан
        foreach ($this->arResult['DELIVERY'] as $key => $delivery){
            if($delivery['CHECKED'] == 'Y' && $delivery['TYPE'] == 'POINT' && empty($this->arResult['TERMINAL'][$this->arResult['CUSTOM_PROPS']['DPD_TERMINAL_CODE']['VALUE']])){
                $terminal = reset($this->arResult['TERMINAL']);
                $this->arResult['CUSTOM_PROPS']['DPD_TERMINAL_CODE']['VALUE'] = $terminal['terminalCode'];
                $this->arResult['DELIVERY'][$key]['TERMINAL_INFO'] = array(
                    'ADDRESS' => $terminal['address']['cityName'] . ', ' . $terminal['address']['streetAbbr'] . ' ' . $terminal['address']['street'] . ', ' . $terminal['address']['houseNo'],
                    'IS_TERMINAL' => $terminal['is_terminal'],
                );
                $this->arResult['RELOAD'] = 'N';
                if(empty($terminal['schedule'][0]['timetable'][0])){
                    $this->arResult['DELIVERY'][$key]['TERMINAL_INFO']['DATA'][0] = $terminal['schedule'][0]['timetable'];
                }else{
                    $this->arResult['DELIVERY'][$key]['TERMINAL_INFO']['DATA'] = $terminal['schedule'][0]['timetable'];
                }
            }
        }
    }

    function getTimeDeliveryCourier(){ // получаем время курьерской доставки
        foreach ($this->arResult['DELIVERY'] as $key => $delivery){
            if($delivery['TYPE'] == 'COURIER'){
                if($delivery['CHECKED'] == 'Y'){
                    foreach ($delivery['EXTRA_SERVICES_ARR'] as $extra){
                        if($extra['CHECKED'] == 'Y' && $extra['CODE'] == 'D_EVENING'){
                            $this->arResult['DELIVERY'][$key]['TIME_WORK'] = '18:00 - 22:00';
                        }
                        if($extra['CHECKED'] == 'Y' && $extra['CODE'] == 'D_WEEKEND_1'){
                            $this->arResult['DELIVERY'][$key]['DEY_WORK'] = 'CБ';
                        }
                        if($extra['CHECKED'] == 'Y' && $extra['CODE'] == 'D_WEEKEND_2'){
                            $this->arResult['DELIVERY'][$key]['DEY_WORK'] = 'ВС';
                        }
                        if($extra['CHECKED'] == 'Y' && $extra['CODE'] == 'D_WEEKEND_3'){
                            $this->arResult['DELIVERY'][$key]['DEY_WORK'] = 'СБ, ВС';
                        }
                    }
                }

                if(empty($this->arResult['DELIVERY'][$key]['TIME_WORK'])){
                    $this->arResult['DELIVERY'][$key]['TIME_WORK'] = '09:00 - 18:00';
                }

                if(empty($this->arResult['DELIVERY'][$key]['DEY_WORK'])){
                    $this->arResult['DELIVERY'][$key]['DEY_WORK'] = 'Пн, Вт, Ср, Чт, Пт';
                }
            }
        }
    }

    function setPersonTypeOld(){ // устанавливаем значение "старого типа пользователя" если оно не задана
        if(empty($this->arResult['USER_VALS']['PERSON_TYPE_OLD'])) $this->arResult['USER_VALS']['PERSON_TYPE_OLD'] = $this->arResult['USER_VALS']['PERSON_TYPE_ID'];
    }

    //

    // Платежные системы
    function getDiscountPaySystem(){ // Получаем скидку за использование платежной системы
        foreach ($this->arResult['PAY_SYSTEM'] as $key => $paySystem){
            foreach ($this->arParams as $keyParam => $param){
                if($keyParam == 'DISCOUNT_PERCENT_'.$paySystem['ID']){
                    if(is_numeric($param)) {
                        if ($paySystem['CODE'] == $this->arParams['PAY_SYSTEM_INSTALLMENTS']) {
                            $this->arResult['PAY_SYSTEM'][$key]['DISCOUNT'] = round($this->arResult['PRICE_WITHOUT_DISCOUNT_VALUE'] * $param / 100) . '.− в месяц';
                        } else {
                            $this->arResult['PAY_SYSTEM'][$key]['DISCOUNT'] = 'Скидка ' . round($this->arResult['PRICE_WITHOUT_DISCOUNT_VALUE'] * $param / 100) . '.−';
                        }
                    }elseif(!empty($param) && $this->arResult['USER_VALS']['PERSON_TYPE_ID'] == 1){
                        $this->arResult['PAY_SYSTEM'][$key]['DISCOUNT'] = $param;
                    }
                }
            }
            if($this->arParams['PAYS_ANOTHER_CODE'] == $paySystem['CODE']){
                $this->arResult['PAY_SYSTEM'][$key]['ANOTHER'] = 'Y';
                $this->arJsParams['ANTHER_PAY_SYSTEM_ID'] = $paySystem['ID'];
            }


        }
    }

    function sortPaySystem(){ // сортируем платежные системы и узнаем выбранную
        $arSortPaySystem = array();
        $count = 0;
        foreach ($this->arResult['PAY_SYSTEM'] as $key=>$paySystem) {
            if ($paySystem['CHECKED'] == 'Y') $count = $key + 1;
            if (empty($arSortDelivery[$paySystem['SORT']])) {
                $arSortPaySystem[$paySystem['SORT']] = $paySystem;
            } else {
                $arSortPaySystem[] = $paySystem;
            }
        }

        if($count > 3) $this->arResult['PAY_SYSTEM_OPEN'] = true;

        ksort($arSortPaySystem);

        $arSortPaySystem = array_values($arSortPaySystem);

        $this->arResult['PAY_SYSTEM'] = $arSortPaySystem;
    }

    function getPaySystemAction(){ // если у дпд нет наличного расчета, отключаем его и выбираем ту же платежную систему при смене типа плательщика
        if(!empty($_POST['PERSON_TYPE']) && $_POST['PERSON_TYPE'] != $_POST['PERSON_TYPE_OLD'] && is_array($this->arResult['PAY_SYSTEM'])) {
            $checked = false;

            foreach ($this->arResult['PAY_SYSTEM'] as $key => $pay){
                    if($pay['ID'] == $_POST['PAY_SYSTEM_ID']){
                    $this->arResult['PAY_SYSTEM'][$key]['CHECKED'] = 'Y';
                    $checked = true;
                }else{
                    unset($this->arResult['PAY_SYSTEM'][$key]['CHECKED']);
                }
            }

            if(!$checked){
                $this->arResult['PAY_SYSTEM'][0]['CHECKED'] = "Y";
            }
        }

        if($this->city['CASH_PAYMENT_OFF'] == 'Y'){
            foreach ($this->arResult['PAY_SYSTEM'] as $key => $value){
                foreach ($this->arParams['NAL'] as $id){
                    if($value['ID'] == $id){
                        unset($this->arResult['PAY_SYSTEM'][$key]);
                    }
                }
            }
        }
    }

    function getPaySystemType(){ // получаем тип оплаты
        if($this->arResult['ORDER_DATA']['PAY_SYSTEM_ID'] == $this->arParams['NAL']){
            $this->arResult['THIS_PAYMENT'] = 'NAL';
        }else{
             $this->arResult['THIS_PAYMENT'] = 'BEZNAL';
        }
    }

    function delNal(){ // удаляем наличный расчет если доставка больше 4000 (дубль ограничения платежной системы)
        if($this->arResult['ORDER_DATA']['PRICE_DELIVERY'] > 4000){
            foreach ($this->arResult['PAY_SYSTEM'] as $key => $value){
                if(array_search($value['ID'],$this->arParams['NAL']) !== false){
                    unset($this->arResult['PAY_SYSTEM'][$key]);
                }
            }
        }
    }

    function delRetailCrmPayment(){ // удаляем платежную систему от интеграции с Retail (та что имеет привязку к отдельной я.кассе)
        foreach ($this->arResult['PAY_SYSTEM'] as $key => $value){
            if(array_search($value['ID'],$this->arParams['RETAIL_PAYMENT']) !== false){
                unset($this->arResult['PAY_SYSTEM'][$key]);
            }
        }
    }

    function delPayOnlyDelivery(){ // удаляем платежную сиситему 'оплатить доставку' если менее 4000
        if($this->arResult['ORDER_DATA']['PRICE_DELIVERY'] <= 4000 && !empty($this->arParams['PAYS_ONLY_DELIVERY_CODE'])) {
            foreach ($this->arResult['PAY_SYSTEM'] as $key => $value) {
                if ($value['CODE'] == $this->arParams['PAYS_ONLY_DELIVERY_CODE']) {
                    unset($this->arResult['PAY_SYSTEM'][$key]);
                }
            }
        }
    }
    //
    // Товары в корзине
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

    function getListItems(){ // Проходимся по всем товарам в корзине
        foreach ($this->arResult["GRID"]["ROWS"] as $key => $arData){
            if ($arData['data']['PRODUCT_ID']== $this->arParams['ID_WARRANTY']) {
                $this->arResult['SERVICE_OK'] = 'Y';
                $this->servicePrice = $arData['data']['BASE_PRICE'];
                unset($this->arResult["GRID"]["ROWS"][$key]);
            }else{

                /** Блок сбора данных для метода получения терминалов **/

                $sku = CCatalogSku::GetProductInfo($arData['data']['PRODUCT_ID']); // узнаем это торговое предложение или нет
                if (is_array($sku)) {
                    if(empty($this->arBasketTerminal[$sku['ID']])) {
                        $this->arBasketTerminal[$sku['ID']] = array( // добавляем товар для метода терминала
                            'ID' => $arData['id'],
                            'PRODUCT_ID' => $arData['data']['PRODUCT_ID'],
                            'QUANTITY' => $arData['data']['QUANTITY'],
                            'PARENT' => $sku['ID'],
                            'PACK' => GetPackData($arData['data']['PRODUCT_XML_ID']), // упаковки товара
                        );

                    }else{
                        $this->arBasketTerminal[$sku['ID']]['QUANTITY'] = $this->arBasketTerminal[$sku['ID']]['QUANTITY']+$arData['data']['QUANTITY'];
                    }
                    $arIdBasket[$sku['ID']] = $sku['ID'];

                    $arElement[$sku['ID']] = array('ORDER_ID'=>$arData['id'],'ELEMENT_ID'=>$sku['ID']);

                } else {
                    if(empty($this->arBasketTerminal[$arData['data']['PRODUCT_ID']])) {
                        $this->arBasketTerminal[$arData['data']['PRODUCT_ID']] = array( // добавляем товар для метода терминала
                            'ID' => $arData['id'],
                            'PRODUCT_ID' => $arData['data']['PRODUCT_ID'],
                            'QUANTITY' => $arData['data']['QUANTITY'],
                            'PACK' => GetPackData($arData['data']['PRODUCT_XML_ID']), // упаковки товара
                        );
                    }else{
                        $this->arBasketTerminal[$arData['data']['PRODUCT_ID']] = $this->arBasketTerminal[$arData['data']['PRODUCT_ID']]+$arData['data']['QUANTITY'];
                    }

                    $arIdBasket[$arData['data']['PRODUCT_ID']] = $arData['data']['PRODUCT_ID'];

                    $arElement[$arData['data']['PRODUCT_ID']] = array('ORDER_ID'=>$arData['id'],'ELEMENT_ID'=>$arData['data']['PRODUCT_ID']);
                }

                $resultElement = \Bitrix\Iblock\ElementTable::getList(array( // Получаем элементы
                    'select' => array('ID', 'IBLOCK_SECTION_ID', 'NAME'),
                    'filter' => array('ID' => $arIdBasket),
                ));

                while ($row = $resultElement->fetch())
                {
                    $arElement[$row['ID']] = array_merge($arElement[$row['ID']],$row);
                    $arSectionId[] = $row['IBLOCK_SECTION_ID'];
                    $this->arBasketTerminal[$row['ID']]['IBLOCK_SECTION_ID'] = $row['IBLOCK_SECTION_ID'];
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

                    foreach ($this->arBasketTerminal as $keySectiom => $valueSection){ // Добавляем данные о разделах в массив для терминала
                        if($valueSection['IBLOCK_SECTION_ID'] == $row['ID']){
                            $this->arBasketTerminal[$keySectiom]['SECTION'] = $row['NAME'];
                            $this->arBasketTerminal[$keySectiom]['SECTION_URL'] = $row['SECTION_PAGE_URL'];
                            $this->arBasketTerminal[$keySectiom]['CODE'] = $row['CODE'];
                        }
                    }
                }

                ////////////////////////////////////////////////////////////////////////////////////////////////////////

                if(!empty($arData['data']['PROPERTY_FOTOGRAFIYA_1_VALUE'])){ // получаем изображение товаров
                    $this->arResult["GRID"]["ROWS"][$key]['data']['PHOTO_PRODUCT'] = self::getPhoto($arData['data']['PROPERTY_FOTOGRAFIYA_1_VALUE']);
                }

                if(!$arData['data']['PROPERTY_DOPOLNITELNAYA_GARANTIYA_OTSUTSTVUET_VALUE']) {
                    $this->countWarranty = $this->countProduct + $arData['data']['QUANTITY']; // Количество товаров c доп гарантией
                }

                $basePrise = CPrice::GetBasePrice($arData['data']['PRODUCT_ID']); // Получаем базовую цену
                $this->totalDiscountPrice = $this->totalDiscountPrice + (($basePrise['PRICE'] - $arData['data']['PRICE'])*$arData['data']['QUANTITY']); // Узнаем скидку с диапазонами
                $this->totalProductPrice = $this->totalProductPrice + ($basePrise['PRICE']*$arData['data']['QUANTITY']); // цена товаров без скидки
                $this->countProduct = $this->countProduct + $arData['data']['QUANTITY']; // Количество товаров
            }
        }
    }
    //

    //Терминалы
    function  GetTerminals(){ // получаем терминалы
        \Bitrix\Main\Loader::includeModule('dsklad.site');
        if(!empty($this->arBasketTerminal)) $arData = \Dsklad\Order::getDPDTerminals($this->arBasketTerminal);

        $arResult['mapParams'] = $arData['mapParams'];
        $arResult['TERMINAL'] = array();

        foreach ($arData['TERMINAL'] as $arItem) {
            if(array_search($this->arResult['ORDER_DATA']['PAY_SYSTEM_ID'],$this->arParams['NAL']) !==false && $arItem['npp_sum'] < $this->arResult['ORDER_TOTAL_PRICE']) continue; // убираем терминалы где нет наложенного платежа
            if (!empty($arItem['schedule']['operation'])) {
                $arItem['schedule'] = array($arItem['schedule']);
            }
            $arItem['address']['terminalAddress'] = 'г. ' . $arItem['address']['cityName'] . ', ';
            $arItem['address']['terminalAddress'] .= $arItem['address']['streetAbbr'] . ', ';
            $arItem['address']['terminalAddress'] .= $arItem['address']['street'] . ', ';
            $arItem['address']['terminalAddress'] .= (!empty($arItem['address']['houseNo'])) ? $arItem['address']['houseNo'] : $arItem['address']['ownership'];

            $arResult['TERMINAL'][$arItem['terminalCode']] = $arItem;
        }

        $deliveryPrice = 0;

        foreach($this->arResult['DELIVERY'] as $var){
            if($var['TYPE'] == 'POINT'){
                $deliveryId = $var['ID'];
                foreach ($var['EXTRA_SERVICES_ARR'] as $service){
                    if($service['CODE'] == 'NO_TERMINAL'){
                       $this->arResult['AAA'] = $service;
                        foreach ($service['PARAMS']['PRICES'] as $priceServiceKey=>$priceService){
                            if($priceService['TITLE'] == $_SESSION['DPD_CITY']){
                                if($_REQUEST['DELIVERY_EXTRA_SERVICES'][$var['ID']][$service['ID']] != $priceServiceKey) {
                                    $dopPrice = $priceService['PRICE'];
                                }else{
                                    $dopPrice = 0;
                                }

                            }
                        }
                    }
                }
                $deliveryPrice = $var['PRICE'];
            }
        };

        foreach ($arResult['mapParams']['PLACEMARKS'] as $key => $plas){

            $openingHours ='';
            if(empty($plas['OPENING_HOURS'][1])) $plas['OPENING_HOURS'] = array($plas['OPENING_HOURS']);
            if(!empty($plas['OPENING_HOURS'])) {
                foreach ($plas['OPENING_HOURS'] as $time) {
                    $openingHours = $openingHours . '<span>' . $time['weekDays'] . ': ' . $time['workTime'] . '</span>';
                }
            }

            $newDeliveryPrice = $deliveryPrice;

            if($plas['IS_TERMINAL'] == 'N') $newDeliveryPrice = $newDeliveryPrice + $dopPrice;

            $arResult['mapParams']['PLACEMARKS'][$key]['TEXT'] =
                ' <div class="delivery-info-result delivery-info-result--modal"><span class="delivery-result-header">'.$plas['TITLE'].'</span><span class="delivery-result-address">'.$plas['ADDRESSES'].'</span><br/><span class="delivery-result-price">Стоимость доставки:<span class="ds-price"> '.$newDeliveryPrice.'</span></span>
                        <div class="delivery-result-add-info">'.
                            $openingHours
                        .'</div>
                        <span data-city="'.$_SESSION['DPD_CITY'].'"
                              data-is-terminal="'.$plas['IS_TERMINAL'].'"
                              data-terminal-id="'.$plas['TERMINAL'].'"
                              data-delivery-id="'.$deliveryId.'"
                              class="ds-btn ds-btn--default ds-btn--full js-delivery-here delivery-point-ok">
                              Заберу отсюда
                        </span>
                    </div>
                ';

            $arResult['mapParams']['PLACEMARKS'][$key]['SEARCH_TITLE'] = $plas['TERMINAL'].' '.$plas['TITLE'];
            $arResult['mapParams']['PLACEMARKS'][$key]['SEARCH_DESCRIPTION'] = $plas['TERMINAL'].' '.$plas['TITLE'].' '.$plas['ADDRESSES'];
        }

        if(empty($arResult['TERMINAL'])) $arResult['mapParams'] = false;

        $this->arResult = array_merge($this->arResult,$arResult);
    }

    function noMapActiveDelivery(){ // убираем карту если нет терминалов
        if(empty($this->arResult['TERMINAL'])){
            foreach ($this->arParams['TYPE_DELIVERY_POINT'] as $valDel){
                unset($this->arResult['DELIVERY'][$valDel]);
            }
        }
    }

    function subtractPlacemarks() { // определяем пункты самовывоза, которые необходимо скрыть
        $result = [];
        foreach ($this->arResult['mapParams']['PLACEMARKS'] as $p) {
            $result[] = $p['TERMINAL'];
        }
        foreach ($this->arResult['mapParamsLimits']['PLACEMARKS'] as $placemark) {
            foreach ($this->arResult['mapParams']['PLACEMARKS'] as $i => $placemark2) {
                if ($placemark['TERMINAL'] == $placemark2['TERMINAL'])
                    unset($result[$i]);
            }
        }
        $this->arResult['placemarksToHide'] = $result;
    }

    function elseCheckDeliveryPoint(){ // Если выбрана доставка до пункта выдачи то записываем адрес терминала
        if(array_search($this->arResult['USER_VALS']['DELIVERY_ID'],$this->arParams['TYPE_DELIVERY_POINT']) !== false){
            $this->arResult['DELIVERY_POINT'] = true;
            $this->arResult['CUSTOM_PROPS']['ADDRESS_TERMINAL']['VALUE'] = $this->arResult['TERMINAL'][$this->arResult['CUSTOM_PROPS']['DPD_TERMINAL_CODE']['VALUE']]['address']['terminalAddress'];
        }
    }

    //

    //Купон
    function setCoupon($coupon = '',$sessionId=''){ // обработчик купонов
        if(!empty($coupon) && bitrix_sessid() == $sessionId) {
            if($arCoupon = CCatalogDiscountCoupon::SetCoupon($coupon)) {
                return 'Y';
            }else{
               return 'N';
            }
        }else{
            return 'N';
        }
    }

    function getStateCoupon(){ // Получаем состояние купона
         if(!empty($this->arResult['JS_DATA']['COUPON_LIST'])){
            $this->arResult['COUPON']['APPLIED'] = true;
            $coupon = end($this->arResult['JS_DATA']['COUPON_LIST']);
            if($coupon['JS_STATUS'] != 'BAD'){
                $this->arResult['COUPON']['OK'] = true;
                $this->arResult['COUPON']['COUPON'] = $coupon['COUPON'];
            }else{
                $this->arResult['COUPON']['OK'] = false;
                $this->arResult['COUPON']['COUPON'] = $coupon['COUPON'];
            }
        }else{
             $this->arResult['COUPON']['APPLIED'] = false;
         }
    }
    //

    // Города и страны
    function getCityName(){ // Получаем название города и код страны
        if(!empty($_SESSION['DPD_CITY'])){
            $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/dpd_cities'))->fetch();
            $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
            $strEntityDataClass = $obEntity->getDataClass();

            $arFilter = array(
                'UF_CITYCODE' => $_SESSION['DPD_CITY']
            );

            $rsData = $strEntityDataClass::getList(array(
                'order' => array('UF_SORT' => 'ASC'),
                'select' => array('UF_CITYNAME', 'UF_CITYCODE', 'UF_CITYID', 'UF_COUNTRYCODE'),
                'filter' => $arFilter
            ));
            if ($arItem = $rsData->fetch()) {
                $this->arResult['DPD_CITY_NAME'] = $arItem['UF_CITYNAME'];
                $this->arResult['COUNTRY_CODE'] = $arItem['UF_COUNTRYCODE'];
            }
        }
    }

    // Правописание
    function endOfBox($quantity){ // окончание слова коробка
        $strWord = 'короб';
        if ($quantity > 20) {
            $intLastCount = substr($quantity, -1);
        } else {
            $intLastCount = $quantity;
        }

        if($intLastCount == 1){
            $strWord .= 'ка';
        } elseif (($intLastCount > 1) && ($intLastCount < 5)) {
            $strWord .= 'ки';
        } elseif ($intLastCount > 4) {
            $strWord .= 'ок';
        }

        return $strWord;
    }

    function modificationArResult($arResult,$arParams){
        global $USER;
        $this->arResult = $arResult;
        $this->arParams = $arParams;
        $this->arResult['POST'] = $_POST;
        // Изменение свойств
        $this->orderPropSort(); // сортируем свойства в соответствии с индексом
        $this->getPropUserAuth();  // получаем свойтво телефон
        $this->getPropsAction(); // подставляет значение свойств из предыдущего типа плательщика
        $this->getPhone(); // Добавляем номер телефона в свойство заказа если не задан
        $this->setPersonTypeOld(); // устанавливаем значение "старого типа пользователя" если оно не задана

        // Платежные системы
        $this->delPayOnlyDelivery(); // удаляем платежную сиситему 'оплатить доставку' если менее 4000
        $this->getDiscountPaySystem(); // Получаем скидку за использование платежной системы
        $this->sortPaySystem(); // сортируем платежные системы и узнаем выбранную
        $this->getPaySystemAction(); // если у дпд нет наличного расчета, отключаем его и выбираем ту же платежную систему при смене типа плательщика
        $this->getPaySystemType(); // получаем тип оплаты
        $this->delNal(); // удаляем наличный расчет если доставка больше 4000 (дубль ограничения платежной системы)
        $this->delRetailCrmPayment(); // удаляем платежную систему от интеграции с Retail (та что имеет привязку к отдельной я.кассе)

        // Товары в корзине
        $this->getListItems();

        // Доставки
        $this->showDelivery();  // убираем доставки ненужные пользователю
        $this->setTypeDelivery(); // отмечаем тип доставки
        $this->setDeliveryCalculateStatus(); // Проверяем доступен ли сейчас расчет доставок
        $this->getDeliveryServices(); // Дополнительные услуги доставки
        $this->getErrorDelivery(); // Получаем есть ли ошибка доставки

        // Терминалы
        $this->GetTerminals(); // получаем терминалы
        $this->noMapActiveDelivery(); // убираем карту если нет терминалов
        $this->subtractPlacemarks(); // определяем пункты самовывоза, которые необходимо скрыть

        //Доставки
        $this->modifierDeliveryPoint(); // Снимаем отметку о выборе доставки если не выбран терминал и записываем инфу по выбранному терминалу и проставляем их количество
        $this->getCourierDelivery(); // получаем курьерскую доставку
        $this->getDpdRateAndPac(); // получаем код услуги ДПД и упаковки товаров
        $this->choosePickupDefolt(); // При первом заказе выберает самовывоз
        $this->noAddress(); // Если выбрана доставка курьером, но не заполнен адрес
        $this->chooseDelivery(); // Выбираем тот же тип доставки, что и раньше
        $this->checkedTerminal(); // Выбираем первый терминал, если не задан
        $this->getTimeDeliveryCourier(); // получаем время курьерской доставки

        //Купон
        $this->getStateCoupon(); // Получаем состояние купона

        // Терминалы
        $this->elseCheckDeliveryPoint(); // Если выбрана доставка до пункта выдачи то записываем адрес терминала

        // Города и страны
        $this->getCityName(); // Получаем название города

        // Хэш авторизации
        $this->getAuthorizeHash();

        $this->arResult['JS_PARAMS'] = $this->arJsParams;
        $this->arResult['PARAMS'] = $this->arParams; //
        $this->arResult['AUTHORIZED'] = $USER->IsAuthorized(); //
        $this->arResult['TOTAL_DISCOUNT_PRICE'] = $this->totalDiscountPrice; //
        $this->arResult['COUNT_PRODUCT'] = $this->countProduct; //
        $this->arResult['SERVICE_PRICE'] = $this->servicePrice; //
        $this->arResult['WARRANTY_COUNT'] = $this->countWarranty; //
        $this->arResult['TOTAL_PRICE_NO_SERVICE'] = $this->totalProductPrice; //
        $this->arResult['DPD_CITY'] = $_SESSION['DPD_CITY']; //
        $this->arResult['SITE_TEMPLATE_PATH'] = SITE_TEMPLATE_PATH; //

        return $this->arResult;
    }
}