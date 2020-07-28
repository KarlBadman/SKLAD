<?
use \Bitrix\Main\Loader;
use \Bitrix\Main\Context;
use \Bitrix\Main\Web;
use \Bitrix\Sale\Basket;
use \Bitrix\Sale\Fuser;

define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
define('NO_AGENT_STATISTIC', true);
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', true);

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

try {
    Loader::includeModule('sale');

    $context = Context::getCurrent();
    $request = $context->getRequest();
    $basketId = (int)filter_var($request->get('ID'), FILTER_SANITIZE_NUMBER_INT);

    if (empty($basketId)) {
        throw new \Exception('Не задан ID записи корзины');
    }

    $obBasket = Basket::loadItemsForFUser(Fuser::getId(), $context->getSite());

    $isFound = false;
    foreach ($obBasket as $obBasketItem) {
        if ($obBasketItem->getId() == $basketId ){
            $isFound = true;
            $res = $obBasketItem->delete();
            if ($res->isSuccess()) {
                $obBasket->save();
            } else {
                throw new \Exception('Не удалось удалить запись корзины');
            }
        }
    }

    if (!$isFound) {
        throw new \Exception('Не найдена запись корзины с таким ID');
    }

    $result = [
        'status' => 'ok'
    ];

    echo Web\Json::encode($result);
} catch (\Exception $e) {
    $result = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];

    echo Web\Json::encode($result);
}