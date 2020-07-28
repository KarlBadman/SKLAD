        <div class="ds-modal__header">
            <h5>Курьерская доставка</h5>
            <span class="icon-svg ic-close ds-modal-close js-ds-modal-close" onclick="purepopup.closePopup();"></span>
        </div>
        <div class="ds-modal__body">
            <div class="form-group">
                <label for="courier-delivery-address">Адрес доставки</label>
                <span class="form-group__item">
                    <span class="icon-svg ic-input-clean js-input-clean"></span>
                    <input class="inp-big" id="courier-delivery-address" data-name="is_address" type="text" name="ORDER_PROP_{{CUSTOM_PROPS.ADDRESS.ID}}" value="{{CUSTOM_PROPS.ADDRESS.VALUE}}">
                </span>
            </div>
            <div class="form-group form-group--m-20 {{#iff CUSTOM_PROPS.ROOM.VALUE.length '==' 0}} hidden {{/iff}} js-form-office">
                <label for="courier-delivery-office">Квартира / офис</label>
                <span class="form-group__item form-group__item--small">
                    <span class="icon-svg ic-input-clean js-input-clean"></span>
                    <input class="inp-small" id="courier-delivery-office" type="number" name="ORDER_PROP_{{CUSTOM_PROPS.ROOM.ID}}" value="{{CUSTOM_PROPS.ROOM.VALUE}}">
                </span>
            </div>
            {{#if DELIVERY_COURIER.EXTRA_SERVICES_ARR}}
                <div class="courier-services">
                    <h5>Услуги</h5>
                   {{#each DELIVERY_COURIER.EXTRA_SERVICES_ARR}}
                        <div class="courier-services__item">
                            <p>{{NAME}}</p>
                            <span class="small-text">{{DESCRIPTION}}</span>
                            <div class="courier-services__price-btn">
                                <span {{#iff PARAMS.PRICE '>' 0}}class="ds-price" {{/iff}}>
                                    {{#iff PARAMS.PRICE '>' 0}}{{PARAMS.PRICE}}{{/iff}}
                                </span>
                                {{#if CHECKED}}
                                    <label class="ds-btn ds-btn--light js-courier-delivery-add
                                     {{#iff CODE '==' 'D_UP_LIFT'}}
                                        js-courier-delivery-floor
                                     {{/iff}}
                                     {{#iff CODE '==' 'D_UP_KGT'}}
                                        js-courier-delivery-floor
                                     {{/iff}}
                                     active">
                                        <input checked="checked" type="hidden" value="Y" name="DELIVERY_EXTRA_SERVICES[{{../DELIVERY_COURIER.ID}}][{{ID}}]">
                                        <span class="delivery-checked-text">Удалить</span>
                                    </label>
                                {{else}}
                                    <label class="ds-btn ds-btn--light js-courier-delivery-add
                                     {{#iff CODE '==' 'D_UP_LIFT'}}
                                        js-courier-delivery-floor
                                     {{/iff}}
                                     {{#iff CODE '==' 'D_UP_KGT'}}
                                        js-courier-delivery-floor
                                     {{/iff}}
                                    ">
                                        <input type="hidden" value="N" name="DELIVERY_EXTRA_SERVICES[{{../DELIVERY_COURIER.ID}}][{{ID}}]">
                                        <span class="delivery-checked-text">Добавить</span>
                                    </label>
                                {{/if}}
                            </div>
                        </div>
                    {{/each}}
                </div>
            {{/if}}
            <span data-delivery-id="{{DELIVERY_COURIER.ID}}" class="ds-btn ds-btn--default ds-btn--full js-delivery-here delivery-courier-ok">Привезти сюда</span>
        </div>

