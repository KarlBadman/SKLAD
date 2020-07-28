<?

namespace AppController;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Entity\Field;
use \Bitrix\Main\Entity\DataManager;

class FuserTokenRelationsTable extends DataManager {

    public static function getTableName() {
        return 'ws_fuser_token_relations';
    }

    public static function getMap() {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true
            ),
            'TOKEN' => array(
                'data_type' => 'string'
            ),
            'FUSERID' => array(
                'data_type' => 'string'
            ),
        );
    }
}

?>
