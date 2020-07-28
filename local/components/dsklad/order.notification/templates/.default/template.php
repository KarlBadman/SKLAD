<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!empty($arResult['ORDER_INFO'])) {
    $dateOrderInTimeStamp = MakeTimeStamp($arResult['ORDER_INFO']['DATE_INSERT']);
    $orderLink = '/personal/order/' . $arResult['ORDER_INFO']['ID'] .'/';
    $orderPaymentLink = '/order/thankyou/' . $arResult['ORDER_INFO']['ID'] .'/';
    ?>
    <div class="order-notification" data-notice-id="<?=$arResult['ORDER_INFO']['ID'];?>">
        <div class="ds-wrapper order-notification__content">
            <p>Ваш заказ&nbsp;<a href="<?=$orderLink?>">№<?=$arResult['ORDER_INFO']['ID'];?></a> от <?=FormatDate("d F", $dateOrderInTimeStamp);?> ожидает оплаты.</p><a class="ds-btn ds-btn--success" href="<?=$orderPaymentLink?>">Оплатить</a><span class="order-notification-close js-order-notification-close"></span>
        </div>
    </div>
<? } ?>