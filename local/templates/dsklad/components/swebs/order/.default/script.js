/**
 * Created by Дмитрий on 23.09.2016.
 */
 // var sum_price=0;

function plus_dop_price()
{
    var dop_price = 0;
    if ($('input[name="obreshetka_price"]').length > 0) {
        $('.obreshetka_srv').attr('num', $('input[name="obreshetka_price"]').val());
    }
    $('.tabs-content > .tab.active .delivery_service').prop('disabled', false);
    $('.tabs-content > .tab.active .delivery_service').parents('label.label__widget').removeClass('disabled');

    $('[data-kgt-req=\"Y\"]').remove();
    $('.tabs-content > .tab.active .delivery_service').each(function() {
        if ($(this).prop('checked') == true) {
            dop_price = dop_price + parseInt($(this).attr('num'));

            if ($(this).data('dserv-code') == 'delivery_weekend' || $(this).data('dserv-code') == 'delivery_weekend_1' || $(this).data('dserv-code') == 'delivery_weekend_2' || $(this).data('dserv-code') == 'delivery_evening') {
                $('[data-dserv-upliftkgt=\"Y\"]').prop('disabled', true);
                $('[data-dserv-upliftkgt=\"Y\"]').parents('label').addClass('disabled');
                $('[data-kgt-req=\"Y\"]').remove();
            } else if ($(this).data('dserv-code') == 'delivery_up_lift' && $(this).data('dserv-upliftkgt') == 'Y') {
                $('[data-dserv-code=\"delivery_weekend\"]').parents('label').addClass('disabled');
                $('[data-dserv-code=\"delivery_weekend_1\"]').parents('label').addClass('disabled');
                $('[data-dserv-code=\"delivery_weekend_2\"]').parents('label').addClass('disabled');
                $('[data-dserv-code=\"delivery_evening\"]').parents('label').addClass('disabled');
                $('[data-dserv-code=\"delivery_weekend\"]').prop('disabled', true);
                $('[data-dserv-code=\"delivery_weekend_1\"]').prop('disabled', true);
                $('[data-dserv-code=\"delivery_weekend_2\"]').prop('disabled', true);
                $('[data-dserv-code=\"delivery_evening\"]').prop('disabled', true);
          }

          if ($(this).data('kgt-req-hidden') == 'Y') {
              $(this).parents('label .control').append('<input data-kgt-req="Y" type="hidden" name="dserv[]" value="upkgtreq" autocomplete="off" />');
          }
        }
    });

    var dostavka_num = parseInt($('.dostavka_price').attr('num')),
        sum_num = parseInt($('.sum_price').attr('num')),
        new_dostavka_num = dostavka_num + dop_price,
        new_sum_num = sum_num + dop_price;

    if (new_dostavka_num >= 0) {
        $('input[name="delivery_price"]').val(new_dostavka_num);
        new_dostavka_num = number_format(new_dostavka_num, 0, '.', ' ') + '.–';
    }

    if (new_sum_num > 0) {
        new_sum_num = number_format(new_sum_num, 0, '.', ' ') + '.–';

    }

    if ($('[data-dpd-status]').data('dpd-status') == 1) {
        $('.dostavka_price span').text(new_dostavka_num);
        $('.tab.active #delivery_coast').text(new_dostavka_num);
        $('.legal_warning').remove();
    }

    cantCalculateDelivery = $('.calculation #delivery_calculate').val();
    spb = 78000000000;
    txt = 'Доставка осуществляется согласно тарифам транспортной компании';
    if (
        cantCalculateDelivery
        || (
            $('input[name="legal"]').is(':checked')
            && (
                $('input[name="delivery"]').val() != 7
                || $('#city_id').val() != spb
            )
        )
    ) {
        // $('.tab.active #delivery_coast').text('');
        // $('.tab.active #delivery_coast').next().html('<span style="font-weight: 700">' + txt + '</span>');
        // // $('.dostavka_price span').text('- *');
        // $('#total_summ').after('<div class="legal_warning">*' + txt + '</div>');
    }

    $('.sum_price span').text(new_sum_num);
}

function update(obj)
{
    $.ajax({
        url: obj.url,
        dataType: "text",
        data: obj.data,
        async: true,
        type: "post",
        success: function(ans) {
            if (obj.nodes.indexOf('.map_no_limitation') !== -1 && obj.nodes.indexOf('.map_limitation') !== -1) {
                for (mapId in window.GLOBAL_arMapObjects) {
                    window.GLOBAL_arMapObjects[mapId].destroy();
                    delete window.GLOBAL_arMapObjects[mapId];
                }
            }

            ans = '<div>' + ans + '</div>';

            $(obj.nodes).each(function() {
                var node = this.concat();
                $(node).html($(ans).find(node).html());

                if(node == '.test_input') { // кастыли на релоуд страницы если изменилась возможность доставки дпд

                    // cost =  $('.test_input input').val();

                    // if(costNew != cost && cost == 0 || costNew == 0 && cost > 0){

                    //     location.reload();
                    // }
                }
            });

            obj.success();

            plus_dop_price();

            if ($('#l_3').hasClass('active')) {
                // $('.npp_hide').hide();
                // $('option.npp_hide').addClass('disabled');
                // $('.map_limitation').show();
                // $('.map_no_limitation').hide();
            }

            window.sum_price = $('.sum_price').attr('num');

            if ($('[name="delivery"]').val() == '7') {
                window.deliveryFix();
            }

        }
    });
}

$(document).ready(function() {
    // delete item
    $('#order_wrapper').on('click', 'a.remove_basket_items', function() {
        $('.wrap_container_spinner').show();
        var obj = {};
        obj.id = $(this).attr('href');
        $.ajax({
            url: window.order_ajax_path + '/ajax/del.php',
            dataType: "text",
            data: obj,
            async: true,
            type: "post",
            success: function(ans) {
                if (ans == 'ok') {
                    window.location.reload();
                    obj = {};
                    obj.url = '/include_areas/order.php';
                    obj.data = {
                        delivery: $('input[name="delivery"]').val(),
                        is_legal_entity: $('input[name="legal"]').is(':checked')
                    };
                    obj.nodes = [
                        '#basket_items',
                        '#total_summ',
                        '.total_summ_title',
                        '.field-total',
                        '.field-additions',
                        '.options',
                        '.obreshetka_price',
                        '#input-delivery-point',
                        '.map_no_limitation',
                        '.map_limitation',
                        '.payment',
                        '.test_input'
                    ];
                    obj.success = function() {
                        $('.basket_area').load('/include_areas/small_basket.php', function() {
                            counter_widget();
                            $('.wrap_container_spinner').hide();
                        });
                    };
                    update(obj);
                    if ($("#basket_items").children().length == "1") {
                        location.reload();
                    }
                } else {
                    alert(ans);
                    $('.wrap_container_spinner').hide();
                }
            }
        });
        return false;
    });

    // set quantity
    $('#order_wrapper').on('update change recalc', 'input.order_quantity', function() {

        $('.wrap_container_spinner').show();
        var obj = {};
        obj.id = $(this).attr('product_id');
        obj.q = $(this).val();
        $.ajax({
            url: window.order_ajax_path + '/ajax/add.php',
            dataType: "text",
            data: obj,
            async: true,
            type: "post",
            success: function(ans) {
                if (ans == 'ok') {
                    obj = {};
                    obj.url = '/include_areas/order.php';
                    obj.data = {
                        payment : $('input[name=\"payment\"]:checked').val(),
                        deliveryPointCode : $('[name=\"point\"]').val(),
                        delivery: $('input[name="delivery"]').val(),
                        is_legal_entity: $('input[name="legal"]').is(':checked')
                    };

                    obj.nodes = [
                        '#basket_items',
                        '#total_summ',
                        '.total_summ_title',
                        '.field-total',
                        '.obr_price',
                        '.obreshetka_price',
                        '#input-delivery-point',
                        '.map_no_limitation',
                        '.map_limitation',
                        '.services',
                        '.payment',
                        '.test_input',
                        "#basket_items",
                        ".services",
                        ".cart-add-more"
                    ];
                    obj.success = function() {
                        $('.basket_area').load('/include_areas/small_basket.php', function() {
                            counter_widget();
                            if (parseInt($('.dostavka_price').attr('num')) >= 4000) {
                                $('#l_3').addClass('super-disabled');
                                $('#l_3').find('[type=\"radio\"]').prop('disabled', true);
                            } else {
                                $('#l_3').removeClass('super-disabled');
                                $('#l_3').find('[type=\"radio\"]').prop('disabled', false);
                            }
                            $('.wrap_container_spinner').hide();
                        });
                        // balloonOpenByOptionSelected();
                    };

                    update(obj);


                } else {
                    $('.wrap_container_spinner').hide();
                }
            }
        });
    });

    // add service
    $('#order_wrapper').on('click', '.order_service', function() {
        $('.wrap_container_spinner').show();
        var url = window.order_ajax_path + '/ajax/del.php';
        if ($(this).prop('checked')) {
            url = window.order_ajax_path + '/ajax/add.php';
        }
        var obj = {};
        obj.id = $(this).val();
        $.ajax({
            url: url,
            dataType: "text",
            data: obj,
            async: false,
            type: "post",
            success: function(ans) {
                if (ans == 'ok') {
                    //window.location.reload();
                    obj = {};
                    obj.url = '/include_areas/order.php';
                    obj.data = {
                        payment : $('input[name=\"payment\"]:checked').val(),
                        delivery: $('input[name="delivery"]').val(),
                        is_legal_entity: $('input[name="legal"]').is(':checked')
                    };
                    obj.nodes = [
                        '#total_summ',
                        '.total_summ_title',
                        '.field-total',
                        '.payment',
                        "#basket_items",
                        ".services",
                        '.test_input'
                    ];
                    obj.success = function() {
                        counter_widget();
                        $('.wrap_container_spinner').hide();
                    };
                    update(obj);
                } else {
                    alert(ans);
                    $('.wrap_container_spinner').hide();
                }
            }
        });
    });

    // add delivery service
    $('#order_wrapper').on('click', '.delivery_service', function() {
        $('.wrap_container_spinner').show();
        //удалил чтобы не добавлялись доп. услуги как товары в корзину
        /*var url = window.order_ajax_path + '/ajax/del.php';
        if ($(this).prop('checked')) {
            url = window.order_ajax_path + '/ajax/add.php';
        }*/
        var obj = {};
        obj.id = $(this).val();
        var obr_show_error = ($(this).hasClass('obreshetka_srv') && !$(this).prop("checked"));
        /*$.ajax({
            url: url,
            dataType: "text",
            data: obj,
            async: true,
            type: "post",
            success: function (ans) {
                if (ans == 'ok') {*/
        //window.location.reload();
        obj = {};
        obj.url = '/include_areas/order.php';
        obj.data = {
            delivery: $('input[name="delivery"]').val(),
            is_legal_entity: $('input[name="legal"]').is(':checked')
        };
        obj.nodes = [
            '#total_summ', '.total_summ_title', '.field-total', '.payment', '.test_input'
        ];
        obj.success = function() {
            $('.wrap_container_spinner').fadeOut();
            //показываем предупреждение об обрешетке
            if (obr_show_error) {
                $('.obrerror, .obrerror_after').fadeIn('slow');
                $(document).mouseup(function(e) { // событие клика по веб-документу
                    if (!$('.obrerror, .obrerror_after').is(e.target) // если клик был не по нашему блоку
                        && $('.obrerror, .obrerror_after').has(e.target).length === 0) { // и не по его дочерним элементам
                        $('.obrerror, .obrerror_after').fadeOut(); // скрываем его
                    }
                });
            }
        };
        update(obj);
        /*} else {
            alert(ans);
            $('.wrap_container_spinner').fadeOut();
        }*/

        if (!obr_show_error) {
            $('.obrerror, .obrerror_after').fadeOut();
        }
        /*}
    });*/
    });

    // signin
    $('#order_wrapper').on('click', '.order_signin', function() {
        $('.actions .signin.fancybox').trigger('click');
        return false;
    });

    // change city
    window.cityValOld = $('#city_id').val();
    /*if( cityValOld != 77000000000 && cityValOld != 78000000000 ){
        $('input.obreshetka_srv[name="dserv_blv[]"]').prop("checked",true);
    }*/
    $('input.obreshetka_srv[name="dserv_blv[]"]').prop("checked", true);

    $('#order_wrapper').on('focusout', '#autocomplete', function() {
        setTimeout(function() {
            var cityValNew = $('#city_id').val();
            if (cityValOld != cityValNew) {
                window.cityValOld = cityValNew;
                $('.wrap_container_spinner').show();
                var obj = {};
                obj.intLocationID = cityValNew;
                $.ajax({
                    url: window.order_ajax_path + '/ajax/recity.php',
                    dataType: "text",
                    data: obj,
                    async: false,
                    type: "post",
                    success: function(ans) {
                        window.location.reload();
                    }
                });
            }
        }, 300);
    });

    // change terminal address
    $('body').on('change', '#order_wrapper #input-delivery-point', function() {
        var option = $(this).find('option:selected').val(), cityId = $(this).find('option:selected').attr('cityId');
        var index = $(this)[0].selectedIndex;
        var optionsCount = $(this).find('option').length;
        var optionIsTerminal = $(this).find('option:selected').data('isterminal');
        /*if(
            cityId != 49694102 //Москва
            ||
            cityId != 49694167 //Санкт-Петербург
            ){
            $('input.obreshetka_srv[name="dserv_blv"]').prop("checked",false);
            $('input.obreshetka_srv[name="dserv[]"]').prop("checked",false);
        }else{
            $('input.obreshetka_srv[name="dserv_blv"]').prop("checked",true);
            $('input.obreshetka_srv[name="dserv[]"]').prop("checked",true);
        }*/
        $('input.obreshetka_srv[name="dserv_blv"]').prop("checked", true);
        $('input.obreshetka_srv[name="dserv[]"]').prop("checked", true);
        $('.terminal_address').hide();
        $('#' + option).show();

        $('.payment .options label#l_3').removeClass('cup');
        $('.payment .options label#l_3 .discript.new').remove();
        $('.payment .options label#l_3 input[type="radio"]').removeAttr('disabled');

        if (parseInt($('.terminal_address#' + option + ' .max-sum').text()) < parseInt($('.sum_price').attr("num"))) {
            $('.payment .options label#l_3').addClass('cup');
            $('.payment .options label#l_3 input[type="radio"]').attr('disabled', "disabled");
            $('.payment .options label#l_3').append('<div class="discript new" style="display: none;">В выбранном пункте самовывоза действует ограничение по сумме наложенного платежа.</div>');
        }

        if ($('.terminal_address#' + option + ' .max_pay').text() == 'нет') {
            $('.payment .options label#l_3').addClass('cup');
            $('.payment .options label#l_3 input[type="radio"]').attr('disabled', "disabled");
            $('.payment .options label#l_3').append('<div class="discript new" style="display: none;">В выбранном пункте самовывоза отсутствует возможность оплаты наличными при получении.</div>');
        }

        // Костыль на сумму доставки по москве и питеру с учетом терминалов
        deliveryFix();
        balloonOpenByOptionSelected();
    });

    function balloonOpenByOptionSelected () {
        // $("#input-delivery-point").trigger("chosen:updated");
        var index = $('#order_wrapper #input-delivery-point')[0].value,
        optionsCount = $('#order_wrapper #input-delivery-point').find('option').length;

        dskladMapYndex.mapInstance.geoObjects.get(0).objects.balloon.open(index);
        // window.globalPlacemark[index].balloon.open();
        // window.globalPlacemark[index + optionsCount].balloon.open();
    }

    window.deliveryFix = function() {
        window.sum_price = parseInt($('.sum_price').attr('num')) - parseInt($('.dostavka_price').attr('num'));
        var spb = '78000000000', msk = '77000000000';
        var optionIsTerminal = $('#input-delivery-point').find('option:selected').data('isterminal');
        if ($('#city_id').val().length > 0 && ($('#city_id').val() == spb || $('#city_id').val() == msk) && $('[data-dpd-status]').data('dpd-status') != 0) {

            if ($('[name=\"dpd_code\"]').val() != 'dpd_online_classic') {
                if ($('[name=\"legal\"]').prop('checked') == false) {
                    if ($('#city_id').val().length > 0 && $('#city_id').val() == spb) {
                        priceFix = optionIsTerminal == 'Y' ? 250 : 500;
                    }
                    if ($('#city_id').val().length > 0 && $('#city_id').val() == msk) {
                        priceFix = optionIsTerminal == 'Y' ? 500 : 700;
                    }
                } else {
                    if ($('#city_id').val().length > 0 && $('#city_id').val() == spb) {
                        // priceFix = optionIsTerminal == 'Y' ? 500 : 700;
                        priceFix = parseInt($('.dostavka_price').attr('num'));
                    }
                    if ($('#city_id').val().length > 0 && $('#city_id').val() == msk) {
                        // priceFix = optionIsTerminal == 'Y' ? 700 : 900;
                        priceFix = parseInt($('.dostavka_price').attr('num'));
                    }
                }
            } else {
                priceFix = parseInt($('.dostavka_price').attr('num'));
            }

            $('.sum_price').attr('num', parseInt(window.sum_price) + parseInt(priceFix));
            $('#total_summ .dostavka_price').attr('num', priceFix);

            // Костыль если сумма доставки больше 4000
            if (parseInt($('.dostavka_price').attr('num')) >= 4000) {
                $('#l_3').addClass('super-disabled');
                $('#l_3').find('[type=\"radio\"]').prop('disabled', true);
            } else {
                $('#l_3').removeClass('super-disabled');
                $('#l_3').find('[type=\"radio\"]').prop('disabled', false);
            }
            $("[name=\"point\"]").trigger("chosen:updated");
            plus_dop_price();
        } else {
            if (optionIsTerminal != 'Y' && $('[data-dpd-status]').data('dpd-status') == 0) {
                var txt = 'Расчет стоимости доставки в данный момент недоступен';
                $('.dostavka_price span').text('- *');
                if ($('#total_summ').parent().find('.legal_warning').length == 0)
                    $('#total_summ').after('<div class="legal_warning">*' + txt + '</div>');
            }
        }
    }

    // input to session
    $('#order_wrapper').on('change', '.session', function() {
        var obj = {};
        obj.name = $(this).attr('name');
        obj.value = $(this).val();
        $.ajax({
            url: window.order_ajax_path + '/ajax/fields.php',
            dataType: "text",
            data: obj,
            async: true,
            type: "post"
        });
    });

    // change delivery
    $('#order_wrapper').on('change', 'a:not([target])', function() {

        $('.wrap_container_spinner').show();
        var id = $(this).attr('href');
        $('input[name="delivery"]').val(id);

        obj = {};
        obj.url = '/include_areas/order.php';
        obj.data = {
            payment : $('input[name=\"payment\"]:checked').val(),
            delivery: id,
            is_legal_entity: $('input[name="legal"]').is(':checked')
        };
        obj.nodes = [
            '#delivery_coast', '#total_summ', '.total_summ_title', '.field-total', '#debug', '.payment', '.test_input'
        ];
        obj.success = function() {
            $('.wrap_container_spinner').hide();
            var daysStr = $('input[name="payment"]:checked').data('dpc');
            $('[data-dpc-days=\"dpc-str\"]').text(daysStr);
        };
        update(obj);
    });

    // change page by legal entity checkbox
    $('#order_wrapper').on('change init', 'input[type="checkbox"].switcher', function() {
        $('.wrap_container_spinner').show();

        obj = {};
        obj.url = '/include_areas/order.php';
        obj.data = {
            delivery: $('input[name="delivery"]').val(),
            is_legal_entity: $('input[name="legal"]').is(':checked')
        };
        obj.nodes = [
            '#delivery_coast',
            '#total_summ',
            '.total_summ_title',
            '.field-total',
            '.field-additions',
            '#debug',
            '.payment',
        ];
        obj.success = function() {
            $('.wrap_container_spinner').hide();
        };
        update(obj);
    });

    // .basket__page section.order .info .fields fieldset.payment .options
    $('fieldset.payment .options label').hover(function() {
        $(this).children('.discript').show();
    }, function() {
        $(this).children('.discript').hide();
    });

    // 	promo
    $('#order_wrapper').on('click', '#promo', function() {

        $('.wrap_container_spinner').show();
        $("#promocode").removeClass("notfind");
        var promo = $('input[name=promo]').val();

        $.ajax({
            type: "POST",
            url: "/ajax/check_promo.php",
            data: {promo: promo}
        })
            .done(function(result) {
                if (!(parseInt(result) == 1)) {
                    $("#promocode").addClass("notfind");
                    $('.wrap_container_spinner').hide();
                } else {
                    obj = {};
                    obj.url = '/include_areas/order.php';
                    obj.data = {
                        delivery: $('input[name="delivery"]').val(),
                        is_legal_entity: $('input[name="legal"]').is(':checked'),
                        promo: promo
                    };
                    obj.nodes = [
                        '#basket_items',
                        '#delivery_coast',
                        '#total_summ',
                        '.total_summ_title',
                        '.field-total',
                        '#debug',
                        '.payment',
                        ".services",
                        '.test_input'
                    ];
                    obj.success = function() {
                        $('.basket_area').load('/include_areas/small_basket.php', function() {
                            counter_widget();
                        });

                        $('.wrap_container_spinner').hide();
                        try {
                            yaCounter26291919.reachGoal('discount_code');
                        } catch (e) {

                        }
                    };
                    update(obj);
                }
            });

    });

    //успешное использование купона

    // $('.success_popup_send_txt').removeClass('active');
    // setTimeout(function () {
    //     $('.success_popup_send_txt').find('.desc_popup_send_txt').find('span').html('Промокод на скидку применен!');
    //     $('.success_popup_send_txt').addClass('active');
    // }, 1000);
    //
    // setTimeout(function () {
    //     $('.success_popup_send_txt').removeClass('active');
    // }, 6000);
    ///////////////////////////////////////////////////
    //
    // $('.item_popup_send_txt').removeClass('active');
    // $('.error_popup_send_txt').find('.desc_popup_send_txt').find('span').html('Промокода не существует, или введен неверно!');
    // $('.error_popup_send_txt').addClass('active');
    //
    // setTimeout(function(){
    //     $('.item_popup_send_txt').removeClass('active');
    // }, 3000);
    //
    // return false;


    //payment on click
    $('a.link_success_order_custom').on('click', function() {
        var btnPayShow = $(this).closest('.success__page_custom').find('.sale-paysystem-yandex-button-item')
        btnPayShow.trigger('click');
        return false;
    });

    $('body').on('keydown', '#checkout-form', function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    var errorSend = false;
    var errorPaySend = false;
    var errorAgremSend = false;

    $('body').on('blur', '#input-about-name', function () {
        if ($(this).val() == '') {
            $(this).closest('.input').addClass('error');
            errorSend = true;
        } else if ($(this).val().length < 2) {
            $(this).closest('.input').addClass('error');
            errorSend = true;
        } else {
            $(this).closest('.input').removeClass('error');
            errorSend = false;
        }
    });

    $('body').on('blur', '#input-about-email', function () {
        if ($(this).val() == '') {
                $(this).closest('.input').addClass('error');
                errorSend = true;
            } else {
                var pattern = /.+@.+\..+/i;
                if ($(this).val().search(pattern) != -1) {
                    $(this).closest('.input').removeClass('error');
                    $(".text_error:not('.input-about-phone-error')").css("display", "none");
                    errorSend = false;
                } else {
                    $(this).closest('.input').addClass('error');
                    $(this).closest('.input').after("<p class='text_error'>Электронная почта указана не верно! Проверьте правильность ввода.</p>");
                    errorSend = true;
                }
            }
    });

    $('#input-about-phone').on('blur', function() {

        if ($(this).val() == '') {
                $(this).closest('.input').addClass('error');
                if ($('.input-about-phone-error').length < 1)
                    $(this).closest('.input').after("<p class='text_error input-about-phone-error'>Номер телефона не введен.</p>");
                errorSend = true;
            } else if ($(this).val()[3] == '8') {
                $(this).closest('.input').addClass('error');
                if ($('.input-about-phone-error').length < 1)
                    $(this).closest('.input').after("<p class='text_error input-about-phone-error'>Номер телефона введен не корректно.</p>");
                errorSend = true;
            } else {
                $(this).closest('.input').removeClass('error');
                $('document').find('.input-about-phone-error').remove();
                errorSend = false;
            }
    });

    //send order
    $('#order_wrapper').on('click', '.btn_send_order_process', function(a, b, c) {

        if (!$('.btn_send_order_process').is('.proc')) {
            var oldBtnText = $.trim($('.btn_send_order_process').text());
            $('.btn_send_order_process').addClass('proc').text('ОТПРАВКА...');

            if (!checkMaxSumOrder()) {
                $('.btn_send_order_process').removeClass('proc').text(oldBtnText);
                return false;
            }

            $(".text_error").css("display", "none");

            if ($('#input-about-name').val() == '') {
                $('#input-about-name').closest('.input').addClass('error');
                errorSend = true;
            } else if ($('#input-about-name').val().length < 2) {
                $('#input-about-name').closest('.input').addClass('error');
                errorSend = true;
            } else {
                $('#input-about-name').closest('.input').removeClass('error');
            }

            if ($('#input-about-phone').val() == '') {
                $('#input-about-phone').closest('.input').addClass('error');
                errorSend = true;
            } else if ($('#input-about-phone').val()[3] == '8') {
                $('#input-about-phone').closest('.input').addClass('error');
                $('#input-about-phone').closest('.input').after("<p class='text_error input-about-phone-error'>Номер телефона введен не корректно.</p>");
                errorSend = true;
            } else if ($('.js-need-confirm').length > 0) {  //Подтверждение телефонного номера
                $('#input-about-phone').closest('.input').addClass('error');
                errorSend = true;
            } else {
                $('#input-about-phone').closest('.input').removeClass('error');
                $('document').find('.input-about-phone-error').remove();
            }

            //Добавляем поле email в обязательные поля
            if ($('#input-about-email').val() == '') {
                $('#input-about-email').closest('.input').addClass('error');
                errorSend = true;
            } else {
                //валидация
                var pattern = /.+@.+\..+/i;

                if ($('#input-about-email').val().search(pattern) != -1) {
                    $('#input-about-email').closest('.input').removeClass('error');
                    $(".text_error:not('.input-about-phone-error')").css("display", "none");
                } else {
                    $('#input-about-email').closest('.input').addClass('error');
                    $('#input-about-email').closest('.input').after("<p class='text_error'>Электронная почта указана не верно! Проверьте правильность ввода.</p>");
                    errorSend = true;
                }
            }

            //Должна быть выбрана оплата
            if (!$('#delivery_payment_area .payment [name=\"payment\"]:checked').length) {
                errorPaySend = true
            }

            if (errorSend) {
                $('.item_popup_send_txt').removeClass('active');
                $('.error_popup_send_txt').find('.desc_popup_send_txt').find('span').html('Заполните обязательные поля!');
                $('.error_popup_send_txt').addClass('active');
                $('.btn_send_order_process').removeClass('proc').text(oldBtnText);

                setTimeout(function() {
                    $('.item_popup_send_txt').removeClass('active');
                }, 3000);

                return false;
            } else if (errorPaySend) {
                $('.item_popup_send_txt').removeClass('active');
                $('.error_popup_send_txt').find('.desc_popup_send_txt').find('span').html('Выберите способ оплаты!');
                $('.error_popup_send_txt').addClass('active');
                $('.btn_send_order_process').removeClass('proc').text(oldBtnText);

                setTimeout(function() {
                    $('.item_popup_send_txt').removeClass('active');
                }, 3000);

                return false;
            } else if (errorAgremSend) {
                $('.item_popup_send_txt').removeClass('active');
                $('.error_popup_send_txt').find('.desc_popup_send_txt').find('span').html('Для продолжения вы должны согласиться на обработку персональных данных');
                $('.error_popup_send_txt').addClass('active');
                $('.btn_send_order_process').removeClass('proc').text(oldBtnText);

                setTimeout(function() {
                    $('.item_popup_send_txt').removeClass('active');
                }, 3000);

                return false;
            }

            try {
                yaCounter26291919.reachGoal('confirmed_order');
            } catch (e) {

            }
        } else return false;
    });

    // Example: https://codepen.io/dadata/pen/NAZyZL
    $("#input-about-address").suggestions({
        token: "48d88f32802f15df9b9ad44dcf41c9fed9502b64",
        type: "ADDRESS",
        bounds: "city-settlement-street-house",
        count: 10,
        constraints : {
            locations : $('#city_id').val() == '78000000000' ?
                [{"kladr_id": "47"}, {"kladr_id": "78"}] :
                    $('#city_id').val() == '77000000000' ?
                [{"kladr_id": "50"}, {"kladr_id": "77"}] : {kladr_id : $('#city_id').val() + '00'},
                // kladr_id: $('#city_id').val() + '00'
        },
        onSelect: function(suggestion) {
            var address = suggestion.data, city_id = '';
            $("#region_name").val(address.region_with_type);
            if ($('[name=\"city_name\"]').val() !== suggestion.data.city && suggestion.data.city_kladr_id !== null) {
                city_id = suggestion.data.city_kladr_id;
                $.ajax({
                    url: window.order_ajax_path + '/ajax/recity.php',
                    dataType: "text",
                    data: {intLocationID : city_id.substring(0, city_id.length-2)},
                    async: false,
                    type: "post",
                    success: function (ans) {
                        window.location.reload();
                    }
                });
            }
        },
        restrict_value : true
    });

    $(document).on('change', 'input[name="dserv_blv"]', function() {
        var val = $(this).val(), boolVal = $(this).prop("checked");
        if ($('input[name="dserv[]"][value="' + val + '"]').length > 0) {
            $('input[name="dserv[]"][value="' + val + '"]').prop("checked", boolVal);
        }
    });

    $(document).on('change', 'input[name="dserv[]"]', function() {
        var val = $(this).val(), boolVal = $(this).prop("checked");
        if ($('input[name="dserv_blv"][value="' + val + '"]').length > 0) {
            $('input[name="dserv_blv"][value="' + val + '"]').prop("checked", boolVal);
        }
    });

    $(document).on('change', 'input[name="payment"]', function() {
        var self = this;
        $('.wrap_container_spinner').show();
        var id = $(this).val();
        obj = {};
        obj.url = '/include_areas/order.php';
        obj.data = {
            payment: id,
            is_legal_entity: $('input[name="legal"]').is(':checked'),
            delivery : $('.delivery li.active a').attr('href')
        };
        obj.nodes = [
            '#delivery_coast', '#total_summ', '.total_summ_title', '.field-total', '#debug', "#basket_items", ".services", '.test_input'
        ];
        obj.success = function() {
            $('.payment .options label').removeClass('active');
            $(self).parent('label').addClass('active');
            var daysStr = $(self).data('dpc');
            $('[data-dpc-days=\"dpc-str\"]').text(daysStr);
            $('.wrap_container_spinner').hide();
            counter_widget();
        };
        update(obj);

    });

    $(document).on('change', '.obreshetka_srv', function() {
        if (!$(this).prop("checked")) {
            $('input[name="payment"][value="3"]').prop('disabled', 'disabled');
            if ($('input[name="payment"][value="3"]').prop("checked")) {
                $('input[name="payment"]').each(function() {
                    var payment_val = $(this).val();
                    if (payment_val != 3 && !$(this).prop('disabled')) {
                        $('input[name="payment"][value="' + payment_val + '"]').click();
                        return false;
                    }
                });
            }
        } else {
            $('input[name="payment"][value="3"]').prop('disabled', false);
        }
    });

    plus_dop_price();

    // Если сумма больше 100 000 руб., скрываем оплату при получении
    $("#total_summ").on("DOMSubtreeModified", function() {
        var sum_text = $(this).find('.sum_price span').text().replace(' ', '');
        if (sum_text) {
            var sum = parseInt(sum_text);
            if (sum > 100000) {
                $("#l_3").addClass('disabled');

                if ($("#l_3").hasClass('active')) {
                    $("#l_2").click();
                }

            } else {
                $("#l_3").removeClass('disabled');
            }
        }
    });

    $("#total_summ, .services .price").trigger("DOMSubtreeModified");

    $('.promo .promo-text').click(function() {
        $(this).hide();
        $('.promo .promo-form').fadeIn();
    });

    // изменение маркеров на карте в зависемости от типа оплаты

    $('#delivery_payment_area').on('click','.options .tugle_payment',function() {
        if($(this).attr('id') === 'l_3') {
            for (var i in window.plasemarksHide) {
                var terminal = window.plasemarksHide[i];
                if (window.globalPlacemarkIndices[window.dataMapInfo]) {
                    var placemarkIndex = globalPlacemarkIndices[window.dataMapInfo][terminal];
                    if (globalPlacemark[placemarkIndex])
                        globalPlacemark[placemarkIndex].options.set('visible', false);
                }
            }
        }else{
            for (var i in window.plasemarksHide) {
                var terminal = window.plasemarksHide[i];
                if (window.globalPlacemarkIndices[window.dataMapInfo]) {
                    var placemarkIndex = globalPlacemarkIndices[window.dataMapInfo][terminal];
                    if (globalPlacemark[placemarkIndex])
                        globalPlacemark[placemarkIndex].options.set('visible', true);
                }
            }
        }

    });

    $('#autocomplete').autocomplete({
        serviceUrl: window.suggest,
        onSelect: function (suggestion) {
            $('input[name=city_id]').val(suggestion.data);
        }
    });

    // Проверяет, что сумма заказа не превышает максимальной (устанавливается в настройках модуля dsklad.site)
    function checkMaxSumOrder() {
        var sumOrder = parseFloat($('#total_summ .sum_price').attr('num')) || 0;
        var maxSumOrder = parseFloat(BX.message('MAX_SUM_ORDER')) || 0;

        if ((maxSumOrder > 0) && (maxSumOrder < sumOrder)) {
            showSumOrderPopup();

            return false;
        }

        return true;
    }

    checkMaxSumOrder();

    $('body').on('change update keyup', '.count input', function () {
        var value = parseInt(this.value, 10);
        var max = parseInt(this.max, 10);
        var min = parseInt(this.min, 10);

        if (value > max) {
            this.value = max;
        } else if (value < min) {
            this.value = min
        }
    });

    // Отображает попап с информацией, что большие заказы оформляются при телефонном звонке
    function showSumOrderPopup() {
        if ($('.sum-order__popup').length > 0) {
            $.fancybox({
                content: $('.sum-order__popup'),
                padding: 0,
                tpl: {
                    closeBtn: '<a title="Close" class="fancybox-item fancybox-close" href="javascript:;" style="top: 10px;"><span class="icon__cross2"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/local/templates/dsklad/images/sprite.svg#cross2"></use></svg></span></a>'
                }
            });
        }
    }

    $(document).on('mymap.eventreadyinstance', function () {

        dskladMapYndex.setMapInstanceEventsListner("overlayClickEventHandler", function (e) {
            var plasId = e.get('objectId');

            $('#input-delivery-point option').removeAttr('selected');

            $('#input-delivery-point option[value="'+plasId+'"]').prop('selected', true);

            $('#input-delivery-point').trigger('change');

        });

    });

});
