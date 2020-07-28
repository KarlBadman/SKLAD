<?
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include.php';

if ($USER->IsAuthorized()) {
    LocalRedirect('/');
    exit();
}
?>
<?
$APPLICATION->IncludeComponent(
    'bitrix:main.register',
    'register_top',
    Array(
        'AUTH' => 'Y',
        'REQUIRED_FIELDS' => array('EMAIL'),
        'SET_TITLE' => 'N',
        'SHOW_FIELDS' => array('EMAIL', 'NAME', 'PERSONAL_PHONE'),
        'SUCCESS_PAGE' => '/',
        'USER_PROPERTY' => array(),
        'USER_PROPERTY_NAME' => '',
        'USE_BACKURL' => 'Y'
    )
);
?>