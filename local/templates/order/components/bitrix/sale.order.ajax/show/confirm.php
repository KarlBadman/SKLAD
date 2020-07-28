<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $USER;
use Bitrix\Main\Localization\Loc;
?>
<div id="order_wrapper">
    <div class="success__page_custom" data-page-type="order-thanx">
        <div class="default">
            <div class="inner_success__page_custom">
                <div class="tnx-page">
                    <?if (!empty($arResult["ORDER"])) : ?>
                        <div class="desc_success_order_custom tnx-page__content tnx-page__content--success" data-order-page="thanx-fieldset">
                            <h1><?=GetMessage("SOA_TEMPL_ORDER_COMPLETE")?></h1>
                            <p><?= GetMessage("SOA_TEMPL_ORDER_SUC", Array("#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"], "#ORDER_ID#" => $arResult["ORDER"]["ACCOUNT_NUMBER"]))?></p>
                            <p>Заказ <a href="<?=$arParams["PATH_TO_PERSONAL"] . $arResult["ORDER"]["ACCOUNT_NUMBER"] . '/'?>" class="tnx-link">№ <?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?></a> успешно сформирован. Отслеживать статус выполнения можно в личном кабинете.</p>
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
                                        <?if (strlen($arResult['PAY_SYSTEM']['BUFFERED_OUTPUT']) > 0) : ?>
                                            <?=$arResult['PAY_SYSTEM']['BUFFERED_OUTPUT'];?>
                                        <?endif;?>
									<?endif;?>
								<?endif;?>
                                <?if (!$arResult['PAY_SYSTEM']['IS_AFFORD_PDF'] && $arResult["PAY_SYSTEM"]['IS_CASH'] != 'Y') : ?>
                                    <!-- PAYMENT LINK HERE -->
                                <?endif;?>
							<?endif;?>
                            <?foreach ($arResult["PAYMENT"] as $payment):?>
                                <?if(array_search($payment["PAY_SYSTEM_ID"],$arParams['PAYMENT_NEW_URL']) !== false):?>
                                    <?$arPaySystem = $arResult['PAY_SYSTEM_LIST_BY_PAYMENT_ID'][$payment["ID"]];?>
                                    <? if (strlen($arPaySystem["ACTION_FILE"]) > 0 && $arPaySystem["NEW_WINDOW"] == "Y" && $arPaySystem["IS_CASH"] != "Y"): ?>
                                        <?
                                        $orderAccountNumber = urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]));
                                        $paymentAccountNumber = $payment["ACCOUNT_NUMBER"];
                                        ?>
                                        <script>
                                            window.open('<?='../'.$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?=$orderAccountNumber?>&PAYMENT_ID=<?=$paymentAccountNumber?>');
                                        </script>
                                    <p>
                                    <?=Loc::getMessage("SOA_PAY_LINK", array("#LINK#" => '../'.$arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".$orderAccountNumber."&PAYMENT_ID=".$paymentAccountNumber))?>
                                    <? if (CSalePdf::isPdfAvailable() && $arPaySystem['IS_AFFORD_PDF']): ?>
                                    <br/>
                                        <?=Loc::getMessage("SOA_PAY_PDF", array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".$orderAccountNumber."&pdf=1&DOWNLOAD=Y"))?>
                                    </p>
                                    <? endif ?>
                                    <? else: ?>
                                        <?=$arPaySystem["BUFFERED_OUTPUT"]?>
                                    <? endif ?>
                                <? endif ?>
                            <?endforeach;?>
                            <div class="tnx-page__btn">
                                <a href="<?=$arParams["PATH_TO_PERSONAL"] . $arResult["ORDER"]["ACCOUNT_NUMBER"] . '/'?>" class="ds-btn ds-btn--light">Детали заказа</a>
                            </div>
                        </div>
                    <?else : ?>
                        <?if($_REQUEST['oneClick'] != 'Y'):?>
                        <?$arResult["ACCOUNT_NUMBER"] = strip_tags($arResult["ACCOUNT_NUMBER"]);?>
                            <div class="desc_success_order_custom tnx-page__content tnx-page__content--error">
                                <h1>Произошла ошибка</h1>
                                <p><?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST", Array("#ORDER_ID#" => $arResult["ACCOUNT_NUMBER"]))?></p>
                                <p><?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST1")?></p>

                                <div class="tnx-page__btn">
                                    <a href="/catalog/" class="ds-btn ds-btn--default">Перейти в каталог</a>
                                </div>

                            </div>
                        <?else:?>
                            <div class="desc_success_order_custom tnx-page__content tnx-page__content--success">
                                <h1><?=GetMessage("SOA_TEMPL_ORDER_COMPLETE")?></h1>
                                <p>Заказ № <?=str_replace('?oneClick=Y','',$_REQUEST['ORDER_ID'])?> успешно сформирован. Менеджер свяжется с Вами в ближайшее время, для уточнения заказа.</p>
                            </div>
                        <?endif?>
                    <?endif;?>
                </div>
            </div>
        </div>
    </div>
</div>
