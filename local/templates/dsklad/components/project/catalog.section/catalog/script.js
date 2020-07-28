$(document).ready(function () {
    window.detail_ajax_path="/bitrix/templates/dsklad/components/bitrix/catalog.element/catalog/ajax";
    // remove
    $(document).on('click', '.remove a', function () {
        var obj = {};
        obj.item_id = $(this).attr('data');
        $.ajax({
            url: window.detail_ajax_path + '/del.php',
            dataType: "text",
            data: obj,
            async: false,
            type: "post",
            success: function (ans) {
                purepopup.closePopup();
                $('.basket_area').load('/include_areas/small_basket.php');
            }
        });

        return false;
    });

    // item minus
    $(document).on('click', '#item-minus, #item-minus_big', function () {
        var q = parseInt($('#quantity').text());
        var obj = {};
        if (q == 1) {
            obj.item_id = $(this).attr('data');
            $.ajax({
                url: window.detail_ajax_path + '/del.php',
                dataType: "text",
                data: obj,
                async: false,
                type: "post",
                success: function (ans) {
                    purepopup.closePopup();
                    $('.basket_area').load('/include_areas/small_basket.php');
                }
            });
        } else if (q > 1) {
            obj.item_id = $(this).attr('data');
            obj.operation = 'minus';
            $.ajax({
                url: window.detail_ajax_path + '/quantity.php',
                dataType: 'json',
                data: obj,
                async: false,
                type: "post",
                success: function (ans) {
                    if (ans.status == 'ok') {
                        $('#quantity').html(ans.q);
                        $('#total_price').html(ans.tp);
                        $('#quantity_big').val(ans.q + ' шт.');
                        $('.basket_area').load('/include_areas/small_basket.php');
                    }
                },
                error: function (err) {
                    $('body').html(err.responseText);
                }
            });
        }

        return false;
    });

    // item plus
    $(document).on('click', '#item-plus, #item-plus_big', function () {
        var obj = {};
        obj.item_id = $(this).attr('data');
        obj.operation = 'plus';
        $.ajax({
            url: window.detail_ajax_path + '/quantity.php',
            dataType: 'json',
            data: obj,
            async: false,
            type: "post",
            success: function (ans) {
                if (ans.status == 'ok') {
                    $('#quantity').html(ans.q);
                    $('#total_price').html(ans.tp);
                    $('#quantity_big').val(ans.q + ' шт.');
                    $('.basket_area').load('/include_areas/small_basket.php');
                }
            },
            error: function (err) {
                $('body').html(err.responseText);
            }
        });

        return false;
    });

	//офрмлен предзаказ
    $('#preorder-form').on('submit', function(e){
		e.preventDefault();
		var form = $(this);
		$.post(
			'/ajax/preorder.php',
			form.serialize(),
			function(data){
				$('.success_popup_send_txt').removeClass('active');
				$('.error_popup_send_txt').removeClass('active');
				try {
                    ym(26291919, 'reachGoal', 'preorder');
					ga('send','event','pre_order','preorder');
				} catch(e){}
				try {
					if (data.status) {
                        purepopup.closePopup();
						form[0].reset()
						setTimeout(function () {
							$('.success_popup_send_txt').find('.desc_popup_send_txt').find('span').html('Благодарим за заявку. Ждите звонка!');
							$('.success_popup_send_txt').addClass('active');
						}, 1000);

						setTimeout(function () {
							$('.success_popup_send_txt').removeClass('active');
						}, 6000);
					} else {
						
						setTimeout(function () {
							$('.error_popup_send_txt').find('.desc_popup_send_txt').find('span').html('Ошибка сохранения данных!');
							$('.error_popup_send_txt').addClass('active');
						}, 1000);

						setTimeout(function () {
							$('.error_popup_send_txt').removeClass('active');
						}, 6000);
					}
				} catch(e){}
			},
			'json'
		);
	});
});