<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $templateFile
 * @var string $templateFolder
 * @var CBitrixComponent $component
 * @var string $componentPath
 */

$this->setFrameMode(true);
?>
<div class="field__widget type-inline field-confirm-code">
    <div class="field">
        <div class="label">
            <span class="success">Вам отправлен SMS-код</span>
            <span class="error js-error-phone">Вы изменили телефоный номер!</span>
            <span class="error js-error-tries">Превышено количество попыток ввода кода!</span>
        </div>
        <div class="input">
            <input id="input-confirm-code" name="confirm_code" placeholder="Введите проверочный код" autocomplete="off" value="">
        </div>
        <p class="text_error error js-error-code">Неверный код!</p>
        <div class="wait">
            <?
            // нули обязательны, т.к. php возращет время в секундах, а js нужны милисекунды
            ?>
            Повторный запрос возможен через: <span id="countdown" data-time="<?= $_SESSION['CONFIRM_PHONE']['TIME'] ?>000"></span>
        </div>
    </div>
</div>

<script>
    var SaleConfirmPhoneController = new SaleConfirmPhone({
        payments: '<?= $arParams['PAYMENTS_SELECTOR'] ?>',
        payment_confirm: '<?= $arParams['PAYMENT_CONFIRM_SELECTOR'] ?>',
        wait_time: '<?= $arParams['WAIT_TIME'] ?>',
        code_length: '<?= $arParams['LENGTH'] ?>',
    });
</script>