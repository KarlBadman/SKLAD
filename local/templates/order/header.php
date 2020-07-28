<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <script data-skip-moving="true" src="/local/assets/js/logger.min.js"></script>
        <title> <?$APPLICATION->ShowTitle(false)?> </title>
        <?
        $APPLICATION->ShowHead();
        Bitrix\Main\Page\Asset::getInstance()->addCss('/local/assets/css/slick.css');
        Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/components/bitrix/sale.order.ajax/show/style.css');
        Bitrix\Main\Page\Asset::getInstance()->addCss('/local/assets/css/normalize.css');
        Bitrix\Main\Page\Asset::getInstance()->addCss('/local/assets/css/jquery.suggestions.css');
        Bitrix\Main\Page\Asset::getInstance()->addCss('/local/assets/css/dsklad-styles.css');
        Bitrix\Main\Page\Asset::getInstance()->addCss('/local/assets/css/purepopup.css');
        Bitrix\Main\Page\Asset::getInstance()->addJs('/local/assets/js/jquery-3.4.1.min.js');
        Bitrix\Main\Page\Asset::getInstance()->addJs('/local/assets/js/slick.min.js');
        Bitrix\Main\Page\Asset::getInstance()->addJs('/local/assets/js/isMobile.min.js');
        Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/handlebars-v4.1.2.js');
        Bitrix\Main\Page\Asset::getInstance()->addJs('/local/assets/js/common.js');
        Bitrix\Main\Page\Asset::getInstance()->addJs('/local/assets/js/jquery.suggestions.js');
        Bitrix\Main\Page\Asset::getInstance()->addJs('/local/assets/js/purepopup.js');
        Bitrix\Main\Page\Asset::getInstance()->addJs('/local/assets/js/jquery.autocomplete.js');
        Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/parsley.min.js');
        Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/parsley-ru.js');
        Bitrix\Main\Page\Asset::getInstance()->addJs('/local/assets/js/jquery.inputmask.min.js');
        Bitrix\Main\Page\Asset::getInstance()->addJs('/local/assets/js/jquery.inputmask-multi.js');
        Bitrix\Main\Page\Asset::getInstance()->addJs('/local/assets/js/jquery.inputmask-conf.js');
        Bitrix\Main\Page\Asset::getInstance()->addJs('/local/assets/js/analytic-systems.js');
        ?>
        <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="yandex-verification" content="12f3bf5b1ce3ead2" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <? $APPLICATION->IncludeFile('/include_areas/common_header_metrics.php'); ?>
    </head>
    <body>
        <? $APPLICATION->IncludeFile('/include_areas/common_body_metrics.php'); ?>
        <div id="panel">
            <? $APPLICATION->ShowPanel(); ?>
        </div>
        <div class="ds-checkout-header">
            <div class="ds-wrapper ds-checkout-header__content">
                <div class="ds-checkout-header__logo">
                    <a href="/">
                        <img src="https://www.dsklad.ru/local/templates/dsklad/images/logo.svg" alt="">
                    </a>
                </div>
                <div class="ds-checkout-header__phone"><span>Служба поддержки</span>
                    <p><a href="tel:88007771274">8 800 777-12-74</a></p>
                </div>
            </div>
        </div>
        <div class="ds-wrapper">