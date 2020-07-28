<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
?>

<?$APPLICATION->IncludeComponent(
	"dsklad:basket",
	"",
	Array(
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"ID_WARRANTY" => "14202",
		"PRICE_TYPE" => "2",
		"RECOMMENDED_ITEMS_IN_CART" => "RECOMMENDED_ITEMS_IN_CART",
	)
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>