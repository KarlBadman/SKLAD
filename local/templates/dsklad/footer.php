</main>

<footer class="border">
    <div class="ds-wrapper">
        <div class="footer-about">
            <div class="footer-about__links"><a href="/"><img src="<?=SITE_TEMPLATE_PATH?>/images/logo-footer.svg" alt=""></a>
                <?$APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "top_footer",
                    Array(
                        "ALLOW_MULTI_SELECT" => "Y",
                        "CHILD_MENU_TYPE" => "top_footer",
                        "COMPOSITE_FRAME_MODE" => "A",
                        "COMPOSITE_FRAME_TYPE" => "AUTO",
                        "DELAY" => "N",
                        "MAX_LEVEL" => "1",
                        "MENU_CACHE_GET_VARS" => array(""),
                        "MENU_CACHE_TIME" => "3600",
                        "MENU_CACHE_TYPE" => "A",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "ROOT_MENU_TYPE" => "top_footer",
                        "USE_EXT" => "Y",
                    )
                );?>
            </div>
            <div class="footer-about__phone">
                <h2>
                    <span class="tel">
                    <?$APPLICATION->IncludeFile(
                        SITE_TEMPLATE_PATH.'/include_areas/phone.php',
                        array(),
                        array(
                            'MODE' => 'text'
                        )
                    );?>
                    </span>
                </h2>
                <p>Ежедневно с 9 до 21</p>
            </div>
        </div>
    </div>
    <div class="footer-info">
        <div class="ds-wrapper-footer">
            <div class="footer-info__content">
                <div class="footer-menu js-footer-menu">
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:menu",
                        "top_mini",
                        Array(
                            "ALLOW_MULTI_SELECT" => "Y",
                            "CHILD_MENU_TYPE" => "footer_order",
                            "COMPOSITE_FRAME_MODE" => "A",
                            "COMPOSITE_FRAME_TYPE" => "AUTO",
                            "DELAY" => "N",
                            "MAX_LEVEL" => "1",
                            "MENU_CACHE_GET_VARS" => array(""),
                            "MENU_CACHE_TIME" => "3600",
                            "MENU_CACHE_TYPE" => "A",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "ROOT_MENU_TYPE" => "footer_order",
                            "USE_EXT" => "Y",
                            "TITLE_MENU"=>"Заказы"
                        )
                    );?>
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:menu",
                        "top_mini",
                        Array(
                            "ALLOW_MULTI_SELECT" => "Y",
                            "CHILD_MENU_TYPE" => "footer_profile",
                            "COMPOSITE_FRAME_MODE" => "A",
                            "COMPOSITE_FRAME_TYPE" => "AUTO",
                            "DELAY" => "N",
                            "MAX_LEVEL" => "1",
                            "MENU_CACHE_GET_VARS" => array(""),
                            "MENU_CACHE_TIME" => "3600",
                            "MENU_CACHE_TYPE" => "A",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "ROOT_MENU_TYPE" => "footer_profile",
                            "USE_EXT" => "Y",
                            "TITLE_MENU"=>"Профиль"
                        )
                    );?>
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:menu",
                        "top_mini",
                        Array(
                            "ALLOW_MULTI_SELECT" => "Y",
                            "CHILD_MENU_TYPE" => "footer_shop",
                            "COMPOSITE_FRAME_MODE" => "A",
                            "COMPOSITE_FRAME_TYPE" => "AUTO",
                            "DELAY" => "N",
                            "MAX_LEVEL" => "1",
                            "MENU_CACHE_GET_VARS" => array(""),
                            "MENU_CACHE_TIME" => "3600",
                            "MENU_CACHE_TYPE" => "A",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "ROOT_MENU_TYPE" => "footer_shop",
                            "USE_EXT" => "Y",
                            "TITLE_MENU"=>"Магазин"
                        )
                    );?>
                </div>
                <div class="footer-social">
                    <h5>Социальные сети</h5>
                    <?$APPLICATION->IncludeFile(
                        SITE_TEMPLATE_PATH . '/include_areas/social_icon.php',
                        array(),
                        array(
                            'MODE' => 'php'
                        )
                    );?>
                </div>
                <div class="footer-payment">
                    <h5>Способы оплаты</h5>
                    <div class="footer-payment__list"><span class="icon-svg ic-mastercard"></span><span class="icon-svg ic-visa"></span><span class="icon-svg ic-mir"></span></div>
                    <p>Оплатите покупки наличными при получении, либо выберите другой <a href="/delivery/?show=payment">способ оплаты</a></p>
                </div>
            </div>
        </div>
        <div class="border">
            <div class="ds-wrapper-footer">
                <div class="footer-copyright-info">
                    <div class="footer-copyright-info__header">
                        <p>Интернет-магазин Dsklad.ru<a href="/files_cookie/"> использует файлы &laquo;cookie&raquo;</a>, с&nbsp;целью повышения удобства пользования веб-сайтом.</p>
                    </div>
                    <div class="footer-copyright-info__content">
                        <p>Все ресурсы сайта www.dsklad.ru, включая текстовую, графическую и&nbsp;видео информацию, структуру и&nbsp;оформление страниц, защищены российскими и&nbsp;международными законами и&nbsp;соглашениями об&nbsp;охране авторских прав и&nbsp;интеллектуальной собственности (статьи 1259 и&nbsp;1260 главы 70&nbsp;&laquo;Авторское право&raquo; Гражданского Кодекса Российской Федерации от&nbsp;18&nbsp;декабря 2006 года N&nbsp;230-ФЗ).</p>
                        <p>*Все цены на&nbsp;сайте указаны в&nbsp;рублях.</p>
                    </div>
                    <div class="footer-copyright-info__copy">
                        <p>&copy;&nbsp;2019 &laquo;Дизайн Склад&raquo;&nbsp;&mdash; Интернет-магазин дизайнерской мебели. Все права защищены.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="ds-market-banner"></div>
<script type="text/javascript">
    $(function () {
        if (screen.width > 480) { //Для десктопа показываем всегда
            $('.ds-market-banner').html('<a target="_blank" rel="noopener" class="yandex-badge" href="https://clck.yandex.ru/redir/dtype=stred/pid=47/cid=2508/*https://market.yandex.ru/shop/312282/reviews">\n' +
                '<img src="https://clck.yandex.ru/redir/dtype=stred/pid=47/cid=2507/*https://grade.market.yandex.ru/?id=312282&action=image&size=2" border="0" width="150" height="101" alt="Читайте отзывы покупателей и оценивайте качество магазина на Яндекс.Маркете" />\n' +
                '</a>');
        };

        $(window).scroll(function () {
            if (document.cookie.search('marketBannerLoaded') < 0 && screen.width <= 480) {

                $('.ds-market-banner').html('<a class="mobile-yandex-widget yandex-badge" target="_blank" rel="noopener" href="https://clck.yandex.ru/redir/dtype=stred/pid=47/cid=2508/*https://market.yandex.ru/shop/312282/reviews">\n' +
                    '<img src="https://clck.yandex.ru/redir/dtype=stred/pid=47/cid=2505/*https://grade.market.yandex.ru/?id=312282&action=image&size=0" border="0" width="88" height="31" alt="Читайте отзывы покупателей и оценивайте качество магазина на Яндекс.Маркете" />\n' +
                    '</a>');

                //Устанавливаем куку по которой отличаем первый и второй хит
                var cookie_date = new Date();
                cookie_date.setTime(cookie_date.getTime() + 60 * 60 * 28 * 1000); //24 часа
                document.cookie = "marketBannerLoaded=1;path=/;expires=" + cookie_date.toGMTString();

                setTimeout(function () {
                    var el = $('.mobile-yandex-widget');
                    el.animate({left: -el.width()});
                }, 3000);
            }
        });
    });
</script>

<script type="text/javascript">
    var dimensionValue = '';
    <?if ($_SERVER['HTTP_HOST'] != 'www.dsklad.ru') {?>
        dimensionValue = 'test_dimension';
    <?} else {?>
        dimensionValue = 'production_dimension';
    <?}?>
    window.addEventListener("load", function(){
        if(window.ga && ga.create) {
            ga('set', 'dimension8', dimensionValue);
        } 
    }, false);
</script>
<?
$APPLICATION->IncludeComponent(
    'prmedia:sale.promo4views',
    '.default',
    array(),
    false
);
?>
    <div class="ds-modal-overlay closed"></div>
    <div class="ds-modal closed">
        <div class="ds-modal__inner">
        </div>
    </div>
</body>
</html>