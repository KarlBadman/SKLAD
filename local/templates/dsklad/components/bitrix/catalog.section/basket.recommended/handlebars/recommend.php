{{#iff COUNT '>' 0}}
    <h4>Рекомендуем</h4>
    <div class="ds-recommend">
        <div class="ds-basket-slider-arrows js-slider-recommend-arrows"></div>
        <div class="ds-basket-slider js-slider-recommend" data-type="arrows">
            {{#each ITEMS}}
                <div class="ds-basket-slider__item">
                    <a href="{{DETAIL_PAGE_URL}}>">
                        <img src="{{PREVIEW_PICTURE.SRC}}" alt="{{PREVIEW_PICTURE.ALT}}">
                    </a>
                    <p>{{NAME}}</p>
                    <div class="ds-price-sale">
                        {{#iff CATALOG_PRICE_2 '>' 0}}
                            <span class="ds-price"> {{NF CATALOG_PRICE_2}}</span>
                            {{#iff CATALOG_PRICE_2 '<' CATALOG_PRICE_1}}
                                <span class="ds-sale">%</span>
                            {{/iff}}
                        {{else}}
                            <span class="ds-price"> {{NF CATALOG_PRICE_1}}</span>
                        {{/iff}}
                    </div>
                    <span class="ds-btn ds-btn--light add-basket" data-productID="{{ID}}">В корзину</span>
                </div>
            {{/each}}
        </div>
    </div>
{{/iff}}

