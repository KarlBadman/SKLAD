<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if($_POST["is_ajax_post"] != "Y"):?>
    <?if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y"):?>
       <?if(strlen($arResult["REDIRECT_URL"]) == 0){
            if(strripos($_SERVER['REQUEST_URI'],'thankyou') === false){
                header('Location: /order/thankyou/'.$arResult['ORDER_ID']);
            }else{
                include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/confirm.php");
            }
        }?>
    <?else:?>
        <div class="ds-checkout" data-page-type="order-checkout">
            <div class="spinner hidden"></div>
            <div class="ds-back"><a href="/basket/">В корзину</a></div>
            <h1>Оформление заказа</h1>
            <div class="ds-basket-city">
                <div class="ds-basket-city__item" data-block-name="city_name">
                    <?$APPLICATION->IncludeComponent(
                        'swebs:dpd.current.city',
                        '',
                        array(
                            'DPD_HL_ID' => \Dsklad\Config::getParam('hl/dpd_cities'),
                            'COMPONENT_TEMPLATE' => 'new_order'
                        ),
                        false
                    );?>
                </div>
                <div class="ds-basket-city__btn">
                    <a href="<?= SITE_TEMPLATE_PATH ?>/ajax/region.php" class="js-ds-modal region" id="city_link" data-ds-modal-width="520">
                        Изменить
                    </a>
                </div>
                <div class="ds-basket-city__text">Доступность способов доставки и оплаты заказа зависит от выбранного города.</div>
            </div>
            <form name="ORDER_FORM" id="ORDER_FORM" action="" method="post">
                <?=bitrix_sessid_post()?>
                <div class="ds-checkout__content">
                    <div class="ds-checkout__form">
                        <div class="ds-checkout-form">
                            <?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/auth.php");?>
                            <?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");?>
                            <?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/comment.php");?>
                            <?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/payment.php");?>
                            <?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket.php");?>
                            <?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/map_modal.php");?>
                            <?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/courier_modal.php");?>
                        </div>
                    </div>
                    <?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/total.php");?>
                </div>
                <?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props_hidden.php");?>
            </form>
            <?if(!$arResult['AUTHORIZED']):?>
                <?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/phone_modal.php");?>
            <?endif;?>
            <script>
                window.dsCheckout.phpParams = <?=CUtil::PhpToJSObject($arResult['JS_PARAMS']);?>;
                <?if($_POST['is_ajax_post'] !='Y'):?>
                    window.dsCheckout.getOrderJson();
                <?endif;?>
            </script>
        </div>
    <?endif;?>
<?else:?>
    <?
    $APPLICATION->RestartBuffer();
    echo json_encode($arResult);
    ?>
<?endif;?>

