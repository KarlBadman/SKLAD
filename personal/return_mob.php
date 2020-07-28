<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Возврат товара");
?>

<? $APPLICATION->IncludeComponent(
    "swebs:order.return",
    "mobile",
    array(
        "ORDER_ID" => $_REQUEST['ORDER_ID'],
    ),
    false
); ?>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
