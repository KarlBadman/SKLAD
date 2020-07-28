<?
/**
 * RetailRocket feed
 */
    use Bitrix\Highloadblock\HighloadBlockTable;

    ini_set("memory_limit", '1G');

    require_once(__DIR__ . '/feeds.php');
    $feeds = new feeds();
    $file = $feeds->generateFilename(__FILE__);
    
    $mainTemplate = '<?xml version="1.0" encoding="UTF-8"?><yml_catalog date="#TIMESTAMP#"><shop>#CATEGORIES##OFFERS#</shop></yml_catalog>';
    $categoriesTemplate = '<categories>#CAT#</categories>';
    $offersTemplate = '<offers>#OFFER#</offers>';
    
    $feeds->printInfo('START');
    $categoryArr = [];
    $tovars = [];
    $CURRENT_FEED_EXLUDED_CATEGORIES = [];
    $CATEGORY = '';
    $OFFER = '';
        
    $categoryList = CIBlockSection::GetList(
        ["SORT" => "ASC"],
        ["IBLOCK_ID" => $feeds->CATALOG_IBLOCK_ID, "ACTIVE" => "Y", "!ID" => array_merge($feeds->EXCLUDED_CATEGORIES, $CURRENT_FEED_EXLUDED_CATEGORIES),'UF_NO_MENU'=>false],
        ["ELEMENT_SUBSECTIONS" => "Y", "CNT_ACTIVE" => "Y"], [], false
    );
    
    while ($ar_result = $categoryList->GetNext()) {
        if ($ar_result["ELEMENT_CNT"] > 0) {
            $categoryArr[$ar_result["ID"]] = [
                "id" => $ar_result["ID"],
                "parent_id" => $ar_result["IBLOCK_SECTION_ID"] ? $ar_result["IBLOCK_SECTION_ID"] : 0,
                "name" => $ar_result["NAME"],
                "count" => 0
            ];
            $cat_id[] = $ar_result["ID"];
        }
    }

    $CIBlockElement = CIBlockElement::GetList(
        $arOrder = ["SORT" => "ASC"],
        $arFilter = ["IBLOCK_ID" => $feeds->CATALOG_IBLOCK_ID, "ACTIVE" => "Y", "SECTION_ID" => $cat_id],
        $arGroupBy = false,
        $arNavStartParams = false,
        $arSelectFields = ["NAME", "IBLOCK_SECTION_ID", "CODE", "IBLOCK_ID", "ID", "DETAIL_TEXT", "DETAIL_PAGE_URL", "PROPERTY_HIDE2VIEW", "PROPERTY_SALE", "PROPERTY_NEW", "PROPERTY_HIT",'PROPERTY_MINIMUM_PRICE']
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
        ["ACTIVE" => "Y",
            'LOGIC'=> 'AND',
            ['LOGIC'=> 'OR',
            ">CATALOG_QUANTITY"=>0,
            '!PROPERTY_ARRIVAL_DATE'=>false
            ]
        ],
        ["IBLOCK_ID", "ID", "CATALOG_QUANTITY", "NAME", "DETAIL_PAGE_URL"],
        ['CODE' => ['FOTOGRAFIYA_1', 'FOTOGRAFIYA_2', 'CML2_LINK', 'RECOMMENDED_QUANTITY_FOR_SALE', "KOD_TSVETA",'ARRIVAL_DATE']]
    );

    if (is_writable($file)) {
        $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
        $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();

        foreach ($categoryArr as $cat) {
            if($cat['parent_id'] > 0){
                $CATEGORY .= '<category id="'.$cat['id'].'" parentId="'.$cat['parent_id'].'">'.$cat['name'].'</category>';
            }else{
                $CATEGORY .= '<category id="'.$cat['id'].'">'.$cat['name'].'</category>';
            }
        }

        foreach ($predl as $key => $values) {
            foreach ($values as $product_id => $item) {
                if($item["CATALOG_QUANTITY"] < 1 && empty($item["PROPERTIES"]["ARRIVAL_DATE"]["VALUE"])){
                    unset($predl[$key][$product_id]);
                    continue;
                }
                $PRODUCT['#IMAGE_LINK#'] = empty($item["PROPERTIES"]["FOTOGRAFIYA_1"]["VALUE"]) ? $item["PROPERTIES"]["FOTOGRAFIYA_2"]["VALUE"] : $item["PROPERTIES"]["FOTOGRAFIYA_1"]["VALUE"];
                $rsData = $strEntityDataClass::getList(array(
                    'select' => array('UF_FILE'),
                    'filter' => array('UF_XML_ID' => $PRODUCT['#IMAGE_LINK#']),
                    'limit' => '1',
                ));
                if ($arItem = $rsData->fetch()) {
                    if ($arItem['UF_FILE']) {
                        $source = CFile::GetFileArray($arItem['UF_FILE']);
                        $arImage = CFile::ResizeImageGet($source, Array('width' => 400, 'height' => 400), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
                        $PRODUCT['#IMAGE_LINK#'] = $arImage['src'];
                    }
                }

                $predl[$key][$product_id]['#IMAGE_LINK#'] = $PRODUCT['#IMAGE_LINK#'];
            }
        }

        foreach ($predl as $key => $values) {
            foreach ($values as $product_id => $item) {
                $EXTRABAGE = '';
                $variations = '';
                $imagesOffers = '';
                $notAvaliable = '';
                $notAvaliableAr =[];
                if(count($values[$product_id]) > 1){
                    $variations = '<param name="avaliable_variations">'.implode(',',array_keys($values)).'</param>';
                    foreach ($values as $keyValues => $val){
                        if($product_id != $val['ID'])
                            $imagesOffers .=  '<param name="url_'.$val['ID'].'">https://www.dsklad.ru'.$val['#IMAGE_LINK#'].'</param>';
                            if($val['CATALOG_QUANTITY'] < 1) $notAvaliableAr[] = $val['ID'];
                    }
                    if(!empty($notAvaliableAr)) $notAvaliable = '<param name="not_avaliable">'.implode(',',$notAvaliableAr).'</param>';
                }
                $item['URL'] = (substr_count($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"], $product_id) == 0) ? $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"] . $product_id . '/' : $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"];
                $arOptimalPrice = CCatalogProduct::GetOptimalPrice($product_id, $feeds->getProductCount($item["PROPERTIES"]["RECOMMENDED_QUANTITY_FOR_SALE"]["VALUE"], true));

                $dbPrice = CPrice::GetListEx(array(), array('PRODUCT_ID' => $product_id, 'CATALOG_GROUP_ID' => 2), false, false, array());

                $priceForOneItem = '';
                while ($arFields = $dbPrice->GetNext()) {
                    if (!empty($priceForOneItem) && $priceForOneItem > $arFields['PRICE'] && empty($item['QUANTITY_FROM'])) {
                        $item['QUANTITY_FROM'] = $arFields['QUANTITY_FROM'];
                    } elseif(empty($priceForOneItem)) {
                        $priceForOneItem = $arFields['PRICE'];
                    }
                }

                if ($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_TEXT"]) {
                    $PRODUCT['#DESCRIPTION#'] = cutString(str_replace($feeds->from, $feeds->to, strip_tags($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["~DETAIL_TEXT"])),5000); //ограничим описания соглавно требованиям
                } else {
                    $PRODUCT['#DESCRIPTION#'] = str_replace($feeds->from, $feeds->to, strip_tags($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["NAME"]));
                }
                
                if ($item["PROPERTIES"]["KOD_TSVETA"]["VALUE"]) {
                    $explode_value1_code = explode("#", $item["PROPERTIES"]["KOD_TSVETA"]["VALUE"]);
                    if ($explode_value1_code[0]) {
                        $color_name = trim($explode_value1_code[0]);
                    }
                }

                if (intVal($item['CATALOG_QUANTITY']) <= 0) {
                    $EXTRABAGE = '<param name="preorder">true</param>';
                } elseif ($item['QUANTITY_FROM']) {
                    $EXTRABAGE = '<param name="promo_price">Дешевле от ' . $item['QUANTITY_FROM'] . ' шт.</param>';
                } elseif(!empty($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["PROPERTY_NEW_VALUE"])) {
                    $EXTRABAGE = '<param name="new">true</param>';
                } elseif(!empty($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["PROPERTY_HIT_VALUE"])) {
                    $EXTRABAGE = '<param name="hit">true</param>';
                } elseif(!empty($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["PROPERTY_SALE_VALUE"])) {
                    $EXTRABAGE = '<param name="Sale">true</param>';
                }

                $OFFER .= '<offer id="' . $item['ID'] . '" available="true" group_id="' . $item["PROPERTIES"]["CML2_LINK"]["VALUE"] . '">
                    <url>https://www.dsklad.ru'.$item['URL'].'</url>
                    <price>'.number_format($arOptimalPrice['DISCOUNT_PRICE'], 2, '.', '').'</price>
                    <oldprice>'.number_format($arOptimalPrice['MAX_PRICE'], 2, '.', '').'</oldprice>
                    <categoryId>'.$tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]['IBLOCK_SECTION_ID'].'</categoryId>
                    <picture>https://www.dsklad.ru'.$item['#IMAGE_LINK#'].'</picture>
                    <name>'.str_replace($feeds->from, $feeds->to, $item["NAME"]).'</name>
                    <param name="Цвет">'.$color_name.'</param>
                    <description>'.$PRODUCT['#DESCRIPTION#'].'</description>
                    <vendor>dsklad.ru</vendor>
                    <model>'.str_replace($feeds->from, $feeds->to, $item["NAME"]).'</model>
                    <param name="Article">'.$tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]['PROPS']['CML2_ARTICLE']['VALUE'].'</param>
                    '.$EXTRABAGE.'
                    '.$variations.'
                    '.$notAvaliable.'
                    '.$imagesOffers.'
                    <param name="min_price">'.$tovars[$key]['PROPERTY_MINIMUM_PRICE_VALUE'].'</param>
                    <stock id="4">
                      <available>true</available>
                      <price>'.number_format($arOptimalPrice['DISCOUNT_PRICE'], 2, '.', '').'</price>
                      <oldprice>'.number_format($arOptimalPrice['MAX_PRICE'], 2, '.', '').'</oldprice>
                      <url>https://www.dsklad.ru'.$item['URL'].'</url>
                      <picture>https://www.dsklad.ru'.$item['#IMAGE_LINK#'].'</picture>
                    </stock>
                  </offer>';
            }
        }

        $categoriesTemplate = str_replace('#CAT#', $CATEGORY, $categoriesTemplate);
        $offersTemplate = str_replace('#OFFER#', $OFFER, $offersTemplate);
        $mainTemplate = str_replace('#CATEGORIES#', $categoriesTemplate, $mainTemplate);
        $mainTemplate = str_replace('#OFFERS#', $offersTemplate, $mainTemplate);
        $mainTemplate = str_replace('#TIMESTAMP#', date("Y-m-d H:i"), $mainTemplate);

        $fp = fopen($file, "w");
        fwrite($fp, $mainTemplate);
        fclose($fp);
    }
    
    $feeds->printInfo('END');
    $feeds->printLink();
?>