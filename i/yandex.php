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

ini_set("memory_limit", '1G');

#@TODO move to templates in module settings

$main_template = '<?xml version="1.0" encoding="UTF-8"?>
<yml_catalog date="#TIMESTAMP#">
    <shop>
        <name>#SITE_NAME_DEFINE#</name>
        <company>«#SITE_NAME_DEFINE#»</company>
        <url>#SITE_URL#</url>
        <currencies>
            <currency id="RUR" rate="1"/>
        </currencies>
        <categories>#CATEGORIES#</categories>
        <delivery-options>
            <option cost="950" days="1-3"/>
        </delivery-options>
        <offers>';
$main_template_end = '</offers> 
    </shop>
</yml_catalog>';

$categories_itemplate = '<category id="#CATEGORY_ID#" #PARENT_CATEGORY#>#CATEGORY_NAME#</category>' . PHP_EOL;

$items_template = '<offer id="#ID#" available="#AVAILABILITY#" #GROUP_INFO#>
    <url>#SITE_URL##LINK#?#UTM#</url>
    <oldprice>#OLD_PRICE#</oldprice>
    <price>#PRICE#</price>
    <name>#NAME#</name>
    <currencyId>RUR</currencyId>
    <categoryId>#CATEGORY_ID#</categoryId>
    #IMAGES#<delivery-options><option cost="950" days="#DELIVERY_DAYS#"/></delivery-options>
    <store>false</store>
    <pickup>true</pickup>
    <delivery>true</delivery>
    <sales_notes>Наличные, безнал., эл. платежи</sales_notes>
    
    <typePrefix>#TYPE_PREFIX#</typePrefix>
    <vendor>Дизайн Склад</vendor>
    <vendorCode>#VENDOR_CODE#</vendorCode>
    <model>#MODEL#</model>

    <barcode>#BARCODE#</barcode>
    <description><![CDATA[#DESCRIPTION#]]></description>
    #PARAMS#</offer>' . PHP_EOL;

$params_template = '<param name="#PARAM_NAME#" #PARAM_UNIT#>#PARAM_VALUE#</param>' . PHP_EOL;

$images_template = '<picture>#IMG_URL#</picture>' . PHP_EOL;

//Использовать наименьшую цену от количества
$useRecomendedPricesDefault = true;

//Массив товаров для которых принудительно передавать цену от рекомендуемого количества
$useRecomendedPricesProducts = array(19700, 19701, 19702, 19703, 19704, 19705, 19706, 19708, 19709, 19710, 19711, 19712, 19713, 19714, 19715);

//Выставить отсутствие для товаров
$selectively_reset_catalog_quantity = false;

//Массив id товарных предложений, которые нужно принудительно занулить
$selectively_ended_products = array();

//UTM метки для генерации
$vk_utm = htmlspecialchars('utm_source=vk&utm_medium=cpc&utm_campaign={campaign_id}&utm_term={ad_id}&utm_content={random}');

require_once(__DIR__ . '/feeds.php');
$feeds = new feeds();

use Bitrix\Main\Application;
$request = Application::getInstance()->getContext()->getRequest();

$task = !empty(@$argv[1]) ? @$argv[1] : $request->getPost('task');

$SITE_NAME = $feeds->SITE_NAME_DEFINE;

//исключаем категории, что возжелал исключать маркетинг для конкретного фида
$CURRENT_FEED_EXLUDED_CATEGORIES = array(
//    176, // аксессуары
);

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

//добавим вымышленные категории для маркетинга и корректировки БЕЗ СОХРЕНЕНИЯ КЛЮЧЕЙ
$categoryArr = array_values($feeds->ya_extra_categories + $categoryArr + $feeds->ya_light_fix_categories);

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

$arElementId = array();

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
    array("IBLOCK_ID", "ID", "CATALOG_QUANTITY", "NAME", "DETAIL_PAGE_URL"),
    array('CODE' =>
        array(
            "FOTOGRAFIYA_5",
            "FOTOGRAFIYA_6",
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
            "FOTOGRAFIYA_3",
            "FOTOGRAFIYA_4",
            "UPAKOVKA_2_1",
            "UPAKOVKA_3_1",
            "UPAKOVKA_4_1",
            "MATERIAL_NOZHEK",
            "MATERIAL_NOZHEK_1",
            "MATERIAL_SEDLA",
            "TSVET_NOZHEK_1",
            "TSVET_SEDLA",
            "MATERIAL_NOZHEK_2",
            "TOLSHCHINA_STOLESHNITSY_1",
            "TIP_POVERKHNOSTI",
            "TIP_POVERKHNOSTI_1",
            "TSVET_STOLESHNITSY_1",
            "TSVET_NOZHEK_2",
            "DIAMETR_STOLESHNITSY",
            "STRANA_PROISKHOZHDENIYA",
            "RAZMER_SH_KH_G_KH_V_1",
            "VYSOTA_DO_SIDENYA",
            "MAKSIMALNAYA_NAGRUZKA",
            "MAKSIMALNAYA_NAGRUZKA_1",
            "VYSOTA_DO_SIDENYA_1",
            "MATERIAL_NOZHEK_3",
            "RAZMER_SH_KH_G_KH_V_2",
            "VYSOTA_PODLOKOTNIKOV",
            "VYSOTA_PODLOKOTNIKOV_1",
            "DIAMETR_STOLESHNITSY_1",
            "RAZMER_STOLESHNITSY",
            "VES",
            "VYSOTA_STOLESHNITSY",
            "VYSOTA_STOLESHNITSY_1",
            "VYSOTA_SIDENYA_2",
            "RAZMER_STOLESHNITSY_1",
            "NOZHKI",
            "GABARITY_SH_KH_G_KH_V",
            "MATERIAL",
            "CML2_LINK",
            "CML2_BAR_CODE",
            "RECOMMENDED_QUANTITY_FOR_SALE",
            "TYPEPREFIX_KATEGORIYA_DLYA_MARKETA",
            "MODEL_MODEL_KATEGORIYA_DLYA_MARKETA",
            "HIDE2VIEW",
            "ARRIVAL_DATE"
        )
    )
);

//свойств предложение которые не попадают params
$not_display_offer_props = array(
    "FOTOGRAFIYA_1",
    "FOTOGRAFIYA_2",
    "FOTOGRAFIYA_3",
    "FOTOGRAFIYA_4",
    "FOTOGRAFIYA_5",
    "FOTOGRAFIYA_6",
    "KOD_TSVETA",
    "CML2_LINK",
    "RECOMMENDED_QUANTITY_FOR_SALE",
    "ARRIVAL_DATE"
);

//Свойства в фид
$display_prop = array(
    "MATERIAL_STOLESHNITSY_1",
    "CML2_ARTICLE",
    "TIP_POVERKHNOSTI",
    "TSVET_STOLESHNITSY_1",
    "TSVET_NOZHEK_2",
    "STRANA_PROISKHOZHDENIYA",
    "MATERIAL_NOZHEK_3",
    "VES",
    "RUSSKAYA_TRANSKRIPTSIYA_2",
    "DIAMETR_STOLESHNITSY",
    "VYSOTA_STOLESHNITSY",
    "TOLSHCHINA_STOLESHNITSY",
    "MATERIAL",
    "RAZMER_SH_KH_G_KH_V_1",
    "VYSOTA_DO_SIDENYA",
    "REVIEWS_COUNT",
    "REVIEWS_AGGREGATE",
    "MATERIAL_SEDLA_1",
    "MAKSIMALNAYA_NAGRUZKA",
    "VYSOTA_PODLOKOTNIKOV_1",
    "TSVET_SEDLA",
    "RAZMER_D_KH_SH",
    "RAZMER_STOLA_SH_KH_G_KH_V",
    "RAZMER_STULA_SH_KH_G_KH_V",
    "MATERIALY_STOLA",
    "MATERIALY_STULEV",
);

if ($task == 'standard' || empty($task)) {
    $file = $feeds->generateFilename(__FILE__);
} elseif($task == 'vk') {
    $file = $feeds->generateFilename('vk.xml');
} elseif($task == 'msk') {
    $file = $feeds->generateFilename('yandex_msk.xml');
} else {
    $file = $feeds->generateFilename(__FILE__, $task);
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
            //скрытие позиций
            if(($task == 'msk') && (!empty($item['PROPERTIES']['HIDE2VIEW']['VALUE']) || !empty($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["PROPERTY_HIDE2VIEW_VALUE"]))) {
                continue;
            }

            $arElementId[] = $product_id;

            if($selectively_reset_catalog_quantity && in_array($product_id, $selectively_ended_products)){
                $item["CATALOG_QUANTITY"] = 0;
            }
            if ($item > 0) {

                for ($i = 0; $i < 6; ++$i) {
                    if (!empty($item["PROPERTIES"]['FOTOGRAFIYA_' . $i]['VALUE'])) {
                        $rsData = $strEntityDataClass::getList(array(
                            'select' => array('UF_FILE'),
                            'filter' => array('UF_XML_ID' => $item["PROPERTIES"]['FOTOGRAFIYA_' . $i]['VALUE']),
                            'limit' => '1',
                        ));
                        if ($arItem = $rsData->fetch()) {
                            if ($arItem['UF_FILE']) {
                                $source = CFile::GetFileArray($arItem['UF_FILE']);
                                $arImage = CFile::ResizeImageGet($source, Array('width' => 550, 'height' => 550), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
                                ${'ID_IMG_' . $i} = $arImage['src'];
                            }
                        }
                    }
                }

                // Если товар без картинки, то он ломает фид. Скорее всего это товары из кривой заливки из 1С
                if($item["CATALOG_QUANTITY"] < 1 && empty($item["PROPERTIES"]["ARRIVAL_DATE"]["VALUE"])){
                    for ($i = 0; $i < 6; ++$i) {
                        unset(${'ID_IMG_' . $i});
                    }
                    continue;
                }
                if (!empty($ID_IMG_1)) {

                    #@TODO вынести в настройки конкретных фидов, у разных фидов это могут быть разные настройки
                    //Замена имен для управления размещением в нужных категориях на маркете
                    $SEARCH_FOR_SOCKS = array('Носки для ножек');
                    $REPLACE_FOR_SOCKS = array('Носки для стульев');
                    $SEARCH_FOR_CHAIRS = array('Стул-кресло Eames Style DAW (', 'Стул-кресло Eames Style DAW Black (', 'Стул-кресло Eames Style DAW Brown (', 'Стул-кресло Eames Style DAW White (');
                    $REPLACE_FOR_CHAIRS = array('Кресло Eames Style DAW (', 'Кресло Eames Style DAW Black (', 'Кресло Eames Style DAW Brown (', 'Кресло Eames Style DAW White (');
                    $PRODUCT_CAT_MAPPING = array(
                        'подвесной светильник' => 177,
                        'настенный светильник' => 178,
                        'настольная лампа' => 179,
                        'торшер' => 185,
                        'подушка декоративная' => 22,
                    );
                    $PRODUCT_NAME_REPLACE_SEARCH = array_merge(array('Комплект'), $SEARCH_FOR_SOCKS, $SEARCH_FOR_CHAIRS);
                    $PRODUCT_NAME_REPLACED_BY = array_merge(array('Стол'), $REPLACE_FOR_SOCKS, $REPLACE_FOR_CHAIRS);

                    $PRODUCT['#NAME#'] = str_replace($feeds->from, $feeds->to, str_replace($feeds->ya_forbidden_strings, "", $item["NAME"]));
                    $PRODUCT['#NAME#'] = str_replace($PRODUCT_NAME_REPLACE_SEARCH, $PRODUCT_NAME_REPLACED_BY, $PRODUCT['#NAME#']);
                    $PRODUCT['#AVAILABILITY#'] = ($item["CATALOG_QUANTITY"] > 0) ? 'true' : 'false';
                    $PRODUCT["#ID#"] = $product_id;
                    $PRODUCT["#GROUP_INFO#"] = ($offers) ? 'group_id="' . $item["PROPERTIES"]["CML2_LINK"]["VALUE"] . '"' : '';
                    $PRODUCT["#SITE_URL#"] = $feeds->SITE_URL_DEFINE;
                    $PRODUCT["#LINK#"] = (substr_count($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"], $product_id) == 0) ? $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"] . $product_id . '/' : $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"];

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
                        $PRODUCT["#UTM#"] = htmlspecialchars("utm_source=yandex.market&utm_medium=cpc&utm_campaign=" . $utm_campaign . "&utm_content=" . $utm_content);
                    } elseif ($task == 'dynamic_ads') {
                        $PRODUCT["#UTM#"] = htmlspecialchars("utm_source=yandex.dynamic&utm_medium=cpc&utm_campaign=dynamic.ads");
                    } elseif ($task == 'banners_24_35') {
                        $PRODUCT["#UTM#"] = htmlspecialchars("utm_source=yandex.smart&utm_medium=cpc&utm_campaign=smart.banners.24_35.j");
                    } elseif ($task == 'banners_34_45') {
                        $PRODUCT["#UTM#"] = htmlspecialchars("utm_source=yandex.smart&utm_medium=cpc&utm_campaign=smart.banners.34_45.j");
                    } elseif ($task == 'vk') {
                        $PRODUCT["#UTM#"] = $vk_utm;
                    }

                    if ( ($useRecomendedPricesDefault) || (count($useRecomendedPricesProducts) > 0 && in_array($product_id, $useRecomendedPricesProducts))) {
                        $useRecomendedPrices = true;
                    } else {
                        $useRecomendedPrices = false;
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
                    foreach($PRODUCT_CAT_MAPPING   as $product_name => $product_category){
                        if(strpos(strtolower($PRODUCT['#NAME#']), $product_name) !== FALSE){
                            $PRODUCT['#CATEGORY_ID#'] = $product_category;
                        }
                    }

                    $PRODUCT['#DELIVERY_DAYS#'] = ($item["CATALOG_QUANTITY"] == 0) ? '32' : '1-3';

                    $PRODUCT['#VENDOR_CODE#'] = $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]['PROPS']['CML2_ARTICLE']['VALUE'];

                    if ($item["PROPERTIES"]["CML2_BAR_CODE"]["VALUE"])
                        $PRODUCT['#BARCODE#'] = $item["PROPERTIES"]["CML2_BAR_CODE"]["VALUE"];

                    if ($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_TEXT"]) {
                        $PRODUCT['#DESCRIPTION#'] = cutString(str_replace($feeds->from, $feeds->to, $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_TEXT"]), 3000);//ограничивание описания в 3000 символов
                    } else {
                        $PRODUCT['#DESCRIPTION#'] = str_replace($feeds->from, $feeds->to, strip_tags($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["NAME"]));
                    }

                    if (!empty($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["PROPS"]["TYPEPREFIX_KATEGORIYA_DLYA_MARKETA"]["VALUE"])) {
                        $PRODUCT['#TYPE_PREFIX#'] = $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["PROPS"]["TYPEPREFIX_KATEGORIYA_DLYA_MARKETA"]["VALUE"];
                    }
                    if (!empty($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["PROPS"]["MODEL_MODEL_DLYA_MARKETA"]["VALUE"])) {
                        $PRODUCT['#MODEL#'] = $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["PROPS"]["MODEL_MODEL_DLYA_MARKETA"]["VALUE"];
                        $PRODUCT['#MODEL#'] .= ' ' . $color_name;
                    }

                    $PRODUCT['#IMAGES#'] = '';

                    $newFileName = '/i/tmp/' . pathinfo($ID_IMG_1)['filename'] . "1.jpg";
                    $sourceFile = $feeds->yml_root() . $ID_IMG_1;
                    if (file_exists($sourceFile)) {
                        if (!file_exists($feeds->yml_root() . $newFileName) || date('H:i', time()) == '00:00') { //формирование зеркального изображения если его нет или в полночь
                            $feeds->makeMirrorPic($sourceFile, $feeds->yml_root() . $newFileName);
                        }
                        $PRODUCT['#IMAGES#'] =  str_replace("#IMG_URL#", $feeds->root_address() . $newFileName, $images_template);
                    } else {
                        $feeds->printInfo('WARN', 'не найдено изображение для товара "' . $key . ' ' . $PRODUCT['#NAME#'] . '" id=' . $item["ID"] . ' по пути ' . $sourceFile);
                    }

                    if(!empty($ID_IMG_2)){
                        $images_set = array();
                        for ($i = 0; $i < 6; ++$i) {
                            if (${'ID_IMG_' . $i})
                                array_push($images_set, str_replace("#IMG_URL#", $feeds->root_address() . ${'ID_IMG_' . $i}, $images_template));
                        }
                        $PRODUCT['#IMAGES#'] = $PRODUCT['#IMAGES#'] . implode('', $images_set);
                        unset($images_set);
                    }

                    foreach ($item["PROPERTIES"] as $key2 => $value2):
                        if (strlen($value2["VALUE"]) > 0 && !in_array($value2["CODE"], $not_display_offer_props)):
                            $PRODUCT['#PARAMS#'] = $PRODUCT['#PARAMS#'] . str_replace(array('#PARAM_NAME#', '#PARAM_UNIT#', '#PARAM_VALUE#'), array($value2["NAME"], '', $value2["VALUE"]), $params_template);
                        endif;
                    endforeach;
                    unset($key2, $value2);

                    if (!empty($color_name)) {
                        $PRODUCT['#PARAMS#'] = $PRODUCT['#PARAMS#'] . str_replace(array('#PARAM_NAME#', '#PARAM_UNIT#', '#PARAM_VALUE#'), array('Цвет', '', $color_name), $params_template);
                    }

                    foreach ($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]['PROPS'] as $value2) {
                        if (in_array($value2["CODE"], $display_prop) && $value2["VALUE"]) {
                            switch ($value2["CODE"]) {
                                case "RAZMER_SH_KH_G_KH_V_1":
                                    if (strpos($value2["VALUE"], "мм") !== false):
                                        $unit = "мм";
                                    elseif (strpos($value2["VALUE"], "см") !== false):
                                        $unit = "см";
                                    endif;
                                    $explode_value2 = preg_split( "/ (x|х|X|Х) /", str_replace(array(" мм", " см"), "", $value2["VALUE"]) );
                                    $unit = 'unit="' . $unit . '"';
                                    $PRODUCT['#PARAMS#'] = $PRODUCT['#PARAMS#'] . str_replace(array('#PARAM_NAME#', '#PARAM_UNIT#', '#PARAM_VALUE#'), array('ширина', $unit, $explode_value2[0]), $params_template);
                                    $PRODUCT['#PARAMS#'] = $PRODUCT['#PARAMS#'] . str_replace(array('#PARAM_NAME#', '#PARAM_UNIT#', '#PARAM_VALUE#'), array('глубина', $unit, $explode_value2[1]), $params_template);
                                    $PRODUCT['#PARAMS#'] = $PRODUCT['#PARAMS#'] . str_replace(array('#PARAM_NAME#', '#PARAM_UNIT#', '#PARAM_VALUE#'), array('высота', $unit, $explode_value2[2]), $params_template);
                                    unset($explode_value2);
                                    break;

                                case "RAZMER_STOLA_SH_KH_G_KH_V":
                                    if (strpos($value2["VALUE"], "мм") !== false):
                                        $unit = "мм";
                                    elseif (strpos($value2["VALUE"], "см") !== false):
                                        $unit = "см";
                                    endif;
                                    $explode_value2 = preg_split( "/ (x|х|X|Х) /", str_replace(array(" мм", " см"), "", $value2["VALUE"]) );
                                    $unit = 'unit="' . $unit . '"';
                                    $PRODUCT['#PARAMS#'] = $PRODUCT['#PARAMS#'] . str_replace(array('#PARAM_NAME#', '#PARAM_UNIT#', '#PARAM_VALUE#'), array('ширина стола', $unit, $explode_value2[0]), $params_template);
                                    $PRODUCT['#PARAMS#'] = $PRODUCT['#PARAMS#'] . str_replace(array('#PARAM_NAME#', '#PARAM_UNIT#', '#PARAM_VALUE#'), array('глубина стола', $unit, $explode_value2[1]), $params_template);
                                    $PRODUCT['#PARAMS#'] = $PRODUCT['#PARAMS#'] . str_replace(array('#PARAM_NAME#', '#PARAM_UNIT#', '#PARAM_VALUE#'), array('высота стола', $unit, $explode_value2[2]), $params_template);
                                    unset($explode_value2);
                                    break;

                                default:
                                    if (in_array($value2["CODE"], array("TOLSHCHINA_STOLESHNITSY", "VYSOTA_PODLOKOTNIKOV", "VYSOTA_DO_SIDENYA_1", "VYSOTA_DO_SIDENYA", "VYSOTA_PODLOKOTNIKOV_1"))) {
                                        preg_match("/([0-9]+)\s{1,}([\S]+)/", $value2["VALUE"], $match);
                                        $unit = 'unit="' . $match[2] . '"';
                                        $PRODUCT['#PARAMS#'] = $PRODUCT['#PARAMS#'] . str_replace(array('#PARAM_NAME#', '#PARAM_UNIT#', '#PARAM_VALUE#'), array($value2["NAME"], $unit, $match[1]), $params_template);
                                        unset($match);
                                    } elseif (in_array($value2["CODE"], array("MAKSIMALNAYA_NAGRUZKA", "VES"))) {
                                        $unit = 'unit="кг"';
                                        $PRODUCT['#PARAMS#'] = $PRODUCT['#PARAMS#'] . str_replace(array('#PARAM_NAME#', '#PARAM_UNIT#', '#PARAM_VALUE#'), array($value2["NAME"], $unit, str_replace(" кг", "", $value2["VALUE"]) ), $params_template);
                                    } else {
                                        $PRODUCT['#PARAMS#'] = $PRODUCT['#PARAMS#'] . str_replace(array('#PARAM_NAME#', '#PARAM_UNIT#', '#PARAM_VALUE#'), array($value2["NAME"], '', $value2["VALUE"]), $params_template);
                                    }
                                    break;
                            }
                        }
                    }
                    $PRODUCT = str_replace(array_keys($PRODUCT), array_values($PRODUCT), $items_template);
                    $items_list .= $feeds->removeEmptyBlocks($PRODUCT);
                    unset($color_name, $color_translit, $unit, $value2, $PRODUCT);
                }
                for ($i = 0; $i < 6; ++$i) {
                    unset(${'ID_IMG_' . $i});
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