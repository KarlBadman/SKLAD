<?
    if (!empty($arResult['ITEMS'])) {
        foreach ($arResult['ITEMS'] as &$arItem) {
            if (!empty($arItem['UF_IMG'])) {
                $arImage = CFile::GetFileArray($arItem['UF_IMG']);
                $arItem['UF_IMG'] = CFile::ResizeImageGet($arImage, array('width' => 900, 'height' => 900), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
            }
        }
    }