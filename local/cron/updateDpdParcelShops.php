<?php
$_SERVER["DOCUMENT_ROOT"] = realpath(__DIR__ . "/../../"); // Master

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/classes/dpd_service.class.php");

use
    Bitrix\Main\Type\DateTime,
    Bitrix\Highloadblock\HighloadBlockTable,
    Bitrix\Main\Context,
    Bitrix\Main\Mail\Event,
    \Dsklad\Config;

$sDateStart = date('Y.m.d H:i:s');

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
set_time_limit(500);

@ini_set('memory_limit', '2048M');
@ini_set('output_buffering', 'off');

$oDPD = new DPD_service_my;

$arCityUndergroundId = array(
    52000001000,
    78000000000,
    77000000000,
    54000001000,
    66000001000,
    63000001000,
    16000001000
);

// Получаем список ПВЗ от DPD.
$arDpdTerminalsRu = $oDPD->getTerminal(array('countryCode' => "RU"));
$arDpdTerminalsKZ = $oDPD->getTerminal(array('countryCode' => "KZ"));
$arDpdTerminalsBY = $oDPD->getTerminal(array('countryCode' => "BY"));

$arDpdTerminals['parcelShop'] = array_merge($arDpdTerminalsRu['parcelShop'], $arDpdTerminalsBY['parcelShop'], $arDpdTerminalsKZ['parcelShop']);

$arDpdTerminals2 = $oDPD->getTerminal2($arData); // пункты БЕЗ ограниченй (примерно 158)

$arDpdTerminalsByCode = array();

if (empty($arDpdTerminals["parcelShop"]) || empty($arDpdTerminals2["terminal"])) {

    global $DB;
    $exception_entity = 'no_data_recived';
    $entity_type = 'dpdParcelShops';
    $exception_type = 'updateDpdParcelShops';
    $extra_info = 'Файл: ' . __FILE__ . \n . ' В интеграции с DPD произошла ошибка: пустой масив c пунктами самовывоза';

    $arFields = array(
        "entity_type" =>        "'" . $entity_type . "'",
        "entity_id" =>          time(),
        "exception_type" =>     "'" . $exception_type . "'",
        "exception_entity" =>   "'" . $exception_entity . "'",
        "extra_info" =>         "'" . $extra_info . "'"
    );

    $LOG_ID = $DB->Insert("xtra_log", $arFields);
    if (intval($LOG_ID)) {
        $obContext = Context::getCurrent();
        // goes to email
        $arFields['ID'] = $LOG_ID;
        $arFields['COMMENT'] = 'Исключение зафиксировано в updateDpdParcelShops.php! Не удалось получить ПВЗ от DPD.';
        $arMailFields = array(
            'EVENT_NAME' => 'LOGGING',
            'LID' => 's1',
            'C_FIELDS' => $arFields
        );
        Event::send($arMailFields);
    }

    die('data not received');
}

$hlblock = HighloadBlockTable::getList(['filter' => ['=NAME' => 'EXCLUDEDPDTERMINALS']])->fetch();
$hlClassName = (HighloadBlockTable::compileEntity($hlblock))->getDataClass();

$excludedTerminals = array();
$hlData = $hlClassName::getList();

while ($exTerminal = $hlData->fetch()) {
    $excludedTerminals[] = $exTerminal["UF_TERMINALCODE"];
}

foreach ($arDpdTerminals['parcelShop'] as $arDpdTerminalItem) {
    if (!in_array($arDpdTerminalItem['terminalCode'], $excludedTerminals)) {
        $arDpdTerminalItem['terminalCode'] = $arDpdTerminalItem['terminalCode'] ? : $arDpdTerminalItem['code'];
        $arDpdTerminalsByCode[$arDpdTerminalItem['terminalCode']] = $arDpdTerminalItem;
    }
}

foreach ($arDpdTerminals2['terminal'] as $arDpdTerminalItem) {
    if (!in_array($arDpdTerminalItem['terminalCode'], $excludedTerminals)) {
        $arDpdTerminalsByCode[$arDpdTerminalItem['terminalCode']] = $arDpdTerminalItem;
    }
}

$arDpdTerminals = $arDpdTerminalsByCode;

// Получаем список терминалов сохраненных в HL-блоке.
$arHLBlock = HighloadBlockTable::getById(Config::getParam('hl/dpd_terminals'))->fetch();
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
    if (isset($arDpdTerminals[(string)$sTerminalCode])) {
        $arToUpdate[$sTerminalCode] = $arTerminal["ID"];
    } else {
        if (!in_array($sTerminalCode, $excludedTerminals)) {
            $arToDelete[$sTerminalCode] = $arTerminal["ID"];
        }
    }
}

$arToAdd = array_keys(array_filter($arDpdTerminals, function ($key) {
    global $arDpdTerminalsInHL;
    return !array_key_exists($key, $arDpdTerminalsInHL);
}, ARRAY_FILTER_USE_KEY));

// Удаление ...
foreach ($arToDelete as $sTerminalCode => $ID) {
    $strEntityDataClass::Delete((int)$ID);
}

// Обновление ...
$arResultUpdate = array();
foreach ($arToUpdate as $sTerminalCode => $ID) {
    $exclude = array_map(function ($item) {
        return stripos($item['params']['value'], 'ПРОС') !== false;
    }, $arDpdTerminals[$sTerminalCode]['extraService']);
    if(in_array(true, $exclude) || 1) {
        $arDpdTerminals[$sTerminalCode]['undeground'] = getUnderground($arDpdTerminals[$sTerminalCode],$arCityUndergroundId);
	    $arFields = dataPrepare($arDpdTerminals[$sTerminalCode]);
        $res = $strEntityDataClass::Update((int)$ID, $arFields);
	    if ($res->getAffectedRowsCount()) {
            $arResultUpdate[$sTerminalCode] = $ID;
            $objDateTime = new DateTime();
            $strEntityDataClass::Update((int)$ID, array("UF_DATE_MODIFIED" => $objDateTime->toString()));
        }
    }
}

unset($arFields);
unset($res);

// Добавление ...
$arResultAdd = array();
foreach ($arToAdd as $sTerminalCode) {
    $exclude = array_map(function ($item) {
        return stripos($item['params']['value'], 'ПРОС') !== false;
    }, $arDpdTerminals[$sTerminalCode]['extraService']);
    if(in_array(true, $exclude) || 1) {
        $arDpdTerminals[$sTerminalCode]['undeground'] = getUnderground($arDpdTerminals[$sTerminalCode],$arCityUndergroundId);
        $arFields = dataPrepare($arDpdTerminals[$sTerminalCode]);
	    $objDateTime = new DateTime();
        $arFields["UF_DATE_MODIFIED"] = $arFields["UF_DATE_CREATE"] = $objDateTime->toString();
	    $res = $strEntityDataClass::Add($arFields);
        if ($res->getId()) {
	        $arResultAdd[$sTerminalCode] = $res->getId();
        }
    }
}
unset($arFields);
unset($res);

print '-------------------[ START ]-------------------' . PHP_EOL;
printf('Time start: %s', $sDateStart . PHP_EOL);
printf('Loaded from DPD: %d terminals' . PHP_EOL, count($arDpdTerminals));
printf('Loaded from Database: %d terminals' . PHP_EOL, count($arDpdTerminalsInHL));

printf('Deleted: %d terminals' . PHP_EOL, count($arToDelete));
echo "<pre>";
if (count($arToDelete))
    print_r($arToDelete);

printf('Updated: %d terminals' . PHP_EOL, count($arResultUpdate));
if (count($arResultUpdate))
    print_r($arResultUpdate);

printf('Added: %d terminals' . PHP_EOL, count($arResultAdd));
if (count($arResultAdd))
    print_r($arResultAdd);
echo "</pre>";

printf('Time end: %s', date('Y.m.s H:i:s') . PHP_EOL);
print '--------------------[ END ]--------------------' . PHP_EOL;

// Подготавливает массив для добавления в HL.
function dataPrepare($arTerminalData)
{
    $payByCard = '';
    foreach ($arTerminalData['schedule'] as $terminal) {
        if ($terminal['operation'] == 'PaymentByBankCard') $payByCard = 'да';
    }

    $sumGabarit = (int)$arTerminalData['limits']['dimensionSum'];
    $maxGabarit = max((int)$arTerminalData['limits']['maxLength'], (int)$arTerminalData['limits']['maxWidth'], (int)$arTerminalData['limits']['maxHeight']);

    return array(
        "UF_TERMINALCODE" =>    $arTerminalData['terminalCode'],
        "UF_TERMINALNAME" =>    $arTerminalData['terminalName'],
        "UF_CITYID" =>          $arTerminalData["address"]["cityId"],
        "UF_COUNTRYCODE" =>     $arTerminalData["address"]["countryCode"],
        "UF_REGIONCODE" =>      $arTerminalData["address"]["regionCode"],
        "UF_REGIONNAME" =>      $arTerminalData["address"]["regionName"],
        "UF_CITYCODE" =>        $arTerminalData["address"]["cityCode"],
        "UF_CITYNAME" =>        $arTerminalData["address"]["cityName"],
        "UF_DATA_SOURCE" =>     serialize($arTerminalData),
        "UF_ONLINE_PAY" =>      $payByCard,
        "UF_GABAPIT_MAX" =>     ($maxGabarit > 0) ? $maxGabarit : 'без ограничений',
        "UF_MAX_SUM" =>         ($sumGabarit > 0) ? $sumGabarit : 'без ограничений',
        "UF_MAX_VES_OTPAVKI" => (!empty($arTerminalData['limits']['maxShipmentWeight'])) ? $arTerminalData['limits']['maxShipmentWeight'] : 'без ограничений',
        "UF_MAX_VES_UPAKOVKI" =>(!empty($arTerminalData['limits']['maxWeight'])) ? $arTerminalData['limits']['maxWeight'] : 'без ограничений',
    );
}

function getUnderground($val,$arCityUndergroundId){

    if(array_search($val['address']['cityCode'],$arCityUndergroundId) !== false){
        $myCurl = curl_init();
        curl_setopt_array($myCurl, array(
            CURLOPT_URL => 'https://geocode-maps.yandex.ru/1.x/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(array(
                'apikey' => \Dsklad\Config::getParam('api_key/yandex_map'),
                'geocode' => $val['geoCoordinates']['longitude'] . ',' . $val['geoCoordinates']['latitude'],
                'kind' => 'metro',
                'format' => 'json',
                'results' => 3,
            ))
        ));
        $response = curl_exec($myCurl);
        curl_close($myCurl);

        $arMetro = (array)json_decode($response);
        $metroName = array();
        foreach ($arMetro['response']->GeoObjectCollection->featureMember as $metro) {
            foreach ($metro->GeoObject->metaDataProperty->GeocoderMetaData->Address->Components as $addresses) {
                if ($addresses->kind == 'metro') {
                    $metroName[] = $addresses->name;
                }
            }
        }

        return json_encode($metroName);

    }else{
        return '';
    }
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");