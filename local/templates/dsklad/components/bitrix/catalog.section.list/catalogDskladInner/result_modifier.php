<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

function actionSectionCSLCDL($arResult)
{
    foreach ($arResult['SECTIONS'] as $key => $val) {
        if (strripos($_SERVER['REQUEST_URI'], $val['SECTION_PAGE_URL']) !== false) {
            $arResult['SECTIONS'][$key]['ACTIVE'] = 'Y';
        }
    }
    return $arResult;
}

function initCSLCDL($arResult)
{

    $arResult = actionSectionCSLCDL($arResult);

    return $arResult;
}

$arResult = initCSLCDL($arResult);
