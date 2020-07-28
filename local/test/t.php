<?php
require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;
use Bitrix\Main\Context;
use Bitrix\Sale\Order;
//use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
use Bitrix\Highloadblock\HighloadBlockTable;


Loader::includeModule('sale');
Loader::includeModule('iblock');
Loader::includeModule('catalog');
Loader::includeModule('highloadblock');

if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/Utils.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/Utils.php");



if (in_array($_SERVER['REMOTE_ADDR'], array('188.134.3.73', '5.144.98.9')) && @$_GET['ant'] == 1) {
    echo phpinfo();
    exit;
}
if (in_array($_SERVER['REMOTE_ADDR'], array('188.134.3.73', '5.144.98.9')) && @$_GET['ant'] == 1) {
    exit;

    $arrOrders = array(

        '137',
        '1744',
        '5462',
        '5616',
        '5629',
        '6769',
        '9123',
        '9968',
        '10342',
        '10563',
        '10573',
        '11701',
        '11777',
        '11854',
        '20',
        '20',
        '192',
        '192',
        '192',
        '193',
        '193',
        '196',
        '204',
        '206',
        '206',
        '208',
        '212',
        '213',
        '214',
        '214',
        '214',
        '218',
        '220',
        '222',
        '222',
        '224',
        '226',
        '226',
        '226',
        '228',
        '228',
        '228',
        '229',
        '229',
        '229',
        '234',
        '234',
        '236',
        '237',
        '237',
        '237',
        '237',
        '238',
        '239',
        '243',
        '243',
        '243',
        '245',
        '245',
        '247',
        '260',
        '260',
        '264',
        '265',
        '269',
        '273',
        '273',
        '274',
        '274',
        '275',
        '276',
        '284',
        '285',
        '285',
        '289',
        '289',
        '293',
        '299',
        '311',
        '311',
        '312',
        '321',
        '323',
        '323',
        '327',
        '328',
        '331',
        '333',
        '334',
        '334',
        '343',
        '344',
        '346',
        '357',
        '359',
        '361',
        '361',
        '361',
        '362',
        '365',
        '365',
        '365',
        '369',
        '370',
        '370',
        '371',
        '372',
        '372',
        '372',
        '373',
        '376',
        '379',
        '381',
        '382',
        '384',
        '385',
        '385',
        '386',
        '390',
        '402',
        '402',
        '403',
        '403',
        '405',
        '406',
        '408',
        '411',
        '412',
        '413',
        '413',
        '418',
        '418',
        '419',
        '419',
        '419',
        '420',
        '421',
        '423',
        '425',
        '425',
        '426',
        '426',
        '426',
        '427',
        '430',
        '438',
        '440',
        '443',
        '445',
        '445',
        '445',
        '456',
        '460',
        '461',
        '462',
        '463',
        '463',
        '464',
        '464',
        '464',
        '464',
        '465',
        '468',
        '468',
        '469',
        '469',
        '475',
        '477',
        '478',
        '479',
        '479',
        '481',
        '482',
        '483',
        '485',
        '485',
        '486',
        '486',
        '487',
        '488',
        '489',
        '489',
        '490',
        '490',
        '491',
        '492',
        '493',
        '494',
        '497',
        '497',
        '503',
        '504',
        '506',
        '506',
        '506',
        '507',
        '507',
        '507',
        '507',
        '508',
        '509',
        '509',
        '512',
        '513',
        '513',
        '515',
        '515',
        '518',
        '518',
        '518',
        '520',
        '523',
        '523',
        '523',
        '523',
        '523',
        '523',
        '523',
        '523',
        '524',
        '524',
        '526',
        '528',
        '531',
        '532',
        '532',
        '533',
        '533',
        '538',
        '539',
        '539',
        '539',
        '540',
        '543',
        '547',
        '548',
        '549',
        '551',
        '551',
        '552',
        '552',
        '557',
        '558',
        '558',
        '558',
        '559',
        '561',
        '561',
        '561',
        '563',
        '564',
        '565',
        '570',
        '570',
        '572',
        '572',
        '573',
        '574',
        '576',
        '576',
        '576',
        '576',
        '577',
        '579',
        '579',
        '580',
        '580',
        '581',
        '583',
        '585',
        '586',
        '588',
        '592',
        '592',
        '593',
        '594',
        '594',
        '595',
        '595',
        '598',
        '598',
        '600',
        '601',
        '602',
        '604',
        '606',
        '608',
        '608',
        '608',
        '608',
        '608',
        '608',
        '608',
        '608',
        '608',
        '609',
        '611',
        '611',
        '613',
        '616',
        '617',
        '620',
        '620',
        '621',
        '627',
        '628',
        '629',
        '631',
        '632',
        '635',
        '636',
        '636',
        '638',
        '639',
        '640',
        '640',
        '642',
        '644',
        '648',
        '650',
        '651',
        '653',
        '654',
        '654',
        '654',
        '662',
        '663',
        '663',
        '664',
        '665',
        '666',
        '669',
        '670',
        '671',
        '672',
        '673',
        '673',
        '675',
        '677',
        '679',
        '680',
        '683',
        '684',
        '684',
        '686',
        '687',
        '688',
        '689',
        '691',
        '691',
        '691',
        '692',
        '692',
        '693',
        '694',
        '697',
        '699',
        '701',
        '702',
        '702',
        '702',
        '702',
        '703',
        '704',
        '705',
        '705',
        '705',
        '705',
        '709',
        '712',
        '712',
        '712',
        '712',
        '712',
        '712',
        '712',
        '712',
        '712',
        '712',
        '713',
        '716',
        '717',
        '718',
        '719',
        '720',
        '720',
        '721',
        '721',
        '722',
        '724',
        '726',
        '726',
        '727',
        '727',
        '728',
        '729',
        '729',
        '729',
        '730',
        '730',
        '745',
        '750',
        '750',
        '750',
        '750',
        '751',
        '752',
        '773',
        '774',
        '778',
        '778',
        '783',
        '784',
        '785',
        '785',
        '785',
        '785',
        '785',
        '785',
        '788',
        '791',
        '800',
        '800',
        '805',
        '808',
        '821',
        '866',
        '868',
        '878',
        '883',
        '883',
        '883',
        '883',
        '884',
        '887',
        '895',
        '895',
        '896',
        '904',
        '907',
        '908',
        '911',
        '918',
        '919',
        '932',
        '936',
        '936',
        '954',
        '956',
        '958',
        '958',
        '962',
        '962',
        '963',
        '963',
        '972',
        '978',
        '983',
        '985',
        '985',
        '985',
        '1000',
        '1002',
        '1002',
        '1002',
        '1008',
        '1008',
        '1017',
        '1019',
        '1028',
        '1033',
        '1034',
        '1040',
        '1041',
        '1043',
        '1043',
        '1043',
        '1057',
        '1066',
        '1069',
        '1071',
        '1073',
        '1074',
        '1074',
        '1075',
        '1079',
        '1079',
        '1079',
        '1081',
        '1087',
        '1087',
        '1093',
        '1105',
        '1105',
        '1106',
        '1108',
        '1110',
        '1113',
        '1116',
        '1125',
        '1136',
        '1140',
        '1144',
        '1149',
        '1154',
        '1157',
        '1157',
        '1163',
        '1173',
        '1187',
        '1193',
        '1204',
        '1206',
        '1211',
        '1215',
        '1217',
        '1219',
        '1227',
        '1235',
        '1245',
        '1252',
        '1252',
        '1252',
        '1252',
        '1252',
        '1282',
        '1303',
        '1306',
        '1317',
        '1320',
        '1340',
        '1342',
        '1342',
        '1350',
        '1352',
        '1358',
        '1358',
        '1358',
        '1358',
        '1383',
        '1393',
        '1393',
        '1393',
        '1410',
        '1419',
        '1428',
        '1434',
        '1436',
        '1436',
        '1436',
        '1449',
        '1458',
        '1466',
        '1466',
        '1474',
        '1494',
        '1517',
        '1535',
        '1581',
        '1593',
        '1612',
        '1636',
        '1636',
        '1643',
        '1649',
        '1668',
        '1674',
        '1734',
        '1752',
        '1756',
        '1777',
        '1786',
        '1825',
        '1851',
        '1935',
        '1950',
        '1951',
        '1967',
        '2009',
        '2030',
        '2037',
        '2052',
        '2113',
        '2135',
        '2164',
        '2175',
        '2210',
        '2215',
        '2220',
        '2265',
        '2272',
        '2355',
        '2384',
        '2453',
        '2489',
        '2601',
        '2881',
        '3669',
        '3771',
        '3848',
        '3892',
        '4911',
        '5044',
        '5093',
        '5503',
        '7770',
        '13316',
    );


    echo "<table border='1'>";

    echo "<tr>";
    echo "<td>Номер заказа</td>"; // order_id
    echo "<td>Сумма заказа</td>"; // order_id
    echo "<td>Сумма доставки </td>"; // order_id
    echo "<td>Наименование товара</td>"; // Имя продукта
    echo "<td>Категория товара</td>"; // Имя раздела
    echo "<td>E-Mail пользователя</td>";
    echo "</tr>";


    foreach ($arrOrders as $id) {

        $arFilter = Array(
            'ID' => $id,
        );
        $arSales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter)->Fetch();

        $dbBasketItems = CSaleBasket::GetList(
            array("ID" => "ASC"),
            array("ORDER_ID" => $id),
            false,
            false,
            array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "CATALOG_XML_ID", "NAME")
        );

        while ($arItems = $dbBasketItems->Fetch()) {

            // Получаем свойства товара
            $arProdProps2 = CIBlockElement::GetList(
                array("ID" => "DESC"),
                array("IBLOCK_ID" => $prod_cat['IBLOCK_ID'], 'ID' => $arItems['PRODUCT_ID']),
                false
            )->Fetch();

            echo "<tr>";
            // Печатаем заказ
            echo "<td>" . $arSales['ID'] . "</td>"; // order_id
            echo "<td>" . $arSales['PRICE'] . "</td>"; // order_id
            echo "<td>" . $arSales['PRICE_DELIVERY'] . "</td>"; // order_id
            echo "<td>" . $arItems['NAME'] . "</td>"; // Имя продукта
            echo "<td>" . $arProdProps2['IBLOCK_NAME'] . "</td>"; // Имя раздела
            echo "<td>" . $arSales['USER_EMAIL'] . "</td>";

            //echo "<td>" . $isTest . "</td>";
            echo "</tr>";
            $isTest = '';
            $ilpStatus = '';
        }
    }
    echo "</table>";

    //if(!empty($codeId)) {
    //$response = Ilp::get()->updateRegisteredCode($codeId, $ilpStatus);
    //}

    exit;
}



if (in_array($_SERVER['REMOTE_ADDR'], array('188.134.3.73', '5.144.98.9')) && @$_GET['ant'] == 1) {
    echo  Utils::translit_str('Стуля');

    exit;
    require_once ('/home/bitrix/www/catalog/index.php');

    $returnValue = unserialize('a:7:{s:12:"terminalCode";s:3:"LED";s:12:"terminalName";s:48:"Санкт-Петербург - терминал";s:7:"address";a:11:{s:6:"cityId";i:49694167;s:11:"countryCode";s:2:"RU";s:10:"regionCode";s:2:"78";s:10:"regionName";s:29:"Санкт-Петербург";s:8:"cityCode";s:11:"78000000000";s:8:"cityName";s:29:"Санкт-Петербург";s:5:"index";s:6:"192012";s:6:"street";s:35:"Обуховской Обороны";s:10:"streetAbbr";s:16:"проспект";s:7:"houseNo";s:3:"295";s:8:"descript";s:1766:"На автомобиле: Терминал находится на территории складского комплекса "Логопарк Троицкий". Заезд с улицы Запорожская, ориентир – д. 33 (заправка ПТК). Напротив заправки под полотном КАД находится въезд со шлагбаумом. На КПП нужно сообщить, что Вы направляетесь в DPD и получить разовый пропуск, на нем необходимо поставить штамп DPD (есть в зоне самовывоза/самопривоза и в клиентском зале). Общественным транспортом: Пешеходам удобно попасть на терминал от м. Обухово (200 м). Следует выйти из метро, сразу повернуть налево, перейти дорогу по пешеходному переходу и двигаться вдоль автостоянки в сторону КАД. Через 1,5 минуты справа Вы увидите проходную складского комплекса. В окне регистрации вам выдадут магнитную карточку Гостя. (Возьмите с собой документ, удостоверяющий личность). По территории терминала следует двигаться прямо, ко второму зданию по правую руку, обозначенному литерой А3. Его нужно обойти справа и зайти в первую дверь на пандусе между 19 и 18 воротами.";}s:14:"geoCoordinates";a:2:{s:8:"latitude";s:8:"59.84627";s:9:"longitude";s:9:"30.464036";}s:8:"schedule";a:4:{i:0;a:2:{s:9:"operation";s:7:"Payment";s:9:"timetable";a:2:{s:8:"weekDays";s:34:"Пн,Вт,Ср,Чт,Пт,Сб,Вс";s:8:"workTime";s:13:"09:00 - 20:00";}}i:1;a:2:{s:9:"operation";s:17:"PaymentByBankCard";s:9:"timetable";a:2:{s:8:"weekDays";s:34:"Пн,Вт,Ср,Чт,Пт,Сб,Вс";s:8:"workTime";s:13:"09:00 - 20:00";}}i:2;a:2:{s:9:"operation";s:12:"SelfDelivery";s:9:"timetable";a:2:{s:8:"weekDays";s:34:"Пн,Вт,Ср,Чт,Пт,Сб,Вс";s:8:"workTime";s:13:"09:00 - 20:00";}}i:3;a:2:{s:9:"operation";s:10:"SelfPickup";s:9:"timetable";a:2:{s:8:"weekDays";s:34:"Пн,Вт,Ср,Чт,Пт,Сб,Вс";s:8:"workTime";s:13:"09:00 - 20:00";}}}s:12:"extraService";a:3:{i:0;a:2:{s:6:"esCode";s:6:"НПП";s:6:"params";a:2:{s:4:"name";s:7:"sum_npp";s:5:"value";s:6:"200000";}}i:1;a:2:{s:6:"esCode";s:6:"ОЖД";s:6:"params";a:2:{s:4:"name";s:12:"reason_delay";s:5:"value";s:28:"ПРИМ, ПРОС, РАБТ";}}i:2;a:1:{s:6:"esCode";s:6:"ТРМ";}}s:8:"services";a:1:{s:11:"serviceCode";a:12:{i:0;s:3:"NDY";i:1;s:3:"BZP";i:2;s:3:"CUR";i:3;s:3:"DIR";i:4;s:3:"DPE";i:5;s:3:"DPI";i:6;s:3:"ECN";i:7;s:3:"ECU";i:8;s:3:"MAX";i:9;s:3:"PCL";i:10;s:3:"CSM";i:11;s:3:"MXO";}}}');

    echo "<pre>";
    print_r($returnValue);
    echo "</pre>";

    exit;
}

if (in_array($_SERVER['REMOTE_ADDR'], array('188.134.3.73', '5.144.98.9')) && @$_GET['ant'] == 1) {

    exit;
    $xmlTemplate = '
        <xml version="1.0" encoding="UTF-8">
        <orders>
            %s
        </orders>
        </xml>
    ';

    $arOrder = array(
        'id' => 'id',
        'state' => 'state',
        'date' => 'date',
        'price' => 'price',
        'lastorderdate' => '',
        'coupon' => '',
        'margin' => '',
        'iscallcenter' => '',
        'ordersource' => '',
        'commission' => '',
        'new' => '',
        'wait' => '',
        'done' => '',
        'cancel' => '',
        'refuse' => '',
        'return' => '',
        'partial_return' => '',
    );

    $filter = array(
            ">=DATE_UPDATE" => date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), mktime(0, 0, 0, date("n"), date("d")-1, date("Y")))
    );

    //$filter = array('ID'=>'5555');

    $orders = '';

    $items = CSaleOrder::GetList(array("ID"=>"ASC"), $filter);
    while($item = $items->Fetch()){

        $filterlb = array("USER_ID" => $item['USER_ID'], "!ID" => $item['ID']);
        $lastBuy = CSaleOrder::GetList(array("ID" => "ASC"), $filterlb)->fetch();
        if (!empty($lastBuy['DATE_INSERT'])) $dateLastBuy = $lastBuy['DATE_INSERT'];


        $arOrder = array(
            'id' => $item['ID'],
            'state' => 'new', // TODO: схлопнуть наши статусы
            'date' => $item['DATE_UPDATE'],
            'price' => $item['PRICE'],
            'lastorderdate' => $dateLastBuy,
            'coupon' => '',
            'margin' => $item['PRICE'], // TODO: реальная наценка пока стоимость товаров без учета доставки
            'iscallcenter' => '0',
            'ordersource' => 'site',
            'commission' => '',
        );

        $orders = $orders. "<order>".Utils::makeParams($arOrder)."</order>";
    }

    echo sprintf($xmlTemplate, $orders);

    die;
}
if (in_array($_SERVER['REMOTE_ADDR'], array('188.134.3.73', '5.144.98.9')) && @$_GET['ant'] == 1) {
    exit;

    echo "Удалим юлмарты";

//Напишем функцию получения экземпляра класса:
    function GetEntityDataClass($HlBlockId) {
        if (empty($HlBlockId) || $HlBlockId < 1)
        {
            return false;
        }
        $hlblock = HLBT::getById($HlBlockId)->fetch();
        $entity = HLBT::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        return $entity_data_class;
    }

    // Получить все элементы highload-инфоблока
    $entity_data_class = GetEntityDataClass(MY_HL_BLOCK_ID);
    $rsData = $entity_data_class::getList( array(
        'select' => array('*'),
        'filter' => array('UF_TERMINALNAME' => array('Юлмарт')),
    ));

    while ($el = $rsData->fetch()) {
        echo "<pre>";
        print_r($el);
        HLBT::delete((int)$el['ID']);
        echo "</pre>";
    }


    exit;
}

if (in_array($_SERVER['REMOTE_ADDR'], array('188.134.3.73', '5.144.98.9')) && @$_GET['ant'] == 1) {
    exit;

    $email = 'ayk@ooott.ru';
    $site = $_SERVER['SERVER_NAME'];
    $date = date('d-m-Y');


    $filter = array("ACTIVE" => "Y", "EMAIL" => $email,);
    $arSel = array("ID","NAME", "PERSONAL_MOBILE",);
    $arUsers = CUser::GetList(($by = "name"), ($order = "asc"), $filter, array("FIELDS" => $arSel))->fetch();

    if (!empty($arUsers['ID'])) {
        $updUser = new CUser; // для апдейта данных

        // Если юзер зареган
        $filter = array("ID"=>$arUsers['ID']); // фильтр для полей по залогиненному юзеру
        $rsUsers = CUser::GetList(($by="id"), ($order="desc"), $filter, array("SELECT"=>array("UF_*"))); // Достаем дополнительные поля начинающиеся с UF_

        $regToken =  md5($email.$site.$date);

        // подготовим массив для апдейта
        $updFields = Array(
            "UF_REG_TOKEN"  => $regToken,
        );

        $updUser->Update($arUsers['ID'], $updFields); // Пишем даннве в базу
        $strError .= $updUser->LAST_ERROR; // Ошибки будут тут
    }else{

    }


    echo "<pre>";
    print_r($arUsers);
    echo "</pre>";



    echo "<br>email: ".$email;
    echo "<br>site: ".$site;
    echo "<br>date: ".$date;

    echo "<br>".md5($email.$site.$date);

    exit;
}


// Получить инфу по товару
if (in_array($_SERVER['REMOTE_ADDR'], array('188.134.3.73', '5.144.98.9')) && @$_GET['logo'] == 1) {

    exit;
    echo "Директория с темой: ". SITE_TEMPLATE_PATH ?>

    <img src="<?= SITE_TEMPLATE_PATH ?>/images/logo_christmas.svg">

    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/logo_christmas.svg"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#logo"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-call"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-details"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-email"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-partner"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-questions"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-skype"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-whatsapp"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#bank"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#box"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#card"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#card2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cart"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cheap-arrow"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cheap-buyer"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cheap-factory"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cheap-profit"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cheap-store"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cheap-total"></use>
    </svg>

    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cheap-wholesale"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check3"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cross"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cross2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#darr"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#darr2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delete"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery-back"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery-exchange"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery-legal"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery-lift"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery-lock"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery-pack"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery-time"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery-week"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#"></use>
    </svg>

    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#download"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#expand"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#guarantee"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#help"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#larr"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#larr2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#lock"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#logo.short"></use>
    </svg>

    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#logo"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#logo2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#mastercard"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#menu-armchair"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#menu-chair"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#menu-light"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#menu-sale"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#menu-sofa"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#menu-table"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#moneyback"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-armchairs-office"></use>
    </svg>

    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-armchairs-relax"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-chairs-bar"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-chairs-eames"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-chairs-ghost"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-chairs-kids"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-chairs-masters"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-chairs-navy"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-chairs-panton"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-chairs-tolix"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-left"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-light-ceiling"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-light-floor"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-light-table"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-light-wall"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-others-clock"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-others-dekor"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-others-hangers"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-right"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-sofas-1"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-sofas-2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-sofas-3"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-tables-coffee"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-tables-supper"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#number1"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#number2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#number30"></use>
    </svg>

    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#pickup"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#rarr"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#rarr2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#rarr3"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#search"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#share-facebook"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#share-facebook2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#share-instagram"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#share-like"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#share-twitter"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#share-vk"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#share-vk2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#star-disabled"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#star-disabled2"></use>
    </svg>

    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#star-enabled"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#uarr"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#visa"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#wallet"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#warranty"></use>
    </svg>
<?
}
if (in_array($_SERVER['REMOTE_ADDR'], array('188.134.3.73', '5.144.98.9')) && @$_GET['ant'] == 1) {
    exit;
    Utils::writeLog( 'test', 'req_test', 'resp_test', 'log_test');

    exit;
}

// Получить товар по торговому предложению
if (in_array($_SERVER['REMOTE_ADDR'], array('188.134.3.73', '5.144.98.9')) && @$_GET['ant'] == 1) {
    exit;
    $intElementID = 18901; // ID предложения
    $mxResult = CCatalogSku::GetProductInfo(
        $intElementID
    );
    if (is_array($mxResult)) {
        echo 'ID товара = ' . $mxResult['ID'];
    } else {
        ShowError('Это не торговое предложение');
    }

    exit;
}

// Инфа по заказу
if (in_array($_SERVER['REMOTE_ADDR'], array('188.134.3.73', '5.144.98.9')) && @$_GET['ant'] == 1) {
    exit;
    $arSales = CSaleOrder::GetByID(11548);

    echo "<pre>";
    print_r($arSales);
    echo "</pre>";

    exit;
}

// Получаем список заказов
if (in_array($_SERVER['REMOTE_ADDR'], array('188.134.3.73', '5.144.98.9')) && @$_GET['ant'] == 1) {
    exit;

    $row = 0;
    $arPos = 0;
    $limit = 100001;
    $orderId = 0;

    IF (($handle = fopen("bills.csv", "r")) !== FALSE) {

        echo "<table border='1'>";
        echo "<tr>";
        echo "<td>" . "Номер заказа" . "</td>";
        echo "<td>" . "Общая сумма заказа" . "</td>";
        echo "<td>" . "Стоимость доставки" . "</td>";
        echo "<td>" . "Состав заказа" . "</td>";
        echo "<td>" . "Email покупателя" . "</td>";
        echo "<td>" . "Телефон покупателя" . "</td>";
        echo "</tr>";

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < $limit) {
            $row++;
            $code = implode($data);

            $ar = explode(";", $code);

            //if ($ar[1] != $orderId) {
                $orderId = $ar[1];

                if ($orderId > 0 && !empty($orderId)) {

                    // Данные заказа
                    $arFilter = Array('ID' => $orderId);
                    $arSales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter)->fetch();

                    // Данные юзера
                    $filter = Array("ID" => $arSales['USER_ID']);
                    $arUsers = CUser::GetList(($by = "personal_country"), ($order = "desc"), $filter, array("SELECT" => array("UF_*")))->Fetch();

                    // Данные о составе заказа
                    $rsItems = CSaleBasket::GetList(
                        array("ID" => "ASC"),
                        array("ORDER_ID" => $arSales['ID']),
                        false,
                        false,
                        array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "CATALOG_XML_ID")
                    );


                    if ($arUsers['EMAIL'] != 'demoriz@gmail.com') {
                        echo "<tr>";
                        echo "<td>" . $arSales['ID'] . "</td>";
                        echo "<td>" . $arSales['PRICE'] . "</td>";
                        echo "<td>" . $arSales['PRICE_DELIVERY'] . "</td>";

                        echo "<td>";
                        echo "<table>";

                        while ($arItems = $rsItems->fetch()) {
                            echo "<tr>";

                            $ar_res = CIBlockElement::GetByID($arItems['PRODUCT_ID'])->GetNext();

                            echo "<td>" . $ar_res['NAME'] . "</td>";
                            echo "<td>" . $arItems['QUANTITY'] . " шт.</td>";
                            echo "<td>" . "Цена: " . $arItems['PRICE'] . "</td>";

                            echo "</tr>";
                        }


                        echo "</table>";
                        echo "</td>";

                        echo "<td>" . $arUsers['EMAIL'] . "</td>";
                        echo "<td>" . $arUsers['PERSONAL_PHONE'] . "</td>";
                        echo "</tr>";

                    }
                }
            //}
        }

        echo "</table>";


        fclose($handle);
    }
    exit;
}

// Получить список товаров
if (in_array($_SERVER['REMOTE_ADDR'], array('188.134.3.73')) && @$_GET['ant'] == 1) {
    exit;

    // получаем категории
    $categoryList = CIBlockSection::GetList(
        array("SORT" => "ASC"),
        array(
            "IBLOCK_ID" => CATALOG_IBLOCK_ID,
            "ACTIVE" => "Y",
            "!ID" => array("180", "181", "157", "186", "185")
            //из каталога фильтр
        ),
        array("ELEMENT_SUBSECTIONS" => "Y", "CNT_ACTIVE" => "Y"),
        array(),
        false
    );

    $categoryArr = array();

    while ($ar_result = $categoryList->GetNext()) {
        if ($ar_result["ELEMENT_CNT"] > 0) {
            $categoryArr[$ar_result["ID"]] = array(
                "id" => $ar_result["ID"],
                "parent_id" => $ar_result["IBLOCK_SECTION_ID"] ? $ar_result["IBLOCK_SECTION_ID"] : 0,
                "parent_text" => $ar_result["IBLOCK_SECTION_ID"] ? "parentId=\"" . $ar_result["IBLOCK_SECTION_ID"] . "\"" : "",
                "name" => $ar_result["NAME"],
                "count" => 0
            );
            $cat_id[] = $ar_result["ID"];
        }
    }

    $CIBlockElement = CIBlockElement::GetList(
        $arOrder = Array("SORT" => "ASC"),
        $arFilter = Array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "ACTIVE" => "Y", "SECTION_ID" => $cat_id),
        $arGroupBy = false,
        $arNavStartParams = false,
        $arSelectFields = Array(
            "NAME",
            "IBLOCK_SECTION_ID",
            "CODE",
            "IBLOCK_ID",
            "ID",
            "DETAIL_TEXT",
            "DETAIL_PICTURE",
            "DETAIL_PAGE_URL",
        )
    );

    $tovars = array();
    while ($ob = $CIBlockElement->GetNextElement()) {

        $ar_result = $ob->GetFields();
        $ar_props = $ob->GetProperties();

        $tovars[$ar_result['ID']] = $ar_result;
        $tovars[$ar_result['ID']]['PROPS'] = $ar_props;
        $tovars_id[] = $ar_result['ID'];

    }

    $predl = CCatalogSKU::getOffersList(
        $tovars_id,
        '',
        array("AVAILABLE" => "Y", "ACTIVE" => "Y"),
        array("IBLOCK_ID", "ID", "CATALOG_QUANTITY", "CATALOG_PRICE_" . PRICE_ID, "CATALOG_GROUP_" . PRICE_ID, "NAME", "DETAIL_PAGE_URL", "CATALOG_STORE_AMOUNT_" . PRICE_ID),
        array('CODE' =>
            array(
                "FOTOGRAFIYA_5",
                "FOTOGRAFIYA_6",
                "TSVET_NOZHEK",
                "MATERIAL_STOLESHNITSY",
                "RAZMER_SH_KH_G_KH_V",
                "MATERIAL_STOLESHNITSY_1",
                "FOTOGRAFIYA_1",
                "KOD_TSVETA",
                "CML2_ATTRIBUTES",
                "UPAKOVKA_1_1",
                "TSVET_STOLESHNITSY",
                "FOTOGRAFIYA_2",
                "FOTOGRAFIYA_3",
                "FOTOGRAFIYA_4",
                "UPAKOVKA_2_1",
                "UPAKOVKA_3_1",
                "UPAKOVKA_4_1",
                "MATERIAL_NOZHEK",
                "MATERIAL_NOZHEK_1",
                "MATERIAL_SEDLA",
                "TSVET_NOZHEK_1",
                "TSVET_SEDLA",
                "MATERIAL_NOZHEK_2",
                "TOLSHCHINA_STOLESHNITSY_1",
                "TIP_POVERKHNOSTI",
                "TIP_POVERKHNOSTI_1",
                "TSVET_STOLESHNITSY_1",
                "TSVET_NOZHEK_2",
                "DIAMETR_STOLESHNITSY",
                "STRANA_PROISKHOZHDENIYA",
                "RAZMER_SH_KH_G_KH_V_1",
                "VYSOTA_DO_SIDENYA",
                "MAKSIMALNAYA_NAGRUZKA",
                "MAKSIMALNAYA_NAGRUZKA_1",
                "VYSOTA_DO_SIDENYA_1",
                "MATERIAL_NOZHEK_3",
                "RAZMER_SH_KH_G_KH_V_2",
                "VYSOTA_PODLOKOTNIKOV",
                "VYSOTA_PODLOKOTNIKOV_1",
                "DIAMETR_STOLESHNITSY_1",
                "RAZMER_STOLESHNITSY",
                "VES",
                "VYSOTA_STOLESHNITSY",
                "VYSOTA_STOLESHNITSY_1",
                "VYSOTA_SIDENYA_2",
                "RAZMER_STOLESHNITSY_1",
                "NOZHKI",
                "GABARITY_SH_KH_G_KH_V",
                "MATERIAL",
                "CML2_LINK",
                "ARRIVAL_DATE",
            )
        )
    );

    //свойств предложение которые не попадают params
    $not_display_offer_props = array(
        "FOTOGRAFIYA_1",
        "FOTOGRAFIYA_2",
        "FOTOGRAFIYA_3",
        "FOTOGRAFIYA_4",
        "FOTOGRAFIYA_5",
        "FOTOGRAFIYA_6",
        "KOD_TSVETA",
        "CML2_LINK"
    );
    //дублирование свойств?
    $not_display_prop = array(
        "MIN_CHECK",
        "MIN_QTY",
        "MIN_PRICE",
        "STATUS_ZAKAZA",
        "FOTOGRAFIYA_5",
        "FOTOGRAFIYA_6",
        "FOTOGRAFIYA_1",
        "CML2_BAR_CODE",
        "CML2_TRAITS",
        "CML2_BASE_UNIT",
        "CML2_TAXES",
        "MORE_PHOTO",
        "CML2_FILES",
        "KOD_TSVETA",
        "RECOMM",
        "HIT",
        "NEW",
        "SALE",
        "WITH_THIS",
        "RELATED",
        "INTERIOR",
        "ARRIVAL_DATE",
        "RUSSKAYA_TRANSKRIPTSIYA",
        "FOTOGRAFII_V_INTERERE",
        "UPAKOVKA_1",
        "UPAKOVKA_1_2",
        "UPAKOVKA_2",
        "DEAKTIVIROVAT_NA_SAYTE",
        "ID",
        "UPAKOVKA_3",
        "DEAKTIVIROVAT_NA_SAYTE_1",
        "SITE",
        "UPAKOVKA_4",
        "UPAKOVKA_1_3",
        "RASPRODAZHA",
        "IDPOLZOVATELYA",
        "UPAKOVKA_5",
        "KHIT_PRODAZH",
        "UPAKOVKA_6",
        "REKOMENDUEM",
        "UPAKOVKA_5_1",
        "UPAKOVKA_6_1",
        "DATA_POLUCHENIYA_PODTVERZHDENIYA_EDO",
        "FOTOGRAFIYA_2",
        "FOTOGRAFIYA_3",
        "FOTOGRAFIYA_4",
        "UPAKOVKA_2_1",
        "UPAKOVKA_3_1",
        "UPAKOVKA_4_1",
        "MATERIAL_NOZHEK_1",
        "MATERIAL_NOZHEK_2",
        "TIP_POVERKHNOSTI_1",
        "TOLSHCHINA_STOLESHNITSY_1",
        "TSVET_STOLESHNITSY",
        "TSVET_NOZHEK",
        "RAZMER_SH_KH_G_KH_V",
        "MATERIAL_STOLESHNITSY",
        "CML2_ATTRIBUTES",
        "FILES",
        "UPAKOVKA_1_1",
        "D_AND_M",
        "OTHER_CONFIG",
        "RAZMER_SH_KH_G_KH_V_2",
        "VYSOTA_DO_SIDENYA_1",//не пустое val
        "MAKSIMALNAYA_NAGRUZKA_1",//не пустое val
    );
    $from = array('"', '&', '>', '<', '\'');
    $to = array('&quot;', '&amp;', '&gt;', '&lt;', '&apos;');


    $offers = false;

    echo "<table border='1'>";

    echo "<tr>";
    echo "<td>" . "Наименование" . "</td>";
    echo "<td>" . "Код/штрихкод" . "</td>";
    echo "<td>" . "Цена" . "</td>";
    echo "<td>" . "Ед. изм. (шт/кг/л)" . "</td>";
    echo "<td>" . "Короткое наименование" . "</td>";
    echo "<td>" . "Группа" . "</td>";
    echo "<td>" . "Экран" . "</td>";
    echo "<td>" . "НДС*" . "</td>";
    echo "</tr>";

    foreach ($predl as $key => $value) {
        if (count($value) > 1):
            $offers = true;
        else:
            $offers = false;
        endif;


        foreach ($value as $key1 => $value1) {

            if ($value1["CATALOG_QUANTITY"] >= 0) {

                $name = str_replace($from, $to, str_replace(REPLACE_FOR_YANDEX, "", $value1["NAME"]));
                $opt_price = CCatalogProduct::GetOptimalPrice($value1["ID"], "1", array(), "N");

                echo "<tr>";
                echo "<td>" . $name . "</td>";
                echo "<td>" . $value1["ID"]. "</td>";
                echo "<td>" . $opt_price["PRICE"]["PRICE"] . "</td>";
                echo "<td>" . "шт" . "</td>";
                echo "<td>" . "" . "</td>";
                echo "<td>" . "" . "</td>";
                echo "<td>" . "Товары" . "</td>";
                echo "<td>" . "-" . "</td>";
                echo "</tr>";

                unset($match);

            }
        }
    }

    echo "</table>";

    unset($tovars);
    unset($predl);

    unset($key);
    unset($value);

    unset($key1);
    unset($value1);

    exit;
}
