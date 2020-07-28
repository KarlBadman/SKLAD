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
$this->setFrameMode(true);
?>
<?
if (!empty($arResult['ITEMS'])):?>
    <? if ($_COOKIE['ajax_get_page'] == 'Y') $APPLICATION->RestartBuffer(); ?>
    <div data-name="catalog_section_list" data-product-impressions="container" data-product="container"
         <? if ($arResult['ID'] != \Dsklad\Config::getParam('section/discounts')): ?>data-category-id="<?= (!empty($arResult['ID'])) ? $arResult['ID'] : 0; ?>"<? endif;
        ?>>
        <div class="ds-catalog__list" data-name="catalog_section_item_box">
            <? foreach ($arResult['ITEMS'] as $item):?>
                <div class="ds-catalog-item" data-galist="item" data-item-name="<?= $item['NAME'] ?>"
                     data-item-id="<?= $item['ID'] ?>" data-item-price="<?= $item['TOTAL_PRICE'] ?>"
                     data-item-category="<?= $arResult['NAME'] ?>">
                    <div class="ds-catalog-item__header">
                        <div class="ds-catalog-item__status">
                            <? if ($item['PREORDER']):?>
                                <span class="pre-order">Предзаказ</span>
                            <? elseif ($item['SALE_PERCENT']):?>
                                <span class="discount">Скидка <?= $item['SALE_PERCENT'] ?>%</span>
                            <? elseif ($item['QUANTITY_FROM']):?>
                                <span class="cheaper">Дешевле от <?= $item['QUANTITY_FROM'] ?> шт.</span>
                            <? elseif (!empty($item['PROPERTIES']['NEW']['VALUE'])):?>
                                <span class="new">Новинка</span>
                            <? elseif (!empty($item['PROPERTIES']['HIT']['VALUE'])):?>
                                <span class="hurry-up">Хит</span>
                            <? elseif (!empty($item['PROPERTIES']['SALE']['VALUE'])):?>
                                <span class="pre-order">Распродажа</span>
                            <? endif; ?>
                        </div>
                        <div class="ds-catalog-item__favorite">
                            <button data-product-name="<?= $item['NAME'] ?>" data-product-id="<?= $item['ID'] ?>"
                                    class="icon-svg <? if ($item['FAVORITE']):?>ic-favorite_on<? else:?>ic-favorite_off<? endif; ?> js-add-to-favorite"></button>
                        </div>
                        <a class="ds-catalog-item__name" href="<?= $item['DETAIL_PAGE_URL'] ?>"><?= $item['NAME'] ?></a>
                        <div class="ds-catalog-item__vendor">Арт.:
                            <span><?= $item['PROPERTIES']['CML2_ARTICLE']['VALUE'] ?></span>
                        </div>
                    </div>
                    <a class="ds-catalog-item__img" href="<?= $item['DETAIL_PAGE_URL'] ?>">
                        <? if (!empty($item['PICTURE_ITEM'])):?>
                            <img src="<?= $item['PICTURE_ITEM'] ?>" alt="<?= $item['NAME'] ?>"/>
                        <? else:; ?>
                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/no-img.jpg" alt="<?= $item['NAME'] ?>"/>
                        <? endif; ?>
                    </a>
                    <div class="ds-catalog-item__footer">
                        <div class="ds-catalog-item__add-info">
                            <? if (!empty($item['PROPERTY_SELECT'])):?>
                                <span><?= $item['PROPERTY_SELECT']['NAME'] ?>: <?= $item['PROPERTY_SELECT']['QUANTITY'] ?></span>
                            <? endif; ?>
                        </div>
                        <div class="ds-catalog-item__price <? if ($item['RED']):?>price-discount<? endif ?>">
                            <? if ($item['FROM']):?>
                                <span>от </span>
                            <? endif; ?>
                            <span class="ds-price"><?= $item['TOTAL_PRICE'] ?></span>
                        </div>
                    </div>
                </div>
            <? endforeach; ?>

        </div>
        <div data-name="nav_box">
            <?= $arResult['NAV_STRING'] ?>
        </div>
    </div>
    <?
    if (!empty($_COOKIE['COUNT_ELEMENT'])) {
        unset($_COOKIE['COUNT_ELEMENT']);
        setcookie('COUNT_ELEMENT', null, -1, '/');
    }
    if ($_COOKIE['ajax_get_page'] == 'Y') {
        unset($_COOKIE['ajax_get_page']);
        setcookie('ajax_get_page', null, -1, '/');
        die();
    } ?>
<?endif;?>
<? if ($arResult['ID'] > 0): ?>
    <? if ($arResult['ID'] != \Dsklad\Config::getParam('section/discounts')): ?>
        <div class="retail-rocket-catalog">
            <div data-retailrocket-markup-block="5d5ce89197a52817280bcfec" data-category-id="<?= $arResult['ID'] ?>"
                 data-stock-id="4"></div>
        </div>
    <? else: ?>
        <div class="retail-rocket-catalog">
            <div data-retailrocket-markup-block="5d5ce91997a52817280bcff9" data-stock-id="4"></div>
        </div>
    <? endif ?>
<? endif ?>

<script>
    window.templateFloderCatalogSection = '<?=$templateFolder?>';
</script>
