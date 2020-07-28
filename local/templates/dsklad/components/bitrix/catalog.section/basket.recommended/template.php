<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="ds-recommend-wrapper" data-block-name="recommend">
    <?if($arResult['ITEMS']):?>
        <h4>Рекомендуем</h4>
        <div class="ds-recommend">
            <div class="ds-basket-slider-arrows js-slider-recommend-arrows"></div>
            <div class="ds-basket-slider js-slider-recommend" data-type="arrows">
                <?foreach ($arResult['ITEMS'] as $item):?>
                    <div class="ds-basket-slider__item">
                        <a href="<?=$item['DETAIL_PAGE_URL']?>">
                            <img src="<?=$item['PREVIEW_PICTURE']['SRC']?>" alt="<?=$item['PREVIEW_PICTURE']['ALT']?>">
                        </a>
                        <p><?=$item['NAME']?></p>
                        <div class="ds-price-sale">
                            <?if(!empty($item['CATALOG_PRICE_2'])):?>
                                <span class="ds-price"> <?=number_format($item['CATALOG_PRICE_2'],0,'.',' ')?></span>
                                <?if($item['CATALOG_PRICE_2'] < $item['CATALOG_PRICE_1']):?>
                                    <span class="ds-sale">%</span>
                                <?endif;?>
                            <?else:;?>
                                <span class="ds-price"> <?=number_format($item['CATALOG_PRICE_1'],0,'.',' ')?></span>
                            <?endif;?>
                        </div>
                        <span class="ds-btn ds-btn--light add-basket" data-productID="<?=$item['ID']?>" href="#">В корзину</span>
                    </div>
                <?endforeach;?>
            </div>
        </div>
    <?endif;?>
</div>
<?if (file_exists($_SERVER['DOCUMENT_ROOT'].$templateFolder."/handlebars/itemBasketRecommended.php"))
    require_once($_SERVER['DOCUMENT_ROOT'].$templateFolder."/handlebars/itemBasketRecommended.php");?>



