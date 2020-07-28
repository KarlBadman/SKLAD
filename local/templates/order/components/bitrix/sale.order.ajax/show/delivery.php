
<div class="ds-checkout-form__item" id="checkout-delivery" data-block-name="delivery">
    <div class="ds-checkout-form__header">
        <h4>Доставка</h4>
    </div>
    <div class="delivery-info">
        <?foreach ($arResult['DELIVERY'] as $delivery):?>
            <div data-delivery-id="<?=$delivery['ID']?>" class="delivery-info__item <?if($delivery['CHECKED'] == 'Y'):?>active<?endif;?>" data-order-page="delivery-option-field"
                <?if($delivery['TYPE'] == 'POINT'):?>
                data-width="812"
                <?else:?>
                data-width="412"
                <?endif?>
                >
                <div class="delivery-info__header <?if($delivery['TYPE'] != 'STOCK'):?>js-checkout-modal<?endif?>" data-target="<?=strtolower($delivery['TYPE']);?>-delivery">
                    <input
                            id="<?=$delivery['TYPE'];?>"
                            class="js-delivery-radio"
                            type="radio" name="DELIVERY_ID"
                            value="<?=$delivery['ID']?>"
                            data-parsley-errors-messages-disabled data-parsley-errors-container="#checkout-delivery"
                            <?if($delivery['CHECKED'] == 'Y'):?>
                                checked="checked"
                            <?endif;?>
                            data-input-type="<?=$delivery['TYPE'];?>"
                    >
                    <label for="<?=$delivery['TYPE'];?>" class="delivery_label" data-lable-name="<?=$delivery['TYPE'];?>" data-lable-id="<?=$delivery['TYPE'];?>">
                        <span class="header"><?=$delivery['NAME']?>
                                <?if($delivery['PRICE'] > 0):?>
                                    <span class=" ds-price"><?=$delivery['PRICE'];?></span>
                                <?else:?>
                                    <?if (($delivery['DPD_STATUS'] === 0 && $delivery['TYPE'] == 'COURIER') || $delivery['ERROR']) : ?>
                                        <span class="ds-price-warning">Уточните по тел. 8 800 777-12-74</span>
                                    <?else : ?>
                                        <span class="ds-price-free">Бесплатно</span>
                                    <?endif;?>
                                <?endif;?>
<!--                                <span class="ds-price ds-price--discount">790</span>-->
                        </span>
                        <?if($delivery['CHECKED'] != 'Y' || $delivery['TYPE'] == 'STOCK'):?>
                            <span class="subheader"><?=$delivery['DESCRIPTION']?></span>
                            <?if($delivery['PERIOD_TEXT']):?>
                                <span class="subheader">Примерный срок доставки:<strong> <?=$delivery['PERIOD_TEXT']?> дней</strong></span>
                            <?endif;?>
                        <?endif;?>
                        <?if($delivery['CHECKED'] != 'Y'):?>
                            <?if($delivery['TYPE'] == 'COURIER'):?>
                                <div class="delivery-result-add-info"><?=$delivery['DEY_WORK']?>: <?=$delivery['TIME_WORK']?></div>
                            <?endif;?>
                        <?endif;?>
                    </label>
                </div>
                <?if($delivery['CHECKED'] == 'Y'):?>
                    <?if($delivery['TYPE'] == 'POINT'):?>
                        <div class="delivery-info-result">
                            <span class="delivery-result-header">Выбранный пункт выдачи:</span>
                            <span class="delivery-result-address"><?=$delivery['TERMINAL_INFO']['ADDRESS']?></span>
                            <div class="delivery-result-add-info">
                                <?foreach ($delivery['TERMINAL_INFO']['DATA'] as $data):?>
                                    <span data-target="<?=$delivery['TYPE'];?>-delivery"><?=$data['weekDays']?>: <?=$data['workTime']?></span>
                                <?endforeach;?>
                                <?if ($delivery['TERMINAL_INFO']['IS_TERMINAL'] == 'Y') : ?>
                                    <span>Срок хранения посылки 14 дней</span>
                                <?elseif ($delivery['TERMINAL_INFO']['IS_TERMINAL'] == 'N') : ?>
                                    <span>Срок хранения посылки 7 дней</span>
                                <?endif;?>
                                <span>Для получения заказа необходимо иметь при себе паспорт</span>
                            </div>
                            <span class="ds-btn ds-btn--default js-delivery-change" data-ds-modal-width="812">Выбрать другой</span>
                        </div>
                    <?endif;?>
                    <?if($delivery['TYPE'] == 'COURIER'):?>
                        <div class="delivery-info-result">
                            <?if($arResult['NO_ADDRESSES']):?>
                                <div class="delivery-result-add-info"><?=$delivery['DEY_WORK']?>: <?=$delivery['TIME_WORK']?></div>
                                <span data-target="<?=strtolower($delivery['TYPE']);?>-delivery" class="ds-btn ds-btn--default js-delivery-change">Укажите адрес</span>
                            <?else:?>
                                <span class="delivery-result-header">Заказ приедет по адресу:</span>
                                <span class="delivery-result-address"><?=$arResult['CUSTOM_PROPS']['CITY']['VALUE']?>, <?=$arResult['CUSTOM_PROPS']['ADDRESS']['VALUE']?> <?=$arResult['CUSTOM_PROPS']['ROOM']['VALUE']?></span>
                                <div class="delivery-result-add-info"><?=$delivery['DEY_WORK']?>: <?=$delivery['TIME_WORK']?></div>
                                <?if($delivery['SERVICES_OK']):?>
                                    <div class="delivery-result-add-info">
                                        <span class="header">Дополнительные услуги:</span>
                                        <span>
                                            <?=$delivery['SERVICE_NAME_OK']?>
                                        </span>
                                    </div>
                                    <span data-target="<?=strtolower($delivery['TYPE']);?>-delivery" class="ds-btn ds-btn--default js-delivery-change">Изменить</span>
                                <?endif;?>
                            <?endif;?>
                        </div>
                    <?endif;?>
                <?endif;?>
            </div>
        <?endforeach;?>
    </div>
</div>