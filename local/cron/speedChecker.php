<?php
/**
 * User: a.kobetskoy
 * Date: 16.01.2019
 */

class speedChecker{
    const USERNAME = "feedgenarator";
    const PASSWD = "eDQDy5FerT3UH4IaIb7L8CUSXnjtbeg5";
    const HOST = 'http://www.1c-bitrix.ru/buy_tmp/ba.php';
    const PARAMS= [
        "license" => 'b1863c806fd6359836361ceca19e7c32',
        "op" => 'site_index',
        "domain" => "www.dsklad.ru",
        "aid" => '676af410c94e8568d3c03520651cfa5f',
        "tmz" => '-180'
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
     * Get speed information and start writing to database
     * @return bool
     */
    public function writeSpeedData(){
        $arParams = $this->getSpeeData();
        return $this->xtraSpeed($arParams);
    }

    /**
     * Caclulate speed values
     * @return mixed
     */
    private function getSpeeData() {
        $result = $this->getSpeedFromServer();
        $siteSpeedInfo['IndexTime'] = number_format(($result->p50/1000), 2, '.', ''); // значение скорости
        $siteSpeedInfo['IndexPercent'] = $result->p50/2500 * 100;
        $siteSpeedInfo['IndexPercent'] = min(max($siteSpeedInfo['IndexPercent'], 4), 98); // положение градусника на шкале (изначанально задает смещение для css)
        return $siteSpeedInfo;
    }

    /**
     * Get speed information from bitrix server
     * @return mixed
     */
    private function getSpeedFromServer() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::HOST);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query(self::PARAMS));
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }

    /**
     * Extra speed logging
     * @param $arParams
     * @return bool or params
     */
    private function xtraSpeed ($arParams) {

        try {
            require_once (realpath(__DIR__) . "/xtraspeedtableorm.php");
            if (
                !is_array($arParams) || empty($arParams)
                || empty($arParams['IndexTime']) || empty($arParams['IndexPercent'])
            )
                return false;

            $arFields = [
                "speed_time" => $arParams['IndexTime'],
                "speed_percents" => $arParams['IndexPercent'],
            ];
            $SPEED_ID = XtraSpeedTable::add($arFields);
            if($SPEED_ID->isSuccess()) {
                return $arParams;
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }
}

$speedInfo = new speedChecker();
print_r($speedInfo->writeSpeedData());
?>