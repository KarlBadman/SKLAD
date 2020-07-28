<?
    require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";

    if ($_GET['zabbix-get-data'] != '2c16c854f0b6beac7a3c6b24834cce2d') {

        exit;

    } else {

        if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
        $KEYAPI_RETAILCRM = COption::GetOptionString("intaro.retailcrm", "api_key");

        try {

            echo $KEYAPI_RETAILCRM;

        } catch (Exception $e) {

            echo $e->getMessage();

        }

    }

?>
