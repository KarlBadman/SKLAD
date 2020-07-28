<?
define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
define('NO_AGENT_STATISTIC', true);
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', true);
define('AMQP_WITHOUT_SIGNALS', true);
define('BX_NO_ACCELERATOR_RESET', true);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$APPLICATION->IncludeComponent(
    'dsklad:sale.confirm.phone',
    '.default',
    array(
        'WAIT_TIME' => \Dsklad\Config::getOption('UF_CONF_PHONE_TIME'),  //время до повторной отправки
        'TRIES' => \Dsklad\Config::getOption('UF_CONF_PHONE_TRIES'),  //количество попыток
        'LENGTH' => \Dsklad\Config::getOption('UF_CONF_PHONE_LENGTH'),  //длина кода
        'NO_CONFORM_CODE' =>  \Dsklad\Config::getOption('UF_NO_CONFORM_CODE'), // коды телефонов для которых не нужно подтверждения
    ),
    false
);
