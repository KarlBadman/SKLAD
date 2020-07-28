<?php
session_start();
require_once ($_SERVER['DOCUMENT_ROOT']."/insta/class/adminka.php");

class login{

    public function init(){
        if(maininsta::get("sublog")){
            $this->check();
        }
    }

    public function check(){
        if(maininsta::get("login") == "adm" and maininsta::get("pass") == "dfdSdjs_342s"){
            $_SESSION['loginst'] = 1;
            header ('Location: /insta/admin/');
            exit();
        }
        echo "Логин или пароль не верный";
    }
}

$login = new login;
$login->init();
?>

<div>
    <form action="" method="post">
        <input type="text" name="login">
        <input type="password" name="pass">
        <input type="submit" name="sublog">
    </form>
</div>