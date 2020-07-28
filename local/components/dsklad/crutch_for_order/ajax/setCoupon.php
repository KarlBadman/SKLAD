<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CBitrixComponent::includeComponentClass("dsklad:crutch_for_order");

echo  crutchForOrder::setCoupon($_POST['coupon'],$_POST['sessionId']);