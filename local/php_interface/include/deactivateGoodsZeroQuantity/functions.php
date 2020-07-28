<?php
function deactivateGoodsZeroQuantity($id,&$arFields)
{
    $element = CIBlockElement::GetList([], ['IBLOCK_ID'=>$arFields['IBLOCK_ID'],'ID'=>$id], false,[], ['IBLOCK_ID','ID','ACTIVE','PROPERTY_ARRIVAL_DATE'])->Fetch();

    if($arFields['QUANTITY'] < 1 && $element['ACTIVE'] == 'Y' && empty($element['PROPERTY_ARRIVAL_DATE_VALUE'])){
        $el = new CIBlockElement;
        $el->Update($id, ['ACTIVE'=>'N']);
    }elseif($arFields['QUANTITY'] < 1 && $element['ACTIVE'] == 'N' && !empty($element['PROPERTY_ARRIVAL_DATE_VALUE'])){
        $el = new CIBlockElement;
        $el->Update($id, ['ACTIVE'=>'Y']);
    }elseif ($arFields['QUANTITY'] > 0 && $element['ACTIVE'] == 'N'){
        $el = new CIBlockElement;
        $el->Update($id, ['ACTIVE'=>'Y']);
    }
}