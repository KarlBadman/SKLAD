<?php
class dsklad_site extends CModule
{
    var $MODULE_ID = 'dsklad.site';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_SORT;
    var $MODULE_MODE_EXEC = 'local';
    var $PATH;
    var $PATH_ADMIN;
    var $PATH_INSTALL;
    var $PATH_INSTALL_DB;
    var $BXPATH;
    var $BXPATH_ADMIN;

    public function __construct()
    {
        $this->MODULE_NAME = "Основной модуль проекта";
        $this->MODULE_DESCRIPTION = "
            -Подключается: /local/php_interface/init.php;
            -Расположен: /local/modules/dsklad.site/;
            -Настройки: /local/modules/dsklad.site/config/ [Класс /local/modules/dsklad.site/config/lib/Config.php];
            -Настройки автоподгрузки: /local/modules/dsklad.site/settings.php;
        ";
        $this->PARTNER_NAME = "Progressive Media";
        $this->PARTNER_URI = "http://progressivemedia.ru/";

        include('version.php');

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

        global $DBType;
        $this->PATH = \Bitrix\Main\Application::getDocumentRoot() . "/$this->MODULE_MODE_EXEC/modules/$this->MODULE_ID";
        $this->PATH_ADMIN = "$this->PATH/admin";
        $this->PATH_INSTALL = "$this->PATH/install";
        $this->PATH_INSTALL_DB = "$this->PATH_INSTALL/db/$DBType";

        $this->BXPATH = \Bitrix\Main\Application::getDocumentRoot() . "/$this->MODULE_MODE_EXEC";
        $this->BXPATH_ADMIN = \Bitrix\Main\Application::getDocumentRoot() . "/bitrix/admin";
    }

    public function InstallDB()
    {
        return true;
    }

    public function UnInstallDB()
    {
        return true;
    }

    public function InstallFiles()
    {
        $this->InstallCopyAdminFiles();

        return true;
    }

    public function UnInstallFiles()
    {
        $this->UnInstallRemoveAdminFiles();

        return true;
    }

    function InstallEvents()
    {
        return true;
    }

    function UnInstallEvents()
    {
        return true;
    }

    public function DoInstall()
    {
        $this->InstallDB();
        $this->InstallFiles();
        $this->InstallEvents();
        \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
    }

    public function DoUninstall()
    {
        $this->UnInstallEvents();
        $this->UnInstallFiles();
        $this->UnInstallDB();
    }

    private function InstallCopyAdminFiles()
    {
        $prefix = str_replace('.', '_', $this->MODULE_ID);
        $content = '<?php require($_SERVER[\'DOCUMENT_ROOT\'] . \'/' . $this->MODULE_MODE_EXEC . '/modules/' . $this->MODULE_ID . '/admin/#CONTENT#\');';
        $exclude = array('menu.php');

        if ($reader = opendir($this->PATH_ADMIN)) {
            while (false !== ($file = readdir($reader))) {
                if (strpos($file, '.php') !== false && !in_array($file, $exclude)) {
                    file_put_contents(
                        "$this->BXPATH_ADMIN/$prefix" . "_$file",
                        str_replace('#CONTENT#', $file, $content)
                    );
                }
            }
            closedir($reader);
        }
    }

    private function UnInstallRemoveAdminFiles()
    {
        $prefix = str_replace('.', '_', $this->MODULE_ID);
        if ($reader = opendir($this->PATH_ADMIN)) {
            while (false !== ($file = readdir($reader))) {
                if (strpos($file, '.php') !== false) {
                    unlink("$this->BXPATH_ADMIN/$prefix" . "_$file");
                }
            }
            closedir($reader);
        }
    }
}