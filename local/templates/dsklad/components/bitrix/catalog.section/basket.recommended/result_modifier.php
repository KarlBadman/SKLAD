<?php

if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/resultModifierComponents/ResultModifierCatalogSectionRecommended.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/resultModifierComponents/ResultModifierCatalogSectionRecommended.php");

$RMB = new ResultModifierCatalogSectionRecommended;
$arResult = $RMB->modificationArResult($arResult,$arParams);