<?php
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

function listElementEST($arItems)
{

    $arHLBlockFoto = \Bitrix\Highloadblock\HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
    $obEntityFoto = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlockFoto);
    $photoEntityDataClass = $obEntityFoto->getDataClass();

    foreach ($arItems as $key => $item) {
        global $USER, $EXCLUDEDGROUPID;
        if (in_array($EXCLUDEDGROUPID, $USER->GetUserGroupArray())) {
            $object = new CIBlockElementRights($item['CML2_LINK_ID'] ? CATALOG_IBLOCK_ID : $item['IBLOCK_ID'], $item['CML2_LINK_ID'] ?: $item['ID']);
            $arRights = $object->GetRights();
            $elementDisable = array_map(function ($item) {
                global $EXCLUDEDGROUPID, $EXCLUDEDRIGHTID;
                if ($item['GROUP_CODE'] == 'G' . $EXCLUDEDGROUPID && $item['TASK_ID'] == $EXCLUDEDRIGHTID)
                    return true;
            }, $arRights);
            if (in_array(true, $elementDisable)) {
                unset($arItems[$key]);
                continue;
            }
        }

        // HIDE2VIEW
        if (checkHitOnHideProductPosition($item['PROPERTIES']['HIDE2VIEW']['VALUE_XML_ID'])) {
            unset($arItems[$key]);
            continue;
        }

        if ($item['CATALOG_QUANTITY'] < 1) $arItems[$key]['PREORDER'] = true;

        $arItems[$key]['PICTURE_ITEM'] = getPhotoEST($photoEntityDataClass, $item['PROPERTIES']);

        $arItems[$key] = array_merge($arItems[$key], getPriceEST($item['ID']));

        if ($arItems[$key]['QUANTITY_FROM']) $arItems[$key]['FROM'] = true;

        if ($arItems[$key]['SALE_PERCENT'] > 0) $arItems[$key]['RED'] = true;

    }
    return $arItems;
}

function getPriceEST($productId)
{

    $arPriceParam = [];

    $allProductPrices = \Bitrix\Catalog\PriceTable::getList([
        "select" => ["*"],
        "filter" => [
            "=PRODUCT_ID" => $productId,
        ],
        "order" => ["CATALOG_GROUP_ID" => "ASC"]
    ])->fetchAll();

    $minPrice = PHP_INT_MAX;
    $arMinPrice = [];
    $arOldPrice = [];
    foreach ($allProductPrices as $price) {
        if ($price['CATALOG_GROUP_ID'] == 1) {
            $arOldPrice[$price['QUANTITY_FROM']] = $price['PRICE'];
        } else {
            if ($minPrice > $price['PRICE']) {
                $arMinPrice = $price;
            }
        }
    }

    $arMinPrice['QUANTITY_FROM'] > 1 ? $arPriceParam['QUANTITY_FROM'] = $arMinPrice['QUANTITY_FROM'] : $arPriceParam['QUANTITY_FROM'] = false;

    $arPriceParam['MIN_PRICE'] = $arMinPrice['PRICE'];

    if ($arMinPrice['PRICE'] < $arOldPrice[$arMinPrice['QUANTITY_FROM']]) {
        $arPriceParam['SALE_PERCENT'] = 100 - round($arMinPrice['PRICE'] * 100 / $arOldPrice[$arMinPrice['QUANTITY_FROM']]);
    }

    return $arPriceParam;
}

function getPhotoEST($photoEntityDataClass, $property)
{ // Получаем изображение товара
    for ($i = 1; isset($property['FOTOGRAFIYA_' . $i]); $i++) {
        if (!empty($property['FOTOGRAFIYA_' . $i]['VALUE'])) {
            $rsData = $photoEntityDataClass::getList(array(
                'select' => array('UF_FILE'),
                'filter' => array('UF_XML_ID' => $property['FOTOGRAFIYA_' . $i]['VALUE']),
                'limit' => '50',
            ));
            if ($arItem = $rsData->fetch()) {
                $arImage = CFile::GetFileArray($arItem['UF_FILE']);
                $arImage = CFile::ResizeImageGet($arImage, array('width' => 680, 'height' => 680), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
                return $arImage['src'];
            }
            break;
        }
    }
}

function initEST($arResult)
{
    Loader::includeModule('highloadblock');
    $arResult['ITEMS'] = listElementEST($arResult['ITEMS']);

    return $arResult;
}

$arResult = initEST($arResult);