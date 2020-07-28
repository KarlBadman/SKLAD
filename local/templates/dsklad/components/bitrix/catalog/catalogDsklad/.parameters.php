<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

$arTemplateParameters = array(
    "OFFERS_SELECT_PROPERTY" => array(
        "PARENT" => "BASE",
        "NAME" => 'Свойства отвечающие за переключение торговых предложений',
        "TYPE" => "LIST",
        "VALUES" => '',
        'MULTIPLE' => 'Y',
    ),
    "OFFERS_PROPERTY_TYPE_IMAGES_LINK" => array(
        "PARENT" => "BASE",
        "NAME" => 'Свойства типа ссылка на изображение',
        "TYPE" => "LIST",
        "VALUES" => '',
        'MULTIPLE' => 'Y',
    ),
    "OFFERS_PROPERTY_TYPE_SM" => array(
        "PARENT" => "BASE",
        "NAME" => 'Свойства в сантиметрах',
        "TYPE" => "LIST",
        "VALUES" => '',
        'MULTIPLE' => 'Y',
    ),
    "BALANCE_ON_STOCK" => array(
        "PARENT" => "BASE",
        "NAME" => 'CODE свойства количества товаров',
        "TYPE" => "STRING",
        "DEFAULT"=>'BALANCE_ON_STOCK',
    ),
    "NO_RANGE_PRICE" => array(
        "PARENT" => "BASE",
        "NAME" => 'Не выводить рекомендованное количество',
        "TYPE" => "CHECKBOX",
        "DEFAULT"=>'N',
    ),
    "BANNER_MOBILE_CATALOG_SECTION" => array(
        "PARENT" => "BASE",
        "NAME" => 'Тип банера раздела каталог для мобильного',
        "TYPE" => "STRING",
    ),
    "BANNER_CATALOG_SECTION" => array(
        "PARENT" => "BASE",
        "NAME" => 'Тип банера раздела каталог',
        "TYPE" => "STRING",
    ),
);


?>