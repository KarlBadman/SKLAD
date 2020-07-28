<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Возврат товара");
?>

<? $APPLICATION->IncludeComponent(
    "swebs:order.return.doc",
    "mobile",
    array(
        'EMAIL' => 'info@dsklad.ru'
    ),
    false
); ?>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
