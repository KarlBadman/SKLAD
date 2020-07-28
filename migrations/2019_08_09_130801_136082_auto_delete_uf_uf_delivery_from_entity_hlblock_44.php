<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AutoDeleteUfUfDeliveryFromEntityHlblock4420190809130801136082 extends BitrixMigration
{
    /**
     * Run the migration.
     *
     * @return mixed
     * @throws MigrationException
     */
    public function up()
    {
        $fields = array (
  'ID' => '351',
  'ENTITY_ID' => 'HLBLOCK_44',
  'FIELD_NAME' => 'UF_DELIVERY',
  'USER_TYPE_ID' => 'double',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'PRECISION' => 4,
    'SIZE' => 20,
    'MIN_VALUE' => 1.0,
    'MAX_VALUE' => 100.0,
    'DEFAULT_VALUE' => 0.0,
  ),
);
        $id = $this->getUFIdByCode('HLBLOCK_44', 'UF_DELIVERY');

        $oUserTypeEntity = new CUserTypeEntity();

        $dbResult = $oUserTypeEntity->delete($id);
        if (!$dbResult->result) {
            throw new MigrationException("Не удалось обновить удалить свойство с FIELD_NAME = {$fields['FIELD_NAME']} и ENTITY_ID = {$fields['ENTITY_ID']}");
        }
    }

    /**
     * Reverse the migration.
     *
     * @return mixed
     * @throws MigrationException
     */
    public function down()
    {
        return false;
    }
}
