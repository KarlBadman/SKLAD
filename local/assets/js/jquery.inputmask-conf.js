/*
 * Сквозные конфиги масок для телефонов
 */
(function ($) {
    var maskList = $.masksSort($.masksLoad("/local/assets/phone-codes.json"), ['#'], /[0-9]|#/, "order");
    window.maskOpts = {
        inputmask: {
            definitions: {
                '#': {
                    validator: "[0-9]",
                    cardinality: 1
                }
            },
            //clearIncomplete: true,
            showMaskOnHover: false,
            autoUnmask: true
        },
        match: /[0-9]/,
        replace: '#',
        list: maskList,
        listKey: "mask"
    };
})(jQuery);