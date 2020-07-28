<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);
?>
<section class="catalog seen">
    <h2>Вы уже смотрели</h2>
    <div class="catalog__widget" data-product-impressions="container">
        <?
        foreach ($arResult['ITEMS'] as $intKey => $arElement) {
            ?>
            <div class="item<?= $arElement['CONTAINER_CLASS'] ?>"
                data-galist="item"
                data-item-name="<?=$arElement['NAME']?>"
                data-item-id="<?=$arElement['ID'] ?>"
                data-item-price="<?=$arElement['MIN_PRICE']?>"
            >
                <div class="item-wrap">
                    <div class="image">
                        <a href="<?= $arElement['DETAIL_PAGE_URL'] ?>" class="link" id="mainlink-<?= $arElement['ID'] ?>" data-ga-analys-btn="to-detail-lnk"> </a>
                        <span class="photos">
                            <img
                                id="mainimg-<?= $arElement['ID'] ?>"
                                src="<?= SITE_TEMPLATE_PATH ?>/images/blank-loader.gif"
                                data-lazysrc="<?= $arElement['OFFERS'][0]['DISPLAY_PROPERTIES']['FOTOGRAFIYA_4']['IMG']['SRC'] ?>"
                                alt="<?= $arElement['PREVIEW_PICTURE']['ALT'] ?>"
                                class="lazyload active mainimg-<?= $arElement['ID'] ?>"/>
                            <input type="hidden" data-old-img="<?= $arElement['OFFERS'][0]['DISPLAY_PROPERTIES']['FOTOGRAFIYA_4']['IMG']['SRC'] ?>" data-value="<?= $arElement['ID'] ?>">
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
                            <? if($APPLICATION->get_cookie("TEMP_STOCK_ON") && $arElement['PROPERTIES']['TEMPORARY_STOCK']['VALUE'] == 'Да'){?>
                                <span class="tmpstock"></span>
                            <? } elseif ($arElement['DISPLAY_PROPERTIES']['SALE']['VALUE'] == 'да') { ?>
                                <span class="sale"></span>
                            <? } elseif ($arElement['DISPLAY_PROPERTIES']['NEW']['VALUE'] == 'да') { ?>
                                <span class="new"></span>
                            <? } ?>
                        </span>
                    </div>
                    <div class="info">
                        <a data-ga-analys-btn="to-detail-lnk" href="<?= $arResult['SECTION_PAGE_URL'] ?>" class="category"><?= $arResult['NAME'] ?></a><br/>
                        <a data-ga-analys-btn="to-detail-lnk" href="<?= $arElement['DETAIL_PAGE_URL'] ?>" class="title"><?= $arElement['NAME'] ?></a>
                        <div class="price">
                            <?
                            if (!empty($arElement['CONTAINER_CLASS'])) {
                                ?>
                                <span class="from">от </span>
                                <?
                            }
                            ?>
                            <span class="pricetag"><?= $arElement['MIN_PRICE'] ?>.–</span>
                            <?
                            if (!empty($arElement['MIN_PRICE']['DISCOUNT_PERCENT'])) {
                                ?>
                                <span class="sale__widget">%</span>
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
                        } else {
                            ?>
                            <div class="variants">
                                <div class="fader left">
                                    <a href="" data-direction="-1">
                                    <span class="icon__larr2">
                                        <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#larr2"></use></svg>
                                    </span>
                                    </a>
                                </div>
                                <div class="fader right">
                                    <a href="" data-direction="1">
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
                                                    class="js-select-offer"
                                                    <?= ($key == 0) ? ' checked="checked"' : '' ?>
                                                />
                                                <u>
                                                    <span class="icon__check">
                                                        <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use></svg>
                                                    </span>
                                                </u>
                                                <img class="thumbimg"
                                                     data-thumbimg="<?= $arElement['ID'] ?>"
                                                     src="<?= SITE_TEMPLATE_PATH ?>/images/blank-loader.gif"
                                                     data-thumbsrc="<?= $arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['IMG']['SRC'] ?>"
                                                     data-mainlinkimg="<?= $arElement['DETAIL_PAGE_URL'] . $arOffer['ID'] . '/'?>"
                                                     data-src="<?=$arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_4']['IMG']['SRC']?>"
                                                     alt=""/>
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
                        <a
                            href="<?= SITE_TEMPLATE_PATH ?>/ajax/cart.php?ID=<?= $arOffer['ID'] ?>"
                            data-href-tmp="/local/templates/dsklad/ajax/cart.php?ID=#ID#"
                            data-ga-analys-btn="basket-add-item"
                            data-ds-modal-width="660"
                            class="button size-41 type-blue fill js-ds-modal to_basket btn_to_basket_<?= $cnt ?>">В корзину</a>
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
</section>
