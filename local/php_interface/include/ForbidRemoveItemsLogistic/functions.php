<?php

function ForbidRemoveItemsLogistic($id){
    global $USER;
    if (!$USER->IsAdmin()) {
        $arGroups = CUser::GetUserGroup($USER->GetID());
        $rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"),  Array("ID"=>implode('|',$arGroups)));
        while ($row = $rsGroups->fetch()){
           if($row['STRING_ID'] == 'LOGIST'){
               global $APPLICATION;
               $APPLICATION->throwException('У Вас нет прав на удаление товара');
               return false;
           }
        }
    }else{
        return false;
    }
}