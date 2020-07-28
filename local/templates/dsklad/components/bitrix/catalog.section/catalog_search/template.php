<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
<?/* vk pixel temporary removed 01.11.18
$ids='[';
*/?>
<div class="default">
    <h2 class="hidden-gt-s"><?= $arResult['NAME'] ?></h2>
    <? if (!empty($arResult['ITEMS_PERCENT'])): ?>
        <section class="catalog">
            <div class="subheading">Товары со скидкой</div>
            <div class="catalog__widget">
                <? foreach ($arResult['ITEMS_PERCENT'] as $intKey => $arElement): ?>
    <?/* vk pixel temporary removed 01.11.18
if($ids!='[') $ids.=",";
$ids.='{"id":"'.$arElement['ID'].'"}';*/?>
                    <div class="item<?= $arElement['CONTAINER_CLASS'] ?>">
                        <div class="item-wrap">
                            <div class="image">
                                <a href="<?= $arElement['DETAIL_PAGE_URL'] ?>" class="link"> </a>
                            <span class="photos">
                                    <img src="<?= $arElement['PREVIEW_PICTURE']['SRC'] ?>"
                                         alt="<?= $arElement['PREVIEW_PICTURE']['ALT'] ?>" class="active"/>
                                <span class="prev">
                                        <span class="icon__larr2">
                                            <svg>
                                                <use
                                                    xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#larr2"></use>
                                            </svg>
                                        </span>
                                    </span>
                                    <span class="next">
                                        <span class="icon__rarr2">
                                            <svg>
                                                <use
                                                    xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#rarr2"></use>
                                            </svg>
                                        </span>
                                    </span>
                                </span>
                            <span class="stickers__widget type-block">
                                    <? if ($arElement['DISPLAY_PROPERTIES']['SALE']['VALUE_ENUM_ID'] == 1211): ?>
                                        <span class="sale"></span>
                                    <? endif ?>
                                <? if ($arElement['DISPLAY_PROPERTIES']['NEW']['VALUE_ENUM_ID'] == 1210): ?>
                                    <span class="new"></span>
                                <? endif ?>
                                </span>
                            </div>
                            <div class="info">
                                <a href="<?= $arResult['SECTION_PAGE_URL'] ?>"
                                   class="category"><?= $arResult['NAME'] ?></a><br/>
                                <a href="<?= $arElement['DETAIL_PAGE_URL'] ?>"
                                   class="title"><?= $arElement['NAME'] ?></a>

                                <div class="price">
                                    <? if (!empty($arElement['CONTAINER_CLASS'])): ?>
                                        <span class="from">от </span>
                                    <? endif ?>
                                    <span class="pricetag"><?= $arElement['MIN_PRICE']['PRINT_DISCOUNT_VALUE'] ?>
                                        .–</span>
                                    <? if (!empty($arElement['MIN_PRICE']['DISCOUNT_PERCENT'])): ?>
                                        <span class="sale__widget">%</span>
                                    <? endif ?>
                                </div>
                            </div>
                            <form action="#" novalidate="novalidate" method="post">
                                <? if (empty($arElement['OFFERS'])): ?>
                                    <input type="hidden" name="product_id" value="<?= $arElement['ID'] ?>"
                                           data-item-id="data-item-id"/>
                                <? elseif ($arElement['INT_COUNT_OFFERS']): ?>
                                    <input type="hidden" name="product_id" value="<?= $arElement['OFFERS'][0]['ID'] ?>"
                                           data-item-id="data-item-id"/>
                                <? else: ?>
                                    <div class="variants">
                                        <div class="fader left">
                                            <a href="" data-direction="-1">
                                        <span class="icon__larr2">
                                            <svg>
                                                <use
                                                    xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#larr2"></use>
                                            </svg>
                                        </span>
                                            </a>
                                        </div>
                                        <div class="fader right">
                                            <a href="" data-direction="1">
                                        <span class="icon__rarr2">
                                            <svg>
                                                <use
                                                    xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#rarr2"></use>
                                            </svg>
                                        </span>
                                            </a>
                                        </div>
                                        <div class="scrollable">
                                            <div class="list">
                                                <? foreach ($arElement['OFFERS'] as $key => $arOffer): ?>
                                                    <label>
                                                        <input type="radio" name="product_id"
                                                               value="<?= $arOffer['ID'] ?>"
                                                               autocomplete="off"
                                                               <? if ($key == 0): ?>checked="checked"<? endif; ?>
                                                            />
                                                        <u>
                                                    <span class="icon__check">
                                                        <svg>
                                                            <use
                                                                xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use>
                                                        </svg>
                                                    </span>
                                                        </u>
                                                        <img
                                                            src="<?= $arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['IMG']['SRC'] ?>"
                                                            alt=""/>
                                                    </label>
                                                <? endforeach ?>
                                            </div>
                                        </div>
                                    </div>
                                <? endif ?>
                                <button class="button type-blue fill size-41">В корзину</button>
                            </form>
                        </div>
                    </div>
                <? endforeach ?>
                <? $cnt = 4-(count($arResult['ITEMS_PERCENT']) % 4);
                   for ($i = 0; $i < $cnt; $i++): ?>
                    <div class="item"></div>
                <? endfor;?>
            </div>
        </section>
    <? endif; ?>

    <? if (!empty($arResult['ITEMS_NO_PERCENT'])): ?>
        <section class="catalog">
            <div class="subheading">Все предложения</div>
            <div class="catalog__widget">
                <? foreach ($arResult['ITEMS'] as $intKey => $arElement): ?>
                    <div class="item<?= $arElement['CONTAINER_CLASS'] ?>">
                        <div class="item-wrap">
                            <div class="image">
                                <a href="<?= $arElement['DETAIL_PAGE_URL'] ?>" class="link"> </a>
                            <span class="photos">
                                    <img src="<?= $arElement['PREVIEW_PICTURE']['SRC'] ?>"
                                         alt="<?= $arElement['PREVIEW_PICTURE']['ALT'] ?>" class="active"/>
                                <span class="prev">
                                        <span class="icon__larr2">
                                            <svg>
                                                <use
                                                    xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#larr2"></use>
                                            </svg>
                                        </span>
                                    </span>
                                    <span class="next">
                                        <span class="icon__rarr2">
                                            <svg>
                                                <use
                                                    xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#rarr2"></use>
                                            </svg>
                                        </span>
                                    </span>
                                </span>
                            <span class="stickers__widget type-block">
                                    <? if ($arElement['DISPLAY_PROPERTIES']['SALE']['VALUE_ENUM_ID'] == 1211): ?>
                                        <span class="sale"></span>
                                    <? endif ?>
                                <? if ($arElement['DISPLAY_PROPERTIES']['NEW']['VALUE_ENUM_ID'] == 1210): ?>
                                    <span class="new"></span>
                                <? endif ?>
                                </span>
                            </div>
                            <div class="info">
                                <a href="<?= $arResult['SECTION_PAGE_URL'] ?>"
                                   class="category"><?= $arResult['NAME'] ?></a><br/>
                                <a href="<?= $arElement['DETAIL_PAGE_URL'] ?>"
                                   class="title"><?= $arElement['NAME'] ?></a>

                                <div class="price">
                                    <? if (!empty($arElement['CONTAINER_CLASS'])): ?>
                                        <span class="from">от </span>
                                    <? endif ?>
                                    <span class="pricetag"><?= $arElement['MIN_PRICE']['PRINT_DISCOUNT_VALUE'] ?>
                                        .–</span>
                                    <? if (!empty($arElement['MIN_PRICE']['DISCOUNT_PERCENT'])): ?>
                                        <span class="sale__widget"><?= $arElement['MIN_PRICE']['DISCOUNT_PERCENT'] ?>
                                            %</span>
                                    <? endif ?>
                                </div>
                            </div>
                            <form action="#" novalidate="novalidate" method="post">
                                <? if (empty($arElement['OFFERS'])): ?>
                                    <input type="hidden" name="product_id" value="<?= $arElement['ID'] ?>"
                                           data-item-id="data-item-id"/>
                                <? elseif ($arElement['INT_COUNT_OFFERS']): ?>
                                    <input type="hidden" name="product_id" value="<?= $arElement['OFFERS'][0]['ID'] ?>"
                                           data-item-id="data-item-id"/>
                                <? else: ?>
                                    <div class="variants">
                                        <div class="fader left">
                                            <a href="" data-direction="-1">
                                        <span class="icon__larr2">
                                            <svg>
                                                <use
                                                    xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#larr2"></use>
                                            </svg>
                                        </span>
                                            </a>
                                        </div>
                                        <div class="fader right">
                                            <a href="" data-direction="1">
                                        <span class="icon__rarr2">
                                            <svg>
                                                <use
                                                    xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#rarr2"></use>
                                            </svg>
                                        </span>
                                            </a>
                                        </div>
                                        <div class="scrollable">
                                            <div class="list">
                                                <? foreach ($arElement['OFFERS'] as $key => $arOffer): ?>
                                                    <label>
                                                        <input type="radio" name="product_id"
                                                               value="<?= $arOffer['ID'] ?>"
                                                               autocomplete="off"
                                                               <? if ($key == 0): ?>checked="checked"<? endif; ?>
                                                            />
                                                        <u>
                                                    <span class="icon__check">
                                                        <svg>
                                                            <use
                                                                xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use>
                                                        </svg>
                                                    </span>
                                                        </u>
                                                        <img
                                                            src="<?= $arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['IMG']['SRC'] ?>"
                                                            alt=""/>
                                                    </label>
                                                <? endforeach ?>
                                            </div>
                                        </div>
                                    </div>
                                <? endif ?>
                                <button class="button type-blue fill size-41">В корзину</button>
                            </form>
                        </div>
                    </div>
                <? endforeach ?>
                <? $cnt = 4-(count($arResult['ITEMS']) % 4);
                for ($i = 0; $i < $cnt; $i++): ?>
                    <div class="item"></div>
                <? endfor;?>
            </div>
        </section>
    <? endif; ?>
    <?= $arResult['NAV_STRING'] ?>
</div>
<?/* vk pixel temporary removed 01.11.18
$ids.="]"; ?>
<script>
$(document).ready(function(){
	var params={"products":<? echo $ids?>};
	VK.Retargeting.ProductEvent(<?=VK_PRICE_LIST_ID?>, 'view_search',params);
	
});
</script>
*/?>