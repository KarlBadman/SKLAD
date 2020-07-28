<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!empty($_REQUEST['hashtag'])) {
    
    $APPLICATION->IncludeComponent(
        "dsklad:highload_get_list",
        "instagramElementModal",
        Array(
            "COMPOSITE_FRAME_MODE" => "A",
            "COMPOSITE_FRAME_TYPE" => "AUTO",
            "COUNT" => "5",
            "FILTER" => "{\"UF_POSTCONTENT\":[\"%#".$_REQUEST['hashtag']."%\"]}",
            "HL_TABLE_NAME" => "instagram4detail",
            "SELECT" => "",
            "SORT_FIELD" => "ID",
            "SORT_ORDER" => "RANDOM",
            "USE_CACHE" => "Y",
            "CACHE_TIME" => "1209600"
        )
    );
    
} else {
    
    $APPLICATION->IncludeComponent(
    "dsklad:highload_get_list",
    "instagramElementModalDefault",
    Array(
        "COMPOSITE_FRAME_MODE" => "A",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "COUNT" => "5",
        "FILTER" => "",
        "HL_TABLE_NAME" => "instagram",
        "SELECT" => "",
        "SORT_FIELD" => "ID",
        "SORT_ORDER" => "RANDOM",
        "USE_CACHE" => "Y",
        "CACHE_TIME" => "1209600"
    )
);
    
}