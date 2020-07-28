<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Dsklad\Tools\Helpers;

$this->setFrameMode(true);
?>

<script type="text/javascript">
    //Yandex commerce
    window.dataYandex = window.dataYandex || [];
    dataYandex.push({
        'ecommerce': {
            'add': {
                'products': [
                    {
                        'id': '<?= $arResult['ITEMS'][0]['ID'] ?>',
                        'name': '<?= $arResult['ITEMS'][0]['NAME'] ?>',
                        'price': <?= $arResult['ITEMS'][0]['PRICE'] ?>,
                        'quantity': <?= $arResult['ITEMS'][0]['QUANTITY'] ?>
                    }
                ]
            }
        }
    });
    //END Yandex commerce
</script>

<script type="text/javascript">
    if (typeof ym !== 'undefined') {
        ym(26291919, 'reachGoal', 'add_to_basket');
    }
    $('.basket_area').load('/include_areas/small_basket.php');
</script>

<div class="cart__popup">
    <span class="icon-svg ic-close ds-modal-close js-ds-modal-close" onclick="purepopup.closePopup();"></span>
    <input type="hidden" id="product_id-<?= $arResult['ITEMS'][0]['ID'] ?>" value="<?= $arResult['ITEMS'][0]['QUANTITY'] ?>">
    <div class="hidden-s">
        <div class="item">
            <?
            if (!empty($arResult['ITEMS'][0]['PICTURE'])) {
                ?>
                <img src="<?= $arResult['ITEMS'][0]['PICTURE'][203]['SRC'] ?>" width="203" height="203" alt="<?= $arResult['ITEMS'][0]['NAME'] ?>"/>
                <?
            }
            ?>
            <div class="optimal_price data">
                <h2>Товар добавлен в корзину</h2>
                <div class="info">
                    <div class="price ds-price" product-id="<?= $arResult['ITEMS'][0]['ID'] ?>"><?= $arResult['ITEMS'][0]['PRINT_PRICE'] ?></div>
                    <div class="count"><?= $arResult['ITEMS'][0]['QUANTITY'] ?> шт.</div>
                    <div class="name"><?= $arResult['ITEMS'][0]['NAME'] ?></div>
                </div>
                <div class="variant">
                    <?if (empty($arResult['ITEMS'][0]['PROPERTY_VID_KH_KA_VALUE'])) : ?>
                        <span class="label">Цвет: </span>
                        <span style="background-color: <?= $arResult['ITEMS'][0]['COLOR']['HEX'] ?>" class="color"></span>
                    <?else : ?>
                        <span class="label">Вид: </span>
                        <span style="" class=""><?= $arResult['ITEMS'][0]['PROPERTY_VID_KH_KA_VALUE']?></span>
                    <?endif;?>
                    <span class="value"><?= $arResult['ITEMS'][0]['COLOR']['NAME'] ?></span>
                    <span class="label">Артикул: </span>
                    <span class="value"><?= $arResult['ITEMS'][0]['PROPERTY_CML2_ARTICLE_VALUE'] ?></span>
                    <span class="old oldprice ds-price" product-id="<?= $arResult['ITEMS'][0]['ID'] ?>">
                        <?
                        if ($arResult['ITEMS'][0]['OLD_PRICE']) {
                            echo $arResult['ITEMS'][0]['PRINT_OLD_PRICE'];
                        }
                        ?>
                    </span>
                </div>
                <div class="options">
                    <a href="/basket/" class="button type-blue fill size-41 checkout_start"><?= $arParams['ACCEPT_BUTTON_LABEL'] ?></a>
                    <a class="button type-blue size-41 close">Продолжить покупки</a>
                </div>
            </div> <!-- /.optimal_price -->
        </div><!--  /.item -->
        <?
        /*
        // Сейчас этот блок выключен. Для использования нужно переписать под новую логику
        if (!empty($arResult['WITH_THIS'])) {
            ?>
            <div class="related__widget">
                <div class="heading">
                    <h3>Подойдет к покупке</h3>
                    <div class="nav">
                        <a href="" aria-disabled="true" class="prev">
                            <span class="icon__larr2"><svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#larr2"></use></svg></span>
                        </a>
                        <a href="" class="next">
                            <span class="icon__rarr2"><svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#rarr2"></use></svg></span>
                        </a>
                    </div>
                </div>

                <div class="scrollable">
                    <div class="list">
                        <div class="items">
                        <?
                        foreach ($arResult['WITH_THIS'] as $arWithFields) {
                            ?>
                            <div class="item">
                                <div class="info">
                                    <div class="title"><?= $arWithFields['NAME'] ?></div>
                                    <div class="configs_prices">
                                        <div class="priceNoDisc">
                                            <?
                                            if ($arWithFields['DISCOUNT'] == 'Y') {
                                                echo $arWithFields['MIN_PRICE_NO_DISC'];
                                            }
                                            ?>
                                        </div>
                                        <?
                                        if ($arWithFields['DISCOUNT'] == 'Y') {
                                            ?>
                                            <span class="sale__widget">%</span>
                                            <?
                                        }
                                        ?>
                                        <div class="price config_prices"><?= $arWithFields['MIN_PRICE'] ?>.–</div>
                                    </div>
                                    <a href="<?= $arWithFields['DETAIL_PAGE_URL'] ?>" class="button type-blue size-30 font-12">Перейти к товару</a>
                                </div>
                                <?
                                if (!empty($arWithFields['DETAIL_PICTURE'])) {
                                    ?>
                                    <div class="item_img_wrapper">
                                        <a href="<?= $arWithFields['DETAIL_PAGE_URL'] ?>">
                                            <img src="<?= $arWithFields['PICTURE'] ?>" alt="<?= $arWithFields['NAME'] ?>"/>
                                        </a>
                                    </div>
                                    <?
                                }
                                ?>
                            </div>
                            <?
                        }
                        ?>
                        </div>
                    </div>
                </div>
            </div><!--  /.related__widget -->
            <?
        }
        */
        ?>
    </div><!--  /.hidden-s -->

    <div class="hidden-gt-s js_for_mobile">
        <div class="default">
            <div class="subtotal">
                <div class="buttons">
                    <p>В корзине <span class="js_basket_total_quantity"><?= $arResult['TOTAL_QUANTITY'] ?></span> <span class="js_basket_total_quantity_after"><?= Helpers::plural_form($arResult['TOTAL_QUANTITY'], array('товар', 'товара', 'товаров')) ?></span> на <span class="js_basket_total_price"><?= number_format($arResult['TOTAL_PRICE'], 0, '', ' ') ?></span> руб.</p>
                    <a href="javascript:void(0);" onclick="purepopup.closePopup()" class="button type-blue size-41">Вернуться</a>
                    <a href="/basket/" class="button type-blue fill size-41 checkout_start">Оформить заказ</a>
                </div>
            </div><!--  /.subtotal -->
            <div class="basket__widget">
                <div class="list">
                    <?
                    foreach ($arResult['ITEMS'] as $item) {
                        ?>
                        <div
                            class="item"
                            data-id="<?= $item['BASKET_ID'] ?>"
                            data-quantity="<?= $item['QUANTITY'] ?>"
                            data-price="<?= $item['PRICE'] ?>"
                        >
                            <?
                            if (!empty($item['PICTURE'])) {
                                ?>
                                <div class="image">
                                    <img src="<?= $item['PICTURE'][50]['SRC'] ?>" alt="<?= $item['NAME'] ?>"/>
                                </div>
                                <?
                            }
                            ?>
                            <div class="whole-row">
                                <div class="info-row">
                                    <div class="info">
                                        <div class="category"><a href="">Стулья</a></div>
                                        <div class="name"><?= $item['NAME'] ?></div>
                                        <div class="data">
                                            <p class="counter-fallback hidden-gt-s">
                                                <span>Кол-во:</span> <del id="quantity"><?= $item['QUANTITY'] ?></del>
                                            </p>
                                            <p class="article"><span>Артикул:</span> <?= $item['PROPERTY_CML2_ARTICLE_VALUE'] ?></p>
                                            <p class="color"><span>Цвет:</span> <?= $item['COLOR']['NAME'] ?></p>
                                        </div>
                                    </div><!--  /.info -->
                                    <div class="price">
                                        <span class="hidden-l">Цена за шт.</span>
                                        <b class="ds-price js_item_price"><?= $item['PRICE'] ?></b>
                                        <span class="hidden-lte-m">/ шт.</span>
                                        <div class="sale__widget"<?= empty($item['PERCENT']) ? ' style="display: none;"' : '' ?>>%</div>
                                    </div>
                                </div>
                                <div class="count">
                                    <div data-min="1" data-measure=" шт." class="counter__widget">
                                        <a data-add="-1">-</a>
                                        <div class="input">
                                            <input type="tel" name="good[1]" autocomplete="off" value="<?= $item['QUANTITY'] ?>"/>
                                        </div>
                                        <a data-add="1">+</a>
                                    </div>
                                </div>
                                <div class="remove">
                                    <span class="icon__cross">
                                        <svg>
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cross"></use>
                                        </svg>
                                    </span>
                                </div>
                                <div class="total">
                                    <p class="hidden-gt-s">Стоимость:</p>
                                    <p>
                                        <b class="ds-price" id="total_price"><?= $item['TOTAL_PRICE'] ?></b>
                                        <span>В наличии.</span>
                                    </p>
                                </div><!--  /.total -->
                            </div><!--  /.whole-row -->
                        </div>
                        <?
                    }
                    ?>
                </div>
            </div><!--  /.basket__widget -->
        </div>
    </div><!--  /.hidden-gt-s -->
</div>

<script>
    updatePopupCart();
    $('.cart__popup .basket__widget .remove').click(function() {
        var itemNode = $(this).closest('.item');
        $.ajax({
            url: '<?= $this->__component->GetPath() ?>/ajax/remove.php',
            method: 'POST',
            dataType: 'json',
            data: {
                'ID': parseInt(itemNode.data('id'))
            },
            success: function(data) {
                if (data.status === 'ok') {
                    itemNode.slideUp(400, function() {
                        itemNode.remove();
                        updatePopupCart();
                    });
                } else {
                    console.log(data);
                }
            }
        });
    });

    $('.cart__popup .basket__widget .count a').click(function(e) {
        e.preventDefault();
        var itemNode = $(this).closest('.item');
        var delta = parseInt($(this).data('add'));
        $.ajax({
            url: '<?= $this->__component->GetPath() ?>/ajax/update.php',
            method: 'POST',
            dataType: 'json',
            data: {
                'ID': parseInt(itemNode.data('id')),
                'DELTA': delta
            },
            success: function(data) {
                if (data.status === 'ok') {
                    var quantityNode = itemNode.find('#quantity');
                    var newQuantity = parseInt(itemNode.data('quantity')) + delta;
                    var totalPrice = data.ITEM.PRICE * newQuantity;

                    itemNode.data('price', data.ITEM.PRICE);
                    itemNode.data('quantity', newQuantity);

                    quantityNode.text(newQuantity);
                    itemNode.find('.js_item_price').text(parseInt(data.ITEM.PRICE));
                    if (data.ITEM.DISCOUNT) {
                        itemNode.find('.sale__widget').show();
                    } else {
                        itemNode.find('.sale__widget').hide();
                    }
                    itemNode.find('#total_price').text(totalPrice.toLocaleString());

                    if (newQuantity === 0) {
                        itemNode.slideUp(400, function() {
                            itemNode.remove();
                            updatePopupCart();
                        });
                    } else {
                        updatePopupCart();
                    }
                } else {
                    console.log(data);
                }
            }
        });
    });

    $('.cart__popup .options a.close').click(function() {
        purepopup.closePopup();
    });

    /**
     * Обновление общего количества и стоимости, а также каорзины в шапке
     */
    function updatePopupCart()
    {
        BX.onCustomEvent('OnBasketChange');
    }

    /**
     * Склонение слов после числительных
     * http://dimox.name/plural-form-of-nouns/
     *
     * @param number
     * @param after
     * @returns {*}
     */
    function plural_form(number, after)
    {
        var cases = [2, 0, 1, 1, 1, 2];
        return after[ (number%100>4 && number%100<20) ? 2 : cases[Math.min(number%10, 5)] ];
    }
</script>