<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$APPLICATION->AddViewContent('page_type', 'data-page-type="catalog-list"');
?>

<div class="ds-wrapper">
    <? $APPLICATION->IncludeComponent(
        'bitrix:breadcrumb',
        'template',
        array(
            'PATH' => '',
            'SITE_ID' => 's1',
            'START_FROM' => '0',
            'COMPONENT_TEMPLATE' => 'template'
        ),
        false
    ); ?>
</div>

<?$GLOBALS[$arParams['SECTION_TOP_FILTER_NAME']] = [
    'UF_NO_MENU'=>false
]?>

<div class="ds-wrapper">
    <?$APPLICATION->IncludeComponent(
        "bitrix:catalog.section.list",
        "catalogDsklad",
        array(
            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => $arParams["CACHE_TIME"],
            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
            "COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
            "TOP_DEPTH" => $arParams["SECTION_TOP_DEPTH"],
            "SECTION_URL" => $arParams['SEF_FOLDER'].$arParams['SEF_URL_TEMPLATES']['section'],
            "VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
            "SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
            "HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
            "ADD_SECTIONS_CHAIN" => $arParams['ADD_SECTIONS_CHAIN'],
            "FILTER_NAME"=>$arParams['SECTION_TOP_FILTER_NAME'],
            "SECTION_USER_FIELDS"=>$arParams['SECTION_TOP_SECTION_USER_FIELDS'],
            "SECTION_FIELDS" => $arParams['SECTION_TOP_SECTION_FIELDS'],
        ),
        $component,
        array("HIDE_ICONS" => "Y")
    );
    ?>
</div>
