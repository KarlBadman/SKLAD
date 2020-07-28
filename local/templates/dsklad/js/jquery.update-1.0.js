/**
 * Created by Дмитрий Карих (Dmitry Karikh aka demoriz) on 28.09.2016.
 */
$.fn.update = function (options) {
    var options = jQuery.extend({
        obj: {},
        url: '',
        node: '',
        succes: function () {
        }
    }, options);

    return this.each(function () {
        var self = $(this);
        $.ajax({
            url: options.url,
            dataType: "text",
            data: options.obj,
            async: false,
            type: "post",
            success: function (ans) {
                ans = '<div>' + ans + '</div>';
                self.html($(ans).find(options.node).html());
                options.succes();
            }
        });
    });
};