<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Context;

$this->setFrameMode(true);

$request = Context::getCurrent()->getRequest();
$isAjax = $request->isAjaxRequest();
$sID = randString(10);

global $bPreorderFormExist;
if ($bPreorderFormExist != true) {
	$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/ajax/preorder.php');
}

if (!empty($arResult['ITEMS'])) {
    ?>
    <!--static.criteo.net-->
    <section class="catalog">
        <?
        if (!$isAjax) {
            ?>
            <div class="subheading">Все предложения</div>
            <div class="catalog__widget">
            <?
        } else {
            $APPLICATION->RestartBuffer();
            ?>
            <div id="id_<?= $sID ?>" class="catalog__widget">
            <?
        }

        //criteo
        $count = count($arResult['ITEMS']);
        foreach ($arResult['ITEMS'] as $masids) {
            $predelkriteo += 1;

            $zptkriteo = '';
            if ($predelkriteo <= $count - 1) {
                $zptkriteo = ',';
            }

            $newmassidscriteo .= "'".$masids['OFFERS'][0]['ID']."' $zptkriteo";
        }
        ?>

        <script type="text/javascript">
            window.criteo_q = window.criteo_q || [];
            var deviceType = /iPad/.test(navigator.userAgent) ? 't' : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? 'm' : 'd';
            window.criteo_q.push(
                { event: 'setAccount', account: 44817 },
                { event: 'setEmail', email: '<?= $_SESSION['SESS_AUTH']['EMAIL'] ?>' },
                { event: 'setSiteType', type: deviceType },
                { event: 'viewList', item:[ <?= $newmassidscriteo ?>]}
            );
            //END criteo
        </script>

        <script type="text/javascript">
            //google ecomm vit
            var google_tag_params = {
                ecomm_prodid: [<?= $newmassidscriteo ?>],
                ecomm_pagetype: 'category'
            };
            //END google ecomm vit
        </script>

        <?/* vk pixel temporary removed 01.11.18
        $ids='[';
        */
        foreach ($arResult['ITEMS'] as $intKey => $arElement) {
            /* vk pixel temporary removed 01.11.18
            if ($ids != '[') {
                $ids .= ",";
            }
            $ids .= '{"id":"'.$arElement['ID'].'"}';
            */
            ?>
            <div class="item<?= $arElement['CONTAINER_CLASS'] ?>">
                <div itemscope itemtype="http://schema.org/Product" class="item-wrap">
                    <div class="image">
                        <a href="<?= $arElement['DETAIL_PAGE_URL'] ?>" class="link main_prod_link"> </a>
                        <span class="photos">
                            <img
                                id="mainimg-<?= $arElement['ID'] ?>"
                                src="<?= $arElement['OFFERS'][0]['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['IMG']['SRC'] ?>"
                                alt="<?= $arElement['PREVIEW_PICTURE']['ALT'] ?>"
                                class="active mainimg-<?= $arElement['ID'] ?>"/>
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
                        <a itemprop="category" href="<?= $arResult['SECTION_PAGE_URL'] ?>" class="category">
                            <?= $arResult['NAME'] ?>
                        </a>
                        <br/>
                        <a href="<?= $arElement['DETAIL_PAGE_URL'] ?>" class="title">
                            <span itemprop="name"><?= $arElement['NAME'] ?></span>
                        </a>
                        <div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="price">
                            <?
                             if (!empty($arElement['CONTAINER_CLASS'])) {
                                ?>
                                <span class="from">от </span>
                                <?
                             }
                             ?>
                            <span content="<?=$arElement['MIN_PRICE_NUMBER']?>" itemprop="price" class="pricetag">
                                <?= $arElement['MIN_PRICE'] ?>.–
                            </span>
                            <? if (!empty($arElement['DISCOUNT_PERCENT'])) { ?>
                                <span class="sale__widget">%</span>
                            <? } ?>
                            <span style="display: none !important;" itemprop="priceCurrency">руб.</span>
                        </div>
                    </div>
                    <form action="#" novalidate="novalidate" method="post">
                        <?
                        if (empty($arElement['OFFERS'])) {
                            ?>
                            <input
                                type="hidden"
                                class="js-select-offer"
                                name="product_id"
                                data-add-offer="<?= $res ?>"
                                value="<?= $arElement['ID'] ?>"
                                data-item-id="data-item-id"/>
                            <?
                            if ($arElement['PRODUCT']['QUANTITY']) {
                                ?>
                                <a href="<?=SITE_TEMPLATE_PATH?>/ajax/cart.php?ID=<?=$arElement['ID']?>" data-fancybox-type="ajax" class="button type-blue fill size-41 fancybox to_basket">В корзину</a>
                                <?
                            } else {
                                $srtPropArrivalDate = $arElement["PROPERTIES"]["ARRIVAL_DATE"]["VALUE"];
                                if ($srtPropArrivalDate) {
                                    ?>
                                    <a href="<?= SITE_TEMPLATE_PATH ?>/ajax/cart.php?ID=<?= $arOffer['ID'] ?>" data-fancybox-type="ajax" rel="nofollow" class="button size-41 type-blue fill fancybox to_preorder btn_to_basket_<?= $cnt ?><?=(($srtPropArrivalDate)?'':' hidden')?>">Добавить в корзину</a>
                                    <?
                                } else {
                                    ?>
                                    <a href="#preorder-form-container" class="button size-41 type-blue fancybox to_preorder_form hidden">Сообщить о поступлении</a>
                                    <?
                                }
                            }
                        } elseif ($arElement['INT_COUNT_OFFERS'] || empty($arElement['CONTAINER_CLASS'])) {
                            $res = CCatalogSKU::getOffersList(
                                $arElement['ID'],
                                $iblockID = array(),
                                $skuFilter = array(),
                                $fields = array('PROPERTY_ARRIVAL_DATE'),
                                $propertyFilter = array()
                            );

                            foreach ($res[$arElement['ID']] as $key => $value) {
                                $arElement['OFFERS'][0]['ID'] = $value['ID'];
                                $arElement['OFFERS'][0]['ARRIVAL_DATE'] = $value['PROPERTY_ARRIVAL_DATE_VALUE'];
                            }
                            ?>
                            <input
                                data-add-offer="test"
                                type="hidden"
                                name="product_id"
                                value="<?= $arElement['OFFERS'][0]['ID'] ?>"
                                data-item-id="data-item-id"/>
                            <a
                                href="<?=SITE_TEMPLATE_PATH?>/ajax/cart.php?ID=<?=$arElement['OFFERS'][0]['ID']?>"
                                data-href-tmp="<?=SITE_TEMPLATE_PATH?>/ajax/cart.php?ID=#ID#"
                                data-fancybox-type="ajax"
                                class="button type-blue fill size-41 fancybox to_basket<?=($arElement['OFFERS'][0]['PRODUCT']['QUANTITY']?'':' hidden')?>">В корзину</a>
                            <?
                            $srtPropArrivalDate = !empty($arElement['OFFERS'][0]['ARRIVAL_DATE']) ?$arElement['OFFERS'][0]['ARRIVAL_DATE'] : $arElement["PROPERTIES"]["ARRIVAL_DATE"]["VALUE"];
                            ?>
                            <a
                                href="<?=SITE_TEMPLATE_PATH?>/ajax/cart_preorder.php?ID=<?=$arElement['OFFERS'][0]['ID']?>"
                                data-href-tmp="<?=SITE_TEMPLATE_PATH?>/ajax/cart.php?ID=#ID#"
                                data-fancybox-type="ajax"
                                class="button type-blue fill size-41 fancybox to_preorder<?=(($arElement['OFFERS'][0]['PRODUCT']['QUANTITY']==0)?'':' hidden')?>">Оформить предзаказ</a>
                            <a href="#preorder-form-container" class="button size-41 type-blue fancybox to_preorder_form<?=((true || ($arElement['OFFERS'][0]['PRODUCT']['QUANTITY']==0 && $srtPropArrivalDate) || $arElement['OFFERS'][0]['PRODUCT']['QUANTITY'])?' hidden':'')?>">Сообщить о поступлении</a>

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
                                                    class="js-select-offer"
                                                    value="<?= $arOffer['ID'] ?>"
                                                    <? if ($key == 0) { ?>checked="checked"<? } ?>
                                                    data-count="<?=$arOffer['PRODUCT']['QUANTITY']?>"
                                                    data-preorder="<?= empty($arOffer['PROPERTIES']['ARRIVAL_DATE']['VALUE'])?$arElement['PROPERTIES']['ARRIVAL_DATE']['VALUE']:$arOffer['PROPERTIES']['ARRIVAL_DATE']['VALUE'] ?>"
                                                    <? if ($arOffer["DISPLAY_PROPERTIES"]['RASPRODAZHA']['VALUE'] == 'Да') { ?>data-sale="Y"<? } ?>/>
                                                <u>
                                                    <span class="icon__check">
                                                        <svg>
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use>
                                                        </svg>
                                                    </span>
                                                </u>
                                                <img
                                                    class="thumbimg"
                                                    data-thumbimg="<?= $arElement['ID'] ?>"
                                                    src="<?= $arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_3']['IMG']['SRC']?>"
                                                    data-src="<?= $arOffer['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['IMG']['SRC']?>"
                                                    alt=""/>
                                            </label>
                                            <?
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?
                            $srtPropArrivalDate = !empty($arElement['OFFERS'][0]['PROPERTIES']['ARRIVAL_DATE']['VALUE']) ? $arElement['OFFERS'][0]['PROPERTIES']['ARRIVAL_DATE']['VALUE'] : $arElement['PROPERTIES']['ARRIVAL_DATE']['VALUE'];
                            ?>
                            <a
                                href="<?= SITE_TEMPLATE_PATH ?>/ajax/cart.php?ID=<?= $arElement['OFFERS'][0]['ID'] ?>"
                                data-href-tmp="<?= SITE_TEMPLATE_PATH ?>/ajax/cart.php?ID=#ID#"
                                data-fancybox-type="ajax"
                                class="button type-blue fill size-41 fancybox to_basket<?= ($arElement['OFFERS'][0]['PRODUCT']['QUANTITY'] ? ''  :' hidden') ?>">В корзину</a>
                            <a
                                href="<?=SITE_TEMPLATE_PATH?>/ajax/cart_preorder.php?ID=<?= $arElement['OFFERS'][0]['ID'] ?>"
                                data-href-tmp="<?= SITE_TEMPLATE_PATH ?>/ajax/cart.php?ID=#ID#"
                                data-fancybox-type="ajax"
                                class="button type-blue fill size-41 fancybox to_preorder<?= (($arElement['OFFERS'][0]['PRODUCT']['QUANTITY'] == 0) ? '' : ' hidden') ?>">Оформить предзаказ</a>
                            <a
                                href="#preorder-form-container"
                                class="button size-41 type-blue fancybox to_preorder_form<?= ((true || ($arElement['OFFERS'][0]['PRODUCT']['QUANTITY'] == 0 && $srtPropArrivalDate) || $arElement['OFFERS'][0]['PRODUCT']['QUANTITY']) ? ' hidden' : '') ?>">Сообщить о поступлении</a>
                            <?
                        }
                        ?>
                    </form>
                </div>
            </div>
            <?
        }
        /* vk pixel temporary removed 01.11.18
        $ids.="]";
        */
        ?>
        <div class="item placeholder"></div>
        <div class="item placeholder"></div>
        <div class="item placeholder"></div>

        </div><!--/catalog__widget-->

        <?= $arResult['NAV_STRING'] ?>
        <?
        if ($isAjax) {
            ?>
            <script>
    <?/* vk pixel temporary removed 01.11.18
                if (typeof VK !== 'undefined') {
                    var params = {'products': <?= $ids ?>};
                    VK.Retargeting.ProductEvent(<?= VK_PRICE_LIST_ID ?>, 'view_search', params);
                }
    */?>
                var catalog = $('#id_<?= $sID ?>.catalog__widget');

                $('.item-wrap', catalog).each(function(){
                    $(this).one('mouseover', function(){
                        var variants = $(this).find('.variants');
                        if (variants.length){
                            var data = variants.data();
                            $.extend(data, {
                                clientWidth: data.list.width(),
                                scrollWidth: data.list[0].scrollWidth
                            });
                        }
                    });
                });

                $('.variants', catalog).each(function(){
                    $.extend($(this).data() || {}, {
                        list: $('.list', $(this)),
                        scrollLeft: 0,
                        spaceForItem: 68,
                        clientWidth: 0,
                        scrollWidth: 0
                    });
                }).find('.fader a').on('click', function(e){
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

                    data.list.animate({scrollLeft: distance}, 500, 'easeInOutCubic', function(){
                        data.scrollLeft = distance;
                        data.list.trigger('update');
                    });
                    //return false;
                }).end().find('.list').on('scroll update', function (e) {
                    var variants = $(this).parents('.variants'),
                        data = variants.data();

                    if (typeof(data.list.scrollLeft) !== undefined) {
                        
                        data.scrollLeft = data.list.scrollLeft();
    
                        variants
                            .find('.arrows .prev').attr('aria-disabled', (data.scrollLeft <= 0))
                            .end()
                            .find('.arrows .next').attr('aria-disabled', (data.scrollLeft + data.clientWidth >= data.scrollWidth));
                    }

                });
            </script>
            <?
            die();
        }
        ?>
    </section>

    <script>
    <?/* vk pixel temporary removed 01.11.18
        if (typeof VK !== 'undefined') {
            var params = {'products': <?= $ids ?>};
            VK.Retargeting.ProductEvent(<?= VK_PRICE_LIST_ID ?>, 'view_search', params);
        }
    */?>
        <?
        if ($bPreorderFormExist != true) {
            ?>
            //оформлен предзаказ
            $('#preorder-form').on('submit', function(e){
                e.preventDefault();
                var form = $(this);
                $.post(
                    '/ajax/preorder.php',
                    form.serialize(),
                    function(data){
                        $('.success_popup_send_txt').removeClass('active');
                        $('.error_popup_send_txt').removeClass('active');
                        try {
                            ym(26291919, 'reachGoal', 'preorder');
                            ga('send', 'event', 'pre_order', 'preorder');
                        } catch(e){}
                        try {
                            if (data.status) {
                                $.fancybox.close();
                                form[0].reset();
                                setTimeout(function () {
                                    $('.success_popup_send_txt').find('.desc_popup_send_txt').find('span').html('Благодарим за заявку. Ждите звонка!');
                                    $('.success_popup_send_txt').addClass('active');
                                }, 1000);

                                setTimeout(function () {
                                    $('.success_popup_send_txt').removeClass('active');
                                }, 6000);
                            } else {

                                setTimeout(function () {
                                    $('.error_popup_send_txt').find('.desc_popup_send_txt').find('span').html('Ошибка сохранения данных!');
                                    $('.error_popup_send_txt').addClass('active');
                                }, 1000);

                                setTimeout(function () {
                                    $('.error_popup_send_txt').removeClass('active');
                                }, 6000);
                            }
                        } catch(e){}
                    },
                    'json'
                );
            });
            <?
        }
        ?>
    </script>
    <?
    $bPreorderFormExist = true;
}
?>