<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include.php';
?>

<?$APPLICATION->IncludeComponent(
    "bitrix:system.auth.form",
    "error_ajax",
    array(
        "FORGOT_PASSWORD_URL" => "",
        "PROFILE_URL" => "",
        "REGISTER_URL" => "",
        "SHOW_ERRORS" => "Y",
        "COMPONENT_TEMPLATE" => "error_ajax"
    ),
    false
);?>


