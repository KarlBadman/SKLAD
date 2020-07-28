<?php
function OnBeforeUserUpdateHandler(&$arFields)
{
    $arFields["LOGIN"] = $arFields["EMAIL"];
    return $arFields;
}