<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** Название модуля */
$module = basename(__DIR__);

/** Подгружаем первым класс конфигурации */
\Bitrix\Main\Loader::registerAutoLoadClasses($module, ['\Dsklad\Config' => 'lib/Config.php']);

/** Подключаем файл с настройками */
require_once __DIR__ . '/settings.php';

/** Подключаем вспомогательные функции */
require_once __DIR__ . '/fx.php';

/** Подгружаем модули */
is_array($settings['autoload']['modules']) && array_map(
    '\Bitrix\Main\Loader::includeModule',
    $settings['autoload']['modules']
);

/** Подгружаем классы */
!empty($settings['autoload']['lib']) && \Bitrix\Main\Loader::registerAutoLoadClasses(
    $module,
    $settings['autoload']['lib']
);

/** Подключаем обработчики для событий */
require_once __DIR__.'/handlers.php';