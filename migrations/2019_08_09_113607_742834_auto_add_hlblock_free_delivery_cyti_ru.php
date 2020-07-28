<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;
use Bitrix\Highloadblock\HighloadBlockTable;

class AutoAddHlblockFreeDeliveryCytiRu20190809113607742834 extends BitrixMigration
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
  'NAME' => 'FREEDELIVERYCYTIRU',
  'TABLE_NAME' => 'free_delivery_cyti_ru',
);

        $result = HighloadBlockTable::add($fields);

        if (!$result->isSuccess()) {
            $errors = $result->getErrorMessages();
            throw new MigrationException('Ошибка при добавлении hl-блока '.implode(', ', $errors));
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
