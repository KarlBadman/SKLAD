<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetPageProperty('title', 'Личный кабинет');
$APPLICATION->SetTitle('Заказ №'.(int)$_REQUEST['ORDER_ID']);
?>
<?
$APPLICATION->IncludeComponent(
    'swebs:order.detail',
    '.default',
    array(
        'ORDER_ID' => $_REQUEST['ORDER_ID'],
        'COMPONENT_TEMPLATE' => '.default'
    ),
    false
);
?>
<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>