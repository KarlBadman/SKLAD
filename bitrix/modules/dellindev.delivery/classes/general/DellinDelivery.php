<?php

// ���������� �������� �����.
IncludeModuleLangFile(__FILE__);

/**
 * Class DellinDelivery
 * ���������� ������ ������ ��������.
 * ����� ���������� �� ������������ 1�-�������.
 * https://dev.1c-bitrix.ru/api_help/sale/delivery.php
 */
Class DellinDelivery
{
    /**
     * �������� �����������.
     * @return array
     */
    function Init()
    {
        return array(
            // ���������� ��������� ������������� �����������.
            'SID' => 'dellindev.delivery',

            // �������� �����������.
            'NAME' => GetMessage('DELLIN_NAME'),

            // ��������� �������� �����������.
            'DESCRIPTION' => GetMessage('DELLIN_DESCRIPTION'),

            // ���������� �������� �����������, ������������ ��� ������������ ����������� � ������ ����������.
            'DESCRIPTION_INNER' => GetMessage('DELLIN_DESCRIPTION_INNER'),

            // ������������� ������� ������ �����������.
            'BASE_CURRENCY' => 'RUR',

            // ���� � ����� �����������. ����� ��� ����������� ��������������� ����������� ����������� (��� �� �����������).
            // � ����������� ����������� ������� ���������� �������� '__FILE__'.
            'HANDLER' => __FILE__,

            // �������� ������, ������������� ������ �������� ����������.
            // � ������ ���������� ����������� � ���� ������, �������� ������������ ����� ������ ('���_������', '���_������').
            'GETCONFIG' => array('DellinDelivery', 'GetConfig'),

            // �������� ������, ����������� �� �������� �������� ����������� � �������������� ������� �������� � ������ ��� ����������.
            // � ������ ���������� ����������� � ���� ������, �������� ������������ ����� ������ ('���_������', '���_������').
            // � ������ ���������� ����� ������ ������ �������� ����� �������� � ���� � ��������������� ����.
            'DBSETSETTINGS' => array('DellinDelivery', 'SetSettings'),

            // �������� ������, ����������� �� �������� �������������� ������ �������� ����������� � ������.
            // � ������ ���������� ����������� � ���� ������, �������� ������������ ����� ������ ('���_������', '���_������').
            'DBGETSETTINGS' => array('DellinDelivery', 'GetSettings'),

            // �������� ������, ����������� �� �������������� �������� ������������� �������� ��������� � ����������� ������.
            // ���� ����� �����������, �������������� �������� �� ����� �����������. � ������ ���������� ����������� � ���� ������,
            // �������� ������������ ����� ������ ('���_������', '���_������').
            'COMPABILITY' => array('DellinDelivery', 'Compability'),

            // �������� ������, ��������������� ������ ��������� ��������. � ������ ���������� ����������� � ���� ������,
            // �������� ������������ ����� ������ ('���_������', '���_������').
            'CALCULATOR' => array('DellinDelivery', 'Calculate'),

            // ������ �������� ���������.
            'PROFILES' => array(
                // ���������_������������_�������.
                'dellin_delivery_default' => array(
                    // �������� �������.
                    'TITLE' => GetMessage('DELLIN_TITLE'),

                    // �������� �������.
                    'DESCRIPTION' => GetMessage('DELLIN_TITLE_DESCRIPTION'),

                    // ���� ����������� � ������� (�����������_���, ������������_���).
                    'RESTRICTIONS_WEIGHT' => array(0),

                    // ����� ����������� � ������� ������ ����������� (�����������_�����_������, ������������_�����_������).
                    'RESTRICTIONS_SUM' => array(0)
                )
            )
        );
    }

    /**
     * ������ ������������ ������ ��������.
     * @return array
     */
    function GetConfig()
    {
        return array(
            'CONFIG_GROUPS' => array(
                'dellin_delivery_settings' => GetMessage('DELLIN_SETTINGS'),
            ),

            'CONFIG' => array(
                // ��������� API.
                'HEADER_API_SETTINGS' => array(
                    'TYPE' => 'SECTION',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_API_SECTION_HEADER'),
                    'GROUP' => 'dellin_delivery_settings'
                ),
                'API_KEY' => array(
                    'TYPE' => 'STRING',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_API_KEY'),
                    'DEFAULT' => '',
                    'GROUP' => 'dellin_delivery_settings',
                    'SIZE' => '50'
                ),

                // ����� ��������.
                'HEADER_DELIVERY_FROM' => array(
                    'TYPE' => 'SECTION',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_DELIVERY_FROM_SECTION_HEADER'),
                    'GROUP' => 'dellin_delivery_settings'
                ),
                'KLADR_CODE_DELIVERY_FROM' => array(
                    'TYPE' => 'STRING',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_KLADR_CODE_DELIVERY_FROM'),
                    'DEFAULT' => '7800000000000000000000000',
                    'GROUP' => 'dellin_delivery_settings',
                    'SIZE' => '30'
                ),

                // �������������� ����.
                'HEADER_SMALL_GOODS' => array(
                    'TYPE' => 'SECTION',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_SMALL_GOODS_SECTION_HEADER'),
                    'GROUP' => 'dellin_delivery_settings',
                ),
                'IS_SMALL_GOODS_PRICE' => array(
                    'TYPE' => 'CHECKBOX',
                    'DEFAULT' => 'N',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_IS_SMALL_GOODS_PRICE'),
                    'GROUP' => 'dellin_delivery_settings'
                ),
                'TRY_SMALL_GOODS_PRICE_CALCULATE' => array(
                    'TYPE' => 'CHECKBOX',
                    'DEFAULT' => 'N',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_TRY_SMALL_GOODS_PRICE_CALCULATE'),
                    'GROUP' => 'dellin_delivery_settings'
                ),

                // �����������.
                'HEADER_INSURANCE' => array(
                    'TYPE' => 'SECTION',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_INSURANCE_SECTION_HEADER'),
                    'GROUP' => 'dellin_delivery_settings'
                ),
                'IS_INSURANCE_GOODS_WITH_DECLARED_PRICE' => array(
                    'TYPE' => 'CHECKBOX',
                    'DEFAULT' => 'N',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_IS_INSURANCE_GOODS_WITH_DECLARED_PRICE'),
                    'GROUP' => 'dellin_delivery_settings'
                ),

                // ��������. �. �. ���� ����������� � �����������.
                'HEADER_GOODS_LOADING' => array(
                    'TYPE' => 'SECTION',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_GOODS_LOADING_SECTION_HEADER'),
                    'GROUP' => 'dellin_delivery_settings',
                ),
                'IS_GOODS_LOADING' => array(
                    'TYPE' => 'CHECKBOX',
                    'DEFAULT' => 'N',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_IS_GOODS_LOADING'),
                    'GROUP' => 'dellin_delivery_settings'
                ),
                'LOADING_TYPE' => array(
                    'TYPE' => 'DROPDOWN',
                    'DEFAULT' => 'NULL',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_LOADING_TYPE'),
                    'GROUP' => 'dellin_delivery_settings',
                    'VALUES' => array(
                        'NULL' => GetMessage('DELLIN_SETTINGS_VALUES_LOADING_TYPE_BACK'),
                        '0xb83b7589658a3851440a853325d1bf69' => GetMessage('DELLIN_SETTINGS_VALUES_LOADING_TYPE_SIDE'),
                        '0xabb9c63c596b08f94c3664c930e77778' => GetMessage('DELLIN_SETTINGS_VALUES_LOADING_TYPE_UP'),
                    ),
                ),
                'LOADING_TRANSPORT_REQUIREMENTS' => array(
                    'TYPE' => 'DROPDOWN',
                    'DEFAULT' => 'NULL',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_TRANSPORT_REQUIREMENTS'),
                    'GROUP' => 'dellin_delivery_settings',
                    'VALUES' => array(
                        'NULL' => GetMessage('DELLIN_SETTINGS_VALUES_TRANSPORT_REQUIREMENTS_NO'),
                        '0x9951e0ff97188f6b4b1b153dfde3cfec' => GetMessage('DELLIN_SETTINGS_VALUES_TRANSPORT_REQUIREMENTS_OPEN'),
                        '0x818e8ff1eda1abc349318a478659af08' => GetMessage('DELLIN_SETTINGS_VALUES_TRANSPORT_REQUIREMENTS_TENT')
                    ),
                ),
                'LOADING_ADDITIONAL_EQUIPMENT' => array(
                    'TYPE' => 'DROPDOWN',
                    'DEFAULT' => 'NULL',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_ADDITIONAL_EQUIPMENT'),
                    'GROUP' => 'dellin_delivery_settings',
                    'VALUES' => array(
                        'NULL' => GetMessage('DELLIN_SETTINGS_VALUES_ADDITIONAL_EQUIPMENT_NO'),
                        '0x92fce2284f000b0241dad7c2e88b1655' => GetMessage('DELLIN_SETTINGS_VALUES_ADDITIONAL_EQUIPMENT_TAIL_LIFT'),
                        '0x88f93a2c37f106d94ff9f7ada8efe886' => GetMessage('DELLIN_SETTINGS_VALUES_ADDITIONAL_EQUIPMENT_MANIPULATOR'),
                    ),
                ),
                'LOADING_GROUPING_OF_GOODS' => array(
                    'TYPE' => 'RADIO',
                    'DEFAULT' => 'ONE_CARGO_SPACE',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_GROUPING_OF_GOODS'),
                    'GROUP' => 'dellin_delivery_settings',
                    'VALUES' => array(
                        'ONE_CARGO_SPACE' => GetMessage('DELLIN_SETTINGS_VALUES_GROUPING_OF_GOODS_ONE_CARGO_SPACE'),
                        'SEPARATED_CARGO_SPACE' => GetMessage('DELLIN_SETTINGS_VALUES_GROUPING_OF_GOODS_SEPARATED_CARGO_SPACE'),
                        'SINGLE_ITEM_SINGLE_SPACE' => GetMessage('DELLIN_SETTINGS_VALUES_GROUPING_OF_GOODS_SINGLE_ITEM_SINGLE_SPACE'),
                    ),
                ),

                // ��������. �. �. ���� ����������� � ����������.
                'HEADER_GOODS_UNLOADING' => array(
                    'TYPE' => 'SECTION',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_GOODS_UNLOADING_SECTION_HEADER'),
                    'GROUP' => 'dellin_delivery_settings',
                ),
                'IS_GOODS_UNLOADING' => array(
                    'TYPE' => 'CHECKBOX',
                    'DEFAULT' => 'N',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_IS_GOODS_UNLOADING'),
                    'GROUP' => 'dellin_delivery_settings'
                ),
                'UNLOADING_TYPE' => array(
                    'TYPE' => 'DROPDOWN',
                    'DEFAULT' => 'NULL',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_UNLOADING_TYPE'),
                    'GROUP' => 'dellin_delivery_settings',
                    'VALUES' => array(
                        'NULL' => GetMessage('DELLIN_SETTINGS_VALUES_LOADING_TYPE_BACK'),
                        '0xb83b7589658a3851440a853325d1bf69' => GetMessage('DELLIN_SETTINGS_VALUES_LOADING_TYPE_SIDE'),
                        '0xabb9c63c596b08f94c3664c930e77778' => GetMessage('DELLIN_SETTINGS_VALUES_LOADING_TYPE_UP'),
                    ),
                ),
                'UNLOADING_TRANSPORT_REQUIREMENTS' => array(
                    'TYPE' => 'DROPDOWN',
                    'DEFAULT' => 'NULL',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_TRANSPORT_REQUIREMENTS'),
                    'GROUP' => 'dellin_delivery_settings',
                    'VALUES' => array(
                        'NULL' => GetMessage('DELLIN_SETTINGS_VALUES_TRANSPORT_REQUIREMENTS_NO'),
                        '0x9951e0ff97188f6b4b1b153dfde3cfec' => GetMessage('DELLIN_SETTINGS_VALUES_TRANSPORT_REQUIREMENTS_OPEN'),
                        '0x818e8ff1eda1abc349318a478659af08' => GetMessage('DELLIN_SETTINGS_VALUES_TRANSPORT_REQUIREMENTS_TENT')
                    ),
                ),
                'UNLOADING_ADDITIONAL_EQUIPMENT' => array(
                    'TYPE' => 'DROPDOWN',
                    'DEFAULT' => 'NULL',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_ADDITIONAL_EQUIPMENT'),
                    'GROUP' => 'dellin_delivery_settings',
                    'VALUES' => array(
                        'NULL' => GetMessage('DELLIN_SETTINGS_VALUES_ADDITIONAL_EQUIPMENT_NO'),
                        '0x92fce2284f000b0241dad7c2e88b1655' => GetMessage('DELLIN_SETTINGS_VALUES_ADDITIONAL_EQUIPMENT_TAIL_LIFT'),
                        '0x88f93a2c37f106d94ff9f7ada8efe886' => GetMessage('DELLIN_SETTINGS_VALUES_ADDITIONAL_EQUIPMENT_MANIPULATOR'),
                    ),
                ),

                // ��������
                'HEADER_PACKING_FOR_GOODS' => array(
                    'TYPE' => 'SECTION',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_PACKING_FOR_GOODS_SECTION_HEADER'),
                    'GROUP' => 'dellin_delivery_settings'
                ),
                'PACKING_FOR_GOODS_HARD' => array(
                    'TYPE' => 'CHECKBOX',
                    'DEFAULT' => 'N',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_PACKING_FOR_GOODS_HARD'),
                    'GROUP' => 'dellin_delivery_settings',
                ),
                'PACKING_FOR_GOODS_ADDITIONAL' => array(
                    'TYPE' => 'CHECKBOX',
                    'DEFAULT' => 'N',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_PACKING_FOR_GOODS_ADDITIONAL'),
                    'GROUP' => 'dellin_delivery_settings',
                ),
                'PACKING_FOR_GOODS_BUBBLE' => array(
                    'TYPE' => 'CHECKBOX',
                    'DEFAULT' => 'N',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_PACKING_FOR_GOODS_BUBBLE'),
                    'GROUP' => 'dellin_delivery_settings',
                ),
                'PACKING_FOR_GOODS_BAG' => array(
                    'TYPE' => 'CHECKBOX',
                    'DEFAULT' => 'N',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_PACKING_FOR_GOODS_BAG'),
                    'GROUP' => 'dellin_delivery_settings',
                ),
                'PACKING_FOR_GOODS_PALLET' => array(
                    'TYPE' => 'CHECKBOX',
                    'DEFAULT' => 'N',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_PACKING_FOR_GOODS_PALLET'),
                    'GROUP' => 'dellin_delivery_settings',
                ),

                // ��������������� ��������
                'HEADER_INTERCITY' => array(
                    'TYPE' => 'SECTION',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_INTERCITY_SECTION_HEADER'),
                    'GROUP' => 'dellin_delivery_settings',
                ),
                'INTERCITY_HIDE' => array(
                    'TYPE' => 'CHECKBOX',
                    'DEFAULT' => 'N',
                    'TITLE' => GetMessage('DELLIN_SETTINGS_TITLE_INTERCITY_HIDE'),
                    'GROUP' => 'dellin_delivery_settings'
                ),
            ),
        );
    }

    /**
     * ��������� ���������� �����������.
     * @param array $arSettings
     * @return string
     */
    function SetSettings($arSettings)
    {
        $arSettings['MARKUP_ABSOLUTE'] = (float)str_replace(',', '.', $arSettings['MARKUP_ABSOLUTE']);
        $arSettings['KLADR_CODE_DELIVERY_FROM'] = preg_replace('/[^\'0-9\s]/', '', $arSettings['KLADR_CODE_DELIVERY_FROM']);
        $arSettings['API_KEY'] = trim($arSettings['API_KEY']);

        return serialize($arSettings);
    }

    /**
     * ������ ���������� �����������.
     * @param string $strSettings
     * @return mixed
     */
    function GetSettings($strSettings)
    {
        return unserialize($strSettings);
    }

    /**
     * �������� ������������ ������� �������� ������.
     * @param array $arOrder
     * @param array $arConfig
     * @return array
     */
    function Compability($arOrder, $arConfig)
    {
        $profile_list = array();
        $response = DellinAPI::Calculate($arOrder, $arConfig);

        if ($response['STATUS'] == 'OK') {
            $profile_list[] = 'dellin_delivery_default';
        }

        return $profile_list;
    }

    /**
     * ������������ �������� ��������� � ����� ��������
     * @param $profile
     * @param array $arConfig
     * @param array $arOrder
     * @param $STEP
     * @param bool $TEMP
     * @return array
     */
    function Calculate($profile, $arConfig, $arOrder, $STEP, $TEMP = false)
    {
        $response = DellinAPI::Calculate($arOrder, $arConfig);

        if ($response['STATUS'] == 'OK') {
            return array(
                'RESULT' => 'OK',
                'VALUE' => $response['BODY'][0],
                'TRANSIT' => $response['BODY'][1]
            );
        }

        return array(
            'RESULT' => 'ERROR',
            'TEXT' => $response['BODY']
        );
    }
}
