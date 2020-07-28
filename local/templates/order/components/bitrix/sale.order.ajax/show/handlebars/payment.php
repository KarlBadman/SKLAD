<div class="ds-checkout-form__header">
    <h4>Оплата</h4>
</div>
{{#iff USER_VALS.PERSON_TYPE_ID '==' '1'}}
    {{#iff PARAMS.SHOW_PROMO '==' 'Y'}}
        <div class="form-group">
            <label for="promo">Промокод</label>
            <div class="form-group__row promo-info {{#if COUPON.OK}}active {{else}} {{#if COUPON.APPLIED}}error{{/if}} {{/if}}">
                <div class="promo-info-input">
                    <input class="inp-big js-promo" type="text" id="promo" name="promo" placeholder="Промокод"
                           {{#if POST.promo}} value="{{POST.promo}}"
                           {{else}}
                           value="{{COUPON.COUPON}}"
                           {{/if}}>
                    <div class="promo-error-text">Промокод не найден</div>
                </div>
                <button id="promo-submit" class="ds-btn ds-btn--default" {{#if COUPON.OK }} disabled="disabled" {{/if}}>Применить</button>
            </div>
        </div>
    {{/iff}}
    <div class="block-delivery-name">
        <p><strong>Бесплатная доставка</strong></p>
        <p>Ознакомьтесь с <span class="block-delivery-name-link js-ds-modal" data-href="{{SITE_TEMPLATE_PATH}}/include_areas/free-delivery.html" data-ds-modal-width="580">условиями акции</span>.</p>
    </div>
{{/iff}}
<div class="payment-info ">
    <div class="payment-info__principal">
        {{#each PAY_SYSTEM as |value key|}}
            {{#iff key '==' 3 }}
                </div>
                <div class="payment-info__secondary {{#if ../PAY_SYSTEM_OPEN}}open{{/if}}">
            {{/iff}}
            <label {{#iff ANOTHER '==' 'Y'}} data-name="payment-another"{{/iff}} class="payment-info__item js-payment {{#if CHECKED}}active{{/if}}" for="ID_PAY_SYSTEM_ID_{{ID}}" data-order-page="payment-option-field">
                <input type="radio" id="ID_PAY_SYSTEM_ID_{{ID}}" name="PAY_SYSTEM_ID" value="{{ID}}" {{#if CHECKED}}checked="checked"{{/if}}>
                <div class="payment-info__icon">
                    <span class="icon-svg ic-aapayment ic-{{lowercase CODE}}"></span>
                </div>
                <div class="payment-info__info">
                    <span class="header">{{NAME}}</span>
                    <span class="subheader">
                             {{#if DISCOUNT}}
                                 <span class="green-400">{{DISCOUNT}}</span>
                            {{/if}}
                        <span class="small-text">{{DESCRIPTION}}</span>
                    </span>
                    {{#iff ANOTHER '==' 'Y'}}
                        <div class="payment-info__other-person">
                            <div class="form-group">
                                <label>телефон получателя:</label>
                                <input {{#if CHECKED}}required=""{{/if}}  class="inp-big" type="tel" name="ORDER_PROP_{{../CUSTOM_PROPS.PAYER_PHONE.ID}}" placeholder="{{../CUSTOM_PROPS.PAYER_PHONE.DESCRIPTION}}" value="{{../CUSTOM_PROPS.PAYER_PHONE.VALUE}}">
                            </div>
                            <div class="form-group">
                                <label>эл.почта получателя счёта:</label>
                                <input {{#if CHECKED}}required=""{{/if}}  class="inp-big" type="text" name="ORDER_PROP_{{../CUSTOM_PROPS.PAYER_EMAIL.ID}}" placeholder="{{../CUSTOM_PROPS.PAYER_EMAIL.DESCRIPTION}}" value="{{../CUSTOM_PROPS.PAYER_EMAIL.VALUE}}">
                            </div>
                        </div>
                    {{/iff}}
                </div>
            </label>
        {{/each}}
    </div>

    {{#iff PAY_SYSTEM_OPEN '!=' true}}
        {{#iff (get_length PAY_SYSTEM) '>' 3}}
        <div class="payment-info__btn more js-payment-more-btn">Больше способов оплаты</div>
        {{/iff}}
    {{/iff}}
</div>