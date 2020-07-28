<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
use Bitrix\Main\Page\Asset;

Asset::getInstance()->addString('
<script type="text/javascript">
    window.order_ajax_path = "' . $componentPath . '";
</script>');
//__($arResult['DPD']['TERMINAL']);
?>

<div class="pickup__page">
    <div class="default">
        <div class="points">
            <h1>Пункты <br class="hidden-lte-m">самовывоза</h1>

            <form action="" method="post">
                <div class="field__widget type-block">
                    <label for="input-about-city" class="label">Ваш город:</label>
                    <div class="field">
                        <div id="input-about-city" class="input">
                            <? $APPLICATION->IncludeComponent(
                                "swebs:dpd.cities",
                                "",
                                array(
                                    "DPD_HL_ID" => 22,
                                    "COMPONENT_TEMPLATE" => ".default"
                                ),
                                false
                            ); ?>
                        </div>
                    </div>
                    <? /*<div class="field">
                        <div id="input-about-city" placeholder="Выберите город" class="select">
                            <select name="city">
                                <option value="">Москва (автоматически)</option>
                                <option value="">Москва</option>
                            </select>

                            <div class="fallback"><span></span>

                                <div><span class="icon__darr">
                              <svg>
                                  <use xlink:href="<?= SITE_TEMPLATE_PATH?>/images/sprite.svg#darr"></use>
                              </svg></span>
                                </div>
                            </div>
                        </div>
                    </div>*/ ?>

                </div>
                <? if (!empty($arResult['DPD']['TERMINAL'])): ?>
                    <p>Адреса пунктов самовывоза в <b class="city_title">...</b></p>
                <? endif ?>
            </form>
            <? if (!empty($arResult['DPD']['TERMINAL'])): ?>
                <div class="scrollable">
                    <? foreach ($arResult['DPD']['TERMINAL'] as $intKey => $arItem): ?>
                        <?
                        $strClass = '';
                        if ($intKey == 0) {
                            $strClass = ' active';
                        }
                        ?>
                        <a href="javascript: void(0);" class="item<?= $strClass ?>">
                            <b><?= $arItem['address']['streetAbbr'] ?>. <?= $arItem['address']['street'] ?></b>
                            <span class="table">
                                <span class="row">
                                    <span class="th">Адрес:</span>
                                    <span class="td">
                                        <?= $arItem['address']['terminalAddress'] ?>
                                    </span>
                                </span>
                                <span class="row">
                                    <span class="th">Режим работы:</span>
                                    <span class="td">
                                        <? foreach ($arItem['schedule'] as $arSchedule): ?>
                                            <? if ($arSchedule['operation'] == 'SelfDelivery'): ?>
                                                <? if (!array_key_exists('weekDays', $arSchedule['timetable'])): ?>
                                                    <? foreach ($arSchedule['timetable'] as $arTimetable): ?>
                                                        <span class="hidden-s"><?= $arTimetable['weekDays'] ?></span>
                                                        <span
                                                            class="hidden-gt-s"><?= $arTimetable['weekDays'] ?></span> <?= $arTimetable['workTime'] ?>
                                                        <br>
                                                    <? endforeach ?>
                                                <? else: ?>
                                                    <span class="hidden-s"><?= $arSchedule['timetable']['weekDays'] ?></span>
                                                    <span
                                                        class="hidden-gt-s"><?= $arSchedule['timetable']['weekDays'] ?></span> <?= $arSchedule['timetable']['workTime'] ?>
                                                    <br>
                                                <? endif ?>
                                            <? endif ?>
                                        <? endforeach ?>
                                        <? /*<span class="hidden-s">Понедельник - Пятница</span>
                                        <span class="hidden-gt-s">Пн.-Пт.</span> 08:00 - 22:00 <br>
                                        <span class="hidden-s">Суббота - Воскресенье</span>
                                        <span class="hidden-gt-s">Сб.-Вс.</span> 10:00 - 18:00*/ ?>
                                    </span>
                                </span>
                                <span class="row">
                                    <span class="th">Описание:</span>
                                    <span class="td"><?= $arItem['address']['descript'] ?></span>
                                </span>
                            </span>
                        </a>
                    <? endforeach ?>
                </div>
            <? endif ?>
        </div>
    </div>
    <div id="map" class="map">
        <? $APPLICATION->IncludeComponent(
            "bitrix:map.yandex.view",
            "",
            Array(
                "CONTROLS" => array(),
                "INIT_MAP_TYPE" => "MAP",
                "MAP_DATA" => serialize($arResult['DPD']['mapParams']),
                "MAP_HEIGHT" => "615",
                "MAP_ID" => "",
                "MAP_WIDTH" => "100%",
                "OPTIONS" => array("ENABLE_SCROLL_ZOOM", "ENABLE_DBLCLICK_ZOOM", "ENABLE_DRAGGING")
            )
        ); ?>
    </div>
</div>