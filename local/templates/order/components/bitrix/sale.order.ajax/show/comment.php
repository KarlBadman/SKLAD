<div class="ds-checkout-form__item">
    <label>Комментарий к заказу</label>
    <textarea class="inp-big" name="ORDER_DESCRIPTION" placeholder="Особые требования к заказу или доставке..."></textarea>
    <div class="form-group" data-block-name="no_call">
        <input type="checkbox" id="confirm-called" value="Y" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['NOT_CALL']['ID']?>">
        <label for="confirm-called"><?=$arResult['CUSTOM_PROPS']['NOT_CALL']['NAME']?></label>
    </div>
</div>
