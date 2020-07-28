<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script type="text/javascript">
    function changePaySystem(param)
    {
        if (BX("account_only") && BX("account_only").value == 'Y') // PAY_CURRENT_ACCOUNT checkbox should act as radio
        {
            if (param == 'account')
            {
                if (BX("PAY_CURRENT_ACCOUNT"))
                {
                    BX("PAY_CURRENT_ACCOUNT").checked = true;
                    BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
                    BX.addClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');

                    // deselect all other
                    var el = document.getElementsByName("PAY_SYSTEM_ID");
                    for(var i=0; i<el.length; i++)
                        el[i].checked = false;
                }
            }
            else
            {
                BX("PAY_CURRENT_ACCOUNT").checked = false;
                BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
                BX.removeClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
            }
        }
        else if (BX("account_only") && BX("account_only").value == 'N')
        {
            if (param == 'account')
            {
                if (BX("PAY_CURRENT_ACCOUNT"))
                {
                    BX("PAY_CURRENT_ACCOUNT").checked = !BX("PAY_CURRENT_ACCOUNT").checked;

                    if (BX("PAY_CURRENT_ACCOUNT").checked)
                    {
                        BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
                        BX.addClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
                    }
                    else
                    {
                        BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
                        BX.removeClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
                    }
                }
            }
        }

        submitForm();
    }
</script>
<fieldset class="payment ajaxreload" data-order-page="payment-fieldset">
    <?if(!empty($arResult["PAY_SYSTEM"])):?>
        <div class="legend">
            <div class="title">
                <?=GetMessage("SOA_TEMPL_PAY_SYSTEM")?>
                <br/>
                <font color="red" size="2">Скидка <?=number_format($arResult['PRICE_WITHOUT_DISCOUNT_VALUE'] / 100 * 3.5, 0, '.', ' ');?>.–, при оплате банковской картой на сайте</font>
            </div>
        </div>
        <div class="options">
            <?foreach($arResult["PAY_SYSTEM"] as $arPaySystem):?>
                <?
                $arPaySystem["ID"] == 2 ? $bigLable = 'order_big_label' : $bigLable = '';
                if($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")){$active = 'active';}else{ $active = '';}
                ?>
                <label class="pay_system payconfirm <?=$bigLable;?> <?=$active?>" id="l_<?=$arPaySystem["ID"]?>" for="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>" onclick="BX('ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>').checked=true;changePaySystem();">
                    <input type="radio"
                           id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
                           name="PAY_SYSTEM_ID"
                           value="<?=$arPaySystem["ID"]?>"
                        <?if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
                           onclick="changePaySystem();"
                    />
                    <span class="icon__card2">
                        <svg>
                            <?if($arPaySystem['ID'] == 2):?>
                                <use xlink:href="<?=SITE_TEMPLATE_PATH?>/images/sprite.svg#card2"></use>
                            <?elseif($arPaySystem['ID'] == 3):?>
                                <use xlink:href="<?=SITE_TEMPLATE_PATH?>/images/sprite.svg#wallet"></use>
                            <?elseif ($arPaySystem['CODE'] == "YANDEX_INSTALLMENTS"):?>
                                <use xlink:href="<?=SITE_TEMPLATE_PATH?>/images/sprite.svg#cashout"></use>
                            <?elseif ($arPaySystem['CODE'] == "APPLE_PAY"):?>
                                <use xlink:href="<?=SITE_TEMPLATE_PATH?>/images/sprite.svg#apple-pay"></use>
                            <?else:?>
                                <use xlink:href="<?=SITE_TEMPLATE_PATH?>/images/sprite.svg#bank"></use>
                            <?endif?>
                        </svg>
                    </span>
                    <b><?=$arPaySystem['NAME'];?></b>
                    <?if(!empty($arPaySystem['DESCRIPTION'])):?>
                        <div class="discript">
                            <?=$arPaySystem['DESCRIPTION'];?>
                        </div>
                    <?endif;?>
                </label>
            <?endforeach;?>
        </div>
    <?endif;?>
</fieldset>
