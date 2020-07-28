<?php

/*
*Включенна возможно покупки товара которого нет на складе, но ождаеться поставка
*Запрещаем отрицательное количество товара.
*тикет 15556
*/
use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Catalog;

Loader::includeModule('catalog');

Main\EventManager::getInstance()->addEventHandler('catalog', 'OnBeforeStoreProductUpdate', 'OnBeforeStoreProductUpdateHandler');
Main\EventManager::getInstance()->addEventHandler('catalog', 'OnBeforeProductUpdate', 'OnBeforeProductUpdateHandler');

function OnBeforeStoreProductUpdateHandler($intID, &$arFields) {
		if ($arFields["AMOUNT"]<0) {
			$arFields["AMOUNT"]=0;
		}
}

function OnBeforeProductUpdateHandler($intID, &$arFields) {
		if ($arFields["QUANTITY"]<0) {
			$arFields["QUANTITY"]=0;
		}
}

?>