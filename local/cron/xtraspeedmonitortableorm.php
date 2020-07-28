<?

use Bitrix\Main;

class XtraSpeedMonitorTable extends Main\Entity\DataManager {

    public static function getTableName() {
        return 'xtra_speed_monitor';
    }

    public static function getMap() {
        return array(
            'id' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => 'ID field',
            ),
            'domain' => array(
                'data_type' => 'string',
                'title' => 'domain name',
            ),
            'type' => array(
                'data_type' => 'string',
                'title' => 'page type',
            ),
            'response' => array(
                'data_type' => 'string',
                'title' => 'full response field',
            ),
            'speed_index' => array(
                'data_type' => 'float',
                'title' => 'оценка скорости загрузки (PSI)',
            ),
            'LE_FCP_H' => array(
                'data_type' => 'float',
                'title' => 'Первая отрисовка контента (FCP) < 1сек',
            ),
            'LE_FCP_M' => array(
                'data_type' => 'float',
                'title' => 'Первая отрисовка контента (FCP) 1сек < <2.5сек',
            ),
            'LE_FCP_S' => array(
                'data_type' => 'float',
                'title' => 'Первая отрисовка контента (FCP) > 2.5сек',
            ),
            'LE_FID_H' => array(
                'data_type' => 'float',
                'title' => 'loadingExperience Первая задержка ввода (FID) < 1сек',
            ),
            'LE_FID_M' => array(
                'data_type' => 'float',
                'title' => 'loadingExperience Первая задержка ввода (FID) 1сек < <2.5сек',
            ),
            'LE_FID_S' => array(
                'data_type' => 'float',
                'title' => 'loadingExperience Первая задержка ввода (FID) > 2.5сек',
            ),
            'OE_FCP_H' => array(
                'data_type' => 'float',
                'title' => 'originLoadingExperience Первая отрисовка контента (FCP) < 1сек',
            ),
            'OE_FCP_M' => array(
                'data_type' => 'float',
                'title' => 'originLoadingExperience Первая отрисовка контента (FCP) 1сек < <2.5сек',
            ),
            'OE_FCP_S' => array(
                'data_type' => 'float',
                'title' => 'originLoadingExperience Первая отрисовка контента (FCP) > 2.5сек',
            ),
            'OE_FID_H' => array(
                'data_type' => 'float',
                'title' => 'originLoadingExperience Первая задержка ввода (FID)  < 1сек',
            ),
            'OE_FID_M' => array(
                'data_type' => 'float',
                'title' => 'originLoadingExperience Первая задержка ввода (FID) 1сек < <2.5сек',
            ),
            'OE_FID_S' => array(
                'data_type' => 'float',
                'title' => 'originLoadingExperience Первая задержка ввода (FID) > 2.5сек',
            ),
            'audits_FD' => array(
                'data_type' => 'float',
                'title' => 'Используется font-display',
            ),
            'audits_FCP3G_score' => array(
                'data_type' => 'float',
                'title' => 'Показатель времени загрузки первого контента (First Contentful Paint) 3G',
            ),
            'audits_FCP3G_time' => array(
                'data_type' => 'float',
                'title' => 'Время загрузки первого контента (First Contentful Paint) 3G',
            ),
            'audits_EIL' => array(
                'data_type' => 'float',
                'title' => 'Расчетная задержка ввода',
            ),
            'audits_BT_score' => array(
                'data_type' => 'float',
                'title' => 'Показатель времени загрузки js',
            ),
            'audits_BT_time' => array(
                'data_type' => 'float',
                'title' => 'Время загрузки js',
            ),
            'audits_SI_score' => array(
                'data_type' => 'float',
                'title' => 'Показатель скорости',
            ),
            'audits_SI_time' => array(
                'data_type' => 'float',
                'title' => 'Время',
            ),
            'audits_FCI' => array(
                'data_type' => 'float',
                'title' => 'Показатель первого простоя ЦП',
            ),
            'audits_MWB_score' => array(
                'data_type' => 'float',
                'title' => 'Показатель аудита разбивки работ(процессорное время на отрисовку)',
            ),
            'audits_MWB_time' => array(
                'data_type' => 'float',
                'title' => 'Время аудита разбивки работ(процессорное время на отрисовку)',
            ),
            'audits_FCP_score' => array(
                'data_type' => 'float',
                'title' => 'Показатель времени первой отрисовки контента',
            ),
            'audits_FCP_time' => array(
                'data_type' => 'float',
                'title' => 'Время первой отрисовки контента',
            ),
            'audits_CRC' => array(
                'data_type' => 'integer',
                'title' => 'Цепочка критических запросов',
            ),
            'audits_DS' => array(
                'data_type' => 'float',
                'title' => 'Показатель размера структуры DOM',
            ),
            'audits_DS_nodes' => array(
                'data_type' => 'integer',
                'title' => 'Количественный показатель размера структуры DOM',
            ),
            'audits_UJ' => array(
                'data_type' => 'float',
                'title' => 'Показатель неминимизированных js',
            ),
            'audits_FMP_score' => array(
                'data_type' => 'float',
                'title' => 'Показатель времени первой значимой отрисовки кадра',
            ),
            'audits_FMP_time' => array(
                'data_type' => 'float',
                'title' => 'Время первой значимой отрисовки кадра',
            ),
            'audits_TTF_score' => array(
                'data_type' => 'float',
                'title' => 'Показатель времени до первого байта',
            ),
            'audits_TTF_time' => array(
                'data_type' => 'integer',
                'title' => 'Время до первого байта',
            ),
            'audits_RBR' => array(
                'data_type' => 'float',
                'title' => 'Показатель блокирующих ресурсов',
            ),
            'audits_UTC' => array(
                'data_type' => 'float',
                'title' => 'Используется сжатие текстов',
            ),
            'audits_ULCT' => array(
                'data_type' => 'float',
                'title' => 'Используется длительный cache ttl',
            ),
            'audits_interactive_score' => array(
                'data_type' => 'float',
                'title' => 'Показатель времени до интерактивности',
            ),
            'audits_interactive_time' => array(
                'data_type' => 'float',
                'title' => 'Времени до интерактивности',
            ),
            'timing' => array(
                'data_type' => 'float',
                'title' => 'Общая продолжительность прохода Lighthouse',
            ),
            'created_at' => array(
                'data_type' => 'datetime',
                'title' => 'Created at field',
            ),
        );
    }
}