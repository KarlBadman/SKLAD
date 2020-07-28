<?php
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www"; // Master
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];
$sDateStart = date('Y.m.d H:i:s');

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
set_time_limit(0);
// define("LANG", "ru");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/classes/dpd_service.class.php");

use Bitrix\Main\Loader,
    Bitrix\Main\Entity,
	Bitrix\Main\Type\DateTime,
    Bitrix\Highloadblock,
    Bitrix\Highloadblock\HighloadBlockTable;

Loader::includeModule("highloadblock");

$DPD_TERM_HL_ID = 24;

$oDPD = new DPD_service_my;

$arData = array(
	//'countryCode' => "RU"
);

// Получаем список терминалов от DPD.
// getTerminal (getParcelShops) - пункты с ограничениями (примерно 1481)
// getTerminal2 (getTerminalsSelfDelivery2) - пункты БЕЗ ограниченй (примерно 158)

$arDpdTerminals = $oDPD->getTerminal($arData);
$arDpdTerminals2 = $oDPD->getTerminal2($arData);

$arDpdTerminalsByCode = array();

if (empty($arDpdTerminals["parcelShop"]) && empty($arDpdTerminals2["terminal"])){
    die('data not received');
}

//foreach ($arDpdTerminals["terminal"] as $arDpdTerminalItem) {
//    $arDpdTerminalsByCode[$arDpdTerminalItem["terminalCode"]] = $arDpdTerminalItem;
//}

foreach ($arDpdTerminals['parcelShop'] as $arDpdTerminalItem) {
    $arDpdTerminalsByCode[$arDpdTerminalItem['code']] = $arDpdTerminalItem;
}

foreach ($arDpdTerminals2['terminal'] as $arDpdTerminalItem) {
    $arDpdTerminalsByCode[$arDpdTerminalItem['terminalCode']] = $arDpdTerminalItem;
}

//echo "<pre>";
//print_r($arDpdTerminalsByCode);
//echo "</pre>";

$arDpdTerminals = $arDpdTerminalsByCode;


// Получаем список терминалов сохраненных в HL-блоке.
$arHLBlock = HighloadBlockTable::getById($DPD_TERM_HL_ID)->fetch();
$obEntity = HighloadBlockTable::compileEntity($arHLBlock);
$strEntityDataClass = $obEntity->getDataClass();

$arDpdTerminalsInHL = array();
$rsData = $strEntityDataClass::getList();

while ($arItem = $rsData->fetch()) {
    $arDpdTerminalsInHL[$arItem["UF_TERMINALCODE"]] = $arItem;
}

$arToAdd = array();    // Список кодов терминалов, которые нужно добавить из $arDpdTerminals.
$arToUpdate = array(); // Список кодов терминалов, которые нужно обновить в HL-блоке.
$arToDelete = array(); // Список кодов терминалов, которые нужно удалить в HL-блоке.

foreach ($arDpdTerminalsInHL as $sTerminalCode => $arTerminal) {
	if (array_key_exists($sTerminalCode, $arDpdTerminals)) {
		$arToUpdate[$sTerminalCode] = $arTerminal["ID"];
	} else {
		$arToDelete[$sTerminalCode] = $arTerminal["ID"];
	}
}

$arToAdd = array_keys(array_filter($arDpdTerminals, function($key) {
	global $arDpdTerminalsInHL;
	return !array_key_exists($key, $arDpdTerminalsInHL);
}, ARRAY_FILTER_USE_KEY));

// printf('<pre>$arToAdd: %s</pre>', print_r($arToAdd, true));
// printf('<pre>$arToUpdate: %s</pre>', print_r($arToUpdate, true));
// printf('<pre>$arToDelete: %s</pre>', print_r($arToDelete, true));


// Удаление ...
foreach ($arToDelete as $sTerminalCode => $ID) {
	$strEntityDataClass::Delete((int)$ID);
}


// Обновление ...
$arResultUpdate = array();
foreach ($arToUpdate as $sTerminalCode => $ID) {
	$arFields = dataPrepare($arDpdTerminals[$sTerminalCode]);
	$res = $strEntityDataClass::Update((int)$ID, $arFields);
	if ($res->getAffectedRowsCount()) {
		$arResultUpdate[$sTerminalCode] = $ID;
		$objDateTime = new DateTime();
		$strEntityDataClass::Update((int)$ID, array("UF_DATE_MODIFIED" => $objDateTime->toString()));
	}
}
unset($arFields);
unset($res);


// Добавление ...
$arResultAdd = array();
foreach ($arToAdd as $sTerminalCode) {
	$arFields = dataPrepare($arDpdTerminals[$sTerminalCode]);
	$objDateTime = new DateTime();
	$arFields["UF_DATE_MODIFIED"] = $arFields["UF_DATE_CREATE"] = $objDateTime->toString();
	$res = $strEntityDataClass::Add($arFields);
	if ($res->getId()) {
		$arResultAdd[$sTerminalCode] = $res->getId();
	}
}
unset($arFields);
unset($res);


print '-------------------[ START ]-------------------'.PHP_EOL;
printf('Time start: %s', $sDateStart.PHP_EOL);
printf('Loaded from DPD: %d terminals'.PHP_EOL, count($arDpdTerminals));
printf('Loaded from Database: %d terminals'.PHP_EOL, count($arDpdTerminalsInHL));

printf('Deleted: %d terminals'.PHP_EOL, count($arToDelete));
echo "<pre>";
if (count($arToDelete))
	print_r($arToDelete);

printf('Updated: %d terminals'.PHP_EOL, count($arResultUpdate));
if (count($arResultUpdate))
	print_r($arResultUpdate);

printf('Added: %d terminals'.PHP_EOL, count($arResultAdd));
if (count($arResultAdd))
	print_r($arResultAdd);
echo "</pre>";

printf('Time end: %s', date('Y.m.s H:i:s').PHP_EOL);
print '--------------------[ END ]--------------------'.PHP_EOL;


// Подготавливает массив для добавления в HL.
function dataPrepare($arTerminalData) {

    $payByCard = '';
    $terminalCode = '';
    $terminalName = '';
    foreach ($arTerminalData['schedule'] as $terminal){
        if ($terminal['operation'] == 'PaymentByBankCard') $payByCard = 'да';
    }

    $sumGabarit = (int)$arTerminalData['limits']['maxLength'] + (int)$arTerminalData['limits']['maxWidth'] + (int)$arTerminalData['limits']['maxHeight'];
    $maxGabarit = max((int)$arTerminalData['limits']['maxLength'], (int)$arTerminalData['limits']['maxWidth'], (int)$arTerminalData['limits']['maxHeight']);

    if (empty($arTerminalData["code"]) && !empty($arTerminalData["terminalCode"])) {
        $terminalCode = $arTerminalData["terminalCode"];
        $terminalName = $arTerminalData["terminalName"];
    }elseif(!empty($arTerminalData["code"]) && empty($arTerminalData["terminalCode"])) {
        $terminalCode = $arTerminalData["code"];
        $terminalName = $arTerminalData["brand"];
    }

    return array(
        "UF_TERMINALCODE" => $terminalCode,
        "UF_TERMINALNAME" => $terminalName,
        "UF_CITYID" => $arTerminalData["address"]["cityId"],
        "UF_COUNTRYCODE" => $arTerminalData["address"]["countryCode"],
        "UF_REGIONCODE" => $arTerminalData["address"]["regionCode"],
        "UF_REGIONNAME" => $arTerminalData["address"]["regionName"],
        "UF_CITYCODE" => $arTerminalData["address"]["cityCode"],
        "UF_CITYNAME" => $arTerminalData["address"]["cityName"],
        "UF_DATA_SOURCE" => serialize($arTerminalData),
        "UF_ONLINE_PAY" => $payByCard,
        "UF_GABAPIT_MAX" => ($maxGabarit > 0) ? $maxGabarit : 'без ограничений',
        "UF_MAX_SUM" => ($sumGabarit > 0) ? $sumGabarit : 'без ограничений',
        "UF_MAX_VES_OTPAVKI" => (!empty($arTerminalData['limits']['maxShipmentWeight'])) ? $arTerminalData['limits']['maxShipmentWeight'] : 'без ограничений',
        "UF_MAX_VES_UPAKOVKI" => (!empty($arTerminalData['limits']['maxWeight'])) ? $arTerminalData['limits']['maxWeight'] : 'без ограничений',
    );
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>