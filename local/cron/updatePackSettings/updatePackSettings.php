<?php
use \Bitrix\Main\Diag\Debug;
use \Bitrix\Main\Web;

define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
define('NO_AGENT_STATISTIC', true);
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', true);
define('AMQP_WITHOUT_SIGNALS', true);
define('BX_NO_ACCELERATOR_RESET', true);

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../../../');
}

if (!flock($lock_file = fopen(__FILE__ . '.lock', 'w'), LOCK_EX | LOCK_NB)) {
    die("Скрипт уже запущен\n");
}

try {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

    @set_time_limit(0);
    @error_reporting(E_WARNING);
    @ini_set('memory_limit', '1024M');
    @ini_set('display_errors', 'on');
    @ini_set('output_buffering', 'off');
    @ini_set('mbstring.func_overload',  '0');
    @ini_set('auto_detect_line_endings', true);

    $workDir = 'local/cron/updatePackSettings'; // директория из которой вызван. Относительно неё сохраняются все используемые файлы
    $sDateStart = date('Y.m.d H:i:s');
    $logPath = $workDir.'/log/updatePackSettings_' . date('d_m_Y') . '.log';
    CheckDirPath($_SERVER['DOCUMENT_ROOT'].'/'.$workDir.'/log/');

    $log = [];

    $params = [
        'url' => ADDRESS_1C_SERVICES . '/UTDSklad/hs/getk/koef/',
        'login' => 'webservup',
        'pass' => 'Pfujnjdrf5'
    ];
    $response = query($params);

    \Bitrix\Main\Loader::includeModule('highloadblock');

    $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(SETTINGS_HL_ID)->fetch();
    $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
    $strEntityDataClass = $obEntity->getDataClass();

    $rsData = $strEntityDataClass::getList([
        'order' => [
            'ID' => 'ASC'
        ],
        'select' => [
            'ID'
        ],
        'limit' => 1
    ]);
    if ($arItem = $rsData->fetch()) {
        $arFields = dataPrepare($response['МассивКоэффициентов']);

        $res = $strEntityDataClass::Update((int)$arItem['ID'], $arFields);
        if (!$res->isSuccess()) {
            $log['UPDATE_ERROR'][] = [
                'HL_Item_ID' => $arItem['ID'],
                'Error' => $res->getErrorMessages()
            ];
        }
    } else {
        throw new \Exception('Пустой HL-блок');
    }

    \Bitrix\Main\Diag\Debug::writeToFile(
        [
            'Дата' => date('d.m.Y H:i:s').substr((string)microtime(), 1, 8),
            'Время выполнения' => round(microtime(true) - $sDateStart, 4),
            'Ответ сервера' => $response,
            'Ошибки при выполнении' => $log
        ],
        'IMPORT Complete',
        $logPath
    );
} catch (\Exception $e) {
    Debug::writeToFile(
        [
            'Дата' => date('d.m.Y H:i:s').substr((string)microtime(), 1, 8),
            'Код ошибки' => $e->getCode(),
            'Сообщение' => $e->getMessage(),
            'Трассировка' => $e->getTraceAsString()
        ],
        'IMPORT Exception',
        $logPath
    );

    die('ERROR');
}

die('Y');

/**
 *  Подготавливает массив для добавления в HL.
 * @param array $packsData
 * @return array
 */
function dataPrepare(array $packsData)
{
    $result = [];
    foreach ($packsData as $pack) {
        switch (substr(strtolower($pack['Наименование']), 0, strpos($pack['Наименование'], ' '))) {
            case 'мелкие':
                $result['UF_VOLUME_SMALL'] = (float) str_replace(',', '.', $pack['Минимум']);
                $result['UF_COEFF_SMALL'] = (float) str_replace(',', '.', $pack['Коэффициент']);
                break;
            case 'средние':
                $result['UF_VOLUME_MEDIUM'] = (float) str_replace(',', '.', $pack['Минимум']);
                $result['UF_COEFF_MEDIUM'] = (float) str_replace(',', '.', $pack['Коэффициент']);
                break;
            case 'крупные':
                $result['UF_VOLUME_BIG'] = (float) str_replace(',', '.', $pack['Минимум']);
                $result['UF_COEFF_BIG'] = (float) str_replace(',', '.', $pack['Коэффициент']);
                break;
        }
    }

    return $result;
}

/**
 * Совершает запрос к серверу 1С
 * @param array $params
 * @return mixed
 * @throws \Exception
 */
function query(array $params = [])
{
    $options = [
        'redirect' => true, // true, если нужно выполнять редиректы
        'redirectMax' => 5, // Максимальное количество редиректов
        'waitResponse' => true, // true - ждать ответа, false - отключаться после запроса
        'socketTimeout' => 5, // Таймаут соединения, сек
        'streamTimeout' => 10, // Таймаут чтения ответа, сек, 0 - без таймаута
        'version' => Web\HttpClient::HTTP_1_1, // версия HTTP (HttpClient::HTTP_1_0 или HttpClient::HTTP_1_1)
        'compress' => false, // true - принимать gzip (Accept-Encoding: gzip)
        'charset' => '', // Кодировка тела для POST и PUT
        'disableSslVerification' => false // true - отключить проверку ssl (с 15.5.9)
    ];

    /** @doc https://mrcappuccino.ru/blog/post/work-with-http-bitrix-d7 */
    $httpClient = new Web\HttpClient($options);

    $httpClient->setHeader('Content-Type', 'application/json', true);

    $httpClient->setAuthorization(
        $params['login'],
        $params['pass']
    );

    $httpClient->query(
        $params['post'] ? Web\HttpClient::HTTP_POST : Web\HttpClient::HTTP_GET,
        $params['url'],
        !empty($params['query']) ? Web\Json::encode($params['query']) : ''
    );

    $errors = $httpClient->getError();
    if (!empty($errors)) {
        throw new \Exception(print_r($errors, true));
    }

    $result = Web\Json::decode($httpClient->getResult());

    return $result;
}