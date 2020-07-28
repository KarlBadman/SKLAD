<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);?>
<div id="wrap-catalog">
    <section class="catalog">
        <div class="ds-wrapper default">
            <div class="catalog__widget" data-product-impressions="container" data-product="container" <?if($arResult['ID'] !=\Dsklad\Config::getParam('section/discounts')):?>data-category-id="<?=(!empty($arResult['ID'])) ? $arResult['ID'] : 0;?>"<?endif;?>>
                <?
                foreach ($arResult['ITEMS'] as $intKey => $arElement) {
                    if ($arElement['ID'] != 22950) {
                        ?>
                        <div class="item<?=$arElement['CONTAINER_CLASS'] ?>"
                            data-galist="item"
                            data-item-name="<?=$arElement['NAME'] ?>"
                            data-item-id="<?=$arElement['ID'] ?>"
                            data-item-price="<?=$arElement['MIN_PRICE']?>"
                            data-item-category="<?=$arResult['NAME'] ?>"
                            >
                            <div class="item-wrap">
                                <div class="image">
                                    <a href="<?= $arElement['DETAIL_PAGE_URL'] ?>" class="link" id="mainlink-<?= $arElement['ID'] ?>" data-ga-analys-btn="to-detail-lnk"> </a>
                                    <span class="photos">
                                        <img id="mainimg-<?= $arElement['ID'] ?>"
                                             src="<?= $arElement['OFFERS'][0]['IMAGES'][1]['R']['SRC']?>"
                                             alt="<?= $arElement['PREVIEW_PICTURE']['ALT'] ?>"
                                             class="active mainimg-<?= $arElement['ID'] ?>"/>
                                        <input type="hidden" data-old-img="<?= $arElement['OFFERS'][0]['IMAGES'][1]['R']['SRC'] ?>" data-value="<?= $arElement['ID'] ?>">
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
                                        <? } elseif ($arElement['PROPERTIES']['HIT']['VALUE'] == 'да') {?>
                                            <span class="hit"></span>
                                        <? } elseif ($arElement['PROPERTIES']['PRESENT']['VALUE'] == 'да') {?>
                                            <span class="gift"></span>
                                        <? }

                                        if ($arElement['DISPLAY_PROPERTIES']['NO_VARIANTS']['VALUE_ENUM_ID'] == 1276) { ?>
                                            <span class="no_variants">под заказ</span>
                                        <? } ?>
                                    </span>
                                </div>
                                <div class="info">
                                    <a href="<?= $arResult['SECTION_PAGE_URL'] ?>" class="category"><?= $arResult['NAME'] ?></a><br/>
                                    <a href="<?= $arElement['DETAIL_PAGE_URL'] ?>" class="title"><?= $arElement['NAME'] ?></a>
                                    <div class="price">
                                        <? if (!empty($arElement['FROM'])) { ?>
                                            <span class="from">от </span>
                                        <? } ?>
                                        <span class="pricetag ds-price"><?= $arElement['MIN_PRICE']?></span>
                                        <? if (!empty($arElement['DISCOUNT_PERCENT']) && /* TODO: разобраться*/!in_array($arElement['ID'], array('19460', '19440'))) { ?>
                                            <span class="sale__widget">%</span>
                                        <? } ?>
                                    </div>
                                </div>
                                <form action="#" novalidate="novalidate" method="post">
                                    <? if (empty($arElement['OFFERS'])) { ?>
                                        <input
                                                type="hidden"
                                                class="js-select-offer"
                                                name="product_id"
                                                data-addd="other"
                                                value="<?= $arElement['ID'] ?>"
                                                data-item-id="data-item-id"
                                                data-count="<?= $arElement['PRODUCT']['QUANTITY'] ?>"
                                                data-preorder="<?= $arElement['PROPERTIES']['ARRIVAL_DATE']['VALUE'] ?>"
                                            <?= ($arElement['DISPLAY_PROPERTIES']['SALE']['VALUE'] == 'да') ? 'data-sale="Y"' : '' ?>
                                        />
                                    <? } elseif ($arElement['INT_COUNT_OFFERS'] || empty($arElement['CONTAINER_CLASS'])) { ?>
                                        <input type="hidden"
                                               class="no_offers"
                                               value="<?= $arElement['OFFERS'][0]['ID'] ?>">
                                        <input
                                                data-add-test="test"
                                                class="js-select-offer"
                                                type="hidden"
                                                name="product_id"
                                                value="<?= $arElement['OFFERS'][0]['ID'] ?>"
                                                data-item-id="data-item-id" checked="checked"
                                                data-count="<?= $arElement['OFFERS'][0]['PRODUCT']['QUANTITY'] ?>"
                                                data-preorder="<?= empty($arElement['OFFERS'][0]['PROPERTIES']['ARRIVAL_DATE']['VALUE']) ? $arElement['PROPERTIES']['ARRIVAL_DATE']['VALUE'] : $arElement['OFFERS'][0]['PROPERTIES']['ARRIVAL_DATE']['VALUE'] ?>"
                                            <?= ($arElement['DISPLAY_PROPERTIES']['SALE']['VALUE'] == 'да') ? 'data-sale="Y"' : '' ?>
                                        />
                                    <? } else { ?>
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
                                                    <? foreach ($arElement['OFFERS'] as $key => $arOffer) { ?>
                                                        <label>
                                                            <input
                                                                    class="js-select-offer"
                                                                    type="radio"
                                                                    name="product_id"
                                                                    value="<?= $arOffer['ID'] ?>"
                                                                <?= ($key == 0) ? 'checked="checked"' : '' ?>
                                                                    data-count="<?= $arOffer['PRODUCT']['QUANTITY'] ?>"
                                                                    data-preorder="<?= empty($arOffer['PROPERTIES']['ARRIVAL_DATE']['VALUE']) ? $arElement['PROPERTIES']['ARRIVAL_DATE']['VALUE'] : $arOffer['PROPERTIES']['ARRIVAL_DATE']['VALUE'] ?>"
                                                                <?= ($arOffer['DISPLAY_PROPERTIES']['RASPRODAZHA']['VALUE'] == 'Да') ? 'data-sale="Y"' : '' ?>
                                                            />
                                                            <u>
                                                                <span class="icon__check">
                                                                    <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use></svg>
                                                                </span>
                                                            </u>
                                                            <img
                                                                <?= ($key == 0) ? 'data-checked="checked"' : '' ?>
                                                                    class="thumbimg"
                                                                    data-thumbimg="<?= $arElement['ID'] ?>"
                                                                    data-mainlinkimg="<?= $arElement['DETAIL_PAGE_URL'] . $arOffer['ID'] . '/'?>"
                                                                    src="<?= SITE_TEMPLATE_PATH ?>/images/blank-loader.gif"
                                                                    data-thumbsrc="<?= $arOffer['IMAGES'][1]['S']['SRC'] ?>"
                                                                    data-src="<?= $arOffer['IMAGES'][1]['R']['SRC'] ?>"
                                                                    alt=""/>
                                                        </label>
                                                    <? } ?>
                                                </div>
                                            </div>
                                        </div>
                                    <? } ?>
                                    <a
                                            data-ga-analys-btn="basket-add-item"
                                            href="<?= SITE_TEMPLATE_PATH ?>/ajax/cart.php?ID=0"
                                            data-href-tmp="<?= SITE_TEMPLATE_PATH ?>/ajax/cart.php?ID=#ID#"
                                            class="button type-blue fill size-41 js-ds-modal to_basket" data-ds-modal-width="660">В корзину</a>
                                    <a
                                            data-ga-analys-btn="basket-add-item-preorder"
                                            href="<?= SITE_TEMPLATE_PATH ?>/ajax/cart_preorder.php?ID=0"
                                            data-href-tmp="<?= SITE_TEMPLATE_PATH ?>/ajax/cart_preorder.php?ID=#ID#"
                                            class="button type-blue fill size-41 size-41 type-blue js-ds-modal to_preorder hidden" data-ds-modal-width="660">Оформить предзаказ</a>
                                    <a
                                            href="#preorder-form-container2"
                                            class="button size-41 type-blue js-ds-modal to_preorder_form hidden" data-ds-modal-width="660">Сообщить о поступлении</a>
                                </form>
                            </div>
                        </div>
                        <?
                    }
                }
                ?>
                <div class="item placeholder"></div>
                <div class="item placeholder"></div>
                <div class="item placeholder"></div>
            </div>
            <?
            if ($arParams['DISPLAY_BOTTOM_PAGER']) {
                echo $arResult['NAV_STRING'];
            }
            ?>
            <?if($arResult['ID'] > 0 && count($arResult['ITEMS']) > 0):?>
                <?if($arResult['ID'] !=\Dsklad\Config::getParam('section/discounts')):?>
                    <div data-retailrocket-markup-block="5d5ce89197a52817280bcfec" data-category-id="<?=$arResult['ID']?>" data-stock-id="4"></div>
                <?else:?>
                    <div data-retailrocket-markup-block="5d5ce91997a52817280bcff9" data-stock-id="4"></div>
                <?endif?>
            <?endif?>
        </div>
    </section>
    <?
	$seotext = '';
	if(!empty($GLOBALS['arrFilter']['PROPERTY_TAGS'])) {
		$arFilter = Array("IBLOCK_ID"=>IntVal(\Dsklad\Config::getParam('iblock/tags')), "ID"=>$GLOBALS['arrFilter']['PROPERTY_TAGS'], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
		$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), Array("DETAIL_TEXT"));
		if($properties = $res->fetch()) {
			$seotext = $properties['DETAIL_TEXT'];
		}
	} elseif(!empty($arResult["DESCRIPTION"])){
		$seotext = $arResult["DESCRIPTION"];
	}
	if(!empty($seotext)) { ?>
		<section class="seo__widget">
			<div class="ds-wrapper default">
				<?=$seotext;?>
			</div>
		</section>
	<? } ?>
</div>

<?
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/ajax/preorder.php');
?>

<script>
    $(document).ready(function() {
        $('.js-select-offer:checked').each(function() {
            $(this).change();
        });

        $('.js-select-offer[type=hidden]').each(function() {
            $(this).change();
        });
    });

    /* TODO повторяющийся код и его эквивалент $('.catalog').on('change', '.js-select-offer', function(){*/
    $(document).on('change', '.js-select-offer', function() {
        var variant = $(this);
        var form = variant.closest('form');
        var buybtn = form.find('a.to_basket');
        var prebtn = form.find('a.to_preorder');
        var prebtnfrm = form.find('a.to_preorder_form');
        var sale = variant.data('sale');
        var toCatTmp = buybtn.data('href-tmp');
        var toCatTmpPre = prebtn.data('href-tmp');

        toCatTmp = toCatTmp.replace('#ID#', variant.val());
        toCatTmpPre = toCatTmpPre.replace('#ID#', variant.val());
        buybtn.attr('href', toCatTmp);
        prebtn.attr('href', toCatTmpPre);

        // Убираем уведомить о поступлении, оставляем общий вариант покупки (предзаказ)
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

</script>
