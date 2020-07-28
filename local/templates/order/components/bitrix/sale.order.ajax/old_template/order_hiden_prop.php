<?if($arResult['ORDER_DATA']['PAY_SYSTEM_ID'] == $arParams['NAL']){
    $oplata = 'NAL';
}else{
    $oplata = 'BEZNAL';
}
?>
<input type="hidden" name="ORDER_PROP_<?=$arResult['PROP_DPD_CODE'];?>" value="<?=$deliveryData->serviceName?>">
<input type="hidden" name="ORDER_PROP_<?=ORDER_PROPERTY_ID[$arResult['ORDER_DATA']['PERSON_TYPE_ID']]['PAYSYSTEM'];?>" value="<?=$oplata?>">
<input type="hidden" name="ORDER_PROP_<?=ORDER_PROPERTY_ID[$arResult['ORDER_DATA']['PERSON_TYPE_ID']]['PHONE_OK'];?>" value="" id="phoneOk">
<input type="hidden" name="MIN_PACK" value="<?=$arResult['MIN_PACK'];?>">


<?if(array_search($arResult['USER_VALS']['DELIVERY_ID'],$arParams['MAP_DELIVERY']) !== false):?>
    <input type="hidden" name="ORDER_PROP_<?=ORDER_PROPERTY_ID[$arResult['ORDER_DATA']['PERSON_TYPE_ID']]['ADDRESS_TERMINAL'];?>" value="<?=$adresTerminal?>">
<?endif;?>

