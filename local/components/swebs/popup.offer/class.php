<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Highloadblock\HighloadBlockTable;
use \Bitrix\Main\Context;
use \Bitrix\Main\Loader;
use \Bitrix\Sale\Basket;
use \Bitrix\Sale\Fuser;
use \Dsklad\Config;
use Bitrix\Currency\CurrencyManager;
use Swebs\Helper;

Loader::includeModule('sale');
Loader::includeModule('iblock');
Loader::includeModule('catalog');
Loader::includeModule('swebs.helper');

class PopupOfferComponent extends \CBitrixComponent
{
    private $curProductId;
    private $imageIds = [];
    private $parentIds = [];

    /**
     * @return mixed|void
     * @throws Exception
     */
    public function executeComponent()
    {
        $this->curProductId = (int)$this->request->get('ID');
        $intQuantity = (int)$this->request->get('QTY');
        if ($intQuantity <= 0) {
            $intQuantity = 1;
        }

        $obBasket = Basket::loadItemsForFUser(CSaleBasket::GetBasketUserID(), Context::getCurrent()->getSite());

        if ($item = $obBasket->getExistsItem('catalog', $this->curProductId)) {
            $intQuantity = $item->getField('QUANTITY') + $intQuantity;
            $item->setField('QUANTITY', $intQuantity);
        } else {
            $item = $obBasket->createItem('catalog', $this->curProductId);
            $item->setFields(array(
                'QUANTITY' => $intQuantity,
                'CURRENCY' => CurrencyManager::getBaseCurrency(),
                'LID' => Context::getCurrent()->getSite(),
                'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
                'PRODUCT_XML_ID' => Helper\Iblock\Element::getFieldsByID($this->curProductId, 'XML_ID'),
                'CAN_BUY' => 'Y',
                'DELAY' => 'N',
            ));
        }
        $obBasket->save();

        // basket item information
        $arBasketItems = [];
        $arBasketItemIds = [];
        foreach ($obBasket as $obBasketItem) {
            $curBasketItemId = $obBasketItem->getProductId();
            $arBasketItems[$curBasketItemId] = [
                'BASKET_ID' => $obBasketItem->getId(),
                'QUANTITY' => $obBasketItem->getQuantity()
            ];
            $arBasketItemIds[] = $curBasketItemId;
        }

        $this->arResult = [
            'ITEMS' => [],
            'TOTAL_PRICE' => 0,
            'TOTAL_QUANTITY' => 0
        ];

        $arFilter = [
            'ID' => $arBasketItemIds
        ];
        $arSelect = [
            'ID',
            'NAME',
            'IBLOCK_ID',
            'PROPERTY_FOTOGRAFIYA_1',
            'PROPERTY_CML2_ARTICLE',
            'PROPERTY_KOD_TSVETA',
            'PROPERTY_VID_KH_KA',
            'PROPERTY_CML2_LINK'
        ];
        $dbElements = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
        if ($dbElements->SelectedRowsCount() > 0) {
            while ($arFields = $dbElements->Fetch()) {
                $arFields['QUANTITY'] = $arBasketItems[$arFields['ID']]['QUANTITY'];
                $arPriceOptimal = CCatalogProduct::GetOptimalPrice($arFields['ID'], $arFields['QUANTITY']);
                $arFields['PRICE'] = $arPriceOptimal['DISCOUNT_PRICE'];
                if ($arPriceOptimal['MAX_PRICE'] > $arPriceOptimal['DISCOUNT_PRICE']) {
                    $arFields['OLD_PRICE'] = $arPriceOptimal['MAX_PRICE'];
                    $arFields['PRINT_OLD_PRICE'] = number_format($arFields['OLD_PRICE'], 0, '', ' ');
                    $arFields['PERCENT'] = round(100 - $arFields['PRICE'] * 100 / $arFields['OLD_PRICE']);
                }
                $arFields['PRINT_PRICE'] = number_format($arFields['PRICE'], 0, '', ' ');
                $arFields['TOTAL_PRICE'] = number_format($arFields['PRICE'] * $arFields['QUANTITY'], 0, '', ' ');
                $arFields['BASKET_ID'] = $arBasketItems[$arFields['ID']]['BASKET_ID'];


                $this->arResult['TOTAL_QUANTITY'] += $arFields['QUANTITY'];
                $this->arResult['TOTAL_PRICE'] += $arFields['PRICE'] * $arFields['QUANTITY'];

                $this->imageIds[] = $arFields['PROPERTY_FOTOGRAFIYA_1_VALUE'];
                $this->parentIds[] = $arFields['PROPERTY_CML2_LINK_VALUE'];

                $this->arResult['ITEMS'][] = $arFields;

                /*
                // Сейчас этот блок выключен. Для использования нужно переписать под новую логику
                // подойдёт к покупке
                $this->arResult['WITH_THIS'] = [];
                $arWithThis = [];
                $dbWithThis = CIBlockElement::GetProperty(
                    Config::getParam('iblock/catalog'),
                    $arFields['PROPERTY_CML2_LINK_VALUE'],
                    [],
                    ['CODE' => 'WITH_THIS']
                );
                while ($arWithThisFields = $dbWithThis->GetNext()) {
                    $arWithThis[] = $arWithThisFields['VALUE'];
                }
                $arWithThis = array_filter($arWithThis, function($value) { return trim($value) !== ''; });
                if (!empty($arWithThis)) {
                    $arFilter = [
                        'ID' => $arWithThis
                    ];
                    $arSelect = [
                        'NAME', 'PROPERTY_MINIMUM_PRICE', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL'
                    ];
                    $dbElement = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
                    while ($arWithFields = $dbElement->GetNext()) {
                        if (is_numeric($arWithFields['DETAIL_PICTURE'])) {
                            $arWithFields['DETAIL_PICTURE'] = self::image_resize($arWithFields['DETAIL_PICTURE'], 110, 110);
                        }
                        if (empty($arWithFields['PROPERTY_MINIMUM_PRICE_VALUE'])) {
                            $arWithFields['PROPERTY_MINIMUM_PRICE_VALUE'] = 0;
                        }
                        $arWithFields['MIN_PRICE'] = number_format($arWithFields['PROPERTY_MINIMUM_PRICE_VALUE'], 0, '', ' ');
                        $this->arResult['WITH_THIS'][] = $arWithFields;
                    }
                }
                */
            }

            $this->fillImages();
            $this->fillColors();
            $this->fillArticles();

            usort($this->arResult['ITEMS'], ['self', 'sortItems']);
        }

        $this->IncludeComponentTemplate();
    }

    /**
     * Заполняем данные о фото товаров
     * @throws Exception
     */
    private function fillImages()
    {
        $arImages = [];
        $arHLBlock = HighloadBlockTable::getById(Config::getParam('hl/foto_1'))->fetch();
        $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();

        $rsData = $strEntityDataClass::getList([
            'filter' => [
                'UF_XML_ID' => $this->imageIds
            ],
            'select' => [
                'UF_XML_ID',
                'UF_FILE'
            ],
            'limit' => '50',
        ]);

        while ($arItem = $rsData->fetch()) {
            $arImages[$arItem['UF_XML_ID']]['PICTURE'][203] = self::image_resize($arItem['UF_FILE'], 203, 203);
            $arImages[$arItem['UF_XML_ID']]['PICTURE'][50] = self::image_resize($arItem['UF_FILE'], 50, 50);
        }

        foreach ($this->arResult['ITEMS'] as &$item) {
            if (!empty($item['PROPERTY_FOTOGRAFIYA_1_VALUE'])) {
                $item['PICTURE'][203] = $arImages[$item['PROPERTY_FOTOGRAFIYA_1_VALUE']]['PICTURE'][203];
                $item['PICTURE'][50] = $arImages[$item['PROPERTY_FOTOGRAFIYA_1_VALUE']]['PICTURE'][50];
            }
        }
    }

    /**
     * Заполнение данных о цвете товаров
     * @throws Exception
     */
    private function fillColors()
    {
        $arHLBlock = HighloadBlockTable::getById(Config::getParam('hl/color'))->fetch();
        $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();
        $arColors = [];
        $rsData = $strEntityDataClass::getList();
        while ($arItem = $rsData->fetch()) {
            $arColors[$arItem['UF_1C_CODE']] = $arItem;
        }

        foreach ($this->arResult['ITEMS'] as &$item) {
            $intColorCode = $item['PROPERTY_KOD_TSVETA_VALUE'];
            $intColorCode = explode('#', $intColorCode);
            $intColorCode = $intColorCode[1];
            if (!empty($intColorCode)) {
                $item['COLOR']['NAME'] = $arColors[$intColorCode]['UF_NAME'];
                $item['COLOR']['HEX'] = self::fromRGB($arColors[$intColorCode]['UF_RGB']);
            }
        }
    }

    /**
     * Заполнение артикулов товаров
     * @throws Exception
     */
    private function fillArticles()
    {
        // articles
        $dbParents = \CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => Config::getParam('iblock/catalog'),
                'ID' => $this->parentIds
            ],
            false,
            false,
            [
                'ID',
                'PROPERTY_CML2_ARTICLE'
            ]
        );
        $arArticles = [];
        while ($arParent = $dbParents->Fetch()) {
            $arArticles[$arParent['ID']] = $arParent['PROPERTY_CML2_ARTICLE_VALUE'];
        }

        foreach ($this->arResult['ITEMS'] as &$item) {
            if (!empty($arArticles[$item['PROPERTY_CML2_LINK_VALUE']])) {
                $item['PROPERTY_CML2_ARTICLE_VALUE'] = $arArticles[$item['PROPERTY_CML2_LINK_VALUE']];
            }
        }
    }

    private static function image_resize($intID, $intWidth, $intHeight)
    {
        $intWidth = $intWidth * 2;
        $intHeight = $intHeight * 2;
        $arSize = [
            'width' => $intWidth,
            'height' => $intHeight
        ];
        $arImage = CFile::ResizeImageGet($intID, $arSize, BX_RESIZE_IMAGE_PROPORTIONAL, true);
        $arImage = array_change_key_case($arImage, CASE_UPPER);

        return $arImage;
    }

    private static function fromRGB($strRGB)
    {
        $arRGB = explode(',', $strRGB);
        if (count($arRGB) < 3) {
            return '#000';
        }

        $R = (int)$arRGB[0];
        $G = (int)$arRGB[1];
        $B = (int)$arRGB[2];

        $R = dechex($R);
        if (strlen($R) < 2)
            $R = '0' . $R;

        $G = dechex($G);
        if (strlen($G) < 2)
            $G = '0' . $G;

        $B = dechex($B);
        if (strlen($B) < 2)
            $B = '0' . $B;

        return '#' . $R . $G . $B;
    }

    /**
     * Функция для сортировки элементов корзины
     * Текущий товар всегда в начале
     * @param $a
     * @param $b
     * @return int
     */
    private function sortItems($a, $b)
    {
        if ($a['ID'] == $this->curProductId) {
            return -1;
        }
        if ($b['ID'] == $this->curProductId) {
            return 1;
        }

        return strcmp($a['NAME'], $b['NAME']);
    }
}
