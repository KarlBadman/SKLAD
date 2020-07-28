<?php

IncludeModuleLangFile(__FILE__);

class dellindev_delivery extends CModule
{
	var $MODULE_ID = "dellindev.delivery";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;
    var $MODULE_GROUP_RIGHTS = "N";
    var $NEED_MAIN_VERSION = "";
    var $NEED_MODULES = array("sale");

	function dellindev_delivery()
    {
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = GetMessage("DELLIN_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("DELLIN_MODULE_DESCRIPTION");

		$this->PARTNER_NAME = GetMessage("DELLIN_PARTNER_NAME");
		$this->PARTNER_URI = "https://www.dellin.ru/";
	}

    function InstallEvents()
    {
        RegisterModuleDependences("sale", "onSaleDeliveryHandlersBuildList", $this->MODULE_ID, "DellinDelivery", "Init");

        return true;
    }

    function UnInstallEvents()
    {
        UnRegisterModuleDependences("sale", "onSaleDeliveryHandlersBuildList", $this->MODULE_ID, "DellinDelivery", "Init");
        CAgent::RemoveModuleAgents($this->MODULE_ID);

        return true;
    }

	function DoInstall()
    {
        $this->InstallEvents();
        RegisterModule($this->MODULE_ID);
	}

	function DoUninstall()
    {
        $this->UnInstallEvents();
        UnRegisterModule($this->MODULE_ID);
	}
}
