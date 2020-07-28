<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);
?>
<section class="catalog">
    <div class="default">
        <h2 class="hidden-gt-s"><?= $arResult['NAME'] ?></h2>
        <div class="catalog__widget">
            <?
            foreach ($arResult['ITEMS'] as $intKey => $arElement) {
                ?>
                <div class="item<?= $arElement['CONTAINER_CLASS'] ?>" data-item-id="<?= $arElement['ID']?>">
                    <div class="item-wrap">
                        <div class="image">
                            <a data-remove-favorites="<?= $arElement['ID'] ?>" class="remove link_remove_favorites">
                                <span class="icon__cross2">
                                    <svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cross2"></use></svg>
                                </span>
                            </a>
                            <a href="<?= $arElement['DETAIL_PAGE_URL'] ?>" class="link"> </a>
                            <span class="photos">
                                <img src="<?= array_values($arElement['OFFERS'])[0]['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['IMG']['SRC'] ?>" alt="<?= $arElement['PREVIEW_PICTURE']['ALT'] ?>" class="active"/>
                                <span class="prev">
                                    <span class="icon__larr2">
                                        <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#larr2"></use></svg>
                                    </span>
                                </span>
                                <span class="next">
                                    <span class="icon__rarr2">
                                        <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#rarr2"></use></svg>
                                    </span>
                                </span>
                            </span>
                            <span class="stickers__widget type-block">
                                <?
                                if ($arElement['DISPLAY_PROPERTIES']['SALE']['VALUE'] == 'да') {
                                    ?>
                                    <span class="sale"></span>
                                    <?
                                }

                                if ($arElement['DISPLAY_PROPERTIES']['NEW']['VALUE'] == 'да') {
                                    ?>
                                    <span class="new"></span>
                                    <?
                                }
                                ?>
                            </span>
                        </div>
                        <div class="info">
                            <a href="<?= $arResult['SECTION_PAGE_URL'] ?>" class="category"><?= $arResult['NAME'] ?></a>
                            <br/>
                            <a href="<?= $arElement['DETAIL_PAGE_URL'] ?>" class="title"><?= $arElement['NAME'] ?></a>
                            <div class="price">
                                <?
                                if (!empty($arElement['CONTAINER_CLASS'])) {
                                    ?>
                                    <span class="from">от </span>
                                    <?
                                }
                                ?>
                                <span class="pricetag ds-price"><?= $arElement['MIN_PRICE']['PRINT_DISCOUNT_VALUE'] ?></span>
                                <?
                                if (!empty($arElement['MIN_PRICE']['DISCOUNT_PERCENT'])) {
                                    ?>
                                    <span class="sale__widget"><?= $arElement['MIN_PRICE']['DISCOUNT_PERCENT'] ?>%</span>
                                    <?
                                }
                                ?>
                            </div>
                        </div>
                        <form action="#" novalidate="novalidate" method="post">
                            <?
                            if (empty($arElement['OFFERS'])) {
                                ?>
                                <input type="hidden" name="product_id" value="<?= $arElement['ID'] ?>" data-item-id="data-item-id"/>
                                <?
                            } elseif ($arElement['INT_COUNT_OFFERS']) {
                                ?>
                                <input type="hidden" name="product_id" value="<?= $arElement['OFFERS'][0]['ID'] ?>" data-item-id="data-item-id"/>
                                <?
                            } else {
                                ?>
                                <div class="variants">
                                    <div class="fader left">
                                        <a data-direction="-1">
                                            <span class="icon__larr2">
                                                <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#larr2"></use></svg>
                                            </span>
                                        </a>
                                    </div>
                                    <div class="fader right">
                                        <a data-direction="1">
                                            <span class="icon__rarr2">
                                                <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#rarr2"></use></svg>
                                            </span>
                                        </a>
                                    </div>
                                    <div class="scrollable">
                                        <div class="list">
                                            <?
                                            foreach ($arElement['OFFERS'] as $key => $arOffer) {
                                                ?>
                                                <label>
                                                    <input
                                                        type="radio"
                                                        name="product_id"
                                                        value="<?= $arOffer['ID'] ?>"
                                                        autocomplete="off"
                                                        <?= ($key == 0) ? ' checked="checked"' : ''?>
                                                    />
                                                    <u>
                                                        <span class="icon__check">
                                                            <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use></svg>
                                                        </span>
                                                    </u>
                                                    <img src="<?= $arOffer['DISPLAY_PROPERTIES']['S']['IMG']['SRC'] ?>" alt=""/>
                                                </label>
                                                <?
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?
                            }
                            ?>
                            <button class="button type-blue fill size-41">В корзину</button>
                        </form>
                    </div>
                </div>
                <?
            }
            ?>
            <div class="item placeholder"></div>
            <div class="item placeholder"></div>
            <div class="item placeholder"></div>
        </div>
    </div>
</section>
<p class="no_favorites_items">Нет избранных товаров</p>
