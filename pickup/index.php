<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Пункты самовывоза");
$APPLICATION->SetTitle("Пункты самовывоза");
$APPLICATION->AddChainItem("Пункты самовывоза");
$APPLICATION->AddViewContent('page_type', 'data-page-type="other-page"');
?>

<? $APPLICATION->IncludeComponent(
    "swebs:pickup",
    ".default",
    array(
        "TERMINAL_CODE" => $_REQUEST['TERMINAL_CODE'],
        "COMPONENT_TEMPLATE" => ".default",
		'KEY'=>\Dsklad\Config::getParam('api_key/yandex_map'),
    ),
    false
); ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
