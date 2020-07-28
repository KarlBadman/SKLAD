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


<? if (!empty($arResult['ITEMS'])): ?>
    <section class="catalog<? if ($arParams['NAME'] == 'Рекомендуем'): ?> hidden-s<? endif ?>">
        <div class="default">
            
            <div class="catalog__widget">
                <? foreach ($arResult['ITEMS'] as $intKey => $arElement): ?>

                    <div class="item<?= $arElement['CONTAINER_CLASS'] ?>">
                        <div class="item-wrap">
                            <div class="image">
                                <a href="<?= $arElement['DETAIL_PAGE_URL'] ?>" class="link"> </a>
                                <span class="photos">
                                    <img id="mainimg-<?= $arElement['ID'] ?>" src="<?= arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['IMG']['SRC'] ?>"
                                         alt="<?= $arElement['PREVIEW_PICTURE']['ALT'] ?>" class="active mainimg-<?= $arElement['ID'] ?>"/>
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
                                    <input type="hidden" name="product_id" data-add-offer="<?= $res ?>"
                                           value="<?= $arElement['ID'] ?>"
                                           data-item-id="data-item-id"/>
                                <? elseif ($arElement['INT_COUNT_OFFERS'] || empty($arElement['CONTAINER_CLASS'])): ?>
                                    <?
                                    $res = CCatalogSKU::getOffersList(
                                        $arElement['ID'],
                                        $iblockID = array(),
                                        $skuFilter = array(),
                                        $fields = array(),
                                        $propertyFilter = array()
                                    );

                                    foreach ($res[$arElement['ID']] as $key => $value) {
                                        $arElement['OFFERS'][0]['ID'] = $value['ID'];
                                    }

                                    ?>
                                    <input data-add-offer="test" type="hidden" name="product_id"
                                           value="<?= $arElement['OFFERS'][0]['ID'] ?>"
                                           data-item-id="data-item-id"/>
                                <? else: ?>
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
                                                <? foreach ($arElement['OFFERS'] as $key => $arOffer): ?>
                                                    <label>
                                                        <input type="radio" name="product_id"
                                                               value="<?= $arOffer['ID'] ?>"
                                                               autocomplete="off"
                                                               <? if ($key == 0): ?>checked="checked"<? endif; ?>
                                                        />
                                                        <u>
                                                    <span class="icon__check">
                                                        <svg><use
                                                                    xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use></svg>
                                                    </span>
                                                        </u>
                                                        <img style="max-width: 68px;" class="thumbimg"
                                                             data-thumbimg="<?= $arElement['ID'] ?>"
                                                                src="<?= /*$arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_2']['IMG']['SRC']*/ $arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_3']['IMG']['SRC']?>"
                                                                data-src="<?=$arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['IMG']['SRC']?>"
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
                <div class="item placeholder"></div>
                <div class="item placeholder"></div>
                <div class="item placeholder"></div>
            </div>
            <?= $arResult['NAV_STRING'] ?>
        </div>
    </section>
    <script>

        $(document).ready(function () {

            $('.catalog__widget').each(function () {
                var is_desktop = false, is_tablet = false, is_phone = false,
                    mediaqueries = {
                        desktop: 1000,
                        tablet: 640
                    };
                var catalog = $(this);

                $(document).on('load', function () {
                    $('img.active + img[data-src]', catalog).attr('src', function () {
                        var img = $(this),
                            src = img.data().src;
                        img.removeAttr('data-src')
                        return src;
                    });
                });

                $('.photos > span', catalog).on('click', function (e) {
                    var arrow = $(this);

                    if (arrow.is('.prev')) {
                        arrow
                            .siblings('.active:not(:first-child)').removeClass('active')
                            .prev().addClass('active');
                    } else {
                        arrow
                            .siblings('.active:not(:last-of-type)').removeClass('active')
                            .next().addClass('active')
                            .next('[data-src]').attr('src', function () {
                            var img = $(this),
                                src = img.data().src;
                            img.removeAttr('data-src')
                            return src;
                        });
                    }
                });

                // $('.item', catalog).on('mouseover', function () {
                //     $(document).trigger('catalog.update');
                // });

                $('.variants', catalog).each(function () {

                    var variants = $(this);

                    $.extend(variants.data() || {}, {
                        list: $('.list', variants),
                        scrollLeft: 0,
                        spaceForItem: 68,
                        clientWidth: 0,
                        scrollWidth: 0
                    });

                }).find('.fader a').on('click', function (e) {

                    e.preventDefault();

                    var arrow = $(this),
                        data = arrow.parents('.variants').data();

                    var direction = parseInt(arrow.data().direction),
                        extraScroll = direction > 0 ? 10 : 0,
                        maxScroll = data.scrollWidth - data.clientWidth,
                        minScroll = 0,
                        distance = data.scrollLeft + extraScroll + data.spaceForItem * Math.floor(data.clientWidth / data.spaceForItem) * direction;

                    if (distance > maxScroll) {
                        distance = maxScroll;
                    }
                    if (distance < minScroll) {
                        distance = minScroll;
                    }

                    data.list.animate({scrollLeft: distance}, 500, 'easeInOutCubic', function () {
                        data.scrollLeft = distance;
                        data.list.trigger('update');
                    });

                }).end().find('.list').on('scroll update', function (e) {

                    var variants = $(this).parents('.variants'),
                        data = variants.data();

                    if (typeof(data.list.scrollLeft) !== undefined) {
                        
                        data.scrollLeft = data.list.scrollLeft();
    
                        variants
                            .find('.arrows .prev').attr('aria-disabled', (data.scrollLeft <= 0))
                            .end()
                            .find('.arrows .next').attr('aria-disabled', (data.scrollLeft + data.clientWidth >= data.scrollWidth));
    
                        var faders = [{
                            el: $('.fader.left', variants),
                            width: 40,
                            base: data.scrollLeft
                        }, {
                            el: $('.fader.right', variants),
                            width: 40,
                            base: data.scrollWidth - data.clientWidth - data.scrollLeft
                        }];
    
                        for (var i = 0; i < 2; i++) {
    
                            var opacity = Math.min(Math.abs(faders[i]['base'] / faders[i]['width']).toFixed(2), 1),
                                hidden = opacity <= .025;
    
                            faders[i]['el'].css('opacity', opacity).attr('aria-hidden', hidden);
    
                        }
                    }

                });

                $(document).on('ready load resize catalog.update', function () {
                    //e.preventDefault();
                    //if (is_desktop) {
                    $('.variants').each(function () {
                        //console.log($(this).attr('class'));
                        var data = $(this).data();
                        
                       if (typeof(data.list.scrollLeft) !== undefined) {
                            //console.log(data);
                            data.scrollLeft = data.list.scrollLeft();
                            data.clientWidth = data.list.width();
                            data.scrollWidth = data.list[0].scrollWidth;
                            $('.arrows', this).attr('aria-hidden', data.scrollWidth <= data.clientWidth);
                            data.list.trigger('update');
                       }
                    });
                    //}
                });

            });

        });
    </script>
<? endif ?>