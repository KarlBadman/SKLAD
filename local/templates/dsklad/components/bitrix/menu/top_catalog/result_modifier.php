<?

if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/resultModifierComponents/ResultModifierTopMenu.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/resultModifierComponents/ResultModifierTopMenu.php");


$RMTO = new ResultModifierTopMenu;
$arResult = $RMTO->modificationArResult($arResult,$arParams);
