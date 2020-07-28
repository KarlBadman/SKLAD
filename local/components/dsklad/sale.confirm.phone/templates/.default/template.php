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
<div class="ds-modal-phone">
    <div class="ds-modal__header">
        <h4>Введите код</h4>
        <span class="icon-svg ic-close ds-modal-close js-ds-modal-close" onclick="purepopup.closePopup();"></span>
    </div>
    <div class="ds-modal__body">
        <div data-name="modal_phone" class="ds-modal-phone__code">
            <input type="number" id="input-confirm-code" maxlength="4" data-name="confirm_code" autocomplete="off" value="">
            <span class="points"></span>
        </div>
        <p>Код выслан на <strong data-name="phone_text"></strong></p>
        <p data-block="timer">Получить новый код можно <br> через <span id="countdown" data-time="<?= $_SESSION['CONFIRM_PHONE']['TIME'] ?>000"><?=date('i:s',$arParams['WAIT_TIME'])?></span></p>
        <a class="repeat-sms" style="display: none" data-block="code_again">Выслать код повторно</a>
        <div class="ds-modal-phone__comment">При входе или регистрации вы соглашаетесь с<a href="/public_offer" target="_blank"> Условиями</a></div>
    </div>
</div>

<script>
    window.saleConfirmPhone.options = ({
        payments: '<?= $arParams['PAYMENTS_SELECTOR'] ?>',
        payment_confirm: '<?= $arParams['PAYMENT_CONFIRM_SELECTOR'] ?>',
        wait_time: '<?= $arParams['WAIT_TIME'] ?>',
        code_length: '<?= $arParams['LENGTH'] ?>',
        reload: '<?= $arParams['RELOAD'] ?>',
        no_confirm_code: <?=CUtil::PhpToJSObject($arParams['NO_CONFORM_CODE']);?>
    });
</script>