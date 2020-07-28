<?php
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Text\Encoding;
use \Bitrix\Main\Diag\Debug;

define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
define('NO_AGENT_STATISTIC', true);
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', true);
define('AMQP_WITHOUT_SIGNALS', true);
define('BX_NO_ACCELERATOR_RESET', true);
define('UPDATE_CITIES_TRIGER', true);

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../../../');
}

if (!flock($lock_file = fopen(__FILE__ . '.lock', 'w'), LOCK_EX | LOCK_NB)) {
    die("Скрипт уже запущен\n");
}

try {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/local/cron/updateDpdCities/conditions.php');

    $isTrigger = Option::get(
        'main',
        'update_dpd_cities_trigger'
    );

    if (!$isTrigger) {
        die('NO TRIGGER');
    }

    @set_time_limit(0);
    @error_reporting(E_WARNING);
    @ini_set('memory_limit', '1024M');
    @ini_set('display_errors', 'on');
    @ini_set('output_buffering', 'off');
    @ini_set('mbstring.func_overload',  '0');
    @ini_set("auto_detect_line_endings", true);

    $workDir = 'local/cron/updateDpdCities'; // директория из которой вызван. Относительно неё сохраняются все используемые файлы
    $sDateStart = date('Y.m.d H:i:s');
    $logPath = $workDir.'/log/updateDpdCities_' . date('d_m_Y') . '.log';
    CheckDirPath($_SERVER['DOCUMENT_ROOT'].'/'.$workDir.'/log/');

    $log = [];

    $importFile = fopen($_SERVER['DOCUMENT_ROOT'].'/'.$workDir.'/import.csv', 'r');
    if (!$importFile) {
        throw new \Exception('Нет файла import.csv с данными для импорта');
    }

    $startLineNum = (int)Option::get(
        'main',
        'update_dpd_cities_start',
        0
    );

    $limit = (int)Option::get(
        'main',
        'update_dpd_cities_limit',
        10000
    );

    //пропускаем уже обработанные строки
    $i = 0;
    while (!feof($importFile) && $i < $startLineNum) {
        if ($line = fgets($importFile)) {
            $i++;
        }
    }

    // Получаем очередную часть данных для импорта
    $arDpdCitiesById = [];
    $i = 0;
    while (!feof($importFile) && $i < $limit) {
        $line = fgetcsv($importFile, 0, ';');
        if (!empty($line[0])) {
            $arLine = [
                'CITY_ID' => $line[0],
                'COUNTRY_CODE' => substr($line[1], 0, 2),
                'REGION_CODE' => substr($line[1], 2, 2),
                'CITY_CODE' => substr($line[1], 2),
                'ABBREVIATION' => Encoding::convertEncoding($line[2], 'windows-1251', 'UTF-8'),
                'CITY_NAME' => Encoding::convertEncoding($line[3], 'windows-1251', 'UTF-8'),
                'REGION_NAME' => Encoding::convertEncoding($line[4], 'windows-1251', 'UTF-8'),
                'COUNTRY_NAME' => Encoding::convertEncoding($line[5], 'windows-1251', 'UTF-8'),
            ];

            $arDpdCitiesById[$arLine['CITY_ID']] = $arLine;
            $i++;
        }
    }

    // Получаем список терминалов сохраненных в HL-блоке.
    \Bitrix\Main\Loader::includeModule('highloadblock');

    $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(DPD_CITIES_HL_ID)->fetch();
    $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
    $strEntityDataClass = $obEntity->getDataClass();

    $arDpdCitiesInHL = [];
    $rsData = $strEntityDataClass::getList([
        'select' => [
            'ID',
            'UF_CITYID',
            'UF_SORT'
        ]
    ]);
    while ($arItem = $rsData->fetch()) {
        $arDpdCitiesInHL[$arItem['UF_CITYID']] = $arItem;
    }

    $arToAdd = [];    // Список кодов городов, которые нужно добавить из $arDpdCitiesById.
    $arToUpdate = []; // Список кодов городов, которые нужно обновить в HL-блоке.

    foreach ($arDpdCitiesInHL as $sCityId => $arCity) {
        if (array_key_exists($sCityId, $arDpdCitiesById)) {
            $arToUpdate[$sCityId] = $arCity['ID'];
        }
    }

    $arToAdd = array_keys(
        array_filter(
            $arDpdCitiesById,
            function ($key) {
                global $arDpdCitiesInHL;
                return !array_key_exists($key, $arDpdCitiesInHL);
            },
            ARRAY_FILTER_USE_KEY)
    );

    // Обновление ...
    $arResultUpdate = [];
    foreach ($arToUpdate as $sCityId => $ID) {
        $arDpdCitiesById[$sCityId]['SORT'] = $arDpdCitiesInHL[$sCityId]['UF_SORT'];
        $arFields = dataPrepare($arDpdCitiesById[$sCityId]);

        $res = $strEntityDataClass::Update((int)$ID, $arFields);
        if (!$res->isSuccess()) {
            $log['UPDATE_ERROR'][] = [
                'City ID' => $sCityId,
                'HL_Item_ID' => $ID,
                'Error' => $res->getErrorMessages()
            ];
        }
    }
    unset($arFields);
    unset($res);

    // Добавление ...
    $arResultAdd = [];
    foreach ($arToAdd as $sCityId) {
        $arFields = dataPrepare($arDpdCitiesById[$sCityId]);

        $res = $strEntityDataClass::Add($arFields);
        if (!$res->isSuccess()) {
            $log['ADD_ERROR'][] = [
                'City ID' => $sCityId,
                'Error' => $res->getErrorMessages()
            ];
        }
    }
    unset($arFields);
    unset($res);

    if (feof($importFile)) {
        $arToDelete = [];  // Список кодов городов, которые нужно удалить в HL-блоке.
        $rsData = $strEntityDataClass::getList([
            'filter' => [
                '!=UF_UPDATE' => date('Ymd')
            ],
            'select' => [
                'ID',
                'UF_CITYID'
            ]
        ]);
        while ($arItem = $rsData->fetch()) {
            $arToDelete[$arItem['UF_CITYID']] = $arItem['ID'];
        }

        // Удаление ...
        foreach ($arToDelete as $sCityId => $ID) {
            $res = $strEntityDataClass::Delete((int)$ID);
            if (!$res->isSuccess()) {
                $log['DELETE_ERROR'][] = [
                    'City ID' => $sCityId,
                    'HL_Item_ID' => $ID,
                    'Error' => $res->getErrorMessages()
                ];
            }
        }

        clearOptions();

        $logMessage = '
            Time start: '.$sDateStart.'
            Taked : '.count($arDpdCitiesById).' cities,
            Loaded from Database: '.count($arDpdCitiesInHL).' cities
            Deleted: '.count($arToDelete).' cities,
            Updated: '.count($arToUpdate).' cities
            Added: '.count($arToAdd).' cities
            Time end: '.date('Y.m.s H:i:s');
        \Bitrix\Main\Diag\Debug::writeToFile(
            [
                'Дата' => date('d.m.Y H:i:s'),
                'Сообщение' => $logMessage,
                'Параметры запуска' => [
                    'START' => $startLineNum,
                    'LIMIT' => $limit
                ],
                'Ошибки при выполнении' => print_r($log, true)
            ],
            'PROCESSING Complete',
            $logPath
        );
    } else {
        \Bitrix\Main\Config\Option::set(
            'main',
            'update_dpd_cities_start',
            $startLineNum + $limit
        );

        $logMessage = '
            Time start: '.$sDateStart.'
            Taked : '.count($arDpdCitiesById).' cities,
            Loaded from Database: '.count($arDpdCitiesInHL).' cities
            Updated: '.count($arToUpdate).' cities
            Added: '.count($arToAdd).' cities
            Time end: '.date('Y.m.s H:i:s');
        \Bitrix\Main\Diag\Debug::writeToFile(
            [
                'Дата' => date('d.m.Y H:i:s'),
                'Сообщение' => $logMessage,
                'Параметры запуска' => [
                    'START' => $startLineNum,
                    'LIMIT' => $limit
                ],
                'Ошибки при выполнении' => print_r($log, true)
            ],
            'PROCESSING Step Complete',
            $logPath
        );
    }
    fclose($importFile);
} catch (\Exception $e) {
    fclose($importFile);
    clearOptions();

    Debug::writeToFile(
        [
            'Дата' => date('d.m.Y H:i:s'),
            'Код ошибки' => $e->getCode(),
            'Сообщение' => $e->getMessage(),
            'Параметры запуска' => [
                'START' => $startLineNum,
                'LIMIT' => $limit
            ],
            'Трассировка' => $e->getTraceAsString()
        ],
        'PROCESSING Exception',
        $logPath
    );

    die('ERROR');
}

die('Y');

// Подготавливает массив для добавления в HL.
function dataPrepare($arCityData)
{
    return array(
        'UF_CITYID' => $arCityData['CITY_ID'],
        'UF_COUNTRYCODE' => $arCityData['COUNTRY_CODE'],
        'UF_COUNTRYNAME' => $arCityData['COUNTRY_NAME'],
        'UF_REGIONCODE' => $arCityData['REGION_CODE'],
        'UF_REGIONNAME' => implode(", ", array_reverse(explode(", ", $arCityData['REGION_NAME']))),
        'UF_CITYCODE' => $arCityData['CITY_CODE'],
        'UF_CITYNAME' => $arCityData['CITY_NAME'],
        'UF_ABBREVIATION' => $arCityData['ABBREVIATION'],
        'UF_SORT' => !empty($arCityData['SORT']) ? (int)$arCityData['SORT'] : 500,
        'UF_UPDATE' => date('Ymd'),
        'UF_CONDITIONS' => getCityConditions($arCityData['CITY_ID'])
    );
}

/**
 * @throws \Bitrix\Main\ArgumentNullException
 */
function clearOptions()
{
    Option::delete(
        'main',
        [
            'name' => 'update_dpd_cities_trigger',
            'site_id' => ''
        ]
    );

    Option::delete(
        'main',
        [
            'name' => 'update_dpd_cities_start',
            'site_id' => ''
        ]
    );

    Option::delete(
        'main',
        [
            'name' => 'update_dpd_cities_limit',
            'site_id' => ''
        ]
    );
}
