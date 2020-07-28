<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	'GROUPS' => array(
	),
    'PARAMETERS' => array(
        'RESULT' => array(
            'INIT_MAP_LON' => array(
                'NAME' => GetMessage('CFO_RESULT'),
                'TYPE' => 'STRING',
                'DEFAULT' => array(),
                'PARENT' => 'BASE',
            ),
        ),
        'PARAMS' => array(
            'INIT_MAP_LON' => array(
                'NAME' => GetMessage('CFO_PARAMS'),
                'TYPE' => 'STRING',
                'DEFAULT' => array(),
                'PARENT' => 'BASE',
            ),
        ),
    )
);
?>