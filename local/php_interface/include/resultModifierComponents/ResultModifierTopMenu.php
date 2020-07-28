<?
class ResultModifierTopMenu{

    public $arResult;
    public $arParams;
    public $arSectionCode;
    public $arSection;
    public $arSectionNoTopElement;
    public $arTopElementId;
    public $arProductId;
    public $arProduct;
    public $arTopElement;
    public $arPhotoId;
    public $arPhoto;

    function getSectionCode(){
        foreach ($this->arResult as $key => $item){
            $arUrl = explode('/',$item['LINK']);
            $this->arSectionCode[$item['LINK']] = !empty(end($arUrl)) ? end($arUrl) : $arUrl[count($arUrl)-2];
        }
    }

    function getSectionMenu(){

        $rsSections = CIBlockSection::GetList(
            [],
            [
                "GLOBAL_ACTIVE"=>"Y",
                "IBLOCK_ACTIVE"=>"Y",
                "CODE"=>$this->arSectionCode,
                "IBLOCK_ID"=>\Dsklad\Config::getParam('iblock/catalog'),
            ],
            false, [
                "ID",
                "UF_NO_MENU",
                "UF_TOP_ELEMENT",
                "CODE",
                "UF_MENUCLASS"
            ]
        );

        while($arSection = $rsSections->fetch()){
            if($arSection['DEPTH_LEVEL'] !=1) {
                if(empty($arSection['UF_NO_MENU'])){
                    if(empty($arSection['UF_TOP_ELEMENT'])) {
                        $this->arSectionNoTopElement[] = $arSection['ID'];
                    }else{
                        $this->arTopElementId[] = $arSection['UF_TOP_ELEMENT'];
                    }
                }
            }
            $this->arSection[$arSection['CODE']] = $arSection;
        }
    }

    function getElement(){
        if(empty($this->arSectionNoTopElement)) return false;

        foreach ($this->arSectionNoTopElement as $sectionId){
            $rsElement = CIBlockElement::GetList(
                ['order' => ["SORT" => "ASC"]],
                [
                    'IBLOCK_ID' => \Dsklad\Config::getParam('iblock/catalog'),
                    'IBLOCK_SECTION_ID' => $sectionId,
                    'ACTIVE' => 'Y'
                ],
                false,
                ['nTopCount'=>1],
                ['ID','IBLOCK_SECTION_ID']
            );

            if($arElement = $rsElement->fetch()){
                $this->arProduct[$arElement['IBLOCK_SECTION_ID']] = $arElement;
                $this->arProductId[] = $arElement['ID'];
            }
        }
    }

    function getOffers(){
        if(empty($this->arProductId)){
            $arFilter = [
                'ID'=>$this->arTopElementId,
                'ACTIVE'=>'Y',
                'IBLOCK_ID' => \Dsklad\Config::getParam('iblock/offers'),
            ];
        }else{
            $arFilter = [
                'ACTIVE'=>'Y',
                'IBLOCK_ID' => \Dsklad\Config::getParam('iblock/offers'),
                ['LOGIC'=>'OR',
                    ['ID'=>$this->arTopElementId],['PROPERTY_CML2_LINK'=>$this->arProductId]
                ]
            ];
        }

        $rsOffers = CIBlockElement::GetList(
            ['order' => ["SORT" => "ASC"]],
            $arFilter,
            false,
            [],
            ['ID', 'NAME', 'PROPERTY_FOTOGRAFIYA_1','PROPERTY_FOTOGRAFIYA_2','CATALOG_GROUP_2', 'DETAIL_PAGE_URL','PROPERTY_CML2_LINK']
        );

        while ($arElement = $rsOffers->GetNext()) {
            if(empty($this->arTopElement[$arElement['PROPERTY_CML2_LINK_VALUE']])) {
                $this->arTopElement[$arElement['PROPERTY_CML2_LINK_VALUE']] = $arElement;
                !empty($arElement['PROPERTY_FOTOGRAFIYA_1_VALUE'])? $this->arPhotoId[] = $arElement['PROPERTY_FOTOGRAFIYA_1_VALUE'] : $arElement['PROPERTY_FOTOGRAFIYA_2_VALUE'];
            }
        }
    }

    function getPhoto(){

        $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
        $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $photoEntityDataClass = $obEntity->getDataClass();

        $rsData = $photoEntityDataClass::getList(array(
            'select' => array('UF_FILE','UF_XML_ID'),
            'filter' => array('UF_XML_ID' => $this->arPhotoId),
            'limit' => '50',
        ));
        while ($arItem = $rsData->fetch()) {
            $arImage = CFile::GetFileArray($arItem['UF_FILE']);
            $arImage = CFile::ResizeImageGet($arImage, array('width' => 300, 'height' => 300), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
            $this->arPhoto[$arItem['UF_XML_ID']] = $arImage['src'];
        }
    }

    function collectsArResult(){
        foreach ($this->arResult as $keyResult =>$valueResult){

            $arUrl = explode('/',$valueResult['LINK']);
            $code = !empty(end($arUrl)) ? end($arUrl) : $arUrl[count($arUrl)-2];
            if(empty($this->arSection[$code])) continue;

            $this->arResult[$keyResult]['MENUCLASS'] = $this->arSection[$code]['UF_MENUCLASS'];

            if($valueResult['DEPTH_LEVEL'] == 1) continue;

            if(empty($this->arSection[$code]['UF_TOP_ELEMENT'])){
                $topElement = $this->arTopElement[$this->arProduct[$this->arSection[$code]['ID']]['ID']];
            }else{
                foreach ($this->arTopElement as $element){
                    if($this->arSection[$code]['UF_TOP_ELEMENT'] == $element['ID']){
                        $topElement = $element;
                        break;
                    }
                }
            }

            !empty($topElement['PROPERTY_FOTOGRAFIYA_1_VALUE'])? $photoId = $topElement['PROPERTY_FOTOGRAFIYA_1_VALUE'] :$photoId = $topElement['PROPERTY_FOTOGRAFIYA_2_VALUE'];

            if(!empty($photoId)) $topElement['IMAGES'] = $this->arPhoto[$photoId];

            $this->arResult[$keyResult]['TOP_ELEMENT'] =  $topElement;
        }
    }

    function noMenu(){
        foreach ($this->arResult as $key =>$arItem){
            $arUrl = explode('/',$arItem['LINK']);
            $code = !empty(end($arUrl)) ? end($arUrl) : $arUrl[count($arUrl)-2];
            if($this->arSection[$code]['UF_NO_MENU']){
                if($arItem['IS_PARENT']) {
                    foreach ($this->arResult as $keyChild =>$child){
                        if($keyChild > $key) {
                            if(!$child['IS_PARENT']) {
                                unset($this->arResult[$keyChild]);
                            }else{
                                break;
                            }
                        }
                    }
                }
                unset($this->arResult[$key]);
            }
        }
    }


    function modificationArResult($arResult,$arParams){
        CModule::IncludeModule("iblock");

        $this->arResult = $arResult;
        $this->arParams = $arParams;

        $this->getSectionCode();
        $this->getSectionMenu();
        $this->getElement();
        $this->getOffers();
        $this->getPhoto();
        $this->noMenu();
        $this->collectsArResult();

        return $this->arResult;
    }
}
