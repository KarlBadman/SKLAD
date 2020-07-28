<?
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www"; // Master

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
?>
<?
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
set_time_limit(0);
$SITE_NAME = SITE_NAME_DEFINE;
$time_now = date("Y-m-d H:i");
//$time_short = date("d.m.Y");

print '-------------------[ START ]-------------------'.PHP_EOL;
printf('Time start:'.date("Y-m-d H:i").PHP_EOL);


// получаем категории
$categoryList = CIBlockSection::GetList(
    array("SORT" => "ASC"),
    array(
        "IBLOCK_ID" => CATALOG_IBLOCK_ID,
        "ACTIVE" => "Y",
        "!ID" => array("180", "181", "157", "186", "185")
        //из каталога фильтр
    ),
    array("ELEMENT_SUBSECTIONS" => "Y", "CNT_ACTIVE" => "Y"),
    array(),
    false
);

$categoryArr = array();

while($ar_result = $categoryList->GetNext())
{
    if( $ar_result["ELEMENT_CNT"] > 0 ) {
        $categoryArr[$ar_result["ID"]] = array(
            "id" => $ar_result["ID"],
            "parent_id" => $ar_result["IBLOCK_SECTION_ID"]?$ar_result["IBLOCK_SECTION_ID"]:0,
            "parent_text" => $ar_result["IBLOCK_SECTION_ID"]?"parentId=\"".$ar_result["IBLOCK_SECTION_ID"]."\"":"",
            "name" => $ar_result["NAME"],
            "count" => 0
        );
        $cat_id[] = $ar_result["ID"];
    }
}

$CIBlockElement = CIBlockElement::GetList(
    $arOrder = Array("SORT"=>"ASC"),
    $arFilter = Array("IBLOCK_ID" =>CATALOG_IBLOCK_ID,"ACTIVE" => "Y","SECTION_ID" => $cat_id),
    $arGroupBy = false,
    $arNavStartParams = false,
    $arSelectFields = Array(
        "NAME",
        "IBLOCK_SECTION_ID",
        "CODE",
        "IBLOCK_ID",
        "ID",
        "DETAIL_TEXT",
        "DETAIL_PICTURE",
        "DETAIL_PAGE_URL",
    )
);

$tovars = array();
while($ob = $CIBlockElement->GetNextElement()){

    $ar_result = $ob->GetFields();
    $ar_props = $ob->GetProperties();

    $tovars[$ar_result['ID']] = $ar_result;
    $tovars[$ar_result['ID']]['PROPS'] = $ar_props;
    $tovars_id[] = $ar_result['ID'];

}

$predl = CCatalogSKU::getOffersList(
    $tovars_id,
    '',
    array("AVAILABLE" => "Y", "ACTIVE" => "Y"),
    array("IBLOCK_ID", "ID", "CATALOG_QUANTITY", "CATALOG_PRICE_".PRICE_ID, "CATALOG_GROUP_".PRICE_ID, "NAME", "DETAIL_PAGE_URL","CATALOG_STORE_AMOUNT_".PRICE_ID),
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
			"ARRIVAL_DATE",
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
    "CML2_LINK"
);
//дублирование свойств?
$not_display_prop = array(
    "MIN_CHECK",
    "MIN_QTY",
    "MIN_PRICE",
    "STATUS_ZAKAZA",
    "FOTOGRAFIYA_5",
    "FOTOGRAFIYA_6",
    "FOTOGRAFIYA_1",
    "CML2_BAR_CODE",
    "CML2_TRAITS",
    "CML2_BASE_UNIT",
    "CML2_TAXES",
    "MORE_PHOTO",
    "CML2_FILES",
    "KOD_TSVETA",
    "RECOMM",
    "HIT",
    "NEW",
    "SALE",
    "WITH_THIS",
    "RELATED",
    "INTERIOR",
    "ARRIVAL_DATE",
    "RUSSKAYA_TRANSKRIPTSIYA",
    "FOTOGRAFII_V_INTERERE",
    "UPAKOVKA_1",
    "UPAKOVKA_1_2",
    "UPAKOVKA_2",
    "DEAKTIVIROVAT_NA_SAYTE",
    "ID",
    "UPAKOVKA_3",
    "DEAKTIVIROVAT_NA_SAYTE_1",
    "SITE",
    "UPAKOVKA_4",
    "UPAKOVKA_1_3",
    "RASPRODAZHA",
    "IDPOLZOVATELYA",
    "UPAKOVKA_5",
    "KHIT_PRODAZH",
    "UPAKOVKA_6",
    "REKOMENDUEM",
    "UPAKOVKA_5_1",
    "UPAKOVKA_6_1",
    "DATA_POLUCHENIYA_PODTVERZHDENIYA_EDO",
    "FOTOGRAFIYA_2",
    "FOTOGRAFIYA_3",
    "FOTOGRAFIYA_4",
    "UPAKOVKA_2_1",
    "UPAKOVKA_3_1",
    "UPAKOVKA_4_1",
    "MATERIAL_NOZHEK_1",
    "MATERIAL_NOZHEK_2",
    "TIP_POVERKHNOSTI_1",
    "TOLSHCHINA_STOLESHNITSY_1",
    "TSVET_STOLESHNITSY",
    "TSVET_NOZHEK",
    "RAZMER_SH_KH_G_KH_V",
    "MATERIAL_STOLESHNITSY",
    "CML2_ATTRIBUTES",
    "FILES",
    "UPAKOVKA_1_1",
    "D_AND_M",
    "OTHER_CONFIG",
    "RAZMER_SH_KH_G_KH_V_2",
    "VYSOTA_DO_SIDENYA_1",//не пустое val
    "MAKSIMALNAYA_NAGRUZKA_1",//не пустое val
);
$from = array('"', '&', '>', '<', '\'');
$to = array('&quot;', '&amp;', '&gt;', '&lt;', '&apos;');

$file = $_SERVER["DOCUMENT_ROOT"]."/feeds/yandex.xml";
$fp = fopen($file, "w");

if (is_writable($file)) {

    fwrite($fp, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
    fwrite($fp, "<yml_catalog date=\"".$time_now."\">\n");
    //shop -start
    fwrite($fp, "<shop>\n");

    //name
    fwrite($fp, "<name>Дизайн склад</name>\n");

    //company
    fwrite($fp, "<company>".str_replace($from, $to, SITE_NAME_DEFINE)."</company>\n");
    fwrite($fp, "<url>".SITE_URL_DEFINE."</url>\n");

    //currencies -start
    fwrite($fp, "<currencies>\n");
    fwrite($fp, "<currency id=\"RUR\" rate=\"1\"/>\n");
    fwrite($fp, "</currencies>\n");
    //currencies -end

    //currencies -start
    fwrite($fp, "<categories>\n");
    foreach ($categoryArr as $key => $value):
        fwrite($fp, "<category id=\"".$value["id"]."\" ".$value["parent_text"].">".$value["name"]."</category>\n");
    endforeach;
    fwrite($fp, "</categories>\n");
    //currencies -end

    fwrite($fp, "<delivery-options><option cost=\"900\" days=\"1-3\" order-before=\"\"/></delivery-options>\n");
    //offers -start
    fwrite($fp, "<offers>\n");
    $offers = false;
    foreach ($predl as $key => $value):
        //$i = 0;
        if( count($value) > 1):
            $offers = true;
        else:
            $offers = false;
        endif;

        foreach ($value as $key1 => $value1):
            //getar("CATALOG_QUANTITY => ".$value1["CATALOG_QUANTITY"]."+".$value1["CATALOG_STORE_AMOUNT_".PRICE_ID]);
            if( $value1["CATALOG_QUANTITY"] > 0) {

                /*if( $tovars[$value1["PROPERTIES"]["CML2_LINK"]["VALUE"]]["PROPS"]["ARRIVAL_DATE"]["VALUE"] ){
                    getar($tovars[$value1["PROPERTIES"]["CML2_LINK"]["VALUE"]]["PROPS"]["ARRIVAL_DATE"]["VALUE"]);
                    getar($time_short);
                }*/

                $name = str_replace($from, $to, str_replace(REPLACE_FOR_YANDEX, "", $value1["NAME"]));
                preg_match("/^([\S\s]*)\s(\([\S\s]+\))$/s", $name, $match );
                $opt_price = CCatalogProduct::GetOptimalPrice($value1["ID"], "1", array(), "N");
                //getar($opt_price);
                $ID_IMG_1 = getList(FOTO_1, "", array("UF_XML_ID" => $value1["PROPERTIES"]["FOTOGRAFIYA_1"]["VALUE"]));
                $ID_IMG_2 = getList(FOTO_2, "", array("UF_XML_ID" => $value1["PROPERTIES"]["FOTOGRAFIYA_2"]["VALUE"]));
                $ID_IMG_3 = getList(FOTO_3, "", array("UF_XML_ID" => $value1["PROPERTIES"]["FOTOGRAFIYA_3"]["VALUE"]));
                $ID_IMG_4 = getList(FOTO_4, "", array("UF_XML_ID" => $value1["PROPERTIES"]["FOTOGRAFIYA_4"]["VALUE"]));
                $ID_IMG_5 = getList(FOTO_5, "", array("UF_XML_ID" => $value1["PROPERTIES"]["FOTOGRAFIYA_5"]["VALUE"]));
                $ID_IMG_6 = getList(FOTO_6, "", array("UF_XML_ID" => $value1["PROPERTIES"]["FOTOGRAFIYA_6"]["VALUE"]));
                //getar($ID_IMG[0]["UF_FILE"]);
                fwrite($fp, "<offer id=\"" . $value1["ID"] . "\" available=\"true\"" . ($offers ? " group_id=\"" . $value1["PROPERTIES"]["CML2_LINK"]["VALUE"] . "\"" : "") . ">\n");
                $utm = $offers?"?offers=".$value1["ID"]."&amp;":"?";
                $utm .= "utm_source=yandex.market&amp;utm_medium=cpc&amp;utm_campaign=".$tovars[$value1["PROPERTIES"]["CML2_LINK"]["VALUE"]]["IBLOCK_SECTION_ID"]."&amp;utm_content=".$value1["ID"];
                fwrite($fp, "<url>" . SITE_URL_DEFINE . $tovars[$value1["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PAGE_URL"].$utm."</url>\n");

                if($opt_price["PRICE"]["PRICE"] != $value1["CATALOG_PRICE_" . PRICE_ID]) {
                    fwrite($fp, "<oldprice>" . $value1["CATALOG_PRICE_" . PRICE_ID] . "</oldprice>\n");
                }


                fwrite($fp, "<price>" . $opt_price["PRICE"]["PRICE"] . "</price>\n");
                //fwrite($fp, "<currencyId>".$value1["CATALOG_CURRENCY_".PRICE_ID]."</currencyId>\n");
                fwrite($fp, "<name>".$match[1]."</name>\n");
                fwrite($fp, "<currencyId>RUR</currencyId>\n");
                fwrite($fp, "<categoryId>" . $tovars[$value1["PROPERTIES"]["CML2_LINK"]["VALUE"]]["IBLOCK_SECTION_ID"] . "</categoryId>\n");
                if ($ID_IMG_1[0]["UF_FILE"])
                    fwrite($fp, "<picture>" . SITE_URL_DEFINE . api_get_img_patch($ID_IMG_1[0]["UF_FILE"]) . "</picture>\n");

                if ($ID_IMG_2[0]["UF_FILE"])
                    fwrite($fp, "<picture>" . SITE_URL_DEFINE . api_get_img_patch($ID_IMG_2[0]["UF_FILE"]) . "</picture>\n");

                if ($ID_IMG_3[0]["UF_FILE"])
                    fwrite($fp, "<picture>" . SITE_URL_DEFINE . api_get_img_patch($ID_IMG_3[0]["UF_FILE"]) . "</picture>\n");

                if ($ID_IMG_4[0]["UF_FILE"])
                    fwrite($fp, "<picture>" . SITE_URL_DEFINE . api_get_img_patch($ID_IMG_4[0]["UF_FILE"]) . "</picture>\n");

                if ($ID_IMG_5[0]["UF_FILE"])
                    fwrite($fp, "<picture>" . SITE_URL_DEFINE . api_get_img_patch($ID_IMG_5[0]["UF_FILE"]) . "</picture>\n");

                if ($ID_IMG_6[0]["UF_FILE"])
                    fwrite($fp, "<picture>" . SITE_URL_DEFINE . api_get_img_patch($ID_IMG_6[0]["UF_FILE"]) . "</picture>\n");

if(isset($value1["PROPERTIES"]["ARRIVAL_DATE"]["VALUE"]) && !empty($value1["PROPERTIES"]["ARRIVAL_DATE"]["VALUE"])){
	   fwrite($fp, "<delivery-options><option cost=\"900\" days=\"\" order-before=\"\"/></delivery-options>\n");
}else {
                fwrite($fp, "<delivery-options><option cost=\"900\" days=\"1-3\" order-before=\"\"/></delivery-options>\n");
}
                fwrite($fp, "<store>false</store>\n");
                fwrite($fp, "<pickup>true</pickup>\n");
                fwrite($fp, "<delivery>true</delivery>\n");
                fwrite($fp, "<cpa>1</cpa>\n");
                fwrite($fp, "<sales_notes>Наличные, безнал., эл. платежи</sales_notes>\n");

                if ($value1["PROPERTIES"]["CML2_MANUFACTURER"]["VALUE"])
                    fwrite($fp, "<vendorCode>" . $value1["PROPERTIES"]["CML2_MANUFACTURER"]["VALUE"] . "</vendorCode>\n");

                if ($value1["PROPERTIES"]["CML2_BAR_CODE"]["VALUE"])
                    fwrite($fp, "<barcode>" . $value1["PROPERTIES"]["CML2_BAR_CODE"]["VALUE"] . "</barcode>\n");

                //ограничивание описания в 3000 символов
                if ($tovars[$value1["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_TEXT"]) {
                    fwrite($fp, "<description><![CDATA[" . cutString(str_replace($from, $to, $tovars[$value1["PROPERTIES"]["CML2_LINK"]["VALUE"]]["~DETAIL_TEXT"]), 3000) . "]]></description>\n");
                }else{
                    fwrite($fp, "<description><![CDATA[" . str_replace($from, $to, $match[1]) . "]]></description>\n");
                }

                foreach ($value1["PROPERTIES"] as $key2 => $value2):
                    if (strlen($value2["VALUE"]) > 0 && !in_array($value2["CODE"], $not_display_offer_props)):
                        fwrite($fp, "<param name=\"" . $value2["NAME"] . "\">" . $value2["VALUE"] . "</param>\n");
                    endif;
                endforeach;
                unset($key2);
                unset($value2);


                if($value1["PROPERTIES"]["KOD_TSVETA"]["VALUE"]){
                    $explode_value1_code = explode("#",$value1["PROPERTIES"]["KOD_TSVETA"]["VALUE"]);
                    if( $explode_value1_code[0] ){
                        fwrite($fp, "<param name=\"Цвет\">" . trim($explode_value1_code[0]) . "</param>\n");
                    }
                }
                unset($match);
                foreach ($tovars[$value1["PROPERTIES"]["CML2_LINK"]["VALUE"]]['PROPS'] as $key2 => $value2):

                    if (!in_array($value2["CODE"], $not_display_prop) && $value2["VALUE"]) {
                        //getar($value2);
                        switch ($value2["CODE"]) {
                            //case "MATERIAL_NOZHEK_3":
                                //fwrite($fp, "<param name=\"".$value2["NAME"]."\">".str_replace($from, $to, $value2["VALUE"])."</param>\n");
                            //break;
                            case "RAZMER_SH_KH_G_KH_V_1":
                                if (strpos($value2["VALUE"], "мм") !== false):
                                    $unit = "мм";
                                elseif (strpos($value2["VALUE"], "см") !== false):
                                    $unit = "см";
                                endif;
                                $explode_value2 = explode(" х ", str_replace(array(" мм", " см"), "", $value2["VALUE"]));
                                fwrite($fp, "<param name=\"ширина\" unit=\"" . $unit . "\">" . $explode_value2[0] . "</param>\n");
                                fwrite($fp, "<param name=\"глубина\" unit=\"" . $unit . "\">" . $explode_value2[1] . "</param>\n");
                                fwrite($fp, "<param name=\"высота\" unit=\"" . $unit . "\">" . $explode_value2[2] . "</param>\n");
                                unset($explode_value2);
                                break;

                            case "RAZMER_STOLA_SH_KH_G_KH_V":
                                if (strpos($value2["VALUE"], "мм") !== false):
                                    $unit = "мм";
                                elseif (strpos($value2["VALUE"], "см") !== false):
                                    $unit = "см";
                                endif;
                                $explode_value2 = explode(" х ", str_replace(array(" мм", " см"), "", $value2["VALUE"]));
                                fwrite($fp, "<param name=\"ширина стола\" unit=\"" . $unit . "\">" . $explode_value2[0] . "</param>\n");
                                fwrite($fp, "<param name=\"глубина стола\" unit=\"" . $unit . "\">" . $explode_value2[1] . "</param>\n");
                                fwrite($fp, "<param name=\"высота стола\" unit=\"" . $unit . "\">" . $explode_value2[2] . "</param>\n");
                                unset($explode_value2);
                                break;

                            default:
                                if (in_array($value2["CODE"], array("TOLSHCHINA_STOLESHNITSY", "VYSOTA_PODLOKOTNIKOV", "VYSOTA_DO_SIDENYA_1", "VYSOTA_DO_SIDENYA", "VYSOTA_PODLOKOTNIKOV_1"))) {
                                    preg_match("/([0-9]+)\s{1,}([\S]+)/", $value2["VALUE"], $match);
                                    fwrite($fp, "<param name=\"" . $value2["NAME"] . "\" unit=\"" . $match[2] . "\">" . $match[1] . "</param>\n");
                                    unset($match);
                                } elseif (in_array($value2["CODE"], array("MAKSIMALNAYA_NAGRUZKA", "VES"))) {
                                    fwrite($fp, "<param name=\"" . $value2["NAME"] . "\" unit=\"кг\">" . str_replace(" кг", "", $value2["VALUE"]) . "</param>\n");
                                } else {
                                    fwrite($fp, "<param name=\"" . $value2["NAME"] . "\">" . $value2["VALUE"] . "</param>\n");
                                }
                                break;
                        }
                    }
                endforeach;

                unset($key2);
                unset($value2);


                fwrite($fp, "</offer>\n");
                //$i++;
            }
        endforeach;
    endforeach;
    fwrite($fp, "</offers>\n");
    //offers -end

    fwrite($fp, "</shop>\n");
    //shop -end

    fwrite($fp, "</yml_catalog>");
    //yml_catalog -end

    fclose($fp);
}
unset($tovars);
unset($predl);

unset($key);
unset($value);

unset($key1);
unset($value1);
printf('Time end:'.date("Y-m-d H:i").PHP_EOL);
print '--------------------[ END ]--------------------'.PHP_EOL;
?>