<div class="ds-checkout-consist" data-order-page="basket-area">
    <h5>Состав заказа</h5>
    <?if($arResult['PACK']['QUANTITY'] > 0 && $arResult['PACK']['WEIGHT'] > 0):?>
        <p><?=$arResult['PACK']['QUANTITY']?> <?=$arResult['PACK']['NAME']?><span class="total-weight">Общий вес: <?=$arResult['PACK']['WEIGHT']?> кг.</span></p>
    <?endif;?>
    <?foreach ($arResult['PACK']['ARR_PACK'] as $pack):?>
        <p><?=$pack['QUANTITY'];?> <?=$pack['NAME'];?><span class="total-weight">(ВхШхГ):  <?=$pack['WEIGHT'];?>x<?=$pack['HEIGHT'];?>x<?=$pack['LENGTH'];?> см </span></p>
    <?endforeach;?>
    <?if($arResult['PACK']['QUANTITY'] > 1):?>
        <p>Макс. вес коробки: <?=$arResult['PACK']['WEIGHT_MAX']?> кг.</p>
    <?endif;?>
    <div class="ds-checkout-slider-arrows js-slider-arrows"></div>
    <div class="ds-checkout-slider js-slider-checkout" data-order-page="basket-fieldset">
        <?foreach ($arResult["GRID"]["ROWS"] as $key => $arData):?>
        <div class="ds-checkout-slider__item" data-order-page="basket-item-field" data-item-name="<?=$arData['data']['NAME']?>" data-item-id="<?=$arData['data']['PRODUCT_ID']?>" data-item-price="<?=$arData['data']['BASE_PRICE']?>" data-gaproduct-quantity="<?=$arData['data']['QUANTITY']?>">
                <img src="<?=$arData['data']['PHOTO_PRODUCT'];?>" alt="">
                <?if($arData['data']['QUANTITY'] > 1):?>
                    <span><?=$arData['data']['QUANTITY'];?> шт</span>
                <?endif;?>
            </div>
        <?endforeach;?>
    </div>
</div>

