<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

function nameColor($items){
    foreach ($items as $key => $item){
        if($item['CODE'] == 'KOD_TSVETA') {
            foreach ($item['VALUES'] as $keyProp => $prop) {
                $items[$key]['VALUES'][$keyProp]['VALUE'] = stristr($prop['VALUE'],'#',true);
            }
        }
    }

    return $items;
}

function initResultModifier($arResult){
    $arResult["ITEMS"] = nameColor($arResult["ITEMS"]);
    return $arResult;
}

$arResult = initResultModifier($arResult);