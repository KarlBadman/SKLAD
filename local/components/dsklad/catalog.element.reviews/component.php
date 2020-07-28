<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Data\Cache;

$cache = Cache::createInstance();
$arResult['REVIEWS'] = [];

if ($arParams['USE_CACHE'] == 'Y' && !empty($arParams['CACHE_TIME'])) {
    $cacheId = crc32('arGoogleReviewList' . json_encode($arParams));
    if ($cache->initCache($arParams['CACHE_TIME'], $cacheId)) {
        $arResult = $cache->getVars();
    } else {
        $result = companyReveiws::getGoogleReviews($arParams);
        if ($result['status'] == 'OK') {
            $cache->startDataCache();
            $arResult['REVIEWS'] = $result;
            $cache->endDataCache($arResult);
        }
    }
} else {
    $result = companyReveiws::getGoogleReviews($arParams);
    if ($result['status'] == 'OK') {
        $arResult['REVIEWS'] = $result;
    }
}
$this->IncludeComponentTemplate();