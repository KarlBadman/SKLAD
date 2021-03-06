<?php
if (class_exists('swebs_color_status')) return;

Class swebs_color_status extends CModule
{
    var $MODULE_ID = 'swebs.color_status';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;
    var $MODULE_GROUP_RIGHTS = 'N';

    function swebs_color_status()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . '/version.php');

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = 'Color Status';
        $this->MODULE_DESCRIPTION = 'Цветные статусы заказа';

        $this->PARTNER_NAME = 's-webs';
        $this->PARTNER_URI = 'http://www.s-webs.ru/';
    }

    function DoInstall()
    {
        RegisterModule($this->MODULE_ID);
        return true;
    }

    function DoUninstall()
    {
        UnRegisterModule($this->MODULE_ID);
        return true;
    }
}
