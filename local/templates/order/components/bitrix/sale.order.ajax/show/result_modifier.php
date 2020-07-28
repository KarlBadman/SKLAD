<?php

if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/resultModifierComponents/ResultModifierOrder.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/resultModifierComponents/ResultModifierOrder.php");

$RMO = new ResultModifierOrder;
$arResult = $RMO->modificationArResult($arResult,$arParams);
