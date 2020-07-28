<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/resultModifierComponents/ResultModifierBasket.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/resultModifierComponents/ResultModifierBasket.php");

$RMB = new ResultModifierBasket;

if($RMB->addProduct($_POST['productId'],$_POST['quantity'],$_POST['sessionId']) == 'Y'){
    echo $RMB->initOrder($_REQUEST['name'],$_REQUEST['phone'],$_REQUEST['sessionId'],true,$_REQUEST['productId'],$_REQUEST['quantity']);
}