<?php
/**
 * User: a.kobetskoy
 * Date: 09.04.2019
 */

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

class speedMonitor{
    const USERNAME = "feedgenarator";
    const PASSWD = "eDQDy5FerT3UH4IaIb7L8CUSXnjtbeg5";
    const PARAMS = [
        "strategy" => 'mobile'
    ];
    const URL_LIST = [
        "https://www.dsklad.ru" => 'home',
        "https://www.divan.ru" => 'home',
        "https://hoff.ru" => 'home',
        "https://www.cosmorelax.ru" => 'home',
        "https://www.ikea.com/ru/ru" => 'home',
        "https://floberis.ru" => 'home',
        "https://lifemebel.ru" => 'home',
        "https://dg-home.ru" => 'home',
        "https://www.dsklad.ru/catalog/stylia/stul_eames_style_dsw/" => 'product',
        "http://www.prokuhni.com" => 'home',
    ];

    public function __construct()
    {
        if (!$this->checkauth()) {
            header("HTTP/1.0 403 Forbidden");
            exit;
        }
        require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    }

    /**
     * Проверяем авторизацию
     * @return bool
     */
    private function checkauth () {
        if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == self::USERNAME && $_SERVER['PHP_AUTH_PW'] == self::PASSWD) {
            return true;
        }
    }

    /**
     * Get speed information for each domain and write to DB
     * @return bool
     */
    public function writeSpeedData(){
        $result = [];
        foreach(self::URL_LIST as $url => $page_type) {
            $result[$url] = $this->getPageSpeedData($url, $page_type);
        }

        return $result;
    }

    /**
     * Get speed information
     * @param $url
     * @return bool|float
     */
    private function getPageSpeedData($pure_url, $page_type)
    {
        $type = '&strategy=' . self::PARAMS['strategy'];
        $url = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=' . $pure_url . $type;
        if (function_exists('file_get_contents')) {
            $result = @file_get_contents($url);
        }
        if ($result == '') {
            $ch = curl_init();
            $timeout = 60;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $result = curl_exec($ch);
            curl_close($ch);
        }


        return $this->writeDataToDb($pure_url, $result, $page_type);
    }

    /**
     * Write info to DB
     * @param $url
     * @param $json_result
     * @return bool|float
     */
    private function writeDataToDb ($url, $json_result, $page_type) {
        $result = (json_decode($json_result, true));
        try {
            require_once (realpath(__DIR__) . "/xtraspeedmonitortableorm.php");
            if (empty($json_result) || empty($result) )
                return false;
            $speed_index = $result['lighthouseResult']['categories']['performance']['score'];
            $audits = $result['lighthouseResult']['audits'];
            $arFields = [
                "domain" => $url,
                "type" => $page_type,
                "response" => '',
                "speed_index" => $speed_index,
                "LE_FCP_H" => $result['loadingExperience']['metrics']['FIRST_CONTENTFUL_PAINT_MS']['distributions'][0]['proportion'],
                "LE_FCP_M" => $result['loadingExperience']['metrics']['FIRST_CONTENTFUL_PAINT_MS']['distributions'][1]['proportion'],
                "LE_FCP_S" => $result['loadingExperience']['metrics']['FIRST_CONTENTFUL_PAINT_MS']['distributions'][2]['proportion'],
                "LE_FID_H" => $result['loadingExperience']['metrics']['FIRST_INPUT_DELAY_MS']['distributions'][0]['proportion'],
                "LE_FID_M" => $result['loadingExperience']['metrics']['FIRST_INPUT_DELAY_MS']['distributions'][1]['proportion'],
                "LE_FID_S" => $result['loadingExperience']['metrics']['FIRST_INPUT_DELAY_MS']['distributions'][2]['proportion'],
                "OE_FCP_H" => $result['originLoadingExperience']['metrics']['FIRST_CONTENTFUL_PAINT_MS']['distributions'][0]['proportion'],
                "OE_FCP_M" => $result['originLoadingExperience']['metrics']['FIRST_CONTENTFUL_PAINT_MS']['distributions'][1]['proportion'],
                "OE_FCP_S" => $result['originLoadingExperience']['metrics']['FIRST_CONTENTFUL_PAINT_MS']['distributions'][2]['proportion'],
                "OE_FID_H" => $result['originLoadingExperience']['metrics']['FIRST_INPUT_DELAY_MS']['distributions'][0]['proportion'],
                "OE_FID_M" => $result['originLoadingExperience']['metrics']['FIRST_INPUT_DELAY_MS']['distributions'][1]['proportion'],
                "OE_FID_S" => $result['originLoadingExperience']['metrics']['FIRST_INPUT_DELAY_MS']['distributions'][2]['proportion'],
                "audits_FD"             => $audits['font-display']['score'],
                "audits_FCP3G_score"    => $audits['first-contentful-paint-3g']['score'],
                "audits_FCP3G_time"     => $this->getCleanTiming($audits['first-contentful-paint-3g']['displayValue']),
                "audits_EIL"            => $audits['estimated-input-latency']['score'],
                "audits_BT_score"       => $audits['bootup-time']['score'],
                "audits_BT_time"        => $this->getCleanTiming($audits['bootup-time']['displayValue']),
                "audits_SI_score"       => $audits['speed-index']['score'],
                "audits_SI_time"        => $this->getCleanTiming($audits['speed-index']['displayValue']),
                "audits_FCI"            => $audits['first-cpu-idle']['score'],
                "audits_MWB_score"      => $audits['mainthread-work-breakdown']['score'],
                "audits_MWB_time"       => $this->getCleanTiming($audits['mainthread-work-breakdown']['displayValue']),
                "audits_FCP_score"      => $audits['first-contentful-paint']['score'],
                "audits_FCP_time"       => $this->getCleanTiming($audits['first-contentful-paint']['displayValue']),
                "audits_CRC"            => $this->getCleanTiming($audits['critical-request-chains']['displayValue']),
                "audits_DS"             => $audits['dom-size']['score'],
                "audits_DS_nodes"       => str_replace(',','',$this->getCleanTiming($audits['dom-size']['displayValue'])),
                "audits_UJ"             => $audits['unminified-javascript']['score'],
                "audits_FMP_score"      => $audits['first-meaningful-paint']['score'],
                "audits_FMP_time"       => $this->getCleanTiming($audits['first-meaningful-paint']['displayValue']),
                "audits_TTF_score"      => $audits['time-to-first-byte']['score'],
                "audits_TTF_time"       => $this->getCleanTiming($audits['time-to-first-byte']['displayValue']),
                "audits_RBR"            => $audits['render-blocking-resources']['score'],
                "audits_UTC"            => $audits['uses-text-compression']['score'],
                "audits_ULCT"           => $audits['uses-long-cache-ttl']['score'],
                "audits_interactive_score" => $audits['interactive']['score'],
                "audits_interactive_time" => $this->getCleanTiming($audits['interactive']['displayValue']),
                "timing"                => $result['lighthouseResult']['timing']['total'],
            ];
            $SPEED_ID = XtraSpeedMonitorTable::add($arFields);
            if($SPEED_ID->isSuccess()) {
                return $speed_index;
            }
        } catch (Exception $e) {
            return false;
        }
        return false;
    }

    private function getCleanTiming($string) {
        return trim(preg_replace('/([a-zA-Z|\s])/', '', $string));
    }
}

$speedInfo = new speedMonitor();
foreach($speedInfo->writeSpeedData() as $key => $value){
    echo '<p>' . $key . ': ' . $value;
}