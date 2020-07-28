<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$frame = $this->createFrame()->begin("Загрузка...");

$arResult['NavQueryString'] = str_replace('&amp;','&', $arResult['NavQueryString']);
$do = preg_match('/.*bxajaxid=(\S+).*/', $arResult['NavQueryString'],$bxajaxid);
$noButtonRecom = false;
$showCatalogButtonRecom = false;
if ($arResult['NavPageNomer'] >= $arResult['NavPageCount'] || $arResult['NavPageNomer'] == 4) {
    $noButtonRecom = true;
}
if ($arResult['NavPageNomer'] == 4) {
    $showCatalogButtonRecom = true;
}
?>
    <script>
        var ajax_nav = <?=CUtil::PhpToJSObject($arResult)?>;
        var bxajaxid = "<?=$bxajaxid[1]?>";
        var noButtonRecom = "<?=$noButtonRecom?>";
        var showCatalogButtonRecom = "<?=$showCatalogButtonRecom?>";
        if (noButtonRecom) {
            $('.show-more-items-hits').remove();
        }
        if (showCatalogButtonRecom) {
            $('#ajax_nav_hit_ml').append('<a href="/catalog" class="button btn-to-catalog">Посмотреть весь каталог товаров</a>');
        }
    </script>
<?
if (!$do) {
    ?>
    <div id ="ajax_nav_hit_ml">
        <?
        if ($arResult['NavPageCount'] > $arResult['NavPageNomer']) {
            ?>
            <button class="button show-more-items-hits show-more-items-hits_ml">Показать ещё</button>
            <?
        }
        ?>
    </div>
    <script type="text/javascript">
        /* isset for javascript */
        window.isset = function() {
            if (arguments.length === 0) {
                return false;
            }
            var buff = arguments[0];
            for (var i = 0; i < arguments.length; i++) {
                if (typeof(buff) === 'undefined') {
                    return false;
                }
                buff = buff[arguments[i + 1]];
            }
            return true;
        }

        $(document).ready(function() {
            // подгрузка элементов по клику
            $('.show-more-items-hits').click(function() {
                if ($(window).scrollTop() + $(window).height() >= $('#ajax_nav_hit_ml').offset().top) {
                    if (parseInt(ajax_nav.NavPageCount) > parseInt(ajax_nav.NavPageNomer)) {
                        if (bxajaxid.length == '') {
                            bxajaxid = $('#ajax_nav_hit_ml').parents('div[id*="comp_"]').attr('id').replace('comp_', '');
                            url = location.pathname + '?PAGEN_' + ajax_nav.NavNum + '=' + (parseInt(ajax_nav.NavPageNomer) + 1) + '&bxajaxid=' + bxajaxid + '&' + ajax_nav.NavQueryString;
                        } else {
                            url = location.pathname + '?PAGEN_' + ajax_nav.NavNum + '=' + (parseInt(ajax_nav.NavPageNomer) + 1) + '&' + ajax_nav.NavQueryString;
                        }
                        if (!isset(window, 'ajax_sent')) {
                            ajax_sent = true;
                            $('.wrap_container_spinner').show();
                            $.get(url, function(data) {
                                $('.wrap_container_spinner').hide();
                                bxajaxid = $('#ajax_nav_hit_ml').before(data);
                                ajax_sent = false;
                            });
                        } else if (ajax_sent == false) {
                            ajax_sent = true;
                            $('.wrap_container_spinner').show();
                            $.get(url, function(data) {
                                $('.wrap_container_spinner').hide();
                                bxajaxid = $('#ajax_nav_hit_ml').before(data);
                                ajax_sent = false;
                            });
                        }
                    }
                }
            });
        });
    </script>
    <?
}

$frame->end();
?>