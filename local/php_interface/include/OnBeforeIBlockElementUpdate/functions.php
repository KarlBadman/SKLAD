<?php
function OnBeforeIBlockElementUpdate(&$arFields)
{

    if($_REQUEST["type"] == "catalog" && $_REQUEST["mode"] == "import")
    {
        $arStopListFields = array(
            35 => array(
                'DETAIL_TEXT',
                'DETAIL_TEXT_TYPE',
            )
        );


        foreach($arStopListFields[ $arFields["IBLOCK_ID"] ] as $codeField)
            unset($arFields[$codeField]);


    }
}