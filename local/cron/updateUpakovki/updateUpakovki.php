<?php

use \Bitrix\Main\Diag\Debug;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

//допустимое отклонение в объемах
$volume_deviation = 0.07;// вот этот покрывает 99% товаров
$volume_deviation = 0.0999;// А используем этот из-за Стул Chiavari (прозрачный)
$deviation_detect = array();

define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
define('NO_AGENT_STATISTIC', true);
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', true);
define('AMQP_WITHOUT_SIGNALS', true);
define('BX_NO_ACCELERATOR_RESET', true);

@set_time_limit(600);
@error_reporting(E_WARNING);
@ini_set('memory_limit', '1024M');
@ini_set('display_errors', 'on');
@ini_set('output_buffering', 'off');
@ini_set('mbstring.func_overload', '0');
@ini_set("auto_detect_line_endings", true);

\Bitrix\Main\Loader::includeModule('highloadblock');

// Подготавливает массив для добавления в HL
function dataPrepare($arUpakovkaData)
{
    return [
        'UF_ID' => $arUpakovkaData['ID'],
        'UF_WEIGHT' => $arUpakovkaData['WEIGHT'],
        'UF_LENGTH' => $arUpakovkaData['LENGTH'] / 100.0,
        'UF_WIDTH' => $arUpakovkaData['WIDTH'] / 100.0,
        'UF_HEIGHT' => $arUpakovkaData['HEIGHT'] / 100.0,
        'UF_QUANTITY' => $arUpakovkaData['QUANTITY'],
    ];
}

if (!flock($lock_file = fopen(__FILE__ . '.lock', 'w'), LOCK_EX | LOCK_NB)) {
    die("Скрипт уже запущен\n");
}

try {
    $workDir = 'local/cron/updateUpakovki'; // директория из которой вызван. Относительно неё сохраняются все используемые файлы
    $sDateStart = date('Y.m.d H:i:s');
    $logPath = $workDir . '/log/updateUpakovki_' . date('d_m_Y') . '.log';
    CheckDirPath($_SERVER['DOCUMENT_ROOT'] . '/' . $workDir . '/log/');

    $log = [];

    $importFile = fopen($_SERVER['DOCUMENT_ROOT'] . '/local/import/upakovki.csv', 'r');
    if (!$importFile) {
        throw new \Exception('Нет файла upakovki.csv с данными для импорта');
    }

    // Получаем очередную часть данных для импорта
    $arUpakovkiImport = [];
    $i = 0;
    while (!feof($importFile)) {
        $line = fgetcsv($importFile, 0, ';');
        if (!empty($line[0])) {
            
            $arLine = [
                'ID' => preg_replace('/[^0-9a-z-#]/i', '', $line[0]),
                'QUANTITY' => (int)$line[1],
                'WEIGHT' => (float)str_replace(',', '.', $line[2]),
                'LENGTH' => (float)str_replace(',', '.', $line[4]),
                'WIDTH' => (float)str_replace(',', '.', $line[5]),
                'HEIGHT' => (float)str_replace(',', '.', $line[3]),
            ];
            $arUpakovkiImport[$i]['filter'] = array('LOGIC' => 'AND', array('UF_ID' => $arLine['ID'], 'UF_QUANTITY' => $arLine['QUANTITY']));
            $arUpakovkiImport[$i]['data'] = dataPrepare($arLine);
            $i++;
        }
    }
    fclose($importFile);

    if (empty($arUpakovkiImport)) {
        throw new \Exception('Нет данных для импорта');
    }

    $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(DPD_UPAKOVKI_HL_ID)->fetch();
    $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
    $strEntityDataClass = $obEntity->getDataClass();

    $addedCount = 0;
    $updatedCount = 0;

    foreach ($arUpakovkiImport as $line => $arUpakovka) {
        $detect = array();
        $offer = CIBlockElement::GetList(Array(), Array("=XML_ID"=>$arUpakovka['data']['UF_ID']), false, false, array('ID', 'NAME', 'ACTIVE'))->Fetch();
        if(!$offer || $offer['ACTIVE']=='N'){ //Пропуск отсутсвующих и снятых товарных предложений
            continue;
        }

        $parent_xml = explode('#',$arUpakovka['data']['UF_ID'])[0];
        $parent = CIBlockElement::GetList(Array(), Array("=XML_ID"=>$parent_xml), false, false, array('ID', 'NAME', 'ACTIVE'))->Fetch();
        if($parent['ACTIVE']=='N'){ //Пропуск если снят весь товар
            continue;
        }

        $arUpakovka['data']["UF_DATE_MODIFIED"] = ConvertTimeStamp(time(), "FULL") ;
        $id = $strEntityDataClass::getList(
            array(
                'select' => array('ID'),
                'filter' => $arUpakovka['filter'],
                'limit' => 1
            )
        )->fetch();

        $volume = ($arUpakovka['data']['UF_LENGTH'] * $arUpakovka['data']['UF_WIDTH'] * $arUpakovka['data']['UF_HEIGHT']);

        if($arUpakovka['filter'][0]['UF_QUANTITY'] > 1){
            $first_pack = $strEntityDataClass::getList(
                array(
                    'select' => array('*'),
                    'filter' => array('LOGIC' => 'AND', array('UF_ID' => $arUpakovka['filter'][0]['UF_ID'], 'UF_QUANTITY' => 1)),
                    'limit' => 1
                )
            )->fetch();

            //Проверим что пришедшие данные не противоречат логике (бОльшие коробки не меньше малых) и пропустим
            $first_volume = ($first_pack['UF_LENGTH'] * $first_pack['UF_WIDTH'] * $first_pack['UF_HEIGHT']);
            $volume_diff = (($first_volume - $volume) > 0) ? $first_volume - $volume : 0;
            if ((($first_pack['UF_WEIGHT'] - $arUpakovka['data']['UF_WEIGHT']) > 0) || ($volume_diff > $volume_deviation )) {
                $detect = array('weight'=>$first_pack['UF_WEIGHT'], 'volume'=>$first_volume);
            }
        }

        if ($arUpakovka['data']['UF_WEIGHT'] == 0 || $volume == 0 || $detect) {
            $text = 'For ' . $offer['NAME'] . ' xml_id (uf_id) ' . $arUpakovka['data']['UF_ID'] . ' Weight or Volume for pack of 1 product';
            $text .= (count($detect) > 0) ? 'less than for ' . $arUpakovka['data']['UF_QUANTITY'] . '. For one: volume=' . $first_volume . ', weight=' . $first_pack['UF_WEIGHT'] . ';' : 'is empty.';
            $text .= ' Data to insert: volume=' . $volume . ', weight=' .$arUpakovka['data']['UF_WEIGHT'];
            $log['ADD_ERROR'][] = [
                'Line' => $line,
                'Operation' => 'comparing',
                'Error' => $text
            ];
            $deviation_detect[$line] = array(
                'name'=>'<tr><td>' . $offer['NAME'] . '</td>',
                'UF_QUANTITY'=>'<td>' . $arUpakovka['data']['UF_QUANTITY']. '</td>',
                'weight'=>'<td>' . $arUpakovka['data']['UF_WEIGHT']. '</td>',
                'first_weight'=>'<td>' . $detect['weight'] . '</td>',
                'volume'=>'<td>' . $volume . '</td>',
                'first_volume'=>'<td>' . $detect['volume'] . '</td>',
                'UF_ID'=>'<td>' . $arUpakovka['data']['UF_ID']. '</td></tr>',
            );
            continue;
        }

        if (!empty($id)) {// update
            $res = $strEntityDataClass::Update($id, $arUpakovka['data']);
            if (!$res->isSuccess() || $some) {
                $log['ADD_ERROR'][] = [
                    'Line' => $line,
                    'Operation' => 'updating',
                    'Error' => $res->getErrorMessages()
                ];
            } else {
                $updatedCount++;
            }
        } else {// add
            $res = $strEntityDataClass::Add($arUpakovka['data']);
            if (!$res->isSuccess()) {
                $log['ADD_ERROR'][] = [
                    'Line' => $line,
                    'Operation' => 'adding',
                    'Error' => $res->getErrorMessages()
                ];
            } else {
                $addedCount++;
            }
        }
        unset($parent_xml, $offer, $parent);
    }

    if ($deviation_counts = count($deviation_detect)) {

       // var_dump(array_map( 'strip_tags', $deviation_detect));
        $deviation_detect = array_map(function($item){return implode('', $item);}, $deviation_detect);
        $deviation_detect = '<style>td, th{border: 1px solid black;padding: 10px;}</style><table cellspacing="0" cellpadding="0" border="0"><thead><th>Товар</th><th>Кол-во</th><th>Вес</th><th>Вес 1 упаковки</th><th>Объем</th><th>Объем 1 упаковки</th><th>ID</th></thead><tbody>' . implode('', $deviation_detect) . '</tbody></table>';
        extra_log(
            array(
                'entity_type' => 'update_upakovki',
                'entity_id' => $deviation_counts,
                'exception_type' => 'strange_pack_gabarits',
                'exception_entity'=>'weight_or_volume_error',
                "exception_text" => implode(';\n', array_column($log['ADD_ERROR'], 'Error')),
                "mail_comment" => 'Зафиксировано расхождение по ВГХ упаковок. Либо расхождение в весе и объеме упаковок, либо оин пусты' . $deviation_detect
            )
        );
    }

    unset($res, $deviation_detect, $deviation_counts);

    $logMessage = '
        Time start: ' . $sDateStart . '
        Taked : ' . count($arUpakovkiImport) . ' packs,
        Updated: ' . $updatedCount . ' packs,
        Added: ' . $addedCount . ' packs
        Time end: ' . date('Y.m.s H:i:s');
    \Bitrix\Main\Diag\Debug::writeToFile(
        [
            'Дата' => date('d.m.Y H:i:s'),
            'Сообщение' => $logMessage,
            'Ошибки при выполнении' => print_r($log, true)
        ],
        'IMPORT Complete',
        $logPath
    );
} catch (\Exception $e) {
    if ($importFile !== false) {
        fclose($importFile);
    }

    Debug::writeToFile(
        [
            'Дата' => date('d.m.Y H:i:s'),
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