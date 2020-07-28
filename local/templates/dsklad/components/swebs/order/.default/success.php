<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

// Если пользователь обновил страницу, то у него уже нет
if (!isset($_SESSION['ORDER_ID']) || empty($_SESSION['ORDER_ID'])) {
global $USER;
    ?>
    <div class="success__page_custom">
        <div class="default">
            <div class="inner_success__page_custom">
                <h1>Ваша корзина пуста!</h1>
                <div class="footer_success_order_custom">
                    <p class="footer_success_order_custom_2">
                        Отследить статус и посмотреть информацию о ранее созданных заказах можно в личном кабинете<? if($USER->getId()){?>, в разделе <a href="/personal/">История заказов</a><?} else {?> после <a href="/login/">авторизации</a><?}?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?
} else {
    //Criteo
    function detali_zakaza()
    {
        global $DB;
        $trackTransaction = '';
        $pribavka = 0;
        $spis_id_google = '';
        $idzakaza = $_SESSION['ORDER_ID'];
        $summa_googletag = 0;
        $cart = $DB->Query("SELECT * FROM b_sale_basket WHERE ORDER_ID = '$idzakaza' ", true);
        $itemsToMetrics = array();
        while($row = $cart->Fetch()){
            $BASE_PRICE = number_format($row['BASE_PRICE'], 0, '', '');
            $QUANTITY = number_format($row['QUANTITY'], 0, '', '');
            $itemsToMetrics[] = $row['PRODUCT_ID'];

            $trackTransaction .=
            '
            { id: "'.$row['PRODUCT_ID'].'", price: '.$BASE_PRICE.', quantity: '.$QUANTITY.' },
            ';

            $pribavka += 1;
            $zpt_google = ", ";
            if($pribavka > count($row) - 1){
                $zpt_google = "";
            }
            $spis_id_google .= $row['PRODUCT_ID'].$zpt_google;

            $summa_googletag += $BASE_PRICE * $QUANTITY;

            //yandex commerce
            $sboryandex['idtovara'] = $row['PRODUCT_ID'];
            $sboryandex['name'] = dannie_tovara($row['PRODUCT_ID'])[0];
            $sboryandex['price'] = $BASE_PRICE;
            $sboryandex['quantity'] = $QUANTITY;

            $mass_yandex[] = $sboryandex;
            //END yandex commerce
        }

        $vivfunc[0] = $trackTransaction;
        $vivfunc[1] = $spis_id_google;
        $vivfunc[2] = $summa_googletag;
        $vivfunc[3] = $mass_yandex;
        $vivfunc[4] = json_encode($itemsToMetrics);

        return $vivfunc;
    }

    function dannie_tovara($idtovara)
    {
        global $DB;
        $cart = $DB->Query("SELECT * FROM b_iblock_element WHERE ID = '$idtovara' ", true);
        $row = $cart->Fetch();

        $vivfunc[0] = $row['NAME'];

        return $vivfunc;
    }
    ?>

	<script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async></script>
    <script type="text/javascript">
        window.criteo_q = window.criteo_q || [];
        var deviceType = /iPad/.test(navigator.userAgent) ? 't' : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? 'm' : 'd';
        window.criteo_q.push(
            { event: 'setAccount', account: 44817 },
            { event: 'setEmail', email: '<?= $_SESSION['SESS_AUTH']['EMAIL'] ?>' },
            { event: 'setSiteType', type: deviceType },
			{ event: 'trackTransaction', id: '<?= $_SESSION['ORDER_ID'] ?>', item: [
				<?= detali_zakaza()[0] ?>
			]}
		);
		//END criteo
    </script>

    <? /* mail counter temporary removed 01.11.18
    $_SESSION['TARGET']['productid'] = detali_zakaza()[4];
    $_SESSION['TARGET']['totalvalue'] = detali_zakaza()[2];
    $APPLICATION->IncludeFile('/include_areas/mail_counter.php');
    */?>

    <script type="text/javascript">
        //google ecomm vit
        // var google_tag_params = {
        //     ecomm_prodid: [<?= detali_zakaza()[1] ?>],
        //     ecomm_pagetype: 'purchase',
        //     ecomm_totalvalue: <?= detali_zakaza()[2] ?>
        // };
        //END google ecomm vit
    </script>

    <script type="text/javascript">
        //yandex commerce
        // window.dataYandex = window.dataYandex || [];
        // dataYandex.push({
        //     'ecommerce': {
        //         'purchase': {
        //             'actionField': {
        //                 'id' : '<?= $_SESSION['ORDER_ID'] ?>'
        //             },
        //             'products': [
        //                 <?
        //                 foreach (detali_zakaza()[3] as $mass_yand) {
        //                     ?>
        //                 {
        //                     'id': '<?= $mass_yand['idtovara'] ?>',
        //                     'name': '<?= $mass_yand['name'] ?>',
        //                     'price': <?= $mass_yand['price'] ?>,
        //                     'quantity': <?= $mass_yand['quantity'] ?>
        //                 },
        //                 <?
        //             }
        //             ?>
        //             ]
        //         }
        //     }
        // });
        //END yandex commerce
    </script>

    <script>
        fbq('track', 'Purchase', {
            value: <?echo detali_zakaza()[2]?>,
            currency: 'RUB',
        });
    </script>

    <div class="success__page_custom" data-page-type="order-thanx">
        <div class="default">
            <div class="inner_success__page_custom">
                <h1>Заказ успешно оформлен!</h1>
                <div class="success_order_custom" data-order-page="thanx-fieldset">
                    <p>№ вашего заказа:</p>
                    <div class="inner_success_order_custom">
                        <span class="number_success_order_custom" data-order-page="order-id-field" data-order-revenue="<?=detali_zakaza()[2];?>" data-order-products="<?=htmlentities(json_encode(detali_zakaza()[3]), ENT_QUOTES, 'UTF-8');?>"><?= $_SESSION['ORDER_ID'] ?></span>
                        <?
                        if ($_SESSION['ORDER_PAY_SYSTEM_ID'][0] == 2) {
                            ?>
                            <a href="#" class="link_success_order_custom">
                                <span class="icon__lock">
                                    <svg>
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#card2"></use>
                                    </svg>
                                </span>
                                <span class="label">Перейти к оплате</span>
                            </a>
                            <?
                        }
                        ?>
                    </div>
                </div>
                <div class="desc_success_order_custom">
                    <?
                    if ($_SESSION['ORDER_PAY_SYSTEM_ID'][0] == 4) {
                        ?>
                        <p>Для выставления счета на оплату, пришлите нам Ваши реквизиты и номер заказа на почту info@dsklad.ru</p>
                        <br/>
                        <p>Наш контактный телефон: <a href="tel:+78007771274">8 (800) 777-12-74</a>.</p>
                        <p class="bot_tit_desc_success">С уважением,<br/> Служба поддержки <a href="/">Dsklad.ru</a></p>
                        <?
                    } else {
                        ?>
                        <p class="tit_title_success">Здравствуйте!</p>
                        <p>Ваш заказ принят в обработку. Скоро мы свяжемся с вами для подтверждения заказа.</p>
                        <p>Если по каким-то причинам мы никак не подтвердили ваш заказ в течение 24 часов, пожалуйста, позвоните по номеру <a href="tel:+78007771274">8 (800) 777-12-74</a>.</p>
                        <p class="bot_tit_desc_success">С уважением,<br/> Служба поддержки <a href="/">Dsklad.ru</a></p>
                        <?
                    }
                    ?>
                </div>
                <div class="footer_success_order_custom">
                    <p class="footer_success_order_custom_1">
                        <a href="/personal/<?= (!empty($_SESSION['ORDER_ID'])) ? 'order/'.$_SESSION['ORDER_ID'].'/' : '' ?>"><span>Отслеживайте заказ</span></a>
                    </p>
                    <p class="footer_success_order_custom_2">
                        Отследить статус и посмотреть информацию по заказу можно в личном кабинете, в разделе <a href="/personal/">История заказов</a>
                    </p>
                </div>
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
    </div>
    <script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async defer></script>
    <script>
        window.renderOptIn = function() {
            window.gapi.load('surveyoptin', function() {
                window.gapi.surveyoptin.render(
                    {
                        // REQUIRED FIELDS
                        "merchant_id": 107465033,
                        "order_id": "<?= $_SESSION['ORDER_ID'] ?>",
                        "email": "<?= $_SESSION['ORDER_EMAIL'] ?>",
                        "delivery_country": "RU",
                        "estimated_delivery_date": "<?=$_SESSION['DELIVERY_DATE'];?>" //предполагаемая дата доставки
                    });
            });
        }
    </script>
    <!-- НАЧАЛО кода языка опроса -->
    <script>
        window.___gcfg = {
            lang: 'ru'
        };
    </script>
    <!-- КОНЕЦ кода языка опроса -->
    <?
    unset($_SESSION['ORDER_ID']);
    unset($_SESSION['ORDER_EMAIL']);
    unset($_SESSION['DELIVERY_DATE']);
}
?>
