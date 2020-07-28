<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("CFO_NAME"),
	"DESCRIPTION" => GetMessage("CFO_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "dsklad",
		    "CHILD" => array(
			"ID" => "crutch_for_order",
			"NAME" => GetMessage("CFO_NAME"),
		),
	),
	"COMPLEX" => "N",
);

?>