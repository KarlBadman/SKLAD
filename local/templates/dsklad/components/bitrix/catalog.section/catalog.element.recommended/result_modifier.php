<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();


if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/resultModifierComponents/ResultModifierCatalogSectionRecommendElement.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/resultModifierComponents/ResultModifierCatalogSectionRecommendElement.php");

$RMCSRE = new ResultModifierCatalogSectionRecommendElement;
$arResult = $RMCSRE->modificationArResult($arResult,$arParams);

