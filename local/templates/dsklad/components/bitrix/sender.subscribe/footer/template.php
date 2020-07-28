<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$buttonId = $this->randString();
?>
<div class="bx-subscribe"  id="sender-subscribe">
<?
$frame = $this->createFrame("sender-subscribe", false)->begin();
?>
	<?if(isset($arResult['MESSAGE'])): CJSCore::Init(array("popup"));?>
		<div id="sender-subscribe-response-cont" style="display: none;">
			<div class="bx_subscribe_response_container">
				<table>
					<tr>
						<td style="padding-right: 40px; padding-bottom: 0px;"><img src="<?=($this->GetFolder().'/images/'.($arResult['MESSAGE']['TYPE']=='ERROR' ? 'icon-alert.png' : 'ok_podpis.png'))?>" alt=""></td>
						<td>
                            <span>
                                <?if($arResult['MESSAGE']['CODE'] == 'message_success'):?>
                                    Вы успешно подписались на нашу рассылку
                                <?else:?>
                                    <?=htmlspecialcharsbx($arResult['MESSAGE']['TEXT'])?>
                                <?endif?>
                            </span>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<script>
			BX.ready(function(){
				var oPopup = BX.PopupWindowManager.create('sender_subscribe_component', window.body, {
					autoHide: true,
					offsetTop: 1,
					offsetLeft: 0,
					lightShadow: true,
					closeIcon: true,
					closeByEsc: true,
					overlay: {
						backgroundColor: 'rgba(57,60,67,0.82)', opacity: '80'
					}
				});
				oPopup.setContent(BX('sender-subscribe-response-cont'));
				oPopup.show();
			});
		</script>
	<?endif;?>

	<script>
		BX.ready(function()
		{
			BX.bind(BX("bx_subscribe_btn_<?=$buttonId?>"), 'click', function() {
				setTimeout(mailSender, 250);
				return false;
			});
		});

		function mailSender()
		{
			setTimeout(function() {
				var btn = BX("bx_subscribe_btn_<?=$buttonId?>");
				if(btn)
				{
					var btn_span = btn.querySelector("span");
					var btn_subscribe_width = btn_span.style.width;
					BX.addClass(btn, "send");
					<? $btnname = GetMessage("subscr_form_button_sent")?>
					btn_span.outterHTML = "<span><i class='fa fa-check'></i><?=$btnname?></span>";
					if(btn_subscribe_width)
						btn.querySelector("span").style["min-width"] = btn_subscribe_width+"px";
				}
			}, 400);
		}
	</script>

	<form role="form" method="post" action="<?=$arResult["FORM_ACTION"]?>"  onsubmit="BX('bx_subscribe_btn_<?=$buttonId?>').disabled=true;">
        <?=bitrix_sessid_post()?>
		<input type="hidden" name="sender_subscription" value="add">
        <div class="field__widget">
            <div class="field">
                <div class="input">
                    <input class="bx-form-control" type="email" name="SENDER_SUBSCRIBE_EMAIL" value="<?=$arResult["EMAIL"]?>" placeholder="<?=htmlspecialcharsbx(GetMessage('subscr_form_email_title'))?>">
                </div>
            </div>
        </div>

        <div class="field__widget field-submit">
            <div class="field">
			    <button class="mailclass button type-blue fill" id="bx_subscribe_btn_<?=$buttonId?>"><span><?=GetMessage("subscr_form_button")?></span></button>
            </div>
        </div>
	</form>

<?
$frame->beginStub();
?>
<?
$frame->end();
?>
</div>