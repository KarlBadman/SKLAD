<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Page\Asset;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <script data-skip-moving="true" src="/local/assets/js/logger.min.js"></script>
    <?
    Asset::getInstance()->addCss('/local/assets/css/slick.css');
    Asset::getInstance()->addCss('/local/assets/css/purepopup.css');
    Asset::getInstance()->addCss('/local/assets/css/jquery.suggestions.css');
    Asset::getInstance()->addCss('/local/assets/css/owl.carousel.min.css');
    Asset::getInstance()->addCss('/local/assets/css/owl.theme.default.min.css');
    Asset::getInstance()->addCss('/local/assets/css/dsklad-styles.css');
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/css/voprosy-otvety/style.css');
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/catalog/style.css');
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/css/normalize.css');
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/css/index/style.css');

    Asset::getInstance()->addJs('/local/assets/js/jquery-3.4.1.min.js');
    Asset::getInstance()->addJs('/local/assets/js/slick.min.js');
    Asset::getInstance()->addJs('/local/assets/js/isMobile.min.js'); #2016
    Asset::getInstance()->addJs('/local/assets/js/owl.carousel.min.js');
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/nouislider.min.js');
    Asset::getInstance()->addJs('/local/assets/js/jquery.autocomplete.js');
    Asset::getInstance()->addJs('/local/assets/js/jquery.suggestions.js');
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/svgxuse.js');
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/sly.js');
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/jquery.visible.min.js');
    Asset::getInstance()->addJs('/local/assets/js/common.js');
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/main.js');
    Asset::getInstance()->addJs('/local/assets/js/purepopup.js');
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/main_new.js');
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/lazysizes.min.js');
    Asset::getInstance()->addJs('/local/assets/js/analytic-systems.js');
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/libs/device.min.js');
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/dsklad-scripts.js');
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/voprosy-otvety.js');
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/jquery.easing.min.js');
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/retailRocket.js');
    ?>

    <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="yandex-verification" content="12f3bf5b1ce3ead2" />

    <link rel="shortcut icon" type="image/icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="57x57" href="<?= SITE_TEMPLATE_PATH ?>/images/favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?= SITE_TEMPLATE_PATH ?>/images/favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?= SITE_TEMPLATE_PATH ?>/images/favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= SITE_TEMPLATE_PATH ?>/images/favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?= SITE_TEMPLATE_PATH ?>/images/favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?= SITE_TEMPLATE_PATH ?>/images/favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?= SITE_TEMPLATE_PATH ?>/images/favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= SITE_TEMPLATE_PATH ?>/images/favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= SITE_TEMPLATE_PATH ?>/images/favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= SITE_TEMPLATE_PATH ?>/images/favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_TEMPLATE_PATH ?>/images/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?= SITE_TEMPLATE_PATH ?>/images/favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= SITE_TEMPLATE_PATH ?>/images/favicons/favicon-16x16.png">
    <link rel="manifest" href="<?= SITE_TEMPLATE_PATH ?>/images/favicons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?= SITE_TEMPLATE_PATH ?>/images/favicons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

<? if(\Dsklad\Config::getOption('UF_PWA') == 1){ ?>
    <script data-skip-moving="true" type="text/javascript">
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('<?= SITE_TEMPLATE_PATH ?>/js/sw.js', { scope: '<?= SITE_TEMPLATE_PATH ?>/js/' })
            .then(function (registration) {
                console.log('ServiceWorker registration successful with scope: ', registration.scope);
            },
                function (err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
        });
    }
    </script>
<? } ?>

    <title><? $APPLICATION->ShowTitle(); ?></title>
    <? $APPLICATION->ShowHead(); ?>

    <?php if($stockVars = checkTempStockAcitvity()){
        echo '<style>' . $stockVars['styles'] . '</style>';
    }?>
    <?$APPLICATION->ShowViewContent('metaCatalogElementImg');?>
    <? $APPLICATION->IncludeFile('/include_areas/common_header_metrics.php'); ?>
</head>

<body <?$APPLICATION->ShowViewContent('page_type');?>>
<? $APPLICATION->IncludeFile('/include_areas/common_body_metrics.php'); ?>
<div id="panel">
    <? $APPLICATION->ShowPanel(); ?>
</div>
<?
$APPLICATION->IncludeComponent(
    'swebs:dpd.current.city',
    '',
    array(
        'DPD_HL_ID' => \Dsklad\Config::getParam('hl/dpd_cities'),
        'COMPONENT_TEMPLATE' => '.default',
        'HIDE_CITY_NAME' => 'Y'
    ),
    false
);
?>

<header>
    <div class="ds-header">
        <div class="ds-header__menu js-header-menu"><span class="header-icon header-icon--menu"></span><span class="catalog-btn">Каталог</span></div>
        <div class="ds-header__logo"><a href="/"><img src="<?=SITE_TEMPLATE_PATH?>/images/logo-header.svg" alt="Дизайн Склад"></a></div>
        <div class="ds-header__phone has-popup"><span class="header-icon header-icon--phone"></span>
            <div class="ds-header-popup ds-header-popup--phones">
                <div class="ds-header-phones">
                    <p>Ежедневно с 9 до 21</p>
                    <span class="main-phone">
                        <?$APPLICATION->IncludeFile(
                                SITE_TEMPLATE_PATH.'/include_areas/phone.php',
                                array(),
                                array(
                                    'MODE' => 'text'
                                )
                            );?>
                    </span>
                    <span class="or">- или -</span>
                    <?$APPLICATION->IncludeFile(
                            SITE_TEMPLATE_PATH.'/include_areas/messenger_header.php',
                            array(),
                            array(
                                'MODE' => 'text'
                            )
                        );?>

                </div>
            </div>
        </div>
        <?$APPLICATION->IncludeComponent(
            "bitrix:sale.basket.basket.line",
            "header",
            Array(
                "COMPOSITE_FRAME_MODE" => "A",
                "COMPOSITE_FRAME_TYPE" => "AUTO",
                "HIDE_ON_BASKET_PAGES" => "Y",
                "PATH_TO_AUTHORIZE" => "",
                "PATH_TO_BASKET" => "/basket/",
                "PATH_TO_ORDER" => "/order/",
                "PATH_TO_PERSONAL" => "/personal/",
                "PATH_TO_PROFILE" => SITE_DIR."personal/",
                "PATH_TO_REGISTER" => SITE_DIR."login/",
                "POSITION_FIXED" => "N",
                "SHOW_AUTHOR" => "N",
                "SHOW_DELAY" => "N",
                "SHOW_EMPTY_VALUES" => "N",
                "SHOW_IMAGE" => "Y",
                "SHOW_NOTAVAIL" => "N",
                "SHOW_NUM_PRODUCTS" => "Y",
                "SHOW_PERSONAL_LINK" => "Y",
                "SHOW_PRICE" => "Y",
                "SHOW_PRODUCTS" => "Y",
                "SHOW_REGISTRATION" => "N",
                "SHOW_SUMMARY" => "Y",
                "SHOW_TOTAL_PRICE" => "Y"
            )
        );?>
        <div class="ds-header__profile">
          <?if ($USER->IsAuthorized()):?>
            <a href="<?=($USER->getId()) ? "/personal/" : "/login/"?>">
            <?if(!empty($USER->GetFullName())):?>
              <span class="user-info"><?=$USER->GetFullName()?></span>
            <?else:?>
              <span class="user-info"><?=$USER->GetLogin()?></span>
            <?endif;?>
              <span class="header-icon header-icon--user"></span>
            </a>
          <?else:?>
            <a href="<?=($USER->getId()) ? "/personal/" : "/login/"?>">
              <span class="header-icon header-icon--login"></span>
            </a>
          <?endif;?>
        </div>
        <?
        $APPLICATION->IncludeComponent(
            'bitrix:search.title',
            'catalog',
            Array(
                'CATEGORY_0' => array(  // Ограничение области поиска
                    0 => 'iblock_1c_catalog',
                ),
                'CATEGORY_0_TITLE' => '', // Название категории
                'CATEGORY_0_iblock_1c_catalog' => array(  // Искать в информационных блоках типа 'iblock_1c_catalog'
                    0 => \Dsklad\Config::getParam('iblock/offers'),
                ),
                'CHECK_DATES' => 'Y', // Искать только в активных по дате документах
                'COMPOSITE_FRAME_MODE' => 'A',  // Голосование шаблона компонента по умолчанию
                'COMPOSITE_FRAME_TYPE' => 'AUTO', // Содержимое компонента
                'CONTAINER_ID' => 'title-search', // ID контейнера, по ширине которого будут выводиться результаты
                'CONVERT_CURRENCY' => 'N',  // Показывать цены в одной валюте
                'CURRENCY_ID' => 'RUB',
                'INPUT_ID' => 'title-search-input', // ID строки ввода поискового запроса
                'NUM_CATEGORIES' => '1',  // Количество категорий поиска
                'ORDER' => 'date',  // Сортировка результатов
                'PAGE' => '#SITE_DIR#search/index.php', // Страница выдачи результатов поиска (доступен макрос #SITE_DIR#)
                'PREVIEW_HEIGHT' => '75', // Высота картинки
                'PREVIEW_TRUNCATE_LEN' => '40', // Максимальная длина анонса для вывода
                'PREVIEW_WIDTH' => '75',  // Ширина картинки
                'PRICE_CODE' => array(  // Тип цены
                    0 => 'Базовая цена продажи',
                    1 => 'Основная цена продажи',
                ),
                'PRICE_VAT_INCLUDE' => 'N', // Включать НДС в цену
                'SHOW_INPUT' => 'Y',  // Показывать форму ввода поискового запроса
                'SHOW_OTHERS' => 'N', // Показывать категорию 'прочее'
                'SHOW_PREVIEW' => 'Y',  // Показать картинку
                'TOP_COUNT' => '7', // Количество результатов в каждой категории
                'USE_LANGUAGE_GUESS' => 'Y',  // Включить автоопределение раскладки клавиатуры
            ),
            false
        ); ?>

    </div>
    <div class="header-menu">
        <div class="header-menu__name">Каталог
            <div class="header-menu-close js-menu-close"><span class="icon-svg ic-close-white"></span></div>
        </div>
        <?$APPLICATION->IncludeComponent(
            "bitrix:menu",
            "top_catalog",
            Array(
                "ALLOW_MULTI_SELECT" => "Y",
                "CHILD_MENU_TYPE" => "top",
                "COMPOSITE_FRAME_MODE" => "A",
                "COMPOSITE_FRAME_TYPE" => "AUTO",
                "DELAY" => "N",
                "MAX_LEVEL" => "2",
                "MENU_CACHE_GET_VARS" => array(""),
                "MENU_CACHE_TIME" => "3600",
                "MENU_CACHE_TYPE" => "A",
                "MENU_CACHE_USE_GROUPS" => "Y",
                "ROOT_MENU_TYPE" => "top",
                "USE_EXT" => "Y",
            )
        );?>
    </div>
</header>

<main>
    <?
    $APPLICATION->IncludeComponent(
                "dsklad:order.notification",
                '',
                Array(
                    "ORDER_STATUSES" => array(
                            "WP", // Ожидает оплаты
                            "N", // Новый заказ
                            //"PD", //Передан в доставку
                            //"PP", //В пункте самовывоза
                    ),
                    "COUNT" => 1,
                )
            );?>