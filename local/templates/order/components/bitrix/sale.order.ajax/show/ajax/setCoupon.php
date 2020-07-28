<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/resultModifierComponents/ResultModifierOrder.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/resultModifierComponents/ResultModifierOrder.php");

$RMO = new ResultModifierOrder;

echo $RMO->setCoupon($_POST['coupon'],$_POST['sessionId']);
