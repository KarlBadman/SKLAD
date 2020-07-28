<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arResult = array();

\Bitrix\Main\Loader::includeModule('dsklad.site');
$arResult['DPD'] = \Dsklad\Order::getDPDTerminals([]);
$terminals = $arResult['DPD']['TERMINAL'];

function myCmp($a, $b) {
    return (int)$b['limits']['dimensionSum'] - (int)$a['limits']['dimensionSum'];
}
$terminalNull = array();
uasort($terminals, 'myCmp');

foreach ($terminals as $k=>$t){

    if($t['limits']['maxShipmentWeight'] == $t['limits']['maxWeight'] && $t['limits']['maxShipmentWeight'] > 0 && $t['limits']['maxWeight'] > 0){
        $terminals[$k]['no_two_pack'] = true;
    }else{
        $terminals[$k]['no_two_pack'] = false;
    }

    if(empty($t['limits']['dimensionSum'])){
        $terminalNull[] = $t;
        unset($terminals[$k]);
    }
}

$arResult['DPD']['TERMINAL'] = array_merge($terminalNull,$terminals);

$this->IncludeComponentTemplate();