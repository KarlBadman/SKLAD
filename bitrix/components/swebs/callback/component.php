<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Event;

Loader::includeModule('iblock');

$arResult = array(
    'FIELDS' => array(
        'NAME' => 'name',
        'PHONE' => 'phone'
    )
);

$obContext = Context::getCurrent();
$obRequest = $obContext->getRequest();
$strName = $obRequest->get($arResult['FIELDS']['NAME']);
$strPhone = $obRequest->get($arResult['FIELDS']['PHONE']);
$strComponent = $obRequest->get('component');
$strTemplate = $obRequest->get('template');
$goodID = $obRequest->get('good-id');
$gRecaptchaResponse=$obRequest->get('g-recaptcha-response');

//__($strName);
//__($strPhone);
//__($strComponent);
//__($strTemplate);
//__($goodID);
//__($this->__templateName);
$success=true;

$secret = '6LfhxiEUAAAAAIWJ7Jf3ffZcg-Hp4cd1O_8r_PNi';
include($_SERVER["DOCUMENT_ROOT"].'/local/php_interface/php_lib/autoload.php');
$recaptcha = new \ReCaptcha\ReCaptcha($secret);
$ReCaptchaRes = $recaptcha->verify($gRecaptchaResponse, $_SERVER['REMOTE_ADDR']);
if($strTemplate!='preorder') {
    $success = $ReCaptchaRes->isSuccess();
}

if ($strComponent == 'callback' && $strTemplate==$this->__templateName && !empty($strPhone)  and $success) {
//    __("ITS oK");
    $strFieldName = $strPhone;
    if (!empty($strName)) {
        $strFieldName .= ' (' . $strName . ')';
    }

    $good = array();
    if(intval($goodID)>0){
        $arSelect = Array("ID","NAME","PROPERTY_CML2_ARTICLE","PROPERTY_KOD_TSVETA","PROPERTY_CML2_LINK");
        $arFilter = Array("IBLOCK_ID"=>IntVal(36), "ID"=>intval($goodID));
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        if($arFields = $res->GetNext())
        {
            $good['NAME']=$arFields['NAME'];
            $good['PARENT_ID']=$arFields['PROPERTY_CML2_LINK_VALUE'];
            $good['COLOR']=$arFields['PROPERTY_KOD_TSVETA_VALUE'];
            $good['COLOR'] = explode('#',$good['COLOR'])[0];
            $arSelect = Array("ID","NAME","PROPERTY_CML2_ARTICLE");
            $arFilter = Array("IBLOCK_ID"=>IntVal(35), "ID"=>intval($good['PARENT_ID']));
            $resParent = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            if($arFieldsParent = $resParent->GetNext()){
                $good['NAME'] = $arFieldsParent['NAME'];
                $good['ART'] = $arFieldsParent['PROPERTY_CML2_ARTICLE_VALUE'];
            }
        }

    }

    $obElement = new CIBlockElement;
    $arFields = array(
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'NAME' => $strFieldName
    );
    $detail_text='';
    if(count($good)){
        $detail_text = str_replace(
            array('#NAME#','#COLOR#','#ART#'),
            array($good['NAME'], $good['COLOR'], $good['ART']),
            'Модель: #NAME#, Цвет: #COLOR#, Артикул: #ART#'
        );
        $arFields['DETAIL_TEXT'] = $detail_text;
    }

    if($strTemplate!='preorder' or ($strTemplate=='preorder' and count($good))) {
        $obResult = $obElement->Add($arFields);
    }

    // goes to email
    $arMailFields = array(
        'EVENT_NAME' => $arParams['EVENT_NAME'],
        'LID' => $obContext->getSite(),
        'C_FIELDS' => array(
            'NAME' => $strName,
            'PHONE' => $strPhone,
            "GOOD_INFO" =>$detail_text
        )
    );
    if($strTemplate!='preorder' or ($strTemplate=='preorder' and count($good))) {
        Event::send($arMailFields);
    }

}
$this->IncludeComponentTemplate();
