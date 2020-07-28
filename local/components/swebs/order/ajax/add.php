<?php
define('PUBLIC_AJAX_MODE', true);
define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;
use Swebs\Helper;
use Dsklad\Config;


Loader::includeModule('catalog');
Loader::includeModule('sale');
Loader::includeModule('swebs.helper');

$obContext = Context::getCurrent();
$obRequest = $obContext->getRequest();
$bFindItem = false;


if (!$obRequest->isAjaxRequest()) {
    echo 'error';
    exit;
}

$intProductId = intval($obRequest->get('id'));
$intQuantity = intval($obRequest->get('q'));
$iblockServis = false;

$res = CIBlockElement::GetByID($intProductId);
if($ar_res = $res->GetNext()){
    if($ar_res['IBLOCK_ID'] == Config::getParam('iblock/basket_services')) {
        $iblockServis = true;
    }
}

if (empty($intProductId)) {
    echo 'error';
    exit;
}

if (empty($intQuantity) || $iblockServis) {
    $intQuantity = 1;
}

$obBasket = Basket::loadItemsForFUser(Fuser::getId(), $obContext->getSite());

if($iblockServis){
    $priceBasket = $obBasket->getBasePrice() /10;
}

foreach ($obBasket as $obBasketItem) {
    if ($obBasketItem->getField('PRODUCT_ID') == $intProductId ) {
        $obBasketItem->setField('QUANTITY', $intQuantity);
        $bFindItem = true;
    }
}

if (!$bFindItem) {
	if ($item = $obBasket->getExistsItem('catalog', $intProductId)) {
		$item->setField('QUANTITY', $intQuantity);
	}else{
		$item = $obBasket->createItem('catalog', $intProductId);

		if($iblockServis){
		    $item->setFields(array('PRICE'=> $priceBasket, 'CUSTOM_PRICE' => 'Y'));
            $item->setFields(array(
                'QUANTITY' => $intQuantity,
                'CURRENCY' => CurrencyManager::getBaseCurrency(),
                'LID' => Context::getCurrent()->getSite(),
                'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
                'PRODUCT_XML_ID' => Helper\Iblock\Element::getFieldsByID($intProductId, 'XML_ID'),
                'CAN_BUY' => 'Y',
                'DELAY' => 'N',
                'PRICE'=> $priceBasket,
                'CUSTOM_PRICE' => 'Y'
            ));
		}else{
            $item->setFields(array(
                'QUANTITY' => $intQuantity,
                'CURRENCY' => CurrencyManager::getBaseCurrency(),
                'LID' => Context::getCurrent()->getSite(),
                'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
                'PRODUCT_XML_ID' => Helper\Iblock\Element::getFieldsByID($intProductId, 'XML_ID'),
                'CAN_BUY' => 'Y',
                'DELAY' => 'N',
            ));
        }
	}
}

$b = $obBasket->save();

\Dsklad\Order::setWarrantyQuantity();

echo 'ok';