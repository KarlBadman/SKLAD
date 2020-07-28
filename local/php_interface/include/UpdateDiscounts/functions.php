<?php
function UpdateDiscounts($ProductID){

    \CIBlockElement::SetPropertyValuesEx($ProductID, 36, array("HAS_DISCOUNT" => hasDiscount($ProductID)));

    $db_props = CIBlockElement::GetProperty(36, $ProductID, array("sort" => "asc"), Array("CODE"=>"CML2_LINK"));
    if($ar_props = $db_props->Fetch())
        $parentID = IntVal($ar_props["VALUE"]);
    else
        $parentID = false;

    $has_discount=0;
    if($parentID>0){
        $arSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM","PROPERTY_HAS_DISCOUNT");
        $arFilter = Array("IBLOCK_ID"=>36, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_CML2_LINK"=>$parentID);
        $res = \CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>100), $arSelect);
        while($arFields = $res->GetNext()){
            $has_discount+=$arFields['PROPERTY_HAS_DISCOUNT_VALUE'];
        }
    }
    \CIBlockElement::SetPropertyValuesEx($parentID, 35, array("HAS_DISCOUNT" => $has_discount));
    return true;
}
