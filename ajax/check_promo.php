<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Sale\DiscountCouponsManager,
    Bitrix\Main\Context;

$request = Context::getCurrent()->getRequest();
$promoCode = trim($request->get('promo'));

if (!empty($promoCode)) {
    $coupon = DiscountCouponsManager::getData($promoCode);
    if (
        $USER->IsAuthorized()
        && ($coupon['DISCOUNT_ID'] == \Dsklad\Config::getParam('promocode_4_views/discount_id'))
    ) {
        if (\Dsklad\PromoCodeFor4Views::isUsedPromoCode($USER->GetID())) {
            echo '0';
            die();
        }
    }

    if (DiscountCouponsManager::add($promoCode)) {
        echo '1';
    } else {
        echo '0';
    }
} else {
    echo '0';
}

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
