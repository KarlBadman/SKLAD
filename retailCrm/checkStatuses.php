<?php
    $_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/..';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
    // echo "<pre>";
    // some test comment
    
    $lasterrordate = file_get_contents(__DIR__ . '/checkStatuses_lasterrordate');
    // var_dump($lasterrordate);
    // var_dump(date('Y-m-d'));
    if ($lasterrordate != '')
        die();
    
    $siteUrl = $_SERVER['SERVER_NAME'];
    
    CModule::IncludeModule('intaro.retailcrm');

    $statuses = unserialize(COption::GetOptionString('intaro.retailcrm', 'pay_statuses_arr', 0));
    $existStatuses = array_values($statuses);
    
    $api_host = COption::GetOptionString('intaro.retailcrm', 'api_host', 0);
    $api_key = COption::GetOptionString('intaro.retailcrm', 'api_key', 0);
    $api = new RetailCrm\ApiClient($api_host, $api_key);

    $statusesList = $api->statusesList()->statuses;
    $allStatuses = [];
    foreach ($statusesList as $status => $statusParams) {
        if ($statusParams['active'])
            $allStatuses[] = $status;
    }
    
    $errorStatuses = array_filter(array_diff($allStatuses, $existStatuses));
    
    if (count($errorStatuses) > 0) {
        $message = "На сайте $siteUrl проставлены не все статусы.\nНеобходимо проставить соответствия для статусов:\n";
        foreach ($errorStatuses as $status) {
            $message .= "$status (" . $statusesList[$status]['name'] . ")\n";
        }

        extra_log([
            "entity_type" => "retailcrm_integration",
            "entity_id" => "1200292",
            "exception_type" => "retailcrmapi_event",
            "exception_entity" => "retailcrm_integration_statuses_conformity",
            "exception_text" => $message,
            "mail_comment" => $message
        ]);


        file_put_contents(__DIR__ . '/checkStatuses_lasterrordate', date('Y-m-d'));
    }
    
    // echo "ok";