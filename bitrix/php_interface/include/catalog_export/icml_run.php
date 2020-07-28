<?php
// $SETUP_SERVER_NAME
// $SETUP_FILE_NAME
// $XML_DATA
// $IBLOCK_ID
// $V
// $strExportErrorMessage

use Bitrix\Main\Loader;
use Swebs\Helper;

set_time_limit(0);

global $APPLICATION;

Loader::includeModule('iblock');
Loader::includeModule('catalog');
Loader::includeModule('swebs.helper');


$XML_DATA = unserialize(stripslashes($XML_DATA));
if (!is_array($XML_DATA)) $XML_DATA = array();

//$strExportErrorMessage = print_r($XML_DATA, true);


if (!function_exists('icml_text2xml')) {
    function icml_text2xml($text, $bHSC = false, $bDblQuote = false)
    {
        global $APPLICATION;

        $bHSC = (true == $bHSC ? true : false);
        $bDblQuote = (true == $bDblQuote ? true : false);

        if ($bHSC) {
            $text = htmlspecialcharsbx($text);
            if ($bDblQuote)
                $text = str_replace('&quot;', '"', $text);
        }
        $text = preg_replace('/[\x1-\x8\xB-\xC\xE-\x1F]/', '', $text);
        $text = str_replace("'", '&apos;', $text);
        return $text;
    }
}
if (!function_exists('get_price')) {
    function get_price($intProductID, $intGroupID)
    {
        $arResult = array(
            'PRICE' => 0,
            'PURCHASE' => 0
        );
        $arPriceFilter = array(
            'PRODUCT_ID' => $intProductID,
            'CATALOG_GROUP_ID' => $intGroupID
        );
        $dbPrice = CPrice::GetList(array(), $arPriceFilter);
        if ($arPriceFields = $dbPrice->Fetch()) {
            $arResult['PRICE'] = $arPriceFields['PRICE'];
        }
        $arProduct = CCatalogProduct::GetByID($intProductID);
        if (!empty($arProduct['PURCHASING_PRICE'])) {
            $arResult['PURCHASE'] = $arProduct['PURCHASING_PRICE'];
        }

        return $arResult;
    }
}

// sections
$arFilter = array(
    'IBLOCK_ID' => $IBLOCK_ID
);
if (is_array($V) && $V[0] != 0) {
    $arFilter['SECTION_ID'] = $V;
}
$arSelect = array('ID', 'NAME', 'CODE', 'IBLOCK_SECTION_ID');

$dbSections = CIBlockSection::GetList(array(), $arFilter, false, $arSelect);

$arSection = array();
while ($arSectionFields = $dbSections->GetNext()) {
    $intIblockSectionID = $arSectionFields['IBLOCK_SECTION_ID'];
    if (is_array($V) && array_search($intIblockSectionID, $V) !== false) {
        $intIblockSectionID = 0;
    }
    $arSection[$arSectionFields['ID']] = array(
        'name' => $arSectionFields['NAME'],
        'code' => $arSectionFields['CODE'],
        'parentID' => $intIblockSectionID
    );
}

// elements
$arFilter = array(
    'IBLOCK_ID' => $IBLOCK_ID,
);
if (is_array($V) && $V[0] != 0) {
    $arFilter['SECTION_ID'] = $V;
    $arFilter['INCLUDE_SUBSECTIONS'] = 'Y';
}
$arSelect = array(
    'ID',
    'NAME',
    'XML_ID',
    'ACTIVE',
    'IBLOCK_SECTION_ID',
    'DETAIL_PAGE_URL',
    'PROPERTY_CML2_ARTICLE',
    'PROPERTY_FOTOGRAFIYA_1',
    'PROPERTY_KOD_TSVETA',
);
if (is_array($XML_DATA) && is_array($XML_DATA['XML_DATA']['PARAMS'])) {
    foreach ($XML_DATA['XML_DATA']['PARAMS'] as $intPropID) {
        $arSelect[] = 'PROPERTY_' . $intPropID;
    }
}
$dbElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

$arElements = array();
while ($arElementFields = $dbElement->GetNext()) {
    $intProductID = $arElementFields['ID'];
    $arElement = array(
        'ID' => $intProductID,
        'PRODUCTID' => $intProductID,
        'URL' => $arElementFields['DETAIL_PAGE_URL'],
        'CATEGORYID' => $arElementFields['IBLOCK_SECTION_ID'],
        'NAME' => $arElementFields['NAME'],
        'PRODUCTACTIVITY' => $arElementFields['ACTIVE'],
        'PRODUCTNAME' => $arElementFields['NAME'],
        'ARTICLE' => $arElementFields['PROPERTY_CML2_ARTICLE_VALUE'],
        'XML_ID' => $arElementFields['XML_ID']
    );

    $arFields = array(
        'ID',
        'NAME',
        'ACTIVE',
        'XML_ID'
    );
    $arProperties = array(
        'CODE' => array(
            'FOTOGRAFIYA_1',
            'KOD_TSVETA'
        )
    );
    $arOffers = CCatalogSKU::getOffersList($intProductID, 0, array(), $arFields, $arProperties);
    if (is_array($arOffers) && count($arOffers) > 0) {
        foreach ($arOffers[$intProductID] as $i => $arItem) {
            //img
            if (!empty($arItem['PROPERTIES']['FOTOGRAFIYA_1']['VALUE'])) {
                $arFile = Helper\Highload\Element::getElement(15, array('UF_XML_ID' => $arItem['PROPERTIES']['FOTOGRAFIYA_1']['VALUE']), array('UF_FILE'));
                if (is_numeric($arFile[0]['UF_FILE'])) {
                    $arElement['IMG'] = CFile::GetPath($arFile[0]['UF_FILE']);
                }
            }
            //color
            if (!empty($arItem['PROPERTIES']['KOD_TSVETA']['VALUE'])) {
                $arFile = Helper\Highload\Element::getElement(21, array('UF_1C_CODE' => $arItem['PROPERTIES']['KOD_TSVETA']['VALUE']), array('UF_NAME'));
                if (!empty($arFile[0]['UF_NAME'])) {
                    $arElement['COLOR'] = $arFile[0]['UF_NAME'];
                }
            }
            //price
            $arPrice = get_price($arItem['ID'], $XML_DATA['PRICE']);
            $arElement['PRICE'] = $arPrice['PRICE'];
            $arElement['PURCHASEPRICE'] = $arPrice['PURCHASE'];
            //fields
            if ($arElement['PRODUCTACTIVITY'] == 'Y') {
                $arElement['PRODUCTACTIVITY'] = $arItem['ACTIVE'];
            }
            $arElement['ID'] = $arItem['ID'];
            $arElement['NAME'] = $arItem['NAME'];
            $arElement['XML_ID'] = $arItem['XML_ID'];
            $arElements[] = $arElement;
        }
    } else {
        //img
        if (!empty($arElementFields['PROPERTY_FOTOGRAFIYA_1_VALUE'])) {
            $arFile = Helper\Highload\Element::getElement(15, array('UF_XML_ID' => $arElementFields['PROPERTY_FOTOGRAFIYA_1_VALUE']), array('UF_FILE'));
            if (is_numeric($arFile[0]['UF_FILE'])) {
                $arElement['IMG'] = CFile::GetPath($arFile[0]['UF_FILE']);
            }
        }
        //color
        if (!empty($arElementFields['PROPERTY_KOD_TSVETA_VALUE'])) {
            $arFile = Helper\Highload\Element::getElement(21, array('UF_1C_CODE' => $arElementFields['PROPERTY_KOD_TSVETA_VALUE']), array('UF_NAME'));
            if (!empty($arFile[0]['UF_NAME'])) {
                $arElement['COLOR'] = $arFile[0]['UF_NAME'];
            }
        }
        //price
        $arPrice = get_price($intProductID, $XML_DATA['PRICE']);
        $arElement['PRICE'] = $arPrice['PRICE'];
        $arElement['PURCHASEPRICE'] = $arPrice['PURCHASE'];
        $arProduct = CCatalogProduct::GetByID($intProductID);
        $arElement['PURCHASEPRICE'] = $arProduct['PURCHASING_PRICE'];
        $arElements[] = $arElement;
    }
}

$strXML = '<?php' . "\n";
$strXML .= 'header("Content-Type: text/xml; charset=utf-8");' . "\n";
$strXML .= 'echo "<"."?xml version=\"1.0\" encoding=\"utf-8\"?".">"?>';
$strXML .= '<yml_catalog date="' . date('Y-m-d H:i') . '">' . "\n";
$strXML .= "<shop>\n";
$strXML .= '<name>' . htmlspecialcharsbx(COption::GetOptionString('main', 'site_name', '')) . "</name>\n";
$strXML .= '<company>' . htmlspecialcharsbx(COption::GetOptionString('main', 'site_name', '')) . "</company>\n";
$strXML .= "<categories>\n";

foreach ($arSection as $intID => $arItem) {
    $strXML .= '<category id="' . $intID . '"';
    if ($arItem['parentID']) {
        $strXML .= ' parentId="' . $arItem['parentID'] . '"';
    }
    $strXML .= '>' . icml_text2xml($arItem['name'], true) . '</category>' . "\n";
}

$strXML .= "</categories>\n";
$strXML .= "<offers>\n";

foreach ($arElements as $arElement) {
    $strXML .= '<offer id="' . $arElement['ID'] . '" productId="' . $arElement['PRODUCTID'] . '" >' . "\n";
    $strXML .= '<url>' . icml_text2xml('http://212.116.113.29' . $arElement['URL']) . '</url>' . "\n";
    $strXML .= '<price>' . $arElement['PRICE'] . '</price>' . "\n";
    $strXML .= '<purchasePrice>' . $arElement['PURCHASEPRICE'] . '</purchasePrice>' . "\n";
    $strXML .= '<productActivity>' . $arElement['PRODUCTACTIVITY'] . '</productActivity>' . "\n";
    $strXML .= '<categoryId>' . $arElement['CATEGORYID'] . '</categoryId>' . "\n";
    if (!empty($arElement['IMG'])) {
        $strXML .= '<picture>' . icml_text2xml('http://212.116.113.29' . $arElement['IMG']) . '</picture>' . "\n";
    } else {
        $strXML .= '<picture/>' . "\n";
    }
    $strXML .= '<name>' . icml_text2xml($arElement['NAME']) . '</name>' . "\n";
    $strXML .= '<xmlId>' . $arElement['XML_ID'] . '</xmlId>' . "\n";
    $strXML .= '<productName>' . icml_text2xml($arElement['PRODUCTNAME']) . '</productName>' . "\n";
    $strXML .= '<param name="Артикул" code="article">' . icml_text2xml($arElement['ARTICLE']) . '</param>' . "\n";
    $strXML .= '<param name="Цвет" code="color">' . icml_text2xml($arElement['COLOR']) . '</param>' . "\n";

    $strXML .= "</offer>\n";
}

$strXML .= "</offers>\n";
$strXML .= "</shop>\n";
$strXML .= "</yml_catalog>\n";


$fileXML = @fopen($_SERVER["DOCUMENT_ROOT"] . $SETUP_FILE_NAME, "wb");

if (!$fileXML) {
    $strExportErrorMessage .= 'Do not open file - ' . $_SERVER["DOCUMENT_ROOT"] . $SETUP_FILE_NAME;
}

@fwrite($fileXML, $strXML);
@fclose($fileXML);
