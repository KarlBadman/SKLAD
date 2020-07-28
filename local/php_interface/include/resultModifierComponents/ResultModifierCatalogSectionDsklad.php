
<?php
class ResultModifierCatalogSectionDsklad {

    public $arResult;
    public $arParams;
    public $arPropertySelect;

    function __construct() {
        $arHLBlockFoto = \Bitrix\Highloadblock\HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
        $obEntityFoto = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlockFoto);
        $this->photoEntityDataClass = $obEntityFoto->getDataClass();
    }

    // Товары
    function getListItems(){ // Проходимся по всем товарам
        foreach ($this->arResult['ITEMS'] as $keyItem => $item){
            $this->getListOffers($item['OFFERS'],$keyItem); // Проходимся по всем предложениям
            $this->showSelectProperty($keyItem); // Показывем количество предложений и свойство по которому они разлечаются
            $this->favorite($keyItem); // узнаем в закладках ли товар
        }
    }

    function showSelectProperty($keyItem){ // Показывем количество предложений и свойство по которому они разлечаются
        if(empty($this->arPropertySelect)) return false;
        uasort($this->arPropertySelect, function ($a, $b) {
            return (int)$a['SORT'] - (int)$b['SORT'];
        });
        foreach ($this->arPropertySelect as $prop){
            if(count($prop['AR_VALUE']) > 1){
               if(strripos($prop['CODE'],'KOD_TSVETA') !== false){
                   $prop['NAME'] = 'Вариантов цвета';
               }elseif (strripos($prop['CODE'],'RAZMER_STOLESHNITSY') !== false){
                   $prop['NAME'] = 'Вариантов товара';
               }elseif (strripos($prop['CODE'],'VID_KH_KA') !== false){
                   $prop['NAME'] = 'Вариантов товара';
               }
               $this->arResult['ITEMS'][$keyItem]['PROPERTY_SELECT'] = ['NAME'=>$prop['NAME'],'QUANTITY'=>count($prop['AR_VALUE'])];
               break;
            }
        }
        $this->arPropertySelect = [];
    }

    function favorite($keyItem){ // узнаем в закладках ли товар
        if(array_search($this->arResult['ITEMS'][$keyItem]['ID'],$_SESSION['FAVORITES']) !== false && is_array($_SESSION['FAVORITES'])){
            $this->arResult['ITEMS'][$keyItem]['FAVORITE'] = true;
        }
    }
    //

    // Предложения
    function getListOffers($offers,$keyItem){ // Проходимся по всем предложениям
        $offerActive = false;
        foreach ($offers as $keyOffer=> $offer){
            if($offer['CATALOG_QUANTITY'] > 0  && !$offerActive){
                $offerActive = $offer;
            }
            $this->countOffersOptions($offer['PROPERTIES']); //свойства по которому отличаются предложения
        }

        if(!$offerActive) {
            $offerActive = reset($offers);
            $this->arResult['ITEMS'][$keyItem]['PREORDER'] = true;
        }
        $this->arResult['ITEMS'][$keyItem]['OFFER_ID'] = $offerActive['ID'];
        $this->getPhoto($offerActive['PROPERTIES'],$keyItem); // Получаем фото товаров
        $this->getPrice($offerActive,$keyItem); // Получаем цены
    }

    function getPhoto($property,$keyItem){ // Получаем фото товаров
        for ($i = 1; isset($property['FOTOGRAFIYA_'.$i]); $i++) {
            if(!empty($property['FOTOGRAFIYA_'.$i]['VALUE'])){
                $rsData = $this->photoEntityDataClass::getList(array(
                    'select' => array('UF_FILE'),
                    'filter' => array('UF_XML_ID' => $property['FOTOGRAFIYA_'.$i]['VALUE']),
                    'limit' => '50',
                ));
                if ($arItem = $rsData->fetch()) {
                    $arImage = CFile::GetFileArray($arItem['UF_FILE']);
                    $arImage = CFile::ResizeImageGet($arImage, array('width' => 680, 'height' => 680), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
                    $this->arResult['ITEMS'][$keyItem]['PICTURE_ITEM'] = $arImage['src'];
                }
                break;
            }
        }
    }

    function  getPrice($offer,$keyItem){ // Получаем цены
        $minPrice = PHP_INT_MAX;
        $quantityFrom = 1;
        foreach ($offer['PRICES'] as $price){
            if($price['VALUE'] > $offer['MIN_PRICE']['VALUE']) {
                $this->arResult['ITEMS'][$keyItem]['RED'] = true;
                $this->arResult['ITEMS'][$keyItem]['SALE_PERCENT'] = 100 - round($offer['MIN_PRICE']['VALUE']*100/$price['VALUE']);
            }
            if($minPrice > $price['VALUE']) $minPrice = $price['VALUE'];
        }

        if(count($offer['ITEM_PRICES']) > 1){
            $this->arResult['ITEMS'][$keyItem]['FROM'] = $this->arResult['ITEMS'][$keyItem]['RED'] = true;
            foreach ($offer['ITEM_PRICES'] as $price){
                if($quantityFrom == 1) $quantityFrom = $this->arResult['ITEMS'][$keyItem]['QUANTITY_FROM'] = $price['QUANTITY_FROM'];
                if($minPrice > $price['PRICE']) $minPrice = $price['PRICE'];
            }
        }

        $minPrice < PHP_INT_MAX ? $this->arResult['ITEMS'][$keyItem]['TOTAL_PRICE'] = $minPrice : $this->arResult['ITEMS'][$keyItem]['TOTAL_PRICE'] = 0;
    }

    function countOffersOptions($property){ //свойства по которому отличаются предложения
        if(empty($this->arParams['OFFERS_SELECT_PROPERTY'])) return false;
        foreach ($property as $key => $prop){
            if(array_search($key,$this->arParams['OFFERS_SELECT_PROPERTY']) !== false && !empty($prop['VALUE'])){
                if(empty($this->arPropertySelect[$key])){
                    $prop['AR_VALUE'][$prop['VALUE']] = $prop['VALUE'];
                    $this->arPropertySelect[$key] = $prop;
                }else{
                    if(empty( $this->arPropertySelect[$key]['AR_VALUE'][$prop['VALUE']]))   $this->arPropertySelect[$key]['AR_VALUE'][$prop['VALUE']] =$prop['VALUE'];
                }
            }
        }
    }

    //

    function modificationArResult($arResult,$arParams){
        $this->arResult = $arResult;
        $this->arParams = $arParams;

        // Товары
        $this->getListItems();// Проходимся по всем товарам
        //
        return  $this->arResult;
    }
}
