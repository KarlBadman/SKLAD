<?php
require_once ($_SERVER['DOCUMENT_ROOT']."/insta/class/main.php");
?>

    <form action="" method="post">
        <input type="text" name="bd">
        <input type="submit" value="Отправить">
    </form>

<?
if($_REQUEST['bd']){
    $maininsta = new maininsta;

    echo "<pre>";
    print_r($maininsta->chtenie($_REQUEST['bd'])[0]);
    echo "</pre>";
}
?>