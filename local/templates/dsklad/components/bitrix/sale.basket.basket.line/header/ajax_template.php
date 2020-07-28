<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$this->IncludeLangFile('template.php');

$cartId = $arParams['cartId'];

require(realpath(dirname(__FILE__)).'/top_template.php');

if ($arParams["SHOW_PRODUCTS"] == "Y" && ($arResult['NUM_PRODUCTS'] > 0 || !empty($arResult['CATEGORIES']['DELAY'])))
{
?>
    <div id="<?=$cartId?>products" class="ds-header-popup ds-header-popup--minicart">
        <div class="ds-header-minicart" data-selector="minicart-wrapper">
            <div class="ds-header-minicart__content">
                <div class="mini-goods mini-goods--cart">
                    <h3>Корзина</h3>
                    <?foreach ($arResult["CATEGORIES"] as $category => $items):
                        if (empty($items))
                            continue;
                        ?>
                        <?foreach ($items as $v):?>
                            <div class="mini-goods__item" data-item-type="<?if ($v['IS_SERVICE'] == 'Y') : ?>S<?else : ?>P<?endif;?>" data-id="<?=$v['ID']?>">
                                <div class="mini-goods__img">
                                    <img src="<?=$v["PICTURE_SRC"]?>" alt="">
                                </div>
                                <div class="mini-goods__content">
                                    <p><?=$v["NAME"]?></p>
                                    <div class="mini-goods__info">
                                        <span class="mini-goods__quantity"><?=$v["QUANTITY"]?> шт.</span>
                                        <span class="ds-price"><?=$v['PRICE']?></span>
                                    </div>
                                </div>
                                <div class="mini-goods__remove" onclick="<?=$cartId?>.removeItemFromCart(<?=$v['ID']?>)"><span class="mini-goods-remove"></span></div>
                            </div>
                        <?endforeach?>
                    <?endforeach?>
                </div>
            </div>
            <div class="ds-header-minicart__footer">
                <a class="ds-btn ds-btn--full ds-btn--default" href="<?=$arParams['PATH_TO_BASKET']?>">Перейти в корзину<span class="ds-price ds-price--point"><?=number_format(preg_replace("/[^0-9]/", '', $arResult['TOTAL_PRICE']),0,'.',' ')?></span></a>
            </div>
        </div>
    </div>

	<script>
		BX.ready(function(){
			<?=$cartId?>.fixCart();
		});
	</script>
<?
}