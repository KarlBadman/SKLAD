<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);
?>
<script type="text/javascript">
    $('.basket_area').load('/include_areas/small_basket.php');
</script>
<div class="cart__popup">
    <div class="hidden-s">
        <div class="item">
            <?
            if (!empty($arResult['ITEM']['PICTURE'])) {
                ?>
                <img src="<?= $arResult['ITEM']['PICTURE'][203]['SRC'] ?>" width="203" height="203" alt="<?= $arResult['ITEM']['NAME'] ?>"/>
                <?
            }
            ?>
            <div class="data">
                <h2>Товар добавлен в корзину</h2>
                <div class="info">
                    <div class="count count_basket_popup_big">
                        <div data-min="1" data-measure=" шт." class="counter__widget">
                            <a href="" data-add="-1" id="item-minus_big" data="<?= $arResult['ITEM']['ITEM_ID'] ?>">-</a>
                            <div class="input">
                                <input type="tel" name="good[1]" readonly autocomplete="off" value="<?= $arResult['ITEM']['QUANTITY'] ?> шт." id="quantity_big"/>
                            </div>
                            <a href="" data-add="1" id="item-plus_big" data="<?= $arResult['ITEM']['ITEM_ID'] ?>">+</a>
                        </div>
                    </div>

                    <div class="price">
                        <?
                        if (!empty($arResult['ITEM']['OLD_PRICE'])) {
                            echo $arResult['ITEM']['OLD_PRICE'];
                        } else {
                            echo $arResult['ITEM']['PRICE'];
                        }
                        ?>
                        .–
                    </div>
                    <div class="name"><?= $arResult['ITEM']['NAME'] ?></div>
                </div>
                <div class="variant">
                    <span class="label">Цвет: </span>
                    <span style="background-color: <?= $arResult['ITEM']['COLOR']['HEX'] ?>" class="color"></span>
                    <span class="value"><?= $arResult['ITEM']['COLOR']['NAME'] ?></span>
                    <span class="label">Артикул: </span>
                    <span class="value"><?= $arResult['ITEM']['PROPERTY_CML2_ARTICLE_VALUE'] ?></span>
                    <?
                    if (!empty($arResult['ITEM']['OLD_PRICE']) && $arResult['ITEM']['OLD_PRICE'] != $arResult['ITEM']['PRICE']) {
                        ?>
                        <span class="oldprice"><?= $arResult['ITEM']['PRICE'] ?> .–</span>
                        <?
                    } else {
                        ?>
                        <br>
                        <?
                    }
                    ?>
                </div>
                <div class="options">
                    <a href="/basket/" class="button type-blue fill size-41">Оформить заказ</a>
                    <a href="javascript:void(0);" onclick="purepopup.closePopup()" class="button type-blue size-41">Продолжить покупки</a>
                </div>
            </div>
        </div>
        <?
        if (!empty($arResult['WITH_THIS'])) {
            ?>
            <div class="related__widget">
                <div class="heading">
                    <h3>Подойдет к покупке</h3>
                    <div class="nav">
                        <a href="" aria-disabled="true" class="prev">
                            <span class="icon__larr2">
                                <svg>
                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#larr2"></use>
                                </svg>
                            </span>
                        </a>
                        <a href="" class="next">
                            <span class="icon__rarr2">
                                <svg>
                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#rarr2"></use>
                                </svg>
                            </span>
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
                                    <?
                                    if (!empty($arWithFields['DETAIL_PICTURE'])) {
                                        ?>
                                        <img src="<?= $arWithFields['DETAIL_PICTURE']['SRC'] ?>" alt="<?= $arWithFields['NAME'] ?>"/>
                                        <?
                                    }
                                    ?>
                                    <div class="info">
                                        <div class="title"><?= $arWithFields['NAME'] ?></div>
                                        <div class="price"><?= $arWithFields['MIN_PRICE'] ?>.–</div>
                                        <a href="<?= $arWithFields['DETAIL_PAGE_URL'] ?>" class="button type-blue size-30 font-12">Перейти к товару</a>
                                    </div>
                                </div>
                                <?
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?
        }
        ?>
        
        <? $APPLICATION->IncludeFile(
            SITE_TEMPLATE_PATH . '/include_areas/order-block3.php',
            array(),
            array(
                'MODE' => 'php'
            )
        ); ?>
    </div>

    <div class="hidden-gt-s">
        <div class="default">
            <div class="subtotal">
                <p><?/*В корзине 2 товара на 9 986 руб.*/?></p>
                <div class="buttons">
                    <a href="javascript:void(0);" onclick="purepopup.closePopup();" class="button type-blue size-41">Вернуться</a>
                    <a href="/basket/" class="button type-blue fill size-41">Оформить заказ</a>
                </div>
            </div>
            <div class="basket__widget">
                <div class="list">
                    <div class="item">
                        <?
                        if (!empty($arResult['ITEM']['PICTURE'])) {
                            ?>
                            <div class="image"><img src="<?= $arResult['ITEM']['PICTURE'][50]['SRC'] ?>" alt="<?= $arResult['NAME'] ?>"/></div>
                            <?
                        }
                        ?>
                        <div class="whole-row">
                            <div class="info-row">
                                <div class="info">
                                    <div class="name"><?= $arResult['ITEM']['NAME'] ?></div>
                                    <div class="data">
                                        <p class="counter-fallback hidden-gt-s">
                                            <span>Кол-во:</span>
                                            <del id="quantity"><?= $arResult['ITEM']['QUANTITY'] ?></del>
                                        </p>
                                        <p class="article"><span>Артикул:</span> <?= $arResult['ITEM']['PROPERTY_CML2_ARTICLE_VALUE'] ?></p>
                                        <p class="color"><span>Цвет:</span> <?= $arResult['ITEM']['COLOR']['NAME'] ?></p>
                                    </div>
                                </div>
                                <div class="price"><span class="hidden-l">Цена за шт.</span> <b><?= $arResult['ITEM']['PRICE'] ?>.–</b><span
                                        class="hidden-lte-m">/ шт.</span>
                                    <?
                                    if (!empty($arResult['ITEM']['PERCENT'])) {
                                        ?>
                                        <div class="sale__widget"><?= $arResult['ITEM']['PERCENT'] ?>%</div>
                                        <?
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="count">
                                <div data-min="1" data-measure=" шт." class="counter__widget">
                                    <a href="" data-add="-1" id="item-minus" data="<?= $arResult['ITEM']['ITEM_ID'] ?>">-</a>
                                    <div class="input">
                                        <input type="tel" name="good[1]" autocomplete="off" value="<?= $arResult['ITEM']['QUANTITY'] ?> шт."/>
                                    </div>
                                    <a href="" data-add="1" id="item-plus" data="<?= $arResult['ITEM']['ITEM_ID'] ?>">+</a>
                                </div>
                            </div>
                            <div class="remove">
                                <a href="" data="<?= $arResult['ITEM']['ITEM_ID'] ?>">
                                    <span class="icon__cross">
                                        <svg>
                                          <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cross"></use>
                                        </svg>
                                    </span>
                                </a>
                            </div>
                            <div class="total">
                                <p class="hidden-gt-s">Стоимость:</p>
                                <p><b id="total_price"><?= $arResult['ITEM']['TOTAL_PRICE'] ?>.–</b> <span>В наличии.</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?/* vk pixel temporary removed 01.11.18
<!-- VK Pixel Code -->
<?
$ids = '[';
if ($ids != '[') {
    $ids.=",";
}
$ids .= '{"id":"'.$arResult['ITEM']['ID'].'"}';
$ids.="]";
?> 
<script>
    window.vkAsyncInit = function () {
        VK.Retargeting.Init('VK-RTRG-204612-2Fkje');  // инициализация пикселя ВКонтакте
        var param={'products':<?= $ids ?>};
        console.log(<?= $arResult['ITEM']['ID'] ?>);
        VK.Retargeting.ProductEvent(<?= VK_PRICE_LIST_ID ?>, 'add_to_cart',param);
    }
</script>
<!-- VK Pixel Code -->
*/?>