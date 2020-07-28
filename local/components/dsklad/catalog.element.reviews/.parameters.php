<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
#TODO в случае необходимости сортировки и т.п. нужно работать через mybusiness сейчас это не доработано, нужна авторизация по OAuth 2. Походу либа от гугла под php для получения токена
    $arComponentParameters = [
        "GROUPS" => [],
        "PARAMETERS" => [
            "ACCOUNT_ID" => [
                "PARENT" => "BASE",
                "NAME" => GetMessage("ACCOUNT_ID"),
                "TYPE" => "STRING",
                "DEFAULT" => []
            ],
            "LOCATION_ID" => [
                "PARENT" => "BASE",
                "NAME" => GetMessage("LOCATION_ID"),
                "TYPE" => "STRING",
                "DEFAULT" => []
            ],
            "PLACE_ID" => [
                "PARENT" => "BASE",
                "NAME" => GetMessage("PLACE_ID"),
                "TYPE" => "STRING",
                "DEFAULT" => []
            ],
            "AUTH_TOKEN" => [
                "PARENT" => "BASE",
                "NAME" => GetMessage("AUTH_TOKEN"),
                "TYPE" => "STRING",
                "DEFAULT" => []
            ],
            "USE_MYBUSINESS" => [
                "PARENT" => "BASE",
                "NAME" => GetMessage("USE_MYBUSINESS"),
                "TYPE" => "STRING",
                "DEFAULT" => []
            ],
            "USE_CACHE" => [
                "PARENT" => "BASE",
                "NAME" => GetMessage("USE_CACHE"),
                "TYPE" => "STRING",
                "DEFAULT" => []
            ], 
            "CACHE_TIME" => [
                "PARENT" => "BASE",
                "NAME" => GetMessage("CACHE_TIME"),
                "TYPE" => "STRING",
                "DEFAULT" => []
            ]
        ],
    ];