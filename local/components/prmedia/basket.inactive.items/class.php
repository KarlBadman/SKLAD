<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Iblock\ElementTable;
use \Dsklad\Config;
use \Bitrix\Sale\Basket;
use \Bitrix\Sale\Fuser;
use \Bitrix\Main\Context;

/**
 * Работа с неактивными товарами в корзине при оформлении заказа
 * Class BasketInactiveItemsComponent
 */
class BasketInactiveItemsComponent extends \CBitrixComponent
{

    private $inactiveItems = [];
    private $relatedProducts = [];

    /**
     * @return mixed|void
     * @throws \Bitrix\Main\ArgumentException
     * @throws Exception
     */
    public function executeComponent()
    {
        $this->arResult = [];

        if (!empty($this->arParams['BASKET_ITEMS'])) {
            $this->inactiveItems = $this->getInactive();

            $this->arResult['ITEMS'] = $this->getItemsProperties();
            $this->arResult['RELATED'] = $this->relatedProducts;

            if (!empty($this->arResult['ITEMS'])) {
                $this->deleteInactive();

                $this->includeComponentTemplate();
            }
        }
    }

    /**
     * Получает список неактивных товаров
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws Exception
     */
    private function getInactive() :array
    {
        $inactiveItems = [];

        $basket = $this->arParams['BASKET_ITEMS'];

        //Обрабатываем два типа товаров - "Простой" и "Торговое предложение"
        $itemsByTypes = [
            'OFFERS' => [],
            'PRODUCTS' => []
        ];
        foreach ($basket as $basketItem) {
            if (!empty($basketItem['PARENT'])) {
                $itemsByTypes['OFFERS'][$basketItem['PRODUCT_ID']] = $basketItem['PARENT'];
            } else {
                $itemsByTypes['PRODUCTS'][] = $basketItem['PRODUCT_ID'];
            }
        }

        if (empty($itemsByTypes['OFFERS']) && empty($itemsByTypes['PRODUCTS'])) {
            return $inactiveItems;
        }

        $productIds = [];

        foreach ($itemsByTypes['PRODUCTS'] as $productId) {
            $productIds[] = $productId;
        }

        if (!empty($itemsByTypes['OFFERS'])) {
            $offersData = self::getOffersData($itemsByTypes['OFFERS']);

            if (!empty($offersData['IN_ACTIVE'])) {
                foreach ($offersData['IN_ACTIVE'] as $offerId => $productId) {
                    $inactiveItems[] = $offerId;
                }
            }

            // Для торгвых предложений проверяем также активность товара-родителя
            if (!empty($offersData['ACTIVE'])) {
                foreach ($offersData['ACTIVE'] as $offerId => $productId) {
                    $productIds[] = $productId;
                }
            }
        }

        $inactiveProducts = self::getInactiveProducts($productIds);

        if (!empty($inactiveProducts)) {
            if (!empty($itemsByTypes['OFFERS'])) {
                foreach ($itemsByTypes['OFFERS'] as $offerId => $productId) {
                    if (in_array($productId, $inactiveProducts)) {
                        $inactiveItems[] = $offerId;
                    }
                }
            } else {
                $inactiveItems = array_merge($inactiveItems, $inactiveProducts);
            }
        }

        $inactiveItems = array_unique($inactiveItems);

        return $inactiveItems;
    }

    /**
     * Получаем данные по активности торговых предложений
     * @param array $offers
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws Exception
     */
    private static function getOffersData(array $offers) :array
    {
        $offersData = [];

        if (empty($offers)) {
            return $offersData;
        }

        $db = ElementTable::getList([
            'filter' => [
                'IBLOCK_ID' => Config::getParam('iblock/offers'),
                'ID' => array_keys($offers)
            ],
            'select' => [
                'ID',
                'ACTIVE'
            ]
        ]);

        $offersData = [
            'ACTIVE' => [],
            'IN_ACTIVE' => []
        ];
        while ($ar = $db->fetch()) {
            if ($ar['ACTIVE'] == 'Y') {
                $offersData['ACTIVE'][$ar['ID']] = $offers[$ar['ID']];
            } else {
                $offersData['IN_ACTIVE'][$ar['ID']] = $offers[$ar['ID']];
            }
        }

        return $offersData;
    }

    /**
     * Возвращает список неактивных товаров
     * Проверяется активность самого товара и раздела в который он входит
     * @param array $productIds
     * @return array
     * @throws Exception
     */
    private static function getInactiveProducts(array $productIds) :array
    {
        $inactiveProducts = [];

        if (empty($productIds)) {
            return $inactiveProducts;
        }

        $db = \CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => Config::getParam('iblock/catalog'),
                'ID' => $productIds,
                'ACTIVE' => 'Y',
                'SECTION_GLOBAL_ACTIVE' => 'Y'
            ],
            false,
            false,
            [
                'ID'
            ]
        );

        $activeProducts = [];
        while ($ar = $db->Fetch()) {
            $activeProducts[] = $ar['ID'];
        }

        $inactiveProducts = array_diff($productIds, $activeProducts);

        return $inactiveProducts;
    }


    /**
     * Получаем параметры товаров в корзине
     * @return array
     * @throws Exception
     */
    private function getItemsProperties() :array
    {
        $itemsProperties = [];

        if (empty($this->inactiveItems) || empty($this->arParams['BASKET_ITEMS'])) {
            return $itemsProperties;
        }

        $productIds = [];
        foreach ($this->arParams['BASKET_ITEMS'] as $item) {
            if (in_array($item['PRODUCT_ID'], $this->inactiveItems)) {
                $itemsProperties[] = $item;

                if (!empty($item['PARENT'])) {
                    $productIds[] = $item['PARENT'];
                } else {
                    $productIds[] = $item['PRODUCT_ID'];
                }
            }
        }

        $productIds = array_unique($productIds);
        $this->getRelatedProducts($productIds);

        return $itemsProperties;
    }

    /**
     * Получаем список товаров из свойства "Похожие"
     * @param array $productIds
     * @throws Exception
     */
    private function getRelatedProducts(array $productIds)
    {
        if (empty($productIds)) {
            return;
        }

        $db = \CIBlockElement::GetList(
            [
                'ID' => 'ASC',
                'RAND' => 'ASC'
            ],
            [
                'IBLOCK_ID' => Config::getParam('iblock/catalog'),
                'ID' => $productIds,
                'ROPERTY_RELATED.ACTIVE' => 'Y'
            ],
            false,
            false,
            [
                'ID',
                'PROPERTY_RELATED.PREVIEW_PICTURE',
                'PROPERTY_RELATED.DETAIL_PAGE_URL'
            ]
        );

        //Суммарное количество похожих товаров должно быть не меньше 6
        $count = max(ceil(6 / count($productIds)), 1);

        $i = 0;
        $lastParentId = 0;
        while ($ar = $db->Fetch()) {
            if ($lastParentId != $ar['ID']) {
                $lastParentId = $ar['ID'];
                $i = 0;
            }

            if ($i < $count) {
                if (!empty($ar['PROPERTY_RELATED_PREVIEW_PICTURE'])) {
                    $this->relatedProducts[] = [
                        'IMAGE' => $ar['PROPERTY_RELATED_PREVIEW_PICTURE'],
                        'URL' => str_replace('//', '/', $ar['PROPERTY_RELATED_DETAIL_PAGE_URL'])
                    ];
                }
            }

            $i++;
        }

        shuffle($this->relatedProducts);
    }

    /**
     * Удаляет неактивные товары из текущей корзины пользователя
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentNullException
     */
    private function deleteInactive()
    {
        if (empty($this->inactiveItems)) {
            return;
        }

        $basket = Basket::loadItemsForFUser(Fuser::getId(), Context::getCurrent()->getSite());

        foreach ($basket as $basketItem) {
            if (in_array($basketItem->getProductId(), $this->inactiveItems)) {
                $basketItem->delete();
            }
        }

        $basket->save();
    }
}