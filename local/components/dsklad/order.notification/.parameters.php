<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
    $arComponentParameters = [
        "GROUPS" => [],
        "PARAMETERS" => [
            "ACCOUNT_ID" => [
                "PARENT" => "BASE",
                "NAME" => GetMessage("COOKIE_LIVE_TIME"),
                "TYPE" => "STRING",
                "DEFAULT" => []
            ]
        ],
    ];