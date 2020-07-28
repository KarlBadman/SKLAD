<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetPageProperty('title', 'Личный кабинет — dsklad.ru');
$APPLICATION->AddViewContent('page_type', 'data-page-type="other-page"');
?>
<div class="cabinet__page">
    <div class="ds-wrapper">
        <section class="heading">
            <ul class="breadcrumbs__widget">
                <li><a href="/">Главная</a></li>
                <li><a href="/personal/">Личный кабинет</a></li>
            </ul>
            <div class="title">
                <h1>Личный кабинет</h1>
            </div>
        </section>
    </div>
    <section class="data">
        <div class="tabs__widget tabs__widget_adding">
            <div class="tabs-handler">
                <ul class="ds-wrapper">
                    <li class="active"><a href=""><span class="hidden-s">История заказов</span> <span class="hidden-gt-s">Заказы</span></a></li>
                    <li><a id="personal_wish_link" href=""><span class="hidden-s">Избранные товары</span> <span class="hidden-gt-s">Избранное</span></a></li>
                    <li><a id="personal_sett_link" href=""><span class="hidden-s">Личные настройки</span> <span class="hidden-gt-s">Настройки</span></a></li>
                    <?
                    /*
                    <li class="remove">
                        <a href="" target="_self" class="remove_all_favorites">
                            <span class="icon">
                                <span class="icon__cross2">
                                    <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cross2"></use></svg>
                                </span>
                            </span>
                            Удалить все
                        </a>
                    </li>
                    */
                    ?>
                    <li class="remove link_logout_personal hidden-lte-m">
                        <a href="<?= SITE_TEMPLATE_PATH ?>/ajax/logout.php" class="js-ds-modal" data-ds-modal-width="440">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/icon_logout_personal.png" alt=""/>
                            <span>Выйти из личного кабинета</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tabs-content ds-wrapper">
                <div class="tab history">
                    <?
                    $APPLICATION->IncludeComponent(
                        'swebs:order.story',
                        '.default',
                        array(
                            'COMPONENT_TEMPLATE' => '.default'
                        ),
                        false
                    );
                    ?>
                </div>
                <div class="tab like">
                    <?
                    if (!empty($_SESSION['FAVORITES'])) {
                        global $arrFilterFavorites;
                        $arrFilterFavorites = array('ID' => $_SESSION['FAVORITES']);
                        ?>
                        <?
                        $APPLICATION->IncludeComponent(
                            'bitrix:catalog.section',
                            'catalog_favorites',
                            array(
                                'ACTION_VARIABLE' => 'action',
                                'ADD_PICT_PROP' => '-',
                                'ADD_PROPERTIES_TO_BASKET' => 'Y',
                                'ADD_SECTIONS_CHAIN' => 'N',
                                'ADD_TO_BASKET_ACTION' => 'ADD',
                                'AJAX_MODE' => 'N',
                                'AJAX_OPTION_ADDITIONAL' => '',
                                'AJAX_OPTION_HISTORY' => 'N',
                                'AJAX_OPTION_JUMP' => 'N',
                                'AJAX_OPTION_STYLE' => 'Y',
                                'BACKGROUND_IMAGE' => '-',
                                'BASKET_URL' => '/personal/basket.php',
                                'BROWSER_TITLE' => '-',
                                'CACHE_FILTER' => 'N',
                                'CACHE_GROUPS' => 'Y',
                                'CACHE_TIME' => '1209600',
                                'CACHE_TYPE' => 'N',
                                'CONVERT_CURRENCY' => 'N',
                                'DETAIL_URL' => '',
                                'DISABLE_INIT_JS_IN_COMPONENT' => 'N',
                                'DISPLAY_BOTTOM_PAGER' => 'Y',
                                'DISPLAY_TOP_PAGER' => 'N',
                                'ELEMENT_SORT_FIELD' => 'sort',
                                'ELEMENT_SORT_FIELD2' => 'id',
                                'ELEMENT_SORT_ORDER' => 'asc',
                                'ELEMENT_SORT_ORDER2' => 'desc',
                                'FILTER_NAME' => 'arrFilterFavorites',
                                'HIDE_NOT_AVAILABLE' => 'N',
                                'IBLOCK_ID' => \Dsklad\Config::getParam('iblock/catalog'),
                                'IBLOCK_TYPE' => '1c_catalog',
                                'INCLUDE_SUBSECTIONS' => 'Y',
                                'LABEL_PROP' => '-',
                                'LINE_ELEMENT_COUNT' => '4',
                                'MESSAGE_404' => '',
                                'MESS_BTN_ADD_TO_BASKET' => 'В корзину',
                                'MESS_BTN_BUY' => 'Купить',
                                'MESS_BTN_DETAIL' => 'Подробнее',
                                'MESS_BTN_SUBSCRIBE' => 'Подписаться',
                                'MESS_NOT_AVAILABLE' => 'Нет в наличии',
                                'META_DESCRIPTION' => '-',
                                'META_KEYWORDS' => '-',
                                'OFFERS_CART_PROPERTIES' => array(),
                                'OFFERS_FIELD_CODE' => array(
                                    0 => '',
                                    1 => '',
                                ),
                                'OFFERS_LIMIT' => '0',
                                'OFFERS_PROPERTY_CODE' => array(
                                    0 => 'FOTOGRAFIYA_1',
                                    1 => '',
                                ),
                                'OFFERS_SORT_FIELD' => 'sort',
                                'OFFERS_SORT_FIELD2' => 'id',
                                'OFFERS_SORT_ORDER' => 'asc',
                                'OFFERS_SORT_ORDER2' => 'desc',
                                'PAGER_BASE_LINK_ENABLE' => 'N',
                                'PAGER_DESC_NUMBERING' => 'N',
                                'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
                                'PAGER_SHOW_ALL' => 'N',
                                'PAGER_SHOW_ALWAYS' => 'N',
                                'PAGER_TEMPLATE' => 'modern',
                                'PAGER_TITLE' => 'Товары',
                                'PAGE_ELEMENT_COUNT' => '99999',
                                'PARTIAL_PRODUCT_PROPERTIES' => 'N',
                                'PRICE_CODE' => array(
                                    0 => 'Базовая цена продажи',
                                    1 => 'Основная цена продажи',
                                ),
                                'PRICE_VAT_INCLUDE' => 'Y',
                                'PRODUCT_DISPLAY_MODE' => 'N',
                                'PRODUCT_ID_VARIABLE' => 'id',
                                'PRODUCT_PROPERTIES' => array(),
                                'PRODUCT_PROPS_VARIABLE' => 'prop',
                                'PRODUCT_QUANTITY_VARIABLE' => '',
                                'PRODUCT_SUBSCRIPTION' => 'N',
                                'PROPERTY_CODE' => array(
                                    0 => '',
                                    1 => 'NEW',
                                    2 => 'SALE',
                                    3 => '',
                                ),
                                'SECTION_CODE' => $_REQUEST['SECTION_CODE'],
                                'SECTION_ID' => '',
                                'SECTION_ID_VARIABLE' => 'SECTION_ID',
                                'SECTION_URL' => '',
                                'SECTION_USER_FIELDS' => array(
                                    0 => '',
                                    1 => '',
                                ),
                                'SEF_MODE' => 'N',
                                'SET_BROWSER_TITLE' => 'Y',
                                'SET_LAST_MODIFIED' => 'N',
                                'SET_META_DESCRIPTION' => 'Y',
                                'SET_META_KEYWORDS' => 'Y',
                                'SET_STATUS_404' => 'N',
                                'SET_TITLE' => 'Y',
                                'SHOW_404' => 'N',
                                'SHOW_ALL_WO_SECTION' => 'Y',
                                'SHOW_CLOSE_POPUP' => 'N',
                                'SHOW_DISCOUNT_PERCENT' => 'N',
                                'SHOW_OLD_PRICE' => 'N',
                                'SHOW_PRICE_COUNT' => '1',
                                'TEMPLATE_THEME' => 'blue',
                                'USE_MAIN_ELEMENT_SECTION' => 'N',
                                'USE_PRICE_COUNT' => 'N',
                                'USE_PRODUCT_QUANTITY' => 'N',
                                'COMPONENT_TEMPLATE' => 'catalog_favorites'
                            ),
                            false
                        );
                        ?>
                        <?
                    } else {
                        ?>
                        <p>Нет избранных товаров</p>
                        <?
                    }
                    ?>
                </div>
                <div class="tab settings">
                    <?
                    $APPLICATION->IncludeComponent(
                        'swebs:personal.data',
                        '.default',
                        array(
                            'COMPONENT_TEMPLATE' => '.default'
                        ),
                        false
                    );
                    ?>
                </div>
                <div class="retail-rocket-block">
                    <div class="retail-rocket-block__item">
                        <div data-retailrocket-markup-block="5d5ce92697a52817280bcffa" data-stock-id="4"></div>
                    </div>
                    <div class="retail-rocket-block__item">
                        <div data-retailrocket-markup-block="5d5ce92d97a52817280bcffb" data-stock-id="4"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>