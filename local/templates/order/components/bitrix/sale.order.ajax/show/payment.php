<div class="ds-checkout-form__item" id="checkout-payment" data-block-name="payment">
    <div class="ds-checkout-form__header">
        <h4>Оплата</h4>
    </div>
    <?if($arResult['USER_VALS']['PERSON_TYPE_ID'] == 1):?>
        <?if($arParams['SHOW_PROMO'] =='Y'):?>
            <div class="form-group">
                <label for="promo">Промокод</label>
                <div class="form-group__row promo-info <?if($arResult['COUPON']['OK']):?>active<?endif;?>">
                    <div class="promo-info-input">
                        <input class="inp-big js-promo" type="text" id="promo" name="promo" placeholder="Промокод" value="<?if($arResult['POST']['promo']){echo $arResult['POST']['promo'];}else{echo $arResult['COUPON']['COUPON'];}?>">
                        <div class="promo-error-text">Промокод не найден</div>
                    </div>
                    <button id="promo-submit" class="ds-btn ds-btn--default" <?if($arResult['COUPON']['OK']):?>disabled="disabled"<?endif;?>>Применить</button>
                </div>
            </div>
        <?endif;?>
        <div class="block-delivery-name">
            <p><strong>Бесплатная доставка</strong></p>
            <p>Ознакомьтесь с <span class="block-delivery-name-link js-ds-modal" data-href="<?=SITE_TEMPLATE_PATH?>/include_areas/free-delivery.html" data-ds-modal-width="580">условиями акции</span>.</p>
        </div>
    <?endif;?>
    <div class="payment-info ">
        <div class="payment-info__principal">
        <? foreach ($arResult['PAY_SYSTEM'] as $key => $paysystem):
                if($key == 3){?>
                </div
                ><div class="payment-info__secondary">
                <?}?>
                <label <?if($paysystem['ANOTHER'] == 'Y'):?>data-name="payment-another"<?endif;?> class="payment-info__item js-payment <?if($paysystem['CHECKED'] == 'Y'):?>active<?endif?>" for="ID_PAY_SYSTEM_ID_<?=$paysystem['ID']?>" data-order-page="payment-option-field">
                <input type="radio" id="ID_PAY_SYSTEM_ID_<?=$paysystem['ID']?>" name="PAY_SYSTEM_ID" value="<?=$paysystem['ID']?>" <?if($paysystem['CHECKED'] == 'Y'):?>checked="checked"<?endif?>>
                <div class="payment-info__icon">
                    <span class="icon-svg ic-aapayment ic-<?=strtolower($paysystem['CODE'])  ?>"></span>
                </div>
                <div class="payment-info__info">
                    <span class="header"><?=$paysystem['NAME']?></span>
                    <span class="subheader">
                             <?if($paysystem['DISCOUNT']):?>
                        <span class="green-400"><?=$paysystem['DISCOUNT'];?></span>
                        <?endif;?>
                        <span class="small-text"><?=$paysystem['DESCRIPTION']?></span>
                    </span>
                    <?if($paysystem['ANOTHER'] == 'Y'):?>
                    <div class="payment-info__other-person">
                        <div class="form-group">
                            <label>телефон получателя:</label>
                            <input <?if($paysystem['CHECKED'] == 'Y'):?>required=""<?endif?> class="inp-big" type="tel" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['PAYER_PHONE']['ID']?>" placeholder="<?=$arResult['CUSTOM_PROPS']['PAYER_PHONE']['DESCRIPTION']?>" value="<?=$arResult['CUSTOM_PROPS']['PAYER_PHONE']['VALUE']?>">
                        </div>
                        <div class="form-group">
                            <label>эл.почта получателя счёта:</label>
                            <input <?if($paysystem['CHECKED'] == 'Y'):?>required=""<?endif?> class="inp-big" type="text" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['PAYER_EMAIL']['ID']?>" placeholder="<?=$arResult['CUSTOM_PROPS']['PAYER_EMAIL']['DESCRIPTION']?>" value="<?=$arResult['CUSTOM_PROPS']['PAYER_EMAIL']['VALUE']?>">
                        </div>
                    </div>
                    <?endif;?>
                </div>
                </label>
        <?endforeach;?>
        </div>
        <? if(count($arResult['PAY_SYSTEM'])>3){ ?>
            <div class="payment-info__btn more js-payment-more-btn">Больше способов оплаты</div>
        <?}?>
    </div>
</div>
