<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class DPDCitiesListComponent extends \CBitrixComponent
{
    public function executeComponent()
    {
        if ($this->startResultCache()) {
            \Bitrix\Main\Loader::includeModule('highloadblock');

            $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(DPD_CITIES_HL_ID)->fetch();
            $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
            $strEntityDataClass = $obEntity->getDataClass();

            $arCities = [];
            $rsData = $strEntityDataClass::getList([
                'order' => [
                    'UF_COUNTRYNAME' => 'ASC',
                    'UF_SORT' => 'ASC',
                    'UF_CITYNAME' => 'ASC'
                ],
                'filter' => [
                    'UF_SHOW_IN_LIST' => true
                ],
                'select' => [
                    'ID',
                    'UF_COUNTRYNAME',
                    'UF_CITYNAME',
                    'UF_CITYCODE',
                    'UF_FAVORITES'
                ]
            ]);
            while ($arItem = $rsData->fetch()) {
                $letter = substr(strtoupper($arItem['UF_CITYNAME']), 0, 1);
                $arCities[$arItem['UF_COUNTRYNAME']]['CITIES'][$letter][] = [
                    'NAME' => $arItem['UF_CITYNAME'],
                    'CODE' => $arItem['UF_CITYCODE']
                ];
                if ($arItem['UF_FAVORITES']) {
                    $arCities[$arItem['UF_COUNTRYNAME']]['FAVORITES'][] = [
                        'NAME' => $arItem['UF_CITYNAME'],
                        'CODE' => $arItem['UF_CITYCODE']
                    ];
                }
            }

            foreach ($arCities as &$arRegion) {
                ksort($arRegion['CITIES']);
            }
            unset($arRegion);

            $this->arResult['ITEMS'] = $arCities;

            $this->setResultCacheKeys(['ITEMS']);
            $this->includeComponentTemplate();
        }
    }
}