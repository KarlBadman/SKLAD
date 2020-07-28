/**
 * Created by Дмитрий on 13.10.2016.
 */

$(document).ready(function () {
    $('#autocomplete').autocomplete({
        serviceUrl: window.suggest,
        onSelect: function (suggestion) {
            $('input[name=city_id]').val(suggestion.data);

        }
    });

    window.cityValOld = $('#city_id_head').val();
    $('.js-city-name-element').on('focusout',  function () {
        setTimeout(function () {
            var cityValNew = $('.js-city-id-element').val();
            if (cityValOld != cityValNew) {
                window.cityValOld = cityValNew;
                $('.wrap_container_spinner').show();
                var obj = {};
                obj.intLocationID = cityValNew;
                $.ajax({
                    url: '/bitrix/components/swebs/order/ajax/recity.php',
                    dataType: "text",
                    data: obj,
                    async: false,
                    type: "post",
                    success: function (ans) {
                        // alert(ans);
                        // var separator = (window.location.href.indexOf("?")===-1)?"?":"&";
                        // window.location.href = window.location.href + separator + "showDelivery=Y";
                        window.location.reload();

                    }
                });
            }
        }, 300);
    });


});