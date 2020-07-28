<?

    // Get payed ordders list
    if (empty($_SERVER["DOCUMENT_ROOT"])) $_SERVER['DOCUMENT_ROOT'] = __DIR__."/../../";
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    
    set_time_limit(500);
    @ini_set('memory_limit', '2048M');
    @ini_set('output_buffering', 'off');
    
    // Use classes
    use \Bitrix\Main\Loader;
    use \Bitrix\Main\Application;
    use Bitrix\Main\Entity;
	use Bitrix\Main\Type\DateTime;
    use Bitrix\Highloadblock;
    use Bitrix\Highloadblock\HighloadBlockTable;
    use \Bitrix\Main\Web\HttpClient;
    
    Loader::includeModule("highloadblock");
    
    $INSTAGRA4DETAIL = "48";
    
    
    $arHLBlock = HighloadBlockTable::getById($INSTAGRA4DETAIL)->fetch();
    $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
    $strEntityDataClass = $obEntity->getDataClass();
    $arData = $strEntityDataClass::getList()->fetchAll();
    
    $httpClient = new HttpClient();
    
    $page = "";
    $arPosts = [];
    while (True) {
        
        $arAnswer = $httpClient->query("GET", "https://www.instagram.com/graphql/query/?query_id=17888483320059182&id=1513074597&first=20" . $page);
        $result = json_decode($httpClient->getResult());
        
        if ($result) {
            
            $arPosts = array_merge($arPosts, $result->data->user->edge_owner_to_timeline_media->edges);
            
            if ($result->data->user->edge_owner_to_timeline_media->page_info->has_next_page) {
                $page = "&after=" . $result->data->user->edge_owner_to_timeline_media->page_info->end_cursor;
            } else {
                $page = "";
            }
            
            if ($result->data->user->edge_owner_to_timeline_media->page_info->has_next_page == false) break;
        }
        
        sleep(3);
        
    }
    
    // var_dump($arPosts);die();
    
    if (!empty($arData)) {
        
        // DELETE ALL
        foreach ($arData as $arPost) {
            
            $strEntityDataClass::Delete($arPost['ID']);
            
        }
        
    } 
    
    // RECORD ALL
    if (!empty($arPosts)) {
        
        foreach ($arPosts as $arPost) {
            
            if ($arPost->node->id) {
                $strEntityDataClass::Add([
                    "UF_POSTLNK" => "https://www.instagram.com/p/" . $arPost->node->shortcode,
                    "UF_POSTID" => $arPost->node->id,
                    "UF_POSTTAGS" => "",
                    "UF_POSTTHUMBLNK" => $arPost->node->thumbnail_src,
                    "UF_POSTORIGIN" => $arPost->node->display_url,
                    "UF_POSTCONTENT" =>  preg_replace('#[^а-яА-ЯA-Za-z;\#@:_.ёЁ,? -]+#u', ' ', $arPost->node->edge_media_to_caption->edges[0]->node->text),
                ]);
            }
            
        }
        
        echo 'OK';
        
    }