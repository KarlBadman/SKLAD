<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?
use \Bitrix\Main;
use \Bitrix\Main\Loader;
use \Bitrix\Sale;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Mail\Event;
// use \Bitrix\Sale\Delivery;
// use \Bitrix\Sale\ShipmentCollection;

Loader::includeModule('sale');
/*
$ORDER_ID = 3926;

$order = Sale\Order::load($ORDER_ID);

$paymentIds = $order->getPaymentSystemId(); // массив id способов оплат
$deliveryIds = $order->getDeliverySystemId(); // массив id способов доставки
$propertyCollection = $order->getPropertyCollection();
$arPropCollection = $propertyCollection->getArray();

$shipmentCollection = $order->getShipmentCollection();
//$arShipmentCollection = $shipmentCollection->getField("TRACKING_NUMBER");
// $shipment = ShipmentCollection::load($order);

$arDeliveries = array();
foreach ($shipmentCollection as $shipment) {
	if (!$shipment->isSystem()) { // sushhestvuet sistemnaya otgruzka, t.k. tovary` ne mogut by`t` bez otgruzki
		$arDeliveries['SHIPMENTS'][] = array(
			'TRACKING_NUMBER' => $shipment->getField('TRACKING_NUMBER'),
			'DELIVERY_DOC_NUM' => $shipment->getField('DELIVERY_DOC_NUM'),
			'DELIVERY_DOC_DATE' => $shipment->getField('DELIVERY_DOC_DATE')//->format("d.m.Y")
		);
	}
}
*/

$orderId = 3799;
$order = Sale\Order::load($orderId);

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

	// switch ($statusCode) {
		// case 'PD': // Передан в доставку
			$arFields['ORDER_DATE'] = explode(' ', $order->getDateInsert())[0];

			$shipmentCollection = $order->getShipmentCollection();
			foreach ($shipmentCollection as $shipment) {
				if (!$shipment->isSystem()) { // существует системная отгрузка, т.к. товары не могут быть без отгрузки
					$arFields['ORDER_TRACKING_NUMBER'] = $shipment->getField('TRACKING_NUMBER');
				}
			}

			if (!empty($arFields['ORDER_TRACKING_NUMBER'])) {
				//break;
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

			// Event::send(array("EVENT_NAME" => "SALE_ORDER_TRACKING_NUMBER", "LID" => "s1", "C_FIELDS" => $arFields));
		// break;
	// }


?>
<pre style="" class="xxx-result"><?=__FILE__?><br /><?=print_r($arOrderProps, true)?></pre>
<pre style="" class="xxx-result"><?=__FILE__?><br /><?=print_r($arFields, true)?></pre>
<?/*
<pre style="" class="xxx-result"><?=__FILE__?><br /><?=Option::get("main", "all_bcc")?></pre>
<pre style="" class="xxx-result"><?=__FILE__?><br /><?=Option::get("sale", "order_email")?></pre>
<pre style="" class="xxx-result"><?=__FILE__?><br /><?=explode(' ', $order->getDateInsert())[0]?></pre>
<pre style="" class="xxx-result"><?=__FILE__?><br /><?=$order->getPersonTypeId()?></pre>
<pre style="" class="xxx-result"><?=__FILE__?><br /><?=$propertyCollection->getUserEmail()?></pre>
<pre style="" class="xxx-result"><?=__FILE__?><br /><?//=print_r($paymentIds, true)?></pre>
<pre style="" class="xxx-result"><?=__FILE__?><br /><?//=print_r($deliveryIds, true)?></pre>
<pre style="" class="xxx-result"><?=__FILE__?><br /><?=print_r($arPropCollection, true)?></pre>
<pre style="" class="xxx-result"><?=__FILE__?><br /><?//=print_r($arShipmentCollection, true)?></pre>
<pre style="" class="xxx-result"><?=__FILE__?><br /><?=var_dump($arDeliveries)?></pre>
*/?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>