<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$frame = $this->createFrame()->begin("Загрузка...");

if ($arParams['NAV_TITLE'] == '/include_areas/index_hit_stul.php') {
    $st = '_ml';
}  elseif ($arParams['NAV_TITLE'] == '/include_areas/index_hit_stol.php') {
    $st = '_nl';
} else {
    $st = '_d1';
}

$arResult['NavQueryString'] = str_replace('&amp;', '&', $arResult['NavQueryString']);
$do_hit = preg_match('/.*bxajaxid=(\S+).*/', $arResult['NavQueryString'], $bxajaxid);
$noButtonHit = false;
$showCatalogButtonHit = false;

if ($arResult['NavPageNomer'] >= $arResult['NavPageCount'] || $arResult['NavPageNomer'] == 4) {
    $noButtonHit = true;
}
if ($arResult['NavPageNomer'] == 4) {
    $showCatalogButtonHit = true;
}
?>

    <script>
        var ajax_nav_hit<?= $st ?> = <?= \CUtil::PhpToJSObject($arResult) ?>;
        var bxajaxid_hit<?= $st ?> = '<?= $bxajaxid[1] ?>';
        var noButtonHit<?= $st ?> = '<?= $noButtonHit ?>';
        var showCatalogButtonHit<?= $st ?> = '<?= $showCatalogButtonHit ?>';

        if (noButtonHit<?= $st ?>) {
            $('.show-more-items-hits<?= $st ?>').remove();
        }

        if (showCatalogButtonHit<?= $st ?>) {
            $('#ajax_nav_hit<?= $st ?>').append('<a href="/catalog/" class="button btn-to-catalog" <?= ($st == '_ml') ? 'id="hitsale_button_stool"' : 'id="hitsale_button_table"' ?> >Посмотреть весь каталог товаров</a>');
        }
    </script>
<?
if (!$do_hit) {
    if ($st) {
        ?>
        <div id ="ajax_nav_hit<?= $st ?>">
            <?
            if ($arResult['NavPageCount'] > $arResult['NavPageNomer']) {
                ?>
                <button class="button show-more-items-hits show-more-items-hits<?= $st ?>">Показать ещё</button>
                <?
            }
            ?>
        </div>
        <?
    }
    ?>

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
        };

        $(document).ready(function() {
            $('body').on('click', '.show-more-items-hits<?= $st ?>', function() {            // подгрузка элементов по клику
                if ($(window).scrollTop() + $(window).height() >= $('#ajax_nav_hit<?= $st ?>').offset().top) {
                    if (parseInt(ajax_nav_hit<?= $st ?>.NavPageCount) > parseInt(ajax_nav_hit<?= $st ?>.NavPageNomer)) {
                        if (bxajaxid_hit<?= $st ?>.length === 0) {
                            bxajaxid_hit = $('#ajax_nav_hit<?= $st ?>').parents('div[id*="comp_"]').attr('id').replace('comp_', '');
                            url_hit = '/' + '?<?= ($st == '_nl') ? 'PAGEN_1=2&' : ''?>PAGEN_' + ajax_nav_hit<?= $st ?>.NavNum + '=' + (parseInt(ajax_nav_hit<?= $st ?>.NavPageNomer) + 1) + '&bxajaxid=' + bxajaxid_hit + '&' + ajax_nav_hit<?= $st ?>.NavQueryString;
                        } else {
                            url_hit = '/' + '?<?= ($st == '_nl') ? 'PAGEN_1=2&' : '' ?>PAGEN_' + ajax_nav_hit<?= $st ?>.NavNum + '=' + (parseInt(ajax_nav_hit<?= $st ?>.NavPageNomer) + 1) + '&' + ajax_nav_hit<?= $st ?>.NavQueryString;
                        }

                        if (!isset(window, 'ajax_sent_hit') || (ajax_sent_hit<?= $st ?> == false)) {
                            ajax_sent_hit<?= $st ?> = true;
                            $('.wrap_container_spinner').show();
                            $.get(url_hit, function(data) {
                                $('.wrap_container_spinner').hide();
                                bxajaxid_hit<?= $st ?> = $('#ajax_nav_hit<?= $st ?>').before(data);
                                ajax_sent_hit<?= $st ?> = false;
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