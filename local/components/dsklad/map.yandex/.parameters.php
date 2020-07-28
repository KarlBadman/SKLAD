<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	'GROUPS' => array(
	),
	'PARAMETERS' => array(
        'INIT_MAP_LON' => array(
            'NAME' => GetMessage('DMAP_INIT_MAP_LON'),
            'TYPE' => 'STRING',
            'DEFAULT' => 30.335881444444,
            'PARENT' => 'BASE',
        ),
        'INIT_MAP_LAT' => array(
            'NAME' => GetMessage('DMAP_INIT_MAP_LAT'),
            'TYPE' => 'STRING',
            'DEFAULT' => 59.933179055556,
            'PARENT' => 'BASE',
        ),
        'INIT_MAP_SCALE' => array(
            'NAME' => GetMessage('DMAP_INIT_MAP_SCALE'),
            'TYPE' => 'STRING',
            'DEFAULT' => 9,
            'PARENT' => 'BASE',
        ),
	    'MAP_WIDTH' => array(
            'NAME' => GetMessage('DMAP_PARAM_MAP_WIDTH'),
            'TYPE' => 'STRING',
            'DEFAULT' => '600',
            'PARENT' => 'BASE',
        ),
		'INIT_MAP_TYPE' => array(
			'NAME' => GetMessage('DMAP_PARAM_INIT_MAP_TYPE'),
			'TYPE' => 'LIST',
			'VALUES' => array(
				'yandex#map' => GetMessage('DMAP_PARAM_INIT_MAP_TYPE_MAP'),
                'yandex#satellite' => GetMessage('DMAP_PARAM_INIT_MAP_TYPE_SATELLITE'),
                'yandex#hybrid' => GetMessage('DMAP_PARAM_INIT_MAP_TYPE_HYBRID'),
                'yandex#publicMap' => GetMessage('DMAP_PARAM_INIT_MAP_TYPE_PUBLIC'),
                'yandex#publicMapHybrid' => GetMessage('DMAP_PARAM_INIT_MAP_TYPE_PUBLIC_HYBRID'),
			),
			'DEFAULT' => 'yandex#map',
			'ADDITIONAL_VALUES' => 'N',
			'PARENT' => 'BASE',
		),

		'MAP_WIDTH' => array(
			'NAME' => GetMessage('DMAP_PARAM_MAP_WIDTH'),
			'TYPE' => 'STRING',
			'DEFAULT' => '100%',
			'PARENT' => 'BASE',
		),

		'MAP_HEIGHT' => array(
			'NAME' => GetMessage('DMAP_PARAM_MAP_HEIGHT'),
			'TYPE' => 'STRING',
			'DEFAULT' => '500',
			'PARENT' => 'BASE',
		),

		'CONTROLS' => array(
			'NAME' => GetMessage('DMAP_PARAM_CONTROLS'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'VALUES' => array(
				'fullscreenControl'=>GetMessage('DMAP_FULL_SCREEN_CONTROL'),
                'geolocationControl'=>GetMessage('DMAP_GEOLOCATION_CONTROL'),
                'routeEditor'=>GetMessage('DMAP_ROUTE_EDITOR'),
                'rulerControl'=>GetMessage('DMAP_RULER_CONTROL'),
                'searchControl'=>GetMessage('DMAP_SEARCH_CONTROL'),
                'trafficControl'=>GetMessage('DMAP_TRAFFIC_CONTROL'),
                'typeSelector'=>GetMessage('DMAP_TYPE_SELECTOR'),
                'zoomControl'=>GetMessage('DMAP_ZOOM_CONTROL')
			),
			'DEFAULT' => array('fullscreenControl','geolocationControl'),
			'PARENT' => 'ADDITIONAL_SETTINGS',
		),

		'MAP_ID' => array(
            "PARENT" => "BASE",
			'NAME' => GetMessage('DMAP_PARAM_MAP_ID'),
			'TYPE' => 'STRING',
			'DEFAULT' => '',
		),

        'PLAS' => array(
            "PARENT" => "BASE",
            'NAME' => GetMessage('DMAP_PLAS'),
            'TYPE' => 'STRING',
            'DEFAULT' => '',
        ),

        'CLASTER' => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("DMP_CLASTER"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),

        'CLASTER_SIZE'=>array(
            "PARENT" => "BASE",
            'NAME' => GetMessage('DMP_CLASTER_SIZE'),
            'TYPE' => 'STRING',
            'DEFAULT' => 32,
        ),
        'CLASTER_ZOOM' => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("DMP_CLASTER_ZOOM"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ),
        'COLOR_PLAS' => array(
            'NAME' => GetMessage('DMAP_COLOR_PLAS'),
            'TYPE' => 'STRING',
            'DEFAULT' => '#1d97ff',
            'PARENT' => 'BASE',
        ),
        'OPEN_BALLOON_CLASTER' => array(
            'NAME' => GetMessage('DMP_OPEN_BALLOON_CLASTER'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'PARENT' => 'BASE',
        ),
        'OPEN_BALLOON_OBJECT' => array(
            'NAME' => GetMessage('DMP_OPEN_BALLOON_OBJECT'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
            'PARENT' => 'BASE',
        ),
        'COLOR_PLAS_TERMINAL' => array(
            'NAME' => GetMessage('DMAP_COLOR_PLAS_TERMINAL'),
            'TYPE' => 'STRING',
            'DEFAULT' => '#1ab500',
            'PARENT' => 'BASE',
        ),
        'TYPE_PLAS' => array(
            'NAME' => GetMessage('DMAP_TYPE_PLAS'),
            'TYPE' => 'STRING',
            'DEFAULT' => 'islands#blueDotIcon',
            'PARENT' => 'BASE',
        ),
        'TYPE_PLAS_TERMINAL' => array(
            'NAME' => GetMessage('DMAP_TYPE_PLAS_TERMINAL'),
            'TYPE' => 'STRING',
            'DEFAULT' => 'islands#greenDotIcon',
            'PARENT' => 'BASE',
        ),
        'API_KEY' => array(
            'NAME' => GetMessage('API_KEY'),
            'TYPE' => 'STRING',
            'PARENT' => 'BASE',
        ),
        'DISABLE_POINT' => array(
            'NAME' => GetMessage('DMAP_DISABLE_POINT'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
            'PARENT' => 'BASE',
        ),
        'HANDLE_RUN' => array(
            'NAME' => GetMessage('DMAP_HANDLE_RUN'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'PARENT' => 'BASE',
        ),
        'SEARCH_PLASMARK' => array(
            'NAME' => GetMessage('SEARCH_PLASMARK'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'PARENT' => 'BASE',
        ),

	),
);
?>