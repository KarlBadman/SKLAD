<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$evtManager = \Bitrix\Main\EventManager::getInstance();

$evtManager->addEventHandler(
    'iblock',
    'OnAfterIBlockElementAdd',
    ['\Dsklad\Reviews', 'OnAfterIBlockElementAdd']
);

$evtManager->addEventHandler(
    'iblock',
    'OnAfterIBlockElementUpdate',
    ['\Dsklad\Reviews', 'OnAfterIBlockElementUpdate']
);

$evtManager->addEventHandler(
    'iblock',
    'OnBeforeIBlockElementDelete',
    ['\Dsklad\Reviews', 'OnBeforeIBlockElementDelete']
);

$evtManager->addEventHandler(
    'sale',
    'OnSaleOrderEntitySaved',
    ['\Dsklad\PromoCodeFor4Views', 'OnSaleOrderEntitySaved']
);
