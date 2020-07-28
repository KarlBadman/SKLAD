<?
    if (empty($_SERVER["DOCUMENT_ROOT"])) $_SERVER['DOCUMENT_ROOT'] = __DIR__."/../../";
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    require $_SERVER['DOCUMENT_ROOT'] . "/local/libs/vendor/autoload.php";
    require $_SERVER['DOCUMENT_ROOT'] . "/bitrix/admin/services/instagram/instaclient.php";
    
    use Insta\clientInterface;
    
    $client = new ClientInterface();
    $client->getAuthToken();
    $updated = $client->updateLikesForWidget();
    $client->updateFolloversCountInAccount();
    
    print '-------------------[ START ]-------------------' . PHP_EOL;
    printf('Time start: %s', date('Y.m.d H:i:s') . PHP_EOL);
    printf('Updated: %d ' . PHP_EOL, $updated);
    printf('Time end: %s', date('Y.m.s H:i:s') . PHP_EOL);
    print '--------------------[ END ]--------------------' . PHP_EOL;
    
    
?>