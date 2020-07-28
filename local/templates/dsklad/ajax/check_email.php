<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$answer = false;

if ($_POST["email"])
{
    $rsUsers = CUser::GetList(($by="ID"),($order="desc"),Array("=EMAIL" => $_POST['email']));
    while ($rsUser = $rsUsers->Fetch()){
        //var_dump($rsUser["ID"]);
        $answer=true;
        //$id_user = $rsUser["ID"];
    }
}
echo json_encode($answer);
