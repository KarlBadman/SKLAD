<?php
function Makcrx_OnBeforeEventSend_DontSendToInvalidEmails(&$arFields) {

    function invalid_email($email) {
        // Исключаем отправку почты юзерам, с почтой, которую сгенерили сами
        if (empty($email) || (strpos($email, 'no_mail') !== false && strpos($email, '@dsklad.ru') !== false ) || (strpos($email, 'user_')  !== false && strpos($email, '@crm.com') !== false ))
            return true;
        return false;
    }

    if (invalid_email($arFields['EMAIL'])) {
        $arFields['EMAIL'] = 'dev@ooott.ru';
    }
    // file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/OnBeforeEventSend.log', print_r($arFields, true), FILE_APPEND);
}