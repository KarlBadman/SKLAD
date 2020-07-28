<div class="insta-slider"><span class="icon-svg ic-close ds-modal-close" onclick="purepopup.closePopup();"></span>
    <div class="js-ds-slider-arrows"></div>
    <div class="insta-slider-img js-ds-slider">
        <?foreach ($arResult['ITEMS'] as $key => $arItem) : ?>
            <div class="insta-slider-img__item"><img <?=($key==0)? 'src' : 'data-lazy' ;?>="<?=$arItem['UF_POSTORIGIN']?>" alt="<?=$arItem['UF_POPUP_TEXT']?>"/></div>
        <?endforeach;?>
    </div>
</div>