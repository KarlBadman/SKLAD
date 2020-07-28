<?php
    $_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/..';
    require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";
    
    $currentDate = date('Y-m-d H:i:s');
    
    ini_set('memory_limit','1G');

    $context = \Bitrix\Main\Application::getInstance()->getContext();
    $culture = array_map(trim, explode("\n", print_r($context->getCulture(), true)));
    $cultureParams = [];
    foreach ($culture as $row) {
        $row = array_map(trim, explode('=>', $row));
        if (count($row) == 2) {
            $cultureParams[$row[0]] = $row[1];
        }
    }
    
    $serverName = $cultureParams['[SERVER_NAME]'];

    if (file_exists(__DIR__ . '/orderAgent.lock')) {
        $date = file_get_contents(__DIR__ . '/orderAgent.lock');
        $seconds = strtotime($currentDate) - strtotime($date);
        $minutes = $seconds / 60;
        
        if ($minutes > 20) {

            extra_log([
                "entity_type" => "retailcrmapi",
                "entity_id" => "120013",
                "exception_type" => "retailcrmapi_event",
                "exception_entity" => "retailcrmapi_max_execution_time",
                "exception_text" => "[".$server['HTTP_HOST']."] Скрипт выгрузки выполняется слишком долго: более $minutes минут",
                "mail_comment" => "[".$server['HTTP_HOST']."] Скрипт выгрузки выполняется слишком долго: более $minutes минут"
            ]);
            
        }
        
        if ($minutes > 30) {
            unlink(__DIR__ . '/orderAgent.lock');
        }
        die();
    }

	if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
    
    file_put_contents(__DIR__ . '/orderAgent.lock', $currentDate);
    try {
        RCrmActions::orderAgent();
    } catch (Exception $e) {
        $msg = $e->getMessage();
        $trace = $e->getTraceAsString();
        
        extra_log([
            "entity_type" => "retailcrmapi",
            "entity_id" => "120033",
            "exception_type" => "retailcrmapi_event",
            "exception_entity" => "retailcrmapi_update_event_error",
            "exception_text" => $msg,
            "mail_comment" => $trace
        ]);
        die();
    }
    
    unlink(__DIR__ . '/orderAgent.lock');
    echo "ok";