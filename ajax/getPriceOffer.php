<?require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';

	use Bitrix\Main\Context,
		Bitrix\Main\Web\Json,
		Bitrix\Main\Loader,
		Bitrix\Sale\Basket,
		Bitrix\Sale\Fuser;

	Loader::includeModule("catalog");

	$obContext = Context::getCurrent();
	$obRequest = $obContext->getRequest();
	if(!$obRequest->isAjaxRequest() || !$obRequest->isPost()) die(Json::encode(array("ERROR"=>'Request')));

	$intItemID = intval($obRequest->get('id'));
	$intQuantity = intval($obRequest->get('qty'));
	if(!$intItemID) die(Json::encode(array("error"=>'ItemID')));
	if(!$intQuantity) $intQuantity = 1;

	/*$obBasket = Basket::loadItemsForFUser(Fuser::getId(), $obContext->getSite());
	$obItem = $obBasket->getItemById($intItemID);
	$obItem->setField('QUANTITY', $intQuantity);
	$obBasket->save();

	$obItem = $obBasket->getItemById($intItemID);
	$intQty = $obItem->getQuantity();*/
	// Получим цену с учетом всех скидок зависящих от количества однотипных товаров в корзине
	$arOptimalPrice = CCatalogProduct::GetOptimalPrice($intItemID, $intQuantity);

	if($arOptimalPrice && !empty($arOptimalPrice))
	{
		$arResult = array(
			'id'        => $intItemID,
			'qty'       => $intQuantity,
			'error'     => false,
			'min_price' => $arOptimalPrice['DISCOUNT_PRICE'],
			'max_price' => $arOptimalPrice['MAX_PRICE'],
		);
		die(Json::encode($arResult));
	}else die(Json::encode(array("error"=>'Result')));
