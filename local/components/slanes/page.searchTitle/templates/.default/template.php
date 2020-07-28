<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<? use \Dsklad\Tools\Helpers; ?>
<? if (!empty($arResult['ITEMS'])) { ?>
    <h1>По запросу «<?= $_REQUEST['search'] ?>»
        найдено <?= count($arResult['ITEMS']) ?> <?= Helpers::wordProducts(count($arResult['ITEMS'])) ?>:</h1>

    <?
    $APPLICATION->IncludeComponent(
        'slanes:elements.searchTitle',
        '.default',
        array(
            'ELEMENTS' => $arResult
        ),
        false
    );?>
    <div class="retail-rocket-block">
        <div data-retailrocket-markup-block="5d5ce8b197a52817280bcfef" data-search-phrase="<?=$_REQUEST['search']?>" data-stock-id="4"></div>
    </div>
<?}else{?>
    <h1>Результаты поиска «<?= $_REQUEST['search'] ?>»</h1>
    <?
    if (!empty($_REQUEST['search'])): ?>
        <div class="search-empty-result">
            <h2>По вашему запросу ничего не найдено</h2>
        </div>
        <div class="retail-rocket-block">
            <div data-retailrocket-markup-block="5d5ce8bf97a52817280bcff0"
                 data-search-phrase="<?= $_REQUEST['search'] ?>" data-stock-id="4"></div>
        </div>
    <? else : ?>
        <h2>Пустой поисковой запрос. Ваш запрос не содержит символы.</h2>
    <?endif; ?>
<?}
?>


