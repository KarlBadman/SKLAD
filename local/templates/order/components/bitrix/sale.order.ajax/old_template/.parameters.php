<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arProps = array();
$arPropsTerminal = array();
$arPaymentProp = array();

$db_props = CSaleOrderProps::GetList(array("SORT" => "ASC"),array('ACTIVE'=>'Y'),false,false,array('ID','PERSON_TYPE_ID','NAME','CODE'));
while ($props = $db_props->Fetch()){
    $arProps[$props['ID']] = '['.$props['PERSON_TYPE_ID'].']'.$props['NAME'].'('.$props['CODE'].')';
    $arPropsTerminal[$props['PERSON_TYPE_ID'].'_'.$props['ID']] = '['.$props['PERSON_TYPE_ID'].']'.$props['NAME'].'('.$props['CODE'].')';
}

$arDelivery = \Bitrix\Sale\Delivery\Services\Manager::getActiveList();
$arDeliveryName = array();
foreach ($arDelivery as $props){
    $arDeliveryName[$props['ID']] = $props['NAME'].'['.$props['DESCRIPTION'].']';
}


$rsPaySystem = \Bitrix\Sale\Internals\PaySystemActionTable::getList(array(
    'filter' => array('ACTIVE'=>'Y'),
));
while($arPaySystem = $rsPaySystem->fetch()){
    $arPaymentProp[$arPaySystem['ID']] = $arPaySystem['NAME'];
}

$arTemplateParameters = array(
	"ALLOW_NEW_PROFILE" => array(
		"NAME"=>GetMessage("T_ALLOW_NEW_PROFILE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT"=>"Y",
		"PARENT" => "BASE",
	),
	"SHOW_PAYMENT_SERVICES_NAMES" => array(
		"NAME" => GetMessage("T_PAYMENT_SERVICES_NAMES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" =>"Y",
		"PARENT" => "BASE",
	),
	"SHOW_STORES_IMAGES" => array(
		"NAME" => GetMessage("T_SHOW_STORES_IMAGES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" =>"N",
		"PARENT" => "BASE",
	),
    "PARAMS_USER" => array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("T_PARAMS_USER"),
        "TYPE" => "LIST",
        "VALUES" => $arProps,
        'MULTIPLE' => 'Y',
    ),
    "PARAMS_DELIVERY" => array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("T_PARAMS_DELIVERY"),
        "TYPE" => "LIST",
        "VALUES" => $arProps,
        'MULTIPLE' => 'Y',
    ),
    "MAP_DELIVERY" => array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("T_MAP_DELIVERY"),
        "TYPE" => "LIST",
        "VALUES" => $arDeliveryName,
        'MULTIPLE' => 'Y',
    ),
    "NAL" => array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("T_NAL"),
        "TYPE" => "LIST",
        "VALUES" => $arPaymentProp,
        'MULTIPLE' => 'Y',
    ),
    "SHOW_DELIVERY" => array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("T_SHOW_DELIVERY"),
        "TYPE" => "LIST",
        "VALUES" => $arDeliveryName,
        'MULTIPLE' => 'Y',
    ),
    "SVETOFOR_OK" => array(
        "NAME" => GetMessage("T_SVETOFOR_OK"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" =>"N",
        "PARENT" => "BASE",
    ),
    "SVETOFOR_ARTICUL" => array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("T_SVETOFOR_ARTICUL"),
        "TYPE" => "STRING",
        'MULTIPLE' => 'Y',
    ),
    "SVETOFOR_NO_ID" => array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("T_SVETOFOR_NO_ID"),
        "TYPE" => "STRING",
        'MULTIPLE' => 'Y',
    ),
    "PROP_TERMINAL" => array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("T_PROP_TERMINAL"),
        "TYPE" => "LIST",
        "VALUES" => $arPropsTerminal,
        'MULTIPLE' => 'Y',
    ),
    "PROP_DPD_CODE" => array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("T_PROP_DPD_CODE"),
        "TYPE" => "LIST",
        "VALUES" => $arPropsTerminal,
        'MULTIPLE' => 'Y',
    ),
    "PROP_NOT_CALL_CODE" => array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("T_PROP_NOT_CALL_CODE"),
        "TYPE" => "LIST",
        "VALUES" => $arPropsTerminal,
        'MULTIPLE' => 'Y',
    ),
);
