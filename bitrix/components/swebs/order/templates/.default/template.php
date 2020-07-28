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
$this->setFrameMode(false);
use Bitrix\Main\Page\Asset;
Asset::getInstance()->addString('
<script type="text/javascript">
    window.order_ajax_path = "'.$componentPath.'";
</script>');
if ($arResult['STATUS'] == 'success'){
    localRedirect($arParams['ORDER_SUCCESS_PAGE']);
}elseif($arParams['IS_SUCCESS_PAGE']=='Y'){
    include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/success.php';
}elseif ($arResult['STATUS'] == 'payment') {
    include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/payment.php';
} elseif(count($arResult['BASKET_ITEMS'])<1){
    include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/empty.php';
} else {
    include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/process.php';
}


