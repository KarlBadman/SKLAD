$(function () {
    $('.city_title').html($('#autocomplete').val() + ':');
    $('.scrollable .item').on('click', function () {
        var index = $(this).attr('data-terminalcode');
        $('.scrollable .item').removeClass('active');
        $(this).addClass('active');

        coordinate = dskladMapYndex.objectInstance._objectsCollection.getById(index).geometry.coordinates;
        dskladMapYndex.mapInstance.setCenter(coordinate);
        dskladMapYndex.mapInstance.geoObjects.get(0).objects.balloon.open(index);
    });

    $(document).on('mymap.eventreadyinstance', function () {
        dskladMapYndex.setMapInstanceEventsListner("overlayClickEventHandler", function (e) {
            var plasId = e.get('objectId');
            $('.scrollable .item').removeClass('active');
            var element = $('.scrollable .item[data-terminalcode="'+plasId+'"]');
            element.addClass('active');
            $('.scrollable').scrollTop($('.scrollable').scrollTop() + element.position().top - $('.scrollable').height()/2 + element.height()/6);
        });
    });
});