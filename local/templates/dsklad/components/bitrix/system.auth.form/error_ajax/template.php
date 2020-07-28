<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)    die();

if($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR'] && $arResult['ERROR_MESSAGE']['TYPE'] != 'OK'){
    echo strip_tags($arResult['ERROR_MESSAGE']['MESSAGE']);
}else{
    echo 'ok';
}

//if($arResult['ERROR_MESSAGE']['TYPE'] == 'OK'){
//    echo 'ok';
//}

//print_r($arResult);