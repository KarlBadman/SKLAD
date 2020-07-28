<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("TITLE", "Спасибо за заказ!");
$APPLICATION->SetTitle("Спасибо за заказ");
$APPLICATION->AddViewContent('page_type', 'data-page-type="order-thanx"');
define('SHOW_THANK_YOU','Y');
?>

<div id="order_wrapper">
    <div class="success__page_custom" data-page-type="order-thanx">
        <div class="default">
            <div class="inner_success__page_custom">
                <div class="tnx-page">

                <div class="desc_success_order_custom tnx-page__content tnx-page__content--success">
                    <h1><?=GetMessage("SOA_TEMPL_ORDER_COMPLETE")?></h1>
                    <p>Заказ № <?=$_REQUEST['ORDER_ID']?> успешно сформирован. Менеджер свяжется с Вами в ближайшее время, для уточнения заказа.</p>
                </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
