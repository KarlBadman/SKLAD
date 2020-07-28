<?php
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

try {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

    @set_time_limit(0);
    @error_reporting(E_WARNING);
    @ini_set('memory_limit', '1024M');
    @ini_set('display_errors', 'on');
    @ini_set('output_buffering', 'off');
    @ini_set('mbstring.func_overload',  '0');

    $workDir = 'local/cron/updateDpdCities'; // директория из которой вызван. Относительно неё сохраняются все используемые файлы
    $sDateStart = date('Y.m.d H:i:s');
    $logPath = $workDir.'/log/updateDpdCities_' . date('d_m_Y') . '.log';
    CheckDirPath($_SERVER['DOCUMENT_ROOT'].'/'.$workDir.'/log/');

    $log = [];

    // Копируем файл с данными для импорта
    $remoteFile = 'ftp://ftp.dpd.ru/integration/GeographyDPD_'.date('Ymd').'.csv';
    $temporaryFileDescriptor = fopen($_SERVER['DOCUMENT_ROOT'].'/'.$workDir.'/import.csv', 'w');
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $remoteFile); #input
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FILE, $temporaryFileDescriptor); #output
    curl_setopt($curl, CURLOPT_USERPWD, 'integration:xYUX~7W98');
    if(curl_exec($curl) === false) {
        throw new \Exception('Ошибка curl: ' . curl_error($curl));
    }
    curl_close($curl);
    fclose($temporaryFileDescriptor);

    // инициализируем глобальные параметры
    \Bitrix\Main\Config\Option::set(
        'main',
        'update_dpd_cities_trigger',
        'Y'
    );

    \Bitrix\Main\Config\Option::set(
        'main',
        'update_dpd_cities_start',
        0
    );

    \Bitrix\Main\Config\Option::set(
        'main',
        'update_dpd_cities_limit',
        10000
    );

    \Bitrix\Main\Diag\Debug::writeToFile(
        [
            'Дата' => date('d.m.Y H:i:s'),
            'Сообщение' => 'TRIGGER START',
        ],
        'TRIGGER Complete',
        $logPath
    );
} catch (\Exception $e) {
    \Bitrix\Main\Diag\Debug::writeToFile(
        [
            'Дата' => date('d.m.Y H:i:s'),
            'Код ошибки' => $e->getCode(),
            'Сообщение' => $e->getMessage(),
            'Трассировка' => $e->getTraceAsString()
        ],
        'TRIGGER Exception',
        $logPath
    );

    die('ERROR');
}

die('Y');
