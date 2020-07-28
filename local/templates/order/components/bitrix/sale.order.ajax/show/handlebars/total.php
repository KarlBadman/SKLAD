    <div class="total-price__row">
        <div class="total-price__col">Товары</div>
        <div class="total-price__order-detail"><span class="small-text"> {{COUNT_PRODUCT}} шт</span></div>
        <div class="total-price__col"><span class="ds-price">{{NF TOTAL_PRICE_NO_SERVICE}}</span></div>
    </div>
    <div class="total-price__row">
        <div class="total-price__col">Скидка</div>
        <div class="total-price__col"><span class="ds-price">{{NF TOTAL_DISCOUNT_PRICE}}</span></div>
    </div>
    {{#if SERVICE_PRICE}}
        <div class="total-price__row">
            <div class="total-price__col">Гарантия</div>
            <div class="total-price__order-detail"><span class="small-text"> {{WARRANTY_COUNT}} шт</span></div>
            <div class="total-price__col"><span class="ds-price">{{NF SERVICE_PRICE}}</span></div>
        </div>
    {{/if}}
    {{#iff ERROR_DELIVERY '!=' 'Y'}}
        <div class="total-price__row">
            <div class="total-price__col">Доставка</div>
            <div class="total-price__col"><span class="ds-price" data-order-shipping="{{ORDER_DATA.PRICE_DELIVERY}}">{{NF ORDER_DATA.PRICE_DELIVERY}}</span></div>
        </div>
    {{/iff}}
    <div class="total-price__row total-price__row--summ">
        <div class="total-price__col"><span class="ds-price ds-price--total" data-order-revenue="{{ORDER_DATA.PRICE}}">{{NF ORDER_DATA.PRICE}}</span>
        </div>
    </div>
    <div class="total-price__buttons">
        <input class="ds-btn ds-btn--full ds-btn--success js-checkout-validate" data-ga-analys-btn="order-submit" type="button" value="Оформить заказ">
    </div>
    {{#iff AUTHORIZED '!=' true}}
        {{#iff POST.confirmorder '==''Y'}}
            <div style="color: red">
                Подтвердите номер телефона
            </div>
        {{/iff}}
    {{/iff}}

















