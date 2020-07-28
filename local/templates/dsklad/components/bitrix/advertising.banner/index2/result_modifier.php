<?php
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

preg_match("/href=\"(.+?)\"/", $arResult['BANNER'], $arMatch);
$arResult['HREF'] = $arMatch[1];
preg_match("/src=\"(.+?)\"/", $arResult['BANNER'], $arMatch);
$arResult['SRC'] = $arMatch[1];
if($arParams["TYPE"] == "in_index_5"){
	$arResult['SRC_1366'] = CFile::ResizeImageGet($arResult["BANNER_PROPERTIES"]["IMAGE_ID"], array('width'=>1366, 'height'=>10000), BX_RESIZE_IMAGE_PROPORTIONAL)["src"];
	$arResult['SRC_1024'] = CFile::ResizeImageGet($arResult["BANNER_PROPERTIES"]["IMAGE_ID"], array('width'=>1024, 'height'=>10000), BX_RESIZE_IMAGE_PROPORTIONAL)["src"];
	$arResult['SRC_768'] = CFile::ResizeImageGet($arResult["BANNER_PROPERTIES"]["IMAGE_ID"], array('width'=>768, 'height'=>10000), BX_RESIZE_IMAGE_PROPORTIONAL)["src"];
	$arResult['SRC_320'] = CFile::ResizeImageGet($arResult["BANNER_PROPERTIES"]["IMAGE_ID"], array('width'=>320, 'height'=>10000), BX_RESIZE_IMAGE_PROPORTIONAL)["src"];
}
if($arParams["TYPE"] == "in_index_2" || $arParams["TYPE"] == "in_index_3" || $arParams["TYPE"] == "in_index_4" || $arParams["TYPE"] == "in_index_6" || $arParams["TYPE"] == "in_index_9"){
	$arResult['SRC_1366'] = CFile::ResizeImageGet($arResult["BANNER_PROPERTIES"]["IMAGE_ID"], array('width'=>1366/3, 'height'=>10000), BX_RESIZE_IMAGE_PROPORTIONAL)["src"];
	$arResult['SRC_1024'] = CFile::ResizeImageGet($arResult["BANNER_PROPERTIES"]["IMAGE_ID"], array('width'=>1024/3, 'height'=>10000), BX_RESIZE_IMAGE_PROPORTIONAL)["src"];
	$arResult['SRC_768'] = CFile::ResizeImageGet($arResult["BANNER_PROPERTIES"]["IMAGE_ID"], array('width'=>768/3, 'height'=>10000), BX_RESIZE_IMAGE_PROPORTIONAL)["src"];
	$arResult['SRC_320'] = CFile::ResizeImageGet($arResult["BANNER_PROPERTIES"]["IMAGE_ID"], array('width'=>320/3, 'height'=>10000), BX_RESIZE_IMAGE_PROPORTIONAL)["src"];
}
if($arParams["TYPE"] == "in_index_7" || $arParams["TYPE"] == "in_index_8"){
	$arResult['SRC_1366'] = CFile::ResizeImageGet($arResult["BANNER_PROPERTIES"]["IMAGE_ID"], array('width'=>1366/3*2, 'height'=>10000), BX_RESIZE_IMAGE_PROPORTIONAL)["src"];
	$arResult['SRC_1024'] = CFile::ResizeImageGet($arResult["BANNER_PROPERTIES"]["IMAGE_ID"], array('width'=>1024/3*2, 'height'=>10000), BX_RESIZE_IMAGE_PROPORTIONAL)["src"];
	$arResult['SRC_768'] = CFile::ResizeImageGet($arResult["BANNER_PROPERTIES"]["IMAGE_ID"], array('width'=>768/3*2, 'height'=>10000), BX_RESIZE_IMAGE_PROPORTIONAL)["src"];
	$arResult['SRC_320'] = CFile::ResizeImageGet($arResult["BANNER_PROPERTIES"]["IMAGE_ID"], array('width'=>320/3*2, 'height'=>10000), BX_RESIZE_IMAGE_PROPORTIONAL)["src"];
}