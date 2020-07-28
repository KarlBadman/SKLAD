<? //Вы уже смотрели
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

Loader::includeModule('highloadblock');

$arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
$obEntity = HighloadBlockTable::compileEntity($arHLBlock);
$photoEntityDataClass = $obEntity->getDataClass();

foreach ($arResult['ITEMS'] as $itemId => &$arElement) {

    global $USER, $EXCLUDEDGROUPID;
    if (in_array($EXCLUDEDGROUPID, $USER->GetUserGroupArray())) {
        $object = new CIBlockElementRights($arParams['IBLOCK_ID'], $arElement['ID']);
        $arRights = $object->GetRights();
        $elementDisable = array_map(function ($item) {
            global $EXCLUDEDGROUPID, $EXCLUDEDRIGHTID;
            if ($item['GROUP_CODE'] == 'G'.$EXCLUDEDGROUPID && $item['TASK_ID'] == $EXCLUDEDRIGHTID)
                return true;
        }, $arRights);

        if (in_array(true, $elementDisable))
            unset($arResult['ITEMS'][$itemId]);
    }
    
    // HIDE2VIEW
    if (checkHitOnHideProductPosition($arElement['PROPERTIES']['HIDE2VIEW']['VALUE_XML_ID'])) {
        unset($arResult['ITEMS'][$itemId]); continue;
    }
    
    $arElement['CONTAINER_CLASS'] = '';
    if (!empty($arElement['OFFERS'])) {
        $arElement['CONTAINER_CLASS'] = ' has-variants';
    }

    $ph1 = $ph2 = array();
    // prices
    if (!empty($arElement['OFFERS'])) {
        foreach ($arElement['OFFERS'] as $k => $arOffer) {
            
            // HIDE2VIEW
            if (checkHitOnHideProductPosition($arOffer['PROPERTIES']['HIDE2VIEW']['VALUE_XML_ID'])) {
                unset($arElement['OFFERS'][$k]); continue;
            }
            
            $arPrice = array();
            $dbPrices = CPrice::GetList(array(), array('=PRODUCT_ID' => $arOffer['ID'], 'CAN_BUY'=> 'Y'));
            while ($ar_price = $dbPrices->fetch()) {
                if (!$arPrice['MAX_PRICE'] || $arPrice['MAX_PRICE'] < $ar_price['PRICE']) {
                    $arPrice['MAX_PRICE'] = $ar_price['PRICE'];
                }
                if (!$arPrice['MIN_PRICE'] || $arPrice['MIN_PRICE'] > $ar_price['PRICE']) {
                    $arPrice['MIN_PRICE'] = $ar_price['PRICE'];
                }
            }
            // скидка от кол-ва с комбинированием цветов
            if ($arElement['PROPERTIES']['MIN_CHECK']['VALUE'] == 'да') {
                if ($arPrice['MIN_PRICE'] > $arElement['PROPERTIES']['MIN_PRICE']['VALUE']) {
                    $arPrice['MIN_PRICE'] = $arElement['PROPERTIES']['MIN_PRICE']['VALUE'];
                }
                if ($arOffer['PROPERTIES']['MIN_PRICE']['VALUE'] > 0 && $arOffer['PROPERTIES']['MIN_PRICE']['VALUE'] < $arPrice['MIN_PRICE']) {
                    $arPrice['MIN_PRICE'] = $arOffer['PROPERTIES']['MIN_PRICE']['VALUE'];
                }
            }
            if (!$arElement['MAX_PRICE'] || $arElement['MAX_PRICE'] < $arPrice['MAX_PRICE']) {
                $arElement['MAX_PRICE'] = $arPrice['MAX_PRICE'];
            }
            if (!$arElement['MIN_PRICE'] || $arElement['MIN_PRICE'] > $arPrice['MIN_PRICE']) {
                $arElement['MIN_PRICE'] = $arPrice['MIN_PRICE'];
            }
            unset($dbPrices, $arPrice, $ar_price);

            $OffersWithKeys[$arOffer['ID']] = $arOffer; //keys to glue images later
            $ph1[$arOffer['ID']] = $arOffer['PROPERTIES']['FOTOGRAFIYA_1']['~VALUE'];
            $ph2[$arOffer['ID']] = $arOffer['PROPERTIES']['FOTOGRAFIYA_2']['~VALUE'];
        }
        $arElement['OFFERS'] = $OffersWithKeys;//подмена на массив с ключами
        unset($OffersWithKeys);

        if ($arElement['MAX_PRICE'] > $arElement['MIN_PRICE']) {
            $arElement['OLD_PRICE'] = number_format($arElement['MAX_PRICE'], 0, '', ' ');
            $arElement['DISCOUNT_PERCENT'] = round(100 - $arElement['MIN_PRICE'] * 100 / $arElement['MAX_PRICE']);
        }
        $arElement['MIN_PRICE_NUMBER'] = $arElement['MIN_PRICE'];
        $arElement['MIN_PRICE'] = number_format($arElement['MIN_PRICE'], 0, '', ' ');
    }

    // offers images
    $rsData = $photoEntityDataClass::getList(array(
        'select' => array('UF_FILE', 'UF_XML_ID'),
        'filter' => array('=UF_XML_ID' => array_merge(array_values($ph1), array_values($ph2)))
    ));

    while($arItem = $rsData->Fetch()){
        if($arItem['UF_FILE']){
            if(in_array($arItem['UF_XML_ID'], array_values($ph1))) {
                $sourceImage = CFile::GetFileArray($arItem['UF_FILE']);

                $keys = array_keys($ph1, $arItem['UF_XML_ID']);
                foreach($keys as $img) {
                    $arImage = CFile::ResizeImageGet($sourceImage, array('width' => 264, 'height' => 264), BX_RESIZE_IMAGE_PROPORTIONAL, false, array('name' => 'sharpen', 'precision' => 15));
                    $arElement['OFFERS'][$img]['DISPLAY_PROPERTIES']['FOTOGRAFIYA_4']['IMG']['SRC'] = $arImage['src'];
                    $arImage = CFile::ResizeImageGet($sourceImage, Array('width' => 68, 'height' => 68), BX_RESIZE_IMAGE_PROPORTIONAL, false, array('name' => 'sharpen', 'precision' => 15));
                    $arElement['OFFERS'][$img]['DISPLAY_PROPERTIES']['FOTOGRAFIYA_1']['IMG']['SRC'] = $arImage['src'];
                }
            } else {
                $keys = array_keys($ph2, $arItem['UF_XML_ID']);
                foreach($keys as $img) {
                    $arImage = CFile::ResizeImageGet($arItem['UF_FILE'], array('width' => 264, 'height' => 264), BX_RESIZE_IMAGE_PROPORTIONAL, false, array("name" => "sharpen", "precision" => 15));
                    $arElement['OFFERS'][$img]['DISPLAY_PROPERTIES']['SECOND_FOTO']['S']['SRC'] = $arImage['src'];
                }
            }
        }
    }

    $arElement['OFFERS'] = array_values($arElement['OFFERS']); //flush keys

    unset($arOffer, $sourceImage, $arItem, $rsData, $ph1, $ph2);
}
