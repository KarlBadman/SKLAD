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
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Page\Asset;
use \Bitrix\Conversion\Internals\MobileDetect;

$APPLICATION->AddViewContent('page_type', 'data-page-type="catalog-list"');

Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/css/selectize.css');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/selectize.min.js');?>
<? if ($_COOKIE['ajax_get_page'] != 'Y'):?>
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

    <div class="ds-wrapper">
        <div class="ds-catalog">
            <h1><?$APPLICATION->ShowTitle(false)?></h1>

            <? $GLOBALS[$arParams['SECTION_TOP_FILTER_NAME']] = ['UF_NO_MENU' => false] ?>

            <?
            $sectionParentId = \Bitrix\Iblock\SectionTable::getList(array(
                'select' => array('IBLOCK_SECTION_ID'),
                'filter' => array('CODE' => $arResult["VARIABLES"]["SECTION_CODE"])
            ))->fetch()['IBLOCK_SECTION_ID'];
            ?>

            <?$APPLICATION->IncludeComponent(
                "bitrix:catalog.section.list",
                "catalogDskladInner",
                array(
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                    "COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
                    "TOP_DEPTH" => '',
                    "SECTION_URL" => $arParams['SEF_FOLDER'].$arParams['SEF_URL_TEMPLATES']['section'],
                    "VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
                    "SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
                    "HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
                    "ADD_SECTIONS_CHAIN" => 'N',
                    "FILTER_NAME"=>$arParams['SECTION_TOP_FILTER_NAME'],
                    "SECTION_USER_FIELDS"=>$arParams['SECTION_TOP_SECTION_USER_FIELDS'],
                    "SECTION_FIELDS" => $arParams['SECTION_TOP_SECTION_FIELDS'],
                    "SECTION_CODE" => empty($sectionParentId) ? $arResult["VARIABLES"]["SECTION_CODE"] : false,
                    "SECTION_ID" => !empty($sectionParentId) ? $sectionParentId : false,
                ),
                $component,
                array("HIDE_ICONS" => "Y")
            );
            ?>

            <?$detect = new MobileDetect;
            if($detect->isMobile() && !$detect->isTablet()){
                $typeBanner = $arParams['BANNER_MOBILE_CATALOG_SECTION'];
                $mobile = true;
            }else{
                $typeBanner = $arParams['BANNER_CATALOG_SECTION'];
                $mobile = false;
            }
            $APPLICATION->IncludeComponent(
                'bitrix:advertising.banner',
                'catalogDsklad',
                array(
                    'CACHE_TIME' => $arParams["CACHE_TYPE"],
                    'CACHE_TYPE' => $arParams["CACHE_TYPE"],
                    'NOINDEX' => 'N',
                    'QUANTITY' => '99',
                    'TYPE' => $typeBanner,
                    'MOBILE' => $mobile,
                ),
                false
            );?>

            <div class="ds-sorting ds-select">
                <select name="SORT_CATALOG" class="js-chosen-select">
                    <option value="">Сортировать по:</option>
                    <option value="CHEAPER" <?if($_COOKIE['SORT_CATALOG'] == 'CHEAPER'):?>selected="selected"<?endif;?>>Сначала дешевые</option>
                    <option value="EXPENSIVE" <?if($_COOKIE['SORT_CATALOG'] == 'EXPENSIVE'):?>selected="selected"<?endif;?>>Сначала дорогие</option>
                    <option value="POPULAR" <?if($_COOKIE['SORT_CATALOG'] == 'POPULAR'):?>selected="selected"<?endif;?>>Популярные</option>
                    <option value="NEW" <?if($_COOKIE['SORT_CATALOG'] == 'NEW'):?>selected="selected"<?endif;?>>Новинки</option>
                </select>
            </div>

            <?if($arParams['USE_FILTER'] == 'Y'):?>
                <div class="ds-catalog__filter">
                    <?
                    $APPLICATION->IncludeComponent(
                        "bitrix:catalog.smart.filter",
                        "catalogDsklad",
                        array(
                            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                            "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                            "FILTER_NAME" => $arParams["FILTER_NAME"],
                            "PRICE_CODE" => ['Основная цена продажи'],
                            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                            "CACHE_TIME" => $arParams["CACHE_TIME"],
                            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                            "SAVE_IN_SESSION" => "N",
                            "FILTER_VIEW_MODE" => $arParams["FILTER_VIEW_MODE"],
                            "XML_EXPORT" => "N",
                            'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
                            "TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
                            'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                            'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                            "SEF_MODE" => $arParams["SEF_MODE"],
                            "SEF_RULE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
                            "SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
                            "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
                            "INSTANT_RELOAD" => 'Y',
                        ),
                        $component,
                        array('HIDE_ICONS' => 'Y')
                    );
                    ?>
                </div>
            <?endif?>

<? endif; ?>

            <?if($_COOKIE['SORT_CATALOG']) {
                switch ($_COOKIE['SORT_CATALOG']) {
                    case 'CHEAPER':
                        $sort = [
                            'ELEMENT_SORT_FIELD'=>'PROPERTY_MINIMUM_PRICE',
                            'ELEMENT_SORT_ORDER'=>'asc',
                            'ELEMENT_SORT_FIELD2'=>'sort',
                            'ELEMENT_SORT_ORDER2'=>'asc',
                        ];
                        break;
                    case 'EXPENSIVE':
                        $sort = [
                            'ELEMENT_SORT_FIELD'=>'PROPERTY_MINIMUM_PRICE',
                            'ELEMENT_SORT_ORDER'=>'desc',
                            'ELEMENT_SORT_FIELD2'=>'sort',
                            'ELEMENT_SORT_ORDER2'=>'asc',
                        ];
                        break;
                    case 'POPULAR':
                        $sort = [
                            'ELEMENT_SORT_FIELD'=>'shows',
                            'ELEMENT_SORT_ORDER'=>'desc',
                            'ELEMENT_SORT_FIELD2'=>'sort',
                            'ELEMENT_SORT_ORDER2'=>'asc',
                        ];
                        break;
                    case 'NEW':
                        $sort = [
                            'ELEMENT_SORT_FIELD'=>'PROPERTY_NEW',
                            'ELEMENT_SORT_ORDER'=>'desc',
                            'ELEMENT_SORT_FIELD2'=>'sort',
                            'ELEMENT_SORT_ORDER2'=>'asc',
                        ];
                        break;
                    case 'SALE':
                        $sort = [
                            'ELEMENT_SORT_FIELD'=>'PROPERTY_SALE',
                            'ELEMENT_SORT_ORDER'=>'desc',
                            'ELEMENT_SORT_FIELD2'=>'sort',
                            'ELEMENT_SORT_ORDER2'=>'asc',
                        ];
                        break;
                    default:
                        $sort = [
                            'ELEMENT_SORT_FIELD'=>$arParams["ELEMENT_SORT_FIELD"],
                            'ELEMENT_SORT_ORDER'=>$arParams["ELEMENT_SORT_ORDER"],
                            'ELEMENT_SORT_FIELD2'=>$arParams["ELEMENT_SORT_FIELD2"],
                            'ELEMENT_SORT_ORDER2'=>$arParams["ELEMENT_SORT_ORDER2"],
                        ];
                        break;
                }
            }else {
                $sort = [
                    'ELEMENT_SORT_FIELD'=>$arParams["ELEMENT_SORT_FIELD"],
                    'ELEMENT_SORT_ORDER'=>$arParams["ELEMENT_SORT_ORDER"],
                    'ELEMENT_SORT_FIELD2'=>$arParams["ELEMENT_SORT_FIELD2"],
                    'ELEMENT_SORT_ORDER2'=>$arParams["ELEMENT_SORT_ORDER2"],
                ];
            }?>

        <?$intSectionID = $APPLICATION->IncludeComponent(
            "bitrix:catalog.section",
            "catalogDsklad",
            array(
                "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "ELEMENT_SORT_FIELD" => $sort["ELEMENT_SORT_FIELD"],
                "ELEMENT_SORT_ORDER" => $sort["ELEMENT_SORT_ORDER"],
                "ELEMENT_SORT_FIELD2" => $sort["ELEMENT_SORT_FIELD2"],
                "ELEMENT_SORT_ORDER2" => $sort["ELEMENT_SORT_ORDER2"],
                "PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
                "META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
                "META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
                "BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
                "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
                "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
                "BASKET_URL" => $arParams["BASKET_URL"],
                "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                "FILTER_NAME" => $arParams["FILTER_NAME"],
                "CACHE_TYPE" => $_COOKIE['ajax_get_page'] == 'Y' ? 'N' : $arParams["CACHE_TYPE"],
                "CACHE_TIME" => $arParams["CACHE_TIME"],
                "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                "SET_TITLE" => $arParams["SET_TITLE"],
                "MESSAGE_404" => $arParams["MESSAGE_404"],
                "SET_STATUS_404" => $arParams["SET_STATUS_404"],
                "SHOW_404" => $arParams["SHOW_404"],
                "FILE_404" => $arParams["FILE_404"],
                "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                "PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
                "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
                "PRICE_CODE" => $arParams["PRICE_CODE"],
                "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

                "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                "PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

                "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
                "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
                "PAGER_TITLE" => $arParams["PAGER_TITLE"],
                "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
                "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
                "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
                "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
                "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
                "PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
                "PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
                "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],

                "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
                "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
                "OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
                "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
                "OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],

                "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
                "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
                "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
                'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],

                'LABEL_PROP' => $arParams['LABEL_PROP'],
                'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
                'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

                'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
                'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
                'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
                'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
                'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
                'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
                'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
                'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
                'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
                'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],

                'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                "ADD_SECTIONS_CHAIN" => "Y",
                'ADD_TO_BASKET_ACTION' => $basketAction,
                'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
                'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],

                'OFFERS_SELECT_PROPERTY'=>$arParams['OFFERS_SELECT_PROPERTY'],

            ),
            $component
        );?>

<? if ($_COOKIE['ajax_get_page'] != 'Y'):?>
        </div>
    </div>
<?endif;?>
