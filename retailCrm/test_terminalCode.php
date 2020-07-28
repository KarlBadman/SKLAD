<?php

// var_dump($_SERVER['SCRIPT_URI'] . '?' . $_SERVER['QUERY_STRING']);
// die();

$hash = trim(strip_tags($_GET['hash']));
$orderIdCrm = abs((int)$_GET['orderId']);
$city = trim(strip_tags($_REQUEST['city']));
$street = trim(strip_tags($_REQUEST['street']));
$houseNo = trim(strip_tags($_REQUEST['houseNo']));

if ('XLHbLjFXH2trQx' != $hash || 0 == $orderIdCrm || !$city || !$street) return;


$cityArray = explode('.', $city);
if (count($cityArray) > 1)
    $city = trim($cityArray[1]);

$streetArray = explode('.', $street);
if (count($streetArray) > 1)
    $street = trim($streetArray[1]);

$street = preg_replace('/^ул /', '', $street);
$street = preg_replace('/^пр-кт /', '', $street);
$street = str_replace('улица', '', $street);
$street = str_replace('дорога', '', $street);
$street = trim($street);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

Loader::includeModule('highloadblock');

$arHLBlock = HighloadBlockTable::getById(24)->fetch();
$obEntity = HighloadBlockTable::compileEntity($arHLBlock);
$strEntityDataClass = $obEntity->getDataClass();


// file_put_contents(__DIR__ . '/terminal_code_log.txt', print_r($_REQUEST, true));


$arFilter = array(
	'?UF_DATA_SOURCE' => "%$city% && %$street%",
);

$rsData = $strEntityDataClass::getList(array(
    'select' => array('UF_CITYNAME', 'UF_CITYCODE', 'UF_CITYID', 'UF_TERMINALCODE'),
    'filter' => $arFilter
));

$arItems = [];
while ($arItem = $rsData->fetch()) {
    $arItems[] = $arItem;
}

// если на одной улице несколько терминалов
if (count($arItems) > 1) {
    if ($houseNo) {
        $houseNoSearch = serialize(array('houseNo' => $houseNo));
        $houseNoSearch = str_replace('a:1:{', '', $houseNoSearch);
        $houseNoSearch = str_replace(';}', '', $houseNoSearch);
            
        $arFilter = array(
            '?UF_DATA_SOURCE' => "%$city% && %$street%",
            'UF_DATA_SOURCE' => "%$houseNoSearch%",
        );

        $rsData = $strEntityDataClass::getList(array(
            'select' => array('UF_CITYNAME', 'UF_CITYCODE', 'UF_CITYID', 'UF_TERMINALCODE'),
            'filter' => $arFilter
        ));
        
        $arItems = [];
        while ($arItem = $rsData->fetch()) {
            $arItems[] = $arItem;
        }
        
        if (count($arItems) == 0) {
            if (!is_numeric($houseNo)) {
                $houseNo = preg_replace('/\D/', '', $houseNo);
                $houseNo = (string)(int)$houseNo;
                
                $houseNoSearch = serialize(array('houseNo' => $houseNo));
                $houseNoSearch = str_replace('a:1:{', '', $houseNoSearch);
                $houseNoSearch = str_replace('";}', '', $houseNoSearch);
                var_dump($houseNoSearch);
                
                $arFilter = array(
                    '?UF_DATA_SOURCE' => "%$city% && %$street%",
                    'UF_DATA_SOURCE' => "%$houseNoSearch%",
                );

                $rsData = $strEntityDataClass::getList(array(
                    'select' => array('UF_CITYNAME', 'UF_CITYCODE', 'UF_CITYID', 'UF_TERMINALCODE'),
                    'filter' => $arFilter
                ));

                $arItems = [];
                while ($arItem = $rsData->fetch()) {
                    $arItems[] = $arItem;
                }
            }
        }
    }
}

var_dump($arFilter);
var_dump($arItems);
die();

if (count($arItems) == 1) {
    $arItem = $arItems[0];
    $terminalCode = $arItem['UF_TERMINALCODE'];
    
    // file_put_contents(__DIR__ . '/terminal_code_log.txt', print_r($arFilter, true), FILE_APPEND);
    // file_put_contents(__DIR__ . '/terminal_code_log.txt', print_r($arItem, true), FILE_APPEND);
} else {
    $terminalCode = '';
    // file_put_contents(__DIR__ . '/terminal_code_errors.txt', print_r($arFilter, true) . print_r($arItems, true), FILE_APPEND);
}
    
    if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
	

	$api_by = 'id';
    $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
    $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
    $client = new \RetailCrm\ApiClient($api_host, $api_key);
    
    $order = array(
        'id' => $orderIdCrm,
        'customFields' =>[
            'dpd_terminal_code' => $terminalCode,
            'changed_by_api' => true,
        ]
    );
    
    // file_put_contents(__DIR__ . '/terminal_code_log.txt', print_r($order, true), FILE_APPEND);
    
    // $response = $client->ordersEdit($order, $api_by);
    
    // file_put_contents(__DIR__ . '/terminal_code_log.txt', print_r($response, true), FILE_APPEND);