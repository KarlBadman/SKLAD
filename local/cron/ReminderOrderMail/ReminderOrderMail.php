<?
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Mail\Event;
use Bitrix\Sale\Internals;

define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
define('NO_AGENT_STATISTIC', true);
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', true);
define('AMQP_WITHOUT_SIGNALS', true);
define('BX_NO_ACCELERATOR_RESET', true);

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../../../');
}

if (!flock($lock_file = fopen(__FILE__ . '.lock', 'w'), LOCK_EX | LOCK_NB)) {
    die("Скрипт уже запущен\n");
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

@set_time_limit(0);
@error_reporting(E_WARNING);

$timeBefore = DateTime::createFromTimestamp(strtotime("-6 days"));

$rows = array();
$result = Internals\OrderTable::getList(array(
    'select' => array('ID', 'PERSON_TYPE_ID', 'DATE_STATUS', 'STATUS_ID','DATE_INSERT'),
    'filter' => array(
        'STATUS_ID' => 'WP',
        '>DATE_STATUS' => $timeBefore),
));

while ($row = $result->fetch()) {

    $resultOrder = Internals\OrderPropsValueTable::getList(array(
            'select' => array('ID', 'CODE', 'NAME', 'VALUE'),
            'filter' => array('ORDER_ID' => $row['ID']))
    );


    $mailOk = false;

    while ($rowOrder = $resultOrder->fetch()) {
        $rowsOrders[$rowOrder['CODE']] = array('NAME' => $rowOrder['NAME'], 'VALUE' => $rowOrder['VALUE']);
    }

    if ($row['PERSON_TYPE_ID'] == 1) {
        $email = $rowsOrders['F_EMAIL']['VALUE'];
        $name = $rowsOrders['F_NAME']['VALUE'];

        if($row['DATE_STATUS']->getTimestamp() > strtotime("-3 days") &&
           $row['DATE_STATUS']->getTimestamp() < strtotime("-2 days") ||
           $row['DATE_STATUS']->getTimestamp() > strtotime("-2 days") &&
           $row['DATE_STATUS']->getTimestamp() < strtotime("-1 days")
        ){
           $mailOk = true;
        }
    } else {
        $email = $rowsOrders['U_EMAIL']['VALUE'];
        $name = $rowsOrders['U_NAME']['VALUE'];


        if($row['DATE_STATUS']->getTimestamp() > strtotime("-5 days") &&
           $row['DATE_STATUS']->getTimestamp() < strtotime("-4 days") ||
           $row['DATE_STATUS']->getTimestamp() > strtotime("-3 days") &&
           $row['DATE_STATUS']->getTimestamp() < strtotime("-2 days")
        ){
            $mailOk = true;
        }
    }

    if($mailOk) {
        $mail = Event::send(array(
            "EVENT_NAME" => "SALE_ORDER_REMINDER",
            "LID" => SITE_ID,
            "LANGUAGE_ID" => LANGUAGE_ID,
            "C_FIELDS" => array(
                "EMAIL" => $email,
                "SALE_EMAIL" => \COption::GetOptionString('main', 'email_from'),
                "ORDER_ID" => $row['ID'],
                "NAME" => $name,
            ),
        ));
    }
}



