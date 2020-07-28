<?
    
    foreach ($arResult["CATEGORIES"] as $category => $items) {
        
        if (empty($items)) {
            continue;
        }
        
        foreach ($items as $k => $v) {
            
            $PRODUCT = CIBlockElement::GetById($v['PRODUCT_ID'])->Fetch();
            $arResult["CATEGORIES"][$category][$k]['IS_SERVICE'] = 'N';
            if ($PRODUCT['IBLOCK_ID'] == '37') {
                $arResult["CATEGORIES"][$category][$k]['IS_SERVICE'] = 'Y';
            }
        }
    }
    
?>