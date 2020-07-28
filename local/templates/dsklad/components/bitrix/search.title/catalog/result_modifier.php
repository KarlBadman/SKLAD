<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Highloadblock\HighloadBlockTable;

CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');

$PREVIEW_WIDTH = intval($arParams['PREVIEW_WIDTH']);
if ($PREVIEW_WIDTH <= 0) {
    $PREVIEW_WIDTH = 75;
}

$PREVIEW_HEIGHT = intval($arParams['PREVIEW_HEIGHT']);
if ($PREVIEW_HEIGHT <= 0) {
    $PREVIEW_HEIGHT = 75;
}

$arParams['PRICE_VAT_INCLUDE'] = $arParams['PRICE_VAT_INCLUDE'] !== 'N';

/**
 * функция получения минимальной цены среди торговых предложений товара с id = $item_id
 * @param $item_id
 * @param $item_iblock_id
 * @param array $price_id
 * @return array|int
 */
function get_offer_min_price_slanes($item_id, $item_iblock_id, $price_id = array(1, 2)) {
    $ret = is_array($price_id) ? [] : 0;
    $IBLOCK_ID = $item_iblock_id;
    $ID = $item_id;
    $arInfo = \CCatalogSKU::GetInfoByProductIBlock($IBLOCK_ID);

    if (is_array($arInfo)) {
        $res = \CIBlockElement::GetList(
            array('PRICE' => 'ASC'),
            array(
                '>PRICE' => 0,
                'IBLOCK_ID' => $arInfo['IBLOCK_ID'],
                'ACTIVE' => 'Y',
                'PROPERTY_'.$arInfo['SKU_PROPERTY_ID'] => $ID
            ),
            false,
            false,
            array('ID', 'NAME')
        )->Fetch();
        if ($res) {
            if (is_array($price_id)) {
                foreach ($price_id as $priceId) {
                    $price = GetCatalogProductPrice($res['ID'], $priceId);
                    if ($price['PRICE']) {
                        $ret[$priceId] = $price['PRICE'];
                    }
                }
            } else {
                $price = GetCatalogProductPrice($res['ID'], $price_id);
                if ($price['PRICE']) {
                    $ret = $price['PRICE'];
                }
            }
        }
    }
    return $ret;
}

/**
 * @param $arProperties
 * @return mixed
 */
function getPictureOffers_slanes(&$arProperties){
    $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
    $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
    $photoEntityDataClass = $obEntity->getDataClass();

    for ($i = 0; $i < 6; ++$i) {
        if (!empty($arProperties['PROPERTY_FOTOGRAFIYA_' . $i . '_VALUE'])) {
            $rsData = $photoEntityDataClass::getList(array(
                'select' => array('UF_FILE'),
                'filter' => array('UF_XML_ID' => $arProperties['PROPERTY_FOTOGRAFIYA_' . $i . '_VALUE']),
                'limit' => '50',
            ));
            if ($arItem = $rsData->fetch()) {
                if ($arItem['UF_FILE']) {
                    return $arItem['UF_FILE'];
                }
            }
        }
    }
}


$arProductIds = array();
$arOffersIds = array();
$arSectionIds = array();

$arResult['ELEMENTS'] = array();
foreach($arResult['CATEGORIES'] as $category_id => $arCategory) {
    foreach($arCategory['ITEMS'] as $i => $arItem) {
        $element = $arItem;
        if (isset($arItem['ITEM_ID'])) {
            if ($arItem['MODULE_ID'] == 'iblock' && substr($arItem['ITEM_ID'], 0, 1) !== 'S') {
                if ($arItem['PARAM2'] == \Dsklad\Config::getParam('iblock/catalog')) {
                    $element['WHOIS'] = 'PRODUCT';
                    $arProductIds[] = $arItem['ITEM_ID'];
                }

                if ($arItem['PARAM2'] == \Dsklad\Config::getParam('iblock/offers')){
                    $element['WHOIS'] = 'OFFER';
                    $arOffersIds[] = $arItem['ITEM_ID'];
                }
            } else {
                $element['WHOIS'] = 'SECTION';
                $arSectionIds[] = substr($arItem['ITEM_ID'], 1);
            }
        } else {
            $element['WHOIS'] = 'SERVICE';
        }

        if($element['WHOIS'] != 'SERVICE') {
            $arResult['ELEMENTS'][$arItem['ITEM_ID']] = $element;
        }
    }
}

$arOffersIds = array_merge($arOffersIds, $arProductIds);

if (!empty($arOffersIds) && \CModule::IncludeModule('iblock')) {
    $arConvertParams = array();
    if ('Y' == $arParams['CONVERT_CURRENCY']) {
        if (!CModule::IncludeModule('currency')) {
            $arParams['CONVERT_CURRENCY'] = 'N';
            $arParams['CURRENCY_ID'] = '';
        } else {
            $arCurrencyInfo = \CCurrency::GetByID($arParams['CURRENCY_ID']);
            if (!(is_array($arCurrencyInfo) && !empty($arCurrencyInfo))) {
                $arParams['CONVERT_CURRENCY'] = 'N';
                $arParams['CURRENCY_ID'] = '';
            } else {
                $arParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
                $arConvertParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
            }
        }
    }

    $useCatalogTab = (string)\Bitrix\Main\Config\Option::get('catalog', 'show_catalog_tab_with_offers') == 'Y';

    if (is_array($arParams['PRICE_CODE'])) {
        $arResult['PRICES'] = \CIBlockPriceTools::GetCatalogPrices(0, $arParams['PRICE_CODE']);
    } else {
        $arResult['PRICES'] = array();
    }

    $arSelect = array(
        'ID',
        'NAME',
        'IBLOCK_ID',
        'PREVIEW_TEXT',
        'PREVIEW_PICTURE',
        'DETAIL_PICTURE',
        'PROPERTY_SALE',
        'PROPERTY_NEW',
        'PROPERTY_FOTOGRAFIYA_1',
        'PROPERTY_FOTOGRAFIYA_2',
        'PROPERTY_FOTOGRAFIYA_3',
        'PROPERTY_FOTOGRAFIYA_4',
        'PROPERTY_FOTOGRAFIYA_5',
        'PROPERTY_FOTOGRAFIYA_6',
        'PROPERTY_MIN_PRICE',
        'ACTIVE',
        'PROPERTY_HIDE2VIEW'
    );
    $arFilter = array(
        'IBLOCK_LID' => SITE_ID,
        'IBLOCK_ACTIVE' => 'Y',
        'ACTIVE_DATE' => 'Y',
        'ACTIVE' => 'Y',
        'CHECK_PERMISSIONS' => 'Y',
        'MIN_PERMISSION' => 'R',
    );

    foreach ($arResult['PRICES'] as $value) {
        if (!$value['CAN_VIEW'] && !$value['CAN_BUY']) {
            continue;
        }
        $arSelect[] = $value['SELECT'];
        $arFilter['CATALOG_SHOP_QUANTITY_'.$value['ID']] = 1;
    }

    $arFilter['=ID'] = $arOffersIds;

    $products = array();

    $rsElements = \CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    while ($arElement = $rsElements->Fetch()) {
        $arElement['PRICES'] = array();

        if ($arElement['CATALOG_TYPE'] != \Bitrix\Catalog\ProductTable::TYPE_SKU || $useCatalogTab) {
            $db_res = \CPrice::GetList(
                array(),
                array(
                    'PRODUCT_ID' => $arElement['ID'],
                    'CATALOG_GROUP_ID' => 2
                )
            );
            if ($ar_res = $db_res->Fetch()) {
                $arElement['PRICE'] = $ar_res['PRICE'];

                if ($arElement['CATALOG_PRICE_1'] > $arElement['PRICE']) {
                    $arElement['DISCOUNT_PERCENT'] = 100 - ceil($arElement['PRICE'] / $arElement['CATALOG_PRICE_1'] * 100);
                }
            }
        }

        if ($arElement['CATALOG_TYPE'] == \Bitrix\Catalog\ProductTable::TYPE_SKU) {
            $prices = get_offer_min_price_slanes($arElement['ID'], $arElement['IBLOCK_ID'], array(1, 2));
            $arElement['PRICE'] = $prices[2];

            if ($prices[1] > $arElement['PRICE']) {
                $arElement['DISCOUNT_PERCENT'] = 100 - ceil($arElement['PRICE'] / $prices[1] * 100);
            }
        }

        if($arParams['PREVIEW_TRUNCATE_LEN'] > 0) {
            $obParser = new \CTextParser;
            $arElement['PREVIEW_TEXT'] = $obParser->html_cut($arElement['PREVIEW_TEXT'], $arParams['PREVIEW_TRUNCATE_LEN']);
        }

        if ($arParams['SHOW_PREVIEW'] == 'Y') {
            if ($arElement['PREVIEW_PICTURE'] > 0) {
                $arElement['PICTURE'] = \CFile::ResizeImageGet(
                    $arElement['PREVIEW_PICTURE'],
                    array(
                        'width' => $PREVIEW_WIDTH,
                        'height' => $PREVIEW_HEIGHT
                    ),
                    BX_RESIZE_IMAGE_PROPORTIONAL,
                    true
                );
            } elseif ($arElement['DETAIL_PICTURE'] > 0) {
                $arElement['PICTURE'] = \CFile::ResizeImageGet(
                    $arElement['DETAIL_PICTURE'],
                    array(
                        'width' => $PREVIEW_WIDTH,
                        'height' => $PREVIEW_HEIGHT
                    ),
                    BX_RESIZE_IMAGE_PROPORTIONAL,
                    true
                );
            } elseif(($arElement['PICTURE'] = getPictureOffers_slanes($arElement)) > 0) {
                $arElement['PICTURE'] = \CFile::ResizeImageGet(
                    $arElement['PICTURE'],
                    array(
                        'width' => $PREVIEW_WIDTH,
                        'height' => $PREVIEW_HEIGHT
                    ),
                    BX_RESIZE_IMAGE_PROPORTIONAL,
                    true
                );
            }
        }
        $arElement['PRICE'] = number_format(intVal($arElement['PRICE']), 0, '', ' ');

        $product = \CCatalogSku::GetProductInfo($arElement['ID']);
        if (is_array($product)) {
            $products[$product['ID']] = array();
            $arElement['PRODUCT_ID'] = $product['ID'];
        }

        $arResult['ELEMENTS'][$arElement['ID']] = array_merge($arResult['ELEMENTS'][$arElement['ID']], $arElement);
    }

    if (!empty($products)) {
        $db_res = \CIBlockElement::GetList(
            array(),
            array(
                'IBLOCK_ID' => \Dsklad\Config::getParam('iblock/catalog'),
                'ID' => array_keys($products)
            ),
            false,
            false,
            array(
                'ID',
                'IBLOCK_SECTION_ID',
                'PROPERTY_SALE',
                'PROPERTY_NEW',
                'PROPERTY_MIN_CHECK',
                'ACTIVE'
            )
        );

        $sections = array();
        while ($product = $db_res->Fetch()) {
            $products[$product['ID']] = array(
                'SECTION_ID' => $product['IBLOCK_SECTION_ID'],
                'IS_SALE' => !empty($product['PROPERTY_SALE_VALUE']) || !empty($product['PROPERTY_MIN_CHECK_VALUE']),
                'IS_NEW' => !empty($product['PROPERTY_NEW_VALUE']),
                'ACTIVE' => $product['ACTIVE']
            );
            $sections[$product['IBLOCK_SECTION_ID']] = '';
        }

        foreach ($arResult['ELEMENTS'] as $key => $element) {
            $arResult['ELEMENTS'][$key]['SECTION_ID'] = $products[$element['PRODUCT_ID']]['SECTION_ID'];
            $arResult['ELEMENTS'][$key]['IS_SALE'] = $products[$element['PRODUCT_ID']]['IS_SALE'];
            $arResult['ELEMENTS'][$key]['IS_NEW'] = $products[$element['PRODUCT_ID']]['IS_NEW'];
        }

        $db_res = \CIBlockSection::GetList(
            array(),
            array(
                'IBLOCK_ID' => \Dsklad\Config::getParam('iblock/catalog'),
                'ID' => array_keys($sections)
            ),
            false,
            array('ID', 'NAME','SECTION_PAGE_URL'),
            false
        );
        while ($section = $db_res->GetNext()) {
            $sections[$section['ID']] = $section['NAME'];
            $arResult['SECTION_CATALOG'][$section['ID']] = $section;
        }

        foreach ($arResult['ELEMENTS'] as $key => $element) {
            $arResult['ELEMENTS'][$key]['SECTION_NAME'] = $sections[$element['SECTION_ID']];
        }
    }

    foreach ($arResult['ELEMENTS'] as $key => $element) {
        
        // HIDE2VIEW
        if (strtolower($element['PROPERTY_HIDE2VIEW_VALUE']) == 'весь мир') {
            $element['PROPERTY_HIDE2VIEW_VALUE'] = 'ABROAD';
        } else if (strtolower($element['PROPERTY_HIDE2VIEW_VALUE']) == 'весь мир и москва') {
            $element['PROPERTY_HIDE2VIEW_VALUE'] = 'MSKABROAD';
        }
        if (checkHitOnHideProductPosition($element['PROPERTY_HIDE2VIEW_VALUE'])) {
            unset($arResult['ELEMENTS'][$key]); continue;
        }
        
        if($products[$element['PRODUCT_ID']]['ACTIVE'] == 'N') {
            unset($arResult['ELEMENTS'][$key]);
        }
            if (strpos($element['URL'], 'offers') !== false) {
            $arResult['ELEMENTS'][$key]['URL'] = str_replace('?offers=', '', $element['URL']).'/';
        }
    }
}

function getSection($q,$module_id,$iblock,$iblockType){

    \Bitrix\Main\Loader::includeModule('search');

    if(iconv_strlen($q) < 2) return false;

    $obSearch = new CSearch;
    $obSearch->Search(array(
            "QUERY" => $q,
            "%ITEM_ID"=>"S",
            "MODULE_ID" => $module_id,
            "PARAM1"=>$iblockType,
        )
    );

    $arSectionId = [];

    while ($arSearch = $obSearch->Fetch()) {
        $arSectionId[] = $arSearch['ID'];
    }

    if(empty($arSectionId)) return false;

    $arSection =[];

    $db_res = \CIBlockSection::GetList(
        array('depth_level'=>'asc','sort'=>'asc'),
        array(
            'IBLOCK_ID' => $iblock,
            'ID' => $arSectionId
        ),
        false,
        array('ID', 'NAME','SECTION_PAGE_URL'),
        ['nPageSize' =>3]
    );
    while ($section = $db_res->GetNext()) {
        $arSection[$section['ID']] = $section;
    }

    return $arSection;
}

function getLinckSearch(){
    $arLinck = [];
    $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/search_links'))->fetch();
    $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
    $photoEntityDataClass = $obEntity->getDataClass();

    $rsData = $photoEntityDataClass::getList([]);
    if ($arItem = $rsData->fetch()) {
        $arLinck[] = ['TEXT'=>$arItem['UF_TEXT_LINCK_SEARCH'],'URL'=>$arItem['UF_LINCK_SEARCH']];
    }

    return $arLinck;
}

$arResult['LINKS'] = getLinckSearch();

//$arResult['SECTION_CATALOG'] = getSection($_REQUEST['q'],'iblock',\Dsklad\Config::getParam('iblock/catalog'),'1c_catalog' );