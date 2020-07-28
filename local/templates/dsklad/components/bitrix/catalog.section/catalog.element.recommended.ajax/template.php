<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if(!empty($arResult['ITEMS'])):?>
    <?foreach ($arResult['ITEMS'] as $item):?>
    <div class="ds-catalog-recommend__item">
        <a href="<?=$item['DETAIL_PAGE_URL']?>">
            <img src="<?=$item['PREVIEW_PICTURE']['SRC']?>" alt="<?=$item['PREVIEW_PICTURE']['ALT']?>">
            <p><?=$item['NAME']?></p>
            <div class="ds-price-sale">
                <span class="ds-price"><?=number_format($item['PRICE'],0,'.',' ')?></span>
                <?if($item['MIN_PRICE']):?>
                    <span class="ds-sale">%</span>
                <?endif;?>
            </div>
        </a>
    </div>
    <?endforeach;?>
    <?if($arResult["NAV_RESULT"]->nEndPage > 1 && $arResult["NAV_RESULT"]->NavPageNomer<$arResult["NAV_RESULT"]->nEndPage):?>
        <div class="ds-catalog-recommend__btn" data-action="recommend__add"  data-show-more="<?=$arResult["NAV_RESULT"]->NavNum?>" data-next-page="<?=($arResult["NAV_RESULT"]->NavPageNomer + 1)?>" data-max-page="<?=$arResult["NAV_RESULT"]->nEndPage?>">
            <span class="ds-btn ds-btn--light">Показать еще</span>
        </div>
    <?endif?>
<?endif?>



