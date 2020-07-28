<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if ($arParams['BX_EDITOR_RENDER_MODE'] == 'Y'):
?>
<img src="/bitrix/components/bitrix/map.yandex.view/templates/.default/images/screenshot.png" border="0" />
<?
else:

	$arTransParams = array(
		'KEY' => $arParams['KEY'],
		'INIT_MAP_TYPE' => $arParams['INIT_MAP_TYPE'],
		'INIT_MAP_LON' => $arResult['POSITION']['yandex_lon'],
		'INIT_MAP_LAT' => $arResult['POSITION']['yandex_lat'],
		'INIT_MAP_SCALE' => $arResult['POSITION']['yandex_scale'],
		'MAP_WIDTH' => $arParams['MAP_WIDTH'],
		'MAP_HEIGHT' => $arParams['MAP_HEIGHT'],
		'CONTROLS' => $arParams['CONTROLS'],
		'OPTIONS' => $arParams['OPTIONS'],
		'MAP_ID' => $arParams['MAP_ID'],
		'LOCALE' => $arParams['LOCALE'],
		'ONMAPREADY' => 'BX_SetPlacemarks_'.$arParams['MAP_ID'],
	);

	if ($arParams['DEV_MODE'] == 'Y')
	{
		$arTransParams['DEV_MODE'] = 'Y';
		if ($arParams['WAIT_FOR_EVENT'])
			$arTransParams['WAIT_FOR_EVENT'] = $arParams['WAIT_FOR_EVENT'];
	}

    $i = 0;

	$arPlas["type"] = "FeatureCollection";
	foreach ($arResult["POSITION"]["PLACEMARKS"] as $plas){

        if($plas['IS_TERMINAL'] == 'Y'){
            $color = '#1ab500';
            $preset = 'islands#greenDotIcon';
        }else{
            $color = '#1d97ff';
            $preset = 'islands#blueDotIcon';
        }

        $arPlas["features"][] = array(
            "type"=> "Feature",
            "id"=> $i,
            "geometry"=>array(
                "type"=>"Point",
                "coordinates"=>array($plas["LAT"],$plas["LON"])
            ),
            "properties"=>array(
                    "balloonContent"=>$plas["TEXT"],
                    "hintContent"=>$plas["TEXT"],
                    "clusterCaption"=>strtok(substr($plas["TEXT"],strpos($plas["TEXT"],'>')+1),'<')
            ),
            "options"=>array(
                    "iconColor"=> $color,
                    "preset"=>$preset,
            )
        );
        $i++;
    }
    $arTransParams['PLAS'] = $arPlas;
//    {
//      "type": "Feature",
//      "id": 0,
//      "geometry": {
//                  "type": "Point",
//                  "coordinates": [55.831903, 37.411961]
//       },
//      "properties": {
//                  "balloonContent": "Содержимое балуна",
//                  "clusterCaption": "Метка с iconContent",
//                  "hintContent": "Текст подсказки",
//                  "iconContent": "1"
//       }, "options": {
//                  "iconColor": "#ff0000",
//                  "preset": "islands#blueCircleIcon
//       }
//},




//    {
//        "type": "FeatureCollection",
//    "features": [
//        {
//          "type": "Feature",
//          "id": 11,
//          "geometry": {
//                        "type": "Point",
//                        "coordinates": [55.71677, 37.482338]
//          },
//          "properties": {
//                          "balloonContent": "Я не пропадаю, когда балун открыт",
//                          "clusterCaption": "Я не пропадаю, когда балун открыт",
//                          "hintContent": "Я не пропадаю, когда балун открыт"},
//                          "options": {
//                                      "preset": "islands#redGovernmentCircleIcon",
//                                      "hideIconOnBalloonOpen": false
//                                      }
//      },
//    ]
//}
?>
<script type="text/javascript">



    //window.yandex_maps.ready(init);
    //
    //function init () {
    //    var myMap = new ymaps.Map('test_map', {
    //            center: [55.76, 37.64],
    //            zoom: 10
    //        }, {
    //            searchControlProvider: 'yandex#search'
    //        }),
    //        objectManager = new ymaps.ObjectManager({
    //            // Чтобы метки начали кластеризоваться, выставляем опцию.
    //            clusterize: true,
    //            // ObjectManager принимает те же опции, что и кластеризатор.
    //            gridSize: 32,
    //            clusterDisableClickZoom: true
    //        });
    //
    //    // Чтобы задать опции одиночным объектам и кластерам,
    //    // обратимся к дочерним коллекциям ObjectManager.
    //    objectManager.objects.options.set('preset', 'islands#greenDotIcon');
    //    objectManager.clusters.options.set('preset', 'islands#greenClusterIcons');
    //    myMap.geoObjects.add(objectManager);
    //    objectManager.add(<?//=json_encode($arPlas)?>//);
    //}

//    dataMapInfo = '<?//=$arParams['MAP_ID']?>//';
//function BX_SetPlacemarks_<?//echo $arParams['MAP_ID']?>//(map)
//{
//	if(typeof window["BX_YMapAddPlacemark"] != 'function')
//	{
//		/* If component's result was cached as html,
//		 * script.js will not been loaded next time.
//		 * let's do it manualy.
//		*/
//
//		(function(d, s, id)
//		{
//			var js, bx_ym = d.getElementsByTagName(s)[0];
//			if (d.getElementById(id)) return;
//			js = d.createElement(s); js.id = id;
//			js.src = "<?//=$templateFolder.'/script.js'?>//";
//			bx_ym.parentNode.insertBefore(js, bx_ym);
//		}(document, 'script', 'bx-ya-map-js'));
//
//		var ymWaitIntervalId = setInterval( function(){
//				if(typeof window["BX_YMapAddPlacemark"] == 'function')
//				{
//					BX_SetPlacemarks_<?//echo $arParams['MAP_ID']?>//(map);
//					clearInterval(ymWaitIntervalId);
//				}
//			}, 300
//		);
//
//		return;
//	}
//
//	var arObjects = {PLACEMARKS:[],POLYLINES:[]};
<?//
//	if (is_array($arResult['POSITION']['PLACEMARKS']) && ($cnt = count($arResult['POSITION']['PLACEMARKS']))):
//    ?>
//    if (!window.globalPlacemarkIndices)
//        window.globalPlacemarkIndices = {};
//
//    if (!window.globalPlacemarkIndices["<?//echo $arParams['MAP_ID']?>//"])
//        window.globalPlacemarkIndices["<?//echo $arParams['MAP_ID']?>//"] = {};
//    <?//
//		for($i = 0; $i < $cnt; $i++):
//?>
//	arObjects.PLACEMARKS[arObjects.PLACEMARKS.length] = BX_YMapAddPlacemark(map, <?//echo CUtil::PhpToJsObject($arResult['POSITION']['PLACEMARKS'][$i])?>//);
//
//    window.globalPlacemarkIndices["<?//echo $arParams['MAP_ID']?>//"][globalPlacemarkTerminal] = globalPlacemark.length - 1;
<?//
//		endfor;
//        ?>
//            // скрываем пвз не подходящие под условие
//            //var arPlacemarksToHide = <?////echo CUtil::PhpToJsObject($arParams['PLACEMARKS_TO_HIDE'])?>////;
//            //for (var i in arPlacemarksToHide) {
//            //    var terminal = arPlacemarksToHide[i];
//            //    if (globalPlacemarkIndices["<?////echo $arParams['MAP_ID']?>////"]) {
//            //        var placemarkIndex = globalPlacemarkIndices["<?////echo $arParams['MAP_ID']?>////"][terminal];
//            //        if (globalPlacemark[placemarkIndex])
//            //            globalPlacemark[placemarkIndex].options.set('visible', false);
//            //    }
//            //}
//
//        <?//
//
//	endif;
//	if (is_array($arResult['POSITION']['POLYLINES']) && ($cnt = count($arResult['POSITION']['POLYLINES']))):
//		for($i = 0; $i < $cnt; $i++):
//?>
//	arObjects.POLYLINES[arObjects.POLYLINES.length] = BX_YMapAddPolyline(map, <?//echo CUtil::PhpToJsObject($arResult['POSITION']['POLYLINES'][$i])?>//);
<?//
//		endfor;
//	endif;
//
//	if ($arParams['ONMAPREADY']):
//?>
//	if (window.<?//echo $arParams['ONMAPREADY']?>//)
//	{
//		window.<?//echo $arParams['ONMAPREADY']?>//(map, arObjects);
//	}
<?//
//	endif;
//?>
//
//    if (!window.yandex_maps)
//        window.yandex_maps = {};
//    window.yandex_maps["<?//echo $arParams['MAP_ID']?>//"] = arObjects;
// }
</script>
<div class="bx-yandex-view-layout">
	<div class="bx-yandex-view-map" id="test_map">
<?
	$APPLICATION->IncludeComponent('prmedia:map.yandex', 'order', $arTransParams, false, array('HIDE_ICONS' => 'Y'));
?>
	</div>
</div>
<?
endif;
?>