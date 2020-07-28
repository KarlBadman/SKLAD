<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?
$this->addExternalJS(SITE_TEMPLATE_PATH.'/js/handlebars-v4.1.2.js');
$this->SetViewTarget('metaCatalogElementImg');
$this->addExternalJS('/local/assets/js/jquery.inputmask.min.js');
$this->addExternalJS('/local/assets/js/jquery.inputmask-multi.js');
$this->addExternalJS('/local/assets/js/jquery.inputmask-conf.js');
$this->addExternalJS(SITE_TEMPLATE_PATH.'/js/parsley.js');
$this->addExternalJS(SITE_TEMPLATE_PATH.'/js/parsley-ru.js');
?>
<meta property="og:image" content="<?=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST']?><?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" />
<meta property="og:image:secure_url" content="<?=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST']?><?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" />
<meta property="og:image:type" content="image/jpeg" />
<meta property="og:image:width" content="<?=$arResult["PREVIEW_PICTURE"]["WIDTH"]?>" />
<meta property="og:image:height" content="<?=$arResult["PREVIEW_PICTURE"]["HEIGHT"]?>" />
<meta property="og:image:alt" content="<?=$arResult["NAME"]?>" />
<?
$this->EndViewTarget();
?>
<div class="spinner hidden"></div> 
<div class="ds-wrapper" data-product="container">
    <div class="ds-catalog-detail" data-gaproductlist="item" data-item-offersId="<?=json_encode($arResult['AR_OFFERS_ID'])?>" data-stock-id="4">
        <meta itemprop="brand" content="Дизайн склад">
        <div class="ds-catalog-detail__header">
            <div data-block-name="name"><h1 data-item-aproperty="name" itemprop="name"><?=$arResult['OFFERS_ACTIVE']['NAME']?></h1></div>
            <div class="header-rating-vendor">
                <?$APPLICATION->IncludeComponent(
                    "dsklad:catalog.element.reviews",
                    "stars",
                    Array(
                        "AUTH_TOKEN" => "AIzaSyDFbt2PLPnqUO-8hGc96MXfG_53Q_VmHXE",
                        "ACCOUNT_ID" => "113305935758068345714",
                        "LOCATION_ID" => "10366985728781324249",
                        "PLACE_ID" => "ChIJ8_6_xVa1SkERp1ISogxdo38",
                        "USE_MYBUSINESS" => "N",
                        "CACHE_TIME" => "86400",
                        "CACHE_TYPE" => "A",
                        "COMPOSITE_FRAME_MODE" => "A",
                        "COMPOSITE_FRAME_TYPE" => "AUTO",
                        "USE_CACHE" => "Y"
                    )
                );?>
            </div>
            <div data-block-name="vendor" class="header-vendor">
                <span itemprop="productID" content="<?=$arResult['OFFERS_ACTIVE']['PROPERTIES']['CML2_ARTICLE']['VALUE']?>">Арт.: <?=$arResult['OFFERS_ACTIVE']['PROPERTIES']['CML2_ARTICLE']['VALUE']?></span>
            </div>
        </div>
        <div class="ds-catalog-detail__slider" data-block-name="slider">
            <div class="ds-double-slider-for-arrows js-double-slider-for-arrows"></div>
            <div class="ds-double-slider-for js-double-slider-for">
                <?foreach ($arResult['OFFERS_ACTIVE']['IMAGES']['PICTURE'] as $key => $img):?>
                    <div class="ds-double-slider-for__item"><img <?=($key==1)? 'itemprop="image" src' : 'data-lazy' ;?>="<?=$img['src']?>" alt=""></div>
                <?endforeach;?>
            </div>
            <? if(count($arResult['OFFERS_ACTIVE']['IMAGES']['ICON']) > 1) { ?>
                <div class="ds-double-slider-nav-arrows js-double-slider-nav-arrows"></div>
                <div class="ds-double-slider-nav js-double-slider-nav">
                    <?foreach ($arResult['OFFERS_ACTIVE']['IMAGES']['ICON'] as $img):?>
                        <div class="ds-double-slider-nav__item"><img src="<?=$img['src']?>" alt=""></div>
                    <?endforeach;?>
                </div>
            <? } ?>
        </div>
        <div class="ds-catalog-detail__good-info">
            <div class="good-info">
                <div class="good-info__scroll">
                    <div class="good-info__purchase" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                        <div data-block-name="purchase">
                            <div class="good-info__item good-info--price">
                                <meta itemprop="priceCurrency" content="RUB" />
                                <div class="ds-price" data-item-price="<?=number_format($arResult['OFFERS_ACTIVE']['RECOMMEND_PRICE'],2,'.','')?>" itemprop="price" content="<?=number_format($arResult['OFFERS_ACTIVE']['RECOMMEND_PRICE'],2,'.','')?>"><?=number_format($arResult['OFFERS_ACTIVE']['RECOMMEND_PRICE'],0,'.',' ')?></div>
                                <? if ($arResult['OFFERS_ACTIVE']['RECOMMEND_PRICE'] != $arResult['OFFERS_ACTIVE']['PRICES_BASE']['VALUE'] && $arResult['OFFERS_ACTIVE']['PRICES_BASE']['VALUE']) { ?>
                                <div class="ds-price ds-price--old"><?=number_format($arResult['OFFERS_ACTIVE']['PRICES_BASE']['VALUE'],0,'.',' ')?></div>
                                <?}?>
                            </div>
                            <?if($arResult['OFFERS_ACTIVE']['CATALOG_QUANTITY'] > 0):?>
                                <div class="good-info__item good-info--availability">В наличии —&nbsp;<span class="<?=$arResult['OFFERS_ACTIVE']['QUANTITY_STRING']['COLOR_CLASS']?>"><?=$arResult['OFFERS_ACTIVE']['QUANTITY_STRING']['TEXT']?></span></div>
                            <?else:?>
                                <?if(!empty($arResult['OFFERS_ACTIVE']['PROPERTIES']['ARRIVAL_DATE']['VALUE']) || !empty($arResult['PROPERTIES']['ARRIVAL_DATE']['VALUE'])):?>
                                    <div class="good-info__item good-info--waiting"><span>Ожидается <?=(!empty($arResult['OFFERS_ACTIVE']['PROPERTIES']['ARRIVAL_DATE']['VALUE'])) ? $arResult['OFFERS_ACTIVE']['PROPERTIES']['ARRIVAL_DATE']['VALUE'] : $arResult['PROPERTIES']['ARRIVAL_DATE']['VALUE'];?></span></div>
                                <?else:?>
                                    <div class="good-info__item good-info--availability"><span class="<?=$arResult['OFFERS_ACTIVE']['QUANTITY_STRING']['COLOR_CLASS']?>"><?=$arResult['OFFERS_ACTIVE']['QUANTITY_STRING']['TEXT']?></span></div>
                                <?endif;?>
                            <?endif;?>
                            <?if($arParams['USE_PRODUCT_QUANTITY'] == 'Y'):?>
                                <div class="good-info__item good-info--count">
                                    <?if($arResult['OFFERS_ACTIVE']['DISCOUNT_QUANTITY']):?>
                                        <?if($arResult['OFFERS_ACTIVE']['DISCOUNT_QUANTITY'] <= $arResult['OFFERS_ACTIVE']['RECOMMEND_QUANTITY']):?>
                                            <span class="count-title-sale success">
                                                <span class="count-title-sale__icon"></span>
                                                <span>Скидка на <?=$arResult['OFFERS_ACTIVE']['DISCOUNT_QUANTITY']?> шт. применена</span>
                                            </span>
                                        <?else:;?>
                                            <div class="count-title-sale">
                                                <div class="count-title-sale__icon"></div>
                                                <span>Скидка при заказе <br>от <?=$arResult['OFFERS_ACTIVE']['DISCOUNT_QUANTITY']?> шт.</span>
                                            </div>
                                        <?endif;?>
                                    <?else:;?>
                                        <span class="count-title">количество:</span>
                                    <?endif;?>
                                    <div class="ds-count">
                                        <button class="icon-svg ic-count-minus js-count-minus"></button>
                                        <input class="inp-count js-number" type="number" min="1" name="quantityProduct" data-item-aproperty="quantity" value="<?=$arResult['OFFERS_ACTIVE']['RECOMMEND_QUANTITY']?>">
                                        <button class="icon-svg ic-count-plus js-count-plus"></button>
                                    </div>
                            </div>
                            <?endif;?>
                            <div class="good-info__item good-info--btn"
                                 data-gaitem-id="<?=$arResult['OFFERS_ACTIVE']["ID"]?>">
                                <div class="ds-info-btn">
                                    <?if($arResult['OFFERS_ACTIVE']['CATALOG_QUANTITY'] < 1 && $arResult['PREPAYMENT']['CHECK']):?>
                                        <link itemprop="availability" content="http://schema.org/PreOrder"/>
                                        <button class="ds-btn ds-btn--default-big ds-btn--full js-add-to-basket" data-product-id="<?=$arResult['OFFERS_ACTIVE']['ID']?>" data-ga-analys-btn="basket-add-item-preorder">Оформить предзаказ</button>
                                    <?else:?>
                                        <link itemprop="availability" href="https://schema.org/InStock"/>
                                        <button class="ds-btn ds-btn--default-big ds-btn--full js-add-to-basket" data-product-id="<?=$arResult['OFFERS_ACTIVE']['ID']?>" data-ga-analys-btn="basket-add-item">Добавить в корзину</button>
                                    <?endif;?>
                                    <div class="icon-svg ic-favorite_off ds-favorite-btn js-add-to-favorite <?if($arResult['FAVORITE']):?>added<?endif;?>" data-product-id="<?=$arResult['ID']?>" data-ga-analys-btn="favorite"></div>
                                </div>
                            </div>
                            <div class="good-info__item good-info--btn">
                                <button class="ds-btn ds-btn--secondary ds-btn--full js-ds-modal ya-detail-one-click" data-href="<?=$templateFolder?>/modal/incl-quick-order.html" data-ds-modal-width="412">Оформить в 1 клик</button>
                            </div>
                        </div>
                    </div>
                    <div data-block-name="offers_select">
                        <?foreach ($arResult['PROPERTY_SELECT'] as $key => $prop):?>
                            <div class="good-info__item <?if(!$prop['ONE']):?>good-info--border<?endif;?>" data-prop-id="<?=$prop['ID']?>">
                                <div class="good-info__title" data-prop-id="<?=$prop['ID']?>"><?=str_replace('Код цвета', 'Цвет', $prop['NAME']);?>:&nbsp;<span><?=$prop['CHECKED_VALUE']?></span></div>
                                <?if($prop['TYPE'] == 'IMAGES'):?>
                                    <div class="goods-color">
                                        <?foreach ($prop['VALUES'] as $value):?>
                                             <?if($value['HIDDEN'] != 'Y'):?>
                                                <div class="goods-color__item js-offers <?if($value['CHECKED']):?>active<?endif;?>" data-prop-id="<?=$prop['ID']?>" data-prop-code="<?=$key?>" data-value="<?=$value['VALUE']?>" data-position="<?=$prop['POSITION']?>"><img src="<?=$value['SRC']?>" alt=""></div>
                                            <?endif;?>
                                        <?endforeach;?>
                                    </div>
                                <?else:?>
                                        <div class="goods-size">
                                            <?foreach ($prop['VALUES'] as $value):?>
                                                <?if($value['HIDDEN'] != 'Y'):?>
                                                    <span class="goods-size__item js-offers <?if($value['CHECKED']):?>active<?endif;?>" data-prop-id="<?=$prop['ID']?>" data-prop-code="<?=$key?>" data-value="<?=$value['VALUE']?>" data-position="<?=$prop['POSITION']?>"><?=$value['VALUE']?></span>
                                                <?endif;?>
                                            <?endforeach;?>
                                        </div>
                                <?endif;?>
                            </div>
                        <?endforeach;?>
                    </div>
                    <?if(!empty($arResult['DOPOLNITELNO_TEXT'])):?>
                        <div class="good-info__item good-info--border">
                            <div class="good-info__title">
                                <span>Дополнительная информация:</span>
                            </div>
                            <p><?=$arResult['DOPOLNITELNO_TEXT']?></p>
                        </div>
                    <?endif;?>
                    <div class="good-info__item good-info--border good-info--specification">
                        <div class="good-info__title"><span>Характеристики</span></div>
                        <div class="goods-specification js-expanded">
                            <?foreach ($arResult['DISPLAY_PROPERTIES'] as $property):?>
                                <?if ($property['CODE'] == 'INTERIOR') continue;?>
                                <div class="goods-specification__row">
                                    <div class="goods-specification__key"><?=$property['NAME']?></div>
                                    <div class="goods-specification__value"><?=$property['VALUE']?></div>
                                </div>
                            <?endforeach;?>
                        </div>
                        <? if(count($arResult['DISPLAY_PROPERTIES']) > 3) { ?>
                            <div class="ds-btn-more">
                                <button class="ds-btn ds-btn--light js-btn-more ds-btn--full">Ещё характеристики</button>
                            </div>
                        <? } ?>
                    </div>
                    
                    <?$HASHTAG = $arResult['PROPERTIES']['HASHTAG']['VALUE'] ? '#'.$arResult['PROPERTIES']['HASHTAG']['VALUE'] : "#".$arResult['CODE'];?>
                    <?$APPLICATION->IncludeComponent(
                        "dsklad:highload_get_list",
                        "instagramElement",
                        Array(
                            "HASHTAG" => $HASHTAG,
                            "COMPOSITE_FRAME_MODE" => "A",
                            "COMPOSITE_FRAME_TYPE" => "AUTO",
                            "COUNT" => "5",
                            "FILTER" => "{\"UF_POSTCONTENT\":[\"%".$HASHTAG."%\"]}",
                            "HL_TABLE_NAME" => "instagram4detail",
                            "SELECT" => "",
                            "SORT_FIELD" => "ID",
                            "SORT_ORDER" => "RANDOM",
                            "USE_CACHE" => "Y",
                            "CACHE_TIME" => "1209600"
                        )
                    );?>
                    <div class="good-info__item good-info--share">
                        <div class="good-info__title"><span> Поделиться</span></div>
                        <script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
                        <script src="//yastatic.net/share2/share.js"></script>
                        <div class="ya-share2 ds-ya-share2" data-services="facebook,vkontakte,twitter,odnoklassniki"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="retail-rocket-catalog-detail-similar">
            <div data-retailrocket-markup-block="5d5ce89d97a52817280bcfed" data-product-id="<?=implode(',',$arResult['AR_OFFERS_ID'])?>" data-stock-id="4"></div>
        </div>
        <? if(!empty($arResult['DETAIL_TEXT'])) { ?>
            <div class="ds-catalog-detail__descr device-border">
                <h4>Описание</h4>
                <div class="ds-catalog-descr js-expanded">
                    <div class="ds-catalog-descr__inner" itemprop="description">
                        <?=$arResult['DETAIL_TEXT']?>
                    </div>
                </div>
                <div class="ds-btn-more">
                    <button class="ds-btn ds-btn--light js-btn-more">Показать ещё</button>
                </div>
            </div>
        <? } ?>
        <div class="retail-rocket-catalog-detail">
            <div data-retailrocket-markup-block="5d5ce8a597a52817280bcfee" data-product-id="<?=implode(',',$arResult['AR_OFFERS_ID'])?>" data-stock-id="4"></div>
        </div>
        <?//$APPLICATION->IncludeFile('/include_areas/advantages.php'); //преимущества - почему дизайн склад?>
        <?$APPLICATION->IncludeComponent(
            "dsklad:catalog.element.reviews",
            "",
            Array(
                "AUTH_TOKEN" => "AIzaSyDFbt2PLPnqUO-8hGc96MXfG_53Q_VmHXE",
                "ACCOUNT_ID" => "113305935758068345714",
                "LOCATION_ID" => "10366985728781324249",
                "PLACE_ID" => "ChIJ8_6_xVa1SkERp1ISogxdo38",
                "USE_MYBUSINESS" => "N",
                "CACHE_TIME" => "86400",
                "CACHE_TYPE" => "A",
                "COMPOSITE_FRAME_MODE" => "A",
                "COMPOSITE_FRAME_TYPE" => "AUTO",
                "USE_CACHE" => "Y"
            )
        );?>
        </div>
    </div>
</div>

<script>
    <?global $USER;?>
    window.dsCatalogDetail.phpData = <?=CUtil::PhpToJSObject($arResult['JS_DATA']);?>;
    window.dsCatalogDetail.templateFloder = '<?=$templateFolder?>';
    window.dsCatalogDetail.autorized = '<?if($USER->IsAuthorized()){echo 'Y';}else{echo 'N';}?>';
</script>
