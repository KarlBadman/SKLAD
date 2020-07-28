<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

$APPLICATION->AddViewContent('page_type', 'data-page-type="catalog-detail"');
?>
<div class="ds-wrapper">
    <? $APPLICATION->IncludeComponent(
        'bitrix:breadcrumb',
        'template',
        array(
            'PATH' => '',
            'SITE_ID' => 's1',
            'START_FROM' => '0',
            'COMPONENT_TEMPLATE' => 'template'
        ),
        false
    ); ?>
</div>

<div class="item__page el_detail" itemscope itemtype="http://schema.org/Product">
    <?
    /* MODIFIED SELECT 2 HIDE ON */
    $ElementID = $APPLICATION->IncludeComponent(
        'bitrix:catalog.element',
        'catalogDsklad',
        array(
            'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
            'ADD_DETAIL_TO_SLIDER' => $arParams['ADD_DETAIL_TO_SLIDER'],
            'ADD_ELEMENT_CHAIN' => $arParams['ADD_ELEMENT_CHAIN'],
            'ADD_PICT_PROP' => '-',
            'ADD_PROPERTIES_TO_BASKET' => $arParams['ADD_PROPERTIES_TO_BASKET'],
            'ADD_SECTIONS_CHAIN' => $arParams['ADD_SECTIONS_CHAIN'],
            'ADD_TO_BASKET_ACTION' => $arParams['DETAIL_ADD_TO_BASKET_ACTION'],
            'BACKGROUND_IMAGE' => $arParams['DETAIL_BACKGROUND_IMAGE'],
            'BASKET_URL' => $arParams['BASKET_URL'],
            'BRAND_USE' => $arParams['DETAIL_BRAND_USE'],
            'BROWSER_TITLE' => $arParams['DETAIL_BROWSER_TITLE'],
            'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
            'CACHE_TIME' => $arParams['CACHE_TIME'],
            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
            'CHECK_SECTION_ID_VARIABLE' => $arParams['DETAIL_CHECK_SECTION_ID_VARIABLE'],
            'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
            'DETAIL_PICTURE_MODE' => $arParams['DETAIL_DETAIL_PICTURE_MODE'],
            'DETAIL_URL' => '',
            'DISABLE_INIT_JS_IN_COMPONENT' => $arParams['DISABLE_INIT_JS_IN_COMPONENT'],
            'DISPLAY_COMPARE' => 'N',
            'DISPLAY_NAME' => $arParams['DETAIL_DISPLAY_NAME'],
            'DISPLAY_PREVIEW_TEXT_MODE' => $arParams['DETAIL_DISPLAY_PREVIEW_TEXT_MODE'],
            'ELEMENT_CODE' => $_REQUEST['ELEMENT_CODE'],
            'ELEMENT_ID' => '',
            'GIFTS_DETAIL_BLOCK_TITLE' => $arParams['GIFTS_DETAIL_BLOCK_TITLE'],
            'GIFTS_DETAIL_HIDE_BLOCK_TITLE' => $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'],
            'GIFTS_DETAIL_PAGE_ELEMENT_COUNT' => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
            'GIFTS_DETAIL_TEXT_LABEL_GIFT' => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],
            'GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE' => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'],
            'GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE' => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE'],
            'GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT' => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
            'GIFTS_MESS_BTN_BUY' => $arParams['GIFTS_MESS_BTN_BUY'],
            'GIFTS_SHOW_DISCOUNT_PERCENT' => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
            'GIFTS_SHOW_IMAGE' => $arParams['GIFTS_SHOW_IMAGE'],
            'GIFTS_SHOW_NAME' => $arParams['GIFTS_SHOW_NAME'],
            'GIFTS_SHOW_OLD_PRICE' => $arParams['GIFTS_SHOW_OLD_PRICE'],
            'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
            'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
            'LABEL_PROP' => '-',
            'LINK_ELEMENTS_URL' => $arParams['LINK_ELEMENTS_URL'],
            'LINK_IBLOCK_ID' => $arParams['LINK_IBLOCK_ID'],
            'LINK_IBLOCK_TYPE' => $arParams['LINK_IBLOCK_TYPE'],
            'LINK_PROPERTY_SID' => $arParams['LINK_PROPERTY_SID'],
            'MESSAGE_404' => $arParams['MESSAGE_404'],
            'MESS_BTN_ADD_TO_BASKET' => $arParams['MESSAGE_404'],
            'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
            'MESS_BTN_COMPARE' => $arParams['MESS_BTN_COMPARE'],
            'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
            'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
            'META_DESCRIPTION' => '-',
            'META_KEYWORDS' => '-',
            'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],
            'OFFERS_FIELD_CODE' => $arParams['DETAIL_OFFERS_FIELD_CODE'],
            'OFFERS_LIMIT' => '0',
            'OFFERS_PROPERTY_CODE' => $arParams['DETAIL_OFFERS_PROPERTY_CODE'],
            'OFFERS_SORT_FIELD' => $arParams['OFFERS_SORT_FIELD'],
            'OFFERS_SORT_FIELD2' => $arParams['OFFERS_SORT_FIELD2'],
            'OFFERS_SORT_ORDER' => $arParams['OFFERS_SORT_ORDER'],
            'OFFERS_SORT_ORDER2' => $arParams['OFFERS_SORT_ORDER2'],
            'OFFER_ADD_PICT_PROP' => '-',
            'OFFER_TREE_PROPS' => '',
            'PARTIAL_PRODUCT_PROPERTIES' => $arParams['PARTIAL_PRODUCT_PROPERTIES'],
            'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
            'PRICE_VAT_SHOW_VALUE' => $arParams['PRICE_VAT_SHOW_VALUE'],
            'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
            'PRODUCT_PROPERTIES' => $arParams['PRODUCT_PROPERTIES'],
            'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
            'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
            'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
            'PROPERTY_CODE' => $arParams['DETAIL_PROPERTY_CODE'],
            'SECTION_CODE' => $_REQUEST['SECTION_CODE'],
            'SECTION_ID' => '',
            'SECTION_ID_VARIABLE' => $arParams['SECTION_ID_VARIABLE'],
            'SECTION_URL' => '',
            'SEF_MODE' => $arParams['SEF_MODE'],
            'SET_BROWSER_TITLE' => 'Y',
            'SET_CANONICAL_URL' => $arParams['DETAIL_SET_CANONICAL_URL'],
            'SET_LAST_MODIFIED' => $arParams['SET_LAST_MODIFIED'],
            'SET_META_DESCRIPTION' => 'Y',
            'SET_META_KEYWORDS' => 'Y',
            'SET_STATUS_404' => $arParams['SET_STATUS_404'],
            'SET_TITLE' => $arParams['SET_TITLE'],
            'SET_VIEWED_IN_COMPONENT' => $arParams['DETAIL_SET_VIEWED_IN_COMPONENT'],
            'SHOW_404' => $arParams['SHOW_404'],
            'SHOW_CLOSE_POPUP' => $arParams['COMMON_SHOW_CLOSE_POPUP'],
            'SHOW_DEACTIVATED' => $arParams['SHOW_DEACTIVATED'],
            'SHOW_DISCOUNT_PERCENT' =>  $arParams['SHOW_DISCOUNT_PERCENT'],
            'SHOW_MAX_QUANTITY' =>  $arParams['SHOW_MAX_QUANTITY'],
            'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
            'SHOW_PRICE_COUNT' =>  $arParams['SHOW_PRICE_COUNT'],
            'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
            'USE_COMMENTS' => $arParams['DETAIL_USE_COMMENTS'],
            'USE_ELEMENT_COUNTER' => $arParams['USE_ELEMENT_COUNTER'],
            'USE_GIFTS_DETAIL' => $arParams['USE_GIFTS_DETAIL'],
            'USE_GIFTS_MAIN_PR_SECTION_LIST' => $arParams['USE_GIFTS_MAIN_PR_SECTION_LIST'],
            'USE_MAIN_ELEMENT_SECTION' => $arParams['USE_MAIN_ELEMENT_SECTION'],
            'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
            'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
            'USE_VOTE_RATING' => $arParams['DETAIL_USE_VOTE_RATING'],
            'HIDE_NOT_AVAILABLE_OFFERS' => $arParams['HIDE_NOT_AVAILABLE_OFFERS'],
            'STRICT_SECTION_CHECK' => $arParams['DETAIL_STRICT_SECTION_CHECK'],
            'COMPATIBLE_MODE' => $arParams['COMPATIBLE_MODE'],
            'COMPOSITE_FRAME_MODE' => $arParams['COMPOSITE_FRAME_MODE'],
            'COMPOSITE_FRAME_TYPE' => $arParams['COMPOSITE_FRAME_TYPE'],
            'OFFERS_SELECT_PROPERTY'=>$arParams['OFFERS_SELECT_PROPERTY'],
            'OFFERS_PROPERTY_TYPE_IMAGES_LINK'=>$arParams['OFFERS_PROPERTY_TYPE_IMAGES_LINK'],
            'BALANCE_ON_STOCK'=>$arParams['BALANCE_ON_STOCK'],
            'PRICE_CODE'=>$arParams['PRICE_CODE'],
            'OFFERS_PROPERTY_TYPE_SM'=>$arParams['OFFERS_PROPERTY_TYPE_SM'],
        ),
        false
    ); ?>

<? global $USER ?>
<? if (!$USER->IsAuthorized()): ?>
    <div class="hide" data-name="phone_modal" id="phone_modal">
        <div class="ds-modal__body">
            <? $test = $APPLICATION->IncludeComponent(
                'dsklad:sale.confirm.phone',
                '',
                array(
                    'PAYMENTS_SELECTOR' => '.payment .options label',  //css-селектор кнопок выбора способа оплаты
                    'PAYMENT_CONFIRM_SELECTOR' => '.payconfirm',  //css-селектор кнопки способа оплаты, при котором нужно подтверждать телефон
                    'PHONE_INPUT_SELECTOR' => "input[data-name='is_phone']",  //css-селектор поля с номером телефона
                    'WAIT_TIME' => \Dsklad\Config::getOption('UF_CONF_PHONE_TIME'),  //время до повторной отправки
                    'LENGTH' => \Dsklad\Config::getOption('UF_CONF_PHONE_LENGTH'),  //длина кода
                    'RELOAD' => 'N', // Перегружать страницу после авторизации,
                    'NO_CONFORM_CODE' => \Dsklad\Config::getOption('UF_NO_CONFORM_CODE'), // коды телефонов для которых не нужно подтверждения
                ),
                false
            ); ?>
        </div>
    </div>
<? endif; ?>

<? //Добавляем цвет и цену в заголовок.
$old_title = $APPLICATION->GetPageProperty('title');
$repl = $APPLICATION->GetTitle();
$new_title = str_replace(array('PRICE', $repl), array(str_replace(' руб.', '', $GLOBALS['PRICE']), $repl.' '.mb_strtolower($GLOBALS['variant_name'])), $old_title);
$APPLICATION->SetPageProperty('title', $new_title);

$old_description = strip_tags (html_entity_decode($APPLICATION->GetPageProperty('description')));
$new_description = preg_replace('/\s+/', ' ', str_replace(array("\r\n", "\r", "\n"), " ", $old_description));
$new_description = trim($GLOBALS['PRODUCT_NAME'] . ' ' . mb_strtolower($GLOBALS['variant_name'])) . '. ' . $new_description;
$new_description = mb_substr($new_description, 0, 370);
$new_description = trim($new_description);
$APPLICATION->SetPageProperty("description",  $new_description);