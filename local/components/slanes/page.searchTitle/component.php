<?
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Dsklad\Config;
use \Bitrix\Main\Data\Cache;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!IsModuleInstalled('search')) {
    ShowError(GetMessage('CC_BST_MODULE_NOT_INSTALLED'));
    return;
}

CModule::IncludeModule('search');
$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$query = filter_var(trim($request->get('search')), FILTER_SANITIZE_STRING);
$cache = Cache::createInstance();
$string = $arParams['CACHE_KEY'].crc32($query);


if ($cache->initCache($arParams['CACHE_TIME'], $string, "page.searchTitle")) {

    $vars = $cache->GetVars();
    $arResult = $vars;

}else{


    $arResult['CATEGORIES'] = array();
    if (!empty($query) && CModule::IncludeModule('highloadblock') && Config::getParam('highload/replacement_in_search') > 0) {
        \CUtil::decodeURIComponent($query);

        $arResult['alt_query'] = '';
        if ($arParams['USE_LANGUAGE_GUESS'] !== 'N') {
            $arLang = \CSearchLanguage::GuessLanguage($query);
            if (is_array($arLang) && $arLang['from'] != $arLang['to']) {
                $arResult['alt_query'] = \CSearchLanguage::ConvertKeyboardLayout($query, $arLang['from'], $arLang['to']);
            }
        }

        $arResult['query'] = $query;
        $arResult['phrase'] = stemming_split($query, LANGUAGE_ID);

        $hlblock = HL\HighloadBlockTable::getById(Config::getParam('highload/replacement_in_search'))->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entityClass = $entity->getDataClass();

        $res = $entityClass::getList(array(
            'select' => array('*'),
        ));

        while ($replacement = $res->fetch()) {
            if (strripos($arResult['query'], $replacement['UF_SEARCH_LINE']) !== false) {
                $arResult["alt_query"] = '';
            }
        }


        $arParams['NUM_CATEGORIES'] = intval($arParams['NUM_CATEGORIES']);
        if ($arParams['NUM_CATEGORIES'] <= 0) {
            $arParams['NUM_CATEGORIES'] = 1;
        }

        $arParams['TOP_COUNT'] = intval($arParams['TOP_COUNT']);
        if ($arParams['TOP_COUNT'] <= 0) {
            $arParams['TOP_COUNT'] = 5;
        }

        $arOthersFilter = array('LOGIC' => 'OR');


        for ($i = 0; $i < $arParams['NUM_CATEGORIES']; $i++) {
            $bCustom = true;
            if (is_array($arParams['CATEGORY_' . $i])) {
                foreach ($arParams['CATEGORY_' . $i] as $categoryCode) {
                    if ((strpos($categoryCode, 'custom_') !== 0)) {
                        $bCustom = false;
                        break;
                    }
                }
            } else {
                $bCustom = (strpos($arParams['CATEGORY_' . $i], 'custom_') === 0);
            }

            if ($bCustom) {
                continue;
            }


            $category_title = trim($arParams['CATEGORY_' . $i . '_TITLE']);
            if (empty($category_title)) {
                if (is_array($arParams['CATEGORY_' . $i])) {
                    $category_title = implode(', ', $arParams['CATEGORY_' . $i]);
                } else {
                    $category_title = trim($arParams['CATEGORY_' . $i]);
                }
            }

            if (empty($category_title)) {
                continue;
            }

            $arResult['CATEGORIES'][$i] = array(
                'TITLE' => htmlspecialcharsbx($category_title),
                'ITEMS' => array()
            );

            $exFILTER = array(
                0 => \CSearchParameters::ConvertParamsToFilter($arParams, 'CATEGORY_' . $i),
            );

            $exFILTER[0]['LOGIC'] = 'OR';

            if ($arParams['CHECK_DATES'] === 'Y') {
                $exFILTER['CHECK_DATES'] = 'Y';
            }

            $obTitle = new \CSearchTitle;
            $obTitle->setMinWordLength((int)$request->get('l'));


            if ($obTitle->Search(
                $arResult['alt_query'] ? $arResult['alt_query'] : $arResult['query'],
                $arParams['TOP_COUNT'],
                $exFILTER,
                false,
                $arParams['ORDER']
            )) {
                while ($ar = $obTitle->Fetch()) {
                    $arResult['ITEMS'][] = array(
                        'PARAM1' => $ar['PARAM1'],
                        'PARAM2' => $ar['PARAM2'],
                        'ITEM_ID' => $ar['ITEM_ID'],
                    );

                    $arResult['IDS'][] = $ar['ITEM_ID'];
                    $arResult['IBLOCK'][$ar['PARAM2']][$ar['ITEM_ID']] = $ar['ITEM_ID'];
                }
            }
        }
    }

    $cache->endDataCache ($arResult);
}

$phrase = new CSearchStatistic($query);
$phrase->PhraseStat(count($arResult['ITEMS']));

$this->IncludeComponentTemplate();