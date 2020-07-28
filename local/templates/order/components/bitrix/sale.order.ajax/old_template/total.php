<fieldset class="total ajaxreload mapreload" data-dpd-status="1">
    <div class="title" id="total_summ_title">
        Итого: <?=$arResult['COUNT_BASKET'];?> <?=getWord3($arResult['COUNT_BASKET'])?>                                    на
        сумму <?=number_format($arResult['PRICE_WITHOUT_DISCOUNT_VALUE'], 0, '.', ' ');?>.–                                </div>
    <div class="digits">
        <div class="calculation">
            <ul id="total_summ">
                <li>Стоимость: <?=number_format($arResult['TOTAL_PRICE'], 0, '.', ' ');?>.–</li>
                <li class="dostavka_price" num="<?=$arResult['DELIVERY_PRICE']?>" data-order-page="shipping-field">Доставка:
                    <span>
                       <?if($arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']]['PRICE'] > 0):?>
                            <?=number_format($arResult['DELIVERY_PRICE'], 0, '.', ' ');?>.–
                        <?else:;?>
                            *
                        <?endif;?>
                    </span>
                </li>
                <li class="text-red">Общая скидка: <?=number_format($arResult['DISCOUNT_PRICE'], 0, '.', ' ');?>.–</li>
                <li class="sum_price" num="<?=$arResult['ORDER_TOTAL_PRICE']?>" data-order-page="summ-field">
                    <b>К оплате: <span><?=number_format($arResult['ORDER_TOTAL_PRICE'], 0, '.', ' ');?>.–</span></b>
                </li>
                <input type="hidden" name="delivery_price" value="<?=$arResult['DELIVERY_PRICE']?>">
            </ul>

            <div class="place clearfix">
                <div class="button-block clearfix">
                    <div class="button-holder">
                        <a class="button type-blue fill size-38 btn_send_order_process" href="javascript:void();" onclick="submitForm('Y'); return false;" id="ORDER_CONFIRM_BUTTON"><?=GetMessage("SOA_TEMPL_BUTTON")?></a>
                        <div class="discript">
                            Заполните все поля
                        </div>
                    </div>
                    <p>
                        Я принимаю условия <a class="border-link" href="/public_offer/">публичной оферты</a> и соглашаюсь на условия обработки <a class="border-link" href="/public_offer/">персональных данных</a>.
                    </p>
                </div>
                <label class="label__widget">
                    <span class="row">
                        <span class="control">
                            <input type="checkbox" name="ORDER_PROP_<?=$arResult['PROP_NOT_CALL_CODE']?>" value="Y" autocomplete="off">
                            <u class="square">
                                <span class="icon__check">
                                    <svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#check"></use></svg>
                                </span>
                            </u>
                        </span>
                        <span class="label">Не звонить для проверки заказа</span>
                    </span>
                </label>
            </div>
        </div>
        <?if(!empty($arResult['JS_DATA']['COUPON_LIST'])){
            $coupon = end($arResult['JS_DATA']['COUPON_LIST']);
            if($coupon['JS_STATUS'] != 'BAD'){
                $couponClass = 'find';
            }else{
                $couponClass = 'notfind';
            }
        }?>
        <div class="promo">
            <div class="promo-text" <?if(!empty($coupon)) echo 'style="display: none;"'; ?>>Есть промокод?</div>
            <div class="promo-form" <?if(!empty($coupon)) echo 'style="display: block;"'; ?>>
                <div class="input <?=$couponClass?>" id="promocode">
                    <input type="text" name="promo" placeholder="Промокод" autocomplete="off" value="<?=$coupon['STATUS_TEXT']?>">
                </div>
                <button type="button" class="button type-blue" id="promo">Применить</button>
            </div>
        </div>
    </div>

    <? if($arResult['ORDER_TOTAL_PRICE'] > \Dsklad\Config::getOption('UF_SUM_ORDER')):?>
        <div class="sum-order__popup">
            <p style="text-align: center; margin-top: 50px;">Заказы больше <?= number_format(\Dsklad\Config::getOption('UF_SUM_ORDER'), 0, '.', ' ') ?> рублей оформляются менеджером при телефонном звонке по номеру
                <span class="no-wrap">
                <?
                $APPLICATION->IncludeFile(
                    SITE_TEMPLATE_PATH.'/include_areas/phone.php',
                    array(),
                    array(
                        'MODE' => 'text'
                    )
                );
                ?>
            </span>
            </p>
        </div>
    <?endif;?>
</fieldset>
