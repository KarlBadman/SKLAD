<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/resultModifierComponents/ResultModifierCatalogSectionDsklad.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/resultModifierComponents/ResultModifierCatalogSectionDsklad.php");

$RMCSD = new ResultModifierCatalogSectionDsklad;
$arResult = $RMCSD->modificationArResult($arResult,$arParams);