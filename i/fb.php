<?
    /**
      * FB CSV feed generator
      * User s.samoylov
      * Date 25.10.2018
      * 
    */

    use Bitrix\Highloadblock\HighloadBlockTable;
    
    require_once(__DIR__ . '/feeds.php');

    // Functions

    function GetProductList () {
        $PRODUCTS = []; global $feeds;
        $MERCHANT_CATEGORIES = array(
            160 => 'Мебель > Столы > Кухонные и обеденные столы', //Столы
            161 => 'Мебель > Кресла > Кухонные и столовые стулья', //Стулья
            186 => 'Мебель > Наборы мебели > Наборы мебели для кухонь и столовых комнат', //Комплекты
        );

        //исключаем категории, что возжелал исключать маркетинг для конкретного фида
        $CURRENT_FEED_EXLUDED_CATEGORIES = array(
            212, // свет
        );
        
        // получаем категории
        $categoryList = CIBlockSection::GetList(
            array("SORT" => "ASC"), 
            array("IBLOCK_ID" => $feeds->CATALOG_IBLOCK_ID, "ACTIVE" => "Y", "!ID" => array_merge($feeds->EXCLUDED_CATEGORIES, $CURRENT_FEED_EXLUDED_CATEGORIES)),
            array("ELEMENT_SUBSECTIONS" => "Y", "CNT_ACTIVE" => "Y"),
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
        
        // Получаем товары
        $CIBlockElement = CIBlockElement::GetList(
            $arOrder = array("SORT" => "ASC"),
            $arFilter = array("IBLOCK_ID" => $feeds->CATALOG_IBLOCK_ID, "ACTIVE" => "Y", "SECTION_ID" => $cat_id),
            $arGroupBy = false,
            $arNavStartParams = false,
            $arSelectFields = array("NAME", "IBLOCK_SECTION_ID", "CODE", "IBLOCK_ID", "ID", "DETAIL_TEXT", "DETAIL_PAGE_URL")
        );
        
        while ($ob = $CIBlockElement->GetNextElement()) {
            $ar_result = $ob->GetFields();
            $ar_props = $ob->GetProperties();
            $tovars[$ar_result['ID']] = $ar_result;
            $tovars[$ar_result['ID']]['PROPS'] = $ar_props;
            $tovars_id[] = $ar_result['ID'];
        }
        
        // Получаем офферы
        $predl = CCatalogSKU::getOffersList(
            $tovars_id, '',
            array("ACTIVE" => "Y"),
            array("IBLOCK_ID", "ID", "CATALOG_QUANTITY", "NAME", "DETAIL_PAGE_URL"),
            array('CODE' => array('FOTOGRAFIYA_1', 'FOTOGRAFIYA_2', 'CML2_LINK', 'RECOMMENDED_QUANTITY_FOR_SALE', 'MATERIAL', 'VES'))
        );
        
        $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
        $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();
        
        foreach ($predl as $key => $values) {
            foreach ($values as $product_id => $item) {
                if ($item > 0) {
                    
                    // Decription
                    if ($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_TEXT"]) {
                        $DESCRIPTION = cutString(str_replace($feeds->from, $feeds->to, strip_tags($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["~DETAIL_TEXT"])),5000); //ограничим описания соглавно требованиям
                    } else {
                        $DESCRIPTION = str_replace($feeds->from, $feeds->to, strip_tags($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["NAME"]));
                    }
                    
                    // LINK
                    $LINK = (substr_count($tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"], $product_id) == 0) ? $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"] . $product_id . '/' : $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"];
                    
                    // Image link
                    $IMAGE_LINK = empty($item["PROPERTIES"]["FOTOGRAFIYA_2"]["VALUE"]) ? $item["PROPERTIES"]["FOTOGRAFIYA_1"]["VALUE"] : $item["PROPERTIES"]["FOTOGRAFIYA_2"]["VALUE"];
                    $rsData = $strEntityDataClass::getList(array(
                        'select' => array('UF_FILE'),
                        'filter' => array('UF_XML_ID' => $IMAGE_LINK),
                        'limit' => '1',
                    ));
                    if ($arItem = $rsData->fetch()) {
                        if ($arItem['UF_FILE']) {
                            $source = CFile::GetFileArray($arItem['UF_FILE']);
                            $arImage = CFile::ResizeImageGet($source, Array('width' => 550, 'height' => 550), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
                            $IMAGE_LINK = $arImage['src'];
                        }
                    }
                    
                    // MARKET CATEGORY
                    $group = CIBlockElement::GetElementGroups($key, true)->Fetch();
                    $nav = CIBlockSection::GetNavChain(false, $group['ID']);
                    $MARKET_CATEGORY = $MERCHANT_CATEGORIES[$nav->ExtractFields("nav_")['ID']];
                    
                    // MIN PRICE
                    $arOptimalPrice = CCatalogProduct::GetOptimalPrice($product_id, $feeds->getProductCount($item["PROPERTIES"]["RECOMMENDED_QUANTITY_FOR_SALE"]["VALUE"], true));
                    $PRICE = number_format($arOptimalPrice['MAX_PRICE'], 2, '.', '');
                    $SALE_PRICE = number_format($arOptimalPrice['DISCOUNT_PRICE'], 2, '.', '');
                    
                    // CATEGORY ID
                    $CATEGORY_ID = $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["IBLOCK_SECTION_ID"];
                    
                    // MATERIAL
                    $MATERIAL = $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]['PROPS']['MATERIAL']['VALUE'];
                    
                    // VES
                    $WEIGHT = $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]['PROPS']['VES']['VALUE'];
                    
                    // SIZE
                    $SIZE = $tovars[$item['PROPERTIES']['CML2_LINK']['VALUE']]['PROPS']['RAZMER_SH_KH_G_KH_V']['VALUE'];
                    
                    $PRODUCTS[] = array(
                        'ID' => $product_id,
                        'TITLE' => $item['NAME'],
                        'DESCRIPTION' => $DESCRIPTION,
                        'SITE_URL' => $feeds->SITE_URL_DEFINE,
                        'LINK' => $feeds->SITE_URL_DEFINE . $LINK,
                        'IMAGE_LINK' => $feeds->SITE_URL_DEFINE . $IMAGE_LINK,
                        'AVAILABILITY' => ($item["CATALOG_QUANTITY"] > 0) ? "in stock" : "out of stock",
                        'MARKET_CATEGORY' => $MARKET_CATEGORY,
                        'PRICE' => $PRICE,
                        'SALE_PRICE' => $SALE_PRICE,
                        'CONDITION' => 'new',
                        'BRAND' => 'dsklad.ru',
                        'ADDITIONAL_IMAGE_LINK' => '',
                        'AGE_GROUP' => '',
                        'COLOR' => '',
                        'GENDER' => '',
                        'ITEM_GROUP_ID' => $CATEGORY_ID,
                        'GOOGLE_PRODUCT_CATEGORY' => $MARKET_CATEGORY,
                        'MATERIAL' => $MATERIAL,
                        'PATTERN' => '',
                        'PRODUCT_TYPE' => '',
                        'SALE_PRICE_EFFECTIVE_DATE' => '',
                        'SHIPPING' => '',
                        'SHIPPING_WEIGHT' => str_replace(',', '.', $WEIGHT),
                        'SIZE' => $SIZE,
                    );
                }
            }
        }
        
        // var_dump($PRODUCTS);die();
        
        return $PRODUCTS;
    }
    
    function FormatCsvData ($array = [], $csvFieldDelimitter = ",") {
        if (empty($array)) return [];
        
        $fieldvaluelines = [];
        $fieldcodeline = "id" . $csvFieldDelimitter 
            . "title" . $csvFieldDelimitter
            . "description" . $csvFieldDelimitter
            . "availability" . $csvFieldDelimitter
            . "condition" . $csvFieldDelimitter
            . "price" . $csvFieldDelimitter
            . "link" . $csvFieldDelimitter
            . "image_link" . $csvFieldDelimitter
            . "brand" . $csvFieldDelimitter
            . "additional_image_link" . $csvFieldDelimitter
            . "age_group" . $csvFieldDelimitter
            . "color" . $csvFieldDelimitter
            . "gender" . $csvFieldDelimitter
            . "item_group_id" . $csvFieldDelimitter
            . "google_product_category" . $csvFieldDelimitter
            . "material" . $csvFieldDelimitter
            . "pattern" . $csvFieldDelimitter
            . "product_type" . $csvFieldDelimitter
            . "sale_price" . $csvFieldDelimitter
            . "sale_price_effective_date" . $csvFieldDelimitter
            . "shipping" . $csvFieldDelimitter
            . "shipping_weight" . $csvFieldDelimitter
            . "size" 
            // . "custom_label_0" . $csvFieldDelimitter
            // . "custom_label_1" . $csvFieldDelimitter
            // . "custom_label_2" . $csvFieldDelimitter
            // . "custom_label_3" . $csvFieldDelimitter
            // . "custom_label_4" . $csvFieldDelimitter
        ;
        
        foreach ($array as $key => $product) 
            $fieldvaluelines[] = $product['ID'] . $csvFieldDelimitter 
                . ($product['TITLE'] ? : "") . $csvFieldDelimitter
                . '"' .($product['DESCRIPTION'] ? : "") . '"' . $csvFieldDelimitter
                . ($product['AVAILABILITY'] ? : "") . $csvFieldDelimitter
                . ($product['CONDITION'] ? : "") . $csvFieldDelimitter // CONDITION
                . ($product['PRICE'] ? : "") . $csvFieldDelimitter
                . ($product['LINK'] ? : "") . $csvFieldDelimitter
                . ($product['IMAGE_LINK'] ? : "") . $csvFieldDelimitter
                . ($product['BRAND'] ? : "") . $csvFieldDelimitter // BRAND
                . ($product['ADDITIONAL_IMAGE_LINK'] ? : "") . $csvFieldDelimitter // ADDITIONAL IMAGE LINK
                . ($product['AGE_GROUP'] ? : "") . $csvFieldDelimitter // AGE GROUP
                . ($product['COLOR'] ? : "") . $csvFieldDelimitter // COLOR
                . ($product['GENDER'] ? : "") . $csvFieldDelimitter // GENDER
                . ($product['ITEM_GROUP_ID'] ? : "") . $csvFieldDelimitter // ITEM_GROUP_ID
                . ($product['GOOGLE_PRODUCT_CATEGORY'] ? : "") . $csvFieldDelimitter // GOOGLE PRODUCT CATEGORY
                . ($product['MATERIAL'] ? : "") . $csvFieldDelimitter
                . ($product['PATTERN'] ? : "") . $csvFieldDelimitter
                . ($product['PRODUCT_TYPE'] ? : "") . $csvFieldDelimitter
                . ($product['SALE_PRICE'] ? : "") . $csvFieldDelimitter
                . ($product['SALE_PRICE_EFFECTIVE_DATE'] ? : "") . $csvFieldDelimitter
                . ($product['SHIPPING'] ? : "") . $csvFieldDelimitter
                . ($product['SHIPPING_WEIGHT'] ? : "") . $csvFieldDelimitter
                . ($product['SIZE'] ? : "")
            ;
        
        return [$fieldcodeline] + $fieldvaluelines;
    }
    
    function FWriteToFileCsv ($array = [], $filePath = "", $fileName = "", $csvFieldDelimitter = ",") {
        if (empty($array) || empty($filePath) || empty($fileName)) return false;
        $fp = fopen($filePath . $fileName, 'w');
        foreach ($array as $line)
            fwrite($fp, $line . "\r\n");
        fclose($fp);
    }

    $filePath = __DIR__ . "/";
    $fileName = "fb.csv";
    $csvFieldDelimitter = ",";
    $arProducts = array();
    
    $feeds = new feeds();
    $task = @$argv[1];
    $feeds->printInfo('START');
    
    $arProducts = GetProductList();
    $CSVDATA = FormatCsvData($arProducts, $csvFieldDelimitter);
    // var_dump($CSVDATA);die();
    FWriteToFileCsv($CSVDATA, $filePath, $fileName);
        
    $feeds->printInfo('END');
    $feeds->file_name = "fb.csv";
    $feeds->printLink();
?>