<div class="total-price">
    <div class="total-price__content">
        <div class="total-price__row">
            <div class="total-price__col">Товары</div>
            <div class="total-price__order-detail"><span class="small-text"> {{PRODUCT_QUANTITY}} шт</span></div>
            <div class="total-price__col"><span class="ds-price">{{NF OLD_TOTAL_PRICE}}</span></div>
        </div>
        <div class="total-price__row">
            <div class="total-price__col">Скидка</div>
            <div class="total-price__col"><span class="ds-price">{{NF TOTAL_SUM_DISCOUNT}}</span></div>
        </div>
        {{#each BASKET_SERVICE}}
            {{#if CHECkED}}
                <div class="total-price__row js-warranty-total">
                    <div class="total-price__col">Гарантия</div>
                    <div class="total-price__order-detail"><span class="small-text"> {{../PRODUCT_QUANTITY}} шт</span></div>
                    <div class="total-price__col"><span class="ds-price">{{NF PRICE}}</span></div>
                </div>
            {{/if}}
        {{/each}}
        <div class="total-price__row total-price__row--summ">
            <div class="total-price__col">Итого без доставки</div>
            <div class="total-price__col"><span class="ds-price ds-price--total" data-basket-revenue="{{allSum}}">{{NF allSum}}</span>
            </div>
        </div>
        <div class="total-price__buttons">
            <a class="ds-btn ds-btn--success ya-to-order" href="/order/">К оформлению</a>
            <button class="ds-btn ds-btn--light ds-btn--full js-ds-modal ya-one-click" data-href="/local/templates/dsklad/components/bitrix/sale.basket.basket/show/templates/incl-quick-order.html" data-ds-modal-width="412" data-ga-analys-btn="order-one-click-submit">Оформить в 1 клик</button>
        </div>
    </div>
    <div class="total-price__add-info"><span class="small-text">Доступные способы и время доставки можно выбрать при оформлении заказа.</span></div>
</div>
