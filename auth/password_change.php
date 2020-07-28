<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Востановление пароля");

//$password = 'Profsol12#$';
$password = substr(str_shuffle(strtolower(sha1(rand() . time() . "my salt string"))),0, 8);

$login = $_GET['USER_LOGIN'];
$checkWord = $_GET['USER_CHECKWORD'];

$rsUser = \CUser::GetByLogin($login);

$arUser = $rsUser->Fetch();

if($arUser['ID']>0){
    $arEventFields['LOGIN']=$login;
    $arEventFields['PASS']=$password;
    $arEventFields['MESSAGE']='Вы запросили новый пароль.';
    $arEventFields['EMAIL']=$login;

    global $USER;
    $arResult = $USER->ChangePassword($login, $checkWord, $password, $password);
	if ($arResult["TYPE"] == "OK") {
		\CEvent::Send("USER_NEW_PASSWORD", SITE_ID, $arEventFields);
	}
}else{
    $arResult["TYPE"] = "WRONG_USER";
}
?>
<div class="contacts__page">
    <div class="default">
        <div class="contents">
            <section class="heading">
               <div class="title">
                    <h1>Востановление пароля</h1>
                </div>
            </section>
            <section class="text hidden-lte-m">
                <?if($arResult["TYPE"] == "OK"):?>
                <p><b>Ваш пароль сброшен.</b></p>
                <p><b>Новый пароль, выслан вам в письме.</b></p>
                <?else:?>
                <p><b>Извините, ссылка на востановление пароля устарела.</b></p>
                <p><b>Пожалуйста, повторите попытку.</b></p>
                <?endif;?>
            </section>
        </div>
    </div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>