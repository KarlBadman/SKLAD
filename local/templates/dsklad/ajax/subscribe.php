<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';


if(!empty($_REQUEST['email'])){
    uniSenderSubscriber(array(
        "EMAIL" => $_REQUEST["email"],
        "NAME" => "",
        "ENTRY" => ""
    ));
}

