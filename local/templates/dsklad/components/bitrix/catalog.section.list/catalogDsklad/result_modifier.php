<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

function createSectionWood($arResult){
    $arWoodSection = [];
    foreach ($arResult['SECTIONS'] as $section){
       if(empty($section['IBLOCK_SECTION_ID'])){
           $name = 'Товары раздела '.mb_strtolower($section['NAME']);
           $parent = ['NAME' => $name, 'ID' => $section['ID'], 'SECTION_PAGE_URL' => $section['SECTION_PAGE_URL'], 'COUNT' => $section['ELEMENT_CNT'], 'PRODUCTS' => \Dsklad\Tools\Helpers::wordProducts($section['ELEMENT_CNT'])];
           $arWoodSection[$section['ID']] = $parent;
       }else{
           if(!empty($arWoodSection[$section['IBLOCK_SECTION_ID']]))
           $arWoodSection[$section['IBLOCK_SECTION_ID']]['CHILDREN'][] = $section;
       }
    }
    return $arWoodSection;
}

function init($arResult){

    $arResult = createSectionWood($arResult);

    return $arResult;
}

$arResult = init($arResult);
