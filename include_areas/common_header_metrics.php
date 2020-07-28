<? /* Метрики, подключаемые в <head></head>*/ ?>
<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
// $APPLICATION->IncludeFile('/include_areas/googleOptimize.php'); // A-B TEST
$APPLICATION->IncludeFile('/include_areas/googleTM.php');
$APPLICATION->IncludeFile('/include_areas/metrics.php');
?>
<? if ($_SERVER['HTTP_HOST'] == 'www.dsklad.ru') { ?>
    <!-- Facebook Pixel Code -->
    <script data-skip-moving="true">
        !function (f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function () {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1730366553759567');
        fbq('track', 'PageView');
    </script>
    <!-- End Facebook Pixel Code -->
<? } ?>
<script data-skip-moving="true" type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async></script>
<script data-skip-moving="true" type="text/javascript">
    var rrPartnerId = "5d5420ff97a52502d41f142e";
    var rrApi = {};
    var rrApiOnReady = rrApiOnReady || [];
    rrApi.addToBasket = rrApi.order = rrApi.categoryView = rrApi.view =
        rrApi.recomMouseDown = rrApi.recomAddToCart = rrApi.setEmail =  function() {};
    (function(d) {
        var ref = d.getElementsByTagName('script')[0];
        var apiJs, apiJsId = 'rrApi-jssdk';
        if (d.getElementById(apiJsId)) return;
        apiJs = d.createElement('script');
        apiJs.id = apiJsId;
        apiJs.async = true;
        apiJs.src = "//cdn.retailrocket.ru/content/javascript/tracking.js";
        ref.parentNode.insertBefore(apiJs, ref);
    }(document));
</script>