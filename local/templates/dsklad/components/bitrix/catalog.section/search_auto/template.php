<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
    <h2>Возможно, вы ищете</h2>
    <div class="list">
        <? foreach ($arResult['ITEMS'] as $arItem): ?>
		<?if($arItem['IBLOCK_SECTION_ID'] !== "157"){?> 
            <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="item">
                <span class="row">
                    <span class="image">
                        <? if (!empty($arItem['IMG_SMALL']['src'])): ?>
                            <img src="<?= $arItem['IMG_SMALL']['src'] ?>" alt="<?= $arItem['NAME'] ?>">
                        <? else: ?>
                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/no_photo.jpg" width="66"
                                 alt="<?= $arItem['NAME'] ?>">
                        <? endif; ?>
                    </span>
                    <span class="info">
                        <span class="contents">
                            <span class="title">
                                <span class="category"><?= $arItem['SECTION_NAME_PARENT'] ?></span>
                                <span class="name"><?= $arItem['NAME'] ?></span>
                            </span>
                            <span class="stickers__widget type-inline">
                                <? if (!empty($arItem['PROPERTIES']['SALE']['VALUE'])): ?>
                                    <span class="sale"></span>
                                <? endif; ?>
                                <? if (!empty($arItem['PROPERTIES']['NEW']['VALUE'])): ?>
                                    <span class="new"></span>
                                <? endif; ?>
                            </span>
                        </span>
                    </span>
                    <?/*?>
                    <span class="sales">
                        <span class="sale__widget">10%</span>
                    </span>
                    <?*/?>
                    <span class="price"><?= $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE'] ?>.–</span>
                </span>
            </a>
<!--            <pre>-->

<!--                    --><?// foreach ($arItem['OFFERS'] as $arOffer): ?>
<!--                        --><?// if ($arOffer['ID'] == 14608): ?>
<!--                            --><?// print_r($arOffer['PRICES']) ?>
<!--                        --><?// endif ?>
<!--                    --><?// endforeach ?>
<!--                    --><?// print_r($arItem['ALL_PRICES']) ?>
<!--                    </pre>-->
<?}?>
        <? endforeach; ?>
    </div>
<? else: ?>
    <div class="empty auto_search_empty">
        <p>
            <span class="icon__search">
                      <svg>
                          <use xmlns:xlink="http://www.w3.org/1999/xlink"
                               xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#search"></use>
                      </svg>
            </span>
        </p>
        <p>К сожалению, у нас нет <br>товара с таким названием </p>

        <p>Посмотрите наш <a href="/catalog/stulya/">каталог</a>, <br>там может быть то, что <br>вы ищете</p>
    </div>
<? endif; ?>
