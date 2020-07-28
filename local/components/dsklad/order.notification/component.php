<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Application;

if(!empty($USER->GetID())) {

    // TODO: добавить обработку статусов "PD" - Передан в доставку и "PP" - В пункте самовывоза
    $alreadyNoticed = json_decode(Application::getInstance()->getContext()->getRequest()->getCookie("NOTICED"));

    // Выведем даты всех заказов текущего пользователя за текущий месяц, отсортированные по дате заказа
    $arFilter = Array(
        "USER_ID" => $USER->GetID(),
        "@STATUS_ID" => $arParams["ORDER_STATUSES"],
        "!ID" => $alreadyNoticed,
        "!PAY_SYSTEM_ID" => array(
            "3", //не наличный
            "14", //не оплата картой через CRM
            "4", //не банковский перевод Юр лицо
            "16", //не банковский перевод Физ лицо
        ),
        "PAYED"=>'N',
    );

    $arResult['ORDER_INFO'] = CSaleOrder::GetList(
        array("DATE_INSERT" => "ASC"),
        $arFilter,
        false,
        ['nPageSize' => $arParams["COUNT"]]
    )->Fetch();

    if(!empty($arResult['ORDER_INFO'])) {
        $this->IncludeComponentTemplate();
    } else {
        $APPLICATION->IncludeFile(SITE_TEMPLATE_PATH . '/include_areas/header_notification.html');
    }
} else {
    $APPLICATION->IncludeFile(SITE_TEMPLATE_PATH . '/include_areas/header_notification.html');
}