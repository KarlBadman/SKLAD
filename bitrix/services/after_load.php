<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->SetTitle("");

use Bitrix\Main\Diag\Debug;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use RetailCrm\Http\Client as RetailCrmClient;

CModule::IncludeModule('catalog');

const BASE_CATALOG = 35;      // Основной каталог товаров
const PACKAGE_OFFERS_OF_BASE_CATALOG = 36;  // Пакет предложений (Основной каталог товаров)

//пролопатит все категории и все товары. Чтобы выставить связку по свойству товара со всеми его категориями.
//И привяжет к разделу скидок
function setMultiSections()
{
    set_time_limit(0);
    ignore_user_abort(true);
    define('NO_KEEP_STATISTIC', true);
    define('NOT_CHECK_PERMISSIONS', true);
    define('BX_NO_ACCELERATOR_RESET', true);
    $arSection = array();

    CModule::IncludeModule('iblock');
    $arFilter = Array(
        "IBLOCK_ID" => BASE_CATALOG
    );

    //сбор всех категорий в массив xml_id => id
    $secs = CIBlockSection::GetList(array("ID" => "ASC"), $arFilter, false, array("ID", "XML_ID"));
    while ($obSec = $secs->GetNext()) {
        $arSection[$obSec["XML_ID"]] = $obSec["ID"];
    }

    #@TODO проверить возможность работы только с измененными товарами под вопросом
    //если параметра нет, то только свеже измененные товары
    //if(empty($fullrebind)){ $arFilter["<TIMESTAMP_X"] = ; }

    //перебор товаров
    $items = CIBlockElement::GetList(array("ID" => "ASC"), $arFilter, false, false, Array("IBLOCK_ID", "ID"));
    try {
        while ($obItem = $items->GetNextElement()) {
            $arElemField = $obItem->GetFields();

            //выбираем разделы товара
            $sectionsSourceIDs  = \Bitrix\Iblock\SectionElementTable::getList(array(
                'select' => array('IBLOCK_SECTION_ID'),
                'filter' => array('IBLOCK_ELEMENT_ID' =>$arElemField['ID']),
            ))->fetchAll();
            $sectionsSourceIDs = array_map(function($item){return $item['IBLOCK_SECTION_ID'];}, $sectionsSourceIDs);

            $sectionsIncomingIDs = array();
            $res = CIBlockElement::GetProperty($arElemField['IBLOCK_ID'], $arElemField['ID'], array("sort" => "asc"), Array("CODE" => "CML2_TRAITS"));
            while ($section_info = $res->GetNext()) {
                if ($section_info["DESCRIPTION"] == 'Раздел') {
                    $arSec = explode('#', $section_info['VALUE']);
                    if (isset($arSection[$arSec[2]])) {
                        $sectionsIncomingIDs[] = $arSection[$arSec[2]];
                    }
                }
            }
            //проверка есть ли разница в ценах на товар, чтобы добавить товар в раздел скидок
            $dbProductPrice = CPrice::GetListEx(
                array(),
                array("PRODUCT_ID" => $arElemField["ID"]),
                false,
                false,
                array("ID", "CATALOG_GROUP_ID", "PRICE")
            );
            $i = 0;
            $first = array();
            while ($row = $dbProductPrice->fetch()) {
                if($i == 0) {
                    $first = $row;
                } elseif($row['PRICE'] < $first['PRICE']) {
                    $sectionsIncomingIDs[] = \Dsklad\Config::getParam('section/discounts');
                    break;
                }
                $i++;
            }

            //привязка к разделам если она изменилась, хотя это кажется избыточным
            if (!($sectionsSourceIDs == $sectionsIncomingIDs && count($sectionsIncomingIDs) > 0)) {
                CIBlockElement::SetElementSection($arElemField["ID"], array_unique(array_merge($sectionsSourceIDs, $sectionsIncomingIDs))); //Возвращает true если элемент успешно привязан к разделу/разделам и вернёт false если привязка не удалась, а так же false вернётся если элемент привязан к тем же разделам к которым вы хотите их привязать.
                \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(BASE_CATALOG, $arElemField["ID"]);
            }
        }
        return true;
    } catch(Exception $e) {
        return false;
    }
}

//деактивиреут товары по флагу из 1С
function DeactivateProducts()
{
    $result = array();
    $arFilter = array(
        'IBLOCK_ID' => array(BASE_CATALOG, PACKAGE_OFFERS_OF_BASE_CATALOG),
        array(
            "LOGIC" => "OR",
            array('PROPERTY_DEAKTIVIROVAT_NA_SAYTE_1_VALUE' => 'Да',),
            array('PROPERTY_DEAKTIVIROVAT_NA_SAYTE_VALUE' => 'Да'),
        ),
        'ACTIVE' => 'Y'
    );

    $db_res = CIBlockElement::GetList(array("SORT" => "ASC"), $arFilter, false, false, array('ID'));
    $el = new CIBlockElement;

    while ($arElement = $db_res->GetNext()) {
        $updateinfo = $el->Update($arElement["ID"], Array("ACTIVE" => "N"));
        $result[$updateinfo]++;
    }
    return $result;
}

//проверяем цвета и добавляем в хайлоад цветов
function checkColors()
{
    Loader::includeModule('highloadblock');
    $arHLBlock = HL\HighloadBlockTable::getById(21)->fetch();
    $obEntity = HL\HighloadBlockTable::compileEntity($arHLBlock);
    $strEntityDataClass = $obEntity->getDataClass();

    $arSelect = array(
        'ID',
        'PROPERTY_KOD_TSVETA',
        'NAME'
    );
    $arFilter = array(
        'IBLOCK_ID' => array(BASE_CATALOG, PACKAGE_OFFERS_OF_BASE_CATALOG)
    );
    $db_res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    $checked = array();
    while ($arElement = $db_res->GetNext()) {
        $arColor = explode("#", $arElement['PROPERTY_KOD_TSVETA_VALUE']);
        if (!in_array($arColor[1], $checked)) {
            $arFilter = array('filter' => array('UF_1C_CODE' => $arColor[1]));
            $hl = new $strEntityDataClass;
            $arColors = $hl->getList($arFilter)->fetch();
            if (empty($arColors)) {
                $arFields = array(
                    "UF_NAME" => $arColor[0],
                    "UF_1C_CODE" => $arColor[1],
                    "UF_RGB" => $arColor[2] . ',' . $arColor[3] . ',' . $arColor[4]
                );
                $hl->add($arFields);
            }
        }
        $checked[] = $arColor[1];
    }
}


//расчет остатка для составной конкретной позиции из расчета кол-ва каждого входящего в комплект элемента
function warehousesCalculator($stocks)
{
    $result = array();
    foreach($stocks as $stock){
        if(empty($result)) {
            $result = $stock;
        } else {
            foreach ($stock as $k=>$v){
                $result[$k] = ($result[$k] < $v) ? $result[$k] : $v;
            }
        }
    }
    return $result;
}

//Проставляем остатки сборным товарам (например, из ног и сёдел)
function stockBalanceCalculator()
{
    $result = array();
    $elements = array();
    $parts = array();
    $partsAmounts = array();
    $sostavPropertiesCount = 6;
    $sostavProperties = array('PROPERTY_SOSTAV');
    $sostavPropertiesFilter = array("LOGIC" => "OR",array('!PROPERTY_SOSTAV' => false));
    for($i = 1; $i < $sostavPropertiesCount; ++$i) {
        array_push($sostavProperties, $sostavProperties[0] . '_' . $i);
        array_push($sostavPropertiesFilter, array('!' . $sostavProperties[0] . '_' . $i => false));
    }

    $arFilter = array(
        'IBLOCK_ID' => array(BASE_CATALOG, PACKAGE_OFFERS_OF_BASE_CATALOG),
        array(
            "LOGIC" => "AND",
            $sostavPropertiesFilter,
            array('ACTIVE' => 'Y'),
        )
    );
    //выбрали все сборные товары (в том числе комплекты)
    $dbRes = CIBlockElement::GetList(array("SORT" => "ASC"), $arFilter, false, false, array_merge(array('ID'), $sostavProperties));
    while ($arElement = $dbRes->GetNext()) {
        $elParts = array();
        foreach($sostavProperties as $property) {
            if(!empty($arElement[$property . '_VALUE'])) {
                $sostav = $arElement[$property . '_VALUE'];
                break;
            }
        }
        foreach (explode(';', $sostav) as $values) {
            if(!empty($values)){
                $tmp = explode('#', $values);
                //почему в 1С такая дичь - не ведомо, но тут заменяем разделитель товар-товарное предложение на решетку, чтобы сформировать корректный xml_id по товарному предложению для выборки
                $key = str_replace('@', '#', $tmp[0]);
                /*
                //КОСТЫЛИНА ДЛЯ ИСКЛЮЧЕНИЯ ВСЕХ ТОВАРОВ С НАТУР НОГАМИ (19700) потому что они обычно всегда есть, а склад по остаткам врёт
                if($key =='6cf2d1ec-9d93-11e5-80cb-0050569c0a68'){
                    continue 2;
                }
                */
                $elParts[$key] = $tmp[1];
                $parts[] = $key;
            }
        }
        $elements[] = array(
            'ID' => $arElement['ID'],
            'SOSTAV' => $elParts,
        );
        unset($elParts);
    }
    //сформировали массив уникальных комплектующих для составных товаров
    $parts = array_values(array_unique($parts));

    $arFilter = array(
        'IBLOCK_ID' => array(BASE_CATALOG, PACKAGE_OFFERS_OF_BASE_CATALOG),
        'XML_ID' => $parts
    );

    $dbRes = CIBlockElement::GetList(array("SORT" => "ASC"), $arFilter, false, false, array('ID', 'XML_ID'));
    $parts = array();
    while ($arElement = $dbRes->GetNext()) {
        $parts[$arElement['XML_ID']] = $arElement['ID'];
    }

    //собрали массив остатков по складам для каждой комплектующей
    $rsStoreProduct = \Bitrix\Catalog\StoreProductTable::getList(array(
        'filter' => array('=PRODUCT_ID'=>$parts,'STORE.ACTIVE'=>'Y'),
    ));
    while($arStoreProduct=$rsStoreProduct->fetch()) {
        $partsAmounts[$arStoreProduct['PRODUCT_ID']][$arStoreProduct['STORE_ID']] = $arStoreProduct['AMOUNT'];
    }

    //заполнили у составных товаров данные по остаткам конкретных комплектующих
    //тут хитрость в том, что в состав товара могут входить 4 сборных стула и 1 стол. Тогда нужно остаток ног и седеньев делить на 4 с округлением в меньшую, чтобы знать какое обеспечение доступно для комплекта. И из полученных величин выбирать меньшее
    //т.о. если на складе 3 стола и дофига ног и седеньев, то остаток комплекта = 3. А если столов 1000, ног 299, а сиденьев 400, то расчетно комплектов на остатке 99, потому что минимальный остаток обеспечения у ног и это 299/4 и округленное в меньшую
    foreach($elements as &$element) {
        foreach($element['SOSTAV'] as $k => $needed_count) {
            $element['WAREHOUSES'][] = array_map(function ($item) use ($needed_count){return floor($item/$needed_count);}, $partsAmounts[$parts[$k]]);
        }
        //перезаполнили остатков на складе согласно минимальным расчетным величинам
        $element['WAREHOUSES'] = warehousesCalculator($element['WAREHOUSES']);
    }

    //обновляем
    foreach($elements as $element) {
        foreach ($element['WAREHOUSES'] as $k=>$v) {
            $arFields = Array(
                "PRODUCT_ID" => $element['ID'],
                "STORE_ID" => $k,
                "AMOUNT" => $v,
            );
            try {
                $ID = CCatalogStoreProduct::UpdateFromForm($arFields);
            } catch (Exception $e) {
                var_dump($e);
            }
        }
    }
    unset($arFilter, $parts, $elements);
    return $result;
}

/**
 * Сотонина для обновления "доступное количество" у товарных предложенией и попутной передачи данных в crm
 * необходима, потому что пока не разрешено включать "Складской учёт" в настройках торгового каталога
 *
 * @param bool $reportMode - флаг режима работы отладки
 * @param bool $retailFullUpdate - флаг отправки всех остатков и закупочных цен в crm (товары с нулевыми остатками игнорируются на стороне crm), если нужно см. след. флаг
 * @param bool $retailUpdateWithZeroAmountPushPrice - флаг отправки закупочных цен в crm ТОЛЬКО для товаров с нулевым остатком (выставит остаток = 1 и отправит), потом запусти функцию со след.флагом
 * @param bool $retailUpdateWithZeroAmountSet - флаг отправки закупочных цен и корректного остатка (т.е. нулевого) в crm ТОЛЬКО для товаров с нулевым остатком
 * @return string
 */
function updateProductsAvailableQuantity ($reportMode = false, $retailFullUpdate = false, $retailUpdateWithZeroAmountPushPrice = false, $retailUpdateWithZeroAmountSet = false) {
    $elements = array();
    $activeElements = array();
    $tovarsArticle = array();
    $offers = array();
    $mailInformer = false;
    $currentRate = array();

    // Получаем товары
    $CIBlockElement = CIBlockElement::GetList(
        $arOrder = array("SORT" => "ASC"),
        $arFilter = array(
            "IBLOCK_ID" => BASE_CATALOG,
            "ACTIVE" => "Y",
        ),
        $arGroupBy = false,
        $arNavStartParams = false,
        $arSelectFields = array("ID", "PROPERTY_CML2_ARTICLE")
    );
    while ($ob = $CIBlockElement->GetNextElement()) {
        $arResult = $ob->GetFields();
        $tovarsArticle [$arResult['ID']] = $arResult['PROPERTY_CML2_ARTICLE_VALUE'];
        $tovarsId[] = $arResult['ID'];
    }

    // Получаем офферы
    $predl = CCatalogSKU::getOffersList(
        $tovarsId,
        '',
        array(
            "ACTIVE" => "Y",
            "IBLOCK_ID" => PACKAGE_OFFERS_OF_BASE_CATALOG,
        ),
        array('ID', 'CATALOG_QUANTITY', 'PROPERTY_FREEZE_STOCK_CHANGING', 'NAME')
    );

    foreach($predl as $product){
        foreach($product as $offer){
            $activeElements[$offer['ID']]['FREEZE_STOCK_CHANGING'] = (bool)$offer['PROPERTY_FREEZE_STOCK_CHANGING_VALUE'];
            $activeElements[$offer['ID']]['CATALOG_QUANTITY'] = $offer['CATALOG_QUANTITY'];
            $activeElements[$offer['ID']]['NAME'] = $offer['NAME'];
            $activeElements[$offer['ID']]['PARENT_ID'] = $offer['PARENT_ID'];
        }
    }

    $stockFilter = array('STORE.ACTIVE'=>'Y');
    if($retailUpdateWithZeroAmountPushPrice) {
        $stockFilter['AMOUNT'] = 0;
        $stockFilter['>PRODUCT.PURCHASING_PRICE'] = 0;
    }
    $rsStoreProduct = \Bitrix\Catalog\StoreProductTable::getList(array(
        'filter' => $stockFilter,
        'select' => array('ID', 'STORE_ID', 'PRODUCT_ID', 'PRODUCT_PURCHASING_PRICE' => 'PRODUCT.PURCHASING_PRICE', 'PRODUCT_PURCHASING_CURRENCY' => 'PRODUCT.PURCHASING_CURRENCY', 'AMOUNT', 'STORE_CODE' => 'STORE.CODE', 'STORE_TITLE' => 'STORE.TITLE'),
    ));

    while($arStoreProduct = $rsStoreProduct->fetch()) {
        if(!empty($arStoreProduct['STORE_CODE'])) {
            $stores = array('code' => $arStoreProduct['STORE_CODE'], 'available' => ($retailUpdateWithZeroAmountPushPrice && !$retailUpdateWithZeroAmountSet) ? 1: $arStoreProduct['AMOUNT']);
            //purchasePrice
            if(!empty($arStoreProduct['PRODUCT_PURCHASING_PRICE']) && !empty($arStoreProduct['PRODUCT_PURCHASING_CURRENCY'])) {
                if(empty($currentRate[$arStoreProduct['PRODUCT_PURCHASING_CURRENCY']])){
                    $currentRate[$arStoreProduct['PRODUCT_PURCHASING_CURRENCY']] = Utils::getBankCurrency($arStoreProduct['PRODUCT_PURCHASING_CURRENCY']);
                }
                $stores['purchasePrice'] = number_format( $arStoreProduct['PRODUCT_PURCHASING_PRICE'] * $currentRate[$arStoreProduct['PRODUCT_PURCHASING_CURRENCY']], 2, '.', '');;
            }
            $stores = array($stores, array_replace($stores,  array('code' => "sklad-msk"),  array('code' => "sklad-msk-n")));
            $elements[$arStoreProduct['PRODUCT_ID']]['stores'] = $stores;

            unset($stores);
        } elseif(!$mailInformer) {
            $mailInformer = true;
            $transport = new Swift_SmtpTransport('localhost', 25);
            $mailer = new Swift_Mailer($transport);
            $body = 'Не указан символьный код для активного склада. Нет возможности отправить остатки без символьного кода для склада: <b>"' . $arStoreProduct['STORE_TITLE'] . '"</b>. Проверьте код в RetailCrm и внесите такие же настройки в настройки склада на сайте.';
            $receivers = array(
                'dev@ooott.ru',
            );
            $message = (new Swift_Message('Возникла ошибка обновления остатков в RetailCrm'))
                ->setFrom(['info@dsklad.ru' => 'info@dsklad.ru'])
                ->setTo($receivers)
                ->setBody($body, 'text/html');
            $result = $mailer->send($message);
        }
        $elements[$arStoreProduct['PRODUCT_ID']]['WAREHOUSES_QUANTITY'] += $arStoreProduct['AMOUNT'];
    }

    $countZero = 0;

    $output = '<style>
        table {
          font-family: arial, sans-serif;
          border-collapse: collapse;
          width: 100%;
        }
        
        td, th {
          border: 1px solid #000;
          text-align: left;
          padding: 8px;
        }
        
        tr:nth-child(even) {
          background-color: #dddddd;
        }</style>';
    $output .='<table ><th>ID товара</th><th>Артикул товара</th><th>Текущее значение на сайте</th><th>Значение из обмена</th><th>Наименование товара</th><th>Что должно было произойти после обмена</th>';

    foreach ($elements as $element=>$quantities) {
        $CatalogQuantity = (int)$activeElements[$element]['CATALOG_QUANTITY'];
        if($retailFullUpdate){
            $offers[] = array(
                'externalId' => $element,
                'stores' => $quantities['stores'],
            );
        }
        if(in_array($element, array_keys($activeElements)) && $quantities['WAREHOUSES_QUANTITY'] != $CatalogQuantity) {
            // Условие пока принудительно падает в false, чтобы активировать, удали false и будут проверяться и не будут автообновляться товары, которые на сайте есть, но на складе кончились. Конечно, если у них есть флаг заморозки.
            //ps сейчас удалил false && из условия
            $warehousesQuantity = ( $quantities['WAREHOUSES_QUANTITY'] == 0) ? true: false;
            if(($warehousesQuantity || $CatalogQuantity == 0) && $activeElements[$element]['FREEZE_STOCK_CHANGING']) {
                $output .= '<tr><td>'. $element .'</td>
                <td>' . $tovarsArticle[$activeElements[$element]['PARENT_ID']] . '</td>
                <td>' . $activeElements[$element]['CATALOG_QUANTITY'] . '</td>
                <td>' . $quantities['WAREHOUSES_QUANTITY'] . '</td>
                <td>' . $activeElements[$element]['NAME'] . '</td>';
                if($CatalogQuantity == 0) {
                    $output .= '<td>Выведен статус "доступно" и кнопка "купить"</td>';
                } else {
                    $output .= '<td>Выведен статус "ожидается" и включена кнопка "предзаказ"</td>';
                }
                '</tr>';
                $countZero++;
            } elseif(!$reportMode) {
                CCatalogProduct::Update($element, ['QUANTITY' => $quantities['WAREHOUSES_QUANTITY']]);
            } else {
                echo '<p>будут изменения по ' . $activeElements[$element]['NAME'] . ' станет ' . $quantities['WAREHOUSES_QUANTITY'] . '</p>';
            }
            if(!$retailFullUpdate) {
                $offers[] = array(
                    'externalId' => $element,
                    'stores' => $quantities['stores'],
                );
            }
        }
    }

    if(count($offers) > 0)
        updateRetailOffersCountStock($offers);

    $output .= '</table>';
    $output .= '<p>Итого требующих внимания: ' . $countZero . '</p>';
    if($countZero == 0) $output = 0;
    return $output;
}

function updateRetailOffersCountStock($offers) {
    if (!CModule::IncludeModule("intaro.retailcrm")) {
      //  file_put_contents(__DIR__ . '/mdirect_errors.log', sprintf("[%s] ", date('Y-m-d H:i:s')) . "Ошибка: не найден модуль retailcrm" . PHP_EOL, FILE_APPEND);
        die();
    }
    echo 'Данных о товарах подготовленных к отправке в crm: ' . (count($offers));
    $apiHost = COption::GetOptionString("intaro.retailcrm", "api_host");
    $apiKey = COption::GetOptionString("intaro.retailcrm", "api_key");

    $url = $apiHost . '/api/v5';
    $client = new RetailCrmClient($url, array('apiKey' => $apiKey));

    //бьем на блоки, чтобы не сработало ограничение api retailCrm
    foreach(array_chunk($offers, 200) as $chunk) {
        $parameters['offers'] = json_encode($chunk);
        $res = $client->makeRequest(
            '/store/inventories/upload',
            'POST',
            $parameters
        );
        if ($res->isSuccessful()) {
            echo '<p>SUCCESS</p>';
        } else {
            echo '<p><b style="color:red">ERROR</b></p>';
            var_dump($res);
        }
    }
}

$request = Application::getInstance()->getContext()->getRequest();
$reportMode = ($request->getPost('report_mode')) ? $request->getPost('report_mode') : $request->getQuery('report_mode');
$reportMode = !empty(@$argv[1]) ? @$argv[1] : $reportMode;
$reportMode = (bool)$reportMode;

$sendMail = ($request->getPost('send_mail')) ? $request->getPost('send_mail') : $request->getQuery('send_mail');
$sendMail = !empty(@$argv[2]) ? @$argv[2] : $sendMail;
$sendMail = (bool)$sendMail;

$updateRetailPrices = ($request->getPost('update_retail_price')) ? $request->getPost('update_retail_price') : $request->getQuery('update_retail_price');
$updateRetailPrices = !empty(@$argv[3]) ? @$argv[3] : $updateRetailPrices;
$updateRetailPrices = (bool)$updateRetailPrices;

$updateRetailPricesWithZeroAmount = ($request->getPost('update_retail_zero_amount')) ? $request->getPost('update_retail_zero_amount') : $request->getQuery('update_retail_zero_amount');
$updateRetailPricesWithZeroAmount = !empty(@$argv[4]) ? @$argv[4] : $updateRetailPricesWithZeroAmount;
$updateRetailPricesWithZeroAmount = (bool)$updateRetailPricesWithZeroAmount;

if($updateRetailPrices) {
    echo '<b>Обновление ВСЕХ данных в crm по ценам и остаткам (КРОМЕ НУЛЕВЫХ) запущено</b>';
    updateProductsAvailableQuantity(true, true);
    echo '</br>для запуска обновления товаров с нулевыми остатками перейдите по <a href="/bitrix/services/after_load.php?update_retail_zero_amount=1">ссылке</a>';
    die;
}

if($updateRetailPricesWithZeroAmount) {
    echo '<b>Обновление данных в crm ТОЛЬКО по товарам с нулевым остатком по ценам запущено</b>';
    updateProductsAvailableQuantity(true, true, true);
    updateProductsAvailableQuantity(true, true, true, true);
    die;
}

if($reportMode) { // только отчет об обновляемых товарах
    $body = updateProductsAvailableQuantity($reportMode); //выключить после настройки складского учёта
    if($sendMail && !empty($body)){
        $transport = new Swift_SmtpTransport('localhost', 25);
        $mailer = new Swift_Mailer($transport);

        $body = '<h4>Расхождения между остатками и доступным количеством у товаров с флагом "Отключить изменение доступного количества товара при обмене"</h4>' . $body . ' <a href="http://wiki.toptrade.local/?p=2447">Подробности в вики</a>';

        $receivers = array(
            'dev@ooott.ru',
            'tigran@dsklad.ru',
            'a.afanaseva@ooott.ru',
            'a.kostetckaia@dsklad.ru',
            'a.malceva@dsklad.ru',
            'd.checherin@ooott.ru',
        );
        $message = (new Swift_Message('Расхождения между остатками и доступным количеством'))
            ->setFrom(['info@dsklad.ru' => 'info@dsklad.ru'])
            ->setTo($receivers)
            ->setBody($body, 'text/html');
        $result = $mailer->send($message, $error);
    } else {
        echo $body;
    }
} else {
    DeactivateProducts();
    checkColors();
    setMultiSections(); //Тут ещё и вяжутся теги, нужно будет переписать и выключить привязку разделов после новой структуры в 1С (когда группировки будут заданы нормально в рамках узлов обмена)
    stockBalanceCalculator();
    updateProductsAvailableQuantity(); //выключить после настройки складского учёта
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/cron/updateUpakovki/updateUpakovki.php");
}