<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Context;

$obRequest = Context::getCurrent()->getRequest();
$strSearch = $obRequest->get('search');

$arViewed = array();
$basketUserId = (int)CSaleBasket::GetBasketUserID(false);
if ($basketUserId > 0) {
    $arViewed = \Bitrix\Catalog\CatalogViewedProductTable::getRow([
        'order' => [
            'DATE_VISIT' => 'DESC'
        ],
        'filter' => [
            '=FUSER_ID' => $basketUserId,
            '=SITE_ID' => SITE_ID
        ],
        'select' => [
            'ELEMENT_ID'
        ],
    ]);
}

$lastViewedId = CIBlockElement::GetByID($arViewed['ELEMENT_ID'])->fetch();
$lastViewedId = ($lastViewedId['ACTIVE'] == 'Y') ? $arViewed['ELEMENT_ID'] : null;
$GLOBALS['arLastViewed'] = array(
    'ID' => $lastViewedId
);

$dbResRelated = \CIBlockElement::GetProperty(
    \Dsklad\Config::getParam('iblock/catalog'),
    $lastViewedId,
    [],
    ['CODE' => 'RELATED']
);
$arrRelated = [];
while ($arTmpRelated = $dbResRelated->fetch()) {
    if (!empty($arTmpRelated['VALUE']) && $arTmpRelated['VALUE'] != $lastViewedId) {
        $arrRelated[] = $arTmpRelated['VALUE'];
    }
}

$exclude = array('!ID' => $lastViewedId);
if (count($arrRelated) > 1) {
    $GLOBALS['arRelated'] = array(
            "LOGIC" => "AND",
        array('ID' => $arrRelated),
        $exclude
    );
} else {
    $GLOBALS['arRelated'] = $exclude;
}

global $bPreorderFormExist;
$bPreorderFormExist = true;
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/ajax/preorder.php');

?>
<section class="recommend__widget catalog">
    <div class="heading" <?if(!empty($arParams['STYLE_ZAG'])):?>style="<?=$arParams['STYLE_ZAG'];?>"<?endif;?>>
        <?if(!empty($arParams['TITLE'])):?>
            <?if (!empty($strSearch)) : ?>
                <h2>По вашему запросу ничего не найдено </h2>
                <div data-retailrocket-markup-block="5d5ce8bf97a52817280bcff0" data-search-phrase="<?=$arParams['SEARCH'];?>" data-stock-id="4"></div>
                <h2>Рекомендуем обратить внимание на:</h2>
            <?else : ?>
                <h2><?=$arParams['FOR_EMPTY_TITLE']?></h2>
            <?endif;?>
        <?else:;?>
            <h2>Рекомендуем также</h2>
        <?endif;?>
    </div>
    <section class="catalog__widget items list index_recommend_last_viewed" data-product-impressions="container" data-product="container" >
        <?
        $APPLICATION->IncludeComponent(
            'bitrix:catalog.section',
            'index_recommend_last_viewed',
            array(
                'ACTION_VARIABLE' => 'action',
                'ADD_PICT_PROP' => '-',
                'ADD_PROPERTIES_TO_BASKET' => 'Y',
                'ADD_SECTIONS_CHAIN' => 'N',
                'ADD_TO_BASKET_ACTION' => 'ADD',
                'AJAX_MODE' => 'Y',
                'AJAX_OPTION_ADDITIONAL' => '',
                'AJAX_OPTION_HISTORY' => 'N',
                'AJAX_OPTION_JUMP' => 'N',
                'AJAX_OPTION_STYLE' => 'Y',
                'BACKGROUND_IMAGE' => '-',
                'BASKET_URL' => '/personal/basket.php',
                'BROWSER_TITLE' => '-',
                'CACHE_FILTER' => 'N',
                'CACHE_GROUPS' => 'Y',
                'CACHE_TIME' => '3600',
                'CACHE_TYPE' => 'A',
                'CONVERT_CURRENCY' => 'N',
                'DETAIL_URL' => '',
                'DISABLE_INIT_JS_IN_COMPONENT' => 'N',
                'DISPLAY_BOTTOM_PAGER' => 'N',
                'DISPLAY_TOP_PAGER' => 'N',
                'ELEMENT_SORT_FIELD' => 'sort',
                'ELEMENT_SORT_FIELD2' => 'id',
                'ELEMENT_SORT_ORDER' => 'asc',
                'ELEMENT_SORT_ORDER2' => 'desc',
                'FILTER_NAME' => 'arLastViewed',
                'HIDE_NOT_AVAILABLE' => 'N',
                'IBLOCK_ID' => '35',
                'IBLOCK_TYPE' => '1c_catalog',
                'INCLUDE_SUBSECTIONS' => 'Y',
                'LABEL_PROP' => '-',
                'LINE_ELEMENT_COUNT' => '1',
                'MESSAGE_404' => '',
                'MESS_BTN_ADD_TO_BASKET' => 'В корзину',
                'MESS_BTN_BUY' => 'Купить',
                'MESS_BTN_DETAIL' => 'Подробнее',
                'MESS_BTN_SUBSCRIBE' => 'Подписаться',
                'MESS_NOT_AVAILABLE' => 'Нет в наличии',
                'META_DESCRIPTION' => '-',
                'META_KEYWORDS' => '-',
                'OFFERS_CART_PROPERTIES' => array(
                ),
                'OFFERS_FIELD_CODE' => array(
                    0 => '',
                    1 => '',
                ),
                'OFFERS_LIMIT' => '0',
                'OFFERS_PROPERTY_CODE' => array(
                    0 => 'ARRIVAL_DATE',
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
                'PAGER_TEMPLATE' => 'add_main_recom',
                'PAGER_TITLE' => '/include_areas/index_recomm.php',
                'PAGE_ELEMENT_COUNT' => '1',
                'PARTIAL_PRODUCT_PROPERTIES' => 'N',
                'PRICE_VAT_INCLUDE' => 'Y',
                'PRODUCT_DISPLAY_MODE' => 'N',
                'PRODUCT_ID_VARIABLE' => 'id',
                'PRODUCT_PROPERTIES' => array(
                ),
                'PRODUCT_PROPS_VARIABLE' => 'prop',
                'PRODUCT_QUANTITY_VARIABLE' => '',
                'PRODUCT_SUBSCRIPTION' => 'N',
                'PROPERTY_CODE' => array(
                    0 => 'ARRIVAL_DATE',
                    1 => 'NEW',
                    2 => 'SALE',
                    3 => '',
                ),
                'SECTION_CODE' => '',
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
                'USE_PRODUCT_QUANTITY' => 'N'
            ),
            false
        );
        ?>
        <?
        $APPLICATION->IncludeComponent(
            'bitrix:catalog.section',
            'index_recommend',
            array(
                'ACTION_VARIABLE' => 'action',
                'ADD_PICT_PROP' => '-',
                'ADD_PROPERTIES_TO_BASKET' => 'Y',
                'ADD_SECTIONS_CHAIN' => 'N',
                'ADD_TO_BASKET_ACTION' => 'ADD',
                'AJAX_MODE' => 'Y',
                'AJAX_OPTION_ADDITIONAL' => '',
                'AJAX_OPTION_HISTORY' => 'N',
                'AJAX_OPTION_JUMP' => 'N',
                'AJAX_OPTION_STYLE' => 'Y',
                'BACKGROUND_IMAGE' => '-',
                'BASKET_URL' => '/personal/basket.php',
                'BROWSER_TITLE' => '-',
                'CACHE_FILTER' => 'N',
                'CACHE_GROUPS' => 'Y',
                'CACHE_TIME' => '3600',
                'CACHE_TYPE' => 'A',
                'CONVERT_CURRENCY' => 'N',
                'DETAIL_URL' => '',
                'DISABLE_INIT_JS_IN_COMPONENT' => 'N',
                'DISPLAY_BOTTOM_PAGER' => 'N',
                'DISPLAY_TOP_PAGER' => 'N',
                'ELEMENT_SORT_FIELD' => 'sort',
                'ELEMENT_SORT_FIELD2' => 'id',
                'ELEMENT_SORT_ORDER' => 'asc',
                'ELEMENT_SORT_ORDER2' => 'desc',
                'FILTER_NAME' => 'arRelated',
                'HIDE_NOT_AVAILABLE' => 'N',
                'IBLOCK_ID' => '35',
                'IBLOCK_TYPE' => '1c_catalog',
                'INCLUDE_SUBSECTIONS' => 'Y',
                'LABEL_PROP' => '-',
                'LINE_ELEMENT_COUNT' => '8',
                'MESSAGE_404' => '',
                'MESS_BTN_ADD_TO_BASKET' => 'В корзину',
                'MESS_BTN_BUY' => 'Купить',
                'MESS_BTN_DETAIL' => 'Подробнее',
                'MESS_BTN_SUBSCRIBE' => 'Подписаться',
                'MESS_NOT_AVAILABLE' => 'Нет в наличии',
                'META_DESCRIPTION' => '-',
                'META_KEYWORDS' => '-',
                'OFFERS_CART_PROPERTIES' => array(
                ),
                'OFFERS_FIELD_CODE' => array(
                    0 => '',
                    1 => '',
                ),
                'OFFERS_LIMIT' => '0',
                'OFFERS_PROPERTY_CODE' => array(
                    1 => 'ARRIVAL_DATE',
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
                'PAGER_TEMPLATE' => 'add_main_recom',
                'PAGER_TITLE' => '/include_areas/index_recomm.php',
                'PAGE_ELEMENT_COUNT' => '5',
                'PARTIAL_PRODUCT_PROPERTIES' => 'N',
                'PRICE_VAT_INCLUDE' => 'Y',
                'PRODUCT_DISPLAY_MODE' => 'N',
                'PRODUCT_ID_VARIABLE' => 'id',
                'PRODUCT_PROPERTIES' => array(
                ),
                'PRODUCT_PROPS_VARIABLE' => 'prop',
                'PRODUCT_QUANTITY_VARIABLE' => '',
                'PRODUCT_SUBSCRIPTION' => 'N',
                'PROPERTY_CODE' => array(
                    0 => 'ARRIVAL_DATE',
                    1 => 'NEW',
                    2 => 'SALE',
                    3 => '',
                ),
                'SECTION_CODE' => '',
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
                'USE_PRODUCT_QUANTITY' => 'N'
            ),
            false
        );
        ?>
    </section>
</section>

<script>
    $(function(){
        $('.catalog').on('change', '.js-select-offer', function(){
            var variant = $(this),
                form = variant.closest('form'),
                buybtn = form.find('a.to_basket'),
                prebtn = form.find('a.to_preorder'),
                prebtnfrm = form.find('a.to_preorder_form');

            buybtn.attr('href', buybtn.data('href-tmp').replace('#ID#',variant.val()));

            if (variant.data('count') <= 0) {
                buybtn.addClass('hidden');
                prebtn.removeClass('hidden');
                prebtnfrm.addClass('hidden');
            } else {
                buybtn.removeClass('hidden');
                prebtn.addClass('hidden');
                prebtnfrm.addClass('hidden');
            }

            $('#preorder-good-id').val(variant.val());
        });

        $('.js-select-offer:checked').each(function(){
            $(this).change();
        });
    });

    //оформлен предзаказ
    $('#preorder-form').on('submit', function(e){
        e.preventDefault();
        var form = $(this);
        $.post(
            '/ajax/preorder.php',
            form.serialize(),
            function(data){
                $('.success_popup_send_txt').removeClass('active');
                $('.error_popup_send_txt').removeClass('active');
                try {
                    ym(26291919, 'reachGoal', 'preorder');
                    ga('send', 'event', 'pre_order', 'preorder');
                } catch(e){}
                try {
                    if (data.status) {
                        purepopup.closePopup();
                        form[0].reset();
                        setTimeout(function () {
                            $('.success_popup_send_txt').find('.desc_popup_send_txt').find('span').html('Благодарим за заявку. Ждите звонка!');
                            $('.success_popup_send_txt').addClass('active');
                        }, 1000);

                        setTimeout(function () {
                            $('.success_popup_send_txt').removeClass('active');
                        }, 6000);
                    } else {

                        setTimeout(function () {
                            $('.error_popup_send_txt').find('.desc_popup_send_txt').find('span').html('Ошибка сохранения данных!');
                            $('.error_popup_send_txt').addClass('active');
                        }, 1000);

                        setTimeout(function () {
                            $('.error_popup_send_txt').removeClass('active');
                        }, 6000);
                    }
                } catch(e){}
            },
            'json'
        );
    });
</script>
