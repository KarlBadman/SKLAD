    <div class="ds-checkout-form__header">
        <h4>Основное</h4>
        {{#iff USER_VALS.PERSON_TYPE_ID '==' 1}}
            <label class="pseudo-btn js-taxpayer">Заказать как юр.лицо</label>
        {{else}}
            <label class="pseudo-btn js-taxpayer">Заказать как физ.лицо</label>
        {{/iff}}
    </div>
    {{#if AUTHORIZED}}
        <div class="ds-checkout-form__content js-authoriz">
            <div class="form-group form-disabled">
                <label for="tel">Номер телефона (мобильный)</label>
                <span class="tel-success">
                <input required="" data-name="phone_auth" class="inp-big" type="tel" id="tel" placeholder="{{CUSTOM_PROPS.PHONE.DESCRIPTION}}" name="ORDER_PROP_{{CUSTOM_PROPS.PHONE.ID}}" value="{{CUSTOM_PROPS.PHONE.VALUE}}">
            </span>
            </div>
            {{#iff USER_VALS.PERSON_TYPE_ID '==' 2}}
                <div class="form-group js-taxpayer-input">
                    <label for="taxpayer-number">ИНН</label>
                    <input required="" class="inp-big" type="text" id="taxpayer-number" name="ORDER_PROP_{{CUSTOM_PROPS.INN.ID}}" placeholder="{{CUSTOM_PROPS.INN.DESCRIPTION}}" value="{{CUSTOM_PROPS.INN.VALUE}}">
                </div>
            {{/iff}}
            <div class="form-group">
                <label for="user-name">Имя и фамилия</label>
                <input required="" class="inp-big" type="text" id="user-name" name="ORDER_PROP_{{CUSTOM_PROPS.NAME.ID}}" placeholder="{{CUSTOM_PROPS.NAME.DESCRIPTION}}" value="{{CUSTOM_PROPS.NAME.VALUE}}" data-order-baseinfo="user-name">
            </div>
            <div class="form-group">
                <label for="user-email">эл. почта</label>
                <input required="" class="inp-big" type="email" id="user-email" name="ORDER_PROP_{{CUSTOM_PROPS.EMAIL.ID}}" placeholder="{{CUSTOM_PROPS.EMAIL.DESCRIPTION}}" value="{{CUSTOM_PROPS.EMAIL.VALUE}}" data-order-baseinfo="user-email">
            </div>
            <div class="form-group" hidden>
                <input class="sub-agreed" type="checkbox" id="sub-agreed" checked >
                <label for="sub-agreed">Хочу получать персональную рассылку</label>
            </div>
            <div class="reciever-content">
                <div class="reciever-content__btn js-reciever {{#iff CUSTOM_PROPS.BUYER_AND_RECEIVER_THE_SAME.VALUE '!=' 'Y'}}hidden{{/iff}}">
                    <span class="pseudo-btn">Получатель другое лицо?</span>
                    <span class="small-text">Добавьте данные человека, который непосредственно будет принимать заказ.</span>
                </div>
                <div class="reciever-content__info {{#iff CUSTOM_PROPS.BUYER_AND_RECEIVER_THE_SAME.VALUE '==' 'Y'}}hidden{{/iff}} js-reciever-block">
                    <div class="reciever-content__info-header">
                        <h5>Получатель заказа</h5><span class="icon-svg ic-close js-reciever-block-close"></span><span class="subheader">Только указанный ниже человек сможет получить заказ</span>
                    </div>
                    <div class="form-group">
                        <label for="reciever-tel">Телефон получателя</label>
                        <input {{#iff CUSTOM_PROPS.BUYER_AND_RECEIVER_THE_SAME.VALUE '!=' 'Y'}}required=""{{/iff}} class="inp-big" name="ORDER_PROP_{{CUSTOM_PROPS.RECEIVER_PHONE.ID}}" type="tel" id="reciever-tel" placeholder="{{CUSTOM_PROPS.RECEIVER_PHONE.DESCRIPTION}}" value="{{CUSTOM_PROPS.RECEIVER_PHONE.VALUE}}">
                    </div>
                    <div class="form-group">
                        <label for="reciever-user-name">Имя и фамилия</label>
                        <input {{#iff CUSTOM_PROPS.BUYER_AND_RECEIVER_THE_SAME.VALUE '!=' 'Y'}}required=""{{/iff}} class="inp-big" type="text" id="reciever-user-name" name="ORDER_PROP_{{CUSTOM_PROPS.RECEIVER_NAME.ID}}" placeholder="{{CUSTOM_PROPS.RECEIVER_NAME.DESCRIPTION}}" value="{{CUSTOM_PROPS.RECEIVER_NAME.VALUE}}">
                    </div>
                </div>
            </div>
        </div>
    {{else}}
        <div class="ds-checkout-form__content">
            <div class="form-group">
                <label for="tel">Номер телефона (мобильный)</label>
                <span id="phone-number" class="form-group__item">
                    <span class="icon-svg ic-input-clean js-input-clean"></span>
                    <input required="" data-name="is_phone" class="inp-big" type="tel" id="tel" name="ORDER_PROP_{{CUSTOM_PROPS.PHONE.ID}}"  placeholder="{{CUSTOM_PROPS.PHONE.DESCRIPTION}}" value="{{#iff CUSTOM_PROPS.PHONE.VALUE.length '==' 0}}7{{else}}{{CUSTOM_PROPS.PHONE.VALUE}}{{/iff}}" data-parsley-error-message="Сначала введите телефон" data-parsley-errors-container="#phone-number">
                </span>
                <span class="inp-info">Вам будет отправлен код подтверждения</span>
            </div>
            {{#iff USER_VALS.PERSON_TYPE_ID '==' '2' }}
                <div class="form-group js-taxpayer-input">
                    <label for="taxpayer-number">ИНН</label>
                    <span class="form-group__item">
                        <span class="icon-svg ic-input-clean js-input-clean"></span>
                        <input required="" class="inp-big" type="text" id="taxpayer-number" name="ORDER_PROP_{{CUSTOM_PROPS.INN.ID}}" placeholder="{{CUSTOM_PROPS.INN.NAME}}" placeholder="{{CUSTOM_PROPS.INN.VALUE}}">
                    </span>
                </div>
            {{/iff}}
            <div class="form-group form-group--mt-20">
                <span class="ds-btn ds-btn--default ds-btn--mobile" data-name="phoneButton">Подтвердить номер телефона</span>
            </div>
        </div>
    {{/if}}

