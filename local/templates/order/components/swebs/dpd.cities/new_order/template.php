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
$this->setFrameMode(true);
use Bitrix\Main\Page\Asset;

Asset::getInstance()->addString('
<script type="text/javascript">
    window.suggest = "/ajax/getSuggestation.php?HL_ID=' . $arParams['DPD_HL_ID'] . '";
</script>');
?>
<input class='js-city-name-element' type="text" name="<?=$arParams['PROP_NAME'];?>" id="autocomplete" value="<?= $arResult['LOCATION']['VALUE'] ?>" placeholder="Введите город"/>
<input class='js-city-id-element' type="hidden" name="city_id" id="city_id" value="<?= $arResult['LOCATION']['DATA'] ?>"/>