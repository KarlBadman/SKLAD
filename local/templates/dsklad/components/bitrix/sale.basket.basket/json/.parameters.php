<?php

$arPriceType = array();
$dbPriceType = CCatalogGroup::GetList(
    array(),
    array(),
    array(),
    false,
    array('ID','NAME')
);
while ($priceType = $dbPriceType->Fetch())
{
    $arPriceType[$priceType['ID']] = $priceType['NAME'];
}


$arTemplateParameters['PRICE_TYPE'] = array(
    'PARENT' => 'BASE',
    'NAME' => 'Тип цены',
    'TYPE' => 'LIST',
    'VALUES' => $arPriceType,

);

$arTemplateParameters['ID_WARRANTY'] = array(
    'PARENT' => 'BASE',
    'NAME' => 'ID элемента доп. гарантии',
    'TYPE' => 'STRING',
    'VALUES' => '',

);

$arTemplateParameters['RECOMMENDED_ITEMS_IN_CART'] = array(
    'PARENT' => 'BASE',
    'NAME' => 'CODE свойства рекомендованых товаров',
    'TYPE' => 'STRING',
    'VALUES' => '',

);
