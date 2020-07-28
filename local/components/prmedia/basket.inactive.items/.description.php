<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$arComponentDescription = array(
    'NAME' => 'Неактивные товары в корзине',
    'DESCRIPTION' => 'Удаляет неактивные товары из корзины, показывает попап с уведомлением',
    'SORT' => 20,
    'PATH' => array(
        'ID' => 'BX.COMPONENT.DSKLAD',
        'NAME' => 'Кастомные компоненты (DSKLAD)',
        'SORT' => 2000
    ),
    'CACHE_PATH' => 'Y',
    'COMPLEX' => 'N'
);
