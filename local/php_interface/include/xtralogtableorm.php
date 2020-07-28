<?

use Bitrix\Main;

class XtraLogTable extends Main\Entity\DataManager {

    public static function getTableName() {
        return 'xtra_log';
    }

    public static function getMap() {
        return array(
            'id' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => 'Log ID field',
            ),
            'entity_type' => array(
                'data_type' => 'string',
                'title' => 'Entity type field',
            ),
            'entity_id' => array(
                'data_type' => 'string',
                'title' => 'Entity id field',
            ),
            'exception_type' => array(
                'data_type' => 'string',
                'title' => 'Exception type field',
            ),
            'exception_entity' => array(
                'data_type' => 'string',
                'title' => 'Exception entity field',
            ),
            'extra_info' => array(
                'data_type' => 'string',
                'title' => 'Extra info field',
            ),
            'created_at' => array(
                'data_type' => 'datetime',
                'title' => 'Created at field',
            ),
            'count' => array(
                'data_type' => 'integer',
                'title' => 'Count field',
            ),
            'updated_at' => array(
                'data_type' => 'datetime',
                'title' => 'Updated at field',
            ),
        );
    }
}
?>
