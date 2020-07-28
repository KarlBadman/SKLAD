<?php
class ResultModifierCatalogSectionRecommendElement
{
    function __construct()
    {
        $arHLBlockFoto = \Bitrix\Highloadblock\HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
        $obEntityFoto = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlockFoto);
        $this->photoEntityDataClass = $obEntityFoto->getDataClass();
    }

    function getPrice($itemKey){
        foreach ($this->arResult['ITEMS'][$itemKey]['OFFERS'][0]['PRICES'] as $keyPrice => $price) {
            if ($price['MIN_PRICE'] == 'Y') {
                $this->arResult['ITEMS'][$itemKey]['MIN_PRICE'] = true;
                $this->arResult['ITEMS'][$itemKey]['PRICE'] = $price['VALUE_VAT'];
            }
        }
    }

    function getPhoto($itemKey){ // Получаем изображение товара

        $rsData = $this->photoEntityDataClass::getList(array(
            'select' => array('UF_FILE', 'UF_XML_ID'),
            'filter' => array('UF_XML_ID' => $this->arResult['ITEMS'][$itemKey]['OFFERS'][0]['PROPERTIES']['FOTOGRAFIYA_1'])
        ));

        if ($arItem = $rsData->fetch()) {
            $arImage = CFile::GetFileArray($arItem['UF_FILE']);
            $this->arResult['ITEMS'][$itemKey]['PREVIEW_PICTURE'] = array_change_key_case(CFile::ResizeImageGet($arImage, array('width' => 200, 'height' => 200), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15)),CASE_UPPER);
        }
    }

    function itemGetList(){
        foreach ($this->arResult['ITEMS'] as $itemKey => $item) {
            $this->getPrice($itemKey);
            $this->getPhoto($itemKey);
        }
    }

    function modificationArResult($arResult,$arParams){
        $this->arResult = $arResult;
        if(!empty($this->arResult['ITEMS']))  $this->itemGetList();
        return $this->arResult;
    }
}
