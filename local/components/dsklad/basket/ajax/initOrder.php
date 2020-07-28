<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/resultModifierComponents/ResultModifierBasket.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/resultModifierComponents/ResultModifierBasket.php");

$RMB = new ResultModifierBasket;

echo $RMB->initOrder($_REQUEST['name'],$_REQUEST['phone'],$_REQUEST['sessionId']);


