<?
use Bitrix\Main;
class XtraSpeedTable extends Main\Entity\DataManager {

    public static function getTableName() {
        return 'xtra_speed';
    }

    public static function getMap() {
        return array(
            'id' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => 'Speed ID field',
            ),
            'speed_time' => array(
                'data_type' => 'float',
                'title' => 'Speed in time',
            ),
            'speed_percents' => array(
                'data_type' => 'integer',
                'title' => 'Speed in percents',
            ),
            'created_at' => array(
                'data_type' => 'datetime',
                'title' => 'Created at field',
            ),
        );
    }
}
?>
