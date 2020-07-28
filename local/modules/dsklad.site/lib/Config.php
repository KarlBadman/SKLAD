<?php
namespace Dsklad;

use \Bitrix\Main\Config\Configuration;
use \Bitrix\Main\Loader;
use \Bitrix\Highloadblock\HighloadBlockTable;

class Config
{
    protected static $isLoad = false;
    protected static $config = [];
    protected static $settings = [];
    protected static $modes = [
        'dev' => ['file' => 'dev.php'],
        'prod' => ['file' => 'prod.php']
    ];

    /**
     * Загружает конфигурацию
     */
    protected static function load()
    {
        if (!self::$isLoad) {

            $typeMode = self::getMode();
            $mode = self::$modes[$typeMode];

            if (empty($mode)) throw new \Exception("Тип конфигурации " . $typeMode . " не обнаружен.");

            $file = realpath(__DIR__ . '/../') . '/config/' . $mode['file'];

            if (!file_exists($file)) throw new \Exception("Путь до конфигурации " . $typeMode . " [" . $file . "] не обнаружен.");

            self::$config = include_once($file);

            if (empty(self::$config)) throw new \Exception("Конфигурация " . $typeMode . " не обнаружена.");

            self::$isLoad = true;
        }
    }

    /**
     * Возвращает значение параметра
     * @param $paramPath
     * @return mixed
     * @throws \Exception
     */
    public static function getParam($paramPath)
    {
        self::load();

        list($section, $param) = explode('/', $paramPath);

        if (!isset(self::$config[$section][$param])) {
            throw new \Exception("Не найден параметр в файле конфигурации по коду " . $paramPath);
        }

        return self::$config[$section][$param];
    }

    /**
     * Определяем конфигурацию
     * \Bitrix\Main\Config\Configuration::setValue('project_mode', 'prod');
     */
    public static function getMode()
    {
        $mode = Configuration::getValue("project_mode");

        return !empty($mode) ? $mode : "dev";
    }

    /**
     * Получает параметры из HB
     * @param $field
     * @return array|mixed|null|string
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getOption($field)
    {
        if (empty(self::$settings)) {

            if (Loader::includeModule('highloadblock')) {

                $hlblock = HighloadBlockTable::getRow([
                    'filter' => ['=NAME' => 'Settings']
                ]);

                $entity = HighloadBlockTable::compileEntity($hlblock);

                if (is_object($entity)) {
                    $res = $entity->getDataClass();
                    self::$settings = $res::getRow([]);
                }
            }
        }

        $result = !empty($field) ? self::$settings[$field] : self::$settings;

        return is_string($result) ? trim($result) : $result;
    }
}

?>