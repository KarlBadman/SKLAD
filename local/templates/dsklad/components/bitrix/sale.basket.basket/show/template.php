<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<? if($arResult['BASKET_EMPTY'] == true) header("Location:/basket/empty/");?>
    <div class="ds-basket__goods-info">
        <div class="ds-basket__order-btn-top ya-to-order"><a class="ds-btn ds-btn--success" href="/order/">К оформлению</a></div>
        <div class="ds-basket__goods-list" data-block-name="item" data-order-page="basket-fieldset">
            <div class="spinner-target">
                <?foreach ($arResult['ITEMS']['AnDelCanBuy'] as $item):?>
                    <div class="ds-basket-good" data-order-page="basket-item-field" data-item-name="<?=$item['NAME']?>" data-item-id="<?=$item['PRODUCT_ID']?>" data-item-price="<?=number_format($item['FULL_PRICE'],0,'.','')?>">
                        <div class="ds-basket-good__img"><a href="<?=$item['DETAIL_PAGE_URL']?>"><img src="<?=$item['PHOTO_PRODUCT']?>" alt=""></a></div>
                        <div class="ds-basket-good__price-one"><span class="ds-price ds-price--one"><?=number_format($item['FULL_PRICE'],0,'.',' ')?></span></div>
                        <div class="ds-basket-good__count">
                            <div class="ds-basket-count">
                                <button class="icon-svg ic-count-minus js-count-minus"></button>
                                <input class="inp-count js-number"
                                       type="number"
                                       min="1"
                                       name="quantity"
                                       data-gaproduct-quantity="<?=$item['QUANTITY']?>"
                                       value="<?=$item['QUANTITY']?>"
                                       data-productID="<?=$item['ID'];?>"
                                >
                                <button class="icon-svg ic-count-plus js-count-plus"></button>
                            </div>
                        </div>
                        <div class="ds-basket-good__price">
                            <span class="ds-price"><?=number_format($item['SUM_VALUE'],0,'.',' ')?></span>
                            <? if ($item['OLD_PRICE'] > $item['SUM_VALUE']): ?>
                                <span class="ds-price ds-price--old"><?=number_format($item['OLD_PRICE'],0,'.',' ')?></span>
                            <?endif?>
                        </div>

                        <div class="ds-basket-good__add-info">
                            <?if($item['QUANTITY_BEFORE_DISCOUNT'] > 0):?>
                                <div class="ds-basket-add-more fadein"><span>Добавьте еще <?=$item['QUANTITY_BEFORE_DISCOUNT']?> шт. и получите скидку <span class="ds-price"> <?=number_format($item['DIOPOSE_DISCOUNT_PRICE'],0,'.',' ')?></span> </span></div>
                            <?endif;?>
                        </div>

                        <div class="ds-basket-good__descr"><a href="<?=$item['DETAIL_PAGE_URL']?>"><?=$item['NAME']?></a></div>
                        <div class="ds-basket-good__service-info">
                            <?if($item['AVAILABLE_QUANTITY'] <= 0):?>
                                <div class="ds-service-info quantity-order">Предзаказ — предоплата 50%</div>
                            <?endif;?>
                            <?if($arResult['SERVICE_OK'] == 'Y' && !$item['NO_WARANTY']):?>
                                <div class="ds-service-info warranty-status warranty-full">Расширенная гарантия</div>
                            <?else:?>
                                <div class="ds-service-info warranty-status">Стандартная гарантия</div>
                            <?endif?>
                            <?if(!empty($item['PROPERTY_CML2_ARTICLE_VALUE'])):?>
                                <div class="ds-service-info">Арт.:&nbsp;<span> <?=$item['PROPERTY_CML2_ARTICLE_VALUE']?></span></div>
                            <?endif;?>
                            <?if(!empty($item['PROPERTY_KOD_TSVETA_VALUE'])):?>
                                <div class="ds-service-info">Цвет:&nbsp;<span> <?=explode('#',$item['PROPERTY_KOD_TSVETA_VALUE'])[0]?></span></div>
                            <?endif;?>
                        </div>
                        <div class="ds-basket-good__del" data-ga-analys-btn="basket-delete-item" data-item-name="{{NAME}}" data-item-id="{{PRODUCT_ID}}" data-item-price="{{NF FULL_PRICE}}">
                            <div class="ds-basket-del js-basket-remove" data-productId="<?=$item['ID']?>">
                                <div class="icon-svg ic-basket-remove"></div><span>Удалить</span>
                            </div>
                        </div>
                    </div>
                <?endforeach;?>
            </div>

            <? if ($arResult['PREORDER']): ?>
                <div class="ds-basket__preorder-info">
                    <p>В&nbsp;вашем заказе есть товары со&nbsp;статусом предзаказ. Эти товары появятся на&nbsp;складе в&nbsp;ближайшее
                        время, оформляя на&nbsp;них заказ, вы&nbsp;бронируете товар и&nbsp;оплачиваете&nbsp;50% от&nbsp;их&nbsp;стоимости.
                        Остальные&nbsp;50% можно внести, когда товары появятся на&nbsp;складе.</p>
                </div>
            <? endif; ?>
        </div>
        <?if(!empty($arResult['BASKET_SERVICE'])):?>
            <?foreach ($arResult['BASKET_SERVICE'] as $service):?>
                <div class="ds-basket__warranty">
                    <div class="warranty-sm">
                        <div class="warranty-sm__info">
                            <?//=$service['PREVIEW_TEXT']?>
                            <h5>Расширенная гарантия на 24 месяца</h5>
                            <p>Бесплатный обмен или возврат товара надлежащего качества в течение 14 дней.</p>
                            <p>Бесплатная замена товара в течение 14 дней при обнаружении любых повреждений.</p>
                            <p class="small-text"><em>Стандартная гарантия&nbsp;&mdash; 18&nbsp;месяцев.</em></p>
                        </div>
                        <div class="warranty-sm__img"><img src="<?=$templateFolder?>/images/svg/warranty-gray.svg" alt=""></div>
                        <?if($service['CHECkED'] != 'Y'):?>
                            <button class="ds-btn ds-btn--light js-add-warranty" data-serviceId="<?=$service['ID']?>">Добавить гарантию<span class="ds-price"><?=number_format($service['PRICE'],0,'.',' ')?></span></button>
                        <?else:;?>
                            <button class="ds-btn ds-btn--light js-add-warranty added" data-serviceId="<?=$service['ID']?>">Отменить гарантию</button>
                        <?endif;?>
                    </div>
                </div>
            <?endforeach?>
        <?endif;?>
    </div>
    <div class="ds-basket__total-price" data-block-name="total">
        <div class="total-price">
            <div class="total-price__content">
                <div class="total-price__row">
                    <div class="total-price__col">Товары</div>
                    <div class="total-price__order-detail"><span class="small-text"> <?=$arResult['PRODUCT_QUANTITY'];?> шт</span></div>
                    <div class="total-price__col"><span class="ds-price"><?=number_format($arResult['OLD_TOTAL_PRICE'],0,'.',' ')?></span></div>
                </div>
                <div class="total-price__row">
                    <div class="total-price__col">Скидка</div>
                    <div class="total-price__col"><span class="ds-price"><?=number_format($arResult['TOTAL_SUM_DISCOUNT'],0,'.',' ')?></span></div>
                </div>
                <?if(!empty($arResult['BASKET_SERVICE'])):?>
                    <?foreach ($arResult['BASKET_SERVICE'] as $service):?>
                        <?if($service['CHECkED'] == 'Y'):?>
                            <div class="total-price__row js-warranty-total">
                                <div class="total-price__col">Гарантия</div>
                                <div class="total-price__order-detail"><span class="small-text"> <?=$arResult['PRODUCT_QUANTITY'];?> шт</span></div>
                                <div class="total-price__col"><span class="ds-price"><?=number_format($service['PRICE'],0,'.',' ')?></span></div>
                            </div>
                        <?endif;?>
                    <?endforeach?>
                <?endif;?>
                <div class="total-price__row total-price__row--summ">
                    <div class="total-price__col">Итого без доставки</div>
                    <div class="total-price__col"><span class="ds-price ds-price--total" data-basket-revenue="<?=$arResult['allSum']?>"><?=number_format($arResult['allSum'],0,'.',' ')?></span>
                    </div>
                </div>
                <div class="total-price__buttons">
                    <a class="ds-btn ds-btn--success ya-to-order" href="/order/">К оформлению</a>
                    <button class="ds-btn ds-btn--light ds-btn--full js-ds-modal ya-one-click" data-href="<?=$templateFolder?>/templates/incl-quick-order.html" data-ds-modal-width="412" data-ga-analys-btn="order-one-click-submit">Оформить в 1 клик</button>
                </div>
            </div>
            <div class="total-price__add-info"><span class="small-text">Доступные способы и время доставки можно выбрать при оформлении заказа.</span></div>
        </div>
    </div>
    <div class="retail-rocket-order">
        <div data-retailrocket-markup-block="5d5ce8cc97a52817280bcff1" data-products="<?=implode(',',$arResult['AR_PRODUCT_ID'])?>" data-stock-id="4"></div>
    </div>



