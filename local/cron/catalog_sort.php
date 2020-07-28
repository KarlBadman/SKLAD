<?

use Bitrix\Main\Loader;

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('BX_NO_ACCELERATOR_RESET', true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

set_time_limit(0);
ini_set("memory_limit", "16384M");
ini_set('display_errors', '1');
error_reporting(E_ERROR);

Loader::includeModule('sale');
Loader::includeModule('iblock');
Loader::includeModule('catalog');

$arSelect = Array("ID", "IBLOCK_ID", "NAME", "CATALOG_GROUP_2");
$arFilter = Array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "ACTIVE" => "Y", "SECTION_ID" => array(160, 161), 'INCLUDE_SUBSECTIONS' => 'Y');
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while ($ob = $res->Fetch()) {
    $arFields[] = $ob;
    $ids[] = $ob['ID'];
}
$res = CCatalogSKU::getOffersList(
    $ids, // массив ID товаров
    $iblockID = CATALOG_IBLOCK_ID,
    $skuFilter = array(),
    $fields = array("CATALOG_GROUP_2"),
    $propertyFilter = array()
);
foreach ($res as $keyit => $itemid) {
    foreach ($itemid as $key => $item) {
        $skuIdsItem[$keyit][$key] = $key;
        $skuIds[] = $key;
    }
}

$minPrice = array();

$db_res = CPrice::GetList(array(), array("PRODUCT_ID" => $skuIds, "CATALOG_GROUP_ID" => 2));
while ($ar_res = $db_res->Fetch()) {
    $all_prices[$ar_res['PRODUCT_ID']] = $ar_res;
}

foreach ($skuIdsItem as $key => $skuId) {
    $minPrice[$key]['MIN_PRICE'] = '9999999';
    foreach ($skuId as $keySku => $val) {
        $discPrice = CCatalogProduct::GetOptimalPrice($val, 4);
        foreach ($all_prices as $keyPrice => $prices) {

            if ($val == $keyPrice && $discPrice['DISCOUNT_PRICE'] < $minPrice[$key]['MIN_PRICE']) {
                $minPrice[$key]['MIN_PRICE'] = $discPrice['DISCOUNT_PRICE'];
            }
        }
    }
}
foreach ($minPrice as $key => $value) {
    $ELEMENT_ID = $key;  // код элемента
    $PROPERTY_CODE = "PRICE_FOR_SORT";  // код свойства
    $PROPERTY_VALUE = (int)$value['MIN_PRICE'];  // значение свойства

// Установим новое значение для данного свойства данного элемента
    CIBlockElement::SetPropertyValuesEx($ELEMENT_ID, false, array($PROPERTY_CODE => $PROPERTY_VALUE));
}

echo 'Y';



