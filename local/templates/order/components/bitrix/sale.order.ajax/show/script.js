(function () {
    $('html').addClass((isMobile.any ? 'mobile' : 'desktop'));

    window.dsCheckout = {
        selectors: {
            recieverBtn: ".js-reciever",
            recieverBlock: ".js-reciever-block",
            recieverBlockClose: ".js-reciever-block-close",

            authorizClass: ".js-authoriz",

            slickInit: ".js-slider-checkout",

            inputSame: "[data-input-name=\"same\"]",
            inputSameUser: "[data-name=\"some_input\"]",
        },
        phpParams:"",

        jsReciever: function () { //Добавление другого получателя
            var recieverBtn = document.querySelector(dsCheckout.selectors.recieverBtn),
                recieverBlock = document.querySelector(dsCheckout.selectors.recieverBlock),
                recieverBlockClose = document.querySelector(dsCheckout.selectors.recieverBlockClose),
                inputSame = document.querySelector(dsCheckout.selectors.inputSame);

            if (document.querySelector(dsCheckout.selectors.authorizClass)) {
                recieverBtn.onclick = function () {
                    recieverBlock.classList.remove('hidden');
                    recieverBtn.classList.add('hidden');
                    $(dsCheckout.selectors.inputSameUser).attr("required", "");
                    inputSame.value ='N';
                };

                recieverBlockClose.onclick = function () {
                    recieverBlock.classList.add('hidden');
                    recieverBtn.classList.remove('hidden');
                    $(dsCheckout.selectors.inputSameUser).removeAttr("required");
                    inputSame.value ='Y';
                }
            }
        },

        jsDeliveryChosen : function() { // Выбор типа доставки
            var deliveryParent = $('.delivery-info__item');
            var deliveryResult = $('.delivery-info__item .delivery-info-result');
            var self = this;
            $('.ds-checkout-form').on('click', '.js-delivery-radio', function () {
                var active = $(this).parents('.delivery-info__item');
                deliveryParent.removeClass('active');
                deliveryResult.addClass('hidden');
                $('.subheader').removeClass('hidden');
                $('.ds-checkout-form__item .ds-price').addClass('hidden');
                active.addClass('active');
                $('.ds-checkout-form__item').removeClass('error');
                $('body').trigger("setDelivery", $(this).siblings('label').children('.header').contents()[0]);
            });

            $('.ds-modal').on('click', '.delivery-courier-ok', function () {
                var _this = $(this);
                var current_form = _this.parent();
                var address = current_form.find('#courier-delivery-address');
                var office = current_form.find('#courier-delivery-office');
                if(address.val() != '') {
                    address.removeClass('error');
                    office.removeClass('error');

                    var deliveryId = _this.attr('data-delivery-id');
                    $('input[value="' + deliveryId + '"]').prop('checked', true);
                    $('body').trigger("setDelivery", $('input[value="' + deliveryId + '"]').siblings('label').children('.header').contents()[0]);
                    current_form.find('.delivery-info__item').removeClass('active');
                    current_form.find('.delivery-info__item[data-delivery-id="' + deliveryId + '"]').addClass('active');
                    purepopup.closePopup();

                    self.getOrderJson();
                }else{
                    if(address.val() == '')
                        address.focus();
                        address.addClass('error');
                }
            });

            $('.ds-modal').on('click', '.js-courier-delivery-add', function () {
                var _this = $(this);
                if(_this.hasClass('active')){
                    _this.removeClass('active').children('.delivery-checked-text').html('Добавить');
                    _this.children('input').val('N');
                }else{
                    _this.addClass('active').children('.delivery-checked-text').html('Удалить');
                    _this.children('input').val('Y');
                }
            });

            $('.ds-modal').on('click', '.js-courier-delivery-floor', function () {
                var _this = $(this);
                if(_this.hasClass('active')){
                    $('.js-form-office').removeClass('hidden');
                }else{
                    $('.js-form-office').addClass('hidden');
                    $('.js-form-office input').val('').focus();
                }
            });

        },

        jsPaymentChosen: function () { // Платежные системы
            $('.ds-checkout-form').on('click', '.js-payment', function () {
                var elem = $(this);
                $('.payment-info__item').removeClass('active');
                elem.addClass('active');
            });
        },

        jsPaymentMore: function() {
            $('.payment-info').on('click', '.js-payment-more-btn', function () {
                var elem = $(this);
                var scrollElem = $('html, body');
                elem.prev().slideToggle();
                elem.toggleClass('more');

                if (elem.hasClass('more')) {
                    elem.text('Больше способов оплаты');

                    if (scrollElem.hasClass('mobile')) {
                        scrollElem.animate({
                            scrollTop: $("#checkout-payment").offset().top
                        }, 1000)
                    }

                } else {
                    elem.text('Меньше способов оплаты');
                }
            });
        },

        jsPromo: function () { // Промокод
            var self = this;
            $('.ds-checkout-form').on('click', '#promo-submit', function (){
                if( $('#promo').val() !='' ) {
                    $('.promo-info').removeClass('error');
                    var data = {
                        'coupon': $('#promo').val(),
                        'sessionId': $('#sessid').val(),
                    };
                    $.ajax({
                        type: "POST",
                        url: "/local/templates/order/components/bitrix/sale.order.ajax/show/ajax/setCoupon.php",
                        data: data,
                        success: function (msg) {
                            if(msg == 'Y'){
                                $('.promo-info').removeClass('error').addClass('active');
                                $('#promo-submit').attr('disabled','disabled');
                            }else{
                                $('.promo-info').addClass('error');
                            }
                            self.getOrderJson();
                        }
                    });
                }else{
                    $('.promo-info').addClass('error');
                }
                return false;
            });
        },

        jsSlick: function () { // Слайдер
            $(dsCheckout.selectors.slickInit).slick({
                    appendArrows: ('.js-slider-arrows'),
                    prevArrow: "<div class='ds-slider-arrows prev'></div>",
                    nextArrow: "<div class='ds-slider-arrows next'></div>",
                    infinite: false,
                    arrows: true,
                    dots: false,
                    slidesToShow: 5,
                    slidesToScroll: 5,
                    responsive: [
                        {
                            breakpoint: 1000,
                            settings: {
                                slidesToShow: 7,
                                slidesToScroll: 7,
                            }
                        },
                        {
                            breakpoint: 800,
                            settings: {
                                slidesToShow: 6,
                                slidesToScroll: 6,
                            }
                        },
                        {
                            breakpoint: 640,
                            settings: {
                                slidesToShow: 5,
                                slidesToScroll: 5,
                            }
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: 4,
                                slidesToScroll: 4,
                            }
                        },
                        {
                            breakpoint: 400,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 3,
                            }
                        }
                    ]
                }
            );
        },

        jsCheckoutModals : function() { // всплывающие окна

                function modalHeight() { // для определения высоты окна по клику
                    var height = (window.innerHeight - 64);
                    $('.mobile .ds-modal__inner').css('height', height + 'px');
                    $('.mobile .bx-yandex-map').css('height', height + 'px');
                }
                function modalResize() { // для определения высоты окна при смене ориентации
                    var height = (window.innerWidth - 100); // width - из-за специфичной работы события orientationchange
                    var width = (window.innerHeight + 120); // height - из-за специфичной работы события orientationchange
                    $('.mobile .ds-modal__inner').css('height', height + 'px');
                    $('.mobile .bx-yandex-map').css('height', (height * 2) + 'px').css('width', width + 'px');
                }


            $('.ds-checkout-form').on('click', '.js-checkout-modal, .js-delivery-change, .js-ds-modal', function () {
                modalHeight();

                $(window).on('orientationchange', function() {
                    modalResize();
                });
            });


            $('.ds-checkout-form').on('click', '.js-checkout-modal, .js-delivery-change', function (e) {
                    purepopup.ajaxToModal(false, purepopup.modalFilling, $(this).closest('.delivery-info__item').data('width'), $('.ds-checkout-form').find('#' + $(this).data('target')));

                    if($('#OrderMap').is(':visible')){
                        dskladMapYndex.mapInstance.container.fitToViewport();
                        dskladMapYndex.alignCardSize();
                    }
                    return false;
                });
        },

        inputDeliveryChecked:function(){ // выбор доставки
            $('.ds-checkout-form').on('change', '.js-delivery-radio', function () {
                $('.delivery-info__item').removeClass('active');
                $(this).closest('.delivery-info__item').addClass('active');
            });
        },

        mapYandex: function(){ // работа с картой
            var self = this;
            $(document).on('click','.delivery-point-ok',function () {
                $('input[data-input-name="terminal_code"]').val($(this).data('terminal-id'));
                $('input[name="DELIVERY_ID"][data-input-type="POINT"]').prop('checked', true);
                $('body').trigger("setDelivery", $('input[name="DELIVERY_ID"][data-input-type="POINT"]').siblings('label').children('.header').contents()[0]);
                if($(this).attr('data-is-terminal') == 'Y'){
                    $('select[data-name="not_terminal"] option[data-city="N"]').prop('checked', true);
                }else{
                    $('select[data-name="not_terminal"] option[data-city="'+$(this).attr('data-city')+'"]').attr('selected','true');
                }
                self.getOrderJson();
                purepopup.closePopup();
            });
        },

        reloadMap: function (data) { //перестраеваем точки на карте
            var plas = [], searchControlPlas = [];
            if (!!data.mapParams.PLACEMARKS) {
                data.mapParams.PLACEMARKS.forEach(function (item, i, arr) {
                    if (item.IS_TERMINAL == 'Y') {
                        color = "#1ab500";
                    } else {
                        color = "#1d97ff";
                    }
                    plas.push({
                        geometry: {
                            coordinates: [item.LAT, item.LON],
                            type: "Point",
                        },
                        id: item.TERMINAL,
                        options: {
                            iconColor: color,
                            preset: "islands#greenDotIcon",
                        },
                        type: "Feature",
                        properties: {
                            search_title : item.SEARCH_TITLE,
                            search_description : item.SEARCH_DESCRIPTION,
                            balloonContent: item.TEXT,
                            clusterCaption: item.ADDRESSES,
                            hintContent: item.ADDRESSES,
                        }
                    });
                });

                ymaps.ready(function () {
                    dskladMapYndex.objectInstance.removeAll();
                    
                    if (plas !== false) {
                        var objectMapPlac = {
                            type: "FeatureCollection",
                            features: plas,
                        }
                        dskladMapYndex.plas = objectMapPlac;
                        dskladMapYndex.objectInstance.add(JSON.stringify(objectMapPlac));
                        dskladMapYndex.alignCardSize();
                        dskladMapYndex.setSearchMap();
                    }
                });
            }
        },

        doNotChangeInput: function(){ // запретить вводить данные в input
            $('.ds-checkout-form').on('keydown', '[data-name="phone_auth"]', function(e){
                e.preventDefault()
            });
        },

        clickOrderJson:function(){ // элементы при клике по которым происходит обновление шаблона

            var self = this;
            $('.ds-checkout-form').on('change', 'input[name="PAY_SYSTEM_ID"]', function () {
                $('body').trigger("setPayment");
                self.getOrderJson();
            });

            $('.ds-checkout-form').on('click', '.js-taxpayer', function (e) {
                if($('input[name="PERSON_TYPE"]').val() == 1) {
                    $('input[name="PERSON_TYPE"]').val(2);
                    $('input[name="PERSON_TYPE_OLD"]').val(1);
                } else {
                    $('input[name="PERSON_TYPE"]').val(1);
                    $('input[name="PERSON_TYPE_OLD"]').val(2);
                }
                self.getOrderJson(false,true);
                return false;
            });

            $('.ds-checkout-form').on('click', '.delivery_label[data-lable-name="STOCK"]', function () {
                $('input#STOCK').prop('checked', true);
                self.getOrderJson();
            });

            $('.ds-checkout__content').on('click', '.js-checkout-validate', function () {
                self.getOrderJson(true);
                //return false;
            });
        },

        getOrderJson: function(submit = false,personType = false){ // механизм обновления данных заказа

            var self = this;
            var inputs = $('#ORDER_FORM :input');
            var values = {};
            var error = false;

            inputs.each(function() {
                if(this.type != 'checkbox' && this.type != 'radio'){
                    values[this.name] = $(this).val();
                }else if((this.type == 'checkbox' || this.type == 'radio')  && this.checked == true){
                    values[this.name] = $(this).val();
                }
            });

            if(!values.DELIVERY_ID){
                error = true;
                $('#checkout-delivery').addClass('error');
            }else{
                $('#checkout-delivery').removeClass('error');
            }

            values.is_ajax_post = 'Y';

            if(personType) delete values.PAY_SYSTEM_ID;

            if($("input").is("#taxpayer-number")){
                $('#taxpayer-number').inputmask("isComplete");
            }

            if(submit) {
                error = !$('#ORDER_FORM').parsley().validate();

                if(values.NO_ADDRESSES == 'Y') {
                    $('#checkout-delivery').addClass('error');
                    error = true;
                }
                values.confirmorder = 'Y';
            }

            if(error) return false;

            $.ajax({
                type: "POST",
                url: window.location,
                data: values,
                beforeSend: function() {
                    $('.spinner').removeClass('hidden');
                },
                success: function (msg) {
                    msg = JSON.parse(msg);
                    if(submit && !!msg.redirect) {
                        document.location.href = msg.redirect;
                        return false;
                    }
                    if(!!msg.ACCOUNT_NUMBER){
                        $('body').trigger("finishCheckout", msg.ACCOUNT_NUMBER);
                        setTimeout(function() {
                            window.location.href = "/order/thankyou/"+msg.ACCOUNT_NUMBER;
                        }, 1000);
                    }
                    self.handlebarsAction(msg);
                    self.reloadMap(msg);
                    $('.spinner').addClass('hidden');
                }
            });
        },

        handlebarsAction: function(data){ // использование шаблона "Усов"
            var self = this;
            $.ajax({
                type: "POST",
                url: '/local/templates/order/components/bitrix/sale.order.ajax/show/handlebars.php',
                data: '',
                success: function (msg) {
                    msg = JSON.parse(msg);
                    for (key in msg) {
                        var oldElement = document.querySelector('div[data-block-name="'+key+'"]');
                        if(oldElement) {
                            var template = Handlebars.compile(msg[key]);
                            var append = template(data);
                            while (oldElement.firstChild) {
                                oldElement.removeChild(oldElement.firstChild);
                            }
                            oldElement.insertAdjacentHTML('afterbegin', append);
                        }
                    }
                    self.maskPhone();
                    self.maskInn();
                    self.jsReciever();
                    self.jsPaymentMore();
                    if(data.COUNTRY_CODE == 'RU') {
                        self.delivery2doorAddressSuggestion();
                    }
                    if(typeof  window.saleConfirmPhone == 'object') window.saleConfirmPhone.init();
                    self.phoneConfirmedPushToAnalytics();
                    if($('[data-order-baseinfo=\"user-name\"]').val() && $('[data-order-baseinfo=\"user-email\"]').val()){
                        self.onLoadTriggerAnalytics();
                    }
                }
            });

        },

        handlebarsHelpers: function(){ // различные помошники для "Усов"
            Handlebars.registerHelper('iff', function(a, operator, b, opts) {
                var bool = false;
                switch(operator) {
                    case '==':
                        bool = a == b;
                        break;
                    case '!=':
                        bool = a != b;
                        break;
                    case '>':
                        bool = +a > +b;
                        break;
                    case '<':
                        bool = +a < +b;
                        break;
                    default:
                        throw "Unknown operator " + operator;
                }

                if (bool) {
                    return opts.fn(this);
                } else {
                    return opts.inverse(this);
                }
            });

            Handlebars.registerHelper('NF', function(number) {
                return new Intl.NumberFormat('ru-RU').format(number);
            });

            Handlebars.registerHelper('lowercase', function(str) {
                if (typeof str === 'string') {
                    return str.toLowerCase();
                }
            });

            Handlebars.registerHelper('get_length', function (obj) {
                return obj.length;
            });
        },

        onLoadTriggerAnalytics : function() {
            $('body').trigger('setBaseInfo');
        },

        validateFieldsAndPushToAnalytics: function(){
            var self = this;
            $('body').on('focusout', '[data-order-baseinfo]', function() {
                if($('[data-order-baseinfo=\"user-name\"]').val() && $('[data-order-baseinfo=\"user-email\"]').val() && $('#ORDER_FORM').parsley().validate()){
                    self.onLoadTriggerAnalytics();
                }
            });
        },

        phoneConfirmedPushToAnalytics: function(){ // отправка данных при заполненном номере
            if($('.js-authoriz [data-name="phone_auth"]').val()){
                $('body').trigger("phoneConfirmed");
            }
        },

        maskPhone: function(){ // маска для телефона
            $('[type="tel"]').inputmasks(maskOpts);
        },

        maskInn: function(){ // маска для ИНН
            $('#taxpayer-number').inputmask('999999999999');
        },

        delivery2doorAddressSuggestion : function () {
            // var cityValOld = get_cookie('DPD_CITY');
            var self = this;

            var cityValOld = $('[name="cityID"]').val();
            $("input[data-name='is_address']").suggestions({
                token: "48d88f32802f15df9b9ad44dcf41c9fed9502b64",
                type: "ADDRESS",
                bounds: "city-settlement-street-house",
                count: 10,
                constraints: cityValOld == '78000000000' ?
                    [
                        {locations: { kladr_id: '47' } },
                        {locations: { kladr_id: '78' } }
                    ] :
                    cityValOld == '77000000000' ?
                        [
                            {locations: { kladr_id: '50' } },
                            {locations: { kladr_id: '77' } }
                        ] :
                        [
                            {locations: { kladr_id: cityValOld + '00' } }
                        ],

                onSelect: function(suggestion) {

                    if ($('[data-block-name=\"city_name\"]').val() !== suggestion.data.city && (suggestion.data.city_kladr_id !== null || suggestion.data.kladr_id !== null)) {
                        var city_id = suggestion.data.city_kladr_id ? suggestion.data.city_kladr_id : suggestion.data.kladr_id;
                        self.clickCityAutoComplete(city_id.substring(0, city_id.length-2));
                    }

                    if ($('[data-block-name=\"city_name\"]').val() !== suggestion.data.settlement && suggestion.data.settlement_kladr_id !== null) {
                        var city_id = suggestion.data.settlement_kladr_id;
                        self.clickCityAutoComplete(city_id.substring(0, city_id.length-2));
                    }

                },
                restrict_value : true,
                onSuggestionsFetch : function (suggestions) {
                    return suggestions.filter(function(suggestion) {
                        return suggestion.data.kladr_id !== "78000000000183000";
                    });
                }
            });
        },

        clickCityAutoComplete: function (city_id) {
            var _this = this;
            var cityValOld = get_cookie('DPD_CITY');
            if (cityValOld != city_id) {
                var obj = {};
                obj.intLocationID = city_id;
                $.ajax({
                    url: '/local/components/dsklad/crutch_for_order/ajax/recity.php',
                    dataType: "text",
                    data: obj,
                    async: false,
                    type: "post",
                    success: function (ans) {
                        _this.getOrderJson();
                    }
                });
            }
        },

        init: function (one = false) {
            var self = this;
            this.jsReciever(); // Добавление другого получателя
            this.jsDeliveryChosen(); // Выбор типа доставки
            this.jsPaymentChosen(); // Выбор типа оплат
            this.jsPaymentMore(); // Раскрытие дополнительных типов оплат
            this.jsPromo(); // Промо успех или неудача
            if(one) {
                this.jsSlick(); // слайдер в корзине
                $(document).on('mymap.eventreadyinstance',function () {
                    self.mapYandex();
                });
            }
            this.handlebarsHelpers();// помощники усов
            this.jsCheckoutModals(); // модалки на странице checkout
            this.inputDeliveryChecked(); // выбор доставки
            this.doNotChangeInput(); // запретить вводить данные в input
            this.clickOrderJson(); // элементы при клике по которым происходит обновление шаблона
            this.maskPhone(); // маска для телефона
            this.maskInn(); // маска для инн
            this.delivery2doorAddressSuggestion(); //dadata suggestion
            this.validateFieldsAndPushToAnalytics();
        }
    };

    dsCheckout.init(true);
})();


