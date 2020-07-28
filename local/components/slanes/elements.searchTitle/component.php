<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!CModule::IncludeModule("highloadblock")) {
   ShowError(GetMessage("Модуль highloadblock не установлен."));
   return;
}

use \Bitrix\Main\Application;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;


$arCML2_LINK_IDS = array();

foreach ($arParams['ELEMENTS']['IBLOCK'] as $iblock_id => $ids) {
    $arFilter = array(
        'IBLOCK_LID' => SITE_ID,
        'IBLOCK_ACTIVE' => 'Y',
        'ACTIVE_DATE' => 'Y',
        'ACTIVE' => 'Y',
        'CHECK_PERMISSIONS' => 'Y',
        'MIN_PERMISSION' => 'R',
        'IBLOCK_ID' => $iblock_id,
        'ID' => $ids,
    );

    $arSelect = array(
        'CATALOG_QUANTITY',
    'NAME',
        'ID',
        'ACTIVE',
        'IBLOCK_ID',
        'DETAIL_PAGE_URL',
        'XML_ID',
        'IBLOCK_SECTION_ID'
    );

    $arResult['IBLOCK'][$iblock_id] = array();

    $rsElements = \CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    while ($arElement = $rsElements->GetNextElement()) {
        $temp = $arElement->GetFields();
        $temp['PROPERTIES'] = $arElement->GetProperties();
        $temp['PRODUCT'] = array();
        $temp['PRODUCT']['QUANTITY'] = $temp['CATALOG_QUANTITY'];
        $temp['CML2_LINK_ID'] = $temp['PROPERTIES']['CML2_LINK']['VALUE'];
        $arResult['ITEMS'][$temp['ID']] =  $temp;
        $arCML2_LINK_IDS[$temp['PROPERTIES']['CML2_LINK']['VALUE']] = $temp['PROPERTIES']['CML2_LINK']['VALUE'];
    }
}

if (!empty($arCML2_LINK_IDS)) {
    $arFilter = array(
        'IBLOCK_LID' => SITE_ID,
        'IBLOCK_ACTIVE' => 'Y',
        'ACTIVE_DATE' => 'Y',
        'ACTIVE' => 'Y',
        'CHECK_PERMISSIONS' => 'Y',
        'MIN_PERMISSION' => 'R',
        'IBLOCK_ID' => \Dsklad\Config::getParam('iblock/catalog'),
        'ID' => $arCML2_LINK_IDS,
    );

    $rsElements = \CIBlockElement::GetList(array(), $arFilter);
    while ($arElement = $rsElements->GetNextElement()) {
        $temp = $arElement->GetFields();
        $temp['PROPERTIES'] = $arElement->GetProperties();
        $arResult['CML2_LINK'][$temp['ID']] =  $temp;
    }
}

foreach ($arResult['ITEMS'] as $key => $element) {
    if(!in_array($element['CML2_LINK_ID'], array_keys($arResult['CML2_LINK']))){
        unset($arResult['ITEMS'][$key]);
    }
    if (strpos($element['DETAIL_PAGE_URL'], 'offers') !== false) {
        $arResult['ITEMS'][$key]['DETAIL_PAGE_URL'] = str_replace('?offers=', '', $element['DETAIL_PAGE_URL']).'/';
    }
}

$searchRequest = Application::getInstance()->getContext()->getRequest()->getQuery('search');
if (empty($arResult['ITEMS']) && !empty($searchRequest)) {
    $hlblock = HL\HighloadBlockTable::getById(NULLEDSEARCHREQUESTS)->fetch();
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    $rsData = $entity_data_class::getList(array(
       "filter" => array('UF_SEARCH_REQUEST' => $searchRequest),
    ));
    if (!$rsData->fetch()) {
        $result = $entity_data_class::add(
            array(
                'UF_SEARCH_REQUEST' => $searchRequest,
            )
        );
        if (!$result->isSuccess()) {
            $errorMsgs = "";
            foreach ($result->getErrors() as $errors) {
                $errorMsgs .= $errors->getMessage() . ", ";
            }
            CEventLog::Add(
                array(
                    "SEVERITY" => 'ERROR', 
                    "AUDIT_TYPE_ID" => 'OnSearch', 
                    "MODULE_ID" => 'search', 
                    "ITEM_ID" => 'UF_SEARCH_REQUEST', 
                    "DESCRIPTION" => 'Writing error into a HL NULLEDSEARCHREQUESTS empty request | ' . $errorMsgs, 
                    "SITE_ID" => SITE_ID
                )
            );
        }
    }
}

$this->IncludeComponentTemplate();