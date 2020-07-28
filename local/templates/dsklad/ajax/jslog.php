<?
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
    require($_SERVER["DOCUMENT_ROOT"]."/local/libs/vendor/autoload.php");
    
    use Sinergi\BrowserDetector\Os;
    use Sinergi\BrowserDetector\Browser;
    use Sinergi\BrowserDetector\Device;
    use Sinergi\BrowserDetector\Language;
    
    global $USER, $DB;
    
    \Bitrix\Main\Loader::includeModule('sale');
    
    $OS = new Os();
    $Browser = new Browser;
    $Device = new Device;
    $Lang = new Language;
    if ($_COOKIE['BX_USER_ID'])
        $userID = $DB->Query("SELECT `ID` FROM `b_user` WHERE `BX_USER_ID`='" . $_COOKIE['BX_USER_ID'] . "'")->fetch();
        
    try {
        
        $_REQUEST['request'] = json_decode(urldecode(base64_decode($_REQUEST['request'])));
        $client = [
            ' Client OS is -----> ' . $OS->getName() ? : "undefined",
            ' Client browser is -----> ' . $Browser->getName() . ' version ' . $Browser->getVersion(),
            ' Client device is -----> ' . $Device->getName() ? : "undefined",
            ' Client langueage is -----> ' . $Lang->getLanguage() ? : "undefined"
        ];
        
        $xtra_info = [
            ' Bitrix user ID -----> ' . $USER->GetID() ? : $userID['ID'] ? : '',
            ' Bitrix basket user ID -----> ' . \Bitrix\Sale\Fuser::getid() ? : "undefined",
            ' Page URL with error  -----> ' . $_REQUEST['request']->location ? : "undefined",
            ' Error filename -----> ' . $_REQUEST['request']->filename ? : "undefined",
            implode(',', $client),
        ];
        
        extra_log([
            'exception_type' => 'js_error_handler',
            'exception_entity' => 'js_front_error_method_handler',
            'mail_comment' => 'Ошибка JS на клиенте ' . json_encode($_REQUEST['request']) . " | <b>CLIENT INFO -----> " . implode(',', $xtra_info) . "</b>",
            'entity_id' => '110' . strlen(json_encode($_REQUEST['request'])),
            'entity_type' => 'JS ERROR ON FRONTEND',
            'exception_text' => json_encode($_REQUEST['request']) . " | CLIENT INFO -----> " . implode(',', $xtra_info),
        ]);
        
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    
?>


