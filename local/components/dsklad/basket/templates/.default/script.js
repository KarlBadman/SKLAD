(function () {

    window.dsBasket = {
        selectors: {
            flagRemove: ".js-basket-remove",
            containerRemove: ".ds-basket-good",
            divAlert: "alert alert--good-remove",
            link: ".ds-basket-good__descr a",

            flagWarranty: ".js-add-warranty",
            containerWarranty: "div",
            statusWarranty: ".warranty-status",
            containerTotal: ".js-warranty-total",
            fullWarranty: "warranty-full",
            removeWarranty: "alert--warranty-remove",
            addWarranty: "alert alert--warranty",

            flagCountPlus: ".js-count-plus",
            flagCountMinus: ".js-count-minus",
            flagNumber: ".js-number",
            spinner: ".spinner-target",
            animateFadeIn: "fadein",
            animateFadeOut: "fadeout",

            slickInit: ".js-slider-recommend",

            onClickBasket: ".on-click-basket",
            inputOnClickPhone:"#tel",
            inputOnClickName:"#name",
            addBasketSelector:".add-basket",
            inputQuantity:"input[name=\"quantity\"]",

            basketItemsList:".ds-basket__goods-list",
            handlebarsItemBasket:"#itemBasket",
            basketTotalPrice:'.ds-basket__total-price',
            handlebarsTotalBasket:'#totalBasket',
            recommendBasket: ".ds-recommend-wrapper",
            handlebarsRecommendBasket: "#itemBasketRecommended",
            formOnClick: "#on-click-order",


        },
        templatePath: '/local/components/dsklad/basket/ajax/',
        handelbarsPathBasket: '/local/templates/dsklad/components/bitrix/sale.basket.basket/show/handlebars.php',
        handelbarsPathRecommend: '/local/templates/dsklad/components/bitrix/catalog.section/basket.recommended/handlebars.php',

        jsRemoveHandler: function () {
            var timeOut,
                _this = this,
                btnRemove = document.querySelectorAll(_this.selectors.flagRemove),
                div = document.createElement('divAlert');

            div.className = dsBasket.selectors.divAlert;

            for (var i = 0; i < btnRemove.length; i++) {
                btnRemove[i].onclick = function () {
                    var deletedElement = this.parentNode.parentNode.querySelector(dsBasket.selectors.link).innerHTML;
                    clearTimeout(timeOut);

                    div.innerHTML = "<span>удалено из корзины</span><p>" + deletedElement + "</p>";
                    document.body.appendChild(div);

                    _this.requestBxAjax(
                        {quantity:0,productId:this.getAttribute('data-productid'),del:'Y'},
                        _this.templatePath+'quantityChange.php',
                        'two'
                    );

                    timeOut = setTimeout(function () {
                        div.parentNode.removeChild(div);
                    }, 4000);
                };
            }

        },

        jsAddWarrantyHandler: function () {
            var _this = this,
                timeOut,
                btnWarranty = document.querySelector(_this.selectors.flagWarranty),
                notification = document.createElement(dsBasket.selectors.containerWarranty);

            notification.className = dsBasket.selectors.addWarranty;

            if(!!btnWarranty) {

                btnWarranty.onclick = function () {
                    clearTimeout(timeOut);

                    document.body.appendChild(notification);
                    if (btnWarranty.classList.contains('added')) {
                        notification.classList.add(_this.selectors.removeWarranty);
                        notification.innerHTML = "<span>удалено из корзины</span><p>Расширенная гарантия на 24 месяца</p>";
                        btnWarranty.classList.remove('added');
                    } else {
                        notification.classList.remove(_this.selectors.fullWarranty);
                        notification.innerHTML = "<span>добавлено в корзину</span><p>Расширенная гарантия на 24 месяца</p>";
                        btnWarranty.classList.add('added');
                    }

                    _this.requestBxAjax(
                        {productId: btnWarranty.getAttribute('data-serviceid')},
                        _this.templatePath + 'basketServiceСhange.php',
                        'basket'
                    );

                    timeOut = setTimeout(function () {
                        notification.parentNode.removeChild(notification);
                    }, 4000);
                }
            }

        },

        jsCountGoodsHandler: function () {
            var _this = this,
                btnPlus = document.querySelectorAll(_this.selectors.flagCountPlus),
                btnMinus = document.querySelectorAll(_this.selectors.flagCountMinus);


            for (var i = 0; i < btnPlus.length; i++) {
                btnPlus[i].onclick = function () {
                    var fieldValue = this.parentNode.querySelector(dsBasket.selectors.flagNumber);
                    var spinner = document.querySelector(dsBasket.selectors.spinner);
                    spinner.classList.add('active');

                    fieldValue.value++;

                    _this.requestBxAjax(
                        {quantity:fieldValue.value,productId:fieldValue.getAttribute('data-productID'),del:'N'},
                        _this.templatePath+'/quantityChange.php',
                        'basket'
                    );
                };
            }

            for (var i = 0; i < btnMinus.length; i++) {
                btnMinus[i].onclick = function () {
                    var fieldValue = this.parentNode.querySelector(dsBasket.selectors.flagNumber);
                    var spinner = document.querySelector(dsBasket.selectors.spinner);
                    spinner.classList.add('active');

                    if (fieldValue.value > 1) {
                        fieldValue.value--;
                    } else {
                        spinner.classList.remove('active');
                        return 1;
                    }
                    _this.requestBxAjax(
                        {quantity:fieldValue.value,productId:fieldValue.getAttribute('data-productID'),del:'N'},
                        _this.templatePath+'quantityChange.php',
                        'basket'
                    );
                };
            }
        },

        oneClickBasket:function(){
            var _this = this,
                oneClick = document.querySelector(_this.selectors.onClickBasket),
                inputOnClickPhone = document.querySelector(_this.selectors.inputOnClickPhone),
                inputOnClickName = document.querySelector(_this.selectors.inputOnClickName),
                error = true;

            oneClick.onclick = function () {
                $('.quick-order-modal [type="tel"]').parsley().removeError('forcederror');

                if(!$('.quick-order-modal [type="tel"]').inputmask("isComplete")){
                    $('.quick-order-modal [type="tel"]').parsley().addError('forcederror', {message: 'Обязательное поле.', updateClass: true});
                    return false;
                }

                $(_this.selectors.formOnClick).parsley().whenValidate({
                }).done(function() {
                    error = false;
                });
                if(error) return false;

                data = {
                    'name':  inputOnClickName.value,
                    'phone': inputOnClickPhone.value,
                };

                if(_this.autorized !='Y') {
                    _this.checkPhone(data);
                }else {
                    _this.requestBxAjax(
                        data,
                        _this.templatePath + 'initOrder.php',
                        'onClick'
                    );
                }

                return false;
            }


        },

        checkPhone : function(data){
            var _this = this;
            purepopup.closePopup();
            window.saleConfirmPhone.onClick = {'phone':data.phone,'name':data.name,'templatePath':_this.templatePath + 'initOrder.php'}
            window.saleConfirmPhone.confirmPhoneNumber(true);
        },

        addBasketRecommended:function(){
            var _this = this,
                linkAdd = document.querySelectorAll(_this.selectors.addBasketSelector);

            for (var i = 0; i < linkAdd.length; i++) {
                linkAdd[i].onclick = function data() {
                    _this.requestBxAjax(
                        {quantity:1,productId:linkAdd[i].getAttribute('data-productID')},
                        _this.templatePath+'addProduct.php',
                        'two'
                    );
                }
            }
        },

        inputChange: function(){
            var _this = this;

            $(_this.selectors.inputQuantity).on('change', function(){
                if(+$(this).val() > 500) $(this).val(500);
                _this.requestBxAjax(
                    {quantity:$(this).val(),productId:$(this).data('productid'),del:'N'},
                    _this.templatePath+'quantityChange.php',
                    'basket'
                );
            });
        },

        jsBasketSlick: function () {
            $(dsBasket.selectors.slickInit).slick({
                    appendArrows: ('.js-slider-recommend-arrows'),
                    prevArrow: "<div class='ds-slider-arrows prev'></div>",
                    nextArrow: "<div class='ds-slider-arrows next'></div>",
                    infinite: false,
                    arrows: true,
                    dots: false,
                    slidesToShow: 6,
                    slidesToScroll: 6,
                    responsive: [
                        {
                            breakpoint: 1280,
                            settings: {
                                slidesToShow: 5,
                                slidesToScroll: 5,
                            }
                        },
                        {
                            breakpoint: 1140,
                            settings: {
                                slidesToShow: 4,
                                slidesToScroll: 4,
                            }
                        },
                        {
                            breakpoint: 1000,
                            settings: {
                                slidesToShow: 5,
                                slidesToScroll: 5,
                                arrows: false,
                            }
                        },
                        {
                            breakpoint: 800,
                            settings: {
                                slidesToShow: 4,
                                slidesToScroll: 4,
                                arrows: false,
                            }
                        },
                        {
                            breakpoint: 630,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 3,
                                arrows: false,
                            }
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 2,
                                arrows: false,
                            }
                        }
                    ]
                }
            );
        },

        maskPhone: function(){ // маска для телефона
            $('.quick-order-modal [type="tel"]').inputmasks(maskOpts);
        },

        handlebarsAction: function(data,url){ // использование шаблона "Усов"
            var self = this;
            $.ajax({
                type: "POST",
                url: url,
                data: '',
                success: function (msg) {
                    data = JSON.parse(data);
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

                    if(url == self.handelbarsPathBasket){
                        slick = false;
                    }else{
                        slick = true;
                    }

                    self.init(slick);
                }
            });

        },

        requestBasketJson: function(){
            var _this = this,
                data = {'sessionId': BX.bitrix_sessid()};

            BX.ajax({
                url:'/local/templates/dsklad/ajax/basket.php',
                method:'POST',
                async:'true',
                data: data,
                cache: false,
                onsuccess: function(msg){
                    if(JSON.parse(msg).BASKET_EMPTY) window.location.href = '/basket/empty/';
                    _this.handlebarsAction(msg,_this.handelbarsPathBasket);

                    arJs = JSON.parse(msg);

                    if(!!document.querySelector(_this.selectors.flagWarranty)) {
                        if (arJs.SERVICE_OK != 'Y') {
                            document.querySelector(_this.selectors.flagWarranty).innerHTML = 'Добавить гарантию<span class="ds-price">' + new Intl.NumberFormat('ru-RU').format(arJs.WARRANTY_PRICE) + '</span>';
                        } else {
                            document.querySelector(_this.selectors.flagWarranty).innerHTML = 'Отменить гарантию';
                        }
                    }
                   // _this.init(false);
                },
            });
        },

        requestBasketRecommended: function(){
            var _this = this,
                data = {'sessionId': BX.bitrix_sessid()};


            BX.ajax({
                url:'/local/templates/dsklad/ajax/basketRecommended.php',
                method:'POST',
                async:'true',
                data: data,
                cache: false,
                onsuccess: function(msg){
                     _this.handlebarsAction(msg,_this.handelbarsPathRecommend);
                    // _this.init(true);
                },
            });
        },

        requestBxAjax:function(data,url,components){
            var _this = this;
            data.sessionId = BX.bitrix_sessid();

            BX.ajax({
                url:url,
                method:'POST',
                async:'true',
                data:data,
                cache: false,
                onsuccess: function(mes){
                    if(components == 'onClick'){
                        if(!isNaN(mes.trim())) {
                            $('body').trigger("oneClickCheckout", mes);
                            setTimeout(function() {
                                window.location.href = "/order/thankyou/?ORDER_ID="+mes;
                            }, 1000);
                            return false;
                        }else{
                            return mes;
                            return false;
                        }
                    }else{
                        if (mes.trim() == 'Y') {
                            if (components == 'basket' || components == 'two') _this.requestBasketJson();
                            if (components == 'section' || components == 'two') _this.requestBasketRecommended();
                        }else{
                            return mes;
                        }
                    }

                   // _this.init(false);
                },
                onfailure: function(mes){

                }
            });
        },

        handlebarsHelpers: function(){
            Handlebars.registerHelper('iff', function(a, operator, b, opts) {
                var bool = false;
                switch(operator) {
                    case '==':
                        bool = +a == +b;
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
        },

        jsCleanInputs: function() {
            var elem = $('.form-group__item input');
            elem.on('input', function() {
                if( $(this).val() > '' ) {
                    $(this).prev().addClass('focused');
                }
            });

            $('.form-group__item').on('click', '.js-input-clean', function(){
                $(this).next().val('').focus();
                $(this).removeClass('focused');
            });
        },

        init: function (slick=true) {
            this.jsRemoveHandler(); // анимация при удалении/восстановлении товара в корзине
            this.jsAddWarrantyHandler(); // расширенная/стандартная гарантия в корзине
            this.jsCountGoodsHandler(); // изменение количества товаров
            if(slick) this.jsBasketSlick(); // слайдер в корзине
            this.addBasketRecommended(); // Добовление товаров
            this.handlebarsHelpers();
            this.inputChange();
            this.jsCleanInputs();
            if (this.autorized != 'Y') window.saleConfirmPhone.conformCodeSms(true, 'basket');
        }
    };

})();

$('html').addClass((isMobile.any ? 'mobile' : 'desktop'));



$( document ).ready(function() {
    dsBasket.init();
});