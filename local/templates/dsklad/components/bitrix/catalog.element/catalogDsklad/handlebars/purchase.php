<div class="good-info__item good-info--price">
    <div class="ds-price" data-item-price="{{OFFERS_ACTIVE.RECOMMEND_PRICE}}">{{NF OFFERS_ACTIVE.RECOMMEND_PRICE}}</div>
    {{#if OFFERS_ACTIVE.PRICES_BASE.VALUE}}
    {{#FIFNOTEQ OFFERS_ACTIVE.RECOMMEND_PRICE OFFERS_ACTIVE.PRICES_BASE.VALUE}}
    <div class="ds-price ds-price--old">{{NF OFFERS_ACTIVE.PRICES_BASE.VALUE}}</div>
    {{/FIFNOTEQ}}
    {{/if}}
</div>
{{#iff OFFERS_ACTIVE.CATALOG_QUANTITY '>' '0'}}
{{log OFFERS_ACTIVE.QUANTITY_STRING}}
    <div class="good-info__item good-info--availability">В наличии —&nbsp;<span class="{{OFFERS_ACTIVE.QUANTITY_STRING.COLOR_CLASS}}">{{OFFERS_ACTIVE.QUANTITY_STRING.TEXT}}</span></div>
{{else}}
    {{#if OFFERS_ACTIVE.PROPERTIES.ARRIVAL_DATE.VALUE}}
        <div class="good-info__item good-info--waiting"><span>Ожидается {{OFFERS_ACTIVE.PROPERTIES.ARRIVAL_DATE.VALUE}}</span></div>
    {{else}}
        {{#if ARRIVAL_DATE.VALUE}}
            <div class="good-info__item good-info--waiting"><span>Ожидается {{ARRIVAL_DATE.VALUE}}</span></div>
        {{else}}
            <div class="good-info__item good-info--availability"><span class="{{OFFERS_ACTIVE.QUANTITY_STRING.COLOR_CLASS}}">{{OFFERS_ACTIVE.QUANTITY_STRING.TEXT}}</span></div>
        {{/if}}
   {{/if}}
{{/iff}}
{{#if PARAMS.USE_PRODUCT_QUANTITY}}
    <div class="good-info__item good-info--count">
        {{#if OFFERS_ACTIVE.DISCOUNT_QUANTITY}}
            {{#iff OFFERS_ACTIVE.DISCOUNT_QUANTITY '<=' OFFERS_ACTIVE.RECOMMEND_QUANTITY}}
                <span class="count-title-sale success">
                    <div class="count-title-sale__icon"></div>
                    <span>Скидка на {{OFFERS_ACTIVE.DISCOUNT_QUANTITY}} шт. применена</span>
                </span>
            {{else}}
                <div class="count-title-sale">
                    <div class="count-title-sale__icon"></div>
                    <span>Скидка при заказе <br>от {{OFFERS_ACTIVE.DISCOUNT_QUANTITY}} шт.</span>
                </div>
           {{/iff}}
        {{else}}
            <span class="count-title">количество:</span>
        {{/if}}

        <div class="ds-count">
            <button class="icon-svg ic-count-minus js-count-minus"></button>
            <input class="inp-count js-number" type="number" min="1" name="quantityProduct" data-item-aproperty="quantity" value="{{OFFERS_ACTIVE.RECOMMEND_QUANTITY}}">
            <button class="icon-svg ic-count-plus js-count-plus"></button>
        </div>
    </div>
{{/if}}
<div class="good-info__item good-info--btn" data-gaitem-id="{{OFFERS_ACTIVE.ID}}">
    <div class="ds-info-btn">
        {{#iff OFFERS_ACTIVE.CATALOG_QUANTITY '<' '1'}}
            {{#if PREPAYMENT.CHECK}}
                <button class="ds-btn ds-btn--default-big ds-btn--full js-add-to-basket" data-product-id="{{OFFERS_ACTIVE.ID}}" data-ga-analys-btn="basket-add-item-preorder">Оформить предзаказ</button>
            {{else}}
                <button class="ds-btn ds-btn--default-big ds-btn--full js-add-to-basket" data-product-id="{{OFFERS_ACTIVE.ID}}" data-ga-analys-btn="basket-add-item">Добавить в корзину</button>
            {{/if}}
        {{else}}
            <button class="ds-btn ds-btn--default-big ds-btn--full js-add-to-basket" data-product-id="{{OFFERS_ACTIVE.ID}}" data-ga-analys-btn="basket-add-item">Добавить в корзину</button>
       {{/iff}}
        <div class="icon-svg ic-favorite_off ds-favorite-btn js-add-to-favorite {{#if PRODUCT.FAVORITE}}added{{/if}}" data-product-id="{{PRODUCT.ID}}" data-ga-analys-btn="favorite"></div>
    </div>
</div>
<div class="good-info__item good-info--btn">
    <button class="ds-btn ds-btn--secondary ds-btn--full js-ds-modal ya-detail-one-click" data-href="/local/templates/dsklad/components/bitrix/catalog.element/catalogDsklad/modal/incl-quick-order.html" data-ds-modal-width="412">Оформить в 1 клик</button>
</div>