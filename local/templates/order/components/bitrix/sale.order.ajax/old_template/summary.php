<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$bDefaultColumns = $arResult["GRID"]["DEFAULT_COLUMNS"];
$colspan = ($bDefaultColumns) ? count($arResult["GRID"]["HEADERS"]) : count($arResult["GRID"]["HEADERS"]) - 1;
$bPropsColumn = false;
$bUseDiscount = false;
$bPriceType = false;
$bShowNameWithPicture = ($bDefaultColumns) ? true : false; // flat to show name and picture column in one column
?>

    <div class="basket__widget ajaxreload">
        <div class="list" id="basket_items" data-order-page="basket-fieldset">
            <?foreach ($arResult["GRID"]["ROWS"] as $k => $arData):?>
                <div class="item" data-order-page="basket-item-field" data-item-name="<?=$arData['data']["NAME"]?>" data-item-id="<?=$arData['data']['PRODUCT_ID'];?>" data-item-price="<?=$arData['data']['PRICE'];?>" data-item-category="<?=$arData['data']['SECTION']["NAME"]?>">
                <div class="image">
                    <?
                    if (strlen($arData["data"]["PREVIEW_PICTURE_SRC"]) > 0):
                        $url = $arData["data"]["PREVIEW_PICTURE_SRC"];
                    elseif (strlen($arData["data"]["DETAIL_PICTURE_SRC"]) > 0):
                        $url = $arData["data"]["DETAIL_PICTURE_SRC"];
                    else:
                        $url = $templateFolder."/images/no_photo.png";
                    endif;
                    ?>
                    <img src="<?=$url?>" alt="<?=$arData['NAME']?>">
                </div>
                <div class="whole-row">
                    <div class="info-row">
                        <div class="info">
                            <div class="category">
                                <a href="<?=$arData['data']['SECTION']["SECTION_PAGE_URL"]?>"><?=$arData['data']['SECTION']["NAME"]?></a>
                            </div>
                            <div class="name">
                                <a href="<?=$arData['data']["DETAIL_PAGE_URL"]?>"><?=$arData['data']["NAME"]?></a>
                            </div>
                            <div class="data">
                                <p class="counter-fallback hidden-gt-s"><span>Кол-во:</span>
                                    <del><?=$arData['data']['QUANTITY'];?></del>
                                </p>
                                <?if(!empty($arData['data']['PROPERTY_CML2_ARTICLE_VALUE'])):?>
                                    <p class="article"><span>Артикул:</span> <?=$arData['data']['PROPERTY_CML2_ARTICLE_VALUE'];?></p>
                                <?endif;?>
                                <?if (!empty($arData['data']['PROPERTY_VID_KH_KA_VALUE'])) : ?>
                                    <p class="color"><span>Вид:</span> <?=$arData['data']['PROPERTY_VID_KH_KA_VALUE']?></p>
                                <?endif;?>
                                <?if(!empty($arData['data']['PROPERTY_KOD_TSVETA_VALUE']) && empty($arData['data']['PROPERTY_VID_KH_KA_VALUE'])):?>
                                    <?$arColor = explode('#',$arData['data']['PROPERTY_KOD_TSVETA_VALUE'])?>
                                    <p class="color"><span>Цвет:</span> <?=$arColor[0]?></p>
                                <?endif;?>
                            </div>
                        </div>
                        <div class="price">
                            <span class="hidden-l">Цена за <?=$arData['data']['MEASURE_TEXT'];?>.</span>
                            <b><?=number_format($arData['data']['PRICE'], 0, '.', ' ');?>.–</b>
                            <span class="hidden-lte-m"> <?=$arData['data']['MEASURE_TEXT'];?>.</span>
                            <?if($arData['data']['DISCOUNT_FLAG']):?>
                                <div class="sale__widget">%</div>
                            <?endif;?>
                        </div>
                    </div>
                    <div class="count">
                        <div data-min="1" data-measure=" <?=$arData['data']['MEASURE_TEXT'];?>." class="counter__widget" data-product_id="<?=$arData['data']['ID'];?>">
                            <a data-add="-1" product_id="<?=$arData['data']['ID'];?>" class="count_a">-</a>
                            <div class="input js-filled">
                                <input type="tel" data-name="product" name="good[<?=$arData['data']['PRODUCT_ID'];?>]" product_id="<?=$arData['data']['ID'];?>" autocomplete="off" value="<?=$arData['data']['QUANTITY'];?> <?=$arData['data']['MEASURE_TEXT'];?>." class="order_quantity" data-gaproduct-quantity="<?=$arData['data']['QUANTITY'];?>">
                            </div>
                            <a data-add="1" product_id="<?=$arData['data']['ID'];?>" class="count_a">+</a>
                        </div>
                    </div>
                    <div class="remove">
                        <a href="<?=$arData['data']['ID'];?>" data-ga-analys-btn="basket-delete-item" class="remove_basket_items">
                                            <span class="icon__cross">
                                                <svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#cross"></use></svg>
                                            </span>
                        </a>
                    </div>
                    <div class="total">
                        <p class="hidden-gt-s">Стоимость:</p>
                        <p>
                            <b><?=number_format($arData['data']['SUM_NUM'], 0, '.', ' ');?>.–</b>
                            <?if($arData['data']['AVAILABILITY_FLAG']):?>
                                <span>В наличии</span>
                            <?else:;?>
                                <?if(!empty($arData['data']['PROPERTY_ARRIVAL_DATE_VALUE'])):?>
                                    <span style="color:#ea1e31">Ожидается <?=$arData['data']['PROPERTY_ARRIVAL_DATE_VALUE']?></span>
                                <?else:;?>
                                    <span style="color:#ea1e31">Доступно с предзаказом</span>
                                <?endif;?>
                            <?endif;?>
                        </p>
                    </div>
                </div>
            </div>
            <?endforeach;?>
        </div>
        <?if($arParams['SVETOFOR_OK'] == 'Y' && $arResult['SVETOFOR'] > 0 && $arResult['SVETOFOR'] != 4):?>
            <div class="cart-add-more">
                <div class="cart_info">Добавь ещё <?=$arResult['SVETOFOR'];?> <span class="red">Eames Style DSW</span> для уменьшения цены за штуку</div>
            </div>
        <?endif;?>
        <div class="basket_total ajaxreload">
            <div class="title total_summ_title">
                Итого: <?=$arResult['COUNT_BASKET'];?> <?=getWord3($arResult['COUNT_BASKET'])?> на сумму <?=number_format(($arResult['ORDER_PRICE'] - $arResult['SERVICES_PRICE']), 0, '.', ' ');?>.–</div>
        </div>
        <?if(!empty($arResult['SERVICE_TO_BASKET']) && !$arResult['NO_SERVICES_BASKET']):?>
            <div class="services">
                <?foreach ($arResult['SERVICE_TO_BASKET'] as $service):?>
                    <label class="label service_warranty">
                        <span class="icon">
                            <span class="icon__warranty">
                                <img src="<?=$service['PROPERTY_SVG_SPRITE_VALUE']?>" alt="">
                            </span>
                        </span>
                        <span class="checkbox">
                            <input data-name="service" type="checkbox" name="serv[]" class="order_service" value="<?=$service['ID']?>" autocomplete="off" <?if($service['CHECkED'] == 'Y') echo 'checked';?>>
                            <u class="square">
                                <span class="icon__check">
                                    <svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#check"></use></svg>
                                </span>
                            </u>
                        </span>
                        <span class="price">
                            <span class="hidden-gt-s">Стоимость: </span>
                            <b><?=number_format(($arResult['ORDER_PRICE'] - $arResult['SERVICES_PRICE'])/10, 0, '.', ' ');?>.–</b>
                        </span>
                        <span class="title"><?=$service['PREVIEW_TEXT']?></span>
                    </label>
                <?endforeach;?>
            </div>
        <?endif;?>
    </div>
