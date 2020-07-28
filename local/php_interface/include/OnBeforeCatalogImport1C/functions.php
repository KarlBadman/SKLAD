<?php
function TagsImportBeforeCatalogImport1C(&$arParams, $ABS_FILE_NAME)
{
    $REPORT_MODE = false;
    $tagsIblock = 44;
    $objXML = new CDataXML();
    $objXML->Load($ABS_FILE_NAME);
    $arResult = $objXML->GetArray();

    $tags = $arResult['КоммерческаяИнформация']['#']['Каталог'][0]['#']['Теги'][0]['#']['Тег'];
    if(count($tags) > 0){
        $parents = array();
        foreach ($tags as $tag){

            //выбираем родителей и собираем их в массив
            if($tag['#']['Родитель'][0]['#'] !== '00000000-0000-0000-0000-000000000000' && empty($parents[$tag['#']['Родитель'][0]['#']])){
                $rs = CIBlockSection::GetList (
                    Array(),
                    Array("IBLOCK_ID" => $tagsIblock, 'XML_ID' => $tag['#']['Родитель'][0]['#'])
                )->fetch();
                $parents[$tag['#']['Родитель'][0]['#']] = $rs['ID'];
            }

            //Сбор основных данных для добавления
            $arFields = Array(
                "IBLOCK_ID" => $tagsIblock,
                "NAME" => trim($tag['#']['Наименование'][0]['#']),
                "XML_ID" => $tag['#']['Ид'][0]['#'],
                "ACTIVE" => 'Y',
            );

            if(!empty($parents[$tag['#']['Родитель'][0]['#']])) {
                //добавляем родителя, коль есть
                $arFields["IBLOCK_SECTION_ID"] = $parents[$tag['#']['Родитель'][0]['#']];
            }

            if($tag['#']['ПометкаУдаления'][0]['#'] == 'true'){
                //Удалить запись к чертям. Буду удалять только теги. Группы если что руками
                continue;
            }

            if($tag['#']['ЭтоГруппа'][0]['#'] == 'true'){
                //ниже 2 гетлиста, потому что у битры нет поддержки сложносоставных запросов для выборки категорий инфоблоков...
                $section = CIBlockSection::GetList(
                    Array(),
                    Array(
                        "IBLOCK_ID" => $tagsIblock,
                        "XML_ID" => $tag['#']['Ид'][0]['#']
                    )
                )->fetch();
                if(empty($section['ID'])) {
                    $section = CIBlockSection::GetList(
                        Array(),
                        Array(
                            "IBLOCK_ID" => $tagsIblock,
                            "ID" => $tag['#']['Код'][0]['#']
                        )
                    )->fetch();
                }

                $bs = new CIBlockSection;
                if(!empty($section['ID'])) {
                    $res = $bs->Update($section['ID'], $arFields);
                    if($REPORT_MODE)
                        print_r('  ---SECTION UPDATED-- ' . $section['ID'] . ' ' . $res);
                } else {
                    $res = $bs->Add($arFields);
                    if($REPORT_MODE)
                        print_r('  ---SECTION ADDED-- ' . $tag['#']['Ид'][0]['#'] . ' ' . $res);
                }

            } else { //добавим теги
                $arFields['CODE'] = Cutil::translit($arFields['NAME'],"ru"); //транслит для тега, будет в урлах

                $element = CIBlockElement::GetList(
                    Array(),
                    Array(
                        "IBLOCK_ID" => $tagsIblock,
                        array(
                            "LOGIC" => "OR",
                            array('ID' => $tag['#']['Код'][0]['#']),
                            array('XML_ID' => $tag['#']['Ид'][0]['#']),
                        ),
                    )
                )->fetch();

                $be = new CIBlockElement;
                if(!empty($element['ID'])) {
                    $res = $be->Update($element['ID'], $arFields);
                    if($REPORT_MODE)
                        print_r(' --TAG UPDATING ' . $element['ID'] . ' ' . $res);
                } else {
                    $res = $be->Add($arFields);
                    if($REPORT_MODE)
                        print_r(' --TAG ADDING' . $tag['#']['Ид'][0]['#'] . ' ' . $res);
                }
            }
            unset($rs, $element, $section, $arFields);
        }
    }
  //  mail('dev@ooott.ru', 'Debug 1C import tag event handler', $some);
}