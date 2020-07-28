<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<form action="" method="post" id="contact-form">
    <div class="legend">Форма обратной связи</div>
    <fieldset>
        <div class="field__widget type-inline">
            <label for="input-contacts-name" class="label">Контактное лицо:</label>
            <div class="field">
                <div class="input">
                    <div class="placeholder">Ваше имя</div>
                    <input class="js-input-contacts-name" id="input-contacts-name" name="<?= $arResult['FIELDS']['NAME'] ?>" type="text" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="field__widget type-inline">
            <label for="input-contacts-phone" class="label">Телефон</label>
            <div class="field">
                <div class="input">
                    <div class="placeholder">+7 900 000-00-00</div>
                    <input class="js-input-contacts-phone" id="input-contacts-phone" name="<?= $arResult['FIELDS']['PHONE'] ?>" type="tel" autocomplete="off"
                           data-phonemask>
                </div>
            </div>
        </div>
		<div class="field__widget type-inline">
            <label for="input-contacts-phone" class="label">Почта</label>
            <div class="field">
                <div class="input">
                    <div class="placeholder">Электронная почта</div>
                    <input class="js-input-contacts-email" id="input-contacts-email" name="<?= $arResult['FIELDS']['EMAIL'] ?>" type="email" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="field__widget type-inline field-question">
            <label for="input-question" class="label">Текст сообщения:</label>
            <div class="field">
                <div class="input">
                    <textarea class="js-input-contacts-question" id="input-question" name="<?= $arResult['FIELDS']['TEXT'] ?>" rows="3"
                              autocomplete="off"></textarea>
                </div>
            </div>
        </div>
        <div class="field__widget type-inline field-submit">
            <div class="field">
                <button type="button" autocomplete="off" class="js-contact-form-submit button type-blue fill size-41 g-recaptcha">Отправить
                </button>
            </div>
        </div>
    </fieldset>
</form>
<style>
    .field-error{
        border: 1px solid #d43f3f;
    }
</style>


<script>
    $(document).ready(function () {
        $('.js-contact-form-submit').on('click',function(e){
            var nameField = $('.js-input-contacts-name');
            var phoneField = $('.js-input-contacts-phone');
            var questionField = $('.js-input-contacts-question');
            var emptyFields = 0;

            if(nameField.val().length<2){
                nameField.closest('.input').addClass('field-error');
                emptyFields++;
            }else{
                nameField.closest('.input').removeClass('field-error');
            }

            if(phoneField.val().length<2){
                phoneField.closest('.input').addClass('field-error');
                emptyFields++;
            }else{
                phoneField.closest('.input').removeClass('field-error');
            }
			/*if(emailField.val().length<2){
                emailField.closest('.input').addClass('field-error');
				emailField.closest('.input').after("<p class='text_error'>Электронная почта указана не верно! Проверьте правильность ввода.</p>");
                emptyFields++;
            }else{
				//валидация
				var pattern =/.+@.+\..+/i;

			 if (emailField.val().search(pattern) != -1){
				 emailField.closest('.input').removeClass('field-error');
				$(".text_error").css("display","none");
			 }else{
				  emailField.closest('.input').addClass('field-error');
                    emailField.closest('.input').after("<p class='text_error'>Электронная почта указана не верно! Проверьте правильность ввода.</p>");
				emptyFields++;
			 }*/


            if(questionField.val().length<2){
                questionField.closest('.input').addClass('field-error');
                emptyFields++;
            }else{
                questionField.closest('.input').removeClass('field-error');
            }

            if(emptyFields==0){
                $('#contact-form').submit();
            }
            e.preventDefault();
        });

        <?if($arResult['SHOW_SECCESS'] == 'Y'):?>

        $('.success_popup_send_txt').removeClass('active');
        $('.success_popup_send_txt').find('.desc_popup_send_txt').find('span').html('Спасибо за ваше обращение!');
        $('.success_popup_send_txt').addClass('active');

        setTimeout(function () {
            $('.success_popup_send_txt').removeClass('active');
        }, 3000);

        <?endif;?>
    });
</script>