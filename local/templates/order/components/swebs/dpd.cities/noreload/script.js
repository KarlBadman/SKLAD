/**
 * Modify by Дмитрий (o2k) on 04.08.2017.
 */

$(document).ready(function () {
    $('#autocomplete').autocomplete({
        serviceUrl: window.suggest,
        minChars:3,
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
				$('#city_id').trigger('change');
            }
        }, 300);
    });
});