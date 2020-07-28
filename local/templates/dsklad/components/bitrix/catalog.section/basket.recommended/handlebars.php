<?php
$patchFolder = $_SERVER["DOCUMENT_ROOT"]."/local/templates/dsklad/components/bitrix/catalog.section/basket.recommended/handlebars";
$arFolder = scandir($patchFolder);
$arHandlebars = array();
foreach ($arFolder as $fileName){
    if($fileName !='.' && $fileName != '..'){
        $arHandlebars[explode(".",$fileName)[0]] = file_get_contents($patchFolder.'/'.$fileName);
    }
}
echo json_encode($arHandlebars);
