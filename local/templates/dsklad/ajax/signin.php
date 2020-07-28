<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include.php';
?>
<?
if ($USER->IsAuthorized()){
    LocalRedirect('/');
    exit();
}?>

<?$APPLICATION->IncludeComponent(
    "bitrix:system.auth.form",
    "auth_top",
    array(
        "FORGOT_PASSWORD_URL" => "",
        "PROFILE_URL" => "",
        "REGISTER_URL" => "",
        "SHOW_ERRORS" => "Y",
        "COMPONENT_TEMPLATE" => "auth_top"
    ),
    false
);?>


