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
?>
<?if (!empty($arResult) && ($arResult["SUCCESS"] == "Y")):?>
<script>
$(function(){
	var successPopup = $('.success_popup_send_txt');
	setTimeout(function(){
		successPopup.find('.desc_popup_send_txt').find('span').html('Спасибо! Ваш запрос принят.');
		successPopup.addClass('active');
	}, 1000);

	setTimeout(function(){
		successPopup.removeClass('active');
	}, 6000);
});
</script>
<?endif?>