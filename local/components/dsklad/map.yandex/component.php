<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!isset($arParams['YANDEX_VERSION']))
	$arParams['YANDEX_VERSION'] = '2.1.71';

if(empty($arParams['API_KEY'])){
    $arParams['API_KEY'] = \Dsklad\Config::getParam('api_key/yandex_map');
}

if (empty($arParams['HANDLE_RUN'])) {
    $arParams['HANDLE_RUN'] = 'N';
}

$scheme = (CMain::IsHTTPS() ? "https" : "http");
$arResult['MAPS_SCRIPT_URL'] = $scheme.'://api-maps.yandex.ru/'.$arParams['YANDEX_VERSION'].'/?apikey='.$arParams['API_KEY'].'&load=package.standard&mode=release&lang=ru&wizard=bitrix';
$arResult['MAPS_SCRIPT'] = '<script ' . ($arParams['HANDLE_RUN'] == 'Y'  ? 'async' : '') . ' type="text/javaScript" src="'.$arResult['MAPS_SCRIPT_URL'].'"></script>';

$arResult['MAP_WIDTH'] =!empty($arParams["MAP_WIDTH"])?trim($arParams['MAP_WIDTH']):'100%';
if (ToUpper($arResult['MAP_WIDTH']) != 'AUTO' && substr($arResult['MAP_WIDTH'], -1, 1) != '%')
{
    $arResult['MAP_WIDTH'] = intval($arResult['MAP_WIDTH']);
	if ($arResult['MAP_WIDTH'] <= 0) $arResult['MAP_WIDTH'] = 600;
    $arResult['MAP_WIDTH'] .= 'px';
}
$arResult['MAP_HEIGHT'] =!empty($arParams["MAP_HEIGHT"])?trim($arParams['MAP_HEIGHT']):600;

if (substr($arResult['MAP_HEIGHT'], -1, 1) != '%')
{
    $arResult['MAP_HEIGHT'] = intval($arResult['MAP_HEIGHT']);
	if ($arResult['MAP_HEIGHT'] <= 0) $arResult['MAP_HEIGHT'] = 600;
    $arResult['MAP_HEIGHT'] .= 'px';
}


$arResult['MAP_ID'] =
    (strlen($arParams["MAP_ID"])<=0 && !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["MAP_ID"])) ?
        'MAP_'.$this->randString() : $arParams['MAP_ID'];

if(is_array($arParams["PLACEMARKS"]) && !empty($arParams["PLACEMARKS"])) {

    $arPlas["type"] = "FeatureCollection";
    foreach ($arParams["PLACEMARKS"] as $plas) {

        if ($plas['IS_TERMINAL'] == 'Y') {
            $color = !empty($arParams['COLOR_PLAS_TERMINAL']) ? $arParams['COLOR_PLAS_TERMINAL'] : '#1ab500';
            $preset = !empty($arParams['COLOR_PLAS_TERMINAL']) ? $arParams['COLOR_PLAS_TERMINAL'] : 'islands#greenDotIcon';
        } else {
            $color = !empty($arParams['COLOR_PLAS']) ? $arParams['COLOR_PLAS'] : '#1d97ff';
            $preset = !empty($arParams['TYPE_PLAS_TERMINAL']) ? $arParams['TYPE_PLAS_TERMINAL'] : 'islands#blueDotIcon';
        }

        $plasMas = array(
            "type" => "Feature",
            "id" => $plas['TERMINAL'],
            "geometry" => array(
                "type" => "Point",
                "coordinates" => array($plas["LAT"], $plas["LON"])
            ),
            "properties" => array(
                "balloonContent" => $plas["TEXT"],
                "hintContent" => strtok(substr($plas["TEXT"], strpos($plas["TEXT"], '>') + 1), '<'),
                "clusterCaption" => strtok(substr($plas["TEXT"], strpos($plas["TEXT"], '>') + 1), '<')
            ),
            "options" => array(
                "iconColor" => $color,
                "preset" => $preset,

            )
        );

        if(!empty($plas['SEARCH_TITLE']) && !empty($plas['SEARCH_DESCRIPTION'])){
            $plasMas['properties'] = array(
                "balloonContent" => $plas["TEXT"],
                "hintContent" => $plas['TITLE'],
                "clusterCaption" => $plas['TITLE'],
                "search_title"=> $plas['SEARCH_TITLE'],
                "search_description"=> $plas['SEARCH_DESCRIPTION'],
            );
        }else{
            $plasMas['properties'] = array(
                "balloonContent" => $plas["TEXT"],
                "hintContent" => strtok(substr($plas["TEXT"], strpos($plas["TEXT"], '>') + 1), '<'),
                "clusterCaption" => strtok(substr($plas["TEXT"], strpos($plas["TEXT"], '>') + 1), '<')
            );
        }

        $arPlas["features"][] = $plasMas;
    }

    $arResult['PLACEMARKS'] = $arPlas;

}else{
    $arResult['PLACEMARKS'] = false;
}

$arResult['OPTION_MAPS'] = array(
    'id'=>$arResult['MAP_ID'],
    'options'=> array(
        'center'=>array(!empty($arParams["INIT_MAP_LAT"]) &&  !is_nan($arParams["INIT_MAP_LAT"])? floatval($arParams["INIT_MAP_LAT"]):59.9, !empty($arParams["INIT_MAP_LON"]) &&  !is_nan($arParams["INIT_MAP_LON"]) ? floatval($arParams["INIT_MAP_LON"]):30.33),
        'zoom'=>!empty($arParams["INIT_MAP_SCALE"]) && !is_nan($arParams["INIT_MAP_SCALE"]) ?floatval($arParams["INIT_MAP_SCALE"]):9,
        'controls'=>!empty($arParams['CONTROLS'])?$arParams['CONTROLS']:array('fullscreenControl','geolocationControl'),
        'type'=>!empty($arParams['INIT_MAP_TYPE'])?$arParams['INIT_MAP_TYPE']:'yandex#map',
        'yandexMapDisablePoiInteractivity'=>($arParams['DISABLE_POINT'] != 'N') ? true : false,
    ),
    'search_plas'=>(empty($arParams['SEARCH_PLASMARK'])) ? 'N' : $arParams['SEARCH_PLASMARK'],

);

$arResult['OPTION_PLACEMARKS'] = array(
    'clusterize'=> ($arParams['CLASTER'] == 'Y') ? true : false,
    'gridSize'=> $arParams['CLASTER_SIZE'],
    'clusterDisableClickZoom'=> ($arParams['CLASTER_ZOOM'] == 'Y') ? true : false,
    'clusterOpenBalloonOnClick'=>($arParams['OPEN_BALLOON_CLASTER'] == 'Y') ? true : false,
    'geoObjectOpenBalloonOnClick'=> ($arParams['OPEN_BALLOON_OBJECT'] == 'Y') ? true : false,
);

$this->IncludeComponentTemplate();