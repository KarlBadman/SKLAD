<div class="ds-checkout-form__item" data-block-name="auth">
    <div class="ds-checkout-form__header">
        <h4>Основное</h4>
        <?if($arResult['USER_VALS']['PERSON_TYPE_ID'] == 1):?>
            <label class="pseudo-btn js-taxpayer">Заказать как юр.лицо</label>
        <?else:;?>
            <label class="pseudo-btn js-taxpayer">Заказать как физ.лицо</label>
        <?endif;?>
    </div>
    <?if($arResult['AUTHORIZED']):?>
        <div class="ds-checkout-form__content js-authoriz">
            <div class="form-group form-disabled">
                <label for="tel">Номер телефона (мобильный)</label>
                <span class="tel-success">
                    <input required="" data-name="phone_auth" class="inp-big" type="text" id="tel"  placeholder="<?=$arResult['CUSTOM_PROPS']['PHONE']['DESCRIPTION']?>" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['PHONE']['ID']?>" value="<?=$arResult['CUSTOM_PROPS']['PHONE']['VALUE']?>">
                </span>
<!--                <a class="pseudo-btn" href="?logout=yes">Авторизоваться с другим номером</a>-->
            </div>
            <?if($arResult['USER_VALS']['PERSON_TYPE_ID'] == 2):?>
                <div class="form-group hidden js-taxpayer-input">
                    <label for="taxpayer-number">ИНН</label>
                    <input required="" class="inp-big" type="text" id="taxpayer-number" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['INN']['ID']?>" placeholder="<?=$arResult['CUSTOM_PROPS']['INN']['DESCRIPTION']?>" placeholder="<?=$arResult['CUSTOM_PROPS']['INN']['VALUE']?>">
                </div>
            <?endif;?>
            <div class="form-group">
                <label for="user-name">Имя и фамилия</label>
                <input required="" class="inp-big" type="text" id="user-name" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['NAME']['ID']?>" placeholder="<?=$arResult['CUSTOM_PROPS']['NAME']['DESCRIPTION']?>" value="<?=$arResult['CUSTOM_PROPS']['NAME']['VALUE']?>" data-order-baseinfo="user-name">
            </div>
            <div class="form-group">
                <label for="user-email">эл. почта</label>
                <input required="" class="inp-big" type="email" id="user-email" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['EMAIL']['ID']?>" placeholder="<?=$arResult['CUSTOM_PROPS']['EMAIL']['DESCRIPTION']?>" value="<?=$arResult['CUSTOM_PROPS']['EMAIL']['VALUE']?>" data-order-baseinfo="user-email">
            </div>
            <div class="form-group" hidden>
                <input class="sub-agreed" type="checkbox" id="sub-agreed" checked >
                <label for="sub-agreed">Хочу получать персональную рассылку</label>
            </div>
            <div class="reciever-content">
                <div class="reciever-content__btn js-reciever <?if($arResult['CUSTOM_PROPS']['BUYER_AND_RECEIVER_THE_SAME']['VALUE'] != 'Y'):?>hidden<?endif?>"><span class="pseudo-btn">Получатель другое лицо?</span>
                    <span class="small-text">Добавьте данные человека, который непосредственно будет принимать заказ.</span>
                </div>
                <div class="reciever-content__info <?if($arResult['CUSTOM_PROPS']['BUYER_AND_RECEIVER_THE_SAME']['VALUE'] == 'Y'):?>hidden<?endif?> js-reciever-block">
                    <div class="reciever-content__info-header">
                        <h5>Получатель заказа</h5><span class="icon-svg ic-close js-reciever-block-close"></span><span class="subheader">Только указанный ниже человек сможет получить заказ</span>
                    </div>
                    <div class="form-group">
                        <label for="reciever-tel">Телефон получателя</label>
                        <input data-name="some_input" <?if($arResult['CUSTOM_PROPS']['BUYER_AND_RECEIVER_THE_SAME']['VALUE'] != 'Y'):?>required=""<?endif?> class="inp-big" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['RECEIVER_PHONE']['ID']?>" type="tel" id="reciever-tel" placeholder="<?=$arResult['CUSTOM_PROPS']['PHONE_RECIPIENT']['DESCRIPTION']?>" value="<?=$arResult['CUSTOM_PROPS']['PHONE_RECIPIENT']['VALUE']?>">
                    </div>
                    <div class="form-group">
                        <label for="reciever-user-name">Имя и фамилия</label>
                        <input data-name="some_input" <?if($arResult['CUSTOM_PROPS']['BUYER_AND_RECEIVER_THE_SAME']['VALUE'] != 'Y'):?>required=""<?endif?> class="inp-big" type="text" id="reciever-user-name" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['RECEIVER_NAME']['ID']?>" placeholder="<?=$arResult['CUSTOM_PROPS']['NAME_RECIPIENT']['DESCRIPTION']?>" value="<?=$arResult['CUSTOM_PROPS']['NAME_RECIPIENT']['VALUE']?>">
                    </div>
                </div>
            </div>
        </div>
    <?else:?>
        <div class="ds-checkout-form__content">
            <div class="form-group">
                <label for="tel">Номер телефона (мобильный)</label>
                <span id="phone-number" class="form-group__item">
                    <span class="icon-svg ic-input-clean js-input-clean"></span>
                    <input required="" data-name="is_phone" class="inp-big" type="tel" id="tel" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['PHONE']['ID']?>" placeholder="<?=$arResult['CUSTOM_PROPS']['PHONE']['DESCRIPTION']?>" value="<?=(!empty($arResult['CUSTOM_PROPS']['PHONE']['VALUE'])) ? $arResult['CUSTOM_PROPS']['PHONE']['VALUE'] : 7; ?>" data-parsley-error-message="Сначала введите телефон" data-parsley-errors-container="#phone-number">
                </span>
                <span class="inp-info">Вам будет отправлен код подтверждения</span>
            </div>
            <?if($arResult['USER_VALS']['PERSON_TYPE_ID'] == 2):?>
                <div class="form-group hidden js-taxpayer-input">
                    <label for="taxpayer-number">ИНН</label>
                    <span class="form-group__item">
                        <span class="icon-svg ic-input-clean js-input-clean"></span>
                        <input required="" class="inp-big" type="text" id="taxpayer-number" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['INN']['ID']?>" placeholder="<?=$arResult['CUSTOM_PROPS']['INN']['NAME']?>" placeholder="<?=$arResult['CUSTOM_PROPS']['INN']['VALUE']?>">
                    </span>
                </div>
            <?endif;?>
            <div class="form-group form-group--mt-20">
                <span class="ds-btn ds-btn--default ds-btn--mobile" data-name="phoneButton">Подтвердить номер телефона</span>
            </div>
        </div>
    <?endif;?>
</div>
