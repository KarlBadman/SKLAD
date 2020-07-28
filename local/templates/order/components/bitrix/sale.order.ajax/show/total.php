<div class="ds-checkout__total-price">
    <div class="total-price total-price--checkout" data-order-info="order_info">
        <div class="total-price__content" data-block-name="total">
            <div class="total-price__row">
                <div class="total-price__col">Товары</div>
                <div class="total-price__order-detail"><span class="small-text"> <?=$arResult['COUNT_PRODUCT'];?> шт</span></div>
                <div class="total-price__col"><span class="ds-price"><?=number_format($arResult['TOTAL_PRICE_NO_SERVICE'],0,'.',' ')?></span></div>
            </div>
            <div class="total-price__row">
                <div class="total-price__col">Скидка</div>
                <div class="total-price__col"><span class="ds-price"><?=number_format($arResult['TOTAL_DISCOUNT_PRICE'],0,'.',' ')?></span></div>
            </div>
            <?if($arResult['ORDER_DATA']['SERVICE_PRICE'] > 0):?>
                <div class="total-price__row">
                    <div class="total-price__col">Гарантия</div>
                    <div class="total-price__order-detail"><span class="small-text"> <?=$arResult['WARRANTY_COUNT'];?> шт</span></div>
                    <div class="total-price__col"><span class="ds-price"><?=number_format($arResult['SERVICE_PRICE'],0,'.',' ')?></span></div>
                </div>
            <?endif;?>
            <?if($arResult['ERROR_DELIVERY'] != 'Y'):?>
                <div class="total-price__row">
                    <div class="total-price__col">Доставка</div>
                    <div class="total-price__col"><span class="ds-price" data-order-shipping="<?=$arResult['ORDER_DATA']['PRICE_DELIVERY']?>"><?=number_format($arResult['ORDER_DATA']['PRICE_DELIVERY'],0,'.',' ')?></span></div>
                </div>
            <?endif;?>
            <div class="total-price__row total-price__row--summ">
                <div class="total-price__col"><span class="ds-price ds-price--total" data-order-revenue="<?=$arResult['ORDER_DATA']['PRICE'];?>"><?=number_format($arResult['ORDER_DATA']['PRICE'],0,'.',' ')?></span>
                </div>
            </div>
            <div class="total-price__buttons">
                <input class="ds-btn ds-btn--full ds-btn--success js-checkout-validate" data-ga-analys-btn="order-submit" type="button" value="Оформить заказ">
            </div>
        </div>
        <div class="total-price__add-info"><span class="small-text">Нажимая на кнопку, вы подтверждаете своё совершеннолетие, соглашаетесь на обработку персональных данных в соответствии с<a href="/public_offer/"> Условиями</a>, а также с<a href="/public_offer/"> Условиями продажи.</a></span></div>
        <div class="total-price__phone"><a href="tel:88007771274">8 800 777-12-74</a><span>Справочная служба (с 9 до 21)</span></div>
    </div>
</div>
