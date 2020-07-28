<div id="point-delivery" class="hide">
    <div class="ds-modal__header header-map">
        <h5>Пункты самовывоза</h5>
        <span class="icon-svg ic-close ds-modal-close js-ds-modal-close" onclick="purepopup.closePopup();"></span>
    </div>
    <div class="ds-modal__body">
        <div class="delivery-modal-map">
            <?$APPLICATION->IncludeComponent(
                "dsklad:map.yandex",
                "",
                Array(
                    "COMPOSITE_FRAME_MODE" => "A",
                    "COMPOSITE_FRAME_TYPE" => "AUTO",
                    "CONTROLS" => array("geolocationControl", "zoomControl"),
                    "INIT_MAP_TYPE" => "yandex#map",
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
                    'SEARCH_PLASMARK'=>'Y'
                )
            );?>
        </div>
    </div>
</div>
