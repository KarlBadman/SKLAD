<?php
/**
 * Created by PhpStorm.
 * User: AntonKC
 * Date: 25.01.2018
 * Time: 20:31
 */
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www"; // Master

require_once(__DIR__ . '/feeds.php');
$feeds = new feeds();

use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;
use Bitrix\Sale\Order;

Loader::includeModule('sale');

set_time_limit(360);

$file = $_SERVER["DOCUMENT_ROOT"]."/i/trackad.xml";
$fp = fopen($file, "w");

if (is_writable($file)) {
    print '-------------------[ START ]-------------------'.PHP_EOL;
    printf('Time start:'.date("Y-m-d H:i").PHP_EOL);

    fwrite($fp, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
    fwrite($fp, "<orders>\n");

    // Маппинг статусов
    $arrAdStates = array(
        'N' => 'new', //(только что созданный заказ)
        'WT' => 'new', //(только что созданный заказ)
        'OB' => 'new', //(только что созданный заказ)
        'AC' => 'wait', // (заказ проверен, доставляется)
        'OP' => 'wait', // (заказ проверен, доставляется)
        'PD' => 'wait', // (заказ проверен, доставляется)
        'WD' => 'wait', // (заказ проверен, доставляется)
        'WP' => 'wait', // (заказ проверен, доставляется)
        'KO' => 'wait', // (заказ проверен, доставляется)
        'CA' => 'refuse', //(покупатель отказался от заказа, без оплаты)
        'TO' => 'refuse', //(покупатель отказался от заказа, без оплаты)
        'VN' => 'refuse', //(покупатель отказался от заказа, без оплаты)
        'NW' => 'refuse', //(покупатель отказался от заказа, без оплаты)
        'NV' => 'refuse', //(покупатель отказался от заказа, без оплаты)
        'CB' => 'refuse', //(покупатель отказался от заказа, без оплаты)
        'CU' => 'refuse', //(покупатель отказался от заказа, без оплаты)
        'CT' => 'refuse', //(покупатель отказался от заказа, без оплаты)
        'CL' => 'refuse', //(покупатель отказался от заказа, без оплаты)
        'CE' => 'refuse', //(покупатель отказался от заказа, без оплаты)
        'CD' => 'refuse', //(покупатель отказался от заказа, без оплаты)
        'CC' => 'refuse', //(покупатель отказался от заказа, без оплаты)
        'RT' => 'return', //(заказ был оплачен, а затем возвращен)
        'partial_return', //(заказ оплачен, но часть заказа возвращена)
        'F' => 'done', //(заказ получен и оплачен)
        'VZ' => 'refuse',  // Товар возвращается
        'OS' => 'refuse',  // Нет в наличии
        'IT' => 'refuse',  // Условия рассрочки
        'PN' => 'wait',  // Уведомить клиента о поступлении
        'CR' => 'wait',  // Запрос на корректировку
        'PA' => 'wait',  // Собран частично
        'AD' => 'wait',  // Отправлен на сборку и доставку
        'PO' => 'wait',  // Проблема с заказом
        'HI' => 'wait',  // Отгружен
        'AS' => 'wait',  // Собран
        'PP' => 'wait',  // В пункте самовывоза
        'CF' => 'wait',  // Перезвонить
        'DR' => 'wait',  // Повторная отправка
    );


    $filter = array(
        ">=DATE_UPDATE" => date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), mktime(0, 0, 0, date("n"), date("d")-7, date("Y"))),
        "!STATUS_ID" => "TO"
    );

    $orders = '';
    $select = array("ID", "USER_ID", "DATE_INSERT", "DATE_UPDATE", "PRICE", "STATUS_ID", "ORDER_METHOD", "SOURCE_NAME", "MEDIUM_NAME", "CAMPAIGN_NAME");


    $items = CSaleOrder::GetList(array("ID"=>"ASC"), $filter, null, null, $select);
    while($item = $items->Fetch()){

        $method = Utils::getOrderPropValueByCode('ORDER_METHOD', $item['ID']);

        $source = Utils::getOrderPropValueByCode('SOURCE_NAME', $item['ID']);
        $medium = Utils::getOrderPropValueByCode('MEDIUM_NAME', $item['ID']);
        $campaign = Utils::getOrderPropValueByCode('CAMPAIGN_NAME', $item['ID']);

        if (in_array($method, array('live-chat', 'phone', 'email'))){
            $isCC = 1;
        }else{
            $isCC = 0;
        }

        $filterlb = array("USER_ID" => $item['USER_ID'], "<ID" => $item['ID']);
        $lastBuy = CSaleOrder::GetList(array("ID" => "ASC"), $filterlb)->fetch();
        if (!empty($lastBuy['DATE_INSERT']))
            $dateLastBuy = FormatDate("Y-m-d H:i:s", MakeTimeStamp($lastBuy['DATE_INSERT']));


        // Получим данные о составе заказа
        $arBasketItems = array();

        $dbBasketItems = CSaleBasket::GetList(
            array("ID" => "ASC"),
            array("ORDER_ID" => $item['ID']),
            false,
            false,
            array("ID", "PRODUCT_ID", "MODULE", "NAME", "QUANTITY", "PRICE", "WEIGHT", "CATALOG_XML_ID", "DETAIL_PAGE_URL")
        //array()
        );

        $products = '';
        $arrProdIds = array();
        while ($arBasketItems = $dbBasketItems->Fetch()) {

            $category = '';
            if (stristr(strtolower($arBasketItems['NAME']), 'стол')) $category = 160;
            elseif (stristr(strtolower($arBasketItems['NAME']), 'eames')) $category = 165;
            elseif (stristr(strtolower($arBasketItems['NAME']), 'ghost')) $category = 166;
            elseif (stristr(strtolower($arBasketItems['NAME']), 'masters')) $category = 167;
            elseif (stristr(strtolower($arBasketItems['NAME']), 'panton')) $category = 168;
            elseif (stristr(strtolower($arBasketItems['NAME']), 'tolix')) $category = 171;
            elseif (stristr(strtolower($arBasketItems['NAME']), 'барный')) $category = 163;
            else $category = 175;


            // Со старыми заказами естть проблема: в корзине несколько товаров с одинаковым id и кол-во 1, хотя должна быть одна запись с правильным кол-вом
            if (!in_array($arBasketItems['PRODUCT_ID'], $arrProdIds)) {
                $products .=
                    '<product>' .
                    '<id>' . $arBasketItems['PRODUCT_ID'] . '</id>' .
                    '<category_id>' . $category . '</category_id>' .
                    '<price>' . round($arBasketItems['PRICE'] - $arBasketItems['PRICE']/120*20,2) . '</price>' .
                    '<margin>' . $arBasketItems['PRICE'] . '</margin>' .
                    '<quantity>' . $arBasketItems['QUANTITY'] . '</quantity>' .
                    '</product>';

                $arrProdIds[] = $arBasketItems['PRODUCT_ID'];
            }
        }

        // В старых заказах бывает так, что нет товаров в корзине - такие не нужны.
        if (!empty($products)) {
            $arOrder = array(
                'id' => $item['ID'],
                'state' => (!empty($arrAdStates[$item['STATUS_ID']])) ? $arrAdStates[$item['STATUS_ID']] : 'refuse',
                'date' => FormatDate("Y-m-d H:i:s", MakeTimeStamp($item['DATE_INSERT'])),
                'price' => round($item['PRICE'] - $item['PRICE']/120*20,2),
                'lastorderdate' => $dateLastBuy,
                'coupon' => '',
                'margin' => $item['PRICE'], // TODO: реальная наценка пока стоимость товаров без учета доставки
                'iscallcenter' => $isCC,
                'ordersource' => $method,
                'commission' => '',
                'utm_source' => $source,
                'utm_medium' => $medium,
                'utm_campaign' => $campaign,
                'products' => $products,
            );

            $orders = "<order>" . Utils::makeParams($arOrder) . "</order>";
            fwrite($fp, $orders . "\n");
        }
        unset($dateLastBuy);
        unset($isCC);
    }

    fwrite($fp, "</orders>\n");

    fclose($fp);

    printf('Time end:'.date("Y-m-d H:i").PHP_EOL);
    print '--------------------[ END ]--------------------'.PHP_EOL;

}