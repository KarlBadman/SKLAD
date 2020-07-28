<?
use Bitrix\Main\Application;
require "../templates/header.php";
?>
    <div class="header clearfix">
        <h3 class="text-muted">Себестоимость товаров</h3>
    </div>
<?
function array_to_csv_download($array, $filename = "product_costs.csv", $delimiter=",") {
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'";');
    $f = fopen('php://output', 'w');
    foreach ($array as $line) {
        $line = array_map(function($value) {return mb_convert_encoding($value, 'Windows-1251', 'UTF-8');}, $line);
        fputcsv($f, $line, $delimiter);
    }
    die;
}

$APPLICATION->SetPageProperty('title', 'Себестоимость товаров');
    $request = Application::getInstance()->getContext()->getRequest();
    $csv = $request->getQuery('csv');

// Получаем товары
$CIBlockElement = CIBlockElement::GetList(
    $arOrder = array("SORT" => "ASC"),
    $arFilter = array(
        "IBLOCK_ID" => 35,
        "ACTIVE" => "Y",
    ),
    $arGroupBy = false,
    $arNavStartParams = false,
    $arSelectFields = array("ID", "PROPERTY_CML2_ARTICLE")
);
while ($ob = $CIBlockElement->GetNextElement()) {
    $arResult = $ob->GetFields();
    $tovarsArticle[$arResult['ID']] = $arResult['PROPERTY_CML2_ARTICLE_VALUE'];
    $tovarsId[] = $arResult['ID'];
}

// Получаем офферы
$allProduts = CCatalogSKU::getOffersList(
    $tovarsId,
    '',
    array(
        "ACTIVE" => "Y",
        "IBLOCK_ID" => 36,
    ),
    array('ID', 'CATALOG_QUANTITY', 'PROPERTY_FREEZE_STOCK_CHANGING', 'NAME')
);

if (!is_null($csv)) {
    ob_end_clean();
    $csvProductList = array(array('Наименование товара', 'Артикул', 'Себестоимость', 'Валюта'));
    foreach ($allProduts as $id => $product){
        foreach ($product as $offer){
            $csvProductList[] = array(
                $offer['NAME'],
                $tovarsArticle[$id],
                $offer['CATALOG_PURCHASING_PRICE'],
                $offer['CATALOG_PURCHASING_CURRENCY']);
        }
    }
    array_to_csv_download($csvProductList);
} ?>
    <div class="alert alert-info " role="alert">
        <h4 class="alert-heading">Описание</h4>
        <p>Ниже приводятся данные по заполненной себестоимости тоаров на сайте.</p>
        <hr>
    </div>
    <a href="?csv" name="save" class="btn btn-primary">Скачать в CSV</a>
    <hr>

    <div class="container" >
    </div>
        <table class="table table-striped table-bordered table-hover ">
            <thead class="thead-dark">
                <tr>
                    <th>Наименование товара</th>
                    <th>Артикул</th>
                    <th>Себестоимость</th>
                    <th>Валюта</th>
                </tr>
            </thead>
            <tbody>
            <? foreach ($allProduts as $id => $product){
                foreach ($product as $offer){ ?>
                    <tr>
                        <td><?=$offer['NAME']?></td>
                        <td><?=$tovarsArticle[$id]?></td>
                        <td><?=$offer['CATALOG_PURCHASING_PRICE']?></td>
                        <td><?=$offer['CATALOG_PURCHASING_CURRENCY']?></td>
                    </tr>
             <? }
            } ?>
            </tbody>
        </table>
<?require "../templates/footer.php"?>