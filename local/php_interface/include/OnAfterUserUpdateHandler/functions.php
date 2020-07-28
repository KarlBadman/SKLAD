<?php
function OnAfterUserUpdateHandler(&$arFields)
{
    // Bug: На сайте установлен модуль retailCRM.
    // Добавился агент RCrmActions::orderAgent(), в котором есть вызов $USER->Update(),
    // из-за чего срабатывает этот обработчик и клиенту приходит письмо о смене пароля.
    // Решение: Добавляем проверку наличия поля CONFIRM_PASSWORD.
    if (!empty($arFields["CONFIRM_PASSWORD"])) {
        $toSend = Array();
        $toSend["PASSWORD"] = $arFields["CONFIRM_PASSWORD"];
        $toSend["PASSWORD1"] = $arFields["PASSWORD"];
        $toSend["EMAIL"] = $arFields["EMAIL"];
        $toSend["USER_ID"] = $arFields["ID"];
        $toSend["USER_IP"] = $arFields["USER_IP"];
        $toSend["STATUS"] = $arFields["STATUS"];
        $toSend["URL_LOGIN"] = $arFields["URL_LOGIN"];
        $toSend["USER_HOST"] = $arFields["USER_HOST"];
        $toSend["CHECKWORD"] = $arFields["CHECKWORD"];
        $toSend["MESSAGE"] = $arFields["MESSAGE"];
        $toSend["LOGIN"] = $arFields["LOGIN"];
        $toSend["NAME"] = (trim ($arFields["NAME"]) == "")? $toSend["NAME"] = htmlspecialchars('<Не указано>'): $arFields["NAME"];
        $toSend["LAST_NAME"] = (trim ($arFields["LAST_NAME"]) == "")? $toSend["LAST_NAME"] = htmlspecialchars('<Не указано>'): $arFields["LAST_NAME"];
        CEvent::SendImmediate ("MY_USER_PASS_CHANGED", SITE_ID, $toSend);
    }
    return $arFields;
}