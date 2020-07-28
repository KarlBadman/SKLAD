<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? if($_REQUEST['login'] == 'yes'):
    LocalRedirect($_SERVER['HTTP_REFERER']);
endif; ?>

<div class="signup__popup">
    <h2>Вход</h2>
    <form name="system_auth_form<?=$arResult["RND"]?>" id="wrap_form_signin" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>" novalidate="novalidate">
        <?if($arResult["BACKURL"] <> ''):?>
            <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
        <?endif?>
        <?foreach ($arResult["POST"] as $key => $value):?>
            <input type="hidden" name="<?=$key?>" value="<?=$value?>" />
        <?endforeach?>
        <input type="hidden" name="AUTH_FORM" value="Y" />
        <input type="hidden" name="TYPE" value="AUTH" />
        <div class="field__widget type-block field-email required">
            <label for="input-signin-email" class="label">Эл.почта:</label>
            <div class="field">
                <div class="input">
                    <!--<div class="placeholder">name@domain.ru</div>-->
                    <input type="text" id="input-signin-email" name="USER_LOGIN" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" autocomplete="off" placeholder="name@domain.ru" />
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-password-signin required">
            <label for="input-signin-password" class="label">Пароль:</label>
            <div class="field">
                <div class="input">
                    <input type="password" id="input-signin-password" name="USER_PASSWORD" autocomplete="off" />
                </div>
                <p class="restore"><a href="<?= SITE_TEMPLATE_PATH ?>/ajax/restore.php" data-fancybox-type="ajax" class="fancybox">Напомнить пароль</a></p>
            </div>
        </div>
  <!--       <div class="field__widget type-block field-openid">
            <div class="label">Войдите, используя <br/>профиль соцсети:</div>
            <div class="field wrap_socserv_icons"> -->
                <?/*
                $APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "flat",
                    array(
                        "AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
                        "AUTH_URL" => $arResult["AUTH_URL"],
                        "POST" => $arResult["POST"],
                        "POPUP" => "N",
                        "SUFFIX" => "form",
                    ),
                    $component,
                    array("HIDE_ICONS" => "Y")
                );
                */?>
        <!--     </div>
        </div> -->

        <div class="field__widget type-block field-submit">
            <div class="field">
                <button type="submit" name="Login" class="login_popup_btn button type-blue fill size-41">Войти</button>
                <p>Новый пользователь? <a href="<?= SITE_TEMPLATE_PATH ?>/ajax/signup.php" data-fancybox-type="ajax" class="fancybox">Зарегистрируйтесь</a></p>
            </div>
        </div>
    </form>
</div>
