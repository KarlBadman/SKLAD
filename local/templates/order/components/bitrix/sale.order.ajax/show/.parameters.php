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
    $arDeliveryName[$props['ID']] = $props['NAME'].'['.$props['ID'].']';
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
    "TYPE_DELIVERY_POINT" => array(
        "PARENT" => "BASE",
        "NAME" => "Тип доставки до пункта самовызова",
        "TYPE" => "LIST",
        "VALUES" => $arDeliveryName,
        'MULTIPLE' => 'Y',
    ),
    "TYPE_DELIVERY_COURIER" => array(
        "PARENT" => "BASE",
        "NAME" => "Тип доставки курьером",
        "TYPE" => "LIST",
        "VALUES" => $arDeliveryName,
        'MULTIPLE' => 'Y',
    ),
    "TYPE_DELIVERY_STOCK" => array(
        "PARENT" => "BASE",
        "NAME" => "Тип доставки самовызов со склада",
        "TYPE" => "LIST",
        "VALUES" => $arDeliveryName,
        'MULTIPLE' => 'Y',
    ),
    "PAY_SYSTEM_INSTALLMENTS" => array(
        "PARENT" => "BASE",
        "NAME" => 'CODE рассрочки',
        "TYPE" => "STRING",
        'DEFAULT'=>'YANDEX_INSTALLMENTS',
    ),
    "PAYS_ANOTHER_CODE" => array(
        "PARENT" => "BASE",
        "NAME" => 'CODE платежной системы "Заплатит другой"',
        "TYPE" => "STRING",
        'DEFAULT'=>'PAYS_ANOTHER',
    ),
    'ID_WARRANTY'=> array(
        'PARENT' => 'BASE',
        'NAME' => 'ID элемента доп. гарантии',
        'TYPE' => 'STRING',
        'VALUES' => '',
    ),
    'FREE_DELIVERY'=> array(
        'PARENT' => 'BASE',
        'NAME' => 'ID бесплатных доставок',
        'TYPE' => 'CHECKBOX',
        "DEFAULT" =>"N",
    ),
    'SHOW_PROMO'=> array(
        'PARENT' => 'BASE',
        'NAME' => 'Показать промокод',
        'TYPE' => 'CHECKBOX',
        "DEFAULT" =>"N",
    ),
    'PAYMENT_NEW_URL'=> array(
        'PARENT' => 'BASE',
        'NAME' => 'Платежные системы для которых формируется ссылка',
        "TYPE" => "LIST",
        "VALUES" => $arPaymentProp,
        'MULTIPLE' => 'Y',
    ),
    "PAYS_ONLY_DELIVERY_CODE" => array(
        "PARENT" => "BASE",
        "NAME" => 'CODE платежной системы "Оплата доставки по карте"',
        "TYPE" => "STRING",
        'DEFAULT'=>'PAY_ONLY_DELIVERY',
    ),
);

$db_ptype = CSalePaySystem::GetList($arOrder = Array("SORT"=>"ASC", "PSA_NAME"=>"ASC"), Array("LID"=>SITE_ID, "ACTIVE"=>"Y"));
while ($ptype = $db_ptype->Fetch()){
    $arTemplateParameters['DISCOUNT_PERCENT_'.$ptype['ID']] = array(
        "PARENT" => "BASE",
        "NAME" => 'Процент скидки '.$ptype['NAME'],
        "TYPE" => "STRING",
    );
};
