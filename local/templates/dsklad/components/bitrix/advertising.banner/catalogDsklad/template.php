<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$frame = $this->createFrame()->begin();?>
<?if(!empty($arResult['BANNERS_PROPERTIES']) && count($arResult['BANNERS_PROPERTIES']) > 0):?>
    <div class="ds-catalog-owl owl-carousel owl-theme js-catalog-owl-slider">
        <?foreach ($arResult['BANNERS_PROPERTIES'] as $arBanner):?>
            <a href="<?= $arBanner['URL'] ?>">
                <?if($arParams['MOBILE']):?>
                    <img src="<?=$arBanner['IMAGE_SRC'];?>" class="banner-mobile" alt="">
                <?else:?>
                    <img src="<?=$arBanner['IMAGE_SRC'];?>" class="banner-desktop" alt="">
                <?endif;?>
            </a>
        <?endforeach?>
    </div>
<?endif;?>
<?$frame->end();?>