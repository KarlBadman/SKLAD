/**
 * Created by Дмитрий on 23.09.2016.
 */

function update(obj) {
    $.ajax({
        url: obj.url,
        dataType: "text",
        data: obj.data,
        async: true,
        type: "post",
        success: function (ans) {
            ans = '<div>' + ans + '</div>';
            console.log(ans);
            $(obj.nodes).each(function () {
                var node = this.concat();
                $(node).html($(ans).find(node).html());
            });
            obj.success();
        }
    });
}

$(document).ready(function () {

    // delete item
    $('#order_wrapper').on('click', 'a.remove_basket_items', function () {
        $('.wrap_container_spinner').show();
        var obj = {};
        obj.id = $(this).attr('href');
        console.log('del');
        console.log(obj.id);
        $.ajax({
            url: window.order_ajax_path + '/ajax/del.php',
            dataType: "text",
            data: obj,
            async: true,
            type: "post",
            success: function (ans) {
                if (ans == 'ok') {
                    console.log(ans);
                    //window.location.reload();
                    obj = {};
                    obj.url = '/include_areas/order.php';
                    obj.data = {
                        delivery: $('input[name="delivery"]').val()
                    };
                    obj.nodes = [
                        '#basket_items',
                        '#total_summ',
                        '#total_summ_title',
                        '.field-total'
                    ];
                    obj.success = function () {
                        $('.basket_area').load('/include_areas/small_basket.php', function () {
                            counter_widget();
                            $('.wrap_container_spinner').hide();
                        });
                    };
                    update(obj);
                    if($("#basket_items").children().length == "1"){
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
    $('#order_wrapper').on('update focusout', 'input.order_quantity', function () {
        $('.wrap_container_spinner').show();
        var obj = {};
        obj.id = $(this).attr('product_id');
        obj.q = $(this).val();
        console.log(obj.id);
        console.log(obj.q);
        $.ajax({
            url: window.order_ajax_path + '/ajax/add.php',
            dataType: "text",
            data: obj,
            async: true,
            type: "post",
            success: function (ans) {
                if (ans == 'ok') {
                    obj = {};
                    obj.url = '/include_areas/order.php';
                    obj.data = {
                        delivery: $('input[name="delivery"]').val()
                    };
                    obj.nodes = [
                        '#basket_items',
                        '#total_summ',
                        '#total_summ_title',
                        '.field-total'
                    ];
                    obj.success = function () {
                        $('.basket_area').load('/include_areas/small_basket.php', function () {
                            counter_widget();
                            $('.wrap_container_spinner').hide();
                        });
                    };
                    update(obj);
                } else {
                    console.log(ans);
                    $('.wrap_container_spinner').hide();
                }
            }
        });
    });


    // add service
    $('#order_wrapper').on('click', '.order_service', function () {
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
            success: function (ans) {
                if (ans == 'ok') {
                    //window.location.reload();
                    obj = {};
                    obj.url = '/include_areas/order.php';
                    obj.data = {
                        delivery: $('input[name="delivery"]').val()
                    };
                    obj.nodes = [
                        '#total_summ',
                        '#total_summ_title',
                        '.field-total'
                    ];
                    obj.success = function () {
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
    $('#order_wrapper').on('click', '.delivery_service', function () {
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
            async: true,
            type: "post",
            success: function (ans) {
                if (ans == 'ok') {
                    //window.location.reload();
                    obj = {};
                    obj.url = '/include_areas/order.php';
                    obj.data = {
                        delivery: $('input[name="delivery"]').val()
                    };
                    obj.nodes = [
                        '#total_summ',
                        '#total_summ_title',
                        '.field-total'
                    ];
                    obj.success = function () {
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

    // signin
    $('#order_wrapper').on('click', '.order_signin', function () {
        $('.actions .signin.fancybox').trigger('click');
        return false;
    });

    // change city
    window.cityValOld = $('#city_id').val();
    $('#order_wrapper').on('focusout', '#autocomplete', function () {
        setTimeout(function () {
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
                    success: function (ans) {
                        // alert(ans);
                        window.location.reload();
                    }
                });
            }
        }, 300);
    });

    // change terminal address
    $('#order_wrapper').on('change', '#input-delivery-point', function () {
        var option = $(this).find('option:selected').val();
        $('.terminal_address').hide();
        $('#' + option).show();
    });

    // input to session
    $('#order_wrapper').on('change', '.session', function () {
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
    $('#order_wrapper').on('change', 'a:not([target])', function () {
        $('.wrap_container_spinner').show();
        var id = $(this).attr('href');
        $('input[name="delivery"]').val(id);

        obj = {};
        obj.url = '/include_areas/order.php';
        obj.data = {
            delivery: id
        };
        obj.nodes = [
            '#delivery_coast',
            '#total_summ',
            '#total_summ_title',
            '.field-total',
            '#debug'
        ];
        obj.success = function () {
            $('.wrap_container_spinner').hide();
        };
        update(obj);
    });

    // promo
    $('#order_wrapper').on('click', '#promo', function () {
        $('.wrap_container_spinner').show();
        var promo = $('input[name=promo]').val();
        obj = {};
        obj.url = '/include_areas/order.php';
        obj.data = {
            delivery: $('input[name="delivery"]').val(),
            promo: promo
        };
        obj.nodes = [
            '#delivery_coast',
            '#total_summ',
            '#total_summ_title',
            '.field-total',
            '#debug'
        ];
        obj.success = function () {
            $('.wrap_container_spinner').hide();
        };
        update(obj);

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
    $('a.link_success_order_custom').on('click', function () {
        var btnPayShow = $(this).closest('.success__page_custom').find('.sale-paysystem-yandex-button-item')
        btnPayShow.trigger('click');
        return false;
    });

    //send order
    $('#order_wrapper').on('click', '.btn_send_order_process', function() {
				if(!$('.btn_send_order_process').is('.proc')){
					$('.btn_send_order_process').addClass('proc').text('ОТПРАВКА...');
					var errorSend = false;
					if ($('#input-about-name').val() == '') {
							$('#input-about-name').closest('.input').addClass('error');
							errorSend = true;
					} else {
							$('#input-about-name').closest('.input').removeClass('error');
					}
					if ($('#input-about-phone').val() == '') {
							$('#input-about-phone').closest('.input').addClass('error');
							errorSend = true;
					} else {
							$('#input-about-phone').closest('.input').removeClass('error');
					}

					if (errorSend) {
							$('.item_popup_send_txt').removeClass('active');
							$('.error_popup_send_txt').find('.desc_popup_send_txt').find('span').html('Заполните обязательные поля!');
							$('.error_popup_send_txt').addClass('active');

							setTimeout(function () {
									$('.item_popup_send_txt').removeClass('active');
							}, 3000);

							return false;
					}
				}
				else
					return false;
    });


});
