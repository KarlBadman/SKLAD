<?
    if (!empty($arResult['ITEMS'])) {
        foreach ($arResult['ITEMS'] as &$arItem) {
            if (!empty($arItem['UF_IMG'])) {
                $arImage = CFile::GetFileArray($arItem['UF_IMG']);
                $arItem['UF_IMG'] = CFile::ResizeImageGet($arImage, array('width' => 200, 'height' => 200), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
            }
            if (!empty($arItem['UF_LIKES']) && intVal($arItem['UF_LIKES']) > 0)
                $arItem['UF_LIKES'] = intVal($arItem['UF_LIKES']);
        }
    }
    require_once $_SERVER['DOCUMENT_ROOT'] . "/insta/class/main.php";
    $maininsta = new maininsta;
    $arResult['FOLLOWERS_COUNT'] = $maininsta->chtenie("2.txt")[0];