<?
use Bitrix\Main;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Event;
use Bitrix\Sale;

Loader::includeModule('sale');

// Main\EventManager::getInstance()->addEventHandler('sale', 'OnBeforeOrderAdd', 'OnBeforeOrderAddHandler');
Main\EventManager::getInstance()->addEventHandler('sale', 'OnOrderSave', 'OnOrderSaveHandler');
// Main\EventManager::getInstance()->addEventHandler('sale', 'OnOrderAdd', 'OnOrderAddHandler');
// Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleBeforeStatusOrder', 'OnSaleBeforeStatusOrderHandler');
// Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleStatusOrder', 'OnSaleStatusOrderHandler');
Main\EventManager::getInstance()->addEventHandler('sale', 'OnOrderAdd', 'OnOrderAddHandler');

//function OnOrderAddHandler($id,$arFields){
//   \Bitrix\Main\Diag\Debug::dumpToFile($arFields, $varName = '', $fileName = "mylog444.txt");
//
//}
function OnBeforeOrderAddHandler(&$arFields) {}

function OnOrderSaveHandler($orderId, $arFields, $arOrder, $isNew)
{
    /*	AddMessage2Log(
            '---------- TEST ----------' . PHP_EOL .
            __FILE__ . PHP_EOL .
            __FUNCTION__ . PHP_EOL .
            'orderId: '. $orderId . PHP_EOL .
            'arFields: '. print_r($arFields, true) . PHP_EOL .
            'arOrder: '. print_r($arOrder, true) . PHP_EOL .
            'isNew: '. $isNew . PHP_EOL .
            'DEBUGTRACE: '. print_r(debug_backtrace(), true)
        );*/
    if (!$isNew) {
        return true;
    }

    if (empty($arOrder['ORDER_PROP'][3]) && empty($arOrder['ORDER_PROP'][7])) { // e-mail
        return true;
    }

    $order = Sale\Order::load($orderId);
    $basket = $order->getBasket();

    // Проверяем отправлено ли письмо с информацией о заказе клиенту.
    $propertyCollection = $order->getPropertyCollection();
    $orderPersonID = $order->getPersonTypeId();
    if ($orderPersonID == 1) { // ФЛ
        $propUserEmailSend = $propertyCollection->getItemByOrderPropertyId(15); // 15 - USER_EMAIL_SEND
    } elseif ($orderPersonID == 2) { // ЮЛ
        $propUserEmailSend = $propertyCollection->getItemByOrderPropertyId(16); // 16 - USER_EMAIL_SEND
    }

    if (($propUserEmailSend->getValue() == 'Y')
        || $order->getSumPaid() // если есть оплаченная сумма
        || $order->isPaid() // если оплачен
        || $order->isAllowDelivery() // если разрешена доставка
        || $order->isShipped() // если отправлен
        || $order->isCanceled()) // если отменен
    {
        return true;
    }

    Loader::includeModule('iblock');

    $arFields['ORDER_LIST'] .= '<table bgcolor="ffffff" width="100%" rules="rows" border="1" bordercolor="#e1e1e1" align="left" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0; border: 1px solid #e1e1e1;">
						 <colgroup><col width="252" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">
						 <col width="64" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">
						 <col width="92" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">
						 <col width="90" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;"> </colgroup>
						<tbody><tr bgcolor="e6e6e6" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">
							<td class="top_pad bottom_pad left_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 0 7px 10px;" valign="top">
								 Наименование
							</td>
							<td class="top_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 0 0;" valign="top">
								 Кол-во
							</td>
							<td class="top_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 0 0;" valign="top">
								 Цена
							</td>
							<td class="top_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 0 0;" valign="top">
								 Сумма
							</td>
						</tr>';// формируем новый ORDER_LIST
    $arItems = array(); // массив товаров с нужными данными

    //Перебор всех товаров в заказе
    foreach ($basket as $basketItem) {
        //Получаем свойства товара в корзине
        $property = $basketItem->getPropertyCollection();
        $arProps = $property->getPropertyValues();

        //Выборка элемента (пакет предложений)
        $rsElements = CIBlockElement::GetList(Array("SORT" => "ASC"), Array("ID" => $basketItem->getField('PRODUCT_ID'), "IBLOCK_TYPE" => '1c_catalog'), false, false, Array("ID", "NAME", "PROPERTY_CML2_LINK", "PROPERTY_CML2_ARTICLE"));
        if ($arElement = $rsElements->Fetch()) {
            //Выборка данных из основного каталога
            if ($arElement['PROPERTY_CML2_LINK_VALUE']) {
                $rsElementsCatalog = CIBlockElement::GetList(Array("SORT" => "ASC"), Array("ID" => $arElement['PROPERTY_CML2_LINK_VALUE'], "IBLOCK_TYPE" => '1c_catalog'), false, false, Array("ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PROPERTY_MATERIAL", "PROPERTY_CML2_ARTICLE", "DETAIL_PAGE_URL"));
                if ($arElementCatalog = $rsElementsCatalog->Fetch()) {
                    //P($arElementCatalog);
                    if ($arElementCatalog['DETAIL_PICTURE']) {
//                        $foto2 = CFile::MakeFileArray($arElementCatalog['DETAIL_PICTURE']);
//                        $arrFotoNewOrder = CFile::SaveFile($foto2, "new_order");
//                        $arPicture = CFile::ResizeImageGet($arrFotoNewOrder, array('width' => 40, 'height' => 44));
                        $arPicture = CFile::ResizeImageGet($arElementCatalog['DETAIL_PICTURE'], Array("width" => 40, "height" => 40));
                    } else {
                        $arPicture['src'] = false;
                    }

                    if ($arElementCatalog['DETAIL_PAGE_URL']) {
                        $section = CIBlockSection::GetByID($arElementCatalog['IBLOCK_SECTION_ID'])->Fetch();
                        $arElementCatalog['DETAIL_PAGE_URL'] = str_replace(Array('#SITE_DIR#', '#SECTION_CODE#', '#ELEMENT_CODE#'), Array('http://' . $GLOBALS['SERVER_NAME'], $section['CODE'], $arElementCatalog['CODE']), $arElementCatalog['DETAIL_PAGE_URL']);
                        unset($section);
                    }

                    $arItems[] = Array(
                        "NAME" => $arElement['NAME'], //$arElementCatalog['NAME'],
                        "URL" => $arElementCatalog['DETAIL_PAGE_URL'],
                        "ARTICLE" => $arElementCatalog['PROPERTY_CML2_ARTICLE_VALUE'],
                        "MATERIAL" => $arElementCatalog['PROPERTY_MATERIAL_VALUE'],
                        "PICTURE" => $arPicture['src'],
                        "PRICE" => number_format($basketItem->getPrice(), 0, '.', ' ') . ' руб.',
                        "SUMM" => number_format($basketItem->getQuantity() * $basketItem->getPrice(), 0, '.', ' ') . ' руб.',
                        "QUANTITY" => $basketItem->getQuantity()
                    );
                }
            }
        }
    }

    if (empty($arItems)) {
        return true;
    }

    if (count($arItems)) {

        foreach ($arItems as $item) {
            $color = "";
            if ($item['COLOR']) {
                $color = "Цвет: " . $item['COLOR'];
            }
            $article = "";
            if ($item['ARTICLE']) {
                $article = "Арт.: " . $item['ARTICLE'];
            }
            $material = "";
            if ($item['MATERIAL']) {
                $material = "Материал: " . $item['MATERIAL'];
            }
            $picture = "";
            if ($item['PICTURE']) {
                $picture = '<img width="40px" height="40px" src="//' . SITE_SERVER_NAME . '/' . $item['PICTURE'] . '" alt="#" class="good_ico" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; float: left; margin: 0 5px 0 auto; padding: 0;" align="left">';
            }
            $url = "#";
            if ($item['URL']) {
                //$url = '//'.SITE_SERVER_NAME.'/'.$item['URL'];
                $url = $item['URL'];
            }

            $arFields['ORDER_LIST'] .=
                '<tr style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">
            <td class="top_pad bottom_pad left_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 0 7px 10px;" valign="top">
              <a href="' . $url . '" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">' . $picture . '</a>
              <div style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">
              <a href="' . $url . '" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">' . $item['NAME'] . '</a>
              <p style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">' . $article . '&nbsp;' . $color . '<br style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">' . $material . '</p>
              </div>
              </td>
              <td class="top_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 0 0;" valign="top">' . $item['QUANTITY'] . ' шт.</td>
              <td class="top_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 0 0;" valign="top">' . $item['PRICE'] . '</td>
              <td class="top_pad right_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 7px 0 0;" valign="top">' . $item['SUMM'] . '</td>
      </tr>';
        }
        $arFields['ORDER_LIST'] .= '</tbody></table>';
    }

    //Способ оплаты
    // $paymentIds = $order->getPaymentSystemId(); // массив id способов оплат
    $arPayment = CSalePaySystem::GetByID($arFields['PAY_SYSTEM_ID']);
    $arFields['PAYMENT'] = $arPayment['NAME'];

    \Bitrix\Main\Loader::includeModule('highloadblock');

    $hIB_ID = '25';
    $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getById($hIB_ID)->fetch();
    $entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    $entity_table_name = $hlblock['TABLE_NAME'];

    $arFilter = array("UF_IDD" => intval($arFields['PAY_SYSTEM_ID']));

    $sTableID = 'tbl_' . $entity_table_name;
    $rsData = $entity_data_class::getList(array("select" => array('*'), "filter" => $arFilter));
    $rsData = new CDBResult($rsData, $sTableID);
    while ($arRes = $rsData->Fetch()) {
        $arFields["PAYMENTTEXT"] = $arRes["UF_PAYTEXT"];

    }
    
    $arFields['ADDRESS_DESCRIPTION'] = "";
    if ($arOrder['ORDER_PROP'][24]) {
        
        $hlDPDTerminals = Bitrix\Highloadblock\HighloadBlockTable::getById("24")->fetch();
        $entityDPDTerminals = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlDPDTerminals);
        $entityDataDPDTerminals = $entityDPDTerminals->getDataClass();
        $rawData = array_shift($entityDataDPDTerminals::getList(
            array(
                "select" => array('*'), 
                "filter" => ["UF_TERMINALCODE" => $arOrder['ORDER_PROP'][24]]
            )
        )->fetchAll());
        
        $arFields['ADDRESS_DESCRIPTION'] = unserialize($rawData['UF_DATA_SOURCE'])['address']['descript'];
    }
    
    //Способ доставки
    $deliveryId = reset($order->getDeliverySystemId()); // массив id способов доставки
    $arDelivery = \Bitrix\Sale\Delivery\Services\Manager::getById($deliveryId);
    $arFields['DELIVERY'] = $arDelivery['NAME'] . " (" . $order->getDeliveryPrice() . " " . $order->getCurrency() . ")";

    //Сумма доставки
    $arFields['DELIVERY_PRICE'] = $order->getDeliveryPrice();

    //Комментарий пользователя
    $arFields['USER_DESCRIPTION'] = $order->getField("USER_DESCRIPTION");

    //Скидка
    $arFields['DISCOUNT'] = $order->getDiscountPrice();

    //Все свойства заказа
    $arProperties = $propertyCollection->getArray();
    foreach ($arProperties['properties'] as $property) {
        //$arFields['ORDER_PROPERTIES'] .= $property['CODE']." - ".$property['NAME'].": ".$property['VALUE'][0]."<br>";

        if ($property['CODE'] == 'F_NAME') {
            $arFields['USER_NAME'] = !empty($property['VALUE'][0]) ? $property['VALUE'][0] : 'не указано';
            continue;
        }
        if ($property['CODE'] == 'F_CITY') {
            $arFields['USER_CITY'] = $property['VALUE'][0] . ', ';
            continue;
        }
        if ($property['CODE'] == 'U_CITY') {
            $arFields['USER_CITY'] = $property['VALUE'][0] . ', ';
            continue;
        }
    }

    // $namePropValue  = $propertyCollection->getPayerName();
    $addrPropValue = $propertyCollection->getAddress();

    /*if($namePropValue) {
        $arFields['USER_NAME'] = $namePropValue->getValue();
    } else {
        $arFields['USER_NAME'] = "не указано";
    }*/
    if ($addrPropValue) {
        $arFields['USER_ADDRESS'] = $arFields['USER_CITY'] . $addrPropValue->getValue();
    } else {
        $arFields['USER_ADDRESS'] = "не указан";
    }
    if ($orderPersonID == 1) { // ФЛ
        $email = $arOrder['ORDER_PROP'][3];
        $phone = $arOrder['ORDER_PROP'][4];
    } elseif ($orderPersonID == 2) { // ЮЛ
        $email = $arOrder['ORDER_PROP'][7];
        $phone = $arOrder['ORDER_PROP'][8];
    }
    Event::send(array(
        "EVENT_NAME" => "SALE_NEW_ORDER",
        "LID" => "s1",
        "C_FIELDS" => array(
            "EMAIL" => $email,
            "PHONE" => $phone,
            "SALE_EMAIL" => COption::GetOptionString("sale", "order_email"),
            "ORDER_ID" => $orderId,
            "ORDER_USER" => $arFields['USER_NAME'],
            "ORDER_LIST" => $arFields['ORDER_LIST'],
            "PRICE" => number_format($arFields['PRICE'], 0, '.', ' ') . ' руб.',
            "DELIVERY_PRICE" => number_format($arFields['DELIVERY_PRICE'], 0, '.', ' ') . ' руб.',
            "DISCOUNT" => number_format($arFields['DISCOUNT'], 0, '.', ' ') . ' руб.',
            "PAYMENT" => $arFields['PAYMENT'],
            "PAYMENTTEXT" => $arFields["PAYMENTTEXT"],
            "DELIVERY" => $arFields['DELIVERY'],
            "USER_ADDRESS" => $arFields['USER_ADDRESS'],
            "USER_DESCRIPTION" => $arFields['USER_DESCRIPTION'],
            "CALL" => 'OnOrderSaveHandler',
            "ADDRESS_DESCRIPTION" => $arFields['ADDRESS_DESCRIPTION']
        )
    ));

    // Ставим пометку, что письмо отправлено.
    // Данное свойство заказа также устанавливается в файле order_send_mail.php.
    // Этот способ не подходит, т.к. заново вызывается событие, связанное с сохранением заказа и заново отправляются данные в RetailCRM, происходит дублирование данных.
    // $propUserEmailSend->setValue('Y');
    // $order->save(); // будут заново вызваны события, связанные с изменением заказа

    // Поэтому вручную создаем свойство заказа, т.к. в этот момент оно еще не создано.
    $arOprderPropUserEmailSend = $propUserEmailSend->getProperty();
    if (!$propUserEmailSend->getValueId()) {
        $iPropValueID = CSaleOrderPropsValue::Add(array(
            "ORDER_ID" => $ID,
            "ORDER_PROPS_ID" => $arOprderPropUserEmailSend["ID"],
            "NAME" => $arOprderPropUserEmailSend["NAME"],
            "CODE" => $arOprderPropUserEmailSend["CODE"],
            "VALUE" => "Y"
        ));
    } else {
        CSaleOrderPropsValue::Update(
            $propUserEmailSend->getValueId(),
            array("VALUE" => "Y")
        );
    }

    return true;
}

AddEventHandler("main", "OnBeforeEventAdd", array("LiberCode", "ChangeMail"));

class LiberCode
{

    function ChangeMail(&$event, &$lid, &$arFields)
    {
        if ($event == 'SALE_ORDER_TRACKING_NUMBER') {
            \Bitrix\Main\Loader::includeModule('sale');

            \Bitrix\Main\Loader::includeModule('highloadblock');
            $arOrder = \Bitrix\Sale\Order::getList(array("filter" => array("ID" => $arFields["ORDER_ID"]), "select" => array("DELIVERY_ID")))->fetchAll();
            $hIB_ID = '25';
            $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getById($hIB_ID)->fetch();
            $entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            $entity_table_name = $hlblock['TABLE_NAME'];

            $arFilter = array("UF_IDD" => intval($arOrder[0]["DELIVERY_ID"]));

            $sTableID = 'tbl_' . $entity_table_name;
            $rsData = $entity_data_class::getList(array("select" => array('*'), "filter" => $arFilter));
            $rsData = new CDBResult($rsData, $sTableID);
            while ($arRes = $rsData->Fetch()) {
                $arFields["DELIVERY"] = $arRes["UF_DELIVERYTEXT"];
                $arFields["DELIVERYLINK"] = $arRes["UF_LINK"];
                $replace = (!empty($arFields["ORDER_TRACKING_NUMBER"])) ?  $arFields["ORDER_TRACKING_NUMBER"] : '';
                $arTrackingId = explode(',',$replace);
                $arFields["ORDER_TRACKING_NUMBER"] = trim($arTrackingId[0]);
                $arFields["DELIVERYLINK"] = str_replace('#TRACK_NUMBER', trim($arTrackingId[0]) , $arFields["DELIVERYLINK"]);

            }

            /*switch($arOrder[0]["DELIVERY_ID"]){
                case "7":{
                    $arFields["DELIVERY"]="Вы сможете самостоятельно забрать груз, как только он поступит на выбранный склад самовывоза";
                    $arFields["DELIVERYLINK"]='Отслеживайте доставку заказа  <a href="www.dpd.ru/ols/trace2/standard.do2">на сайте курьерской службы DPD</a>';
                    break;}
                case "8":{
                    $arFields["DELIVERY"]="";
                    $arFields["DELIVERYLINK"]='Отслеживайте доставку заказа  <a href="www.dpd.ru/ols/trace2/standard.do2">на сайте курьерской службы DPD</a>';
                    break;}
                case "10":{
                    $arFields["DELIVERY"]="Вы сможете самостоятельно забрать груз, как только он поступит на выбранный склад самовывоза";
                    $arFields["DELIVERYLINK"]='Отслеживайте доставку заказа <a href="http://www.dellin.ru/tracker/?mode=search&rwID='.$arFields["ORDER_TRACKING_NUMBER"].'"> на сайте транспортной компании Деловые Линии</a>';
                    break;}
                case "12":{
                    $arFields["DELIVERY"]="";
                    $arFields["DELIVERYLINK"]='Отслеживайте доставку заказа <a href="http://www.dellin.ru/tracker/?mode=search&rwID='.$arFields["ORDER_TRACKING_NUMBER"].'"> на сайте транспортной компании Деловые Линии</a>';
                    break;}
            }*/
        } elseif (in_array($event, [
        	"SALE_STATUS_CHANGED_WD",
			"SALE_STATUS_CHANGED_AC",
			"SALE_STATUS_CHANGED_WT",
			"SALE_STATUS_CHANGED_OP"
		])) {
			$order = Sale\Order::load($arFields["ORDER_ID"]);
			$basket = $order->getBasket();

			$arFields["PRICE"] = number_format($basket->getPrice(), 0, '.', ' ') . ' руб.';
			$arFields["DELIVERY_PRICE"] = number_format($order->getDeliveryPrice(), 0, '.', ' ') . ' руб.';
			$arFields["DISCOUNT"] = number_format($order->getDiscountPrice(), 0, '.', ' ') . ' руб.';
			$arFields["TOTAL_PRICE"] = number_format($order->getPrice(), 0, '.', ' ') . ' руб.';

			$arFields['ORDER_LIST'] = '<table bgcolor="ffffff" width="100%" rules="rows" border="1" bordercolor="#e1e1e1" align="left" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0; border: 1px solid #e1e1e1;">
						 <colgroup><col width="252" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">
						 <col width="64" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">
						 <col width="92" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">
						 <col width="90" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;"> </colgroup>
						<tbody><tr bgcolor="e6e6e6" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">
							<td class="top_pad bottom_pad left_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 0 7px 10px;" valign="top">
								 Наименование
							</td>
							<td class="top_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 0 0;" valign="top">
								 Кол-во
							</td>
							<td class="top_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 0 0;" valign="top">
								 Цена
							</td>
							<td class="top_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 0 0;" valign="top">
								 Сумма
							</td>
						</tr>';// формируем новый ORDER_LIST
			$arItems = array(); // массив товаров с нужными данными

			//Перебор всех товаров в заказе
			foreach ($basket as $basketItem) {
				//Получаем свойства товара в корзине
				$property = $basketItem->getPropertyCollection();
				$arProps = $property->getPropertyValues();

				//Выборка элемента (пакет предложений)
				$rsElements = CIBlockElement::GetList(Array("SORT" => "ASC"), Array("ID" => $basketItem->getField('PRODUCT_ID'), "IBLOCK_TYPE" => '1c_catalog'), false, false, Array("ID", "NAME", "PROPERTY_CML2_LINK", "PROPERTY_CML2_ARTICLE"));
				if ($arElement = $rsElements->Fetch()) {
					//Выборка данных из основного каталога
					if ($arElement['PROPERTY_CML2_LINK_VALUE']) {
						$rsElementsCatalog = CIBlockElement::GetList(Array("SORT" => "ASC"), Array("ID" => $arElement['PROPERTY_CML2_LINK_VALUE'], "IBLOCK_TYPE" => '1c_catalog'), false, false, Array("ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PROPERTY_MATERIAL", "PROPERTY_CML2_ARTICLE", "DETAIL_PAGE_URL"));
						if ($arElementCatalog = $rsElementsCatalog->Fetch()) {
							//P($arElementCatalog);
							if ($arElementCatalog['DETAIL_PICTURE']) {
//								$foto2 = CFile::MakeFileArray($arElementCatalog['DETAIL_PICTURE']);
//								$arrFotoNewOrder = CFile::SaveFile($foto2, "new_order");
//								$arPicture = CFile::ResizeImageGet($arrFotoNewOrder, array('width' => 40, 'height' => 44));
                                $arPicture = CFile::ResizeImageGet($arElementCatalog['DETAIL_PICTURE'], Array("width" => 40, "height" => 40));
							} else {
								$arPicture['src'] = false;
							}

							if ($arElementCatalog['DETAIL_PAGE_URL']) {
								$section = CIBlockSection::GetByID($arElementCatalog['IBLOCK_SECTION_ID'])->Fetch();
								$arElementCatalog['DETAIL_PAGE_URL'] = str_replace(Array('#SITE_DIR#', '#SECTION_CODE#', '#ELEMENT_CODE#'), Array('http://' . $GLOBALS['SERVER_NAME'], $section['CODE'], $arElementCatalog['CODE']), $arElementCatalog['DETAIL_PAGE_URL']);
								unset($section);
							}

							$arItems[] = Array(
								"NAME" => $arElement['NAME'], //$arElementCatalog['NAME'],
								"URL" => $arElementCatalog['DETAIL_PAGE_URL'],
								"ARTICLE" => $arElementCatalog['PROPERTY_CML2_ARTICLE_VALUE'],
								"MATERIAL" => $arElementCatalog['PROPERTY_MATERIAL_VALUE'],
								"PICTURE" => $arPicture['src'],
								"PRICE" => number_format($basketItem->getPrice(), 0, '.', ' ') . ' руб.',
								"SUMM" => number_format($basketItem->getQuantity() * $basketItem->getPrice(), 0, '.', ' ') . ' руб.',
								"QUANTITY" => $basketItem->getQuantity()
							);
						}
					}
				}
			}

			if (empty($arItems)) {
				return true;
			}

			if (count($arItems)) {

				foreach ($arItems as $item) {
					$color = "";
					if ($item['COLOR']) {
						$color = "Цвет: " . $item['COLOR'];
					}
					$article = "";
					if ($item['ARTICLE']) {
						$article = "Арт.: " . $item['ARTICLE'];
					}
					$material = "";
					if ($item['MATERIAL']) {
						$material = "Материал: " . $item['MATERIAL'];
					}
					$picture = "";
					if ($item['PICTURE']) {
						$picture = '<img width="40px" height="44px" src="//' . SITE_SERVER_NAME . '/' . $item['PICTURE'] . '" alt="#" class="good_ico" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; float: left; margin: 0 5px 0 auto; padding: 0;" align="left">';
					}
					$url = "#";
					if ($item['URL']) {
						//$url = '//'.SITE_SERVER_NAME.'/'.$item['URL'];
						$url = $item['URL'];
					}

					$arFields['ORDER_LIST'] .=
						'<tr style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">
            <td class="top_pad bottom_pad left_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 0 7px 10px;" valign="top">
              <a href="' . $url . '" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">' . $picture . '</a>
              <div style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">
              <a href="' . $url . '" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">' . $item['NAME'] . '</a>
              <p style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">' . $article . '&nbsp;' . $color . '<br style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">' . $material . '</p>
              </div>
              </td>
              <td class="top_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 0 0;" valign="top">' . $item['QUANTITY'] . ' шт.</td>
              <td class="top_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 0 0;" valign="top">' . $item['PRICE'] . '</td>
              <td class="top_pad right_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 7px 0 0;" valign="top">' . $item['SUMM'] . '</td>
      </tr>';
				}
				$arFields['ORDER_LIST'] .= '</tbody></table>';
			}
		}

    }
}

function OnOrderAddHandler($orderId, $arFields)
{
    foreach ($arFields['BASKET_ITEMS'] as $item) {
        $product = CCatalogSku::GetProductInfo($item['PRODUCT_ID']);
        if ($product['ID']) {
            //$PROPERTY_VALUE += (int)$item['QUANTITY'];
            $arFilter = array('IBLOCK_ID' => \Dsklad\Config::getParam('iblock/catalog') ,'ID' => $product['ID']);
            $arSelect = array('ID', 'NAME', 'PROPERTY_NUM_ORDERED');

            $res = CIBlockElement::getList(false, $arFilter, false, false, $arSelect);
            if ($arElement = $res->Fetch()) {
                $ordered = $arElement['PROPERTY_NUM_ORDERED_VALUE'];
            }

            if ($ordered > 0) {
                $PROPERTY_VALUE = $ordered + (int)$item['QUANTITY'];
            } else {
                $PROPERTY_VALUE = (int)$item['QUANTITY'];
            }
            //Выборка данных из основного каталога
            CIBlockElement::SetPropertyValuesEx($product['ID'], \Dsklad\Config::getParam('iblock/catalog'), array('NUM_ORDERED' => $PROPERTY_VALUE));
        }
    }
    //\Bitrix\Main\Diag\Debug::dumpToFile($mxResult, $varName = '', $fileName = "mylog000.txt");
    /*global $USER;
    AddMessage2Log(
        __FILE__ . PHP_EOL .
        __FUNCTION__ . PHP_EOL .
        'UserId: '. $USER->GetID() . PHP_EOL .
        'orderId: '. $orderId . PHP_EOL .
        'arFields: '. print_r($arFields, true)
    );*/
}

function OnSaleBeforeStatusOrderHandler($orderId, $arFields)
{
    /*global $USER;
    AddMessage2Log(
        __FILE__ . PHP_EOL .
        __FUNCTION__ . PHP_EOL .
        'UserId: '. $USER->GetID() . PHP_EOL .
        'orderId: '. $orderId . PHP_EOL .
        'arFields: '. print_r($arFields, true)
    );*/
}

function OnSaleStatusOrderHandler($orderId, $statusCode)
{
    /*global $USER;
    AddMessage2Log(
        __FILE__ . PHP_EOL .
        __FUNCTION__ . PHP_EOL .
        'UserId: '. $USER->GetID() . PHP_EOL .
        'orderId: '. $orderId . PHP_EOL .
        'arFields: '. print_r($arFields, true)
    );*/
    /*$order = Sale\Order::load($orderId);

    $arFields = array(
        'ORDER_ID' => $orderId,
        // 'ORDER_DATE'
        // 'ORDER_USER'
        // 'ORDER_TRACKING_NUMBER'
        'EMAIL' => 'bitrix21@yandex.ru',
        'BCC' => '',//Option::get("main", "all_bcc"),
        'SALE_EMAIL' => Option::get("sale", "order_email"),
        // 'USER_NAME'
        // 'DEFAULT_EMAIL_FROM'
    );

    switch ($statusCode) {
        case 'PD': // Передан в доставку
            $arFields['ORDER_DATE'] = explode(' ', $order->getDateInsert())[0];

            $shipmentCollection = $order->getShipmentCollection();
            foreach ($shipmentCollection as $shipment) {
                if (!$shipment->isSystem()) { // существует системная отгрузка, т.к. товары не могут быть без отгрузки
                    $arFields['ORDER_TRACKING_NUMBER'] = $shipment->getField('TRACKING_NUMBER');
                }
            }

            if (!empty($arFields['ORDER_TRACKING_NUMBER'])) {
                break;
            }

            $arOrderProps = $order->getPropertyCollection()->getArray();
            foreach ($arOrderProps['properties'] as $arProp) {
                switch ($arProp['CODE']) {
                    case 'F_NAME':
                    case 'U_NAME':
                        $arFields['ORDER_USER'] = $arFields['USER_NAME'] = $arProp['VALUE'][0];
                    break;

                    case 'F_EMAIL':
                    case 'U_EMAIL':
                        $arFields['EMAIL_ORIG'] = $arFields['EMAIL'] = $arProp['VALUE'][0];
                    break;
                }
            }

            AddMessage2Log(
                __FILE__ . PHP_EOL .
                __FUNCTION__ . PHP_EOL .
                'orderId: '. $orderId . PHP_EOL .
                'arFields: '. print_r($arFields, true)
            );

            Event::send(array("EVENT_NAME" => "SALE_ORDER_TRACKING_NUMBER", "LID" => "s1", "C_FIELDS" => $arFields));
        break;
    }*/
}