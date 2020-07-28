<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

function getImagesSRC($arResult){
    foreach ($arResult['BANNERS_PROPERTIES'] as $key => $arBanner) {
        if(!empty($arBanner['IMAGE_ID'])){
            $arResult['BANNERS_PROPERTIES'][$key]['IMAGE_SRC'] = CFile::GetPath($arBanner["IMAGE_ID"]);
        }else{
            $arResult['BANNERS_PROPERTIES'][$key]['IMAGE_SRC'] = SITE_TEMPLATE_PATH."/images/no-img.jpg";
        }
    }
    return $arResult;
}

function init($arResult){

    $arResult = getImagesSRC($arResult);

    return $arResult;
}

$arResult = init($arResult);
