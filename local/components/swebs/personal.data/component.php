<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Context;

$obRequest = Context::getCurrent()->getRequest();

$arResult = array();

global $USER;

if ($obRequest->get('action') == 'personal') {
    $arFields = array();

    if (!empty($obRequest->get('name'))) {
        $arFields['NAME'] = trim($obRequest->get('name'));
    }

    if (!empty($obRequest->get('email'))) {
        $arFields['EMAIL'] = trim($obRequest->get('email'));
    }

    if (!empty($obRequest->get('phone'))) {
        $arFields['PERSONAL_PHONE'] = trim($obRequest->get('phone'));
    }

    if (!empty($obRequest->get('company'))) {
        $arFields['WORK_COMPANY'] = trim($obRequest->get('company'));
    }

    if (!empty($obRequest->get('vat'))) {
        $arFields['UF_INN'] = trim($obRequest->get('vat'));
    }

    if ($obRequest->get('legal') == 1) {
        $arFields['UF_LEGAL'] = true;
    } else {
        $arFields['UF_LEGAL'] = false;
    }

    if ($obRequest->get('subscrible') == 1) {
        $arFields['UF_SPAM'] = true;
    } else {
        $arFields['UF_SPAM'] = false;
    }

    if (!empty($obRequest->get('address'))) {
        $arFields['PERSONAL_STREET'] = trim($obRequest->get('address'));
    }

    if (!empty($obRequest->get('password'))) {
        $arFields['PASSWORD'] = trim($obRequest->get('password'));
    }

    if (!empty($obRequest->get('password2'))) {
        $arFields['CONFIRM_PASSWORD'] = trim($obRequest->get('password2'));
    }

    if (!empty($arFields)) {
        $obUser = new CUser;
        $obUser->Update($USER->GetID(), $arFields);
        $arResult['ERROR'] = $obUser->LAST_ERROR;
    }

    if ($obRequest->get('subscrible') == 1 && !empty($obRequest->get('email'))) {
        uniSenderSubscriber(array(
            "EMAIL" => $_REQUEST["email"],
            "NAME" => "",
            "ENTRY" => ""
        ));
    } if ($obRequest->get('subscrible') != 1) {
        // Андрей, снять подписку
    }
}

$dbUser = CUser::GetByID($USER->GetID());
$arResult['USER'] = $dbUser->Fetch();


$this->IncludeComponentTemplate();
