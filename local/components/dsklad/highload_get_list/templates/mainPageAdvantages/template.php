<? if(count($arResult['ITEMS']) > 0) {?>
<div class="advantage-list">
    <div class="advantage-list__header">
        <h2>Наши преимущества</h2>
    </div>
    <div class="advantage-list__content">
        <?foreach ($arResult['ITEMS'] as $key => $arItem) : ?>
            <div class="advantage-item">
                <div class="advantage-item__icon advantage-item__icon--<?=$arItem['UF_PREFIX']?>"><span class="icon-svg ic-<?=$arItem['UF_PREFIX']?>"></span></div>
                <h4><?=$arItem['UF_HEADER']?></h4>
                <p><?=$arItem['UF_TEXT']?></p>
            </div>
        <?endforeach;?>
    </div>
</div>
<? } ?>