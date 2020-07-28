<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 25.07.2016
 * Time: 15:12
 */

use Bitrix\Main\Diag\Debug,
	Bitrix\Main\EventManager,
	Bitrix\Iblock\PropertyIndex,
	Bitrix\Main\Context,
	Bitrix\Main\Loader,
	Bitrix\Main\Type\Collection,
	Bitrix\Main\Type\DateTime,
	Bitrix\Main,
	Bitrix\Currency,
	Bitrix\Catalog,
	Bitrix\Iblock,
	Bitrix\Sale,
    Bitrix\Main\Mail\Event;

\Bitrix\Main\Loader::includeModule('dsklad.site');

// Подключение функций
if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/functions.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/functions.php");

//
if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/constants.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/constants.php");

//
if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/helper/elementHelper.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/helper/elementHelper.php");

//
if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/order.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/order.php");
//
if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/catalog.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/catalog.php");

//
if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/CustomRetailCrmOrder.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/CustomRetailCrmOrder.php");

// Прикладные функции
if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/Utils.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/Utils.php");

// Подключаем обработчики ограничений для платежек и доставок в оформлении заказа
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/RestrictionHandlers.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/RestrictionHandlers.php");

// Распредиляем товары в раздел дисконт
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/goodsInDiscountSection/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/goodsInDiscountSection/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/goodsInDiscountSection/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/goodsInDiscountSection/functions.php");

// Добовляем плагинацию к title каталога
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/pluginForTitleCatalog/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/pluginForTitleCatalog/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/pluginForTitleCatalog/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/pluginForTitleCatalog/functions.php");

// События и функции связанные с админ панелью
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/adminPanelCastom/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/adminPanelCastom/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/adminPanelCastom/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/adminPanelCastom/functions.php");

// Получение минимальной цены
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/MyGetOptimalPrice/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/MyGetOptimalPrice/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/MyGetOptimalPrice/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/MyGetOptimalPrice/functions.php");

//
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/AddCAgent/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/AddCAgent/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/AddCAgent/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/AddCAgent/functions.php");

//
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnBeforeUserUpdateHandler/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnBeforeUserUpdateHandler/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnBeforeUserUpdateHandler/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnBeforeUserUpdateHandler/functions.php");

//отправка письма с паролем при регистрации
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnAfterUserRegisterHandler/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnAfterUserRegisterHandler/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnAfterUserRegisterHandler/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnAfterUserRegisterHandler/functions.php");

//
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnAfterUserUpdateHandler/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnAfterUserUpdateHandler/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnAfterUserUpdateHandler/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnAfterUserUpdateHandler/functions.php");

//
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/DoIBlockAfterSave/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/DoIBlockAfterSave/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/DoIBlockAfterSave/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/DoIBlockAfterSave/functions.php");

//
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/UpdateDiscounts/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/UpdateDiscounts/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/UpdateDiscounts/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/UpdateDiscounts/functions.php");

//
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnBeforeIBlockElementUpdate/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnBeforeIBlockElementUpdate/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnBeforeIBlockElementUpdate/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnBeforeIBlockElementUpdate/functions.php");

// Проставляем в доп поле заказа остаток товаров на момент заказа
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Makcrx_OnOrderSaveNew_WriteItemQuantities/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Makcrx_OnOrderSaveNew_WriteItemQuantities/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Makcrx_OnOrderSaveNew_WriteItemQuantities/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Makcrx_OnOrderSaveNew_WriteItemQuantities/functions.php");

//Модификация письма о заказе
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Makcrx_OnAfterUserAdd_AddEmailIfNotExists/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Makcrx_OnAfterUserAdd_AddEmailIfNotExists/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Makcrx_OnAfterUserAdd_AddEmailIfNotExists/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Makcrx_OnAfterUserAdd_AddEmailIfNotExists/functions.php");

// makcrx: не отправляем сообщения на почты, оканчивающиеся на @dsklad.ru и @crm.com
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Makcrx_OnBeforeEventSend_DontSendToInvalidEmails/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Makcrx_OnBeforeEventSend_DontSendToInvalidEmails/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Makcrx_OnBeforeEventSend_DontSendToInvalidEmails/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Makcrx_OnBeforeEventSend_DontSendToInvalidEmails/functions.php");

// makcrx: когда клиент оплатит заказ с помощью Яндекс-кассы, отправляем время оплаты и ID транзакции в RetailCRM
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Makcrx_OnSalePayOrder_SendTransactionId/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Makcrx_OnSalePayOrder_SendTransactionId/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Makcrx_OnSalePayOrder_SendTransactionId/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Makcrx_OnSalePayOrder_SendTransactionId/functions.php");

// Добавление дополнительного поля для поиска search:title, форма поиска в шапке сайта
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/BeforeIndexHandler/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/BeforeIndexHandler/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/BeforeIndexHandler/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/BeforeIndexHandler/functions.php");

// отмена отправки письма клиенту, если проставлен флаг не отправлять у него
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/send_order_data__SLANES/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/send_order_data__SLANES/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/send_order_data__SLANES/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/send_order_data__SLANES/functions.php");

// не отпровлять повторно письмо по статусу "Выполнен" и "Передан в доставку"
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/doMailOnStatus/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/doMailOnStatus/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/doMailOnStatus/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/doMailOnStatus/functions.php");

// запрещает удалять элементы инфоблоков
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/ForbidRemoveItemsLogistic/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/ForbidRemoveItemsLogistic/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/ForbidRemoveItemsLogistic/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/ForbidRemoveItemsLogistic/functions.php");

//определение телефона //Any mobile device (phones or tablets).
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/php_lib/Mobile_Detect.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/php_lib/Mobile_Detect.php");

//
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/php_lib/autoload.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/php_lib/autoload.php");

// Все функции и события retailcrm живут тут
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Retailcrm/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Retailcrm/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Retailcrm/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Retailcrm/functions.php");

//Модификация письма о заказе
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/order_send_mail.php'))
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/order_send_mail.php');

//Самовызывающееся функции
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/actionFunctions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/actionFunctions.php");

// Перехват выгрузки католога из 1С
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnBeforeCatalogImport1C/event.php"))
	require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnBeforeCatalogImport1C/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnBeforeCatalogImport1C/functions.php"))
	require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/OnBeforeCatalogImport1C/functions.php");

// предоплата по предзаказу
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/PrepaymentOnPreorder/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/PrepaymentOnPreorder/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/PrepaymentOnPreorder/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/PrepaymentOnPreorder/functions.php");

// оплатить только доставку
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/payOnlyDelivery/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/payOnlyDelivery/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/payOnlyDelivery/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/payOnlyDelivery/functions.php");

// сохранение имени покупателя в профиль
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/SaveNameProfileUser/event.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/SaveNameProfileUser/event.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/SaveNameProfileUser/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/SaveNameProfileUser/functions.php");

// деактивируем товары с нулевым остатком, если нет даты поставки
//if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/deactivateGoodsZeroQuantity/event.php"))
//    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/deactivateGoodsZeroQuantity/event.php");
//if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/deactivateGoodsZeroQuantity/functions.php"))
//    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/deactivateGoodsZeroQuantity/functions.php");
//?>