<?php
    
    require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";
    $script = $_REQUEST['script'];
    $remote_script = 'http://dev.retailcrm.services:8080/scripts/' . $script . '.script';
    $hash = $_REQUEST['hash'];
    
    $siteUrl = $_SERVER['SERVER_NAME'];


    if (!$script || !$remote_script || $hash != 'DF3x23jsf32Lm3mcD9FDSp895') {
        $subject = "[$siteUrl] Не удалось обновить скрипт $script.";
        $message = "[$siteUrl] Не удалось обновить скрипт $script. Переданы неправильные параметры:\n" . print_r($_REQUEST, true);
        extra_log([
            "entity_type" => "retailcrmapi",
            "entity_id" => "120029",
            "exception_type" => "retailcrmapi_event",
            "exception_entity" => "retailcrmapi_updatescript_wrong_params",
            "exception_text" => $message,
            "mail_comment" => $message
        ]);
        die();
    }
    
    $data = file_get_contents($remote_script);
    
    if ($data) {
    
        $f = file_put_contents(__DIR__ . '/' . $script, $data);
        if (!$f) {
            $subject = "[$siteUrl] Не удалось обновить скрипт $script.";
            $message = "[$siteUrl] Не удалось обновить скрипт $script. Недостаточно прав.";
            extra_log([
                "entity_type" => "retailcrmapi",
                "entity_id" => "120027",
                "exception_type" => "retailcrmapi_event",
                "exception_entity" => "retailcrmapi_permission_denied",
                "exception_text" => $message,
                "mail_comment" => $message
            ]);
        }
    } else {
        $subject = "[$siteUrl] Не удалось обновить скрипт $script.";
        $message = "[$siteUrl] Не удалось обновить скрипт $script. Файл $remote_script отсутствует на сервере.";
        extra_log([
            "entity_type" => "retailcrmapi",
            "entity_id" => "120025",
            "exception_type" => "retailcrmapi_event",
            "exception_entity" => "retailcrmapi_file_is_not_exist",
            "exception_text" => $message,
            "mail_comment" => $message
        ]);
    }