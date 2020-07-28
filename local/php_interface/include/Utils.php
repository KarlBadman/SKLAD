<?php
/**
 * Created by PhpStorm.
 * User: AntonKC
 * Date: 17.01.2018
 * Time: 15:30
 */
use Bitrix\Main;
class Utils
{
    public static function writeLog($event, $request, $response, $filePrefix = 'log_'){
        $date = date('d-m-Y H:i:s');
        $rec='';
        $filename  = $_SERVER['DOCUMENT_ROOT'] . LOG_DIRECTORY . $filePrefix.date('Ymd_H').'.txt';
        $f = fopen($filename, 'a+');
        $rec .= "Date: $date\r\nEvent: $event\r\n <!---------- Request: ---------->\r\n ".$request."\r\n <!---------- Response: ---------->\r\n $response\r\n\r\n";
        fwrite($f, $rec);
        fclose($f);
    }

    public static function getOrderPropValueByCode($code, $orderId)
    {
        if (!strlen($code))
            return false;

        $props_val = CSaleOrderPropsValue::GetList(array(), array("ORDER_ID" => $orderId, "CODE" => $code));
        $values = $props_val->fetch();

        return $values['VALUE'];
    }

    public static function setOrderPropValueByCode($code, $value, $orderId) {
        if (!strlen($code)) {
            return false;
        }

        if (CModule::IncludeModule('sale')) {
            if ($arOrderProps = CSaleOrderProps::GetList(array(), array('CODE' => $code))->Fetch()) {
                $dbOrderPropsValue = CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $orderId, 'ORDER_PROPS_ID' => $arOrderProps['ID']));
                if ($arVals = $dbOrderPropsValue->Fetch()) {
                    return CSaleOrderPropsValue::Update($arVals['ID'], array(
                        'NAME' => $arVals['NAME'],
                        'CODE' => $arVals['CODE'],
                        'ORDER_PROPS_ID' => $arVals['ORDER_PROPS_ID'],
                        'ORDER_ID' => $arVals['ORDER_ID'],
                        'VALUE' => $value,
                    ));
                } else {
                    return CSaleOrderPropsValue::Add(array(
                        'NAME' => $arOrderProps['NAME'],
                        'CODE' => $arOrderProps['CODE'],
                        'ORDER_PROPS_ID' => $arOrderProps['ID'],
                        'ORDER_ID' => $orderId,
                        'VALUE' => $value,
                    ));
                }
            }
        }
    }

    public static function getPriceByProductId($id, $priceAsNumber = false){
        // Получаем товар по ID (GetList для фильтра по активным)

        $items = CIBlockElement::GetList(
            array("ID" => "DESC"),
            array('ACTIVE' => 'Y', 'ID' => $id),
            false,
            array(
                "nTopCount" => 10000
            )
        );

        while($item = $items->GetNextElement()){
            $item = $item->GetFields();

            $arPrice = CPrice::GetList(array(), array('PRODUCT_ID' => $item['ID']))->Fetch();
            $priceRes = CCurrencyLang::CurrencyFormat($arPrice["PRICE"], 'RUR');
            if ($priceAsNumber)
                $price = (int)$arPrice["PRICE"];
            else
                $price = substr($priceRes, 0, strlen($priceRes) - 3);
        }

        return $price;
    }

    public static function translit_str($str, $spacebar_replace = false) {
        $rus = array(' ','А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
        $lat = array('_','A', 'B', 'V', 'G', 'D', 'E', 'E', 'Zh', 'Z', 'I', 'I', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', '', 'I', '', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'zh', 'z', 'i', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', '', 'y', '', 'e', 'yu', 'ya');

        if ($spacebar_replace) $str = str_replace(' ', '_', $str);

        return str_replace($rus, $lat, $str);
    }

    public static function translit_str_tab_replace($str, $spacebar_replace = false) {
        $rus = array('  ','А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
        $lat = array('_','A', 'B', 'V', 'G', 'D', 'E', 'E', 'Zh', 'Z', 'I', 'I', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', '', 'I', '', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'zh', 'z', 'i', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', '', 'y', '', 'e', 'yu', 'ya');

        if ($spacebar_replace) $str = str_replace(' ', '_', $str);

        return str_replace($rus, $lat, $str);
    }


    // Работа с инфоблоками
    public static function getIblockCode()
    {
        global $APPLICATION;
        $sections = explode('/', $APPLICATION->GetCurPage());
        return @$sections[2];
    }

    function getIBlockIdByCode($code, $type = 'catalog')
    {
        if (empty($code))
            return null;
        $iblock = \CIBlock::GetList(
            Array(), Array(
            'TYPE' => $type,
            'SITE_ID' => SITE_ID,
            'ACTIVE' => 'Y',
            "CODE" => $code
        ), true
        )->Fetch();

        return @$iblock['ID'];
    }

    function getIBlockName($id, $type = 'catalog')
    {
        if (empty($id))
            return null;
        $iblock = \CIBlock::GetList(
            Array(), Array(
            'TYPE' => $type,
            'SITE_ID' => SITE_ID,
            'ACTIVE' => 'Y',
            "ID" => $id
        ), true
        )->Fetch();

        return @$iblock['NAME'];
    }

    function getIBlockByCode($code, $type = 'catalog')
    {
        if (empty($code))
            return null;
        $iblock = \CIBlock::GetList(
            Array(), Array(
            'TYPE' => $type,
            'SITE_ID' => SITE_ID,
            'ACTIVE' => 'Y',
            "CODE" => $code
        ), true
        )->Fetch();

        return @$iblock;
    }

    function makeParams($data)
    {
        $params = null;
        foreach($data as $ind => $val)
            $params .= "<{$ind}>{$val}</{$ind}>\n";
        return $params;
    }

    /**
     * Добыть курс валюты на текущую дату
     * @param $currency
     * @return float
     */
    function getBankCurrency($currency) {
        global $APPLICATION;
        $result = array();
        $url = 'https://www.cbr.ru/scripts/XML_daily.asp?date_req=' . date("d.m.Y", time());
        $http = new Main\Web\HttpClient();
        $http->setRedirect(true);
        $data = $http->get($url);

        $charset = 'windows-1251';
        $matches = array();
        if (preg_match("/<"."\?XML[^>]{1,}encoding=[\"']([^>\"']{1,})[\"'][^>]{0,}\?".">/i", $data, $matches))
        {
            $charset = trim($matches[1]);
        }
        $data = preg_replace("#<!DOCTYPE[^>]+?>#i", '', $data);
        $data = preg_replace("#<"."\\?XML[^>]+?\\?".">#i", '', $data);
        $data = $APPLICATION->ConvertCharset($data, $charset, SITE_CHARSET);

        $objXML = new CDataXML();
        $res = $objXML->LoadString($data);
        if ($res !== false)
            $data = $objXML->GetArray();
        else
            $data = false;

        if (!empty($data) && is_array($data))
        {
            if (!empty($data["ValCurs"]["#"]["Valute"]) && is_array($data["ValCurs"]["#"]["Valute"]))
            {
                $currencyList = $data["ValCurs"]["#"]["Valute"];
                foreach ($currencyList as $currencyRate)
                {
                    if ($currencyRate["#"]["CharCode"][0]["#"] == $currency)
                    {
                        $result['STATUS'] = 'OK';
                        $result['RATE_CNT'] = (int)$currencyRate["#"]["Nominal"][0]["#"];
                        $result['RATE'] = (float)str_replace(",", ".", $currencyRate["#"]["Value"][0]["#"]);
                        break;
                    }
                }
                unset($currencyRate, $currencyList);
            }
        }
        if(empty($result['RATE'])){
            $result['RATE'] = CCurrencyRates::GetConvertFactor($currency, "RUB");
        }
        return $result['RATE'];
    }
}