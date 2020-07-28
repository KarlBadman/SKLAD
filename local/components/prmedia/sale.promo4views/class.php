<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Dsklad\PromoCodeFor4Views;

class SalePromo4ViewsComponent extends \CBitrixComponent
{
    /**
     * @return mixed|void
     * @throws Exception
     */
    public function executeComponent()
    {
        if (empty(\CHTTP::GetLastStatus()) || (\CHTTP::GetLastStatus() == '200 OK')) {
            PromoCodeFor4Views::init();
        }

        if (PromoCodeFor4Views::$needShow) {
            $this->includeComponentTemplate();
        }
    }
}