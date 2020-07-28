<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Оплата");
?>
<? $APPLICATION->IncludeComponent(
    "swebs:sale.order.payment",
    "",
    Array()
); ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>