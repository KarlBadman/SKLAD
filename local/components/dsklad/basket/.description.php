<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("B_NAME"),
	"DESCRIPTION" => GetMessage("B_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "dsklad",
		    "CHILD" => array(
			"ID" => "basket",
			"NAME" => GetMessage("B_NAME"),
		),
	),
	"COMPLEX" => "N",
);

?>