<?
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include.php';

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;

Loader::includeModule('sale');

$obContext = Context::getCurrent();
$obRequest = $obContext->getRequest();

if ($obRequest->isAjaxRequest() !== true) {
    echo 'error';
    exit;
}

$intProductId = $obRequest->get('id');

if (!is_numeric($intProductId)) {
    echo 'error';
    exit;
}

$obBasket = Basket::loadItemsForFUser(Fuser::getId(), $obContext->getSite());

foreach ($obBasket as $obBasketItem) {
    if($obBasketItem->getField('PRODUCT_ID') == $intProductId ){
        $obBasketItem->delete();
        $obBasket->save();
    }
}

\Dsklad\Order::setWarrantyQuantity();

echo 'ok';