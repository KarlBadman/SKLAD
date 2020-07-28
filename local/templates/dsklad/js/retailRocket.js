window.rrAddToBasket =function (productId = false,quantity = 1) {
    if(!productId) return 'error: no product id !!!';

    var data = {
        'productId':productId,
        'quantity':quantity,
        'sessionId': BX.bitrix_sessid(),
    };

    $.ajax({
        url: '/ajax/addProduct.php',
        method:'POST',
        data:data,
        success: function (msg) {
            BX.onCustomEvent('OnBasketChange');
            return msg.trim();
        }
    });

    return false;
};

window.rrAddToFavorite = function (group_id) {
    console.log(group_id);
    $.ajax({
        type: "POST",
        url: '/ajax/addFavorite.php',
        data: {'productId': group_id},
        success: function (msg) {
            return msg.trim();
        }
    });

    return false;
};