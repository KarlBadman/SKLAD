<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<div class="signup__popup">
    <h2>Регистрация</h2>
    <form method="post" id="wrap_form_signup_popup" action="<?= POST_FORM_ACTION_URI ?>" name="regform" enctype="multipart/form-data" novalidate="novalidate">
        <?
        if ($arResult['BACKURL'] <> '') {
            ?>
            <input type="hidden" name="backurl" value="<?= $arResult['BACKURL'] ?>" />
            <?
        }
        ?>
        <div class="field__widget type-block field-name required">
            <label for="input-signup-name" class="label">Ваше имя:</label>
            <div class="field">
                <div class="input">
                    <input type="text" id="input-signup-name" autocomplete="off" name="REGISTER[<?= $arResult['SHOW_FIELDS'][5] ?>]"/>
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-email required">
            <label for="input-signup-email" class="label">Эл.почта:</label>
            <div class="field">
                <div class="input">
                    <div class="placeholder">name@domain.ru</div>
                    <input type="email" id="input-signup-email" autocomplete="off" name="REGISTER[<?= $arResult['SHOW_FIELDS'][3] ?>]"/>
                    <input type="hidden" id="input-signup-login" autocomplete="off" name="REGISTER[<?= $arResult['SHOW_FIELDS'][0] ?>]"/>
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-phone">
            <label for="input-signup-phone" class="label">Телефон:</label>
            <div class="field">
                <div class="input">
                    <input type="text" id="input-signup-phone" autocomplete="off" name="REGISTER[<?= $arResult['SHOW_FIELDS'][6] ?>]" data-phonemask="data-phonemask"/>
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-password required">
            <label for="input-signup-password" class="label">Пароль:</label>
            <div class="field">
                <div class="input">
                    <input type="password" id="input-signup-password" autocomplete="off" name="REGISTER[<?= $arResult['SHOW_FIELDS'][1] ?>]"/>
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-password required">
            <label for="input-signup-password2" class="label">Еще раз:</label>
            <div class="field">
                <div class="input">
                    <input type="password" id="input-signup-password2" autocomplete="off" name="REGISTER[<?= $arResult['SHOW_FIELDS'][2] ?>]"/>
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-submit">
            <div class="field">
                <button id="main_reg_btn" type="submit" name="register_submit_button" value="Y" class="send_reg_top_btn button type-blue fill size-41">Зарегистрироваться</button>
                <p>Если у вас есть аккаунт, <a href="/bitrix/templates/dsklad/ajax/signin.php" data-fancybox-type="ajax" class="fancybox">Войдите</a></p>
            </div>
        </div>
    </form>
</div>
