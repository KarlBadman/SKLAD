<?
use Bitrix\Main\Application;
require "../templates/header.php";

if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/libs/vendor/autoload.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/libs/vendor/autoload.php");

if (file_exists($_SERVER['DOCUMENT_ROOT']."/bitrix/admin/services/ga-transactions-delete/ga-functions.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/services/ga-transactions-delete/ga-functions.php");
?>

<div class="header clearfix">
    <h3 class="text-muted">Отмена транзакций Google Analitycs</h3>
</div>

<?
$APPLICATION->SetPageProperty('title', 'Отмена транзакций Google Analitycs');;
$request = Application::getInstance()->getContext()->getRequest();
$transaction_id = (int)$request->getQuery('transaction_id');
?>

<div class="alert alert-info " role="alert">
    <h4 class="alert-heading">Описание</h4>
    <p>Отмена транзакции в аккаунте Google Analitycs по ID транзакции</p>
    <ul>
        <li>Введите ID транзакции</li>
        <li>Нажмите "Вернуть"</li>
        <li>Если транзакция с таким ID существует в GA (и создана не позднее полугода), то по ней будет создан полный "Возврат"</li>
    </ul>
    <!-- <hr> -->
    <!-- <p>Подробнее можно прочитать тут <a href="http://192.168.1.21/index.php">вики</a></p> -->
</div>

<form class="" action="index.html" method="post" data-form="deleteTransactions">
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Введите ID транзакции" aria-label="Введите ID транзакции" name="transaction-id">
        <div class="input-group-append">
            <input type="submit" class="btn btn-outline-secondary" value="Вернуть">
        </div>
    </div>
</form>

<div class="alert alert-warning d-none" role="alert" data-alert="fail">
    <h5>Ошибка</h5>
    <p>Введите ID транзакции, без него не полетит!</p>
</div>

<div class="alert alert-success d-none" role="alert" data-alert="success">
    <h5>Успешная операция</h5>
    <p>Возврат транзакции <b></b> успешно отправлен в Google Analitycs!</p>
</div>

<?require "../templates/footer.php"?>
