<div data-block-name="props">
    <input type="hidden" name="cityID" value="<?=$arResult['DPD_CITY']?>" />
    <input type="hidden" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['DPD_CODE']['ID'];?>" value="<?=$arResult['CUSTOM_PROPS']['DPD_CODE']['VALUE']?>">
    <input type="hidden" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['PAYMENT_METHOD']['ID'];?>" value="<?=$arResult['THIS_PAYMENT']?>">
    <input type="hidden" data-input-name="same" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['BUYER_AND_RECEIVER_THE_SAME']['ID']?>" value="<?=$arResult['CUSTOM_PROPS']['BUYER_AND_RECEIVER_THE_SAME']['VALUE']?>">
    <input data-input-name="terminal_code" type="hidden" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['DPD_TERMINAL_CODE']['ID']?>" value="<?=$arResult['CUSTOM_PROPS']['DPD_TERMINAL_CODE']['VALUE']?>">
    <?if ($arResult['CUSTOM_PROPS']['TOKEN']['VALUE']) : ?>
        <input type="hidden" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['TOKEN']['ID'];?>" value="<?=$arResult['CUSTOM_PROPS']['TOKEN']['VALUE'];?>">
    <?endif;?>
    <?if($arResult['DELIVERY_POINT']):?>
        <input type="hidden" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['ADDRESS_TERMINAL']['ID'];?>" value="<?=$arResult['CUSTOM_PROPS']['ADDRESS_TERMINAL']['VALUE'];?>">
    <?endif;?>
    <input type="hidden" name="confirmorder" id="confirmorder" value="N">
    <input type="hidden" name="profile_change" id="profile_change" value="N">
    
    <?if ($arResult['CUSTOM_PROPS']['ADDRESS_COMMENT']['VALUE']) : ?>
        <input type="hidden" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['ADDRESS_COMMENT']['ID'];?>" value="<?=$arResult['CUSTOM_PROPS']['ADDRESS_COMMENT']['VALUE']?>">
    <?endif;?>
    
    <input type="hidden" name="PERSON_TYPE" value="<?=$arResult["USER_VALS"]["PERSON_TYPE_ID"]?>">
    <input type="hidden" name="PERSON_TYPE_OLD" value="<?=$arResult["USER_VALS"]["PERSON_TYPE_OLD"]?>">

    <input type="hidden" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['PROMO']['ID'];?>" value="<?=$arResult['PROMO']['VALUE']?>">

    <input type="hidden" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['CITY']['ID'];?>" value="<?=$arResult['DPD_CITY_NAME'];?>">

    <?if($arResult['NO_ADDRESSES']):?>
        <input type="hidden" name="NO_ADDRESSES" value="Y">
    <?endif;?>

    <?if($arResult['SERVICE_TERMINAL']):?>
        <select style="display: none" data-name="not_terminal" name="DELIVERY_EXTRA_SERVICES[<?=$arResult['SERVICE_TERMINAL']['ID_DELIVERY']?>][<?=$arResult['SERVICE_TERMINAL']['ID']?>]">
            <?foreach ($arResult['SERVICE_TERMINAL']['PARAMS']['PRICES'] as $service):?>
            <option <?if($arResult['SERVICE_TERMINAL']['CHECKED'] == 'Y' && $arResult['DPD_CITY'] == $service['TITLE']):?>selected<?endif;?> data-city="<?=$service['TITLE']?>" value="<?=$service['ID']?>"><?=$service['TITLE']?></option>
            <?endforeach;?>
        </select>
    <?endif;?>

    <input type="hidden" name="save" value="Y">
</div>
