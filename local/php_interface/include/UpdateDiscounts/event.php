<?php
AddEventHandler(
    'iblock',
    'OnAfterIBlockElementUpdate',
    function (&$arFields){
        if($arFields['IBLOCK_ID']==36) UpdateDiscounts($arFields['ID']);
    });

AddEventHandler(
    'iblock',
    'OnAfterIBlockElementAdd',
    function (&$arFields){
        if($arFields['IBLOCK_ID']==36) UpdateDiscounts($arFields['ID']);
    });