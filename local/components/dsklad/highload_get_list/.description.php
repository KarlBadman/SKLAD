<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    $arComponentDescription = array(
    	"NAME" => GetMessage("NAME"),
    	"DESCRIPTION" => GetMessage("DESCRIPTION"),
    	"ICON" => "/images/icon.gif",
    	"SORT" => 10,
    	"CACHE_PATH" => "Y",
    	"PATH" => array(
    		"ID" => "dsklad",
    		    "CHILD" => array(
    			"ID" => "highload_get_list",
    			"NAME" => GetMessage("NAME"),
    		),
    	),
    	"COMPLEX" => "N",
    );
?>