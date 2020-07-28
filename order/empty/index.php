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
$APPLICATION->SetTitle("Пустая корзина");?>

<div id="order_wrapper">
    <div class="test_input"><input type="hidden" value="0"></div>
    <div class="success__page_custom">
        <div class="default">
            <div class="ds-basket-empty">
                <h1>Корзина пуста</h1>
                <p>Вы можете найти нужные товары в нашем каталоге:</p>
                <a href="/catalog/" class="ds-btn ds-btn--default-big">Перейти в каталог</a>
            </div>
        </div>
    </div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
