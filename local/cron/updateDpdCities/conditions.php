<?

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

    if (empty($_SERVER['DOCUMENT_ROOT'])) {
        $_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../../../');
    }

    try {
        
        $workDir = 'local/cron/updateDpdCities';
        $sDateStart = date('Y.m.d H:i:s');
        $logPath = $workDir.'/log/updateDpdCities_' . date('d_m_Y') . '.log';
        // CheckDirPath($_SERVER['DOCUMENT_ROOT'].'/'.$workDir.'/log/');
        
        $log = [];
        
        $importFile = fopen($_SERVER['DOCUMENT_ROOT'].'/'.$workDir.'/conditions.csv', 'r');
        if (!$importFile) {
            throw new \Exception('Нет файла conditions.csv с данными для импорта');
        }
        
        global $arCityConditions; $arCityConditions = [];
        
        if (!defined('UPDATE_CITIES_TRIGER')) {
            
            require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
            
            \Bitrix\Main\Loader::includeModule('highloadblock');
            
            $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(DPD_CITIES_HL_ID)->fetch();
            $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
            $strEntityDataClass = $obEntity->getDataClass();
        }
        
        while (!feof($importFile)) {
            $line = fgetcsv($importFile, 0, ';');
            if (array_search('UF_CITYID', $line) !== false) continue;
            if (!empty($line[0])) {
                $arLine = array(
                    "D_WEEKEND_1" => isset($line[3]) ? $line[3] : false,
                    "D_WEEKEND_2" => isset($line[4]) ? $line[4] : false,
                    "D_EVENING" => isset($line[5]) ? $line[5] : false,
                    "D_UP_LIFT" => isset($line[6]) ? $line[6] : false
                );
                $arCityConditions[$line[0]] = $arLine;
            }
            
            if (!defined('UPDATE_CITIES_TRIGER')) {
                $arCity = $strEntityDataClass::getList(['filter' => ['UF_CITYID' => $line[0]],'select' => ['ID','UF_CITYID']])->fetch();
                if ($arCity['UF_CITYID'] == $line[0]) {
                    $arFields = array(
                        "UF_CONDITIONS" => getCityConditions($line[0])
                    );
                    $res = $strEntityDataClass::Update((int)$arCity['ID'], $arFields);
                    if (!$res->isSuccess()) {
                        $log['UPDATE_ERROR'][] = [
                            'City ID' => $sCityId,
                            'HL_Item_ID' => $ID,
                            'Error' => $res->getErrorMessages()
                        ];
                    }
                }
            }
        }
        
            
        
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
    
    if (!defined('UPDATE_CITIES_TRIGER'))
        die('YC');
    
    // Get city conditions JSON string
    function getCityConditions ($arCityID) {
        global $arCityConditions;
        return isset($arCityConditions[$arCityID]) ? json_encode($arCityConditions[$arCityID]) : false;
    }
