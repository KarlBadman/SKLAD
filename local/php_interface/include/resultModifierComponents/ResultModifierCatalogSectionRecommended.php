<?php
class ResultModifierCatalogSectionRecommended {
    public $arResult = array();
    public $photoEntityDataClass;
    public $arJs = array();

    function __construct() {
        $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
        $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $this->photoEntityDataClass = $obEntity->getDataClass();
    }

    function getListItems($arItems=array()){

       foreach ($arItems as $key=>$item) {
           if (!empty($item['PROPERTIES']['FOTOGRAFIYA_1']['~VALUE'])) {
               $arItems[$key]['PREVIEW_PICTURE']['SRC'] = $this->getPhoto($item['PROPERTIES']['FOTOGRAFIYA_1']['~VALUE']);
           }
           $arJs = array(
               'DETAIL_PAGE_URL'=>$arItems[$key]['DETAIL_PAGE_URL'],
               'PREVIEW_PICTURE'=>array('SRC'=> $arItems[$key]['PREVIEW_PICTURE']['SRC'],'ALT'=> $arItems[$key]['PREVIEW_PICTURE']['ALT']),
               'NAME'=>$arItems[$key]['NAME'],
               'CATALOG_PRICE_2'=>$arItems[$key]['CATALOG_PRICE_2'],
               'CATALOG_PRICE_1'=>$arItems[$key]['CATALOG_PRICE_1'],
               'ID'=>$arItems[$key]['ID'],
           );

           $this->arJs['ITEMS'][] = $arJs;

       }
       return $arItems;
    }

    function getPhoto($photoId){
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

    function modificationArResult($arResult,$arParams){
        $this->arResult = $arResult;
        $this->arResult['ITEMS'] = $this->getListItems($arResult['ITEMS']);
        $this->arJs['COUNT'] = count($this->arJs['ITEMS']);
        $this->arResult['JS'] = $this->arJs;
        return $this->arResult;
    }
}