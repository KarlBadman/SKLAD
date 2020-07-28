<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<style>
    .link_2_catalog{
        margin-top:10px;
        background-color: #1769ff;
        color: #fff;
        text-align: center;
        text-decoration: none;
        width: 48%;
        display: block;
        float: left;
        height: 48px;
        line-height: 48px;
        border-radius: 3px;
    }

    .link_2_catalog:hover {
        color: #fff;
        background-color: #3b80ff;
    }
</style>
    <div class="success__page_custom">
        <div class="default">
            <div class="inner_success__page_custom">
                <h1>Ваша корзина пуста!</h1>

                <div class="success_order_custom">
                    <p>Вы можете найти нужные товары в нашем каталоге:</p>

                    <div class="inner_success_order_custom">
                            <a href="/catalog/" class="link_2_catalog">
                                <?/*
                <span class="icon__lock">
                    <svg>
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#card2"></use>
                    </svg>
                </span>
*/?>
                                <span class="label">Перейти в каталог</span>
                            </a>
                    </div>
                </div>


            </div>
        </div>
    </div>
