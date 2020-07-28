<?/*
Отключаем счетчик для страницы /auth/password_change.php,
т.к. на эту же страницу из-за счетчика отправляется запрос,
в результате повторно сбрасывается пароль.
*/?>
<?if($APPLICATION->GetCurPage() != "/auth/password_change.php"):?>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript" data-skip-moving="true">
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(26291919, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true,
            webvisor:true,
            trackHash:true,
            ecommerce:"dataLayer"
        });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/26291919" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->
<? $usermail = (!empty($USER->GetEmail())) ? md5($USER->GetEmail()) : '';?>
    <script>
        $(function () {
            if(typeof(window.analyticSystem.settings.email) == 'string'){
                window.analyticSystem.settings.email = <?=CUtil::PhpToJSObject($usermail)?>;
            }
        });
    </script>
<?endif;?>