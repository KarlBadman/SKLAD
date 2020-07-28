<div id="courier-delivery" class="hide" data-block-name="courier_modal">
    <div class="ds-modal__header">
        <h5>Курьерская доставка</h5>
        <span class="icon-svg ic-close ds-modal-close js-ds-modal-close" onclick="purepopup.closePopup();"></span>
    </div>
    <div class="ds-modal__body">
        <div class="form-group">
            <label for="courier-delivery-address">Адрес доставки</label>
            <span class="form-group__item">
                <span class="icon-svg ic-input-clean js-input-clean"></span>
                <input class="inp-big" id="courier-delivery-address" data-name="is_address" type="text" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['ADDRESS']['ID']?>" value="<?=$arResult['CUSTOM_PROPS']['ADDRESS']['VALUE']?>">
            </span>
        </div>
        <div class="form-group form-group--m-20 hidden js-form-office">
            <label for="courier-delivery-office">Квартира / офис</label>
            <span class="form-group__item form-group__item--small">
                <span class="icon-svg ic-input-clean js-input-clean"></span>
                <input class="inp-small" id="courier-delivery-office" type="number" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['ROOM']['ID']?>" value="<?=$arResult['CUSTOM_PROPS']['ROOM']['VALUE']?>">
            </span>
        </div>
        <?if(!empty($arResult['DELIVERY_COURIER']['EXTRA_SERVICES_ARR'])):?>
            <div class="courier-services">
                <h5>Услуги</h5>
                <?foreach ($arResult['DELIVERY_COURIER']['EXTRA_SERVICES_ARR'] as $value):?>
                    <div class="courier-services__item">
                        <p><?=$value['NAME']?></p>
                        <span class="small-text"><?=$value['DESCRIPTION']?></span>
                        <div class="courier-services__price-btn">
                            <span class="ds-price"><?=$value['PARAMS']['PRICE']?></span>
                            <?if($value['CHECkED'] == 'Y'):?>
                                <label class="ds-btn ds-btn--light js-courier-delivery-add <?if($value['CODE'] == 'D_UP_LIFT' || $value['CODE'] == 'D_UP_KGT'):?>js-courier-delivery-floor<?endif?> active">
                                    <input checked="checked" type="hidden" value="Y" name="DELIVERY_EXTRA_SERVICES[<?=$arResult['DELIVERY_COURIER']['ID']?>][<?=$value['ID']?>]">
                                    <span class="delivery-checked-text">Удалить</span>
                                </label>
                            <?else:?>
                                <label class="ds-btn ds-btn--light js-courier-delivery-add <?if($value['CODE'] == 'D_UP_LIFT' || $value['CODE'] == 'D_UP_KGT'):?>js-courier-delivery-floor<?endif?>">
                                    <input type="hidden" value="N" name="DELIVERY_EXTRA_SERVICES[<?=$arResult['DELIVERY_COURIER']['ID']?>][<?=$value['ID']?>]">
                                    <span class="delivery-checked-text">Добавить</span>
                                </label>
                            <?endif;?>
                        </div>
                    </div>
                <?endforeach;?>
            </div>
        <?endif;?>
        <span data-delivery-id="<?=$arResult['DELIVERY_COURIER']['ID']?>" class="ds-btn ds-btn--default ds-btn--full js-delivery-here delivery-courier-ok">Привезти сюда</span>
    </div>
</div>