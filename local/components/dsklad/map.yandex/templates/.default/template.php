<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$this->addExternalJS($arResult['MAPS_SCRIPT_URL']);?>
<div id="<?echo $arResult['MAP_ID']?>" class="bx-yandex-map" style="height: <?echo $arResult['MAP_HEIGHT'];?>; width: <?echo $arResult['MAP_WIDTH']?>;"></div>
<script>
    (function () {

        function SetMapDefaults () {
            dskladMapYndex.settings.handleRun = <?=CUtil::PhpToJSObject($arParams['HANDLE_RUN']);?>;
            dskladMapYndex.settings.map = <?=CUtil::PhpToJSObject($arResult['OPTION_MAPS'])?>;
            dskladMapYndex.settings.placemarks = <?=CUtil::PhpToJSObject($arResult['OPTION_PLACEMARKS'])?>;
            dskladMapYndex.plas = <?=\Bitrix\Main\Web\Json::encode($arResult['PLACEMARKS'])?>;
        }

        // TODO
        if (typeof $ != "undefined") {
            $(document).ready(function () {
                SetMapDefaults();
                if (dskladMapYndex.settings.handleRun == 'N')
                 dskladMapYndex.init();
            });
        }
    })();
</script>