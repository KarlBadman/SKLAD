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

$hlblock = HighloadBlockTable::getList(['filter' => ['=NAME' => 'DPDTERMINALS']])->fetch();
$hlClassName = (HighloadBlockTable::compileEntity($hlblock))->getDataClass();

$Terminals = array();
$hlData = $hlClassName::getList();

$arCityUndergroundId = array(
    52000001000,
    78000000000,
    77000000000,
    54000001000,
    66000001000,
    63000001000,
    16000001000
);
while ($exTerminal = $hlData->fetch()) {
    $val = unserialize($exTerminal['UF_DATA_SOURCE']);
    if(array_search($val['address']['cityCode'],$arCityUndergroundId) !== false){
        $myCurl = curl_init();
        curl_setopt_array($myCurl, array(
            CURLOPT_URL => 'https://geocode-maps.yandex.ru/1.x/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(array(
                'apikey'=> \Dsklad\Config::getParam('api_key/yandex_map'),
                'geocode'=>$val['geoCoordinates']['longitude'].','.$val['geoCoordinates']['latitude'],
                'kind'=>'metro',
                'format'=>'json',
                'results'=> 3,
            ))
        ));
        $response = curl_exec($myCurl);
        curl_close($myCurl);

        $arMetro = (array)json_decode($response);
        $metroName = array();
        foreach ($arMetro['response']->GeoObjectCollection->featureMember as $metro){
            foreach ($metro->GeoObject->metaDataProperty->GeocoderMetaData->Address->Components as $addresses){
                if($addresses->kind == 'metro'){
                    $metroName[] = $addresses->name;
                }
            }
        }

        $val['undeground'] = json_encode($metroName);

        $result = $hlClassName::update($exTerminal['ID'], array(
            'UF_DATA_SOURCE'=> serialize($val),
        ));
    }
}


