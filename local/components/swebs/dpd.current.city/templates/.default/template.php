<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$frame = $this->createFrame()->begin("Загрузка...");

$_SESSION['DPD_CITY_NAME'] =$arResult['CITY'];
if($arParams['HIDE_CITY_NAME'] != 'Y') {
    echo $arResult['CITY'];
}
$frame->end();?>
