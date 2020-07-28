    <div class="ds-checkout-form__header">
        <h4>Доставка</h4>
    </div>
    <div class="delivery-info">
        {{#each DELIVERY}}
            <div data-delivery-id="{{ID}}" class="delivery-info__item {{#if CHECKED}}active{{/if}}" data-order-page="delivery-option-field"
                 {{#iff TYPE '==' 'POINT'}}
                data-width="812"
                {{else}}
                data-width="412"
                {{/iff}}
                >
                <div class="delivery-info__header {{#iff TYPE '!=' 'STOCK'}}js-checkout-modal{{/iff}}" data-target="{{lowercase TYPE}}-delivery">
                    <input
                        id="{{TYPE}}"
                        class="js-delivery-radio"
                        type="radio" name="DELIVERY_ID"
                        value="{{ID}}"
                        data-parsley-errors-messages-disabled data-parsley-errors-container="#checkout-delivery"
                        data-input-type="{{TYPE}}"
                        {{#if CHECKED}}
                        checked="checked"
                        {{/if}}
                    >
                    <label for="{{TYPE}}" class="delivery_label" data-lable-name="{{TYPE}}" data-lable-id="{{TYPE}}">
                        <span class="header">{{NAME}}
                           {{#iff PRICE '>' 0}}
                               <span class="ds-price">{{PRICE}}</span>
                           {{else}}
                               {{#iff DPD_STATUS '==' 0}}
                                   {{#iff TYPE '==' 'COURIER'}}
                                       <span class="ds-price-warning">Уточните по тел. 8 800 777-12-74</span>
                                   {{else}}
                                       <span class="ds-price-free">Бесплатно</span>
                                   {{/iff}}
                               {{else}}
                                   {{#if ERROR}}
                                         <span class="ds-price-warning">Уточните по тел. 8 800 777-12-74</span>
                                   {{else}}
                                        <span class="ds-price-free">Бесплатно</span>
                                   {{/if}}
                               {{/iff}}
                           {{/iff}}
<!--                            <span class="ds-price ds-price--discount">790</span>-->
                        </span>
                        {{#iff CHECKED '!=' 'Y'}}
                            <span class="subheader">{{DESCRIPTION}}</span>
                            {{#if PERIOD_TEXT}}
                                <span class="subheader">Примерный срок доставки:<strong> {{PERIOD_TEXT}} дней</strong></span>
                            {{/if}}
                        {{/iff}}
                        {{#iff CHECKED '==' 'Y'}}
                            {{#iff TYPE '==' 'STOCK'}}
                                <span class="subheader">{{DESCRIPTION}}</span>
                            {{/iff}}
                        {{/iff}}
                        {{#iff CHECKED '!=' 'Y'}}
                            {{#iff TYPE '==' 'COURIER'}}
                                <div class="delivery-result-add-info">{{DEY_WORK}}: {{TIME_WORK}}</div>
                            {{/iff}}
                        {{/iff}}
                    </label>
                </div>
                {{#if CHECKED}}
                    {{#iff TYPE '==' 'POINT'}}
                        <div class="delivery-info-result">
                            <span class="delivery-result-header">Выбранный пункт выдачи:</span>
                            <span class="delivery-result-address">{{TERMINAL_INFO.ADDRESS}}</span>
                            <div class="delivery-result-add-info">
                                {{#each TERMINAL_INFO.DATA}}
                                    <span>{{weekDays}}: {{workTime}}</span>
                                {{/each}}
                                {{#iff TERMINAL_INFO.IS_TERMINAL '==' 'Y'}}
                                    <span>Срок хранения посылки 14 дней</span>
                                {{/iff}}
                                {{#iff TERMINAL_INFO.IS_TERMINAL '==' 'N'}}
                                    <span>Срок хранения посылки 7 дней</span>
                                {{/iff}}
                                <span>Для получения заказа необходимо иметь при себе паспорт</span>
                            </div>
                            <span class="ds-btn ds-btn--default js-delivery-change" data-target="{{lowercase TYPE}}-delivery" data-ds-modal-width="812">Выбрать другой</span>
                        </div>
                    {{/iff}}
                    {{#iff TYPE '==' 'COURIER'}}
                        <div class="delivery-info-result">
                            {{#if ../NO_ADDRESSES}}
                                <div class="delivery-result-add-info">{{DEY_WORK}}: {{TIME_WORK}}</div>
                                <span data-target="{{lowercase TYPE}}-delivery" class="ds-btn ds-btn--default js-delivery-change">Укажите адрес</span>
                            {{else}}
                                <span class="delivery-result-header">Заказ приедет по адресу:</span>
                                <span class="delivery-result-address">{{../CUSTOM_PROPS.CITY.VALUE}}, {{../CUSTOM_PROPS.ADDRESS.VALUE}} {{../CUSTOM_PROPS.ROOM.VALUE}}</span>
                                <div class="delivery-result-add-info">{{DEY_WORK}}: {{TIME_WORK}}</div>
                                <div class="delivery-result-add-info">
                                    {{#if SERVICES_OK}}
                                        <span class="header">Дополнительные услуги:</span>
                                        <span>
                                            {{SERVICE_NAME_OK}}
                                        </span>
                                    {{/if}}
                                </div>
                            <span class="ds-btn ds-btn--default js-delivery-change" data-target="{{lowercase TYPE}}-delivery">Изменить</span>
                           {{/if}}
                        </div>
                    {{/iff}}
                {{/if}}
            </div>
        {{/each}}
    </div>
