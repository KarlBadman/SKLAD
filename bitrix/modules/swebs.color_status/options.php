<?php

use Bitrix\Main\Loader;

$strModuleID = 'swebs.color_status';

Loader::includeModule($strModuleID);
Loader::includeModule('sale');

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $strModuleID . '/lib/CModuleOptions.php');

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $strModuleID . '/options.php');

$isShowRightsTab = false;

$arTabs = array(
    array(
        'DIV' => 'edit1',
        'TAB' => 'Настройки',
        'ICON' => '',
        'TITLE' => 'Настройки'
    )
);

$arGroups = array(
    'MAIN' => array('TITLE' => 'Цвета статусов', 'TAB' => 0)
);


// status
$arSelect = array(
    'ID', 'NAME', 'TYPE'
);
$arOrdersStatus = array();
$dbStatus = CSaleStatus::GetList(array(), array('LID' => LANGUAGE_ID), false, false, $arSelect);
while ($arStatusFields = $dbStatus->GetNext()) {
    $arOrdersStatus[$arStatusFields['ID']] = $arStatusFields['NAME'] . ' (' . $arStatusFields['ID'] . ')';
}

$i = 10;
foreach ($arOrdersStatus as $strID => $strName) {
    $arOptions[$strID] = array(
        'GROUP' => 'MAIN',
        'TITLE' => $strName . ':',
        'TYPE' => 'COLORPICKER',
        'SORT' => $i
    );
    $i += 10;
}

/*$arOptions = array(
    'N' => array(
        'GROUP' => 'MAIN',
        'TITLE' => 'Новый:',
        'TYPE' => 'COLORPICKER',
        'SORT' => '10'
    ),
    'PASSWORD' => array(
        'GROUP' => 'MAIN',
        'TITLE' => 'Пароль:',
        'TYPE' => 'STRING',
        'DEFAULT' => '',
        'SORT' => '20',
        'NOTES' => 'Пароль для доступа к API "S-Webs SMS".'
    ),
    'NAME' => array(
        'GROUP' => 'MAIN',
        'TITLE' => 'Зарегистрированные имена:',
        'TYPE' => 'SELECT',
        'VALUES' => $arNames,
        'SORT' => '30',
        'NOTES' => 'Выбранное имя будет использоваться по умолчанию.'
    ),
    'BUDGET' => array(
        'GROUP' => 'MAIN',
        'TITLE' => 'Бюджет:',
        'TYPE' => 'CUSTOM',
        'VALUE' => Send::getBudget(),
        'SORT' => '40'
    ),
);*/

$obOptions = new CModuleOptions($strModuleID, $arTabs, $arGroups, $arOptions, $isShowRightsTab);
$obOptions->ShowHTML();
