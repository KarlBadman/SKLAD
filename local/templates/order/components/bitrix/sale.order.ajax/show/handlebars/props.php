<input type="hidden" name="cityID" value="{{DPD_CITY}}" />
<input type="hidden" name="ORDER_PROP_{{CUSTOM_PROPS.DPD_CODE.ID}}" value="{{CUSTOM_PROPS.DPD_CODE.VALUE}}">
<input type="hidden" name="ORDER_PROP_{{CUSTOM_PROPS.PAYMENT_METHOD.ID}}" value="{{THIS_PAYMENT}}">
<input type="hidden" data-input-name="same" name="ORDER_PROP_{{CUSTOM_PROPS.BUYER_AND_RECEIVER_THE_SAME.ID}}" value="{{CUSTOM_PROPS.BUYER_AND_RECEIVER_THE_SAME.VALUE}}">
<input data-input-name="terminal_code" type="hidden" name="ORDER_PROP_{{CUSTOM_PROPS.DPD_TERMINAL_CODE.ID}}" value="{{CUSTOM_PROPS.DPD_TERMINAL_CODE.VALUE}}">

{{#if DELIVERY_POINT}}
    <input type="hidden" name="ORDER_PROP_{{CUSTOM_PROPS.ADDRESS_TERMINAL.ID}}" value="{{CUSTOM_PROPS.ADDRESS_TERMINAL.VALUE}}">
{{/if}}

{{#if CUSTOM_PROPS.ADDRESS_COMMENT.VALUE}}
    <input type="hidden" name="ORDER_PROP_{{CUSTOM_PROPS.ADDRESS_COMMENT.ID}}" value="{{CUSTOM_PROPS.ADDRESS_COMMENT.VALUE}}">
{{/if}}

{{#if CUSTOM_PROPS.TOKEN.VALUE}}
    <input type="hidden" name="ORDER_PROP_{{CUSTOM_PROPS.TOKEN.ID}}" value="{{CUSTOM_PROPS.TOKEN.VALUE}}">
{{/if}}

<input type="hidden" name="confirmorder" id="confirmorder" value="N">
<input type="hidden" name="profile_change" id="profile_change" value="N">

<input type="hidden" name="PERSON_TYPE" value="{{USER_VALS.PERSON_TYPE_ID}}">
<input type="hidden" name="PERSON_TYPE_OLD" value="{{USER_VALS.PERSON_TYPE_OLD}}">

<input type="hidden" name="ORDER_PROP_{{CUSTOM_PROPS.PROMO.ID}}" value="{{PROMO.VALUE}}">

<input type="hidden" name="ORDER_PROP_{{CUSTOM_PROPS.CITY.ID}}" value="{{DPD_CITY_NAME}}">

{{#iff USER_VALS.PERSON_TYPE_ID '==' '1'}}
    <input type="hidden" name="ORDER_PROP_10" value="{{POST.ORDER_PROP_10}}">
{{/iff}}

{{#if NO_ADDRESSES}}
    <input type="hidden" name="NO_ADDRESSES" value="Y">
{{/if}}

{{#if SERVICE_TERMINAL}}
    <select style="display: none" data-name="not_terminal" name="DELIVERY_EXTRA_SERVICES[{{SERVICE_TERMINAL.ID_DELIVERY}}][{{SERVICE_TERMINAL.ID}}]">
        {{#each SERVICE_TERMINAL.PARAMS.PRICES}}
            <option {{#iff ../SERVICE_TERMINAL.CHECKED '==' 'Y'}} {{#iff ../DPD_CITY '==' TITLE}}selected{{/iff}}{{/iff}} data-city="{{TITLE}}" value="{{ID}}">{{TITLE}}</option>
       {{/each}}
    </select>
{{/if}}

<input type="hidden" name="save" value="Y">