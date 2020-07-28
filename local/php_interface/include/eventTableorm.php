<?php

use Bitrix\Main,
    Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class EventTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> EVENT_NAME string(255) mandatory
 * <li> MESSAGE_ID int optional
 * <li> LID string(255) mandatory
 * <li> C_FIELDS string optional
 * <li> DATE_INSERT datetime optional
 * <li> DATE_EXEC datetime optional
 * <li> SUCCESS_EXEC bool optional default 'N'
 * <li> DUPLICATE bool optional default 'Y'
 * <li> LANGUAGE_ID string(2) optional
 * </ul>
 *
 * @package Bitrix\Event
 **/

class EventTable extends Main\Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_event';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('EVENT_ENTITY_ID_FIELD'),
            ),
            'EVENT_NAME' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateEventName'),
                'title' => Loc::getMessage('EVENT_ENTITY_EVENT_NAME_FIELD'),
            ),
            'MESSAGE_ID' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('EVENT_ENTITY_MESSAGE_ID_FIELD'),
            ),
            'LID' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateLid'),
                'title' => Loc::getMessage('EVENT_ENTITY_LID_FIELD'),
            ),
            'C_FIELDS' => array(
                'data_type' => 'text',
                'title' => Loc::getMessage('EVENT_ENTITY_C_FIELDS_FIELD'),
            ),
            'DATE_INSERT' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('EVENT_ENTITY_DATE_INSERT_FIELD'),
            ),
            'DATE_EXEC' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('EVENT_ENTITY_DATE_EXEC_FIELD'),
            ),
            'SUCCESS_EXEC' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
                'title' => Loc::getMessage('EVENT_ENTITY_SUCCESS_EXEC_FIELD'),
            ),
            'DUPLICATE' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
                'title' => Loc::getMessage('EVENT_ENTITY_DUPLICATE_FIELD'),
            ),
            'LANGUAGE_ID' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateLanguageId'),
                'title' => Loc::getMessage('EVENT_ENTITY_LANGUAGE_ID_FIELD'),
            ),
        );
    }
    /**
     * Returns validators for EVENT_NAME field.
     *
     * @return array
     */
    public static function validateEventName()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }
    /**
     * Returns validators for LID field.
     *
     * @return array
     */
    public static function validateLid()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }
    /**
     * Returns validators for LANGUAGE_ID field.
     *
     * @return array
     */
    public static function validateLanguageId()
    {
        return array(
            new Main\Entity\Validator\Length(null, 2),
        );
    }
}
