var globalMap;
var globalMaps = [];
var globalPlacemark = [];

function cleanMap() {
	for(var index in globalPlacemark)
	{
		globalMap.geoObjects.remove(globalPlacemark[index]);
	}
	globalPlacemark = [];
}

if (!window.BX_YMapAddPlacemark)
{
	window.BX_YMapAddPlacemark = function(map, arPlacemark)
	{
		
		globalMap = map;
        globalMaps.push(globalMap);


		if (null == map)
			return false;

		if(!arPlacemark.LAT || !arPlacemark.LON)
			return false;

		var props = {};
		if (null != arPlacemark.TEXT && arPlacemark.TEXT.length > 0)
		{
			var value_view = '';

			if (arPlacemark.TEXT.length > 0)
			{
				var rnpos = arPlacemark.TEXT.indexOf("\n");
				value_view = rnpos <= 0 ? arPlacemark.TEXT : arPlacemark.TEXT.substring(0, rnpos);
			}

			props.balloonContent = arPlacemark.TEXT.replace(/\n/g, '<br />');
			props.hintContent = value_view;
		}
	
		var obPlacemark = new ymaps.Placemark(
			[arPlacemark.LAT, arPlacemark.LON],
			props, {
				preset : arPlacemark.IS_TERMINAL == 'Y' ? "twirl#greenStretchyIcon" : "twirl#blueStretchyIcon",
				balloonCloseButton: true,
			}
		);
	
		obPlacemark.events.add('click', function(){
			$("#input-delivery-point").val(arPlacemark.TERMINAL);
			window.deliveryFix();
			//$("#input-delivery-point").trigger("chosen:updated");
			$(".terminal_address").hide();
			$("#"+arPlacemark.TERMINAL+".terminal_address").show();
		});
		
		map.geoObjects.add(obPlacemark);

		globalPlacemark.push(obPlacemark);
        globalPlacemarkTerminal = arPlacemark.TERMINAL;

		return obPlacemark;
	}
}

if (!window.BX_YMapAddPolyline)
{
	window.BX_YMapAddPolyline = function(map, arPolyline)
	{
		if (null == map)
			return false;

		if (null != arPolyline.POINTS && arPolyline.POINTS.length > 1)
		{
			var arPoints = [];
			for (var i = 0, len = arPolyline.POINTS.length; i < len; i++)
			{
				arPoints.push([arPolyline.POINTS[i].LAT, arPolyline.POINTS[i].LON]);
			}
		}
		else
		{
			return false;
		}

		var obParams = {clickable: true};
		if (null != arPolyline.STYLE)
		{
			obParams.strokeColor = arPolyline.STYLE.strokeColor;
			obParams.strokeWidth = arPolyline.STYLE.strokeWidth;
		}
		var obPolyline = new ymaps.Polyline(
			arPoints, {balloonContent: arPolyline.TITLE}, obParams
		);

		map.geoObjects.add(obPolyline);

		return obPolyline;
	}
}
