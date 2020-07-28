<?
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include.php';
?>
<?
$APPLICATION->IncludeComponent(
    'bitrix:system.auth.forgotpasswd',
    'forgotpass_top',
    array()
);
?>