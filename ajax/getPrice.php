<?require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';

	use Bitrix\Main\Context,
			Bitrix\Main\Web\Json,
			Bitrix\Main\Loader;

	$obRequest = Context::getCurrent()->getRequest();
	if(!$obRequest->isAjaxRequest() || !$obRequest->isPost()) die(Json::encode(array("ERROR"=>'Request')));

	$intItemID = intval($obRequest->get('id'));
	$intQuantity = intval($obRequest->get('qty'));
	if(!$intItemID) die(Json::encode(array("error"=>'ItemID')));
	if(!$intQuantity) $intQuantity = 1;

	//$intCountProductInBasket = GetCountProductInBasket($intItemID);
	//$intQuantity += (int)$intCountProductInBasket;

	// Получим цену с учетом всех скидок зависящих от количества однотипных товаров в корзине
	$arOptimalPrice = CCatalogProduct::GetOptimalPrice($intItemID, $intQuantity);

	if($arOptimalPrice && !empty($arOptimalPrice))
	{
		$arResult = array(
			'id'				=> $intItemID,
			'qty'				=> $intQuantity,
			'min_price'	=> $arOptimalPrice['DISCOUNT_PRICE'],
			'max_price'	=> $arOptimalPrice['MAX_PRICE'],
			'error'			=> false
		);
		die(Json::encode($arResult));
	}else die(Json::encode(array("error"=>'Result')));
