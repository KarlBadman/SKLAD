<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if ($_POST["markers"] and CModule::IncludeModule("sale"))
{
    CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID());
}
echo json_encode($_POST['markers']);