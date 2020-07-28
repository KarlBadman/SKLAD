<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

// HIDE2VIEW

if (checkHitOnHideProductPosition($arResult['PROPERTIES']['HIDE2VIEW']['VALUE_XML_ID'])) {
    $arResult['PROPERTIES']['HIDE2REDIRECT']['VALUE'] = $arResult['PROPERTIES']['HIDE2REDIRECT']['VALUE'] ? : '/catalog/';
    LocalRedirect($arResult['PROPERTIES']['HIDE2REDIRECT']['VALUE'], 'refresh');
}

if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/resultModifierComponents/ResultModifierCatalogElement.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/resultModifierComponents/ResultModifierCatalogElement.php");

$RMCE = new ResultModifierCatalogElement;
$arResult = $RMCE->modificationArResult($arResult,$arParams);