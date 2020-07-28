<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AutoAddUfUfPrepaymentToEntityHlblock2720190808115626703855 extends BitrixMigration
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
  'FIELD_NAME' => 'UF_PREPAYMENT',
  'USER_TYPE_ID' => 'boolean',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 'a:4:{s:13:"DEFAULT_VALUE";i:0;s:7:"DISPLAY";s:8:"CHECKBOX";s:5:"LABEL";a:2:{i:0;s:0:"";i:1;s:0:"";}s:14:"LABEL_CHECKBOX";s:0:"";}',
  'EDIT_FORM_LABEL' => 
  array (
    'ru' => 'Предоплата',
    'en' => '',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'ru' => 'Предоплата',
    'en' => '',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'ru' => 'Предоплата',
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
        $id = $this->getUFIdByCode('HLBLOCK_27', 'UF_PREPAYMENT');
        if (!$id) {
            throw new MigrationException('Не найдено пользовательское свойство для удаления');
        }

        (new CUserTypeEntity())->delete($id);
    }
}
