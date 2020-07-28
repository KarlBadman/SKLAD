
<?php
class ResultModifierCatalogElement {
    public $arResult = [];
    public $arParams = [];
    public $photoEntityDataClass;
    public $arPhotoId;
    public $arSelectProp = [];
    public $arOffersId = [];
    public $arSkuKey = [];
    public $arJs = [];

    function __construct() {
        $arHLBlockFoto = \Bitrix\Highloadblock\HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
        $obEntityFoto = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlockFoto);
        $this->photoEntityDataClass = $obEntityFoto->getDataClass();
    }

    //Торговые предложения

    function offersGetList(){ // Проходимся по всем торговым предложениям
        foreach ($this->arResult["OFFERS"] as $keyOffer => $offer){
            $this->arOffersId[] = $offer['ID']; // собираем id всех торговых предложений
            uasort($offer['PROPERTIES'], function ($a, $b) {
                return (int)$a['SORT'] - (int)$b['SORT'];
            });
            foreach ($offer['PROPERTIES'] as $prop) {
                $this->saveArrToPhoto($prop); // собираем масив id для фото
                $this->propertyType($prop,$offer['ID']); // получаем все результаты свойств участвующих в выборе
                if($prop['CODE'] == $this->arParams['BALANCE_ON_STOCK']) $this->getBalanceOnStock($prop,$offer['CATALOG_QUANTITY'],$keyOffer); // Получаем количество товаров
            }
            $this->getNormalCodePrice($keyOffer); // костыль меняющий русские коды цен на латиницу
            $this->getRangeOffersPrice($keyOffer); // Получаем цену и рекомендовнное количество
            $this->getDiscountQuantity($keyOffer); // получаем минимальное количество товаров для скидки
            $this->getVendorCode($keyOffer); // получаем артикул предложения
            $arOffers[$offer['ID']] = $offer;
        }
    }

    function getNormalCodePrice($keyOffer){ // костыль меняющий русские коды цен на латиницу
        if (is_numeric($this->arResult['OFFERS'][$keyOffer]['PRICES']['Базовая цена продажи']['VALUE']))
            $this->arResult['OFFERS'][$keyOffer]['PRICES_BASE'] = $this->arResult['OFFERS'][$keyOffer]['PRICES']['Базовая цена продажи'];
    }

    function newKeyOffers(){ // Меняем ключи для торговых предложений
        $arOffers = [];
        foreach ($this->arResult["OFFERS"] as $keyOffer => $offer){
            $arOffers[$offer['ID']] = $offer;
        }
        $this->arResult['OFFERS'] = $arOffers;
    }

    function getActiveOffer(){ // Получаем активное торговое предложение
        if(!empty($_REQUEST['offers'])){
            $this->arResult['OFFERS_ACTIVE'] = $this->arResult['OFFERS'][$_REQUEST['offers']];
        }else{
            foreach ($this->arResult['OFFERS'] as $offer) {
                if ($offer['CATALOG_QUANTITY'] > 0) {
                    $this->arResult['OFFERS_ACTIVE'] = $offer;
                    break;
                }
            }
        }

        if (empty($this->arResult['OFFERS_ACTIVE'])) $this->arResult['OFFERS_ACTIVE'] = current($this->arResult['OFFERS']);

        foreach ($this->arResult['OFFERS_ACTIVE']['IMAGES'] as $key=>$value){
            ksort($this->arResult['OFFERS_ACTIVE']['IMAGES'][$key]);
        }
    }

    function getDiscountQuantity($keyOffer){ // получаем минимальное количество товаров для скидки
        if(!empty($this->arResult['OFFERS'][$keyOffer]['ITEM_PRICES'][1])){
            $this->arResult['OFFERS'][$keyOffer]['DISCOUNT_QUANTITY'] = $this->arResult['OFFERS'][$keyOffer]['ITEM_PRICES'][1]['QUANTITY_FROM'];
        }
    }

    function getRangeOffersPrice($keyOffer){ // Получаем цену и рекомендовнное количество
        if($this->arParams['NO_RANGE_PRICE'] != 'Y' && count($this->arResult['OFFERS'][$keyOffer]['ITEM_PRICES'][1]) > 1){
            $data = ['PRICE'=>PHP_INT_MAX,'QUANTITY'=>PHP_INT_MAX];
            foreach ($this->arResult['OFFERS'][$keyOffer]['ITEM_PRICES'] as $range){
                if($data['PRICE'] > $range['BASE_PRICE']){
                    $data = ['PRICE'=>$range['BASE_PRICE'],'QUANTITY'=>$range['QUANTITY_FROM']];
                }
            }
            $this->arResult['OFFERS'][$keyOffer]['RECOMMEND_PRICE'] = $data['PRICE'];
            $this->arResult['OFFERS'][$keyOffer]['RECOMMEND_QUANTITY'] = $data['QUANTITY'];
        }else{
            $this->arResult['OFFERS'][$keyOffer]['RECOMMEND_PRICE'] = $this->arResult['OFFERS'][$keyOffer]['MIN_PRICE']['VALUE'];
            $this->arResult['OFFERS'][$keyOffer]['RECOMMEND_QUANTITY'] = 1;
        }
    }

    function getBalanceOnStock($prop = [], $quantity = 1, $keyOffer){ // Получаем количество товаров
        if ($quantity > 0) {
            if (empty($prop['VALUE_XML_ID'])) {
                $this->arResult['OFFERS'][$keyOffer]['QUANTITY_STRING'] = ['TEXT' => 'средне', 'COLOR_CLASS' => 'middle'];
            } else {
                if ($prop['VALUE_XML_ID'] == 'AVERAGE')  $this->arResult['OFFERS'][$keyOffer]['QUANTITY_STRING'] = ['TEXT' => $prop['VALUE'], 'COLOR_CLASS' => 'middle'];
                if ($prop['VALUE_XML_ID'] == 'LOT')      $this->arResult['OFFERS'][$keyOffer]['QUANTITY_STRING'] = ['TEXT' => $prop['VALUE'], 'COLOR_CLASS' => 'many'];
                if ($prop['VALUE_XML_ID'] == 'FEW')      $this->arResult['OFFERS'][$keyOffer]['QUANTITY_STRING'] = ['TEXT' => $prop['VALUE'], 'COLOR_CLASS' => 'few'];
            }
        } else {
            $this->arResult['OFFERS'][$keyOffer]['QUANTITY_STRING'] = ['TEXT' => 'Нет в наличии', 'COLOR_CLASS' => 'few'];
        }
    }

    function getPhoto(){ // Получаем изображение товара
        $arPhoto = [];
        $rsData = $this->photoEntityDataClass::getList(array(
            'select' => array('UF_FILE','UF_XML_ID'),
            'filter' => array('UF_XML_ID' => $this->arPhotoId)
        ));


        while($arItem = $rsData->Fetch()){
            $arImage = CFile::GetFileArray($arItem['UF_FILE']);
            $arPhoto[$arItem['UF_XML_ID']]['ICON'] = CFile::ResizeImageGet($arImage, array('width' => 128, 'height' => 128), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
            $arPhoto[$arItem['UF_XML_ID']]['PICTURE']= CFile::ResizeImageGet($arImage, array('width' => 550, 'height' => 550), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
            $arPhoto[$arItem['UF_XML_ID']]['BIG'] = CFile::ResizeImageGet($arImage, array('width' => 1100, 'height' => 1100), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
        }
        foreach ($this->arResult["OFFERS"] as $keyOffer => $offer) {
            foreach ($offer['PROPERTIES'] as $keyProp =>$prop) {
                if (strripos($prop['CODE'], 'FOTOGRAFIYA_') !== false && !empty($arPhoto[$prop['VALUE']])) {
                    $i = preg_replace("/[^0-9]/", '', $prop['CODE']);
                    $this->arResult["OFFERS"][$keyOffer]['IMAGES']['ICON'][$i] = $arPhoto[$prop['VALUE']]['ICON'];
                    $this->arResult["OFFERS"][$keyOffer]['IMAGES']['PICTURE'][$i] = $arPhoto[$prop['VALUE']]['PICTURE'];
                    $this->arResult["OFFERS"][$keyOffer]['IMAGES']['BIG'][$i] = $arPhoto[$prop['VALUE']]['PICTURE'];
                }
            }
        }

        foreach ($this->arResult["OFFERS"][$keyOffer]['IMAGES'] as $key=>$value){
           ksort($this->arResult["OFFERS"][$keyOffer]['IMAGES'][$key]);
        }
    }

    function saveArrToPhoto($prop){ // собираем масив id для фото
        if (strripos($prop['CODE'], 'FOTOGRAFIYA_') !== false && !empty($prop['VALUE'])) {
            $this->arPhotoId[$prop['VALUE']] = $prop['VALUE'];
        }
    }

    function propertyType($prop,$id){ // получаем все результаты свойств участвующих в выборе
        if(!empty($this->arParams['OFFERS_SELECT_PROPERTY'])){
            if(array_search($prop['CODE'], $this->arParams['OFFERS_SELECT_PROPERTY']) !== false && !empty($prop['VALUE'])){
                if(empty($this->arSelectProp[$prop['CODE']]['VALUES'][$prop['VALUE']])){
                    $this->arSelectProp[$prop['CODE']]['ID'] = $prop['ID'];
                    $this->arSelectProp[$prop['CODE']]['VALUES'][$prop['VALUE']] = ['NAME'=>$prop['NAME'],'VALUE'=>$prop['VALUE'],'VALUE_STRING'=>$prop['VALUE'],'CODE'=>$prop['CODE'],'OFFERS'=>[$id]];
                }else{
                    $this->arSelectProp[$prop['CODE']]['VALUES'][$prop['VALUE']]['OFFERS'][] = $id;
                }
            }
        }
    }

    function getVendorCode($keyOffer){ // получаем артикул предложения
        if(empty($this->arResult['OFFERS'][$keyOffer]['PROPERTIES']['CML2_ARTICLE']['VALUE'])){
            $this->arResult['OFFERS'][$keyOffer]['PROPERTIES']['CML2_ARTICLE']['VALUE'] = $this->arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'];
        }
    }

    function getActiveTypeOffers(){ // Соотносим свойства активного ТП с свойствами фильтра
        $i = 1;
        $this->arSkuKey[] = $this->arResult['OFFERS_ACTIVE']['ID'];
        foreach ($this->arSelectProp as $keyType =>$type){
            foreach ($type['VALUES'] as $keyProp => $prop) {
                if ($this->skuCheck($prop['OFFERS'])) {
                    if($this->arResult['OFFERS_ACTIVE']['PROPERTIES'][$keyType]['VALUE'] == $prop['VALUE']) {
                        $this->arSelectProp[$keyType]['VALUES'][$keyProp]['CHECKED'] = 'Y';
                        $this->arSelectProp[$keyType]['CHECKED_VALUE'] = $prop['VALUE_STRING'];
                    }
                    $this->arSelectProp[$keyType]['NAME'] = $prop['NAME'];
                    if($i == 1) $this->arSelectProp[$keyType]['ONE'] = true;
                }elseif($i != 1){
                    $this->arSelectProp[$keyType]['VALUES'][$keyProp]['HIDDEN'] = 'Y';
                }
            }
            $this->arSelectProp[$keyType]['POSITION'] = $i;
            $i++;
        }
    }

    function unsetEmpetyProp(){ // удаляем первый вариант выбора торгового
        foreach ($this->arSelectProp as $key => $prop){
            if($prop['ONE'] && count($prop['VALUES']) == 1){
            
                if ($prop['VALUES'][key($prop['VALUES'])]['CODE'] == 'KOD_TSVETA') {
                    $this->arSelectProp[$key]['VALUES'][key($prop['VALUES'])]['HIDDEN'] = 'Y'; continue;
                }
                
                unset($this->arSelectProp[$key]);
            }
        }
    }

    function sortSelectOffers(){ // меняем ключи для js
        foreach ($this->arSelectProp as $key=>$prop){
           $arValues =[];
           foreach ($prop['VALUES'] as $value){
               $arValues[] = $value;
           }
            $this->arSelectProp[$key]['VALUES'] =  $arValues;
        }
    }

    function skuCheck($prop){ // Добовляем id товара в фильтр предложений
        foreach($this->arSkuKey as $sku){
            if (array_search($sku, $prop) !== false) {
                $this->arSkuKey = array_merge($this->arSkuKey,$prop);
                return true;
            }
        }
        return false;
    }

    function getTypePropertyOffersPhoto(){ // Добовляем изображение вместо кода цвета
        foreach ($this->arSelectProp as $keyType =>$type){
            if(array_search($keyType, $this->arParams['OFFERS_PROPERTY_TYPE_IMAGES_LINK']) !== false){
                $this->arSelectProp[$keyType]['NAME'] = 'Цвет';
                $this->arSelectProp[$keyType]['TYPE'] = 'IMAGES';
                foreach ($type['VALUES'] as $keyProp => $prop) {
                    $this->arSelectProp[$keyType]['VALUES'][$keyProp]['VALUE_STRING'] = explode("#", $this->arSelectProp[$keyType]['VALUES'][$keyProp]['VALUE'])[0];
                    $this->arSelectProp[$keyType]['VALUES'][$keyProp]['SRC'] = $this->arResult['OFFERS'][$prop['OFFERS'][0]]['IMAGES']['ICON'][1]['src'];
                    if($prop['CHECKED'] == 'Y') $this->arSelectProp[$keyType]['CHECKED_VALUE'] =  $this->arSelectProp[$keyType]['VALUES'][$keyProp]['VALUE_STRING'];
                }
            }
        }
    }

    function newNameOffers(){ // новые имена предложени
        foreach ($this->arResult['OFFERS'] as $keyOffer=>$offers){
            foreach ($offers['PROPERTIES'] as $prop){
                if(array_search($prop['CODE'],$this->arParams['OFFERS_SELECT_PROPERTY']) !== false && count($this->arSelectProp[$prop['CODE']]['VALUES']) > 1){
                    if(array_search($prop['CODE'], $this->arParams['OFFERS_PROPERTY_TYPE_IMAGES_LINK']) !== false){
                        $this->arResult['OFFERS'][$keyOffer]['NAME'] =   $this->arResult['NAME'].' '.explode("#", $prop['VALUE'])[0];
                        $GLOBALS['variant_name'] = explode("#", $prop['VALUE'])[0];
                    }elseif (array_search($prop['CODE'], $this->arParams['OFFERS_PROPERTY_TYPE_SM']) !== false){
                        $this->arResult['OFFERS'][$keyOffer]['NAME'] =   $this->arResult['NAME'].' '.$prop['VALUE'].' см';
                        $GLOBALS['variant_name'] = $prop['VALUE'].' см';
                    }else{
                        $this->arResult['OFFERS'][$keyOffer]['NAME'] =   $this->arResult['NAME'].' '.$prop['VALUE'];
                    }
                    break;
                }
            }
            if(empty($this->arResult['OFFERS'][$keyOffer]['NAME'])) $this->arResult['OFFERS'][$keyOffer]['NAME'] =   $this->arResult['NAME'];
        }
    }

    function sortOffers(){ // сортируем свойство в зависемости от количства
        foreach ($this->arSelectProp as $keyProp => $prop){
            $arQuantityPlus = [];
            $arQuantityNull = [];
            foreach ($prop['VALUES'] as $keyValue=> $value){
                if($this->arResult['OFFERS'][$value['OFFERS'][0]]['CATALOG_QUANTITY'] > 0){
                    $arQuantityPlus[] = $value;
                }else{
                    $arQuantityNull[] = $value;
                }
            }
            $this->arSelectProp[$keyProp]['VALUES'] = array_merge($arQuantityPlus,$arQuantityNull);
        }
    }
    //

    //Товар
    function favorite(){ // узнаем в закладках ли товар
        if(array_search($this->arResult['ID'],$_SESSION['FAVORITES']) !== false && is_array($_SESSION['FAVORITES'])){
            $this->arResult['FAVORITE'] = true;
        }
    }

    function productRecommended(){ // Дополнительные товары
      $_SESSION['filterCatalogElementRecommended']['ID'] =$GLOBALS['filterCatalogElementRecommended']['ID'] = $this->arResult['PROPERTIES']['OTHER_CONFIG']['VALUE'];
    }

    function  getPrepayment(){ // Предоплата
        \Bitrix\Main\Loader::includeModule('highloadblock');
        $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/settings'))->fetch();
        $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();
        $arSettings = $strEntityDataClass::getList(['select' => ['ID','UF_PREPAYMENT','UF_PAY_PRECENT']])->fetch();
        $this->arResult['PREPAYMENT'] = ['CHECK'=>$arSettings['UF_PREPAYMENT'],'PRECENT'=>$arSettings['UF_PAY_PRECENT']];
    }

    function getPropertyDopolnitelno(){ // Получаем значение свойства "Дополнительно"
        foreach ($this->arResult['PROPERTIES'] as $property){
            if(strripos($property['CODE'],'DOPOLNITELNO') !== false && !empty($property['VALUE'])){
                $this->arResult['DOPOLNITELNO_TEXT'] =  $property['VALUE'];
                break;
            }
        }
    }
    //

    // Ajax
    function addFavorite($productId){ // добовляем в избранное
        if(is_array($_SESSION['FAVORITES'])){
            $intKeyExist = array_search($productId, $_SESSION['FAVORITES']);
            if($intKeyExist !== false){
                unset($_SESSION['FAVORITES'][$intKeyExist]);
            }else{
                $_SESSION['FAVORITES'][] = $productId;
            }
        }else{
            $_SESSION['FAVORITES'][] = $productId;
        }

        return 'Y';
    }
    //

    // Данные для js
    function getArrJs() { // Данные для js
        $this->arJs = [
            'PROPERTY_SELECT'=>$this->arSelectProp,
            'OFFERS'         =>$this->arResult['OFFERS'],
            'OFFERS_ACTIVE'  =>$this->arResult['OFFERS_ACTIVE'],
            'PARAMS'         =>$this->arParams,
            'PRODUCT'        =>[
                'ID'       => $this->arResult['ID'],
                'FAVORITE' => $this->arResult['FAVORITE'],
                'DETAIL_PAGE_URL'=>$this->arResult['DETAIL_PAGE_URL']
            ],
            'PREPAYMENT'   => $this->arResult['PREPAYMENT'],
            'ARRIVAL_DATE'   => $this->arResult['PROPERTIES']['ARRIVAL_DATE'],
        ];
        $this->arResult['JS_DATA'] = $this->arJs;
    }
    //

    function modificationArResult($arResult,$arParams){
        $this->arResult = $arResult;
        $this->arParams = $arParams;

        //Торговые предложения
        $this->offersGetList(); // Проходимся по всем торговым предложениям
        $this->getPhoto(); // Получаем изображение товара
        $this->newKeyOffers(); // Меняем ключи для торговых предложений
        $this->getTypePropertyOffersPhoto(); // Добовляем изображение вместо кода цвета
        $this->newNameOffers(); //новые имена предложений
        $this->getActiveOffer(); // Получаем активное торговое предложение
        $this->getActiveTypeOffers(); // Соотносим свойства активного ТП с свойствами фильтра
        $this->unsetEmpetyProp(); // удаляем первый вариант выбора торгового
        $this->sortSelectOffers(); // меняем ключи для js
        $this->sortOffers(); // сортируем свойство в зависемости от количства
        //
        //Товар
        $this->favorite(); // узнаем в закладках ли товар
        $this->productRecommended();  // Дополнительные товары
        $this-> getPrepayment(); // Предоплата
        $this->getPropertyDopolnitelno(); // Получаем значение свойства "Дополнительно"
        //
        // Данные для js
        $this->getArrJs();  // Данные для js
        //
        $GLOBALS['PRODUCT_NAME'] = $this->arResult['NAME'];
        $GLOBALS['PRICE'] = $this->arResult['OFFERS_ACTIVE']['RECOMMEND_PRICE'];
        $this->arResult['PROPERTY_SELECT'] = $this->arSelectProp;
        $this->arResult['AR_OFFERS_ID'] =  $this->arOffersId;

        return  $this->arResult;
    }
}
