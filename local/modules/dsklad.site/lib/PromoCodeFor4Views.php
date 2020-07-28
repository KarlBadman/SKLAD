<?
namespace Dsklad;

use \Bitrix\Main\Context;
use \Bitrix\Main\Web\Cookie;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\UserGroupTable;

/**
 * Класс для акции "Промокод на скидку 5% за 4 перехода на сайт"
 *
 * Class PromoCodeFor4Views
 * @package Dsklad
 */
class PromoCodeFor4Views
{
    public static $needShow = false;
    private static $counter = 0;

    /**
     * Уже показывали попап этому посетителю
     * @return bool
     * @throws \Exception
     */
    private static function isAlreadyShowed()
    {
        $request = Context::getCurrent()->getRequest();

        if ($request->getCookie(Config::getParam('promocode_4_views/cookie_showed')) == 'Y') {
            self::$needShow = false;
            return true;
        }

        global $USER;
        if ($USER->IsAuthorized()) {
            $userData = \CUser::GetByID($USER->GetID())->Fetch();
            if ($userData[Config::getParam('promocode_4_views/user_showed_code')] == 1) {
                self::$needShow = false;
                return true;
            }
        }

        return false;
    }

    /**
     * Этот пользователь уже использовал промокод
     * @param $userId
     * @param string $userEmail
     * @param string $userPhone
     * @return bool
     * @throws \Exception
     */
    public static function isUsedPromoCode($userId, $userEmail = '', $userPhone = '')
    {
        $userId = (int)$userId;

        if (empty($userId) && !empty($userEmail) && !empty($userPhone)) {
            $user = UserTable::getRow([
                'filter' => [
                    'LOGIC' => 'OR',
                    '=EMAIL' => $userEmail,
                    '=PERSONAL_PHONE' => $userPhone
                ],
                'select' => [
                    'ID'
                ]
            ]);

            $userId = $user['ID'];
        }

        return !self::isUserInDiscountGroup($userId);
    }

    /**
     * Проверяет, что пользователь входит в группу для которой разрешено применение купона
     * @param $userId
     * @return bool
     * @throws \Exception
     */
    private static function isUserInDiscountGroup($userId)
    {
        $db = \CUser::GetUserGroupEx($userId);

        while ($ar = $db->Fetch()) {
            if ($ar['GROUP_ID'] == Config::getParam('promocode_4_views/user_group_id')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Адрес страницы не изменился, т.е. обновили по F5
     * @return bool
     */
    public static function isSamePage()
    {
        $request = Context::getCurrent()->getRequest();

        $requestSignature = $request->getRequestUri();

        if ($_SESSION['LAST_REQUEST'] == $requestSignature) {
            return true;
        }

        $_SESSION['LAST_REQUEST'] = $requestSignature;

        return false;
    }

    /**
     * Вычисление и сохранение значения счетчика переходов
     * @throws \Exception
     */
    private static function setCounterValue()
    {
        $request = Context::getCurrent()->getRequest();
        $server = Context::getCurrent()->getServer();

        self::$counter = (int)$request->getCookie(Config::getParam('promocode_4_views/cookie_counter'));

        $referer = $server->get('HTTP_REFERER');

        if (
            !self::isSamePage()
            && (
                empty($referer)
                || (parse_url($referer, PHP_URL_HOST) != $server->getHttpHost())
            )
        ) {
            self::$counter++;

            $cookie = new Cookie(Config::getParam('promocode_4_views/cookie_counter'), self::$counter);
            Context::getCurrent()->getResponse()->addCookie($cookie);
        }
    }

    /**
     * Установка значения флага, указыввающего на необходимость отображения попапа
     * Также установка значений в куках и свойствах пользователя
     * @throws \Exception
     */
    private static function setNeedShow()
    {
        self::$needShow = true;

        $cookie = new Cookie(Config::getParam('promocode_4_views/cookie_counter'), 0, time() - 3600);
        Context::getCurrent()->getResponse()->addCookie($cookie);

        $cookie = new Cookie(Config::getParam('promocode_4_views/cookie_showed'), 'Y');
        Context::getCurrent()->getResponse()->addCookie($cookie);

        global $USER;
        if ($USER->IsAuthorized()) {
            $USER->Update(
                $USER->GetID(),
                [
                    Config::getParam('promocode_4_views/user_showed_code') => 1
                ]
            );
        }
    }

    /**
     * Инициализация параметров акции
     * @throws \Exception
     */
    public static function init()
    {
        $request = Context::getCurrent()->getRequest();

        global $USER;
        if (
            $request->isPost()
            || self::isAlreadyShowed()
            || ($USER->IsAuthorized() && self::isUsedPromoCode($USER->GetID()))
        ) {
            return;
        }

        self::setCounterValue();

        if (self::$counter == 4) {
            self::setNeedShow();
        }
    }

    /**
     * Удаляем пользователя из группы для акции, если он воспользовался купоном
     * Нужно, чтобы пользователь мог использовать купон только один раз
     * @param \Bitrix\Main\Event $event
     * @throws \Exception
     */
    public static function OnSaleOrderEntitySaved(\Bitrix\Main\Event $event)
    {
        $order = $event->getParameter('ENTITY');

        $userId = $order->getUserId();
        $discountData = $order->getDiscount()->getApplyResult();

        if (!empty($discountData['DISCOUNT_LIST'][Config::getParam('promocode_4_views/discount_id')])) {
            UserGroupTable::delete([
                'GROUP_ID' => Config::getParam('promocode_4_views/user_group_id'),
                'USER_ID' => $userId
            ]);
        }
    }
}
