<?php

namespace Swebs\Helper\Highload;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

Loader::includeModule('highloadblock');

/**
 * Class Element
 * @package Swebs\Helper\Highload
 */
class Element
{
    /**
     * @param $intIblockID
     * @param $arFilter
     * @param $arSelect
     * @param $intLimit
     * @return array
     */
    public static function getElement($intIblockID, $arFilter, $arSelect, $intLimit = 0)
    {
        $strEntityDataClass = self::getEntityDataClass($intIblockID);

        $arQuery = array(
            'select' => $arSelect,
            'filter' => $arFilter
        );

        if (is_numeric($intLimit) && $intLimit > 0) {
            $arQuery['limit'] = $intLimit;
        }

        $dbData = $strEntityDataClass::getList($arQuery);

        $arElements = $dbData->fetchAll();

        return $arElements;
    }

    public static function update($intIblockID, $intElementID, $arUpdate)
    {
        $strEntityDataClass = self::getEntityDataClass($intIblockID);
        $obResult = $strEntityDataClass::update($intElementID, $arUpdate);

        return $obResult;
    }

    private static function getEntityDataClass($intIblockID)
    {
        $arHLBlock = HighloadBlockTable::getById($intIblockID)->fetch();
        $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();

        return $strEntityDataClass;
    }
}