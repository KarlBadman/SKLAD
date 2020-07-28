<?php
function ButtonAuthorization (&$arItems) {
    $request = Bitrix\Main\Context::getCurrent()->getRequest();
    $userId = intval($request->getQuery('ID'));
    if ($request->getRequestedPage() === '/bitrix/admin/user_edit.php' && $userId > 0) {
        $arAuthBtn = array(
            "TEXT" => "Авторизоваться",
            "LINK" => "/bitrix/admin/user_admin.php?ID=$userId&action=authorize&sessid=" . bitrix_sessid(),
            "ICON" => "",
            "TITLE" => "Авторизоваться под текущим пользователем"
        );
        $arItems[] = $arAuthBtn;
    }
};

function ChangeIblockMenu(&$adminMenu, &$moduleMenu){
    $moduleMenu[] = array(
        "parent_menu"	=> "global_menu_services", // поместим в раздел "Сервисы"
        "section"	=> "change_iblock_elements",
        "sort"        => 10,                    // сортировка пункта меню
        "url"         => "services/",  // ссылка на пункте меню
        "text"        => 'Служебные сервисы',       // текст пункта меню
        "title"       => 'Служебные сервисы компании', // текст всплывающей подсказки
        "icon"        => "extension_menu_icon", // малая иконка
        "page_icon"   => "extension_page_icon", // большая иконка
        "items_id"    => "change_iblock_elements",  // идентификатор ветви
        "items"       => array()          // остальные уровни меню сформируем ниже.
    );
}