<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CBitrixComponent::includeComponentClass("dsklad:crutch_for_order");

echo  crutchForOrder::changeTerminal($_POST['terminalId'],$_POST['sessionId'],$_POST['minPack']);