/**
 * Created by Дмитрий on 15.11.2016.
 */
$(function () {
    window.cityValOld = $('#city_id').val();
    $(document).on('focusout', '#autocomplete', function () {
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

    $('.city_title').html($('#autocomplete').val() + ':');
});