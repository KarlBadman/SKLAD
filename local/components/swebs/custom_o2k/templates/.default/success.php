<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<div class="success__page_custom">
    <div class="default">
        <div class="inner_success__page_custom">
            <h1>Заказ успешно оформлен!</h1>

            <div class="success_order_custom">
                <p>№ вашего заказа:</p>

                <div class="inner_success_order_custom">
                    <span class="number_success_order_custom"><?= $_SESSION['ORDER_ID'] ?></span>

                    <?if($_SESSION['ORDER_PAY_SYSTEM_ID'][0]==2):?>
                    <a href="#" class="link_success_order_custom">
                <span class="icon__lock">
                    <svg>
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#card2"></use>
                    </svg>
                </span>
                        <span class="label">Перейти к оплате</span>
                    </a>
                    <?endif;?>
                </div>
            </div>
            <div class="desc_success_order_custom">
<?if($_SESSION['ORDER_PAY_SYSTEM_ID'][0]==4):?>
    <p >Для выставления счета на оплату, пришлите нам Ваши реквизиты и номер заказа на почту info@dsklad.ru </p>
</br>
    <p>Наш контактный телефон:
        <a href="tel:+78003332795">8 (800) 333-27-95</a>.</p>

    <p class="bot_tit_desc_success">С уважением,<br/>
        Служба поддержки <a href="/">Dsklad.ru</a></p>

<?else:?>
                <p class="tit_tit_desc_success">Ожидайте звонка</p>

                <p>В ближайшее время мы свяжемся с Вами для подтверждения заказа.</p>

                <p>Если наши операторы не связались с вами в течении суток, просим Вас перезвонить нам по телефону
                    <a href="tel:+78003332795">8 (800) 333-27-95</a>.</p>

                <p class="bot_tit_desc_success">С уважением,<br/>
                    Служба поддержки <a href="/">Dsklad.ru</a></p>
<?endif;?>
            </div>
            <div class="footer_success_order_custom">
                <p class="footer_success_order_custom_1">
                    <span>Отслеживайте заказ</span>
                </p>

                <p class="footer_success_order_custom_2">
                    Отследить статус и посмотреть информацию по заказу можно в личном кабинете, в разделе <a
                        href="/personal/">История заказов</a>
                </p>
            </div>
            <div class="hidden_payment_success_order">
                <? $APPLICATION->IncludeComponent(
                    "swebs:sale.order.payment",
                    "",
                    Array()
                ); ?>
            </div>
        </div>
    </div>
</div>
<?
    unset($_SESSION['ORDER_ID']);
    unset($_SESSION['ORDER_DATA']);
?>