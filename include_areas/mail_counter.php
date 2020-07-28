<? //deprecated mail counter temporary removed 01.11.18 ?>
<!-- Rating@Mail.ru counter -->
<script type="text/javascript">
    var _tmr = window._tmr || (window._tmr = []);
    _tmr.push({id: "2913720", type: "pageView", start: (new Date()).getTime()});
    (function (d, w, id) {
        if (d.getElementById(id)) return;
        var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true; ts.id = id;
        ts.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//top-fwz1.mail.ru/js/code.js";
        var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
        if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
    })(document, window, "topmailru-code");
</script><noscript><div>
        <img src="//top-fwz1.mail.ru/counter?id=2913720;js=na" style="border:0;position:absolute;left:-9999px;" alt="" />
    </div></noscript>
<!-- //Rating@Mail.ru counter -->
<!-- Rating@Mail.ru counter dynamic remarketing appendix -->
<script type="text/javascript">
    var _tmr = _tmr || [];
    _tmr.push({
        type: 'itemView',
        productid: '<?= (!empty($_SESSION["TARGET"]["productid"]))?$_SESSION["TARGET"]["productid"]:"VALUE" ?>',
        pagetype: '<?= (!empty($_SESSION["TARGET"]["pageType"]))?$_SESSION["TARGET"]["pageType"]:"VALUE" ?>',
        list: '<?= TARGET_MY_COM_PRICE_LIST_ID ?>',
        totalvalue: '<?= (!empty($_SESSION["TARGET"]["totalvalue"]))?$_SESSION["TARGET"]["totalvalue"]:"VALUE" ?>'
    });
</script>
<!-- // Rating@Mail.ru counter dynamic remarketing appendix -->
<? unset($_SESSION["TARGET"]); ?>