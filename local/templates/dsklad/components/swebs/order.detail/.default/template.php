<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Page\Asset;

$this->setFrameMode(true);

Asset::getInstance()->addString('<style>#status_b:before {background-color: '.$arResult['ORDER']['STATUS']['COLOR'].';}</style>');
?>
<div class="cabinet__page detail_cabinet_page">
    <div class="ds-wrapper default">
        <section class="heading">
            <ul itemscope itemtype="http://schema.org/BreadcrumbList" class="breadcrumbs__widget">
                <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <a itemprop="item" href="https://example.com/dresses">
                        <span itemprop="name">Главная</span>
                    </a>
                    <meta itemprop="position" content="1" />
                </li>
                <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <span itemprop="name">Личный кабинет</span>
                    <meta itemprop="position" content="2" />
                </li>
            </ul>
            <div class="title">
                <h1>Заказ № <?= $arResult['ORDER']['ID'] ?></h1>
                <? if ($arResult['ORDER']['PAY_SYSTEM_BUTTON'] && $arResult['ORDER']['STATUS']['NAME'] != 'Отменен' && !in_array($arResult['ORDER']['PAY_SYSTEM_ID'], array(3, 4, 14, 16))) { ?>
                    <a href="/order/thankyou/<?= $arResult['ORDER']['ID'] ?>" class="button type-blue size-41 fill btn_send_item_order" id="pay_but">
                        Оплатить заказ
                    </a>
                <? } ?>
            </div>
        </section>
    </div>
    <section class="data">
        <div class="tabs__widget">
            <div class="tabs-handler">
                <ul class="ds-wrapper default">
                    <li class="active">
                        <a href="/personal/">
                            <span class="hidden-s">История заказов</span>
                            <span class="hidden-gt-s">Заказы</span>
                        </a>
                    </li>
                    <li>
                        <a href="/personal/?tab_like">
                            <span class="hidden-s">Избранные товары</span>
                            <span class="hidden-gt-s">Избранное</span>
                        </a>
                    </li>
                    <li>
                        <a href="/personal/?tab_settings">
                            <span class="hidden-s">Личные настройки</span>
                            <span class="hidden-gt-s">Настройки</span>
                        </a>
                    </li>
                    <li class="link_logout_personal hidden-lte-m right">
                        <a href="/personal/">Вернуться к истории заказов</a>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <div class="ds-wrapper default">
        <section class="info">
            <h2>Общая информация</h2>
            <div class="table">
                <div class="id"><a href="">Заказ № <?= $arResult['ORDER']['ID'] ?></a></div>
                <div class="process">
                    <div class="status" id="status_b" style="color: <?= $arResult['ORDER']['STATUS']['COLOR'] ?>;">
                        <br class="hidden-gt-s"><?= $arResult['ORDER']['STATUS']['NAME'] ?>
                    </div>
                    <div class="shipping">
                        <p><?= $arResult['ORDER']['DELIVERY'] ?></p>
                        <?
                        \Bitrix\Main\Loader::includeModule('sale');
                        $order = \Bitrix\Sale\Order::load($arResult['ORDER']['ID']);

                        if ($arResult['ORDER']['STATUS']['NAME'] != \Dsklad\Config::getParam('order/finished_status')) {

                            $shipmentCollection = $order->getShipmentCollection();
                            foreach ($shipmentCollection as $shipment) {
                                if ($shipment->isSystem()) {
                                    continue;
                                }
                                $track = $shipment->getField('TRACKING_NUMBER');
                            }
                            ?>
                            <p>
                                <?
                                if ($arResult['ORDER']['DELIVERY_ID'][0] == 7) {
                                    echo $track;
                                } elseif(!empty($track)) {
                                    try {
                                        $trackingLink = \Dsklad\Config::getParam('delivery/' . $arResult['ORDER']['DELIVERY_ID'][0])['tracking_link'];
                                        $trackingLink = str_replace('#TRACK_NUMBER#', $track, $trackingLink);
                                    } catch (Exception $e) {
                                        $trackingLink = '#';
                                    } ?>
                                    <?if($trackingLink != '#'):?>
                                        <a href="<?= $trackingLink ?>" target="_blank" rel="noopener"><?= $track ?></a>
                                    <?else:?>
                                        <?=$track;?>
                                    <?endif;?>
                                <?
                                }
                                ?>
                            </p>
                        <? } ?>
                    </div>
                </div>
                <div class="delivery">
                    <table>
                        <?
                        if (!empty($arResult['ORDER']['ADDRESS'])) {
                            ?>
                            <tr>
                                <th>Адрес:</th>
                                <td><?= $arResult['ORDER']['ADDRESS'] ?></td>
                            </tr>
                            <?
                        }
                        ?>
                        <tr>
                            <th>Дополнительно:</th>
                            <td>
                                <?
                                if (!empty($arResult['ORDER']['ADDRESS_COMMENT'])) {
                                    ?>
                                    <div>
                                        <?= $arResult['ORDER']['ADDRESS_COMMENT'] ?> <br>
                                    </div>
                                    <?
                                }
                                
                                if (isset($arResult['ORDER']['DOP_SERVICES']) && !empty($arResult['ORDER']['DOP_SERVICES'])) {
                                    foreach ($arResult['ORDER']['DOP_SERVICES'] as $dop) {
                                        ?>
                                        <label class="label__widget">
                                            <span class="row">
                                                <span class="control">
                                                    <input type="checkbox"/>
                                                    <u class="square">
                                                        <span class="icon__check">
                                                            <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use></svg>
                                                        </span>
                                                    </u>
                                                </span>
                                                <span class="label"><?=$dop?></span>
                                            </span>
                                        </label>
                                        <?
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="paid">
                    <span class="icon__<?= $arResult['ORDER']['PAY_SYSTEM_ICON'] ?>">
                        <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#<?= $arResult['ORDER']['PAY_SYSTEM_ICON'] ?>"></use></svg>
                    </span>
                    <p>
                        <?
                        if ($arResult['ORDER']['PAY_SYSTEM_STATUS']) {
                            ?>
                            Оплачено
                            <?
                        } else {
                            ?>
                            Не оплачено
                            <?
                        }
                        ?>
                        <br><?= $arResult['ORDER']['PAY_SYSTEM'] ?>
                    </p>
                </div>
            </div>
        </section>
        <section class="goods">
            <h2>Товары в заказе</h2>
            <div class="basket__widget">
                <div class="list">
                    <?
                    foreach ($arResult['ORDER']['BASKET'] as $arItem) {
                        ?>
                        <div class="item">
                            <div class="image">
                                <?
                                if (!empty($arItem['IMAGE'])) {
                                    ?>
                                    <img src="<?= $arItem['IMAGE'] ?>" alt="<?= $arItem['NAME'] ?>">
                                    <?
                                } else {
                                    ?>
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/images/no_photo.png" alt="<?= $arItem['NAME'] ?>">
                                    <?
                                }
                                ?>
                            </div>
                            <div class="whole-row">
                                <div class="info-row">
                                    <div class="info">
                                        <div class="category"><a href="<?= $arItem['SECTION_URL'] ?>"><?= $arItem['SECTION_NAME'] ?></a></div>
                                        <div class="name"><a href="<?= $arItem['NAME_URL'] ?>"><?= $arItem['NAME'] ?></a></div>
                                        <div class="data">
                                            <p class="counter-fallback hidden-gt-s"><span>Кол-во:</span>
                                                <del><?= $arItem['QUANTITY'] ?></del>
                                            </p>
                                            <p class="article"><span>Артикул:</span> <?= $arItem['ARTICLE'] ?></p>
                                            <p class="color"><span>Цвет:</span> <?= $arItem['COLOR'] ?></p>
                                        </div>
                                    </div>
                                    <div class="price">
                                        <span class="hidden-l">Цена за шт.</span>
                                        <span class="ds-price"><?= $arItem['PRICE'] ?></span>
                                        <span class="hidden-lte-m">/ шт.</span>
                                        <?
                                        if ($arItem['PERCENT'] > 0) {
                                            ?>
                                            <div class="sale__widget"><?= $arItem['PERCENT'] ?>%</div>
                                            <?
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="count">
                                    <p><?= $arItem['QUANTITY'] ?> шт.</p>
                                </div>
                                <div class="total">
                                    <p class="hidden-gt-s">Стоимость:</p>
                                    <p><span class="ds-price"><?= $arItem['FINAL_PRICE'] ?></span></p>
                                </div>
                            </div>
                        </div>
                        <?
                    }
                    ?>
                </div>
                <?
                if (!empty($arResult['ORDER']['SERVICES'])) {
                    ?>
                    <div class="services">
                        <?
                        foreach ($arResult['ORDER']['SERVICES'] as $arItem) {
                            ?>
                            <div class="label">
                                <span class="icon">
                                    <span class="icon__<?= $arItem['CODE'] ?>">
                                        <img src="<?= $arItem['SVG_SPRITE'] ?><?= !empty($arItem['SVG_ANCHOR']) ? '#'.$arItem['SVG_ANCHOR'] : '' ?>" alt=""/>
                                    </span>
                                </span>
                                <span class="price"><span class="hidden-gt-s">Стоимость: </span><b class="ds-price"><?= $arItem['PRICE'] ?></b></span>
                                <span class="title">
                                    <?= $arItem['TEXT'] ?>
                                </span>
                            </div>
                            <?
                        }
                        ?>
                    </div>
                    <?
                }
                ?>
            </div>
            <div class="order-data">
                <table class="calculation">
                    <tr>
                        <th>Итоговая стоимость:</th>
                        <th><span class="ds-price"><?= $arResult['ORDER']['TOTAL_SUMM'] ?></span></th>
                    </tr>
                    <tr>
                        <td>Стоимость:</td>
                        <td><span class="ds-price"><?= $arResult['ORDER']['TOTAL_PRICE'] ?></span></td>
                    </tr>
                    <tr>
                        <td>Доставка:</td>
                        <td><span class="ds-price"><?= $arResult['ORDER']['DELIVERY_PRICE'] ?></span></td>
                    </tr>
                    <tr>
                        <td>Общая скидка:</td>
                        <td><span class="ds-price"><?= $arResult['ORDER']['DISCOUNT_PRICE'] ?></span></td>
                    </tr>
                </table>
            </div>
        </section>
        
        <div class="hidden_payment_success_order">
            <?
            $APPLICATION->IncludeComponent(
                'swebs:sale.order.payment',
                '',
                Array()
            );
            ?>
        </div>
    </div>
</div>