<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;
use Bitrix\Main,
    Bitrix\Main\Loader,
    Bitrix\Main\Config\Option,
    Bitrix\Sale,
    Bitrix\Sale\Order,
    Bitrix\Main\Application,
    Bitrix\Sale\DiscountCouponsManager;

if (!Loader::IncludeModule('sale'))
    die();

$request = Application::getInstance()->getContext()->getRequest();

$prod_id = $request->getPost("prod_id")

$fio = trim($request->getPost("name"));

$phone = $request->getPost("phone");

$phone = preg_replace("/([^0-9])/", "", $phone);

$comment = $request->getPost("comment");

$email = '';

$quantity = (!($quantity = $request->getPost("quantity")) > 0) ? 1 : $quantity;

$registeredUserID = 0;

function getPropertyByCode($propertyCollection, $code)  {
    foreach ($propertyCollection as $property)
    {
        if($property->getField('CODE') == $code)
            return $property;
    }
}

    global $USER, $APPLICATION;

    $siteId = \Bitrix\Main\Context::getCurrent()->getSite();

    $currencyCode = Option::get('sale', 'default_currency', 'RUB');

    DiscountCouponsManager::init();

    $registeredUserID = $USER->GetID();

    $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());

    if ($item = $basket->getExistsItem('catalog', $prod_id)) {
        $item->setField('QUANTITY', $item->getQuantity() + $quantity);
    } else {
        $item = $basket->createItem('catalog', $prod_id);
        $item->setFields(array(
            'QUANTITY' => $quantity,
            'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
            'LID' => \Bitrix\Main\Context::getCurrent()->getSite(),
            'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
        ));
    }
    $basket->save();

    $order = Order::create($siteId, $registeredUserID);
    $order->setPersonTypeId(1);
    $basket = Sale\Basket::loadItemsForFUser(\CSaleBasket::GetBasketUserID(), Bitrix\Main\Context::getCurrent()->getSite())->getOrderableItems();

    $order->setBasket($basket);

    /*Shipment*/
    $shipmentCollection = $order->getShipmentCollection();
    $shipment = $shipmentCollection->createItem();
    $shipment->setFields(array(
        'DELIVERY_ID' => 6,
        'DELIVERY_NAME' => 'Самовывоз',
        'CURRENCY' => $order->getCurrency()
    ));


    $shipmentItemCollection = $shipment->getShipmentItemCollection();

    foreach ($order->getBasket() as $item)
    {
        $shipmentItem = $shipmentItemCollection->createItem($item);
        $shipmentItem->setQuantity($item->getQuantity());
    }


    /*Payment*/
    $paymentCollection = $order->getPaymentCollection();
    $extPayment = $paymentCollection->createItem();
    $extPayment->setFields(array(
        'PAY_SYSTEM_ID' => 2,
        'PAY_SYSTEM_NAME' => 'Наличные',
        'SUM' => $order->getPrice()
    ));

    /**/
    $order->doFinalAction(true);
    $propertyCollection = $order->getPropertyCollection();

    $phoneProperty = getPropertyByCode($propertyCollection, 'PHONE');
    $phoneProperty->setValue($phone);

    $fioProperty = getPropertyByCode($propertyCollection, 'FIO');
    $fioProperty->setValue($fio);

    $order->setField('CURRENCY', $currencyCode);
    $order->setField('COMMENTS', 'Заказ оформлен через АПИ. ' . $comment);
    $order->save();
    $orderId = $order->GetId();

    if($orderId > 0){
        $result = array('success' => ['text' => 'Ваш заказ оформлен']);
    }
    else{
        $result = array('error' => ['text' => 'Ошибка оформления']);
    }

echo \Bitrix\Main\Web\Json::encode($result);
?>