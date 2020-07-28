function number_format(number, decimals, dec_point, thousands_sep) {
// при этом есть же стандартная функция с этим именем \bitrix\js\main\core\core.js
    // Format a number with grouped thousands
    //
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     bugfix by: Michael White (http://crestidg.com)

    var i, j, kw, kd, km;

    // input sanitation & defaults
    if (isNaN(decimals = Math.abs(decimals))) {
        decimals = 2;
    }
    if (dec_point == undefined) {
        dec_point = ',';
    }
    if (thousands_sep == undefined) {
        thousands_sep = '.';
    }

    i = parseInt(number = (+number || 0).toFixed(decimals)) + '';

    if ((j = i.length) > 3) {
        j = j % 3;
    } else {
        j = 0;
    }

    km = (j ? i.substr(0, j) + thousands_sep : '');
    kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
    kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : '');

    return km + kw + kd;
}

$(document).ready(function() {
    var isPagenAjax = false;
    $('body').on('click', '#ajax_nav a', function () {
        isPagenAjax = true;
        $('#ajax_nav').addClass('bg_container_spinner');
        $('#ajax_nav').children().hide();
    });

    BX.addCustomEvent('onAjaxSuccess', function(e, a, b) {
        if (isPagenAjax) {
            $('html, body').animate({
                scrollTop: $(".catalog__page").offset().top
            }, 1000);
            $('#ajax_nav').removeClass('bg_container_spinner');
            $('#ajax_nav').children().show();
        }
        isPagenAjax = false;
    });

    function scrollto(anchor, auto) {
        var $anchor = $(anchor);

        if (!$anchor.length) {
            return false;
        }

        if (anchor.substr(0, 5) === '#tab-') {
            $('[data-offset-for="' + anchor + '"]:visible a').first().trigger('click')

            var offset = $anchor.parents('[data-offset-for="' + anchor + '"]');
            if (offset.length && history.pushState) {
                $anchor = offset;
            }
        }

        if (auto) {
            w.scrollTop($anchor.offset().top);
        } else {
            $('html, body').animate({scrollTop: $anchor.offset().top}, 750, 'easeInOutCubic', function() {
                if (history.pushState) {
                    history.replaceState(null, null, anchor);
                } else {
                    window.location.hash = anchor;
                }
            });
        }
    }

    $('.catalog__page').on('mouseenter', '.item-wrap', function(){
        $(this).find('.list img').each(function() {
            $(this).attr('src', $(this).data('thumbsrc'));
        });
    })

    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        scrollto($(this).attr('href'));
    });

    w.on('load', function() {
        if (window.location.hash) {
            scrollto(window.location.hash, true);
        }
    });

    //product page
    $('.item__page').each(function() {
        var page = $(this),
            previews = $('.previews .slider', page),
            images = $('.images .slider', page);

        function onChangeOffer(goodId) {
            history.pushState(null, null, window.helpers.getOfferUrl(goodId));
            var mark_price = $('.title_price:visible').text();
            mark_price = mark_price.replace('.–', '');
            $('#preorder-good-id').val(goodId);
            $('.wrap_price_detail').hide();
            $('.item_price_detail_' + goodId).show();
            $('.wrap_slider_detail').removeClass('visible');
            $('.data .qty_detail[data-id="' + goodId + '"]', page).addClass('active').siblings().removeClass('active');
            $('.min_quantity_discounts[data-id="' + goodId + '"]', page).addClass('active').siblings().removeClass('active');
            $('section.item .variants a[data-value="' + goodId + '"]', page).addClass('active').siblings().removeClass('active');
            $('.wrap_btn_to_basket a.to_basket', page).removeClass('active');
            $('.wrap_btn_to_basket a.to_basket.btn_to_basket_' + goodId, page).addClass('active');
            var slideActive = 'item_slider_' + goodId;
            $('div.' + slideActive).addClass('visible');
            $('div.' + slideActive + ' a:first').trigger('click');
            $('.field_custom').hide();
            $('.field_custom_' + goodId).show();
            previews.slick('refresh');

                var origin_name = $('#name_container').data('origin_name');
                var full_name = $('#name_container').text();
                $('#name_container').text(origin_name + ' ' + $('#input-color option:selected').text());
                new_title = document.title.replace(full_name, origin_name + ' ' + $('.chosen-single').text().toLowerCase());
                var price = $('.title_price:visible').text();
                price = $.trim(price.replace('.–', ''));
                new_title = new_title.replace($.trim(mark_price), price);
                document.title = new_title;
        }

        // preorder-good-id
        // data-good-id
        $('.select.type-color select', page).on('change', function(e) {
            var goodId = $(this).val();
            onChangeOffer(goodId);
            if(+$('.item_slider_'+goodId+' .previews .item').length == 1){
                images.slick('refresh');
            }
        });

        $('section.item .variants a', page).on('click', function(e) {
            e.preventDefault();
            var goodId = $(this).data().value;
            $('.select.type-color select', page).val(goodId).trigger('chosen:updated').change();
            $('#input_color_chosen a.chosen-single i.chosen-color').css('background-color', $('.select.type-color select [value="'+ goodId +'"]').data('color'));
            onChangeOffer(goodId);
       });

        previews.slick({
            dots: false,
            slidesToShow: 7,
            useCSS: false,
            vertical: true,
            verticalSwiping: true,
            speed: 0,
            prevArrow: '<span class="icon__uarr"><svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#uarr"></svg></span>',
            nextArrow: '<span class="icon__darr"><svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#darr2"></svg></span>',
            infinite: false,
        });

        images.slick({
            dots: false,
            useCSS: false,
            speed: 300,
            prevArrow: '<span class="slick-prev"><span class="icon__larr"><svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#larr2"></svg></span></span>',
            nextArrow: '<span class="slick-next"><span class="icon__rarr"><svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#rarr2"></svg></span></span>',
        });

        $(previews).on('click', '.item a', function (e) {
            e.preventDefault();
            images.slick('slickGoTo', $(this).data().index);
        });

        $('.tab.photos .button', page).on('click', function (e) {
            e.preventDefault();
            $(this).siblings('.list').attr('aria-expanded', function (i, val) {
                return !$.parseJSON(val);
            });
        });
    });

    $('.tab.pickup .field__widget .show_point').on('click', function(e) {
        e.preventDefault();
        if ($(this).hasClass('activ')) {
            $(this).removeClass('activ').children('.txt_show_point').text('Показать карту и схему проезда');
            $('.tab.pickup .address , .tab.pickup .map').slideUp();
        } else {
            $(this).addClass('activ').children('.txt_show_point').text('скрыть схему проезда');
            $('.tab.pickup .address , .tab.pickup .map').slideDown();
        }
    });

    $('.cabinet__page').each(function() {
        var page = $(this);

        w.on('ready load resize', function() {
            $('.order-data .calculation th:last-child', page).each(function() {
                $(this).css('width', $('.basket__widget .list .item .total').width());
            });
        });
    });

    $('.delivery__page').each(function() {
        var page = $(this);

        $('section.tabs__widget a[target="_self"]', page).on('click', function(e) {
            e.preventDefault();
            $(this).parent('li').toggleClass('opened');
        });

        $('section.tabs__widget ul ul a', page).on('click', function(e) {
            $(this).parents('li.opened').removeClass('opened');
        });
    });

    $('.tabs__widget').each(function() {
        var self = $(this),
            handler = $('> .tabs-handler', self),
            content = $('> .tabs-content', self),
            active = $('.active', handler).index();

        if (!content.length) {
            return false;
        }

        $('a:not([target])', handler).on('click', function(e) {
            if (!$(this).parents('li').hasClass('link_logout_personal')) {
                var index = $(this).parents('li:not(".active")').index();
                if (index === -1) {
                    return false;
                }

                e.preventDefault();
                var current = $('> .tab', content)
                    .removeClass('active')
                    .filter(':eq(' + index + ')').addClass('active');

                $('li', handler)
                    .removeClass('active')
                    .filter(':eq(' + index + ')').addClass('active');

                self.trigger('change');
                $(this).trigger('change');
            }
        });

        $('.intab-toggler', content).on('click', function(e) {
            e.preventDefault();
            $(this).parents('.tab').toggleClass('intab-active');
        });

        $('> .tab', content)
            .removeClass('active')
            .filter(':eq(' + active + ')').addClass('active');
    });

    counter_widget();

    // basket load
    if(window.location.href.indexOf('/order/') < 0) {
        $('.basket_area').load('/include_areas/small_basket.php');
    }
    //фильтр для мобил
    $('.tit_filter_mobile').on('click', function() {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $('.wrap_params_filter').slideUp(100);
        } else {
            $(this).addClass('active');
            $('.wrap_params_filter').slideDown(100);
        }
    });

    //удаление пункта фильтра мобил
    $('a.remove_filter').on('click', function() {
        var attrDelete = $(this).attr('data_delete');
        var filterDelete = $('.wrap_params_filter').find('input[name="' + attrDelete + '"]').prop('checkrd', false);

        filterDelete.closest('.label__widget').trigger('click');
        $(this).closest('.option').hide();

        return false;
    });

    //табы личный кабинет
    var urlTabs = window.location.search;
    if ($('.cabinet__page').length) {
        var selectTab2 = urlTabs.indexOf('?tab_like');
        if (selectTab2 != -1) {
            $('.tabs__widget_adding .default > li').removeClass('active');
            $('.tabs__widget_adding .default > li').eq(1).addClass('active');
            $('.tabs__widget_adding .tabs-content.default > div').removeClass('active');
            $('.tabs__widget_adding .tabs-content.default > div.like').addClass('active');
        }

        var selectTab3 = urlTabs.indexOf('?tab_settings');
        if (selectTab3 != -1) {
            $('.tabs__widget_adding .default > li').removeClass('active');
            $('.tabs__widget_adding .default > li').eq(2).addClass('active');
            $('.tabs__widget_adding .tabs-content.default > div').removeClass('active');
            $('.tabs__widget_adding .tabs-content.default > div.settings').addClass('active');
        }
    }

    if ($('.delivery__page').length) {
        var selectTab3 = urlTabs.indexOf('?tab_guarantee');
        if (selectTab3 != -1) {
            $('.delivery__page .tabs-handler li a').eq(2).trigger('click');
        }
    }

    //подписка на рассылку футер
    $('.subscribe_footer').on('click', function() {
        $('.result_subscribe').text('');
        if ($(this).closest('form').find('input[name="uni_email"]').val() == '') {
            $('.result_subscribe').text('Введите свою почту!');
            return false;
        } else {
            var obj = new Object();
            obj.email = $(this).closest('form').find('input[name="uni_email"]').val();
            $.ajax(
                {
                    url: '/bitrix/templates/dsklad/ajax/subscribe.php',
                    dataType: 'text',
                    data: obj,
                    type: 'post',
                    success: function(ans) {
                        $('.result_subscribe').text(ans);
                        $('.subscribe_footer').closest('form').find('input[name="uni_email"]').val('');
                    }
                });
            return false;
        }
    });

    $('.link_remove_favorites').on('click', function() {
        var obj = new Object();
        obj.idRemoveFavorites = $(this).attr('data-remove-favorites');
        $('.wrap_container_spinner').show();
        $.ajax(
            {
                url: '/bitrix/templates/dsklad/ajax/remove_favorites.php',
                dataType: 'text',
                data: obj,
                type: 'post',
                success: function(ans) {
                    setTimeout(function() {
                        $('.wrap_container_spinner').hide();
                        $('.catalog__widget .item[data-item-id="' + ans + '"]').remove();
                    }, 1000);
                }
            });
        return false;
    });

    $('.remove_all_favorites').on('click', function() {
        $('.wrap_container_spinner').show();
        var obj = new Object();
        obj.idRemoveFavorites = 'removeAll';
        $.ajax(
            {
                url: '/bitrix/templates/dsklad/ajax/remove_favorites.php',
                dataType: 'text',
                data: obj,
                type: 'post',
                success: function(ans) {
                    setTimeout(function() {
                        $('.wrap_container_spinner').hide();
                        $('.catalog__widget .item').remove();
                        $('.no_favorites_items').show();
                    }, 1000);
                }
            });
        return false;
    });

    //наведение сортировка каталог
    $('#control-sort .label__widget').hover(
        function() {
            $('#control-sort .label__widget').find('span.control').find('input.select').addClass('old_select');
            $('#control-sort .label__widget').find('span.control').find('input').removeClass('select');
            $(this).find('span.control').find('input').addClass('select');
        },
        function () {
            $('#control-sort .label__widget').find('span.control').find('input').removeClass('select');
            $('#control-sort .label__widget').find('span.control').find('input.old_select').addClass('select');
        }
    );
/*
    $('.basket_area').on('click', '.cart', function() {
        if ($(this).attr('data-count') == 0) {
            $('.success_popup_send_txt').removeClass('active');
            $('.success_popup_send_txt').find('.desc_popup_send_txt').find('span').html('В вашей корзине пусто!');
            $('.success_popup_send_txt').addClass('active');

            setTimeout(function () {
                $('.success_popup_send_txt').removeClass('active');
            }, 3000)
        }
    });
*/
    $('.field').on('click', '.button', function() {
        if ($(this).attr('data-count') == 0) {
            $('.success_popup_send_txt').removeClass('active');
            $('.success_popup_send_txt').find('.desc_popup_send_txt').find('span').html('Ваш вопрос успешно отправлен.');
            $('.success_popup_send_txt').addClass('active');

            setTimeout(function () {
                $('.success_popup_send_txt').removeClass('active');
            }, 3000)
        }
    });

    $('.field').on('click', '.mailclass', function() {
        if ($(this).attr('data-count') == 2) {
            $('.success_popup_send_txt').removeClass('active');
            $('.success_popup_send_txt').find('.desc_popup_send_txt').find('span').html('Вы подписаны на обновления.');
            $('.success_popup_send_txt').addClass('active');

            setTimeout(function () {
                $('.success_popup_send_txt').removeClass('active');
            }, 3000)
        }
    });

    // удаление товара из малой корзины
    $('body').on('click','.smbas_del', function(e) {
        e.preventDefault();
        var prod = $(this).parents('tr');
        $.ajax({
            url: '/bitrix/templates/dsklad/ajax/remove_from_smcart.php',
            dataType: 'text',
            data: 'id=' + prod.attr('data-id'),
            type: 'post',
            success: function(res) {
                prod.remove();
                location.reload();
            }
        });
    });

    //small slider product switcher
    $('.catalog').on('change', '.js-select-offer', function(){
        var variant = $(this),
            form = variant.closest('form'),
            buybtn = form.find('a.to_basket'),
            prebtn = form.find('a.to_preorder'),
            prebtnfrm = form.find('a.to_preorder_form');

        variant.closest('.item').attr('data-item-id', variant.val());
        buybtn.attr('href', buybtn.data('href-tmp').replace('#ID#',variant.val()));

        if (variant.data('count') <= 0) {
            buybtn.addClass('hidden');
            prebtn.removeClass('hidden');
            prebtnfrm.addClass('hidden');
        } else {
            buybtn.removeClass('hidden');
            prebtn.addClass('hidden');
            prebtnfrm.addClass('hidden');
        }

        $('#preorder-good-id').val(variant.val());
    });

    $('.js-select-offer:checked').each(function(){
        $(this).change();
    });


    /* pixels events */
    if (typeof fbq === "function") {

        // Facebook - start checkout
        $('.checkout_start').click(function () {
            fbq('track', 'InitiateCheckout');
        });

        // Facebook - add payment info
        $('.link_success_order_custom').click(function () {
            fbq('track', 'AddPaymentInfo');
        });
    }
});