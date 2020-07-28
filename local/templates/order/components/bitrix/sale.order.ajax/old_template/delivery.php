<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<fieldset class="delivery payment">
    <input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?=$arResult["BUYER_STORE"]?>" />
    <div class="ajaxreload legend">
        <div class="title">
            Для вашего города <span><?=count($arResult['DELIVERY'])?> <?=getWord(count($arResult['DELIVERY']))?> доставки</span>
        </div>
    </div>
    <?if(array_key_exists($arResult['USER_VALS']['DELIVERY_ID'],$arResult['DELIVERY']) === false){
        foreach ($arResult['DELIVERY'] as $key => $val){
            $arResult['USER_VALS']['DELIVERY_ID'] = $val['ID'];
            $arResult['DELIVERY'][$key]['CHECKED'] = 'Y';
            break;
        }
    }?>
    <div class="ajaxreload options">
        <input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?=$arResult["BUYER_STORE"]?>" />
        <?
        if($arResult['ORDER_DATA']['PERSON_TYPE_OLD'] != $arResult['ORDER_DATA']['PERSON_TYPE']){
            if(array_search($arResult['ORDER_DATA']['DELIVERY_ID'],$arParams['MAP_DELIVERY']) !== false){
                $old_del = 'pickup';
            }else{
                $old_del = 'delivery2';
            }
        }
        ?>
        <?foreach ($arResult['DELIVERY'] as $arDelivery):?>
            <?
            if(array_search($arDelivery['ID'],$arParams['MAP_DELIVERY']) !== false){
                $icon = 'pickup';
                $name = 'Доставка в <br/>ПВЗ';
            }else{
                $icon = 'delivery2';
                $name = 'Курьерская  <br/>доставка';
            }
            if($arDelivery['ACTIVE'] == 'N'){
                $non = 'style="display:none;"';
            }else{
                $non = '';
            }
            if($arResult['ORDER_DATA']['PERSON_TYPE_OLD'] != $arResult['ORDER_DATA']['PERSON_TYPE'] && $old_del == $icon){
                $arDelivery["CHECKED"] = 'Y';
                $arResult['USER_VALS']['DELIVERY_ID'] = $arDelivery['ID'];
            }elseif($arResult['ORDER_DATA']['PERSON_TYPE_OLD'] != $arResult['ORDER_DATA']['PERSON_TYPE']){
                $arDelivery["CHECKED"] = 'N';
            }
            if($arDelivery["CHECKED"]=="Y"){
                $active = 'active';
                $deliveryData = json_decode($arDelivery['CALCULATE_DESCRIPTION']);
            }else{
                $active = '';
            };
            if(count($arResult['DELIVERY']) == 1){
                $active = 'active';
                $arDelivery["CHECKED"] = 'Y';
            }
            ?>
            <label data-name="<?=$icon?>" class="order_big_label_delivery <?=$active;?>" for="ID_DELIVERY_ID_<?=$arDelivery["ID"]?>" <?=$clickHandler?> <?=$non?>>
                <input type="radio"
                       id="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>"
                       name="<?=htmlspecialcharsbx($arDelivery["FIELD_NAME"])?>"
                       value="<?= $arDelivery["ID"] ?>"
                    <?if ($arDelivery["CHECKED"]=="Y") echo "checked=\"checked\"";?>
                       onclick="submitForm();"
                />
                <span class="icon__card2">
                    <svg>
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#<?=$icon?>"></use>
                    </svg>
                </span>
                <b><?= $name ?></b>
            </label>
        <?endforeach;?>
    </div>
    <div class="tabs-content">
        <div class="tab pickup <?if(array_search($arResult['USER_VALS']['DELIVERY_ID'],$arParams['MAP_DELIVERY']) !== false) echo 'active';?>">
            <div class="field__widget type-inline">
                <label for="input-delivery-point" class="label">Пункт самовывоза ТК:</label>
                <div class="field">
                    <div class="select ajaxreload">

                        <?if(array_search($arResult['USER_VALS']['DELIVERY_ID'],$arParams['MAP_DELIVERY']) !== false):?>

                        <select id="input-delivery-point" name="ORDER_PROP_<?=$arResult['PROP_TERMINAL'];?>">
                            <?$isTerminalOptString = ''; $isPartnerPVZ = '';?>
                            <?
                            $i = 0;
                            $cityHasNotChanged = false;
                            foreach ($arResult['TERMINAL'] as $arTerminal) {
                                ?>
                                <?
                                $x = str_replace(
                                    array('ул.,', 'шоссе,', 'улица,', 'ул.  пр-кт', 'проезд'),
                                    array('ул. ', 'ш ', 'ул. ', 'пр-кт ', ' '),
                                    $arTerminal['address']['terminalAddress']
                                );
                                $x_me = str_replace(
                                    array('ул. пр-кт', 'ул.  проезд', 'ул.  пл', 'ул.  пер', 'ул.  ш', 'ул.  проспект', 'проспект', 'ул. проспект'),
                                    array('пр-кт', 'проезд', 'пл', 'пер', 'ш.', 'пр-кт', 'пр-кт', 'пр-кт'),
                                    $x
                                );
                                $x = str_replace('ул.  б-р', 'б-р', $x_me);
                                ?>
                                <?
                                if($i == 0)$rezervTerminal = $x;
                                if($arResult['ORDER_DATA']['ORDER_PROP'][$arResult['PROP_TERMINAL']] == $arTerminal['terminalCode']){$cityHasNotChanged = true;}
                                if($arResult['ORDER_DATA']['ORDER_PROP'][$arResult['PROP_TERMINAL']] == $arTerminal['terminalCode'] || (empty($arResult['ORDER_DATA']['ORDER_PROP'][$arResult['PROP_TERMINAL']]) && $i = 0)){
                                    $adresTerminal = $x;
                                }?>
                                <?if ($arTerminal['is_terminal'] == 'Y') : ?>
                                    <?$isTerminalOptString .= '<option class="'.$arTerminal['css_class'].'" '.($arResult['CURRENT_DELIVERY_POINT_CODE'] == $arTerminal['terminalCode'] ? "selected=\"selected\"" : "").' value="'.$arTerminal['terminalCode'].'" cityId="'.$arTerminal['address']['cityId'].'" data-nppSum="'.$arTerminal['npp_sum'].'" data-IsTerminal="'.($arTerminal['is_terminal'] == 'Y' ? 'Y' : 'N').'">'. $x .'</option>';?>
                                <?else : ?>
                                    <?$isPartnerPVZ .= '<option class="'.$arTerminal['css_class'].'" '.($arResult['CURRENT_DELIVERY_POINT_CODE'] == $arTerminal['terminalCode'] ? "selected=\"selected\"" : "").' value="'.$arTerminal['terminalCode'].'" cityId="'.$arTerminal['address']['cityId'].'" data-nppSum="'.$arTerminal['npp_sum'].'" data-IsTerminal="'.($arTerminal['is_terminal'] == 'Y' ? 'Y' : 'N').'">' . $x . '</option>';?>
                                <?endif;?>
                                <?$i++;
                            }?>
                            <optgroup label="Терминалы самовывоза"><?=$isTerminalOptString?></optgroup>
                            <optgroup label="Партнерские пункты самовывоза"><?=$isPartnerPVZ?></optgroup>
                        </select>
                            <?if(!$cityHasNotChanged){$adresTerminal = $rezervTerminal;}?>
                        <?endif;?>

                        <div class="fallback">
                            <div>
                                                                    <span class="icon__darr">
                                                                        <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#darr"></use></svg>
                                                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <fieldset class="info ajaxreload">
                <div class="field__widget type-inline">
                    <label for="input-about-order-comment" class="label">Дополнительная информация:</label>
                    <?if (!empty($arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']]['PERIOD_TEXT'])) : ?>
                        <?if(is_int($arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']]['PERIOD_TEXT'])
                            && array_search($arResult['USER_VALS']['PAY_SYSTEM_ID'],$arParams['NAL']) !== false)
                        {
                            $arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']]['PERIOD_TEXT'] = floor($arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']]['PERIOD_TEXT'] * 1.5);
                            $deyDeliveriMes = getWord4($arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']]['PERIOD_TEXT']);
                        }else{
                            $deyDeliveriMes = 'рабочих дня';
                        }?>
                        <div class="field">
                            <span>Ориентировочный срок доставки заказа <span data-dpc-days="dpc-str"> <?=$arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']]['PERIOD_TEXT']?></span> <?=$deyDeliveriMes?> после передачи в транспортную компанию. При получении нужен паспорт. </span>
                        </div>
                    <?else : ?>
                        <div class="field">
                            <span>Расчет срока доставки в данный момент недоступен</span>
                        </div>
                    <?endif;?>
                </div>
            </fieldset>
            <div class="map">
                <?
                // определяем пункты самовывоза, которые необходимо скрыть
                function subtract_placemarks($from, $delete_this) {
                    $result = [];
                    foreach ($from as $p) {
                        $result[] = $p['TERMINAL'];
                    }
                    foreach ($delete_this as $placemark) {
                        foreach ($from as $i => $placemark2) {
                            if ($placemark['TERMINAL'] == $placemark2['TERMINAL'])
                                unset($result[$i]);
                        }
                    }
                    return $result;
                }
                $arResult['placemarksToHide'] = subtract_placemarks(
                    $arResult['mapParams']['PLACEMARKS'],
                    $arResult['mapParamsLimits']['PLACEMARKS']
                );
                ?>

                <?if(!empty($arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']])):?>
                    <?foreach ($arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']]['EXTRA_SERVICES'] as $extraServiceId => $extraService):?>
                        <?if($extraService->canUserEditValue()) continue;?>
                        <?$extraServiceParams = $extraService->getParams();?>
                        <?if(is_array($extraServiceParams["PRICES"])):?>
                            <select style="display: none"  data-name="not_terminal" name="DELIVERY_EXTRA_SERVICES[<?=$arResult['USER_VALS']['DELIVERY_ID'];?>][<?=$extraServiceId?>]">
                                <?foreach ($extraServiceParams["PRICES"] as $key => $val):?>
                                    <?if($_POST['DELIVERY_EXTRA_SERVICES'][$arResult['USER_VALS']['DELIVERY_ID']][$extraServiceId] == $key){
                                        $selected = 'selected';
                                    }else{
                                        $selected = '';
                                    }?>
                                    <option data-city="<?=$val['TITLE']?>" value="<?=$key?>"><?=$val['TITLE']?></option>
                                <?endforeach?>
                            </select>
                        <?endif;?>
                    <?endforeach;?>
                <?endif;?>

                <div id="map" class="embed map_limitation">

                    <?if(!$_POST["is_ajax_post"] == "Y"):?>

                    <?$APPLICATION->IncludeComponent(
                        "dsklad:map.yandex",
                        "",
                        Array(
                            "COMPOSITE_FRAME_MODE" => "A",
                            "COMPOSITE_FRAME_TYPE" => "AUTO",
                            "CONTROLS" => array("fullscreenControl", "geolocationControl", "zoomControl"),
                            "INIT_MAP_TYPE" => "yandex#map",
                            "MAP_HEIGHT" => "600",
                            "MAP_ID" => "OrderMap",
                            "MAP_WIDTH" => "100%",
                            "PLACEMARKS" => $arResult['mapParams']['PLACEMARKS'],
                            "INIT_MAP_SCALE"=>$arResult['mapParams']['yandex_scale'],
                            "INIT_MAP_LON"=>$arResult['mapParams']['yandex_lon'],
                            "INIT_MAP_LAT"=>$arResult['mapParams']['yandex_lat'],
                            'CLASTER' => "Y",
                            'CLASTER_SIZE'=>32,
                            'CLASTER_ZOOM' => "N",
                            'OPEN_BALLOON_CLASTER' => "N",
                            'OPEN_BALLOON_OBJECT' => 'Y',
                            'API_KEY'=>\Dsklad\Config::getParam('api_key/yandex_map'),
                            'DMAP_DISABLE_POINT'=>'Y',
                        )
                    );?>

                    <?endif;?>
                </div>
            </div>
        </div>

        <div class="ajaxreload tab delivery <?if(array_search($arResult['USER_VALS']['DELIVERY_ID'],$arParams['MAP_DELIVERY']) === false || empty($arResult['TERMINAL'])) echo 'active';?>">
            <?PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_N"], $arParams["TEMPLATE_LOCATION"],$arParams['PARAMS_DELIVERY'],$arResult["USER_VALS"]["PERSON_TYPE_ID"]);?>
            <?if(!empty($arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']]['EXTRA_SERVICES'])):?>
                <div class="field__widget type-inline field-additions fa_courier">
                    <div class="label">Дополнительно:</div>
                    <div class="field">
                        <?if(!empty($arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']])):?>
                            <?if($_POST['PERSON_TYPE'] == 2):?>
                                <span>Стоимость указанных опций уточняйте у менеджера:</span>
                            <?endif;?>
                            <?foreach ($arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']]['EXTRA_SERVICES'] as $extraServiceId => $extraService):?>
                                <?if(!$extraService->canUserEditValue()) continue;?>
                                <?$extraServiceParams = $extraService->getParams();?>
                                <?if($_POST['DELIVERY_EXTRA_SERVICES'][$arResult['USER_VALS']['DELIVERY_ID']][$extraServiceId] == 'Y'){
                                    $checked = 'checked';
                                }else{
                                    $checked = '';
                                }?>
                                <label class="label__widget">
                                    <input type="hidden" name="DELIVERY_EXTRA_SERVICES[<?=$arResult['USER_VALS']['DELIVERY_ID']?>][<?=$extraServiceId?>]" value="N">
                                    <span class="row">
                                        <?if($arResult["USER_VALS"]["PERSON_TYPE_ID"] !=2):?>
                                        <span class="control">
                                            <input <?=$checked;?> onclick="submitForm();" onchange="<?=$extraServiceParams['ONCHANGE']?>" type="checkbox" class="delivery_service" name="DELIVERY_EXTRA_SERVICES[<?=$arResult['USER_VALS']['DELIVERY_ID'];?>][<?=$extraServiceId?>]" value="Y" num="<?=$extraService->getPrice()?>" autocomplete="off" data-dserv-code="delivery_weekend">
                                            <u class="square">
                                                <span class="icon__check">
                                                    <svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#check"></use></svg>
                                                </span>
                                            </u>
                                        </span>
                                            <span class="label">
                                            <?=$extraService->getName()?>
                                                <?if($extraService->getPrice() > 0):?>
                                                    — <?=$extraService->getPrice()?> руб.–
                                                <?endif;?>
                                        </span>
                                        <?else:?>
                                            <span class="label">
                                                —
                                            <?=$extraService->getName()?>
                                                <?if($extraService->getPrice() > 0):?>
                                                    — <?=$extraService->getPrice()?> руб.–
                                                <?endif;?>
                                        </span>
                                        <?endif;?>

                                    </span>
                                </label>
                            <?endforeach;?>
                        <?endif;?>
                    </div>
                    <div class="field__widget type-inline field-total">
                        <div class="label">Общая стоимость доставки:</div>
                        <div class="field">
                            <?if($arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']]['PRICE'] > 0):?>
                                <?if(is_int($arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']]['PERIOD_TEXT'])
                                    && array_search($arResult['USER_VALS']['PAY_SYSTEM_ID'],$arParams['NAL']) !== false)
                                {
                                    $arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']]['PERIOD_TEXT'] = floor($arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']]['PERIOD_TEXT'] * 1.5);
                                    $deyDeliveriMes = getWord4($arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']]['PERIOD_TEXT']);
                                }else{
                                    $deyDeliveriMes = 'рабочих дня';
                                }?>
                                <b id="delivery_coast"><?=number_format($arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']]['PRICE'], 0, '.', ' ');?>.–</b>
                                <span>Ориентировочный срок доставки заказа <l data-dpc-days="dpc-str"><?=$arResult['DELIVERY'][$arResult['USER_VALS']['DELIVERY_ID']]['PERIOD_TEXT']?></l> <?=$deyDeliveriMes;?> после передачи в транспортную компанию. При получении нужен паспорт. </span>
                            <?else:?>
                                <span>Стоимость доставки уточняйте у менеджеров <a class="tel_fan_link" href="tel:88007771274">8 800 777-12-74</a></span>
                            <?endif;?>
                        </div>
                    </div>
                </div>
            <?endif;?>
        </div>
    </div>
</fieldset>

<fieldset class="comment">
    <div class="field__widget type-inline">
        <label for="input-about-order-comment" class="label">Комментарий к заказу:</label>
        <div class="field">
            <div class="input">
                <textarea id="input-about-order-comment" name="ORDER_DESCRIPTION" rows="3" placeholder="Напишите ваше пожелание к заказу"></textarea>
            </div>
        </div>
    </div>
</fieldset>

