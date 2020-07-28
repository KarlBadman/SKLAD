<?
use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Sale;

Loader::includeModule('sale');

Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnOrderNewSendEmail',
    'OnOrderNewSendEmailHandler'
);

//Функция меняет информацию о товарах в письме о заказе - по сути не должен вызываться. т.к. есть обработчик в order.php который уже выслал письмо
function OnOrderNewSendEmailHandler($ID, &$eventName, &$arFields) {
      $order = Sale\Order::load($ID);

      //Получить корзину заказа
      $basket = $order->getBasket();
      $basketItems = $basket->getBasketItems(); // массив объектов Sale\BasketItem
         //P($basketItems);

      Loader::includeModule('iblock');

      //Формируем новый ORDER_LIST
     $arFields['ORDER_LIST'] .='<table bgcolor="ffffff" width="100%" rules="rows" border="1" bordercolor="#e1e1e1" align="left" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0; border: 1px solid #e1e1e1;">
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
						</tr>';

      $arItems = Array(); //массив товаров с нужными данными

      //Перебор всех товаров в заказе
      foreach ($basket as $basketItem) {
             //Получаем свойства товара в корзине
             $property = $basketItem->getPropertyCollection();
             $arProps = $property->getPropertyValues();

             //echo $basketItem->getField('NAME')." ".$basketItem->getField('PRODUCT_ID');
             
             //Выборка элемента (пакет предложений)
             $rsElements = CIBlockElement::GetList(Array("SORT" => "ASC"), Array("ID" => $basketItem->getField('PRODUCT_ID'), "IBLOCK_TYPE" => '1c_catalog'), false, false, Array("ID","NAME","PROPERTY_CML2_LINK", "PROPERTY_CML2_ARTICLE"));
             if($arElement = $rsElements->Fetch()) {
                //P($arElement);
                //Выборка данных из основного каталога
                if($arElement['PROPERTY_CML2_LINK_VALUE']) {
                    $rsElementsCatalog = CIBlockElement::GetList(Array("SORT" => "ASC"), Array("ID" => $arElement['PROPERTY_CML2_LINK_VALUE'], "IBLOCK_TYPE" => '1c_catalog'), false, false, Array("ID","NAME", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PROPERTY_MATERIAL", "PROPERTY_CML2_ARTICLE", "DETAIL_PAGE_URL"));
                    if($arElementCatalog = $rsElementsCatalog->Fetch()) {
                        //P($arElementCatalog);
                        if($arElementCatalog['DETAIL_PICTURE']) {
                            $foto2 = CFile::MakeFileArray($arElementCatalog['DETAIL_PICTURE']);
                            $arrFotoNewOrder = CFile::SaveFile($foto2, "new_order");
                          $arPicture = CFile::ResizeImageGet($arrFotoNewOrder, array('width' => 40, 'height' => 44));
                        } else {
                          $arPicture['src'] = false;
                        }

                        if($arElementCatalog['DETAIL_PAGE_URL']) {
                          $section = CIBlockSection::GetByID($arElementCatalog['IBLOCK_SECTION_ID'])->Fetch();
                          $arElementCatalog['DETAIL_PAGE_URL'] = str_replace(Array('#SITE_DIR#', '#SECTION_CODE#', '#ELEMENT_CODE#'), Array('http://'.$GLOBALS['SERVER_NAME'], $section['CODE'], $arElementCatalog['CODE']), $arElementCatalog['DETAIL_PAGE_URL']);
                          unset($section);
                        }

                        $arItems[] = Array(
                           "NAME" => $arElement['NAME'], //$arElementCatalog['NAME'],
                           "URL" => $arElementCatalog['DETAIL_PAGE_URL'],
                           "ARTICLE" => $arElementCatalog['PROPERTY_CML2_ARTICLE_VALUE'],                     
                           "MATERIAL" => $arElementCatalog['PROPERTY_MATERIAL_VALUE'],
                           "PICTURE" => $arPicture['src'],
                           "PRICE" => number_format($basketItem->getPrice(), 0, '.', ' '). ' руб.',
                           "SUMM" => number_format($basketItem->getQuantity()*$basketItem->getPrice(), 0, '.', ' '). ' руб.',
                           "QUANTITY" => $basketItem->getQuantity()
                        );
                        //P($arItems);
                    }
                }
             }
      }

      if(count($arItems)) {
		  
        foreach ($arItems as $item) {
           $color = "";
           if($item['COLOR']) {
             $color = "Цвет: ".$item['COLOR'];
           }
           $article = "";
           if($item['ARTICLE']) {
             $article = "Арт.: ".$item['ARTICLE'];
           }
           $material = "";
           if($item['MATERIAL']) {
             $material = "Материал: ".$item['MATERIAL'];
           }
           $picture = "";
           if($item['PICTURE']) {
             $picture = '<img width="40px" height="44px" src="http://'.$GLOBALS['SERVER_NAME'].'/'.$item['PICTURE'].'" alt="#" class="good_ico" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; float: left; margin: 0 5px 0 auto; padding: 0;" align="left">';
           }

           $url = "#";
           if($item['URL']) {
            $url = $item['URL'];
           }


           $arFields['ORDER_LIST'] .= '						
						<tr style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">
            <td class="top_pad bottom_pad left_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 0 7px 10px;" valign="top">
              <a href="'.$url.'" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">'.$picture.'</a>
              <div style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">
              <a href="'.$url.'" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">'.$item['NAME'].'</a>
              <p style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">'.$article.'&nbsp;'.$color.'<br style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 0;">'.$material.'</p>
              </div>
              </td>
              <td class="top_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 0 0;" valign="top">'.$item['QUANTITY'].' шт.</td>
              <td class="top_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 0 0;" valign="top">'.$item['PRICE'].'</td>
              <td class="top_pad right_pad" style="vertical-align: top; line-height: 1.6; font-family: Trebuchet MS, Helvetica, sans-serif; margin: 0 auto; padding: 7px 7px 0 0;" valign="top">'.$item['SUMM'].'</td>
      </tr>';
        }
		$arFields['ORDER_LIST'] .='</tbody></table>';
      } else {
		  return false;
	  }


      //Способ оплаты
      $paymentIds = $order->getPaymentSystemId(); // массив id способов оплат
      $arPayment = CSalePaySystem::GetByID($paymentIds[0]);
      $arFields['PAYMENT'] = $arPayment['NAME'];
	  
	
if($arPayment["ID"]==2){
$arFields["PAYMENTTEXT"]='Оплатить заказ Банковской картой можно <a href="https://www.dsklad.ru/personal/">в личном кабинете </a> ';
			}elseif($arPayment["ID"]==4){
$arFields["PAYMENTTEXT"]="Счет будет отправлен на Вашу электронную почту в ближайшее время.<br>Если Вы не указали реквизиты при оформлении заказа просим их отправить ответом на данное письмо.";
				
			}
      //Способ доставки
      $deliveryIds = $order->getDeliverySystemId(); // массив id способов доставки
      $arDelivery = CSaleDelivery::GetByID($deliveryIds[0]);
      $arFields['DELIVERY'] = $arDelivery['NAME']." (".$order->getDeliveryPrice()." ".$order->getCurrency().")";    

      //Сумма доставки
      $arFields['DELIVERY_PRICE'] = $order->getDeliveryPrice();

      //Комментарий пользователя
      $arFields['USER_DESCRIPTION'] = $order->getField("USER_DESCRIPTION"); 

      //Скидка
      $arFields['DISCOUNT'] = $order->getDiscountPrice();

      //Все свойства заказа
      $propertyCollection = $order->getPropertyCollection();
      $arProperties = $propertyCollection->getArray();
      foreach ($arProperties['properties'] as $property) {
        //$arFields['ORDER_PROPERTIES'] .= $property['CODE']." - ".$property['NAME'].": ".$property['VALUE'][0]."<br>";

        if($property['CODE'] == 'F_CITY') {
          $arFields['USER_CITY'] = $property['VALUE'][0].', ';
        } 
        if($property['CODE'] == 'U_CITY') {
          $arFields['USER_CITY'] = $property['VALUE'][0].', ';
        }   
      }

      $namePropValue  = $propertyCollection->getPayerName();
      $addrPropValue  = $propertyCollection->getAddress();

      if($namePropValue) {
        $arFields['USER_NAME'] = $namePropValue->getValue();
      } else {
        $arFields['USER_NAME'] = "не указано";
      }
      if($addrPropValue) {
        $arFields['USER_ADDRESS'] = $arFields['USER_CITY'].$addrPropValue->getValue();
      } else {
        $arFields['USER_ADDRESS'] = "не указан";
      }

	// Ставим пометку, что письмо отправлено.
	$orderPersonID = $order->getPersonTypeId();
	if ($orderPersonID == 1) { // ФЛ
		$propUserEmailSend = $propertyCollection->getItemByOrderPropertyId(15); // 15 - USER_EMAIL_SEND
	} elseif ($orderPersonID == 2) { // ЮЛ
		$propUserEmailSend = $propertyCollection->getItemByOrderPropertyId(16); // 16 - USER_EMAIL_SEND
	}

	// Ставим пометку, что письмо отправлено.
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
	}

    return true;
}//OnOrderNewSendEmailHandler



Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnOrderStatusSendEmail',
    'OnOrderStatusSendEmailHandler'
);

function OnOrderStatusSendEmailHandler($ID, &$eventName, &$arFields, $val) {
  $order = Sale\Order::load($ID);

  $propertyCollection = $order->getPropertyCollection();
  $arProperties = $propertyCollection->getArray();
  foreach ($arProperties['properties'] as $property) {
    //$arFields['ORDER_PROPERTIES'] .= $property['CODE']." - ".$property['NAME'].": ".$property['VALUE'][0]."<br>";

    if($property['CODE'] == 'F_NAME') {
      $arFields['USER_NAME_1'] = $property['VALUE'][0].', ';
    }
    if($property['CODE'] == 'U_NAME') {
      $arFields['USER_NAME_1'] = $property['VALUE'][0].', ';
    }  
  }

  $namePropValue  = $propertyCollection->getPayerName();
  $addrPropValue  = $propertyCollection->getAddress();

  if($namePropValue) {
    $arFields['USER_NAME'] = $namePropValue->getValue();
  } elseif($arFields['USER_NAME_1']) {
    $arFields['USER_NAME'] = $arFields['USER_NAME_1'];
  }

  $arFields['USER_NAME'] = "Уважаемый(-ая) ".str_replace(", ", "", $arFields['USER_NAME'])."! ";
  

  return true;
}//OnOrderStatusSendEmailHandler
