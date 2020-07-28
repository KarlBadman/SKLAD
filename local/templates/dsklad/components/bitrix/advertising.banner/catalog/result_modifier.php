<?php
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

preg_match("/href=\"(.+?)\"/", $arResult['BANNER'], $arMatch);
$arResult['HREF'] = $arMatch[1];
preg_match("/src=\"(.+?)\"/", $arResult['BANNER'], $arMatch);
$arResult['SRC'] = $arMatch[1];