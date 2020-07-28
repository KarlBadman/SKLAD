<?php

if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/resultModifierComponents/ResultModifierBasket.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/resultModifierComponents/ResultModifierBasket.php");

$RMB = new ResultModifierBasket;
$arResult = $RMB->modificationArResult($arResult,$arParams);
