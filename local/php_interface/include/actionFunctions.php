<?php

//определение телефона
mobileDetectFunction();

//запуск миграции
actionMigrationGo();

$GLOBALS['COMPONENT_CACHE'] = checkHitOnHideProductPosition('ABROAD') || checkHitOnHideProductPosition('MSKABROAD') ? "N" : "A";