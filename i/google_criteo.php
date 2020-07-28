<?php
/**
 * User: a.kobetskoy
 * Date: 11.07.2018
 * @see https://support.google.com/merchants/answer/7052112?hl=ru
 */

use Bitrix\Highloadblock\HighloadBlockTable;

#@TODO move to templates in module settings
$main_template = '<?xml version="1.0" encoding="#ENCODING#"?>
    <rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
    <channel>
        <title>103482283</title>
        <link>#SITE_URL#</link>
        <description>#DESCRIPTION#</description>
        #ITEMS#
    </channel>
</rss>';

$items_template = '<item>
    <g:id>#ID#</g:id>
    <title>#TITLE#</title>
    <description>#DESCRIPTION#</description>  
    <link>#SITE_URL##LINK#&#063;utm_source=criteo&amp;utm_medium=cpc&amp;utm_campaign=lowerfunnel</link>    
    <g:mobile_link>#SITE_URL##MOBILE_LINK#&#063;utm_source=criteo&amp;utm_medium=cpc&amp;utm_campaign=lowerfunnel</g:mobile_link>
    <g:image_link>#SITE_URL##IMAGE_LINK#</g:image_link>
    #ADDITIONAL_IMAGE_LINK#<g:condition>new</g:condition>
    <g:availability>#AVAILABILITY#</g:availability>
    <g:price>#PRICE#</g:price>
    <g:sale_price>#SALE_PRICE#</g:sale_price>
</item>' . PHP_EOL;

$additional_image_link = '<g:additional_image_link>#ADDITIONAL_IMAGE_URL#</g:additional_image_link>' . PHP_EOL;

require_once(__DIR__ . '/feeds.php');
$feeds = new feeds();

#@TODO move to options in module settings
//опции для основного шаблона
$OPTIONS = array(
    '#ENCODING#' => 'utf-8',
    '#SITE_URL#' => $feeds->SITE_URL_DEFINE,
    '#DESCRIPTION#' => 'Дизайнерские стулья и столы',
);

$feeds->printInfo('START');
$categoryArr = array();
$tovars = array();
$items_list = '';

//исключаем категории, что возжелал исключать маркетинг для конкретного фида
$CURRENT_FEED_EXLUDED_CATEGORIES = array(
    186, // комплекты
    212, // свет
);

// получаем категории
$categoryList = CIBlockSection::GetList(
    array("SORT" => "ASC"),
    array(
        "IBLOCK_ID" => $feeds->CATALOG_IBLOCK_ID,
        "ACTIVE" => "Y",
        "!ID" => array_merge($feeds->EXCLUDED_CATEGORIES, $CURRENT_FEED_EXLUDED_CATEGORIES)
        //из каталога фильтр
    ),
    array("ELEMENT_SUBSECTIONS" => "Y", "CNT_ACTIVE" => "Y"),
    ///array("ELEMENT_SUBSECTIONS" => "Y"),
    array(),
    false
);

while ($ar_result = $categoryList->GetNext()) {
    if ($ar_result["ELEMENT_CNT"] > 0) {
        $categoryArr[$ar_result["ID"]] = array(
            "id" => $ar_result["ID"],
            "parent_id" => $ar_result["IBLOCK_SECTION_ID"] ? $ar_result["IBLOCK_SECTION_ID"] : 0,
            "name" => $ar_result["NAME"],
            "count" => 0
        );
        $cat_id[] = $ar_result["ID"];
    }
}
$CIBlockElement = CIBlockElement::GetList(
    $arOrder = Array("SORT" => "ASC"),
    ///$arFilter = Array("IBLOCK_ID" =>$feeds->CATALOG_IBLOCK_ID,"SECTION_ID" => $cat_id),
    $arFilter = Array("IBLOCK_ID" => $feeds->CATALOG_IBLOCK_ID, "ACTIVE" => "Y", "SECTION_ID" => $cat_id),
    $arGroupBy = false,
    $arNavStartParams = false,
    $arSelectFields = Array(
        "NAME",
        "IBLOCK_SECTION_ID",
        "CODE",
        "IBLOCK_ID",
        "ID",
        "DETAIL_TEXT",
        "DETAIL_PAGE_URL",
    )
);

while ($ob = $CIBlockElement->GetNextElement()) {

    $ar_result = $ob->GetFields();
    $ar_props = $ob->GetProperties();

    $tovars[$ar_result['ID']] = $ar_result;
    $tovars[$ar_result['ID']]['PROPS'] = $ar_props;
    $tovars_id[] = $ar_result['ID'];

}

$predl = CCatalogSKU::getOffersList(
    $tovars_id,
    '',
    array("ACTIVE" => "Y"),
    ///array("AVAILABLE" => "Y", "ACTIVE" => "Y"),
    array("IBLOCK_ID", "ID", "CATALOG_QUANTITY", "NAME", "DETAIL_PAGE_URL"),
    array('CODE' =>
        array(
            'FOTOGRAFIYA_1',
            'FOTOGRAFIYA_2',
            'CML2_LINK',
            'RECOMMENDED_QUANTITY_FOR_SALE'))
);

if (is_writable($file = $feeds->generateFilename(__FILE__))) {
    $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
    $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
    $strEntityDataClass = $obEntity->getDataClass();
    foreach ($predl as $key => $values) {
        foreach ($values as $product_id => $item) {
            if ($item > 0) {
                $PRODUCT['#ID#'] = $product_id;
                $PRODUCT['#TITLE#'] = $item['NAME'];

                if ($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_TEXT"]) {
                    $PRODUCT['#DESCRIPTION#'] = cutString(str_replace($feeds->from, $feeds->to, strip_tags($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["~DETAIL_TEXT"])),5000); //ограничим описания соглавно требованиям
                } else {
                    $PRODUCT['#DESCRIPTION#'] = str_replace($feeds->from, $feeds->to, strip_tags($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["NAME"]));
                }

                $PRODUCT['#SITE_URL#'] = $feeds->SITE_URL_DEFINE;
                $PRODUCT['#LINK#'] = $PRODUCT['#MOBILE_LINK#'] = (substr_count($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"], $product_id) == 0) ? $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"] . $product_id . '/' : $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"];

                $rsData = $strEntityDataClass::getList(array(
                    'select' => array('UF_FILE', 'UF_XML_ID'),
                    'filter' => array('=UF_XML_ID' => array($item["PROPERTIES"]["FOTOGRAFIYA_1"]["VALUE"], $item["PROPERTIES"]["FOTOGRAFIYA_2"]["VALUE"]))
                ));

                $imgcount = $rsData->getSelectedRowsCount();

                while($arItem = $rsData->Fetch()) {
                    if ($arItem['UF_FILE']) {
                        $source = CFile::GetFileArray($arItem['UF_FILE']);
                        $arImage = CFile::ResizeImageGet($source, Array('width' => 550, 'height' => 550), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
                        if($arItem['UF_XML_ID'] == $item["PROPERTIES"]["FOTOGRAFIYA_2"]["VALUE"] || $imgcount == 1) {
                            $PRODUCT['#IMAGE_LINK#'] = $arImage['src'];
                        } else {
                            $PRODUCT['#ADDITIONAL_IMAGE_LINK#'] =  str_replace("#ADDITIONAL_IMAGE_URL#", $feeds->root_address() . $arImage['src'], $additional_image_link);
                        }
                    }
                }

                $PRODUCT['#AVAILABILITY#'] = ($item["CATALOG_QUANTITY"] > 0) ? "in stock" : "out of stock";

                $arOptimalPrice = CCatalogProduct::GetOptimalPrice($product_id, $feeds->getProductCount($item["PROPERTIES"]["RECOMMENDED_QUANTITY_FOR_SALE"]["VALUE"], true)); //вытягиваем цены по заданному кол-ву
                $PRODUCT['#PRICE#'] =  number_format($arOptimalPrice['MAX_PRICE'], 2, '.', '');
                $PRODUCT['#SALE_PRICE#'] =  number_format($arOptimalPrice['DISCOUNT_PRICE'], 2, '.', '');
            }
            $PRODUCT = str_replace(array_keys($PRODUCT), array_values($PRODUCT), $items_template);
            $items_list .= $feeds->removeEmptyBlocks($PRODUCT);
            unset($PRODUCT, $arOptimalPrice, $ID_IMG, $group, $nav);
        }
    }
    $main_template = str_replace(array_keys($OPTIONS), array_values($OPTIONS), $main_template);
    $splitted = explode('#ITEMS#', $main_template);

    $fp = fopen($file, "w");
    fwrite($fp, $splitted[0] . $items_list . $splitted[1]);
    fclose($fp);
}

$feeds->printInfo('END');
$feeds->printLink();
?>