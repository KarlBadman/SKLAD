<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);?>
<div class="ds-catalog">
    <div class="ds-catalog__list">
        <? foreach ($arResult['ITEMS'] as $item): ?>
        <div class="ds-catalog-item" data-galist="item" data-item-name="<?= $item['NAME'] ?>"
             data-item-id="<?= $item['ID'] ?>" data-item-price="<?= $item['TOTAL_PRICE'] ?>"
             data-item-category="<?= $arResult['NAME'] ?>">
            <div class="ds-catalog-item__header">
                <div class="ds-catalog-item__status">
                    <? if ($item['PREORDER']): ?>
                    <span class="pre-order">Предзаказ</span>
                    <? elseif ($item['SALE_PERCENT']): ?>
                    <span class="discount">Скидка <?= $item['SALE_PERCENT'] ?>%</span>
                    <? elseif ($item['QUANTITY_FROM']): ?>
                    <span class="cheaper">Дешевле от <?= $item['QUANTITY_FROM'] ?> шт.</span>
                    <? elseif (!empty($item['PROPERTIES']['NEW']['VALUE'])): ?>
                    <span class="new">Новинка</span>
                    <? elseif (!empty($item['PROPERTIES']['HIT']['VALUE'])): ?>
                    <span class="hurry-up">Хит</span>
                    <? elseif (!empty($item['PROPERTIES']['SALE']['VALUE'])): ?>
                    <span class="pre-order">Распродажа</span>
                    <? endif; ?>
                </div>

                <a class="ds-catalog-item__name"
                   href="<?= $item['DETAIL_PAGE_URL'] ?>"><?= $item['NAME'] ?></a>
            </div>
            <a class="ds-catalog-item__img" href="<?= $item['DETAIL_PAGE_URL'] ?>">
                <? if (!empty($item['PICTURE_ITEM'])): ?>
                <img src="<?= $item['PICTURE_ITEM'] ?>" alt="<?= $item['NAME'] ?>"/>
                <? else:; ?>
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/no-img.jpg" alt="<?= $item['NAME'] ?>"/>
                <? endif; ?>
            </a>
            <div class="ds-catalog-item__footer">

                <div class="ds-catalog-item__price <? if ($item['RED']): ?>price-discount<? endif ?>">
                    <? if ($item['FROM']): ?>
                    <span>от </span>
                    <? endif; ?>
                    <span class="ds-price"><?=number_format($item['MIN_PRICE'],0,'.',' ')?></span>
                </div>
            </div>
        </div>
        <? endforeach; ?>
    </div>
</div>
