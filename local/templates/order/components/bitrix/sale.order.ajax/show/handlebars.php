<?php

$patchFolder = $_SERVER["DOCUMENT_ROOT"]."/local/templates/order/components/bitrix/sale.order.ajax/show/handlebars";
$arFolder = scandir($patchFolder);
$arHandlebars = array();
foreach ($arFolder as $fileName){
    if($fileName !='.' && $fileName != '..'){
        $arHandlebars[explode(".",$fileName)[0]] = file_get_contents($patchFolder.'/'.$fileName);
    }
}
echo json_encode($arHandlebars);
