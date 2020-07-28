<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @global string $componentPath
 * @global string $templateName
 * @var CBitrixComponentTemplate $this
 */
$cartStyle = 'bx-basket';
$cartId = "bx_basket".$this->randString();
$arParams['cartId'] = $cartId;

if ($arParams['POSITION_FIXED'] == 'Y')
{
	$cartStyle .= "-fixed {$arParams['POSITION_HORIZONTAL']} {$arParams['POSITION_VERTICAL']}";
	if ($arParams['SHOW_PRODUCTS'] == 'Y')
		$cartStyle .= ' bx-closed';
}
else
{
	$cartStyle .= ' bx-opener';
}
?><script>
var <?=$cartId?> = new BitrixSmallCart;
</script>
<div id="<?=$cartId?>"><?
	/** @var \Bitrix\Main\Page\FrameBuffered $frame */
	$frame = $this->createFrame($cartId, false)->begin();
		require(realpath(dirname(__FILE__)).'/ajax_template.php');
	$frame->beginStub();
		$arResult['COMPOSITE_STUB'] = 'Y';
		require(realpath(dirname(__FILE__)).'/top_template.php');
		unset($arResult['COMPOSITE_STUB']);
	$frame->end();
?></div></div>
<script type="text/javascript">
	<?=$cartId?>.siteId       = '<?=SITE_ID?>';
	<?=$cartId?>.cartId       = '<?=$cartId?>';
	<?=$cartId?>.ajaxPath     = '<?=$componentPath?>/ajax.php';
	<?=$cartId?>.templateName = '<?=$templateName?>';
	<?=$cartId?>.arParams     =  <?=CUtil::PhpToJSObject ($arParams)?>; // TODO \Bitrix\Main\Web\Json::encode
	<?=$cartId?>.closeMessage = '<?=GetMessage('TSB1_COLLAPSE')?>';
	<?=$cartId?>.openMessage  = '<?=GetMessage('TSB1_EXPAND')?>';
	<?=$cartId?>.activate();
</script>
<script type="text/Javascript">
	(function () {
		basketLineModifier = {
			selectors : {
				basketLineWrapper : "[data-selector=\"minicart-wrapper\"]",
				itemType : "data-item-type",
			},
			isServiceRemoveLastItemOnBasketLine : function () {
				var self = this;
				if ($(self.selectors.basketLineWrapper).length > 0) {
					if (
						$(self.selectors.basketLineWrapper + " [" + self.selectors.itemType + "=\"P\"]").length == 1
						&& $(self.selectors.basketLineWrapper + " [" + self.selectors.itemType + "=\"S\"]").length == 1
					) {
						<?=$arParams['cartId']?>.refreshCart({
							sbblRemoveItemFromCart: $(self.selectors.basketLineWrapper + " [" + self.selectors.itemType + "=\"S\"]").data('id')
						});
					} 
				}
			},
		}
	})();
</script>
