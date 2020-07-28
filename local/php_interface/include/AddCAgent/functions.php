<?php
function AddCAgent($arg1, $arg2 = false)
{
    CAgent::AddAgent("blaq();", '', 'N', 30);
}

function blaq($arg1, $arg2 = false)
{

    Bitrix\Main\Diag\Debug::writeToFile($_REQUEST["mode"], '', 'OnSuccessCatalogImport1C');
    // Условия выборки элементов для обработки
    $arFilter = array(
        'IBLOCK_ID' => 14,
        'ACTIVE' => 'Y',
    );
    $arSelect = array(
        'ID',
        'NAME',
        'PROPERTY_ETOKOMPLEKT'
    );

    $res = CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, false, $arSelect);
    $errorMessage = null;
    $arBla = array();
    while ($arItem = $res->GetNext()) {


        $arBla[] = $arItem;
        if ($error === true) {
            $errorMessage = 'Что-то случилось.';
            break;
        }

        $NS['custom']['lastId'] = $arItem['ID'];
        $NS['custom']['counter']++;
    }
    Bitrix\Main\Diag\Debug::writeToFile($arBla, '', 'OnSuccessCatalogImport1C');
}