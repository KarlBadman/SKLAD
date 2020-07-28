var d = $(document), w = $(window), cl = console.log;

var ww = 0;

var is_desktop = false, is_tablet = false, is_phone = false;
var mediaqueries = {
    desktop: 1000,
    tablet: 640
};

d.on('ready', function () {
    w.trigger('ready');
});

w.on('ready load resize', function () {
    ww = w.width();
    is_desktop = ww >= mediaqueries.desktop;
    is_tablet = ww < mediaqueries.desktop && ww >= mediaqueries.tablet;
    is_phone = ww < mediaqueries.tablet;
});

jQuery.extend(jQuery.easing, {
    easeInOutCubic: function (x, t, b, c, d) {
        if ((t /= d / 2) < 1) {
            return c / 2 * t * t * t + b;
        }
        return c / 2 * ((t -= 2) * t * t + 2) + b;
    }
});

jQuery(function () {
    if ($('.variants').length) {
        if ($('.variants input').is(':checked')) {
            $(this).addClass('checked');
        }
    }

    $('.disabled').on('click', function (e) {
        e.preventDefault();
        $(this).addClass('disabled-click');
    });

    $('.item-slider').slick({
        dots: false,
        infinite: false,
        speed: 300,
        arrows: true,
        prevArrow: '<div class="icon__prev"><svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#larr2"></svg></div>',
        nextArrow: '<div class="icon__next"><svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#rarr2"></svg></div>',
        slidesToShow: 4,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 1000,
                settings: {
                    slidesToShow: 3
                }
            }, {
                breakpoint: 640,
                settings: {
                    slidesToShow: 2
                }
            }
        ]
    });

    $('.slider-banners').slick({
        dots: true,
        infinite: true,
        speed: 1500,
        arrows: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        fade: true,
        cssEase: 'linear',
        prevArrow: '<span class="icon__prev"><svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#larr2"></svg></span>',
        nextArrow: '<span class="icon__next"><svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#rarr2"></svg></span>',
        autoplaySpeed: 4000,
        pauseOnHover: true
    });

	var $frame  = $('.frame-sly');
	var $slidee = $frame.children('div.slidee').eq(0);
	var $wrap   = $frame.parent();
    window.filter_slider = function () {

		// Call Sly on frame
		$frame.sly({
			horizontal: 1,
			itemNav: 'basic',
			smart: 1,
			mouseDragging: 1,
			touchDragging: 1,
			releaseSwing: 1,
			startAt: 0,
			scrollBy: 1,
			speed: 300,
			dragHandle: 1,
			elasticBounds: 1,
			dynamicHandle: 1,
			clickBar: 1,

// 			cycleBy: 'items',
// 			cycleInterval: 1500,
// 			pauseOnHover: 1,
// 			startPaused: 1,

			// Buttons
			prevPage: $wrap.find('.icon__prev'),
			nextPage: $wrap.find('.icon__next'),
		});
        $('.slidee').css('width', $('.slidee').width() + 1);

        // if ($(this).width() < 640)
        //     $frame.sly('resume');
        // else
        //     $frame.sly('stop');
	}

    window.filter_slider();
    window.frame_sly = $frame;

    $(window).resize(function () {
        // if ($(this).width() < 640)
            // $frame.sly('resume');
        // else
            // $frame.sly('pause');

        $frame.sly('reload');
        $('.slidee').css('width', $('.slidee').width() + 1);
    });

    $('.related__widget .list').slick({
        dots: false,
        infinite: true,
        speed: 300,
        arrows: true,
        prevArrow: '<div class="icon__prev"><svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#larr2"></svg></div>',
        nextArrow: '<div class="icon__next"><svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#rarr2"></svg></div>',
        slidesToShow: 4,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 1000,
                settings: {
                    slidesToShow: 3
                }
            }, {
                breakpoint: 640,
                settings: {
                    slidesToShow: 2
                }
            }, {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1
                }
            }
        ]
    });

    if ($('.catalog').length) {
        $('.catalog__page').on('click', 'img.thumbimg', function () {
            $(this).find('input[type=radio]').attr('checked', 'checked');
            $('#mainimg-' + $(this).attr('data-thumbimg'), $(this).parents('.catalog__widget')).attr('src', $(this).attr('data-src')).next('input').attr('data-old-img',$(this).attr('data-src'));
            $('#mainlink-' + $(this).attr('data-thumbimg'), $(this).parents('.catalog__widget')).attr('href', $(this).attr('data-mainlinkimg'));

            $(this).closest($('.list')).find($("label")).removeClass("label-active");
            $(this).closest($('.list')).find($("input")).removeAttr('checked');
            $(this).parent().addClass("label-active");
        });
    }

    if ($('.options').length) {
        $('.options label').on('click', function () {
            $(this).siblings().removeClass("active");
            $(this).addClass('active');
        });
    }

    jQuery('img.svg').each(function () {
        var $img = jQuery(this);
        var imgID = $img.attr('id');
        var imgclass = $img.attr('class');
        var imgURL = $img.attr('src');

        jQuery.get(imgURL, function (data) {
            // Get the SVG tag, ignore the rest
            var $svg = jQuery(data).find('svg');

            // Add replaced image's ID to the new SVG
            if (typeof imgID !== 'undefined') {
                $svg = $svg.attr('id', imgID);
            }
            // Add replaced image's classes to the new SVG
            if (typeof imgClass !== 'undefined') {
                $svg = $svg.attr('class', imgClass + ' replaced-svg');
            }

            // Remove any invalid XML tags as per http://validator.w3.org
            $svg = $svg.removeAttr('xmlns:a');

            // Replace image with new SVG
            $img.replaceWith($svg);
        }, 'xml');
    });

    // catalog page
    if ($('.catalog__page').length) {
        var ajax_nav = {
            'NavShowAlways': false,
            'NavTitle': 'Товары',
            'NavRecordCount': '69',
            'NavPageCount': '5',
            'NavPageNomer': '1',
            'NavPageSize': '16',
            'bShowAll': false,
            'NavShowAll': false,
            'NavNum': '1',
            'bDescPageNumbering': false,
            'add_anchor': '',
            'nPageWindow': '5',
            'bSavePage': false,
            'sUrlPath': '/',
            'NavQueryString': '',
            'sUrlPathParams': '/?',
            'nStartPage': '1',
            'nEndPage': '4',
            'NavFirstRecordShow': '1',
            'NavLastRecordShow': '16'
        };
        var bxajaxid_hit_ml = '';
        var noButtonHit_ml = '';
        var showCatalogButtonHit_ml = '';

        if (noButtonHit_ml) {
            $('.show-more-items-hits_ml').remove();
        }

        window.isset = function () {
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

        var pathParts = location.pathname.split('/');
        pathParts.pop();
        var basePath = pathParts.join('/');

        $(document).ready(function () {
            $('.show-more-items-hits_ml').click(function () {
                // подгрузка элементов по клику
                if ($(window).scrollTop() + $(window).height() >= $('#ajax_nav').offset().top) {
                    if (ajax_nav.NavPageCount > ajax_nav.NavPageNomer) {
                        if (bxajaxid_hit_ml.length == '') {
                            bxajaxid_hit = $('#ajax_nav').parents('div[id*="comp_"]').attr('id').replace('comp_', '');
                            url_hit = basePath + '/include_areas/index_catalog.html' + '?PAGEN_' + ajax_nav.NavNum + '=' + (parseInt(ajax_nav.NavPageNomer) + 1) + '&bxajaxid=' + bxajaxid_hit + '&' + ajax_nav.NavQueryString;
                        } else {
                            url_hit = basePath + '/include_areas/index_catalog.html' + '?PAGEN_' + ajax_nav.NavNum + '=' + (parseInt(ajax_nav.NavPageNomer) + 1) + '&' + ajax_nav.NavQueryString;
                        }

                        if (!isset(window, 'ajax_sent_hit')) {
                            ajax_sent_hit_ml = true;
                            $('.wrap_container_spinner').show();
                            $.get(url_hit, function (data) {
                                $('.wrap_container_spinner').hide();
                                bxajaxid_hit_ml = $('#ajax_nav').before(data);
                                ajax_sent_hit_ml = false;
                            });
                        } else if (ajax_sent_hit_ml == false) {
                            ajax_sent_hit_ml = true;
                            $('.wrap_container_spinner').show();
                            $.get(url_hit, function (data) {
                                $('.wrap_container_spinner').hide();
                                bxajaxid_hit_ml = $('#ajax_nav').before(data);
                                ajax_sent_hit_ml = false;
                            });
                        }
                    }
                }
            });
        });
    }
});
function newChusenMobile(context) {
    if (!isMobile.any) {
        if($('.select select').length !== 0) {
            $('.select select', context).chosen();
            $('.chosen-single div b').replaceWith('<i class="icon__darr"><svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#darr"></svg></i>').trigger('init');
            const ps = new PerfectScrollbar('.chosen-drop');
            d.on('click', '.chosen-container', function () {
                ps.update();
            });

            $("#input_color_chosen .chosen-results").on("DOMNodeInserted", function (event) { //colors in dropdown
                var current = $(event.target);
                if (current[0].tagName == 'LI') {
                    var option = $('#input-color option')[current.data('option-array-index')];
                    current.prepend('<i class="chosen-color" style="background-color: ' + $(option).data('color') + ';" />');
                }
            });
            $("#input_color_chosen .chosen-single").on("DOMNodeInserted", function (event) { //color for selected
                var current = $(event.target);
                if (current[0].tagName == 'SPAN') {
                    var color = $('#input-color option:selected').data().color;
                    current.prepend('<i class="chosen-color" style="background-color: ' + color + ';" />');
                }
            });
            $('.select select').trigger("chosen:updated"); //fire color for default load of page
        }
    } else {
        $('select', context).each(function () {
            var select = $(this), container = select.parent();

            container.addClass('no-chosen');

            select.on('change init', function (e) {
                var value = '';

                if (container.is('.type-color')) {
                    value += '<i class="chosen-color" style="background-color: ' + $(':selected', select).data().color + ';" />';
                }

                value += (e.type === 'init' && 'placeholder' in select.data()) ? select.data().placeholder : $(':selected', select).html();

                select.siblings('.fallback').children('span').html(value);

                if ($('.basket__page').length > 0) {
                    select.siblings('.fallback').children('div > span').remove();
                    select.siblings('.fallback').children('div').before('<span>' + value + '</span>');
                }

                if (e.type === 'change') {
                    select.siblings('.fallback').removeClass('chosen-default');
                }
            }).trigger('init');
        });
    }
}


function initForms(context) {
    $('.input', context).find('input, textarea').on('blur focus keyup change paste update', function (e) {
        var input = $(this),
	        parent = input.parent(),
	        form_parent = $(this).closest('form'),
            filled = (input.val() === '' || input.disabled !== 'disabled' ) ? 'removeClass' : 'addClass';

	        parent[filled]('js-filled');


        if (e.type === 'blur' || e.type === 'focus') {
            form_parent[(e.type === 'focus' ? 'addClass' : 'removeClass')]('js-form-focused');
            parent[(e.type === 'focus' ? 'addClass' : 'removeClass')]('js-focused');
        }

        if (e.type === 'focus' && input.is('[data-phonemask]') && input.val() === '') {
            form_parent.addClass('js-form-filled');
            parent.addClass('js-filled');
        }
    }).trigger('update');

    newChusenMobile(context);


        $('input[type="checkbox"].switcher', context).on('change init', function () {
            var toActivate, input = $(this), checkedTarget = $(input.data().checked), uncheckedTarget = $(input.data().unchecked),
                toShow = input.prop('checked') ? checkedTarget : uncheckedTarget,
                toHide = input.prop('checked') ? uncheckedTarget : checkedTarget;
            // toActivate = input.prop('checked') ? '#l_4' : '#l_2';
            toShow.show(0, function () {
                $('input, textarea', toShow).prop('disabled', false);
                $(this).closest('.legal-block').addClass('legal-active');
                $(this).closest('.legal-block').find('input[type="text"]').focus();
            });

            toHide.hide(0, function () {
                $('input, textarea', toHide).prop('disabled', true);
                $(this).closest('.legal-block').removeClass('legal-active');
            });
            $(toActivate).click();
        });


    $('input[type="checkbox"].switcher', context).trigger('init');
}

(function () {
    var method;
    var noop = function () {
    };
    var methods = [
        'assert',
        'clear',
        'count',
        'debug',
        'dir',
        'dirxml',
        'error',
        'exception',
        'group',
        'groupCollapsed',
        'groupEnd',
        'info',
        'log',
        'markTimeline',
        'profile',
        'profileEnd',
        'table',
        'time',
        'timeEnd',
        'timeStamp',
        'trace',
        'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

function noscroll(state) {
    $('html')[(state ? 'addClass' : 'removeClass')]('no-scroll');
    return state;
}

function formatNumber(nStr) {
    nStr += '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(nStr)) {
        nStr = nStr.replace(rgx, '$1' + ' ' + '$2');
    }
    return nStr;
}

$('html').addClass((isMobile.any ? 'mobile' : 'desktop'));

$(document).ready(function () {
    initForms(d);

    $('.topline').each(function () {
        var topline = $(this), cookieName = 'front_toplineExpanded', expanded = get_cookie(cookieName);
        if (expanded !== null) {
            topline.attr('aria-expanded', expanded);
        }
        d.on('click', '.topline .toggle', function (e) {
            if (e.which !== 1) {
                return false;
            }
            e.preventDefault();
            topline.attr('aria-expanded', function (i, val) {
                expanded = !$.parseJSON(val);
                set_cookie(cookieName, expanded, new Date().getFullYear(), new Date().getMonth(), new Date().getDate() + 7);
                return expanded;
            });
        });
    });

    $('.free-delivery-banner').each(function () {
        var freeBanner = $(this), cookieName = 'front_toplineExpanded', expanded = get_cookie(cookieName);

        if (expanded !== null) {
            freeBanner.attr('aria-expanded', expanded);
        }

        $('.js-free-delivery-banner-close').on('click', function(e){
            if (e.which !== 1) {
                return false;
            }

            freeBanner.attr('aria-expanded', function (i, val) {
                expanded = !$.parseJSON(val);
                set_cookie(cookieName, expanded, new Date().getFullYear(), new Date().getMonth(), new Date().getDate() + 7);
                return expanded;
            });
        });
    });

    $('header').each(function () {
        var header = $(this);

        // Переменные для меню
        var menu = document.getElementById('menu'), $menu = $('#menu'), menuExpanded = false; // состояние меню

        // добавляем к структуре страницы отступы, необходимые для раскрытия меню
        // (будут использованы только при открытом меню)
        $(window).on('ready load resize', function () {
            $('.main, .mobilesearch, .topnav-x, .topline, header, .free-delivery-banner').css('transform', 'translateX(' + $menu.outerWidth() + 'px)');
        });

        // Переключение видимости формы связи
        $(document).on('click', 'header .callback .item > a', function (e) {
            if (e.which !== 1) {
                return false;
            }

            var target = $(e.target);
            e.preventDefault();

            target.parent().attr('aria-expanded', function (i, val) {
                return !$.parseJSON(val);
            });

            $(document).on('mouseup.header:contact', function (e) {
                if (!$(e.target).closest('header .callback .item[aria-expanded="true"]').length) {
                    $('.callback .item', header).attr('aria-expanded', false);
                    $(document).off('mouseup.header:contact');
                }
            });
        });

        // Переключение видимости меню
        $(document).on('click', 'header .handler', function (e) {
            if (e.which !== 1) {
                return false;
            }

            e.preventDefault();

            $(window).scrollTop(0);
            $(e.currentTarget).toggleClass('active');

            $menu.parent().attr('aria-expanded', function (i, val) {
                return menuExpanded = !$.parseJSON(val);
            });

            noscroll(menuExpanded);
        });

        $(document).on('click', 'header .actions .search', function (e) {
            e.preventDefault();
            $('.mobilesearch').toggleClass('active').find('input').trigger('focus');
        });
    });

    $('.catalog__page').each(function () {
        var page = $(this), control = 0;

        var controlsTemplate = $('#controls').html(), optionsTemplate = $('#options').html();

        Mustache.parse(controlsTemplate);
        Mustache.parse(optionsTemplate);

        var buildControls = function (section) {
            control = 0;

            $('select', section).each(function () {
                var select = $(this), is_multiple = select.is('[multiple]'), rendered = '', data = [];

                $('option', select).each(function () {
                    var id = 'option-' + control, option = $(this).attr('id', id);

                    data.push({
                        type: (is_multiple ? 'checkbox' : 'radio'),
                        name: select.attr('name'),
                        value: option.val(),
                        id: id,
                        class: (is_multiple ? 'square' : 'simple'),
                        label: option.html(),
                        color: option.data().color || null,
                        checked: (option.is(':selected') ? 'checked' : null)
                    });

                    control++;
                });

                for (var i = 0; i < data.length; i++) {
                    rendered += Mustache.render(controlsTemplate, data[i]);
                }

                $('#' + select.attr('aria-controls')).find('fieldset').html(rendered);
            });

            $('select', section).trigger('update');
        };

        $('section.filter', page).each(function () {
            var section = $(this);
            var currentActive;

            $('.handler a', section).on('click', function (e) {
                e.preventDefault();

                if ($(this).is(currentActive)) {
                    currentActive.toggleClass('handler-active');
                } else {
                    $('.params .handler a').removeClass('handler-active');
                    $(this).addClass('handler-active');
                }

                currentActive = $(this);

                var currentList = $(this).parents('.handler').siblings('.list');
                currentList.addClass('current');
                $('.list:not(.current)', section).removeClass('active');
                currentList.toggleClass('active').removeClass('current');
            });

            $('form', section).on('submit', function (e) {
                $('.list', section).removeClass('active');
            });

            d.on('change', 'section.filter input', function (e) {
                var target = $(e.target);

                $('#' + target.attr('aria-describedby')).prop('selected', target.prop('checked')).parents('select').trigger('update');

                if (target.parents('.sort').length) {
                    $('.js-desktop-filter .sort .handler a').text(target.closest('.row').find('.label').text());
                    $('.js-desktop-filter .sort .label__widget').removeClass('current-label');
                    target.closest('.label__widget').addClass('current-label');
                    $('.js-desktop-filter .sort #control-sort').removeClass('active');

                    ajaxRefreshCatalogPage($('form', section));
                }
            });

            //TODO check this realization
            d.on('click', function (e) {
                var target = $(e.target);
                if (!(target.is('section.filter .list') || target.closest('section.filter .list').length || target.is('section.filter .handler a') || target.closest('section.filter .handler a').length)) {
                    $('.list.active', section).removeClass('active');
                    $('.handler .handler-active').removeClass('handler-active');
                }
            });

            var sliderPriceNode = $('#slider_price');

            function initPriceFilter() {
                var sliderMin = parseInt(sliderPriceNode.data('min'));
                var sliderMax = parseInt(sliderPriceNode.data('max'));

                $('.js-desktop-filter .js-price input[type="checkbox"], .js-mobile-filter .js-price option').each(function () {
                    if (parseInt($(this).val()) < 0) {
                        return;
                    }

                    var elemMin = parseInt($(this).data('min'));
                    var elemMax = parseInt($(this).data('max'));
                    if ((elemMin > 0 && elemMin > sliderMax) || (elemMax > 0 && elemMax < sliderMin) || (elemMin > 0 && elemMax === 0 && elemMin < sliderMin) || (elemMax > 0 && elemMin === 0 && elemMax > sliderMax)) {
                        if ($(this).is('input')) {
                            $(this).closest('.label__widget').hide();
                        } else {
                            $(this).hide();
                        }
                    }
                });
            }

            initPriceFilter();

            // slider prices
            $('#price').change(function () {
                var val = $(this).val();
                sliderPriceNode.slider('values', 0, val);
                checkDesktopPriceFilterCheckboxes();
                ajaxRefreshCatalogPage($('.js-desktop-filter'));
            });

            $('#price2').change(function () {
                var val = $(this).val();
                sliderPriceNode.slider('values', 1, val);
                checkDesktopPriceFilterCheckboxes();
                ajaxRefreshCatalogPage($('.js-desktop-filter'));
            });

            //TODO check event duplicates
            $('.js-desktop-filter .js-price input[type="checkbox"]').change(function () {
                var min = parseInt(sliderPriceNode.data('min'));
                var max = parseInt(sliderPriceNode.data('max'));

                if ($(this).prop('checked')) {
                    $('.js-desktop-filter .js-price input[type="checkbox"]:checked').removeAttr('checked');
                    $(this).prop('checked', 'checked');

                    min = parseInt($(this).data('min')) || min;
                }

                sliderPriceNode.slider('values', 0, min);
                $('#price').val(min);

                sliderPriceNode.slider('values', 1, max);
                $('#price2').val(max);

                ajaxRefreshCatalogPage($('.js-desktop-filter'));
            });

            $('.js-mobile-filter .js-price select').change(function () {
                var activeOption = $(this).find('option:checked');
                if (activeOption.val() >= 0) {
                    var curMin = parseInt(activeOption.data('min'));
                    var curMax = parseInt(activeOption.data('max'));

                    if (curMin > 0) {
                        $('.js-mobile-filter .js-price .js-price-min').val(activeOption.data('min'));
                    } else {
                        $('.js-mobile-filter .js-price .js-price-min').val(sliderPriceNode.data('min'));
                    }

                    if (curMax > 0) {
                        $('.js-mobile-filter .js-price .js-price-max').val(activeOption.data('max'));
                    } else {
                        $('.js-mobile-filter .js-price .js-price-max').val(sliderPriceNode.data('max'));
                    }
                }
            });

            sliderPriceNode.slider({
                range: true,
                min: sliderPriceNode.data('min'),
                step: 100,
                max: sliderPriceNode.data('max'),
                values: [BX.message('FILTER_PRICE_CURRENT_MIN'), BX.message('FILTER_PRICE_CURRENT_MAX')],
                slide: function (event, ui) {
                    $('#price').val(ui.values[0]);
                    $('#price2').val(ui.values[1]);
                    checkDesktopPriceFilterCheckboxes();
                },
                stop: function (event, ui) {
                    ajaxRefreshCatalogPage($('.js-desktop-filter'));
                }
            });

            function checkDesktopPriceFilterCheckboxes() {
                var curSliderMin = parseInt(sliderPriceNode.slider('values', 0));
                var curSliderMax = parseInt(sliderPriceNode.slider('values', 1));

                $('.js-desktop-filter .js-price input[type="checkbox"]').each(function () {
                    var elemMin = parseInt($(this).data('min'));
                    var elemMax = parseInt($(this).data('max'));
                    if ((elemMin > 0 && curSliderMin < elemMin) || (elemMax > 0 && curSliderMax > elemMax)) {
                        $(this).removeAttr('checked');
                    }
                });
            }

            $('#price, #price2').focus(function () {
                $(this).data('placeholder', $(this).attr('placeholder'))
                    .attr('placeholder', '');
            }).blur(function () {
                $(this).attr('placeholder', $(this).data('placeholder'));
            });
        });

        $('section.simplefilter', page).each(function () {
            var section = $(this);

            $('.handler', section).on('click', function (e) {
                e.preventDefault();
                $(this).next('.params').toggleClass('active');
            });

            $('select[aria-controls]', section).on('change update', function () {
                $('option', this).each(function () {
                    var option = $(this);
                    $('[aria-describedby="' + option.attr('id') + '"]').prop('checked', option.prop('selected'));
                });
            });

            $('select', section).on('change update', function (e) {
                var select = $(this), is_multiple = select.is('[multiple]');

                if (is_multiple) {
                    var rendered = '';

                    $('option:selected', select).each(function () {
                        var option = $(this);

                        rendered += Mustache.render(optionsTemplate, {
                            color: option.data().color || null,
                            label: option.html(),
                            id: option.attr('id'),
                            value: option.val()
                        });
                    });

                    $('#' + select.attr('aria-owns')).html(rendered).parents('.param')[(rendered === '' ? 'removeClass' : 'addClass')]('active');
                    $('.selected', section)[($('.selected .param.active', section).length ? 'addClass' : 'removeClass')]('active');
                } else {
                    $(this).next('.default').find('.value').html($(':selected', select).html());
                }

                if (e.type === 'change') {
                    $('.params .actions:not(.active)', section).addClass('active');
                }
            });

            d.on('click', 'section.simplefilter .param .remove', function (e) {
                e.preventDefault();
                var id = $(this).attr('aria-labelledby');
                $('#' + id).prop('selected', false).parents('select').trigger('change');
            });

            buildControls(section);

            $('button[type="reset"]', section).on('click', function () {
                var button = $(this);

                var update = window.setTimeout(function () {
                    buildControls(section);
                    clearTimeout(update);
                    var submit = window.setTimeout(function () {
                        ajaxRefreshCatalogPage($('.js-desktop-mobile'));
                        clearTimeout(submit);
                    }, 50);
                }, 50);
            });

            $('[name="sort"]').filter(':checked').parents('.label__widget').addClass('current-label');
        });

        $('.js-desktop-filter .js-color input').change(function () {
            ajaxRefreshCatalogPage($('.js-desktop-filter'));
        });

        $('.js-mobile-filter button[type="submit"]').click(function (e) {
            e.preventDefault();
            ajaxRefreshCatalogPage($('.js-mobile-filter'));
        });

        var currentFilter = '';
        var originalTitle;
        var origDir;

        if ($('.filter-slider').length) {
            $('.filter-item').on('click', function (e) {
                e.preventDefault();
                var title=$(this).data('title');
                $('.catalog__page section.filter .heading-holder h1').html(title);
                $('head title').html($(this).data('prop-title'));
                if((window.location.href.match(/tags/g)||[]).length > 0){
                    $('.breadcrumbs__widget li').last().remove();
                }
                $('.breadcrumbs__widget').append('<li class="dynamic_crumb"><span itemprop="name">' + title + '</span></li>');
                /*$('head meta[name="keywords"]').attr('content', $(this).attr('data-prop-keywords'));
                 $('head meta[name="description"]').attr('content', $(this).attr('data-prop-description'));*/

                if ($(this).is(currentFilter)) {

                    if (currentFilter.hasClass('active')) {
                        origDir = $(this).attr('href').split('tags')[0];
                        setLocation(origDir);
                        $('.breadcrumbs__widget li').last().remove();
                        originalTitle = $('.breadcrumbs__widget li').last().text();
                        $('.catalog__page section.filter .heading-holder h1').html(originalTitle);
                    } else {
                        setLocation($(this).attr('href'));
                    }
                    currentFilter.toggleClass('active');

                } else if ($(this).hasClass('active')) {
                    origDir = $(this).attr('href').split('tags')[0];
                    setLocation(origDir);
                    $(this).toggleClass('active');
                    $('.breadcrumbs__widget li').last().remove();
                    originalTitle = $('.breadcrumbs__widget li').last().text();
                    $('.catalog__page section.filter .heading-holder h1').html(originalTitle);
                } else {
                    setLocation($(this).attr('href'));
                    $(this).parent().siblings('div').children('.filter-item').removeClass('active');
                    $(this).addClass('active');
                }

                currentFilter = $(this);

                if (is_phone) {
                    ajaxRefreshCatalogPage($('.js-mobile-filter'));
                } else {
                    ajaxRefreshCatalogPage($('.js-desktop-filter'));
                }
                updateTagsSort($(this).data('id'));
            });
        }

        /**
         * ajax обновление списка продуктов, с учётом параметров фильтрации
         * @param form
         */
        function ajaxRefreshCatalogPage(form) {
            var filterParams = form.serialize();
            var activeTag = $('.catalog__page .filter-slider .active');

            if (activeTag.length > 0) {
                if (filterParams) {
                    filterParams += '&tag=' + activeTag.data('id');
                } else {
                    filterParams = 'tag=' + activeTag.data('id');
                }
            }

            $.ajax({
                url: getFilterUrl(filterParams),
                method: 'GET',
                dataType: 'html',
                beforeSend: function () {
                    $('.js-catalog-section').css('opacity', 0.3);
                    $('.catalog__page .loader').show();
                },
                success: function (data) {
                    $('.js-catalog-section').html(data);
                    $('.js-catalog-section').css('opacity', 1);
                    $('.catalog__page .loader').hide();
                    set_catalog_widget();
                }
            });
        }

        /**
         * Формирует ссылку с GET-параметрами фильтра
         * @param formParams
         * @returns {string}
         */
        function getFilterUrl(formParams) {
            var res = '';
            var query = location.search.replace('?', '');
            if (query) {
                var params = query.split('&');
                for (var i = 0; i < params.length; i++) {
                    var keyval = params[i].split('=');
                    if (keyval[0] && !(formParams.search(keyval[0] + '=') >= 0)) {
                        res += params[i] + '&';
                    }
                }
            }

            if (res) {
                res += formParams;
            } else {
                res = formParams;
            }

            return res ? (location.pathname + '?' + res) : location.pathname;
        }

        function updateTagsSort(id) {
            $.ajax({
                url: '/local/templates/dsklad/ajax/updateTagsSort.php',
                dataType: 'json',
                data: {id: id},
                type: 'post',
                success: function (data) {
                    if (data.status != 'ok') {
                        console.log(data.message);
                    }
                }
            });
        }
    });

    set_catalog_widget();

    w.on('ready load resize catalog.update', function (e) {
        $('.catalog__widget .item-wrap:hover .variants').each(function () {
            var data = $(this).data();
            if ('list' in data && 'scrollLeft' in data.list) {
                data.scrollLeft = data.list.scrollLeft();
                data.clientWidth = data.list.width();
                data.scrollWidth = data.list[0].scrollWidth;
                $('.arrows', this).attr('aria-hidden', data.scrollWidth <= data.clientWidth);
                data.list.trigger('update');
            }
        });
    });
});

function counter_widget() {
    $('.counter__widget').each(function () {
        var counter = $(this),
            input = $('input', counter),
            min = parseInt(counter.data().min) || 0,
            max = parseInt(counter.data().max) || 0,
            id = parseInt(counter.data().id) || 0,
            priceMax = parseFloat(counter.data('price-max')) || 0,
            qty = parseInt(counter.data().qty) || 0,
            measure = counter.data().measure || ' шт.';
        var tmp = 1;

        $('[data-add]', counter).on('click', function (e) {
            e.preventDefault();

            var link = $(this);

            input.val(function (i, val) {
                if (max > 0) {
                    return Math.min(
                        Math.max(
                            ((parseInt(val) || min) + parseInt(link.data().add)),
                            min
                        ),
                        max
                    );
                } else {
                    return Math.max(
                        ((parseInt(val) || min) + parseInt(link.data().add)),
                        min
                    );
                }
            });

            input.trigger('recalc');
        });

        input.data().empty = true;

        input.on('change', function (e) {
            if (input.val() === '') {
                input.val(tmp);
            }
        });

        input.on('keydown', function (e) {
            var value;
            switch (e.keyCode) {
                case 38:
                    value = 1;
                    break;
                case 40:
                    value = -1;
                    break;
            }
            $('[data-add="' + value + '"]', counter).trigger('click');
        });

        input.on('focus blur recalc', function (e) {
            var postfix = ' шт.';
            if (e.type === 'focus') {
                tmp = input.val();
                input.val('');
                return;
            }
            if (e.type === 'blur') {
                if (input.val() === '') {
                    input.val(tmp);
                } else if ((max > 0) && (parseInt(input.val()) > max)) {
                    input.val(max);
                }
                input.unmask();
            }
            input.val(function (i, val) {
                var res = Math.max(parseInt(val) || min);
                if (id > 0) {
                    if ($('#product_id-' + id) != undefined) {
                        $('#product_id-' + id).val(res);
                    }
                    if (res == qty) {
                        $('.different .different_colors').show();
                    }
                    var url = $('.btn_buy.btn_to_basket_' + id).attr('href');
                    url = url.replace(/QTY=(\d*)/, 'QTY=' + res);
                    $('.btn_buy.btn_to_basket_' + id).attr('href', url);
                    $.ajax({
                        url: '/ajax/getPrice.php',
                        dataType: 'json',
                        data: {
                            id: id,
                            qty: res
                        },
                        async: false,
                        type: 'post',
                        success: function (ans) {
                            if (!ans.error) {
                                $('.optimal_price .price[product-id="' + ans.id + '"]').html('<span itemprop="price" content="' + ans.min_price + '">' + number_format(ans.min_price, 0, '', ' ') + '.–' + '</span>');
                                if (ans.min_price < priceMax) {
                                    $('.optimal_price .price-tip[product-id="' + ans.id + '"]').show();
                                } else {
                                    $('.optimal_price .price-tip[product-id="' + ans.id + '"]').hide();
                                }
                            }
                        },
                        error: function (err) {
                        }
                    });
                }
                return res + postfix;
            });
        });
    });

}


function setLocation(curLoc) {
    try {
        history.pushState(null, null, curLoc);
        return;
    } catch (e) {
    }
    location.hash = '#' + curLoc;
}

function set_catalog_widget() {
    $('.catalog__widget').each(function () {
        var catalog = $(this);

        w.on('load', function () {
            $('img.active + img[data-src]', catalog).attr('src', function () {
                var img = $(this), src = img.data().src;
                img.removeAttr('data-src');
                return src;
            });
        });

        $('.photos > span', catalog).on('click', function (e) {
            var arrow = $(this);

            if (arrow.is('.prev')) {
                arrow
                    .siblings('.active:not(:first-child)')
                    .removeClass('active')
                    .prev()
                    .addClass('active');
            } else {
                arrow
                    .siblings('.active:not(:last-of-type)')
                    .removeClass('active')
                    .next()
                    .addClass('active')
                    .next('[data-src]')
                    .attr('src', function () {
                        var img = $(this), src = img.data().src;
                        img.removeAttr('data-src');
                        return src;
                    });
            }
        });

        $('.item', catalog).on('mouseover', function () {
            w.trigger('catalog.update');
        });

        $('.variants', catalog).each(function () {
            var variants = $(this);

            $.extend(variants.data() || {}, {
                list: $('.list', variants),
                scrollLeft: 0,
                spaceForItem: 68,
                clientWidth: 0,
                scrollWidth: 0
            });
        }).find('.fader a').on('click', function (e) {
            e.preventDefault();
            var arrow = $(this), data = arrow.parents('.variants').data();

            var direction = parseInt(arrow.data().direction), extraScroll = direction > 0 ? 10 : 0,
                maxScroll = data.scrollWidth - data.clientWidth, minScroll = 0,
                distance = data.scrollLeft + extraScroll + data.spaceForItem * Math.floor(data.clientWidth / data.spaceForItem) * direction;

            if (distance > maxScroll) {
                distance = maxScroll;
            }
            if (distance < minScroll) {
                distance = minScroll;
            }

            data.list.animate({scrollLeft: distance}, 500, 'easeInOutCubic', function () {
                data.scrollLeft = distance;
                data.list.trigger('update');
            });
        }).end().find('.list').on('scroll update', function (e) {
            var variants = $(this).parents('.variants'), data = variants.data();

            if ('list' in data && 'scrollLeft' in data.list) {
                data.scrollLeft = data.list.scrollLeft();
    
                variants
                    .find('.arrows .prev').attr('aria-disabled', (data.scrollLeft <= 0))
                    .end()
                    .find('.arrows .next').attr('aria-disabled', (data.scrollLeft + data.clientWidth >= data.scrollWidth));
    
                var faders = [
                    {
                        el: $('.fader.left', variants),
                        width: 40,
                        base: data.scrollLeft
                    }, {
                        el: $('.fader.right', variants),
                        width: 40,
                        base: data.scrollWidth - data.clientWidth - data.scrollLeft
                    }
                ];
    
                for (var i = 0; i < 2; i++) {
                    var opacity = Math.min(Math.abs(faders[i]['base'] / faders[i]['width']).toFixed(2), 1),
                        hidden = opacity <= .025;
    
                    faders[i]['el'].css('opacity', opacity).attr('aria-hidden', hidden);
                }
            }
        });
    });
}

window.helpers = window.helpers || {};
window.helpers.getOfferUrl = function (offerId) {
    var offerUrl = '';
    var path = location.pathname.split('/');

    if (path.length === 5) {
        offerUrl = location.pathname + offerId + '/';
    } else {
        path[4] = offerId;
        offerUrl = path.join('/');
    }
    offerUrl += location.search;

    return offerUrl;
};

window.helpers.getUrlWithoutOffer = function () {
    var path = location.pathname.split('/');
    var offerUrl = location.href;

    if (path.length > 5) {
        path.splice(4, 1);
        offerUrl = path.join('/');
        offerUrl += location.search;
    }

    return offerUrl;
};

(function(){
    window.lazySizesConfig = window.lazySizesConfig || {};
    window.lazySizesConfig.loadMode = 1;
    window.lazySizesConfig.expand = 10;
    window.lazySizesConfig.expFactor = 1.5;
    window.lazySizesConfig.srcAttr = 'data-lazysrc';
})();
