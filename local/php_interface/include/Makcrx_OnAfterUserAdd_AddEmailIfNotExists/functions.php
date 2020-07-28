<?php
function Makcrx_OnAfterUserAdd_AddEmailIfNotExists(&$arFields) {
    global $USER;
    
    $userId = $arFields['ID'];
    if ($userId > 0) {
        if (!empty($arFields['EMAIL'])) {
            
            if ((stripos($arFields['EMAIL'], 'no_mail') !== false) && (stripos($arFields['EMAIL'], '@dsklad.ru') !== false)) {
                $arFields['EMAIL'] = "";
            }
            
            if ((stripos($arFields['EMAIL'], 'user_') !== false) && (stripos($arFields['EMAIL'], '@crm.com') !== false)) {
                $arFields['EMAIL'] = "";
            }
            
            $USER->Update($userId, $arFields);
        }
    }
}