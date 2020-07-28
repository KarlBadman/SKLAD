<?
    namespace AppController;

    use \Pecee\SimpleRouter\SimpleRouter;

    class BaseController {

        const USERNAME = "AntonBot";

        const PASSWD = "ayk32!";

        /**
         * Create new instance
         */
        public function __construct () {
            $this->BaseModel = new BaseModel();
            $this->BaseModel->setRequestEntity(SimpleRouter::request()->getInput()->all());
            // BaseModel::checkauth(); // TODO
        }
        /**
         * Возвращаем данные по номеру заказа
         * $_REQUEST['order_id'] = "12345"
         */
        public function getorderinfo () {
            $order_id = \botTools::sanitizer($this->BaseModel->getRequestEntity('order_id'));
            $orderData = BaseModel::getOrderData($order_id);
            if (!empty($orderData)) {
                $message = 'Заказ ' . $order_id . '. ' . $orderData['payed'] . '. Ваш трек-номер: ' . $orderData['tracking'] . '. Ссылка на отслеживание: https://www.dpd.ru/dpd/search/search.do2?query=' . $orderData['tracking'];
                \botTools::sendMessage(
                    $message, true, array('result' => array('orderData' => $orderData, 'method' => 'getorderinfo'))
                );
            } else {
                \botTools::throwError(1504, 'Данные по заказу не найдены.');
            }
        }

        /**
         * Возвращаем товар по запросу с учетом кол - ва
         * $_REQUEST['item'] = ""
         * $_REQUEST['count'] = "5"
         */
        public function checkavailability () {
            $item = \botTools::sanitizer($this->BaseModel->getRequestEntity('item'));
            $count = (int)\botTools::sanitizer($this->BaseModel->getRequestEntity('count'));
            $result = array('result' => array('item' => $item, 'count' => $count, 'method' => 'checkavailiblity'));
            if (!empty($item)) {
                $arOffer = BaseModel::findProduct($item, $count);
            }

            if (count($arOffer) == 0 || empty($arOffer["ID"])) {
                \botTools::sendMessage('Товар не найден или количестов меньше указанного', false, $result);
            }

            if ($arOffer['AVAILABLE'] > 0 && $arOffer['AVAILABLE'] >= $count) {
                \botTools::sendMessage($arOffer['SEARCHABLE_CONTENT'] . ' в наличии в нужном количестве. ', true, $result); //SEARCHABLE_CONTENT
            } else {
                \botTools::sendMessage('К сожалению нет нужного количества ' . $arOffer['SEARCHABLE_CONTENT'], false, $result);
            }
        }

        /**
         * Возвращаем стоимость доставки для товара с учетом кол - ва и способа доставки
         * $_REQUEST['item'] = ""
         * $_REQUEST['cout'] = "5"
         * $_REQUEST['city'] = "Санкт-Петербург"
         * $_REQUEST['door'] = "Y\N"
         */
        public function checkcoast () {
            $item = \botTools::sanitizer($this->BaseModel->getRequestEntity('item'));
            $count = (int)\botTools::sanitizer($this->BaseModel->getRequestEntity('count'));
            $city = \botTools::sanitizer($this->BaseModel->getRequestEntity('city'));
            $door = \botTools::sanitizer($this->BaseModel->getRequestEntity('door'));
            $bSelfDelivery = ($door == 'y') ? true : false;//доставка или самовывоз
            $deliveryData = $this->BaseModel->getCoastByCity($city, $item, $count);
            $obOrderHelp = new \COrderBasket;
            $obOrderHelp->setSourceTerminals('HL');
            $deliveryPrice = $obOrderHelp->getDPDCoast($deliveryData['ARRAY_OFFER'], $bSelfDelivery, $deliveryData['LOCATION_ID']);

            // Костыль СПб/Мск
            if (in_array($deliveryData['LOCATION_ID'], array(77000000000, 78000000000))) {
                if ($deliveryData['LOCATION_ID'] == '78000000000') {
                    ($bSelfDelivery) ? $deliveryPrice = DELIVERY_COAST : $deliveryPrice = PICKUP_PVZ_COAST_SPB;
                }
                if ($deliveryData['LOCATION_ID'] == '77000000000') {
                    ($bSelfDelivery) ? $deliveryPrice = DELIVERY_COAST : $deliveryPrice = PICKUP_PVZ_COAST_MSK;
                }
            }
            
            // На случай доставки в город, где нет терминалов ДПД, вернем стоимость курьерской доставки
            if (!$deliveryPrice || $deliveryPrice == 0){
                $deliveryPrice = $obOrderHelp->getDPDCoast($deliveryData['ARRAY_OFFER'], !$bSelfDelivery, $deliveryData['LOCATION_ID']);
            }

            $deliveryPrice = number_format($deliveryPrice, 0, '', ' ') . ' руб';
            
            \botTools::sendMessage(
                'Доставка ' . $deliveryData['ARRAY_OFFER']['NAME'] . ' в количестве ' . $deliveryData['ARRAY_OFFER']['QUANTITY'] . ' до ' . $deliveryData['CITY_INFO']['UF_CITYNAME'] . ' Будет стоить примерно: ' . $deliveryPrice, true,
                array('result' => array('method' => 'checkcoast'))
            );
        }

        /**
         * Добавление товара в корзину с указанием кол - ва
         * $_REQUEST['items'] = ""
         * $_REQUEST['count'] = "5"
         */
        public function add2basket () {
            $item = \botTools::sanitizer($this->BaseModel->getRequestEntity('item'));
            $count = (int)\botTools::sanitizer($this->BaseModel->getRequestEntity('count'));
            $user_id = \botTools::sanitizer($this->BaseModel->getRequestEntity('user_id'));
            $arOffer = BaseModel::findProduct($item, $count);
            if (!empty($arOffer)) {
                if ($this->BaseModel->add2BasketByUserID($user_id, $arOffer, $count))
                    \botTools::sendMessage(
                        "Товар " . $arOffer['NAME'] . " успешно добавлен, в кол-ве " . $count . "шт.", true,
                        array('result' => array('method' => 'add2basket'))
                    );
                else
                    \botTools::throwError(1512, "Произошла ошибка при добавлении, повторите запрос позже");die();
            } else {
                \botTools::throwError(1505, "К сожалению товар не найден");die();
            }
        }

        /**
         * Удаление всех товаров из корзины по USER_ID
         * $_REQUEST['user_id']
         */
        public function clearbasket () {
            $user_id = \botTools::sanitizer($this->BaseModel->getRequestEntity('user_id'));
            if (!empty($user_id)) {
                if ($this->BaseModel->clearUserBasketByUserID($user_id))
                    \botTools::sendMessage('Корзина очищена', true,
                        array('result' => array('method' => 'clearbasket'))
                    );
                else
                    \botTools::throwError(1516, "При очистке корзины возникла ошибка, мы уже исправляем ее");die();
            } else {
                \botTools::throwError(1518, "Не найдена корзина пользователя");die();
            }
        }

        /**
         * Получить текущую корзину пользователя
         * $_REQUEST['user_id']
        */
        public function getuserbasket () {
            $user_id = \botTools::sanitizer($this->BaseModel->getRequestEntity('user_id'));
            $basketItemsListString = "";
            if (!empty($user_id)) {
                $userBasketItems = $this->BaseModel->getUserBasketItems($user_id);
                if (count ($userBasketItems) > 0) {
                    $basketItemsListString = array_map(function (array $item) {
                        // var_dump($item);die();
                        return " ⇒⇒⇒⇒⇒ " . $item['NAME'] . " кол-во: " . $item['QUANTITY'] . " по цене: " . $item['PRICE'] . " за шт.";
                    }, $userBasketItems);
                    $basketItemsListString = implode(' ', $basketItemsListString);
                } else {
                    $basketItemsListString = " не найдены";
                }

                \botTools::sendMessage(
                    "Товары в вашей корзине " . $basketItemsListString, true,
                    array('result' => array('basketItems' => $basketItemsListString, 'method' => 'getuserbasket'))
                );
            } else {
                \botTools::throwError(1506, "Не найдена корзина пользователя");die();
            }
        }

    }
?>
