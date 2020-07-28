<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    $arComponentParameters = [
        "GROUPS" => [],
        "PARAMETERS" => [
            "HL_TABLE_NAME" => [
                "PARENT" => "BASE",
                "NAME" => GetMessage("HL_TABLE_NAME"),
                "TYPE" => "STRING",
                "DEFAULT" => [""]
            ],
            "COUNT" => [
                "PARENT" => "BASE",
                "NAME" => GetMessage("COUNT"),
                "TYPE" => "STRING",
                "DEFAULT" => ["10"]
            ],
            "FILTER" => [
                "PARENT" => "BASE",
                "NAME" => GetMessage("FILTER"),
                "TYPE" => "STRING",
                "DEFAULT" => ["*"]
            ],
            "SELECT" => [
                "PARENT" => "BASE",
                "NAME" => GetMessage("SELECT"),
                "TYPE" => "STRING",
                "DEFAULT" => ["*"]
            ],
            "SORT_ORDER" => [
                "PARENT" => "BASE",
                "NAME" => GetMessage("SORT_ORDER"),
                "TYPE" => "STRING",
                "DEFAULT" => ["DESC"]
            ],
            "SORT_FIELD" => [
                "PARENT" => "BASE",
                "NAME" => GetMessage("SORT_FIELD"),
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
?>