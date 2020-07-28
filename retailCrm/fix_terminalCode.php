<?php

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


file_put_contents(__DIR__ . '/terminal_code_log.txt', print_r($_REQUEST, true));


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
                // $houseNo = preg_replace('/\D/', '', $houseNo);
                $houseNo = (string)(int)$houseNo;
                
                $houseNoSearch = serialize(array('houseNo' => $houseNo));
                $houseNoSearch = str_replace('a:1:{', '', $houseNoSearch);
                $houseNoSearch = str_replace('";}', '', $houseNoSearch);
                
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

if (count($arItems) == 1) {
    $arItem = $arItems[0];
    $terminalCode = $arItem['UF_TERMINALCODE'];
    
    file_put_contents(__DIR__ . '/terminal_code_log.txt', print_r($arFilter, true), FILE_APPEND);
    file_put_contents(__DIR__ . '/terminal_code_log.txt', print_r($arItem, true), FILE_APPEND);
} else {
    $terminalCode = '';
    file_put_contents(__DIR__ . '/terminal_code_errors.txt', print_r($arFilter, true) . print_r($arItems, true), FILE_APPEND);
    
    $script_url = $_SERVER['SCRIPT_URI'] . '?' . $_SERVER['QUERY_STRING'];
    $order_url = "https://dsklad.retailcrm.ru/orders/$orderIdCrm/edit";
    $test_url = str_replace('fix_terminal', 'test_terminal', $script_url);
    
    $message = "$city ул. $street";
    if ($houseNoSearch)
        $message .= " $houseNo";
    $message .= " - ";
    
    if (count($arItems) == 0) {
        $message .= 'Нет терминалов';
    } else {
        $message .= count($arItems) . ' терминалов:';
        foreach ($arItems as $item) {
            $message .= ' ' . $arItem['UF_TERMINALCODE'];
        }
    }
    
    $message .= " $order_url";
    
    // mail ( 'e7a5f7t8f4l5k2f5@analitika-online.slack.com' , '[dsklad.ru] Ошибка проставления кода терминала' , "Заказ $orderIdCrm\n$message\n" . print_r($arFilter, true) . print_r($arItems, true) . "\n$script_url\nТест: $test_url");
    // mail ( 'comment-fe730ce924273a2f6c7f9bf99b1cb7fc4e076185-c491693@cards.kaiten.io', '', $message, "Content-Type: text/html; charset=UTF-8");
    // mail ( 'comment-0ec96c0feef0cf791903b4b91ab483bf1418256a-c491692@cards.kaiten.io', '', $message, "Content-Type: text/html; charset=UTF-8");
    die();
}
var_dump($arFilter);
    
    if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
	

	$api_by = 'id';
    $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
    $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
    $client = new \RetailCrm\ApiClient($api_host, $api_key);
    
    $order = array(
        'id' => $orderIdCrm,
        'customFields' =>[
            'dpd_terminal_code' => $terminalCode,
            // 'changed_by_api' => true,
        ]
    );
    
    file_put_contents(__DIR__ . '/terminal_code_log.txt', print_r($order, true), FILE_APPEND);
    
    $response = $client->ordersEdit($order, $api_by);
    
    file_put_contents(__DIR__ . '/terminal_code_log.txt', print_r($response, true), FILE_APPEND);