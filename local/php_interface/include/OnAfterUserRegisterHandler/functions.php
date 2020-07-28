<?php
function OnAfterUserRegisterHandler(&$arFields)
{
    if (intval($arFields["ID"])>0)
    {
        $toSend = Array();
        $toSend["PASSWORD"] = strlen(trim($arFields["CONFIRM_PASSWORD"]))>0?$arFields["CONFIRM_PASSWORD"]:$arFields["PASSWORD_CONFIRM"];
        // $toSend["PASSWORD"] = $arFields["CONFIRM_PASSWORD"];
        $toSend["EMAIL"] = $arFields["EMAIL"];
        $toSend["USER_ID"] = $arFields["ID"];
        $toSend["USER_IP"] = $arFields["USER_IP"];
        $toSend["USER_HOST"] = $arFields["USER_HOST"];
        $toSend["LOGIN"] = $arFields["LOGIN"];
        $toSend["NAME"] = (trim ($arFields["NAME"]) == "")? $toSend["NAME"] = htmlspecialchars('<Не указано>'): $arFields["NAME"];
        $toSend["LAST_NAME"] = (trim ($arFields["LAST_NAME"]) == "")? $toSend["LAST_NAME"] = htmlspecialchars('<Не указано>'): $arFields["LAST_NAME"];
        CEvent::SendImmediate ("MY_NEW_USER", SITE_ID, $toSend);
    }
    return $arFields;
}