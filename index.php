<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetPageProperty("keywords", "дизайнерская мебель интернет магазин обеденные стулья и столы барные стулья детские стулья");
$APPLICATION->SetPageProperty("description", "Интернет-магазин дизайнерской мебели «Дизайн Склад» предлагает купить стильные дизайнерские стулья и столы. Современная мебель от ведущих брендов мира. Регулярные акции и новинки. Доступна доставка в любой уголок России.");
$APPLICATION->SetPageProperty("title", "Дизайн Склад: Интернет-магазин дизайнерской мебели (столы и стулья) и аксессуаров для дома от Dsklad.ru");
$APPLICATION->SetTitle("Дизайн Склад: Интернет-магазин дизайнерской мебели (столы и стулья) и аксессуаров для дома от Dsklad.ru");
$APPLICATION->AddViewContent('page_type', 'data-page-type="home-page"');
?>

    <div class="border">
        <div class="ds-wrapper">
            <? $APPLICATION->IncludeFile('/include_areas/mainpage_categories.php');?>
            <?$APPLICATION->IncludeComponent(
                "dsklad:highload_get_list",
                "mainPageAdvantages",
                Array(
                    "COMPOSITE_FRAME_MODE" => "A",
                    "COMPOSITE_FRAME_TYPE" => "AUTO",
                    "COUNT" => "4",
                    "FILTER" => "",
                    "HL_TABLE_NAME" => "advantages",
                    "SELECT" => "",
                    "SORT_FIELD" => "UF_ORDER",
                    "SORT_ORDER" => "ASC",
                    "USE_CACHE" => "N",
                    "CACHE_TIME" => "1209600"
                )
            );?>
        </div>
    </div>
<?global $USER;?>
<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>
