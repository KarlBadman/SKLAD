<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule('iblock')) {
    return;
}

$arTypesEx = CIBlockParameters::GetIBlockTypes(array('-' => ' '));

$arIBlocks = array();
$arOrder = array('SORT' => 'ASC');
$arFilter = array(
    'SITE_ID' => $_REQUEST['site'],
    'TYPE' => ($arCurrentValues['IBLOCK_TYPE'] != '-' ? $arCurrentValues['IBLOCK_TYPE'] : '')
);
$dbResult = CIBlock::GetList($arOrder, $arFilter);
while ($arFields = $dbResult->Fetch()) {
    $arIBlocks[$arFields['ID']] = $arFields['NAME'];
}

$arComponentParameters = array(
    "GROUPS" => array(),
    "PARAMETERS" => array(

        'IBLOCK_TYPE' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Тип инфоблока',
            'TYPE' => 'LIST',
            'VALUES' => $arTypesEx,
            'REFRESH' => 'Y',
        ),
        'IBLOCK_ID' => array(
            'PARENT' => 'BASE',
            'NAME' => 'ID Инфоблока с городами',
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks,
            'REFRESH' => 'Y',
        ),
        'ELEMENT_ID' => array(
            'PARENT' => 'BASE',
            'NAME' => 'ID товара',
            'TYPE' => 'STRING'
        ),
        'EVENT_NAME' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Почтовое событие',
            'TYPE' => 'STRING'
        )
    ),
);