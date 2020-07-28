<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}
$this->setFrameMode(true);
?>
<script>
    if (!window.GLOBAL_arMapObjects) {
        window.GLOBAL_arMapObjects = {};
    }

    function init_<?= $arParams['MAP_ID'] ?>()
    {
	    if (!window.ymaps) {
	        return;
        }

	    if (typeof window.GLOBAL_arMapObjects['<?= $arParams['MAP_ID'] ?>'] !== 'undefined') {
	        return;
        }

	    var node = BX('BX_YMAP_<?= $arParams['MAP_ID'] ?>');
	    node.innerHTML = '';

	    var map = window.GLOBAL_arMapObjects['<?= $arParams['MAP_ID'] ?>'] = new ymaps.Map(node, {
		    center: [<?= $arParams['INIT_MAP_LAT'] ?>, <?= $arParams['INIT_MAP_LON'] ?>],
		    zoom: <?= $arParams['INIT_MAP_SCALE'] ?>,
		    type: 'yandex#<?= $arResult['ALL_MAP_TYPES'][$arParams['INIT_MAP_TYPE']] ?>'
	    });

        <?
        foreach ($arResult['ALL_MAP_OPTIONS'] as $option => $method) {
	        if (in_array($option, $arParams['OPTIONS'])) {
                ?>
	            map.behaviors.enable('<?= $method ?>');
                <?
            } else {
                ?>
                if (map.behaviors.isEnabled('<?= $method ?>')) {
                    map.behaviors.disable('<?= $method ?>');
                }
                <?
            }
        }

        foreach ($arResult['ALL_MAP_CONTROLS'] as $control => $method) {
	        if (in_array($control, $arParams['CONTROLS'])) {
                ?>
                map.controls.add('<?= $method ?>');
                <?
            }
        }

        if ($arParams['DEV_MODE'] == 'Y') {
            ?>
            window.bYandexMapScriptsLoaded = true;
            <?
        }

        if ($arParams['ONMAPREADY']) {
            ?>
            if (window.<?= $arParams['ONMAPREADY'] ?>) {
                <?
                if ($arParams['ONMAPREADY_PROPERTY']) {
                    ?>
                    <?= $arParams['ONMAPREADY_PROPERTY'] ?> = map;
                    window.<?= $arParams['ONMAPREADY'] ?>();
                    <?
                } else {
                    ?>
                    window.<?= $arParams['ONMAPREADY'] ?>(map);
                    <?
                }
                ?>
            }
            <?
        }
        ?>
    }

    <?
    if ($arParams['DEV_MODE'] == 'Y') {
        ?>
        function BXMapLoader_<?= $arParams['MAP_ID'] ?>() {
            if (null == window.bYandexMapScriptsLoaded) {
                function _wait_for_map() {
                    if (window.ymaps && window.ymaps.Map) {
                        init_<?= $arParams['MAP_ID'] ?>();
                    } else {
                        setTimeout(_wait_for_map, 50);
                    }
                }

                BX.loadScript('<?= $arResult['MAPS_SCRIPT_URL'] ?>', _wait_for_map);
            } else {
                init_<?= $arParams['MAP_ID'] ?>();
            }
        }

        <?
        if ($arParams['WAIT_FOR_EVENT']) {
            ?>
            <?= \CUtil::JSEscape($arParams['WAIT_FOR_EVENT']) ?> = BXMapLoader_<?= $arParams['MAP_ID'] ?>;
            <?
        } elseif ($arParams['WAIT_FOR_CUSTOM_EVENT']) {
            ?>
            BX.addCustomEvent('<?= \CUtil::JSEscape($arParams['WAIT_FOR_EVENT']) ?>', BXMapLoader_<?= $arParams['MAP_ID'] ?>);
            <?
        } else {
            ?>
            BX.ready(BXMapLoader_<?= $arParams['MAP_ID'] ?>);
            <?
        }
    } else {
        ?>
        (function bx_ymaps_waiter() {
            if (typeof ymaps !== 'undefined') {
                ymaps.ready(init_<?= $arParams['MAP_ID'] ?>);
            } else {
                setTimeout(bx_ymaps_waiter, 100);
            }
        })();
        <?
    }
    ?>

    /* if map inits in hidden block (display:none)
    *  after the block showed
    *  for properly showing map this function must be called
    */
    function BXMapYandexAfterShow(mapId)
    {
        if (window.GLOBAL_arMapObjects[mapId] !== undefined) {
            window.GLOBAL_arMapObjects[mapId].container.fitToViewport();
        }
    }
</script>

<?
$height = !empty($arParams['MAP_NEW_HEIGHT']) ? $arParams['MAP_NEW_HEIGHT'] : $arParams['MAP_HEIGHT'];
?>
<div id="BX_YMAP_<?= $arParams['MAP_ID'] ?>" class="bx-yandex-map" style="height: <?= $height ?>; width: <?= $arParams['MAP_WIDTH'] ?>;">
    <?= GetMessage('MYS_LOADING'.($arParams['WAIT_FOR_EVENT'] ? '_WAIT' : '')) ?>
</div>