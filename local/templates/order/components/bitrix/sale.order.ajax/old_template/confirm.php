<script src="//static.yandex.net/kassa/pay-in-parts/ui/v1/"></script>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $USER;
?>
<div id="order_wrapper">
    <div class="success__page_custom" data-page-type="order-thanx">
        <div class="default">
            <div class="inner_success__page_custom">
				<?if (!empty($arResult["ORDER"])) : ?>
	                <h1><?=GetMessage("SOA_TEMPL_ORDER_COMPLETE")?></h1>
	                <div class="success_order_custom" data-order-page="thanx-fieldset">
	                    <p>№ вашего заказа:</p>
	                    <div class="inner_success_order_custom" data-order-revenue="<?=$arResult['ORDER']['PRICE'];?>" data-order-shipping="<?=$arResult['ORDER']['PRICE_DELIVERY']?>">
	                        <span class="number_success_order_custom" data-order-page="order-id-field" data-order-email="<?=$USER->GetEmail();?>" data-order-products="<?=$arResult["GRID"]["JSON_DATA"]?>"><?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?></span>
                            <?if (!empty($arResult["PAY_SYSTEM"])) : ?>
								<?if (strlen($arResult["PAY_SYSTEM"]["ACTION_FILE"]) > 0) : ?>

									<?if ($arResult["PAY_SYSTEM"]["NEW_WINDOW"] == "Y") : ?>

										<script language="JavaScript">
											window.open('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?=urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))?>');
										</script>
										<?=GetMessage("SOA_TEMPL_PAY_LINK", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))));?>

										<?if (CSalePdf::isPdfAvailable() && CSalePaySystemsHelper::isPSActionAffordPdf($arResult['PAY_SYSTEM']['ACTION_FILE'])) : ?>
											<?= GetMessage("SOA_TEMPL_PAY_PDF", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))."&pdf=1&DOWNLOAD=Y")) ?>
										<?endif;?>

									<?else : ?>
									
										<?if (strlen($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"])>0 && !$arResult['PAY_SYSTEM']['IS_AFFORD_PDF']) : ?>

											<?try {
												include($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]);
											} catch(\Bitrix\Main\SystemException $e) {

												if($e->getCode() == CSalePaySystemAction::GET_PARAM_VALUE)
													$message = GetMessage("SOA_TEMPL_ORDER_PS_ERROR");
												else
													$message = $e->getMessage();

												echo '<span style="color:red;">'.$message.'</span>';
											}?>

										<?endif;?>

                                        <?if (strlen($arResult['PAY_SYSTEM']['BUFFERED_OUTPUT']) > 0 && $arResult['PAY_SYSTEM']['PAY_SYSTEM_ID'] !== "4") : ?>
                                            <?=$arResult['PAY_SYSTEM']['BUFFERED_OUTPUT'];?>
                                        <?endif;?>

									<?endif;?>

								<?endif;?>
                                <?if (!$arResult['PAY_SYSTEM']['IS_AFFORD_PDF'] && $arResult["PAY_SYSTEM"]['IS_CASH'] != 'Y') : ?>
    		                        <?if ($arResult['PAY_SYSTEM']['CODE'] == "YANDEX_INSTALLMENTS") : ?>
	    		                        <div class="credit-button"></div>
	    		                        <script type="text/javascript">
	    		                        	const $checkoutCreditUI = YandexCheckoutCreditUI({ shopId: '89296', sum: <?=intVal($arResult['ORDER']['PRICE'])?>, language: 'ru' });
											const $checkoutCreditButtonon = $checkoutCreditUI({ tag: 'input', type: 'button', theme: 'default', domSelector: '.credit-button' });
											// $checkoutCreditButtonon.on('click', function () { location.href = "https://money.yandex.ru/eshop.xml?shopid=89296&sum=<?=intVal($arResult['ORDER']['PRICE'])?>&paymentType=CR&scid=82719&customerNumber=<?=Bitrix\Sale\Fuser::getId()?>"; });
											$checkoutCreditButtonon.on('click', function () { 
												$.ajax({
													url : "<?=SITE_TEMPLATE_PATH?>/ajax/getcheckoutUrl.php",
													data : "orderID=<?=$arResult['ORDER']['ID']?>",
													success : function (r) {
														var r = JSON.parse(r);
														if (r.checkoutUrl)
															location.href = r.checkoutUrl;
														else console.error("Do not get checkoutUrl path on ajax request");
													}, 
													error : function () { console.error("Ajax on get order checkout url is failed"); }
												});
											});
	    		                        </script>
    		                        <?else : ?>
    		                        	<a href="#" class="link_success_order_custom">
	    		                            <span class="icon__lock"><svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#card2"></use></svg></span>
	    									<span class="label">Перейти к оплате</span>
	    		                        </a>
    		                        <?endif;?>
                                <?endif;?>
							<?endif;?>
	                    </div>
	                </div>
					<div class="desc_success_order_custom">
						<p class="tit_title_success">Здравствуйте!</p>
						<p><?= GetMessage("SOA_TEMPL_ORDER_SUC", Array("#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"], "#ORDER_ID#" => $arResult["ORDER"]["ACCOUNT_NUMBER"]))?></p>
						<p><?= GetMessage("SOA_TEMPL_ORDER_SUC1", Array("#LINK#" => $arParams["PATH_TO_PERSONAL"] . $arResult["ORDER"]["ACCOUNT_NUMBER"] . '/')) ?></p>
						<p class="bot_tit_desc_success">С уважением,<br> Служба поддержки <a href="/">Dsklad.ru</a></p>
					</div>
					<div class="footer_success_order_custom">
						<p class="footer_success_order_custom_1"><a href="<?=$arParams["PATH_TO_PERSONAL"] . $arResult["ORDER"]["ACCOUNT_NUMBER"] . '/'?>"><span>Отслеживайте заказ</span></a></p>
						<p class="footer_success_order_custom_2">Отследить статус и посмотреть информацию по заказу можно в личном кабинете, в разделе <a href="/personal/">История заказов</a></p>
					</div>
				<?else : ?>
                    <?$arResult["ACCOUNT_NUMBER"] = strip_tags($arResult["ACCOUNT_NUMBER"]);?>
	                <div class="desc_success_order_custom">
	                	<p class="tit_title_success"><?=GetMessage("SOA_TEMPL_ERROR_ORDER")?></p>
	                    <p><?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST", Array("#ORDER_ID#" => $arResult["ACCOUNT_NUMBER"]))?></p>
	                    <p><?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST1")?></p>
	                    <p class="bot_tit_desc_success">С уважением,<br> Служба поддержки <a href="/">Dsklad.ru</a></p>
	                </div>
				<?endif;?>
                <div class="hidden_payment_success_order">
					<div class="sale-paysystem-wrapper"></div>
				</div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function () {
        var paymentLink = $('.sale-paysystem-yandex-checkout-button-item').attr('href');
        $('.link_success_order_custom').prop('href', paymentLink);
    })();
</script>
