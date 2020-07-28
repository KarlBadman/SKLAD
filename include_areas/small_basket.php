<?
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
?>
<?
$APPLICATION->IncludeComponent(
    'swebs:small.basket',
    '.default',
    array(),
    false
);
?>