<?php

namespace Dsklad;

class DPD_service_my
{
    public $arMSG = array(); // массив-сообщение ('str' => текст_сообщения, 'type' => тип_сообщения (по дефолту: 0 - ошибка)
    private $IS_ACTIVE = 1; // флаг активности сервиса (0 - отключен, 1 - включен)
    private $IS_TEST = 0; // флаг тестирования (0 - работа, 1 - тест)
    private $SOAP_CLIENT; // SOAP-клиент
    private $MY_NUMBER = '1002018843'; // ЗАМЕНИТЬ НА СВОЙ!!! - клиентский номер в системе DPD (номер договора с DPD)
    private $MY_KEY = '268374CCFC44EC81108D9DB44C0E7C26ABA6BC41'; // ЗАМЕНИТЬ НА СВОЙ!!! - уникальный ключ для авторизации

    private $arDPD_HOST = array(
        0 => 'ws.dpd.ru/services/', //рабочий хост
        1 => 'wstest.dpd.ru/services/' //тестовый хост
    );
    private $arSERVICE = array( //сервисы: название => адрес
        'getCitiesCashPay' => 'geography', //География DPD (города доставки)
        'getTerminalsSelfDelivery2' => 'geography2', //Получить список подразделений DPD, не имеющих ограничений по габаритам и весу посылок приема/выдачи
        'getServiceCostByParcels2' => 'calculator2', //Расчет стоимости
        'getParcelShops' => 'geography2', //Получить список пунктов приема/выдачи посылок, имеющих ограничения по габаритам и весу, с указанием режима работы пункта и доступностью выполнения самопривоза/самовывоза.
        'getStatesByClientOrder' => 'tracing', //Получить состояние заказа

        //При работе с  методом  необходимо проводить получение информации по списку подразделений ежедневно.

    );

    /**
     * Конструктор
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->IS_TEST = $this->IS_TEST ? 1 : 0;
    }

    /**
     * Список городов доставки *
     *
     * @access public
     * @return
     */
    public function getCityList()
    {
        $obj = $this->_getDpdData('getCitiesCashPay');
        // конверт $obj --> $arr
        $res = $this->_parceObj2Arr($obj->return);
        return $res;
    }

    /**
     * Определение стоимости доставки *
     *
     * @access public
     * @param array $arData // массив входных параметров*
     * @return
     */
    public function getServiceCost($arData)
    {
        //var_dump($arData);
        // третий параметр - флаг упаковки запроса в общее поле "request"
        $obj = $this->_getDpdData('getServiceCostByParcels2', $arData, 1);
        //__($obj);
        // конверт $obj --> $arr
        $res = $this->_parceObj2Arr($obj->return);
        return $res;
    }

    /**
     * Получить список пунктов приема/выдачи посылок, ИМЕЮЩИХ ограничения по габаритам и весу, с указанием режима работы пункта и доступностью выполнения самопривоза/самовывоза.
     * При работе с  методом  необходимо проводить получение информации по списку подразделений ежедневно.
     *
     * @access public
     * @param array $arData // массив входных параметров*
     * @return
     */
    public function getTerminal($arData)
    {
        // третий параметр - флаг упаковки запроса в общее поле "request"
        $obj = $this->_getDpdData('getParcelShops', $arData, 1);
        // конверт $obj --> $arr
        $res = $this->_parceObj2Arr($obj->return);


        $counter = 0;

        foreach ($res['parcelShop'] as $terminal){

            // Исключаем пастоматы и неугодные терминалы
            if ($terminal['parcelShopType'] != 'П' && !in_array( $terminal['code'], NO_LOAD_TERMINALS)) {

                $this->changeKey('code', 'terminalCode', $terminal);
                $this->changeKey('brand', 'terminalName', $terminal);

                // Исключаем пункты без ожидания (проверка комплектности)
                foreach ($terminal['extraService'] as $service){
                    if ($service['esCode'] == 'ОЖД') $terminal['ozhd'] = 'yes';
                }

                if ($terminal['ozhd'] == 'yes') {
                    $res['parcelShop'][$counter] = $terminal;
                    $counter++;
                }
            }
        }
        return $res;
    }

    /**
     * Получить список подразделений DPD, НЕ ИМЕЮЩИХ ограничений по габаритам и весу посылок приема/выдачи (таких примерно 150-200 по РФ)
     *
     * @access public
     * @param array $arData // массив входных параметров*
     * @return
     */
    public function getTerminal2()
    {
        // третий параметр - флаг упаковки запроса в общее поле "request"
        $obj = $this->_getDpdData('getTerminalsSelfDelivery2', array(), 0);
        // конверт $obj --> $arr
        $res = $this->_parceObj2Arr($obj->return);
        return $res;
    }

    /**
     * Получить состояния заказа в DPD
     *
     * @access public
     * @param int $orderId
     * @return
     */
    public function getStatesByClientOrder($orderId) {
        $params = [
            'clientOrderNr' => $orderId
        ];
        $obj = $this->_getDpdData('getStatesByClientOrder', $params, 1);
        // конверт $obj --> $arr
        $res = $this->_parceObj2Arr($obj->return);
        return $res;
    }



    // PRIVATE ------------------------
    /**
     * Коннект с соответствующим сервисом *
     *
     * @access private
     * @param string $method_name
     * свойства класса $this->arSERVICE) * @return bool
     * Запрашиваемый метод сервиса (см. ключ
     * Результат инициализации (если положительный - появится свойство $this->SOAP_CLIENT, иначе $this->arMSG)
     */
    private function _connect2Dpd($method_name)
    {
        global $APPLICATION;
        if (!$this->IS_ACTIVE) return false;
        if (!$service = $this->arSERVICE[$method_name]) {
            $this->arMSG['str'] = 'В свойствах класса нет сервиса "' . $method_name . '"';
            return false;
        }
        $host = $this->arDPD_HOST[$this->IS_TEST] . $service . '?WSDL';
        try {
            // Soap-подключение к сервису
            $APPLICATION->DPDnotAvailable = false;
            $this->SOAP_CLIENT = new \SoapClient('http://' . $host, ['connection_timeout' => 10]);
            if (!$this->SOAP_CLIENT) throw new \Exception('Не удалось подключиться к сервисам DPD');
        } catch (\Exception $ex) {
            $APPLICATION->DPDnotAvailable = true;
            $this->arMSG['str'] = 'Не удалось подключиться к сервисам DPD ' . $service;
            return false;
        }
        return true;
    }

    /**
     * Запрос данных в методе сервиса *
     *
     * @access private
     * @param string $method_name Название метода Dpd-сервиса (см.$arSERVICE)
     * @param array $arData Массив параметров, передаваемых в метод
     * @param integer $is_request флаг упаковки запроса в поле 'request'
     * @return XZ_obj Объект, полученный от сервиса
     */
    function _getDpdData($method_name, $arData = array(), $is_request = 0)
    {
        global $APPLICATION;

        if (!$this->_connect2Dpd($method_name)) return false;

        // параметр запроса для аутентификации
        $arData['auth'] = array(
            'clientNumber' => $this->MY_NUMBER,
            'clientKey' => $this->MY_KEY);
        // упаковка запроса в поле 'request'

        if ($is_request) $arRequest['request'] = $arData; else $arRequest = $arData;

        try {
            //eval("\$obj = \$this->SOAP_CLIENT->\$method_name(\$arRequest);");

            $APPLICATION->DPDnotAvailable = false;
            $obj = $this->SOAP_CLIENT->$method_name($arRequest);
            if (!$obj) throw new \Exception('Не удалось вызвать метод');
        } catch (\Exception $ex) {
            $APPLICATION->DPDnotAvailable = true;
            $this->arMSG['str'] = 'Не удалось вызвать метод ' . $method_name . ' / ' . $ex;
        }

        return $obj ? $obj : false;
    }

    /**
     * Парсер объекта в массив (рекурсия) *
     *
     * @access private
     * @param object $obj Объект
     * @param integer $isUTF Флаг необходимости конвертирования строк из UTF в WIN (0|1), по-дефолту "1" - конвертить
     * @param array $arr Внутренний cлужебный массив для обеспечения рекурсии
     * @return array
     */
    private function _parceObj2Arr($obj, $arr = array())
    {
        if (is_object($obj) || is_array($obj)) {
            $arr = array();
            for (reset($obj); list($k, $v) = each($obj);) {
                if ($k === "GLOBALS") continue;
                $arr[$k] = $this->_parceObj2Arr($v, $arr);
            }
            return $arr;
        } else {
            return $obj;
        }
    }

    private function changeKey($key, $new_key, &$arr, $rewrite = true)
    {
        if (!array_key_exists($new_key, $arr) || $rewrite) {
            $arr[$new_key] = $arr[$key];
            unset($arr[$key]);
            return true;
        }
        return false;
    }
}
