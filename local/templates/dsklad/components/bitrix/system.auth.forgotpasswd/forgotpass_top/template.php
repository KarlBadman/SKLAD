<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

if ($_REQUEST['forgot_password'] == 'yes') {
    LocalRedirect($_SERVER['HTTP_REFERER']);
}
?>

<div class="signup__popup">
    <h2>Восстановление <br/>пароля</h2>
    <form name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>" novalidate="novalidate" class="form_forgotpass">
        <input type="hidden" name="AUTH_FORM" value="Y">
        <input type="hidden" name="TYPE" value="SEND_PWD">
        <div class="field__widget type-block field-email required">
            <label for="input-signin-email" class="label">Эл.почта:</label>
            <div class="field">
                <div class="input">
                    <div class="icon pos-after">
                        <span class="icon__help">
                            <svg>
                                <use xlink:href="/bitrix/templates/dsklad/images/sprite.svg#help"></use>
                            </svg>
                        </span>
                    </div>
                    <!--<div class="placeholder">name@domain.ru</div>-->
                    <input type="text" id="input-signin-email" autocomplete="off" name="USER_LOGIN" placeholder="name@domain.ru" />
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-submit align-right">
            <div class="field">
                <a href="/bitrix/templates/dsklad/ajax/signin.php" data-fancybox-type="ajax" class="button button type-blue size-41 fancybox size-c-1">Назад</a>
                <button id="restore_passwd_btn" type="submit" name="send_account_info" class="send_account_info button type-blue fill size-41 size-c-1">Отправить</button>
            </div>
        </div>
    </form>
</div>