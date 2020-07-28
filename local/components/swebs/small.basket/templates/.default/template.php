<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);
?>
<a href="/basket/" data-count="<?= $arResult['QUANTITY'] ?>" class="cart active checkout_start">
    <span class="icon__cart">
        <svg>
            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cart"></use>
        </svg>
    </span>
    <span class="label">
        <?
        if ($arResult['QUANTITY'] > 0) {
            echo ' '.number_format($arResult['PRICE'], 0, '', ' ').'.– ';
        } else {
            echo 'Корзина';
        }
        ?>
    </span>
</a>
<?
if ($arResult['QUANTITY'] > 0) {
    ?>
    <div class="incart-wrap">
        <div class="incart">
            <h2>Корзина <sup><?= $arResult['QUANTITY'] ?></sup></h2>
            <table data-order-page="basket-fieldset">
                <?
                foreach ($arResult['ITEMS'] as $arItem) {
                    ?>

                    <tr class="incart__prod"
                        data-id="<?=$arItem["ID"]?>"
                        data-order-page="basket-item-field"
                        data-item-name="<?=$arItem['NAME']?>"
                        data-item-id="<?=$arItem['ID']?>"
                        data-item-price="<?=$arItem['PRICE']?>"
                    >
                        <td class="image">
                            <?
                            if (!empty($arItem['IMAGE']['SRC'])) {
                                ?>
                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/blank-loader.gif" data-lazysrc="<?= $arItem['IMAGE']['SRC'] ?>" alt="<?= $arItem['NAME'] ?>"  class="lazyload" />
                                <?
                            } else {
                                ?>
                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/no_photo.png" width="66" alt="<?= $arItem['NAME'] ?>">
                                <?
                            }
                            ?>
                        </td>
                        <td class="data">
                            <p class="title">
                                <a href="<?= $arItem['URL'] ?>"><?= $arItem['NAME'] ?></a>
                            </p>
                            <?if (empty($arItem['VID_KH_KA'])) : ?>
                                <p class="modifitation">
                                    <span class="label"> Цвет:</span>
                                    <span style="background: rgb(<?= $arItem['COLOR']['UF_RGB'] ?>)" class="color"></span>
                                    <span><?= $arItem['COLOR']['UF_NAME'] ?></span>
                                </p>
                            <?else : ?>
                                <p class="modifitation">
                                    <span class="label"> Вид:</span>
                                    <span><?= $arItem['VID_KH_KA'] ?></span>
                                </p>
                            <?endif?>
                        </td>
                        <td class="price">
                            <?= $arItem['PRICE'] ?>.–
                            <div class="smbas_del" data-ga-analys-btn="basket-delete-item">
                                <span class="icon__cross">
                                    <svg>
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/local/templates/dsklad/images/sprite.svg#cross"></use>
                                    </svg>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <?
                }
                ?>
            </table>

            <div class="order"><a href="/order/" class="button type-blue fill size-41 checkout_start">Оформить заказ</a></div>
        </div>
    </div>
    <?
}
?>
