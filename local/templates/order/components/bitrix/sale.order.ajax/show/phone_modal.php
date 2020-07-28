<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<div class="hide" data-name="phone_modal" id="phone_modal">
    <div class="ds-modal__body">
        <?$test = $APPLICATION->IncludeComponent(
            'dsklad:sale.confirm.phone',
            '',
            array(
                'PAYMENTS_SELECTOR' => '.payment .options label',  //css-селектор кнопок выбора способа оплаты
                'PAYMENT_CONFIRM_SELECTOR' => '.payconfirm',  //css-селектор кнопки способа оплаты, при котором нужно подтверждать телефон
                'PHONE_INPUT_SELECTOR' => "input[data-name='is_phone']",  //css-селектор поля с номером телефона
                'WAIT_TIME' => \Dsklad\Config::getOption('UF_CONF_PHONE_TIME'),  //время до повторной отправки
                'LENGTH' => \Dsklad\Config::getOption('UF_CONF_PHONE_LENGTH'),  //длина кода
                'RELOAD'=>'N', // Перегружать страницу после авторизации,
                'NO_CONFORM_CODE' =>  \Dsklad\Config::getOption('UF_NO_CONFORM_CODE'), // коды телефонов для которых не нужно подтверждения
            ),
            false
        );?>
    </div>
</div>