<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;
use Bitrix\Highloadblock\HighloadBlockTable;

class AutoUpdateHlblockDiscountDeliveryCytiRu20190809114527606731 extends BitrixMigration
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
  'NAME' => 'DISCOUNTDELIVERYCITYRU',
  'TABLE_NAME' => 'discount_delivery_cyti_ru',
);

        $result = HighloadBlockTable::update(44, $fields);

        if (!$result->isSuccess()) {
            $errors = $result->getErrorMessages();
            throw new MigrationException('Ошибка при обновлении hl-блока '.implode(', ', $errors));
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
