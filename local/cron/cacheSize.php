<?
    // Get bitrix cache size and notify on
    if (empty($_SERVER["DOCUMENT_ROOT"])) $_SERVER['DOCUMENT_ROOT'] = __DIR__."/../../";
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    use \Bitrix\Main\Context, 
        \Bitrix\Main\Mail\Event; 
        
    $cacheLimitSize = 15.0;
    $cacheLimitExt = "Gb";
    $cachePath = $_SERVER["DOCUMENT_ROOT"] . "/bitrix/cache/";
    $managedCachePath = $_SERVER["DOCUMENT_ROOT"] . "/bitrix/managed_cache/";
    $stackCachePath = $_SERVER["DOCUMENT_ROOT"] . "/bitrix/stack_cache/";
    $cacheSize = getCacheSize($cachePath);
    $managedCacheSize = getCacheSize($managedCachePath);
    $stackCacheSize = getCacheSize($stackCachePath);
    $realCacheSize = format_size($cacheSize + $managedCacheSize + $stackCacheSize);
    
    // var_dump($realCacheSize);die();
    
    if ($realCacheSize['size'] >= $cacheLimitSize AND $realCacheSize['ext'] === $cacheLimitExt) {
        LogEvent($realCacheSize['size']);
    }
    
    function getCacheSize ($path = "") {
        $totalsize=0;
        if (empty($path)) return $totalsize;

        if ($dirHandler = @opendir($path)) {
            while (false !== ($filename = readdir($dirHandler))) {
                if ($filename != "." && $filename != "..") {
                    
                    if (is_file($path . "/" . $filename))
                        $totalsize += filesize($path ."/". $filename);
          
                    if (is_dir($path . "/" . $filename))
                        $totalsize += getCacheSize($path ."/". $filename);
                }
            }
        }
        
        closedir($dirHandler);
        return $totalsize;
    }
    
    function format_size ($size = 0) {
        $metrics = array('b', 'Kb', 'Mb', 'Gb', 'Tb'); $metric = 0;
        while(floor($size / 1024) > 0) {
            ++$metric;
            $size /= 1024;
        }        
        
        return array(
            "size" => round($size, 1),
            "ext" => (isset($metrics[$metric]) ? $metrics[$metric] : '??')
        );
    }
    
    function LogEvent ($currentCacheSize = 0) {
        
        global $DB, $APPLICATION;
        
        $exception_type = "system_limit";
        $exception_entity = "bitrix_system_event_type";
        $strSql = "SELECT id FROM xtra_log WHERE entity_id = \"1\" AND exception_type=\"" . $exception_type . "\" AND exception_entity=\"" . $exception_entity . "\"";
        $exist = $DB->Query($strSql);
        
        // if(!$exist->Fetch()) {
        if(true) {
            
            $arFields = array(
                "entity_type" => "'cache_size'",
                "entity_id" => "\"1\"",
                "exception_type" => '\'' . $exception_type . '\'',
                "exception_entity" => '\'' . $exception_entity . '\''
            );
            
            $LOG_ID = $DB->Insert("xtra_log", $arFields);
            
            if(intval($LOG_ID)) {
                
                $obContext = Context::getCurrent();
                
                // goes to email
                $arFields['ID'] = $LOG_ID;
                $arFields['COMMENT'] = 'Зафиксировано превышение размеров кэша более 10 ГБ. Текущий размер: ' . format_size($currentCacheSize)['size'];
                
                $arMailFields = array(
                    'EVENT_NAME' => 'LOGGING',
                    'LID' => $obContext->getSite(),
                    'C_FIELDS' => $arFields
                );
                
                Event::send($arMailFields);
            }
        }
    }
    
    
?>