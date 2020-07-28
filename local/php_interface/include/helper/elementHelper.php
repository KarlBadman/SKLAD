<?php
/**
 * Created by PhpStorm.
 * User: Gor
 * Date: 07.06.2017
 * Time: 8:04
 */

namespace gor;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;

class elementHelper
{
    protected static $resolvedInstance;
    protected $arElements = array();

    static function getInstance(){
        if(!static::$resolvedInstance){
            static::$resolvedInstance = new static();
        }
        return static::$resolvedInstance;
    }

    protected function getElement($intID)
    {
        if (!empty($this->arElements[$intID])) {
            return $this->arElements[$intID];
        }

        $dbElement = \CIBlockElement::GetByID($intID);
        if (!$obElement = $dbElement->GetNextElement()) {
            return false;
        }
        $arElement = array(
            'FIELDS' => $obElement->GetFields(),
            'PROPERTIES' => $obElement->GetProperties()
        );

        $this->arElements[$intID] = $arElement;

        return $arElement;
    }

    protected function getColor($intID)
    {
        Loader::includeModule('highloadblock');
        // colors
        $arHLBlock = HighloadBlockTable::getById(21)->fetch();
        $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();

        $arColors = array();
        $rsData = $strEntityDataClass::getList();
        while ($arItem = $rsData->fetch()) {
            $arColors[$arItem['UF_1C_CODE']] = $arItem;
        }

        // reference
        $arProperties = $this->getElement($intID)['PROPERTIES'];
        $color=$arProperties['KOD_TSVETA']['VALUE'];
        $color=explode('#',$color)[1];
        return $arColors[$color];
    }

    public static function __callStatic($method, $args){
        $instance = static::getInstance();

//        if (! $instance) {
//            throw new RuntimeException('A facade root has not been set.');
//        }

        switch (count($args)) {
            case 0:
                return $instance->$method();

            case 1:
                return $instance->$method($args[0]);

            case 2:
                return $instance->$method($args[0], $args[1]);

            case 3:
                return $instance->$method($args[0], $args[1], $args[2]);

            case 4:
                return $instance->$method($args[0], $args[1], $args[2], $args[3]);

            default:
                return call_user_func_array([$instance, $method], $args);
        }
    }
}