<?php
/**
 * Кнопа в Админ. панели для авторизации от имени пользователя
 */
AddEventHandler('main', 'OnAdminContextMenuShow','ButtonAuthorization');
/**
 * Пункт меню админ панели для перехода к Служебным сервисам
 */
AddEventHandler("main", "OnBuildGlobalMenu", "ChangeIblockMenu");