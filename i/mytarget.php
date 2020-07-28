<?
/**
 * reworked by
 * User: a.kobetskoy
 * Date: 20.07.2018
 * @see https://yandex.ru/support/partnermarket/export/yml.html
 * @see https://yandex.ru/support/webmaster/goods-prices/technical-requirements.html
 * @see https://yandex.ru/support/partnermarket/picture.html - инфа по размерам изображений для фидов!
 */
use Bitrix\Highloadblock\HighloadBlockTable;

#@TODO move to templates in module settings
$main_template = '<?xml version="1.0" encoding="UTF-8"?>
<torg_price date="#TIMESTAMP#">
    <shop>
        <name>#SITE_NAME_DEFINE#</name>
        <company>«#SITE_NAME_DEFINE#»</company>
        <url>#SITE_URL#</url>
        <currencies>
            <currency id="RUR" rate="1"/>
        </currencies>
        <categories>#CATEGORIES#</categories>
        <offers>';
$main_template_end = '</offers> 
    </shop>
</torg_price>';

$categories_itemplate = '<category id="#CATEGORY_ID#" #PARENT_CATEGORY#>#CATEGORY_NAME#</category>' . PHP_EOL;

$items_template = '<offer id="#ID#" available="#AVAILABILITY#">
    <name>#NAME#</name>
    <url>#SITE_URL##LINK#?#UTM#</url>
    <price>#PRICE#</price>
    <oldprice>#OLD_PRICE#</oldprice>
    <currencyId>RUR</currencyId>
    <categoryId>#CATEGORY_ID#</categoryId>
    <picture>#SITE_URL##IMG_URL#</picture>
    <typePrefix>#PRODUCT_TYPE#</typePrefix>
    <vendor>#VENDOR_CODE#</vendor>
    <model>#PRODUCT_MODEL#</model>
    <description><![CDATA[#DESCRIPTION#]]></description>
    </offer>' . PHP_EOL;

//Использовать наименьшую цену от количества
$useRecomendedPrices = true;

//исключаем категории, что возжелал исключать маркетинг для конкретного фида
$CURRENT_FEED_EXLUDED_CATEGORIES = array(
    212, // свет
);

require_once(__DIR__ . '/feeds.php');
$feeds = new feeds();

use Bitrix\Main\Application;
$request = Application::getInstance()->getContext()->getRequest();

$task = !empty(@$argv[1]) ? @$argv[1] : $request->getPost('task');

$SITE_NAME = $feeds->SITE_NAME_DEFINE;

$feeds->printInfo('START');
$categoryArr = array();
$tovars = array();


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
            "parent_text" => $ar_result["IBLOCK_SECTION_ID"] ? "parentId=\"" . $ar_result["IBLOCK_SECTION_ID"] . "\"" : "",
            "name" => $ar_result["NAME"],
            "count" => 0
        );
        $cat_id[] = $ar_result["ID"];
    }
}

$CIBlockElement = CIBlockElement::GetList(
    $arOrder = Array("SORT" => "ASC"),
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
            "TSVET_NOZHEK",
            "MATERIAL_STOLESHNITSY",
            "RAZMER_SH_KH_G_KH_V",
            "MATERIAL_STOLESHNITSY_1",
            "FOTOGRAFIYA_1",
            "KOD_TSVETA",
            "CML2_ATTRIBUTES",
            "UPAKOVKA_1_1",
            "TSVET_STOLESHNITSY",
            "FOTOGRAFIYA_2",
            "RAZMER_SH_KH_G_KH_V_1",
            "MAKSIMALNAYA_NAGRUZKA",
            "RAZMER_SH_KH_G_KH_V_2",
            "VES",
            "GABARITY_SH_KH_G_KH_V",
            "MATERIAL",
            "CML2_LINK",
            "CML2_BAR_CODE",
            "CML2_BAR_CODE CML2_MANUFACTURER",
            "ARRIVAL_DATE",
            "RECOMMENDED_QUANTITY_FOR_SALE",
        )
    )
);

if ($task == 'standard' || empty($task)) {
    $file = $feeds->generateFilename(__FILE__);
}

if (is_writable($file)) {
    //опции для основного шаблона
    $OPTIONS = array(
        '#TIMESTAMP#' => date("Y-m-d H:i"),
        '#SITE_NAME_DEFINE#' => $feeds->SITE_NAME_DEFINE,
        '#SITE_URL#' => $feeds->SITE_URL_DEFINE,
        '#CATEGORIES#' => '',
    );

    foreach ($categoryArr as $value) {
        if ($value["name"] == 'Комплекты') {
            $value["name"] = 'Столы и стулья';
        }
        $OPTIONS['#CATEGORIES#'] .= str_replace(array("#CATEGORY_ID#", "#PARENT_CATEGORY#", "#CATEGORY_NAME#"), array($value["id"], $value["parent_text"], $value["name"]), $categories_itemplate);
    }

    $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
    $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
    $strEntityDataClass = $obEntity->getDataClass();

    foreach ($predl as $values) {
        $offers = (count($values) > 1) ? true : false;
        foreach ($values as $product_id => $item) {
            if($selectively_reset_catalog_quantity && in_array($product_id, $selectively_ended_products)){
                $item["CATALOG_QUANTITY"] = 0;
            }
            if ($item > 0) {

                $PRODUCT['#IMG_URL#'] = empty($item["PROPERTIES"]["FOTOGRAFIYA_2"]["VALUE"]) ? $item["PROPERTIES"]["FOTOGRAFIYA_1"]["VALUE"] : $item["PROPERTIES"]["FOTOGRAFIYA_2"]["VALUE"]; // если у товара 1 фото, например, комплекты
                $rsData = $strEntityDataClass::getList(array(
                    'select' => array('UF_FILE'),
                    'filter' => array('UF_XML_ID' => $PRODUCT['#IMG_URL#']),
                    'limit' => '1',
                ));
                if ($arItem = $rsData->fetch()) {
                    if ($arItem['UF_FILE']) {
                        $source = CFile::GetFileArray($arItem['UF_FILE']);
                        $arImage = CFile::ResizeImageGet($source, Array('width' => 550, 'height' => 550), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
                        $PRODUCT['#IMG_URL#'] = $arImage['src'];
                    }
                }

                // Если товар без картинки, то он ломает фид. Скорее всего это товары из кривой заливки из 1С
                if (!empty($PRODUCT['#IMG_URL#'])) {
                    $PRODUCT['#NAME#'] = str_replace($feeds->from, $feeds->to, str_replace($feeds->ya_forbidden_strings, "", $item["NAME"]));
                    $PRODUCT['#AVAILABILITY#'] = ($item["CATALOG_QUANTITY"] > 0) ? 'true' : 'false';
                    $PRODUCT["#ID#"] = $product_id;
                    $PRODUCT["#SITE_URL#"] = $feeds->SITE_URL_DEFINE;
                    $PRODUCT["#LINK#"] = (substr_count($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"], $product_id) == 0) ? $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"] . $product_id . '/' : $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"];
                    preg_match('/([\w\-]+)/u', $PRODUCT['#NAME#'], $output_array);
                    $PRODUCT["#PRODUCT_TYPE#"] = $output_array[0];
                    $PRODUCT["#VENDOR_CODE#"] = 'dsklad';
                    $PRODUCT["#PRODUCT_MODEL#"] = $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["PROPS"]["CML2_ARTICLE"]["VALUE"];

                    if ($item["PROPERTIES"]["KOD_TSVETA"]["VALUE"]) {
                        $explode_value1_code = explode("#", $item["PROPERTIES"]["KOD_TSVETA"]["VALUE"]);
                        if ($explode_value1_code[0]) {
                            $color_name = trim($explode_value1_code[0]);
                            $color_translit = Utils::translit_str(trim($explode_value1_code[0]), true);
                        }
                    }

                    if ($task == 'standard' || empty($task)) {
                        $color_translit = (!empty($color_translit)) ? '_' . $color_translit : '' ;
                        $utm_content = strtolower(Utils::translit_str_tab_replace(preg_replace('/\xc2\xa0|\s/', '_', rtrim($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["NAME"], '.')), true) . $color_translit); //регулярка для замены пробелов и non breakable пробелов. Это для Bonito в частности
                        $utm_campaign = strtolower(Utils::translit_str($categoryArr[$tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["IBLOCK_SECTION_ID"]]['name'], true));
                        $PRODUCT["#UTM#"] = "utm_source=mytarget&amp;utm_medium=cpc&amp;utm_campaign=" . $utm_campaign . "&amp;utm_content=" . $utm_content;
                    }

                    $arOptimalPrice = CCatalogProduct::GetOptimalPrice($product_id, $feeds->getProductCount($item["PROPERTIES"]["RECOMMENDED_QUANTITY_FOR_SALE"]["VALUE"], $useRecomendedPrices)); //вытягиваем цены по заданному кол-ву
                    if ($arOptimalPrice['MAX_PRICE'] != $arOptimalPrice["DISCOUNT_PRICE"]) {
                        $PRODUCT['#OLD_PRICE#'] = number_format($arOptimalPrice['MAX_PRICE'], 2, '.', '');
                    }
                    $PRODUCT['#PRICE#'] = number_format($arOptimalPrice["DISCOUNT_PRICE"], 2, '.', '');
                    $PRODUCT['#CATEGORY_ID#'] = $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["IBLOCK_SECTION_ID"];
                    foreach($REPLACE_FOR_SOCKS as $single_word) {
                        if(strpos($PRODUCT['#NAME#'], $single_word) !== FALSE) {
                            $PRODUCT['#CATEGORY_ID#'] = $categoryArr[array_search('Фурнитура для мебели', array_column($categoryArr, 'name'))]['id'];
                        }
                    }
                    foreach($REPLACE_FOR_CHAIRS as $single_word) {
                        if(strpos($PRODUCT['#NAME#'], $single_word) !== FALSE) {
                            $PRODUCT['#CATEGORY_ID#'] = $categoryArr[array_search('Кресла', array_column($categoryArr, 'name'))]['id'];
                        }
                    }

                    if ($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_TEXT"]) {
                        $PRODUCT['#DESCRIPTION#'] = cutString(str_replace($feeds->from, $feeds->to, $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_TEXT"]), 3000);//ограничивание описания в 3000 символов
                    } else {
                        $PRODUCT['#DESCRIPTION#'] = str_replace($feeds->from, $feeds->to, strip_tags($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["NAME"]));
                    }

                    $PRODUCT = str_replace(array_keys($PRODUCT), array_values($PRODUCT), $items_template);
                    $items_list .= $feeds->removeEmptyBlocks($PRODUCT);
                    unset($color_name, $color_translit, $unit, $value2, $PRODUCT);
                }

            }
        }
    }

    $main_template = str_replace(array_keys($OPTIONS), array_values($OPTIONS), $main_template);
    $fp = fopen($file, "w");
    #$splitted = explode('#ITEMS#', $main_template);
    #fwrite($fp, $splitted[0] . $items_list . $splitted[1]);
    //так не красиво, зато быстрее
    fwrite($fp, $main_template . $items_list . $main_template_end);
    fclose($fp);
}
unset($tovars, $predl, $product_id, $values, $key1, $item);
$feeds->printInfo('END');
$feeds->printLink();
?>