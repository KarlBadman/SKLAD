<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AutoAddUfUfPayPrecentToEntityHlblock2720190808115845833142 extends BitrixMigration
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
  'ENTITY_ID' => 'HLBLOCK_27',
  'FIELD_NAME' => 'UF_PAY_PRECENT',
  'USER_TYPE_ID' => 'double',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 'a:5:{s:9:"PRECISION";i:0;s:4:"SIZE";i:20;s:9:"MIN_VALUE";d:0;s:9:"MAX_VALUE";d:0;s:13:"DEFAULT_VALUE";d:50;}',
  'EDIT_FORM_LABEL' => 
  array (
    'ru' => 'Процент предоплаты',
    'en' => '',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'ru' => 'Процент предоплаты',
    'en' => '',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'ru' => 'Процент предоплаты',
    'en' => '',
  ),
  'ERROR_MESSAGE' => 
  array (
    'ru' => '',
    'en' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'ru' => '',
    'en' => '',
  ),
);

        $this->addUF($fields);
    }

    /**
     * Reverse the migration.
     *
     * @return mixed
     * @throws MigrationException
     */
    public function down()
    {
        $id = $this->getUFIdByCode('HLBLOCK_27', 'UF_PAY_PRECENT');
        if (!$id) {
            throw new MigrationException('Не найдено пользовательское свойство для удаления');
        }

        (new CUserTypeEntity())->delete($id);
    }
}
