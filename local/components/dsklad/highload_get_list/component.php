<?
    
    use Bitrix\Highloadblock as HL;
    use Bitrix\Main\Entity;
    use Bitrix\Main\Data\Cache;

    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
    
    \Bitrix\Main\Loader::includeModule('highloadblock');
    $cache = Cache::createInstance();
    
    $arResult['ITEMS'] = [];
    $arParams['SORT'] = [];
    
    if (empty($arParams['HL_TABLE_NAME'])) {
        ShowError(GetMessage("HL_TABLE_PARAM_NOT_SET"));
        return;
    }
    
    if (!empty($arParams['FILTER']) && $arParams['FILTER'] !== "*") {
        $arParams['FILTER'] = (array)json_decode($arParams['~FILTER']);
    }
    
    if (!empty($arParams['SELECT']) && $arParams['SELECT'] !== "*") {
        $arParams['SELECT'] = explode(',', $arParams['SELECT']);
    } 
    
    $arParams['SORT_ORDER'] = $arParams['SORT_ORDER'] === "ASC" ? $arParams['SORT_ORDER'] : "DESC";

    if (!empty($arParams['SORT_FIELD'])) {
        $arParams['SORT'] = [$arParams['SORT_FIELD'] => $arParams['SORT_ORDER']];
    }
    
    $hlIblock = HL\HighloadBlockTable::getList(['filter' => ["TABLE_NAME" => $arParams['HL_TABLE_NAME']]])->Fetch();
    
    if (!isset($hlIblock['ID'])) {
        ShowError(GetMessage("HL_TABLE_PARAM_NOT_EXIST"));
        return;
    }
    
    $hlIblockEntity = HL\HighloadBlockTable::compileEntity($hlIblock);
    $hlIblockEntityClass = $hlIblockEntity->getDataClass();
    
    if ($arParams['USE_CACHE'] == 'Y' && !empty($arParams['CACHE_TIME'])) {
        
        $cacheId = crc32('arHLIBlockListItems' . $arParams['HL_TABLE_NAME'] . json_encode($arParams));
        if ($cache->initCache($arParams['CACHE_TIME'], $cacheId)) {
            
            $arResult = $cache->getVars();
            
        } elseif ($cache->startDataCache()) {
            
            $result = $hlIblockEntityClass::getList([
                "filter" => is_array($arParams['FILTER']) ? $arParams['FILTER'] : [],
                "select" => is_array($arParams['SELECT']) ? $arParams['SELECT'] : ["*"],
                "order" => $arParams['SORT'],
                "limit" => intVal($arParams["COUNT"]) > 0 ? intVal($arParams["COUNT"]) : "10"
            ]);
            
            while ($arItem = $result->fetch())
                $arResult['ITEMS'][] = $arItem;
            
            $cache->endDataCache($arResult);
        }
        
    } else {
        
        $result = $hlIblockEntityClass::getList([
            "filter" => is_array($arParams['FILTER']) ? $arParams['FILTER'] : [],
            "select" => is_array($arParams['SELECT']) ? $arParams['SELECT'] : ["*"],
            "order" => $arParams['SORT'],
            "limit" => intVal($arParams["COUNT"]) > 0 ? intVal($arParams["COUNT"]) : "10"
        ]);
        
        while ($arItem = $result->fetch())
            $arResult['ITEMS'][] = $arItem;
        
    }
    
    $APPLICATION->AddHeadScript($this->GetPath() . "/script.js");
    $this->IncludeComponentTemplate();
?>