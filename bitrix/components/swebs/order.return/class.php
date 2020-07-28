<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/*use Bitrix\Main\Loader;

Loader::includeModule('catalog');*/

CBitrixComponent::includeComponentClass('swebs:order.detail');

class COrderReturn extends COrderDetail
{

}