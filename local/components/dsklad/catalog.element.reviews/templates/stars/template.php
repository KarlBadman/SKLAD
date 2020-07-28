<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!empty($arResult['REVIEWS']['result']['rating'])) { ?>
    <div class="ds-rating-stars" data-rate="<?=round($arResult['REVIEWS']['result']['rating'])?>"><span></span><span></span><span></span><span></span><span></span></div>
    <div class="ds-rating-label">на Google</div>
<? } ?>