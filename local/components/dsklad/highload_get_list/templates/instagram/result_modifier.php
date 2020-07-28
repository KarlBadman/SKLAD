<?
    if (!empty($arResult['ITEMS'])) {
        foreach ($arResult['ITEMS'] as &$arItem) {
            if (!empty($arItem['UF_IMG'])) 
                $arItem['UF_IMG'] = CFile::GetFileArray($arItem['UF_IMG']);
            if (!empty($arItem['UF_LIKES']) && intVal($arItem['UF_LIKES']) > 0)
                $arItem['UF_LIKES'] = intVal($arItem['UF_LIKES']);
            
        }
    }
    
    require_once $_SERVER['DOCUMENT_ROOT'] . "/insta/class/main.php";
    $maininsta = new maininsta;
    $arResult['FOLLOWERS_COUNT'] = $maininsta->chtenie("2.txt")[0];
    
?>