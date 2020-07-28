<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("DMAP_NAME"),
	"DESCRIPTION" => GetMessage("DMAP_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "dsklad",
		    "CHILD" => array(
			"ID" => "map.yandex",
			"NAME" => GetMessage("DMAP_NAME"),
		),
	),
	"COMPLEX" => "N",
);

?>