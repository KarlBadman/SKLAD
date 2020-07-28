<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->AddViewContent('page_type', 'data-page-type="search-page"');

use Bitrix\Main\Context;

$obRequest = Context::getCurrent()->getRequest();
$strSearch = $obRequest->get('search');
?>
<div class="ds-wrapper">
        <?
        $APPLICATION->IncludeComponent(
            'bitrix:breadcrumb',
            'template',
            Array(
                'COMPONENT_TEMPLATE' => 'template',
                'PATH' => '',
                'SITE_ID' => 's1',
                'START_FROM' => '0'
            )
        );
        ?>
        <?
        if (!empty($strSearch)) {
            ?>
                <?
                $APPLICATION->SetPageProperty('title', 'Результаты поиска “'.$strSearch.'”');
                ?>
            <?
        } else {
            ?>
                <h1>Поиск</h1>
                <?
                $APPLICATION->SetPageProperty('title', 'Поиск');
                ?>
            <?
        }
        ?>

    <?
    /* MODIFIED SELECT 2 HIDE ON */
    $APPLICATION->IncludeComponent(
        'slanes:page.searchTitle',
        '.default',
        array(
            'CATEGORY_0' => array(
                0 => 'iblock_1c_catalog',
            ),
            'CATEGORY_0_TITLE' => '',
            'CATEGORY_0_iblock_1c_catalog' => array(
                1 => \Dsklad\Config::getParam('iblock/offers'),
            ),
            'CHECK_DATES' => 'Y',
            'COMPOSITE_FRAME_MODE' => 'A',
            'INPUT_ID' => 'title-search-input',
            'NUM_CATEGORIES' => '1',
            'ORDER' => 'date',
            'PRICE_CODE' => array(
                0 => 'Базовая цена продажи',
                1 => 'Основная цена продажи',
            ),
            'TOP_COUNT' => '100',
            'USE_LANGUAGE_GUESS' => 'Y',
            "CACHE_TIME" => "36000000",
            "CACHE_TYPE" => $GLOBALS['COMPONENT_CACHE'],
            "CACHE_KEY"=>"search_test",
        ),
        false
    );
    ?>
</div>
<??>
<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>