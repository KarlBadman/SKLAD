/**
 * Created by Дмитрий on 13.10.2016.
 */

$(document).ready(function () {
    $('.points #autocomplete').autocomplete({
        serviceUrl: window.suggest,
        minChars:3,
        onSelect: function (suggestion) {
            $('input[name=city_id]').val(suggestion.data);
            clickCityAutoComplete(suggestion.data);
        }
    });

    window.cityValOld = get_cookie('DPD_CITY');

    $('.points #autocomplete[name="city_name"]').keyup(function(event){
        if(event.keyCode == 13){
            $('.autocomplete-suggestion:first').trigger('click');
        }
        return false;
    });
});

    function clickCityAutoComplete(city_id) {
            if (cityValOld != city_id) {
                window.cityValOld = city_id;
                $('.wrap_container_spinner').show();
                var obj = {};
                obj.intLocationID = city_id;
                $.ajax({
                    url: window.order_ajax_path + '/ajax/recity.php',
                    dataType: "text",
                    data: obj,
                    async: false,
                    type: "post",
                    success: function (ans) {
                        $('.points form').submit();
                    }
                });
            }
    }