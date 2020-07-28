<div class="spinner-target">
    {{#each ITEMS.AnDelCanBuy}}
        <div class="ds-basket-good" data-order-page="basket-item-field" data-item-name="{{NAME}}" data-item-id="{{PRODUCT_ID}}" data-item-price="{{NF FULL_PRICE}}">
            <div class="ds-basket-good__img"><a href="{{DETAIL_PAGE_URL}}"><img src="{{PHOTO_PRODUCT}}" alt=""></a></div>
            <div class="ds-basket-good__price-one"><span class="ds-price ds-price--one">{{NF FULL_PRICE}}</span></div>
            <div class="ds-basket-good__count">
                <div class="ds-basket-count">
                    <button class="icon-svg ic-count-minus js-count-minus"></button>
                    <input class="inp-count js-number"
                           type="number"
                           min="1"
                           name="quantity"
                           value="{{QUANTITY}}"
                           data-gaproduct-quantity="{{QUANTITY}}"
                           data-productID="{{ID}}"
                    >
                    <button class="icon-svg ic-count-plus js-count-plus"></button>
                </div>
            </div>
            <div class="ds-basket-good__price">
                <span class="ds-price">{{NF SUM_VALUE}}</span>
                {{#iff OLD_PRICE '>' FULL_PRICE}}
                    <span class="ds-price ds-price--old">{{NF OLD_PRICE}}</span>
                {{/iff}}
            </div>

            <div class="ds-basket-good__add-info">
                {{#iff QUANTITY_BEFORE_DISCOUNT '>' 0}}
                    <div class="ds-basket-add-more fadein"><span>Добавьте еще {{QUANTITY_BEFORE_DISCOUNT}} шт. и получите скидку <span class="ds-price"> {{NF DIOPOSE_DISCOUNT_PRICE}}</span> </span></div>
                {{/iff}}
            </div>

            <div class="ds-basket-good__descr"><a href="{{DETAIL_PAGE_URL}}">{{NAME}}</a></div>
            <div class="ds-basket-good__service-info">
                {{#iff AVAILABLE_QUANTITY '==' 0}}
                    <div class="ds-service-info quantity-order">Предзаказ — предоплата 50%</div>
                {{/iff}}
                {{#if ../SERVICE_OK}}
                    {{#if NO_WARANTY}}
                        <div class="ds-service-info warranty-status">Стандартная гарантия</div>
                    {{else}}
                        <div class="ds-service-info warranty-status warranty-full">Расширенная гарантия</div>
                    {{/if}}
                {{else}}
                    <div class="ds-service-info warranty-status">Стандартная гарантия</div>
                {{/if}}
                <div class="ds-service-info">Арт.:&nbsp;<span> {{PROPERTY_CML2_ARTICLE_VALUE}}</span></div>
                <div class="ds-service-info">Цвет:&nbsp;<span> {{PROPERTY_KOD_TSVETA_VALUE}}</span></div>
            </div>
            <div class="ds-basket-good__del" data-ga-analys-btn="basket-delete-item">
                <div class="ds-basket-del js-basket-remove" data-productId="{{ID}}">
                    <div class="icon-svg ic-basket-remove"></div><span>Удалить</span>
                </div>
            </div>
        </div>
    {{/each}}
    {{#if PREORDER}}
    <div class="ds-basket__preorder-info">
        <p>В&nbsp;вашем заказе есть товары со&nbsp;статусом предзаказ. Эти товары появятся на&nbsp;складе в&nbsp;ближайшее
            время, оформляя на&nbsp;них заказ, вы&nbsp;бронируете товар и&nbsp;оплачиваете&nbsp;50% от&nbsp;их&nbsp;стоимости.
            Остальные&nbsp;50% можно внести, когда товары появятся на&nbsp;складе.</p>
    </div>
    {{/if}}
</div>