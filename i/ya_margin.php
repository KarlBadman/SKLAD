<?
/**
 * created by
 * User: a.kobetskoy
 * Date: 11.06.2019
 */
use Bitrix\Highloadblock\HighloadBlockTable;

require_once(__DIR__ . '/feeds.php');
$feeds = new feeds();

$feeds->printInfo('START');
$categoryArr = array();
$tovars = array();
$fieldvaluelines = array();

#@TODO move to template settings
$csvFieldDelimitter = ",";
$fieldcodeline = array(
    "Код товара (с сайта)" . $csvFieldDelimitter
    . "Название товара" . $csvFieldDelimitter
    . "Бренд" . $csvFieldDelimitter
    . "Категория" . $csvFieldDelimitter
    . "Цена" . $csvFieldDelimitter
    . "Маржинальность" . $csvFieldDelimitter
);
//Исключения, по которым не надо слать уведомления об отсутствии себестоимости. Могут быть как ID товарных предложений, так и названия категорий (как в маркете!)
$stopSendMessagesList = array(
    'CATEGORIES' => array(
        'Декор',
    ),
    'PRODUCTS' => array(
    )
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

//добавим вымышленные категории для маркетинга и корректировки С СОХРАНЕНИЕМ КЛЮЧЕЙ, для дальнейшего обращения
$categoryArr = ($feeds->ya_extra_categories + $categoryArr + $feeds->ya_light_fix_categories);

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
    array("IBLOCK_ID", "ID", "CATALOG_QUANTITY", "NAME"),
    array('CODE' =>
        array(
            "KOD_TSVETA",
            "CML2_ATTRIBUTES",
            "CML2_LINK"
        )
    )
);

//Свойства в фид
$display_prop = array(
    "CML2_ARTICLE",
);

$file = $feeds->generateFilename(__FILE__, '', 'csv');

if (is_writable($file)) {
     foreach ($categoryArr as $value) {
        if ($value["name"] == 'Комплекты') {
            $value["name"] = 'Столы и стулья';
        }
         $value["name"];
    }
    $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
    $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
    $strEntityDataClass = $obEntity->getDataClass();

    foreach ($predl as $values) {
        $offers = (count($values) > 1) ? true : false;

        foreach ($values as $product_id => $item) {
            $arElementId[] = $product_id;
            if($selectively_reset_catalog_quantity && in_array($product_id, $selectively_ended_products)){
                $item["CATALOG_QUANTITY"] = 0;
            }
            if ($item > 0) {
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

                    $PRODUCT['NAME'] = str_replace($feeds->from, $feeds->to, str_replace($feeds->ya_forbidden_strings, "", $item["NAME"]));
                    $PRODUCT['NAME'] = str_replace($PRODUCT_NAME_REPLACE_SEARCH, $PRODUCT_NAME_REPLACED_BY, $PRODUCT['NAME']);

                    $PRODUCT['CATEGORY_ID'] = $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]["IBLOCK_SECTION_ID"];
                    foreach($REPLACE_FOR_SOCKS as $single_word) {
                        if(strpos($PRODUCT['NAME'], $single_word) !== FALSE) {
                            $PRODUCT['CATEGORY_ID'] = $categoryArr[array_search('Фурнитура для мебели', array_column($categoryArr, 'name'))]['id'];
                        }
                    }
                    foreach($REPLACE_FOR_CHAIRS as $single_word) {
                        if(strpos($PRODUCT['NAME'], $single_word) !== FALSE) {
                            $some = array_values($categoryArr);
                            $PRODUCT['CATEGORY_ID'] = $some[array_search('Кресла', array_column($some, 'name'))]['id'];
                        }
                    }
                    foreach($PRODUCT_CAT_MAPPING   as $product_name => $product_category){
                        if(strpos(strtolower($PRODUCT['NAME']), $product_name) !== FALSE){
                            $PRODUCT['CATEGORY_ID'] = $product_category;
                        }
                    }

                    $PRODUCT['CATEGORY_NAME'] = $categoryArr[$PRODUCT['CATEGORY_ID']]['name'];
                    $PRODUCT['CODE'] = $tovars[$item["PROPERTIES"]["CML2_LINK"]["VALUE"]]['PROPS']['CML2_ARTICLE']['VALUE'];

                    //игнор, если не заполнена себестоимость, или валюта себестоимости
                    if(empty($item['CATALOG_PURCHASING_PRICE']) || empty($item['CATALOG_PURCHASING_CURRENCY'])){
                        //Соберем проблемные в письмо, чтобы отправить эти исключения, если только они не в игноре в настройках
                        if(!in_array($item['ID'], $stopSendMessagesList['PRODUCTS']) && !in_array($PRODUCT['CATEGORY_NAME'], $stopSendMessagesList['CATEGORIES'])){
                            $mail_body[] = '<tr><td>' . $item['ID'] . '</td><td>' . $PRODUCT['CODE'] . '</td><td>' . $item['NAME'] . '</td></tr>';
                        }
                        continue;
                    }

                    $arOptimalPrice = CCatalogProduct::GetOptimalPrice($product_id, $feeds->getProductCount($item["PROPERTIES"]["RECOMMENDED_QUANTITY_FOR_SALE"]["VALUE"], $useRecomendedPrices)); //вытягиваем цены по заданному кол-ву
                    $PRODUCT['PRICE'] = number_format($arOptimalPrice["DISCOUNT_PRICE"], 2, '.', '');
                    $PRODUCT['NAME'] = str_replace(',', '', $PRODUCT['NAME']);
                    $PRODUCT['BRAND'] = 'Дизайн Склад';
                    $PRODUCT['MARGIN'] = $feeds->marginCalculation($PRODUCT['PRICE'], $item['CATALOG_PURCHASING_PRICE'], $item['CATALOG_PURCHASING_CURRENCY']);
                    $PRODUCT['MARGIN'] = number_format($PRODUCT['MARGIN'], 2, '.', '');

                    $fieldvaluelines[] = $PRODUCT['CODE'] . $csvFieldDelimitter
                        . $PRODUCT['NAME'] . $csvFieldDelimitter
                        . $PRODUCT['BRAND'] . $csvFieldDelimitter
                        . $PRODUCT['CATEGORY_NAME'] . $csvFieldDelimitter
                        . $PRODUCT['PRICE'] . $csvFieldDelimitter
                        . $PRODUCT['MARGIN'] . $csvFieldDelimitter;
            }
        }
    }

    function sendEmptyCostList($mail_body) {
        $output = '<style>
        table {
          font-family: arial, sans-serif;
          border-collapse: collapse;
          width: 100%;
        }
        
        td, th {
          border: 1px solid #000;
          text-align: left;
          padding: 8px;
        }
        
        tr:nth-child(even) {
          background-color: #dddddd;
        }</style>';
        $output  .='<table><th>ID товара</th><th>Артикул товара</th><th>Наименование товара</th>';
        $output .= implode('', $mail_body);
        $output .= "</table>";

        $transport = new Swift_SmtpTransport('localhost', 25);
        $mailer = new Swift_Mailer($transport);
        $body = 'Не указана себестоимость или её валюта для ряда товаров. Проверьте заполненность соответствующих полей у товарных предложений';
        $receivers = array(
            's.belov@ooott.ru',
            'dev@ooott.ru',
        );
        $message = (new Swift_Message('Обнаружены товары без указания себестоимости'))
            ->setFrom(['info@dsklad.ru' => 'info@dsklad.ru'])
            ->setTo($receivers)
            ->setBody($output, 'text/html');
        $result = $mailer->send($message);
    }

    function FWriteToFileCsv ($array = [], $file, $csvFieldDelimitter = ",") {
        if (empty($array) || empty($file)) return 'error';
        $fp = fopen($file, 'w');
        foreach ($array as $line)
            fwrite($fp, $line . "\r\n");
        fclose($fp);
        return 'success';
    }

    if(count($mail_body) > 0) {
        sendEmptyCostList($mail_body);
    }

    $CSVDATA = array_merge($fieldcodeline, $fieldvaluelines);
    if(count($CSVDATA)>1) {
        echo FWriteToFileCsv($CSVDATA, $file);
    } else {
        echo "Nothing to do";
    }
}
unset($tovars, $predl, $product_id, $values, $item, $categoryArr);
$feeds->printInfo('END');
$feeds->printLink();