<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$basket = Bitrix\Sale\Basket::loadItemsForFUser(Bitrix\Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
if(count($basket->getQuantityList()) != 0){
    global $USER;
    if (!$USER->IsAdmin()){
        extra_log(
            array(
                'entity_type' => 'basket',
                'entity_id' => CSaleBasket::GetBasketUserID(True),
                'exception_type' => 'order_empty',
                'exception_entity' => 'transition_to_an_empty_basket',
                "exception_text" => 'Пользователь с IP=' . $_SERVER['REMOTE_ADDR'] . ' в '.date('d.m.o H:i:s').' перешел на страницу пустой корзины имея товары!',
                "mail_comment" => 'Пользователь с IP=' . $_SERVER['REMOTE_ADDR'] . ' в '.date('d.m.o H:i:s').' перешел на страницу пустой корзины имея товары!',
            )
        );
    }
}
$APPLICATION->SetTitle("Пустая корзина");
Bitrix\Main\Page\Asset::getInstance()->addCss('/local/assets/css/dsklad-styles.css')?>



<div id="order_wrapper">
    <div class="test_input"><input type="hidden" value="0"></div>
    <div class="ds-wrapper">
        <div class="ds-basket-empty">
            <h1>Корзина пуста</h1>

            <?if ($USER->IsAuthorized()):?>
                <div class="ds-basket-empty__content">
                    <p>Посмотрите статус и состав своего заказа в <a href="/personal">Личном кабинете</a>.</p>
                </div>
                <div class="ds-basket-empty__content">
                    <p>Чтобы сделать еще один заказ, перейдите в каталог:</p>
                </div>
                <div class="ds-basket-empty__btn">
                    <a href="/catalog/" class="ds-btn ds-btn--default-big">Перейти в каталог</a>
                </div>
            <?else:;?>
                <div class="ds-basket-empty__content">
                    <p>Вы можете найти нужные товары в нашем каталоге:</p>
                </div>
                <div class="ds-basket-empty__btn">
                    <a href="/catalog/" class="ds-btn ds-btn--default-big">Перейти в каталог</a>
                </div>
            <?endif;?>

            <div class="retail-rocket-basket">
                <div data-retailrocket-markup-block="5d5ce8d597a52817280bcff2" data-stock-id="4"></div>
            </div>

        </div>
    </div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
