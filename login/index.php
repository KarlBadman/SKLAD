<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
\Bitrix\Main\Page\Asset::getInstance()->addJs('/local/assets/js/jquery.inputmask.min.js');
\Bitrix\Main\Page\Asset::getInstance()->addJs('/local/assets/js/jquery.inputmask-multi.js');
\Bitrix\Main\Page\Asset::getInstance()->addJs('/local/assets/js/jquery.inputmask-conf.js');
$APPLICATION->SetPageProperty('title', 'Вход на сайт — Дизайн Склад dsklad.ru');
$APPLICATION->SetTitle('Вход на сайт');

if ($USER->IsAuthorized()) {
    LocalRedirect('/', false, '301 Moved permanently');
}
?>
<?
$APPLICATION->IncludeComponent(
    'prmedia:user.autologin.form',
    '.default',
    [
        'CACHE_TYPE' => 'A',
        'CACHE_TIME' => '31000000'
    ],
    false
);
?>
<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>