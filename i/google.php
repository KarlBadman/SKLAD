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
    <g:title>#TITLE#</g:title>
    <g:description>#DESCRIPTION#</g:description>  
    <g:link>#SITE_URL##LINK#&#063;utm_source=google.goods&amp;utm_medium=cpc&amp;utm_campaign=merchant</g:link>    
    <g:mobile_link>#SITE_URL##MOBILE_LINK#&#063;utm_source=google.goods&amp;utm_medium=cpc&amp;utm_campaign=merchant_mobile</g:mobile_link>
    <g:image_link>#SITE_URL##IMAGE_LINK#</g:image_link>
    <g:condition>new</g:condition>
    <g:availability>#AVAILABILITY#</g:availability>
	<g:product_type>#MARKET_CATEGORY#</g:product_type>
    <g:price>#PRICE#</g:price>
    <g:sale_price>#SALE_PRICE#</g:sale_price>
    <g:installment>
        <g:months>3</g:months>
        <g:amount>#CREDIT_PRICE#</g:amount>
    </g:installment>
</item>' . PHP_EOL;

require_once(__DIR__ . '/feeds.php');
$feeds = new feeds();

use Bitrix\Main\Application;
$request = Application::getInstance()->getContext()->getRequest();

$task = !empty(@$argv[1]) ? @$argv[1] : $request->getPost('task');

#@TODO move to options in module settings
//опции для основного шаблона
$OPTIONS = array(
    '#ENCODING#' => 'utf-8',
    '#SITE_URL#' => $feeds->SITE_URL_DEFINE,
    '#DESCRIPTION#' => 'Дизайнерские стулья и столы',
);
//сопоставление идентификаторов верхних категорий товаров с названиями в гугле
$MERCHANT_CATEGORIES = array(
    160 => 'Мебель > Столы > Кухонные и обеденные столы', //Столы
    161 => 'Мебель > Кресла > Кухонные и столовые стулья', //Стулья
    186 => 'Мебель > Наборы мебели > Наборы мебели для кухонь и столовых комнат', //Комплекты
);

//исключаем категории, что возжелал исключать маркетинг для конкретного фида
$CURRENT_FEED_EXLUDED_CATEGORIES = array(
    212, // свет
);

$feeds->printInfo('START');
$categoryArr = array();
$tovars = array();
$items_list = '';

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
        "PROPERTY_HIDE2VIEW",
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
            'RECOMMENDED_QUANTITY_FOR_SALE',
            "HIDE2VIEW",
        ))
);

if($task == 'msk') {
    $file = $feeds->generateFilename('google_msk.xml');
} else {
    $file  = $feeds->generateFilename(__FILE__);
}

if (is_writable($file)) {
    $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
    $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
    $strEntityDataClass = $obEntity->getDataClass();
    foreach ($predl as $key => $values) {
        foreach ($values as $product_id => $item) {
            //скрытие позиций
            if(($task == 'msk') && (!empty($item['PROPERTIES']['HIDE2VIEW']['VALUE']) || !empty($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["PROPERTY_HIDE2VIEW_VALUE"]))) {
                continue;
            }
            if ($item > 0) {
                $PRODUCT['#ID#'] = $product_id;
                $PRODUCT['#TITLE#'] = str_replace($feeds->from, $feeds->to, $item["NAME"]);

                #@TODO вынести это в настройки конкретных фидов, у разных фидов это могут быть разные настройки
                //пропуск исключенных товаров по маске вхождения названия
                $EXCLUDED_PRODUCTS_BY_NAME = array(
                    'Носки ',
                );
                foreach ($EXCLUDED_PRODUCTS_BY_NAME as $exclude_text){
                    if (strpos($PRODUCT['#TITLE#'], $exclude_text) !== false){
                        continue 2;
                    }
                }

                if ($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_TEXT"]) {
                    $PRODUCT['#DESCRIPTION#'] = cutString(str_replace($feeds->from, $feeds->to, strip_tags($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["~DETAIL_TEXT"])),5000); //ограничим описания соглавно требованиям
                } else {
                    $PRODUCT['#DESCRIPTION#'] = str_replace($feeds->from, $feeds->to, strip_tags($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["NAME"]));
                }

                $PRODUCT['#SITE_URL#'] = $feeds->SITE_URL_DEFINE;
                $PRODUCT['#LINK#'] = $PRODUCT['#MOBILE_LINK#'] = (substr_count($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"], $product_id) == 0) ? $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"] . $product_id . '/' : $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"];

                $PRODUCT['#IMAGE_LINK#'] = empty($item["PROPERTIES"]["FOTOGRAFIYA_2"]["VALUE"]) ? $item["PROPERTIES"]["FOTOGRAFIYA_1"]["VALUE"] : $item["PROPERTIES"]["FOTOGRAFIYA_2"]["VALUE"]; // если у товара 1 фото, например, комплекты
                $rsData = $strEntityDataClass::getList(array(
                    'select' => array('UF_FILE'),
                    'filter' => array('UF_XML_ID' => $PRODUCT['#IMAGE_LINK#']),
                    'limit' => '1',
                ));
                if ($arItem = $rsData->fetch()) {
                    if ($arItem['UF_FILE']) {
                        $source = CFile::GetFileArray($arItem['UF_FILE']);
                        $arImage = CFile::ResizeImageGet($source, Array('width' => 550, 'height' => 550), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
                        $PRODUCT['#IMAGE_LINK#'] = $arImage['src'];
                    }
                }
                #$PRODUCT['#CONDITION#'];
                $PRODUCT['#AVAILABILITY#'] = ($item["CATALOG_QUANTITY"] > 0) ? "in stock" : "out of stock";

                $group = CIBlockElement::GetElementGroups($key, true)->Fetch(); //вытягиваем информацию о категории верхнего уровня, чтобы сопоставить с их именами в гугле
                $nav = CIBlockSection::GetNavChain(false, $group['ID']);
                $PRODUCT['#MARKET_CATEGORY#'] = $MERCHANT_CATEGORIES[$nav->ExtractFields("nav_")['ID']];

                $arOptimalPrice = CCatalogProduct::GetOptimalPrice($product_id, $feeds->getProductCount($item["PROPERTIES"]["RECOMMENDED_QUANTITY_FOR_SALE"]["VALUE"], true)); //вытягиваем цены по заданному кол-ву
                $PRODUCT['#PRICE#'] =  number_format($arOptimalPrice['MAX_PRICE'], 2, '.', '');
                $PRODUCT['#SALE_PRICE#'] =  number_format($arOptimalPrice['DISCOUNT_PRICE'], 2, '.', '');
                $PRODUCT['#CREDIT_PRICE#'] =  number_format($arOptimalPrice['DISCOUNT_PRICE']/3, 2, '.', '');
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