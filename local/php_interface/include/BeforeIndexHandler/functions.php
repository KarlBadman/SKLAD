<?php
function BeforeIndexHandler($arFields)
{
    if (!CModule::IncludeModule("iblock")) { // подключаем модуль
        return $arFields;
    }

    if ($arFields["MODULE_ID"] == "iblock") {
        if ($arFields["PARAM2"] == \Dsklad\Config::getParam('iblock/offers')) {
            $res = \CIBlockElement::GetList(
                array(),
                array(
                    "ID" => $arFields["ITEM_ID"]
                ),
                false,
                false,
                array(
                    "PROPERTY_CML2_LINK.PROPERTY_MATERIAL",
                    "PROPERTY_CML2_LINK.DETAIL_TEXT",
                    "PROPERTY_STRING_FOR_SEARCH_TITLE",
                    "PROPERTY_CML2_LINK.PROPERTY_RUSSKAYA_TRANSKRIPTSIYA",
                    "PROPERTY_CML2_LINK.PROPERTY_CML2_ARTICLE"
                )
            );

            if ($ob = $res->GetNextElement()) {
                $addFields = array(
                    "PROPERTY_CML2_LINK_PROPERTY_CML2_ARTICLE_VALUE",
                    "PROPERTY_CML2_LINK_PROPERTY_RUSSKAYA_TRANSKRIPTSIYA_VALUE",
                    "PROPERTY_STRING_FOR_SEARCH_TITLE_VALUE",
                    "PROPERTY_CML2_LINK_DETAIL_TEXT"
                );

                $fields = $ob->GetFields();
                $temp = array();
                foreach ($addFields as $key => $value) {
                    if ($fields[$value]) {
                        $temp[] = strip_tags($fields[$value]);
                    }
                }

                $stroka = implode(' ', $temp);
                if ($stroka) {
                    $arFields["TITLE"] .= " " . $stroka;
                }
            }
        } else if ($arFields["PARAM2"] == \Dsklad\Config::getParam('iblock/catalog')) {
            $res = \CIBlockElement::GetList(
                array(),
                array(
                    "ID" => $arFields["ITEM_ID"]
                ),
                false,
                false,
                array(
                    "DETAIL_TEXT",
                    "PROPERTY_RUSSKAYA_TRANSKRIPTSIYA",
                    "PROPERTY_CML2_ARTICLE"
                )
            );

            if ($ob = $res->GetNextElement()) {
                $addFields = array(
                    "PROPERTY_CML2_ARTICLE_VALUE",
                    "PROPERTY_RUSSKAYA_TRANSKRIPTSIYA_VALUE",
                    "DETAIL_TEXT"
                );

                $fields = $ob->GetFields();
                $temp = array();
                foreach ($addFields as $key => $value) {
                    if ($fields[$value]) {
                        $temp[] = strip_tags($fields[$value]);
                    }
                }

                $stroka = implode(' ', $temp);
                if ($stroka)  {
                    $arFields["TITLE"] .= " " . $stroka;
                }
            }
        }
    }

    return $arFields; // вернём изменения
}