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
<? if ($arParams['EMPTY'] == 'Y') : ?>
    <input type="text" name="city_name" placeholder="Введите ваш регион" id="autocomplete_head" value="<?= $arResult['LOCATION']['VALUE'] ?>"/>
<?else : ?>
    <input type="text" name="city_name" placeholder="Введите ваш регион" id="autocomplete_head" value=""/>
<?endif;?>
<input type="hidden" name="city_id" id="city_id_head" value="<?= $arResult['LOCATION']['DATA'] ?>"/>
