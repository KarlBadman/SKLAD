<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$templateData = array(
	'TEMPLATE_THEME' => $this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/colors.css',
	'TEMPLATE_CLASS' => 'bx-'.$arParams['TEMPLATE_THEME']
);

if (isset($templateData['TEMPLATE_THEME']))
{
	$this->addExternalCss($templateData['TEMPLATE_THEME']);
}
?>
<div class="bx-filter ds-filter">
		<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get" class="smartfilter">
			<?foreach($arResult["HIDDEN"] as $arItem):?>
			<input type="hidden" name="<?echo $arItem["CONTROL_NAME"]?>" id="<?echo $arItem["CONTROL_ID"]?>" value="<?echo $arItem["HTML_VALUE"]?>" />
			<?endforeach;?>
                <div class="ds-filter__list">
                    <?foreach($arResult["ITEMS"] as $key=>$arItem)//prices
                    {
                        $key = $arItem["ENCODED_ID"];
                        if(isset($arItem["PRICE"])):
                            if ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0)
                                continue;

                            $step_num = 4;
                            $step = ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"]) / $step_num;
                            $prices = array();
                            if (Bitrix\Main\Loader::includeModule("currency"))
                            {
                                for ($i = 0; $i < $step_num; $i++)
                                {
                                    $prices[$i] = CCurrencyLang::CurrencyFormat($arItem["VALUES"]["MIN"]["VALUE"] + $step*$i, $arItem["VALUES"]["MIN"]["CURRENCY"], false);
                                }
                                $prices[$step_num] = CCurrencyLang::CurrencyFormat($arItem["VALUES"]["MAX"]["VALUE"], $arItem["VALUES"]["MAX"]["CURRENCY"], false);
                            }
                            else
                            {
                                $precision = $arItem["DECIMALS"]? $arItem["DECIMALS"]: 0;
                                for ($i = 0; $i < $step_num; $i++)
                                {
                                    $prices[$i] = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step*$i, $precision, ".", "");
                                }
                                $prices[$step_num] = number_format($arItem["VALUES"]["MAX"]["VALUE"], $precision, ".", "");
                            }
                            ?>
                            <div class="ds-filter__price" data-role="bx_filter_block">
                                <div class="ds-filter__price-input">
                                    <div class="ds-filter__price-item js-catalog-price">
                                        <input
                                                class="min-price"
                                                type="text"
                                                name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
                                        id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
                                        value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
                                        size="5"
                                        placeholder="<?=current($prices)?>"
                                        onkeyup="smartFilter.keyup(this)"
                                        />
                                        <span class="price-icon price-icon--clear js-price-clear"></span>
                                    </div>
                                    <div class="ds-filter__price-item js-catalog-price">
                                        <input
                                                class="max-price"
                                                type="text"
                                                name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
                                        id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
                                        value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
                                        size="5"
                                        placeholder="<?=end($prices)?>"
                                        onkeyup="smartFilter.keyup(this)"
                                        />
                                        <span class="price-icon price-icon--clear js-price-clear"></span>
                                    </div>
                                </div>

                                <div class="ds-filter__track-container">
                                    <div class="bx-ui-slider-track" id="drag_track_<?=$key?>">
                                        <div class="bx-ui-slider-pricebar-vd" style="left: 0;right: 0;" id="colorUnavailableActive_<?=$key?>"></div>
                                        <div class="bx-ui-slider-pricebar-vn" style="left: 0;right: 0;" id="colorAvailableInactive_<?=$key?>"></div>
                                        <div class="bx-ui-slider-pricebar-v"  style="left: 0;right: 0;" id="colorAvailableActive_<?=$key?>"></div>
                                        <div class="bx-ui-slider-range" id="drag_tracker_<?=$key?>"  style="left: 0%; right: 0%;">
                                            <a class="bx-ui-slider-handle left"  style="left:0;" href="javascript:void(0)" id="left_slider_<?=$key?>"></a>
                                            <a class="bx-ui-slider-handle right" style="right:0;" href="javascript:void(0)" id="right_slider_<?=$key?>"></a>
                                        </div>
                                        <div class="ds-filter__price-value left"><span><?=$prices[0]?></span></div>
                                        <div class="ds-filter__price-value right>"><span><?=end($prices)?></span></div>


                                    </div>
                                </div>
                            </div>
                            <?
                            $arJsParams = array(
                                "leftSlider" => 'left_slider_'.$key,
                                "rightSlider" => 'right_slider_'.$key,
                                "tracker" => "drag_tracker_".$key,
                                "trackerWrap" => "drag_track_".$key,
                                "minInputId" => $arItem["VALUES"]["MIN"]["CONTROL_ID"],
                                "maxInputId" => $arItem["VALUES"]["MAX"]["CONTROL_ID"],
                                "minPrice" => $arItem["VALUES"]["MIN"]["VALUE"],
                                "maxPrice" => $arItem["VALUES"]["MAX"]["VALUE"],
                                "curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
                                "curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
                                "fltMinPrice" => intval($arItem["VALUES"]["MIN"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MIN"]["FILTERED_VALUE"] : $arItem["VALUES"]["MIN"]["VALUE"] ,
                                "fltMaxPrice" => intval($arItem["VALUES"]["MAX"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MAX"]["FILTERED_VALUE"] : $arItem["VALUES"]["MAX"]["VALUE"],
                                "precision" => $precision,
                                "colorUnavailableActive" => 'colorUnavailableActive_'.$key,
                                "colorAvailableActive" => 'colorAvailableActive_'.$key,
                                "colorAvailableInactive" => 'colorAvailableInactive_'.$key,
                            );
                            ?>
                            <script type="text/javascript">
                                BX.ready(function(){
                                    window['trackBar<?=$key?>'] = new BX.Iblock.SmartFilter(<?=CUtil::PhpToJSObject($arJsParams)?>);
                                });
                            </script>
                        <?endif;
                    }

                    //not prices
                    foreach($arResult["ITEMS"] as $key=>$arItem):?>
                        <?if(empty($arItem["VALUES"])	|| isset($arItem["PRICE"])) continue;?>
                        <?if ($arItem["DISPLAY_TYPE"] == "A"	&& ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0))continue;?>
                        <div class="ds-filter__item" data-role="bx_filter_block">
                            <div class="ds-filter__name_property"><?=mb_strtolower($arItem["NAME"])?></div>
                            <div class="ds-filter__item-scroll">
                                <div class="scrollbar">
                                    <div class="handle"></div>
                                </div>
                                <div class="ds-filter__property js-sly-vertical" id="nonitembased">
                                    <div class="slidee">
                                        <?foreach($arItem["VALUES"] as $val => $ar):?>
                                        <span class="ds-filter__label">
                                        <input
                                                type="checkbox"
                                                value="<? echo $ar["HTML_VALUE"] ?>"
                                            name="<? echo $ar["CONTROL_NAME"] ?>"
                                            id="<? echo $ar["CONTROL_ID"] ?>"
                                            <? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
                                            onclick="smartFilter.click(this)"
                                        />
                                        <label data-role="label_<?=$ar["CONTROL_ID"]?>" for="<? echo $ar["CONTROL_ID"] ?>" class="ds-filter__label-text" title="<?=$ar["VALUE"];?>"><?=$ar["VALUE"];?><?
                                            if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
                                                ?>&nbsp;(<span data-role="count_<?=$ar["CONTROL_ID"]?>"><? echo $ar["ELEMENT_COUNT"]; ?></span>)<?
                                            endif;?></label>
                                        </span>
                                        <?endforeach;?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?endforeach;?>
                </div>
            <div class="ds-filter__buttons">
                <input
                        class="ds-btn ds-btn--default"
                        type="submit"
                        id="set_filter"
                        name="set_filter"
                        value="<?=GetMessage("CT_BCSF_SET_FILTER")?>"
                />
                <input
                        class="ds-btn ds-btn--light"
                        type="submit"
                        id="del_filter"
                        name="del_filter"
                        value="<?=GetMessage("CT_BCSF_DEL_FILTER")?>"
                />
                <div class="bx-filter-popup-result <?if ($arParams["FILTER_VIEW_MODE"] == "VERTICAL") echo $arParams["POPUP_POSITION"]?>" id="modef" <?if(!isset($arResult["ELEMENT_COUNT"])) echo 'style="display:none"';?> style="display: inline-block;">
                    <?echo GetMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_num">'.intval($arResult["ELEMENT_COUNT"]).'</span>'));?>
                    <span class="arrow"></span>
                    <br/>
                    <a href="<?echo $arResult["FILTER_URL"]?>" target=""><?echo GetMessage("CT_BCSF_FILTER_SHOW")?></a>
                </div>
            </div>
		</form>
</div>
<script type="text/javascript">
	var smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($arResult["FORM_ACTION"])?>', '<?=CUtil::JSEscape($arParams["FILTER_VIEW_MODE"])?>', <?=CUtil::PhpToJSObject($arResult["JS_FILTER_PARAMS"])?>);
</script>