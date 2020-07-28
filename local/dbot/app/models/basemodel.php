<?
    namespace AppController;

    use Bitrix\Main\Application;
    use Bitrix\Main\Loader;
    use Bitrix\Highloadblock\HighloadBlockTable;
    use Bitrix\Main\Entity;

    class BaseModel {

        private $request;

        /**
         * Проверяем авторизацию клиента
         * @return mixed
         */
        public function checkauth () {
            if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == self::USERNAME && $_SERVER['PHP_AUTH_PW'] == self::PASSWD) {
                return true;
            }
            \botTools::throwError(1404);
        }

        /**
         * Set request in base model
         */
         public function setRequestEntity (array $request = []) {
             return ($this->request = $request ? : []);
         }

         /**
          * Получаем текущий $_REQUEST по ключу или весь
          * @param string $key
          * @return mixed
          */
         public function getRequestEntity (string $key = "") {
             return !empty($key) ? $this->request[$key] : $this->request;
         }

        /**
         * Получаем данные заказа
         * @param string $order_Id
         * @return array
         */
        public function getOrderData ($orderID = "") {
            $orderData = array(); if (empty($orderID)) return false;
            $arOrder = \CSaleOrder::GetById($orderID);
            if (!empty($arOrder)) {
                $orderData = array(
                    'order_id' => $orderID,
                    'payed' => ($arOrder['PAYED'] == 'Y') ? 'Оплачен' : 'Ожидает оплаты',
                    'tracking' => $arOrder['TRACKING_NUMBER'],
                    'status' => $arOrder['STATUS_ID'],
                );
            }
            return $orderData;
        }

        /**
         * Получаем стоимость доставки по выбранному городу
         * @param string $city
         * @return mixed
         */
        public function getCoastByCity (string $city = "", $item = "", $count = 1) {
            $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/dpd_cities'))->fetch();;//Собираем информацию по городу
            $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
            $strEntityDataClass = $obEntity->getDataClass();
            $rsData = $strEntityDataClass::getList(array(
                'order' => array('UF_SORT' => 'ASC', 'UF_CITYNAME' => 'ASC'),
                'select' => array('UF_CITYNAME', 'UF_CITYCODE', 'ID'),
                'filter' => array(
                    'UF_CITYNAME' => $city . '%'
                )
            ));
            $cityInfo = $rsData->fetch();
            $intLocationID = (!empty($cityInfo['UF_CITYCODE'])) ? $cityInfo['UF_CITYCODE'] : 0;
            if (empty($intLocationID)) {
                \botTools::sendMessage('По заданным условиям невозможно расчитать доставку. Попробуйте уточнить название города доставки.', false, array('result' => array('method' => '')));
            }
            $arOffer = self::findProduct($item, $count);
            if (count($arOffer) == 0) {
                \botTools::sendMessage('По заданным условиям невозможно расчитать доставку. Попробуйте уточнить имя товара.', false, false, array('result' => array('method' => '')));
            }

            return array('LOCATION_ID' => $intLocationID, 'ARRAY_OFFER' => $arOffer, 'CITY_INFO' => $cityInfo);
        }

        /**
         * Поиск товара по имени и выбор товарного предложения, если это возможно
         * @param $item
         * @param int $count
         * @param string $offerParam
         * @return array
         */
        public function findProduct($item, $count = 1, $offerParam = '') {
            //Собираем информацию о товаре
            $db_res = \CIBlockElement::GetList(
                array("SORT" => "DESC"),
                array(
                    "ACTIVE" => "Y",
                    array(
                        "LOGIC" => "OR",
                        "NAME" => $item,
                        "PROPERTY_CML2_ARTICLE" => $item,
                    ),
                ),
                false,
                array("nTopCount" => 1),
                array('ID', 'XML_ID', 'NAME', 'PROPERTY_RELATED', 'PROPERTY_WITH_THIS', 'PROPERTY_CML2_ARTICLE', 'XML_ID')
            );
            $arElement = $db_res->Fetch();
            //Собираем информацию о товарном предложении (для габаритов)
            $arProductOffers = \CCatalogSKU::getOffersList($arElement['ID'], 0, array('ACTIVE' => 'Y', 'NAME' => '%' . $offerParam . '%'), array('NAME', 'CATALOG_QUANTITY'));
            // $arProductOffers = CCatalogSKU::getOffersList($arElement['ID']);
            if ($arProductOffers) {
                $arOffer = \CIBlockElement::GetByID(current($arProductOffers[$arElement['ID']])['ID'])->Fetch();
                $arOffer['PARENT'] = $arElement['ID'];
                $arOffer['PACK'] = GetPackData($arOffer['XML_ID']);
                $arOffer['AVAILABLE'] = current($arProductOffers[$arElement['ID']])['CATALOG_QUANTITY'];
                $arOffer['QUANTITY'] = (!empty($count)) ? $count : 1;
            } else {
                if (!empty($offerParam))
                    return array();
                $words = explode(' ', $item);
                $offerParam = mb_strtolower(end($words));
                if ($offerParam == 'см') {
                    $offerParam = implode(' ', array_slice($words, -2));
                    $item = implode(' ', array_splice($words, 0, -2));
                } else {
                    $item = implode(' ', array_splice($words, 0, -1));
                }
                $offerParam = trim(preg_replace("/[^\w_\s]+/u", "", $offerParam));
                return self::findProduct($item, $count, $offerParam);
            }
            return $arOffer;
        }

        /**
         * Получаем все товары из текущей корзины пользователя
         * @param string $user_id
         * @return array
        */
        public function getUserBasketItems ($user_id = "") {
            $arBasket = array();
            $obBasket = \CSaleBasket::GetList(
                array('ID' => 'ASC'),
                array(
                    'FUSER_ID' => $this->getFuserIdByAimyUserID($user_id),
                    'ORDER_ID' => 'NULL'
                )
            );
            while ($arBasketItem = $obBasket->Fetch())
                $arBasket[] = $arBasketItem;

            return $arBasket;
        }

        /**
         * Удалить корзину пользователя по $user_id
         * @param string $user_id
         * @return boolean
        */
        public function clearUserBasketByUserID ($user_id = "") {
            return \CSaleBasket::DeleteAll($this->getFuserIdByAimyUserID($user_id));
        }

        /**
         * Добавляем товар с нужным кол-вом в корзину пользователя
         * @param string $user_id
         * @param array $item
         * @param int $quantity
         * @return mixed
        */
        public function add2BasketByUserID ($user_id = "", $item = array(), $quantity = 1) {
            if (empty($user_id) || empty($item) || $quantity < 1)
                \botTools::sendMessage('Ошибка добавления в корзину, не указан ID пользователя или Товар не найден или кол-во меньше единицы', false, false, array('result' => array('method' => '')));

            $itemPrice = $this->getOptimalPriceByProductID($item['ID']);
            $arFields = array(
                "PRODUCT_ID" => $item['ID'],
                "PRODUCT_PRICE_ID" => $itemPrice['RESULT_PRICE']['PRICE_TYPE_ID'],
                "PRICE" => $itemPrice['RESULT_PRICE']['BASE_PRICE'],
                "CURRENCY" => $itemPrice['RESULT_PRICE']['CURRENCY'],
                "QUANTITY" => $quantity,
                "LID" => LANG,
                "CAN_BUY" => "Y",
                "NAME" => $item['NAME'],
                "FUSER_ID" => $this->getFuserIdByAimyUserID($user_id),
                "PROPS" => array()
              );

              if (\CSaleBasket::Add($arFields)) {
                  return true;
              } else {
                  \botTools::sendMessage('Ошибка добавления в корзину, мы уже исправляем проблему, попробуйте повторить запрос позже', false, array('result' => array('method' => '')));
              }
        }

        /**
         *  Получаем цену для товара по ID
         * @param string $id
         * @return mixed
        */
        public function getOptimalPriceByProductID ($id) {
            if (empty($id)) return false;
            return \CCatalogProduct::GetOptimalPrice($id, 1);
        }

        /**
         * Получаем список всех цен на сайте
         * @return array
        */
        public function getCatalogPriceTypes () {

        }

        /**
         * Получаем FUSER_ID из токена user_id который передает AIMY
         * @param string $user_id
         * @return string
        */
        public function getFuserIdByAimyUserID ($user_id = "") {
            $fuser = \CSaleBasket::GetBasketUserID();
            $fusers = FuserTokenRelationsTable::getList(array(
                // 'select' => array('ID', 'FUSER', 'TOKEN'),
                'filter' => array('TOKEN' => $user_id),
                // 'group' => array(),
                // 'order' => array(),
                // fasle, false,
                // 'runtime' => array()
            ))->fetchAll();
            if (count($fusers) >= 1) {
                $fuser = $fusers[0]['FUSERID'];
            } else {
                FuserTokenRelationsTable::add(array(
                    'TOKEN' => $user_id,
                    'FUSERID' => $fuser
                ));
            }

            return $fuser;
        }

    }

?>
