<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;

$aMenuLinksExt=$APPLICATION->IncludeComponent("bitrix:menu.sections", "", array(
    "IS_SEF" => "Y",
    "SEF_BASE_URL" => "/catalog/",
    "SECTION_PAGE_URL" => "#SECTION_CODE#/",
    "DETAIL_PAGE_URL" => "#SECTION_CODE#/#ELEMENT_CODE#/",
    "IBLOCK_TYPE" => "1c_catalog",
    "IBLOCK_ID" => \Dsklad\Config::getParam('iblock/catalog'),
    "DEPTH_LEVEL" => "2",
    "CACHE_TYPE" => "A",
    "CACHE_TIME" => "36000000"
),
    false
);
$aMenuLinks = array_merge($aMenuLinksExt, $aMenuLinks);
?>
