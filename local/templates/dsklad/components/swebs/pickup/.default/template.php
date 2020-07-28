<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$this->setFrameMode(true);

use Bitrix\Main\Page\Asset;

Asset::getInstance()->addString('
<script type="text/javascript">
    window.order_ajax_path = "' . $componentPath . '";
</script>');
?>
<div class="ds-pickup pickup__page">
    <div class="default">
        <div class="points">
            <h1>Пункты <br class="hidden-lte-m">самовывоза</h1>
            <form action="" method="post">
                <div class="field__widget type-block">
                    <label for="input-about-city" class="label">Ваш город:</label>
                    <div class="field">
                        <div id="input-about-city" class="input">
                            <?
                            $APPLICATION->IncludeComponent(
                                'swebs:dpd.cities',
                                'custom',
                                array(
                                    'DPD_HL_ID' => \Dsklad\Config::getParam('hl/dpd_cities'),
                                    'COMPONENT_TEMPLATE' => 'custom'
                                ),
                                false
                            );
                            ?>
                        </div>
                    </div>
                </div>
                <?
                if (!empty($arResult['DPD']['TERMINAL'])) {
                    ?>
                    <p>Адреса пунктов самовывоза в <b class="city_title">...</b></p>
                    <?
                }
                ?>
            </form>
            <?
            if (!empty($arResult['DPD']['TERMINAL'])) {
                ?>
                <div class="scrollable">
                    <?
                    foreach ($arResult['DPD']['TERMINAL'] as $intKey => $arItem) {
                        $strClass = '';
                        if ($intKey == 0) {
                            $strClass = ' active';
                        }
						switch ($arItem['address']['streetAbbr']) {
							case 'улица':
								$arItem['address']['streetAbbr'] = 'ул.';
							break;
						}
                        ?>
                        <div class="item<?= $strClass ?>" data-terminalcode = <?=$arItem['terminalCode'];?>>
                            <a href="javascript: void(0);">
                                <b>
                                    <?= sprintf('%s %s', $arItem['address']['streetAbbr'], $arItem['address']['street']).
                                        ($arItem['address']['houseNo'] ? sprintf(', дом %s', $arItem['address']['houseNo']) : '').
                                        ($arItem['address']['structure'] ? sprintf(', стр. %s', $arItem['address']['structure']) : '')
                                    ?>
                                </b>
                            </a>
                            <a class="pickup_link" target="_blank" href="https://www.google.com/maps/dir/Current+Location/<?=$arItem['geoCoordinates']['latitude']?>,<?=$arItem['geoCoordinates']['longitude']?>">Схема проезда</a>
                            <span class="table">
                                <span class="row">
                                    <span class="th">Адрес:</span>
                                    <span class="td">
										<?= sprintf('%s %s', $arItem['address']['streetAbbr'], $arItem['address']['street']).
											($arItem['address']['houseNo'] ? sprintf(', дом %s', $arItem['address']['houseNo']) : '').
											($arItem['address']['structure'] ? sprintf(', стр. %s', $arItem['address']['structure']) : '')
										?>
									</span>
                                </span>
                                <span class="row">
                                    <?if(!empty($arItem['undeground'])):?>
                                        <span class="th">Ближайшее метро :</span>
                                        <span class="td">
                                        <?foreach (json_decode($arItem['undeground']) as $metro):?>
                                            <?=$metro?></br>
                                        <?endforeach?>
                                        </span>
                                    <?endif;?>
                                </span>
                                <span class="row">
                                    <span class="th">Режим работы:</span>
                                    <span class="td">
                                        <?
                                        foreach ($arItem['schedule'] as $arSchedule) {
                                            if ($arSchedule['operation'] == 'PaymentByBankCard') {
                                                $payByCard = 'да';
                                            }
                                            if ($arSchedule['operation'] == 'SelfDelivery') {
                                                if (!array_key_exists('weekDays', $arSchedule['timetable'])) {
                                                    foreach ($arSchedule['timetable'] as $arTimetable) {
                                                        ?>
                                                        <span class="hidden-s"><?= $arTimetable['weekDays'] ?></span>
                                                        <span class="hidden-gt-s"><?= $arTimetable['weekDays'] ?></span> <?= $arTimetable['workTime'] ?>
                                                        <br>
                                                        <?
                                                    }
                                                } else {
                                                    ?>
                                                    <span class="hidden-s"><?= $arSchedule['timetable']['weekDays'] ?></span>
                                                    <span class="hidden-gt-s"><?= $arSchedule['timetable']['weekDays'] ?></span> <?= $arSchedule['timetable']['workTime'] ?>
                                                    <br>
                                                    <?
                                                }
                                            }
                                        }
                                        ?>
                                    </span>
                                </span>
                                <?
                                $maxLength = 0;
                                $maxWidth = 0;
                                $maxHeight = 0;

                                $sumGabarit = (int)$arItem['limits']['dimensionSum'];
                                $sumGabarit = ($sumGabarit > 0) ? $sumGabarit : '0';

                                $maxGabarit = max((int)$arItem['limits']['maxLength'], (int)$arItem['limits']['maxWidth'], (int)$arItem['limits']['maxHeight']);
                                $maxGabarit = ($maxGabarit > 0) ? $maxGabarit : '0';

                                $maxWeight = (!empty($arItem['limits']['maxWeight'])) ? $arItem['limits']['maxWeight'] : '0';
                                $maxShipmentWeight = (!empty($arItem['limits']['maxShipmentWeight'])) ? $arItem['limits']['maxShipmentWeight'] : '0';

                                $maxLength = $arItem['limits']['maxLength'];
                                $maxWidth = $arItem['limits']['maxWidth'];
                                $maxHeight = $arItem['limits']['maxHeight'];

                                foreach ($arItem['extraService'] as $extraService) {
                                    if ($extraService['esCode'] == 'ОЖД') {
                                        $ozhd = (stristr($extraService['params']['value'], 'ПРОС') === false) ? 'нет' : 'да';
                                    }
                                    if ($extraService['esCode'] == 'НПП') {
                                        $npp = $extraService['params']['value'];
                                    }
                                }
                                ?>
                                <span class="row">
                                    <span class="th">Ограничения:</span>
                                    <span class="td">
                                        Макс. вес позиции: <?if($maxWeight > 0)echo $maxWeight; else echo 'без ограничений'; ?><br>
                                        Макс. вес отправления: <?if($maxShipmentWeight > 0)echo $maxShipmentWeight; else echo 'без ограничений'; ?><br>
                                        Максимальный габарит: <?if($maxGabarit > 0)echo $maxGabarit; else echo 'без ограничений'; ?><br>
                                        Сумма габаритов: <?if($sumGabarit > 0)echo $sumGabarit; else echo 'без ограничений'; ?><br>
                                        Оплата картой: <?= empty($payByCard)?' нет ': $payByCard ?><br>
                                        Наложенный платеж: <?= empty($npp)?' нет ': $npp ?><br>
                                        Проверка комплектности: <?= $ozhd ?><br>
                                        <?if($arItem['no_two_pack']):?>
                                            Макс. число посылок: 1<br>
                                        <?endif;?>
                                    </span>
                                </span>
                                <span class="row">
                                    <span class="th">Доступные услуги:</span>
                                    <span class="td">
                                        <?foreach ($arItem['services']['serviceCode'] as $code){
                                            switch ($code){
                                                case 'MXO':
                                                    echo "DPD Online Max <br/>";
                                                    break;
                                                case 'PCL':
                                                    echo "DPD Online Classic <br/>";
                                                    break;
                                                case 'ECN':
                                                    echo "Economy <br/>";
                                                    break;
                                            }
                                        }?>
                                    </span>
                                </span>
                                <span class="row">
                                    <span class="th">Описание:</span>
                                    <span class="td"><?= 'Код терминала: '.$arItem['terminalCode'].'<br>'.$arItem['address']['descript'] ?></span>
                                </span>
                            </span>
                        </div>
                        <?
                        $payByCard = '';
                        $sumGabarit = '';
                        $maxGabarit = '';
                        $maxWeight = '';
                        $maxShipmentWeight = '';
                        $npp = '';
                    }
                    ?>
                </div>
			    <?
            } else {
                ?>
				<div class="scrollable error">
					<p>Извините, но в вашем городе нет пунктов самовывоза, доступна только курьерская доставка.</p>
				</div>
                <?
            }
            ?>
        </div>
    </div>
    <div id="map" class="map">
		<?
		/* Fix for PHP 7 */
		$arResult['DPD']['mapParams']['yandex_lat'] = !is_nan($arResult['DPD']['mapParams']['yandex_lat']) ? $arResult['DPD']['mapParams']['yandex_lat'] : 0;
		$arResult['DPD']['mapParams']['yandex_lon'] = !is_nan($arResult['DPD']['mapParams']['yandex_lon']) ? $arResult['DPD']['mapParams']['yandex_lon'] : 0;
		?>
        <?$APPLICATION->IncludeComponent(
            "dsklad:map.yandex",
            "",
            Array(
                "COMPOSITE_FRAME_MODE" => "A",
                "COMPOSITE_FRAME_TYPE" => "AUTO",
                "CONTROLS" => array("fullscreenControl", "geolocationControl", "zoomControl"),
                "INIT_MAP_TYPE" => "yandex#map",
                "MAP_HEIGHT" => "100%",
                "MAP_ID" => "OrderMap",
                "MAP_WIDTH" => "100%",
                "PLACEMARKS" => $arResult['DPD']['mapParams']['PLACEMARKS'],
                "INIT_MAP_SCALE"=>$arResult['DPD']['mapParams']['yandex_scale'],
                "INIT_MAP_LON"=>$arResult['DPD']['mapParams']['yandex_lon'],
                "INIT_MAP_LAT"=>$arResult['DPD']['mapParams']['yandex_lat'],
                'CLASTER' => "Y",
                'CLASTER_SIZE'=>32,
                'CLASTER_ZOOM' => "N",
                'OPEN_BALLOON_CLASTER' => "N",
                'OPEN_BALLOON_OBJECT' => 'Y',
                'API_KEY'=>\Dsklad\Config::getParam('api_key/yandex_map'),
                'DMAP_DISABLE_POINT'=>'Y',
            )
        );?>
    </div>
</div>

<?if ($arParams['TERMINAL_CODE']) : ?>
    <script type="text/javascript">
        $(document).on('mymap.eventreadyinstance', function () {
            var terminals = document.querySelectorAll('[data-terminalcode="<?=$arParams['TERMINAL_CODE']?>"]');
            if (terminals.length > 0) {
                terminals[0].scrollIntoView({ behavior: 'instant', block: 'nearest', inline: 'start' });
                document.onreadystatechange = function() {
                    //check document state is full complete and sources ready
                    if (document.readyState == 'complete') {
                        terminals[0].click();
                    }
                };
            }
        });
    </script>
<?endif;?>