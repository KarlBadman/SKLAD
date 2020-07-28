<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if(!empty($arResult['ITEMS'])):?>
    <div class="ds-catalog-detail__recommend device-border">
        <h4>С этим товаром покупают</h4>
        <div class="ds-catalog-recommend" data-name="catalog-recommend" data-product-impressions="container">
            <?foreach ($arResult['ITEMS'] as $item):?>
                <div class="ds-catalog-recommend__item"
                     data-galist="item"
                     data-item-name="<?=$item['NAME']?>"
                     data-item-id="<?=$item['ID'] ?>"
                     data-item-price="<?=number_format($item['PROPERTIES']['MINIMUM_PRICE']['VALUE'],2,'.',' ')?>"
                >
                    <a href="<?=$item['DETAIL_PAGE_URL']?>">
                        <img src="<?=$item['PREVIEW_PICTURE']['SRC']?>" alt="<?=$item['PREVIEW_PICTURE']['ALT']?>">
                        <p><?=$item['NAME']?></p>
                        <div class="ds-price-sale">
                            <span class="ds-price"><?=number_format($item['PROPERTIES']['MINIMUM_PRICE']['VALUE'],0,'.',' ')?></span>
                            <?if($item['MIN_PRICE']):?>
                                <span class="ds-sale">%</span>
                            <?endif;?>
                        </div>
                    </a>
                </div>
            <?endforeach;?>
            <?if($arResult["NAV_RESULT"]->nEndPage > 1 && $arResult["NAV_RESULT"]->NavPageNomer<$arResult["NAV_RESULT"]->nEndPage):?>
                <div class="ds-catalog-recommend__btn" data-action="recommend__add"  data-show-more="<?=$arResult['NAV_RESULT']->NavNum?>" data-next-page="<?=($arResult['NAV_RESULT']->NavPageNomer + 1)?>" data-max-page="<?=$arResult['NAV_RESULT']->nEndPage?>">
                    <span class="ds-btn ds-btn--light">Показать еще</span>
                </div>
            <?endif?>
        </div>
    </div>
<?endif?>

