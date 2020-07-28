
var userAutologinForm = {
    $: {}
};
/*
userAutologinForm.vars = function() {
    userAutologinForm.$.sectionBlock = $('.section-login');
    userAutologinForm.$.form = userAutologinForm.$.sectionBlock.find('form');
    userAutologinForm.$.emailInput = userAutologinForm.$.form.find('input[name="email"]');
    userAutologinForm.$.submitButton = userAutologinForm.$.form.find('button[type="submit"]');
    userAutologinForm.$.dataBlock = userAutologinForm.$.form.find('.fields-block');

    userAutologinForm.$.loaderBlock = userAutologinForm.$.sectionBlock.find('.loader-block');
    userAutologinForm.$.errorBlock = userAutologinForm.$.sectionBlock.find('.error-block');
    userAutologinForm.$.successBlock = userAutologinForm.$.sectionBlock.find('.ready-block');
    userAutologinForm.$.successEmail = userAutologinForm.$.successBlock.find('strong');
};

userAutologinForm.handlerss = function() {
    userAutologinForm.$.submitButton.click(function(e) {
        e.preventDefault();

        var email = userAutologinForm.$.emailInput.val().toString().trim();

        userAutologinForm.$.emailInput.parent().removeClass('error');
        if (email === '' || !/^[-._+А-Яа-яA-Za-z0-9]*@(?:[A-zА-яА-Яа-яA-Za-z0-9][-А-Яа-яA-Za-z0-9]*\.)+[А-Яа-яA-Za-z]{2,6}$/.test(email)) {
            userAutologinForm.$.emailInput.parent().addClass('error');
            return false;
        }

        userAutologinForm.sendRequest();
    });
};

userAutologinForm.init = function() {
    userAutologinForm.vars();
    userAutologinForm.handlerss();
};

userAutologinForm.sendRequest = function() {
    var requestData = new FormData(userAutologinForm.$.form[0]);

    $.ajax({
        url: location.href,
        method: 'POST',
        dataType: 'json',
        data: requestData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            userAutologinForm.$.dataBlock.hide();
            userAutologinForm.$.loaderBlock.show();
        },
        success: function(data) {
            userAutologinForm.$.loaderBlock.hide();
            if (data.status === 'ok') {
                userAutologinForm.$.successEmail.text(data.email);
                userAutologinForm.$.successBlock.addClass('active');
            } else if (data.status === 'error_email') {
                userAutologinForm.$.emailInput.parent().addClass('error');
            } else {
                userAutologinForm.$.errorBlock.addClass('active');
            }
        }
    });
};

if (window.frameCacheVars !== undefined) {
    BX.addCustomEvent('onFrameDataReceived' , function() {
        userAutologinForm.init();
    });
} else {
    BX.ready(function() {
        userAutologinForm.init();
    });
}


$('.section-login').each(function () {
    $('.section-login .button').on('click', function (e) {
        e.preventDefault();
        $(this).parents('.section-login').addClass('form-ready');
    });
});
*/

$( document ).ready(function() {
    $('.section-login').each(function () {
        $('.section-login .button').on('click', function (e) {
            e.preventDefault();
        });
    });

    $('[type="tel"]').inputmasks(maskOpts);
    if (typeof window.saleConfirmPhone != "undefined") {
        window.saleConfirmPhone.clickSmsGo();
        window.saleConfirmPhone.init();
    }
});

