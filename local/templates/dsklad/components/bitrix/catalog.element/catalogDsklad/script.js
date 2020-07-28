(function () {
    window.dsCatalogDetail = {
        selectors: {
            slickForInit: ".js-double-slider-for",
            slickNavInit: ".js-double-slider-nav",
            showMoreBtn: ".js-btn-more",
            expandedBlock : ".js-expanded",
            colorBlock: ".js-color",
            colorItem: '.goods-color__item',
            colorText: '.good-info__title span',
            instaModalBtn: '.js-insta-modal',
            instaSlider: '.js-insta-slider',
            modal: '#ista-slider',
            modalClose: '.js-ds-modal-close',
            modalOverlay: '.ds-modal-overlay',
            addToBasketBtn: '.js-add-to-basket',

            flagCountPlus : ".js-count-plus",
            flagCountMinus : ".js-count-minus",
            flagNumber : ".js-number",

            addToFavorite: '.js-add-to-favorite',
            containerFavorite : "div",
            removeFavorite : "alert--favorite-remove",
            addFavorite : "alert alert--favorite",

            goodInfoScroll : ".good-info__scroll",

            offersBlock : ".js-offers",
            quantityInput: "[name='quantityProduct']",
            recommend__add: '[data-action="recommend__add"]',
            recommed_product_block:'[data-name="catalog-recommend"]',

            onClickBasket: ".on-click-basket",
            inputOnClickPhone:"#tel",
            inputOnClickName:"#name",
            formOnClick: "#on-click-order",

        },

        phpData:{},
        arSkuKey:[],

        jsCatalogDetailSlider: function() {
            $(dsCatalogDetail.selectors.slickForInit).slick({
                prevArrow: "<div class='ds-slider-arrows prev'></div>",
                nextArrow: "<div class='ds-slider-arrows next'></div>",
                //cssEase: 'ease',
                //easing: 'linear',
                arrows: true,
                dots: false,
                //lazyLoad: 'ondemand',
                slidesToShow: 1,
                slidesToScroll: 1,
                asNavFor: dsCatalogDetail.selectors.slickNavInit,
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {
                            infinite: true,
                            dots: true,
                            arrows: false,
                            dotsClass: "ds-slider-dots",
                        }
                    }
                ]
            });
            $(dsCatalogDetail.selectors.slickNavInit).slick({
                arrows: false,
                slidesToShow: 6,
                asNavFor: dsCatalogDetail.selectors.slickForInit,
                focusOnSelect: true
            });
        },

        jsShowMore: function() {

          const showMoreBtns = document.querySelectorAll(dsCatalogDetail.selectors.showMoreBtn);
          const expandedBlocks = document.querySelectorAll(dsCatalogDetail.selectors.expandedBlock);

          for (var expandedBlock of expandedBlocks) {
            if(!!expandedBlock) {
              const expandedBlockHeight = expandedBlock.clientHeight;

              if (expandedBlockHeight >= 260) {
                expandedBlock.classList.add('expanded');
              }

              for (var showMoreBtnClick of showMoreBtns) {
                showMoreBtnClick.addEventListener('click', descrFull, false);

                function descrFull() {
                  this.parentElement.previousElementSibling.classList.add('ds-more');
                  this.parentElement.classList.add('hidden');
                }
              }
            }
          }
        },

        jsChangeOffers: function(){ // Выбор торгового предложения
          var self = this;
          var offerBlock = document.querySelectorAll(this.selectors.offersBlock);

            for (var i = 0; i < offerBlock.length; i++) {
                offerBlock[i].onclick = function () {
                  var activeOffers = self.getActiveOffers(offerBlock,this);
                  if(activeOffers ) self.phpData.OFFERS_ACTIVE = activeOffers;
                  self.getActiveTypeOffers(this); // Соотносим свойства активного ТП с свойствами фильтра
                  self.handlebarsAction(self.phpData); // использование шаблона "Усов"
                  window.history.pushState("object or string", "Title", self.phpData.PRODUCT.DETAIL_PAGE_URL+self.phpData.OFFERS_ACTIVE.ID+'/');
                };
            }
        },

        getActiveOffers: function(offerBlock,thisOfferBlock){ // Получаем активное торговое предложение
            var self = this;

            var dataValue = thisOfferBlock.getAttribute('data-value');
            var dataPropCode = thisOfferBlock.getAttribute('data-prop-code');
            var dataPropPosition = thisOfferBlock.getAttribute('data-position');
            var quantity = document.querySelector(this.selectors.quantityInput).value;
            var  arrDataValue = {};
            arrDataValue[dataPropCode] = dataValue;

            for (var j = 0; j < offerBlock.length; j++) {
                if (offerBlock[j].classList.contains('active') && +offerBlock[j].getAttribute('data-position') < +dataPropPosition){
                    arrDataValue[offerBlock[j].getAttribute('data-prop-code')] = offerBlock[j].getAttribute('data-value')
                }
            }

            for (var keyOffer in self.phpData.OFFERS) {
                var checked = true;
                for (var keyProp in self.phpData.OFFERS[keyOffer].PROPERTIES) {
                    if(!!arrDataValue[keyProp] && arrDataValue[keyProp] != self.phpData.OFFERS[keyOffer].PROPERTIES[keyProp].VALUE){
                        checked = false;
                    }
                }
                if(checked) {
                    self.phpData.OFFERS[keyOffer].RECOMMEND_QUANTITY = quantity;
                    if(self.phpData.OFFERS[keyOffer].ITEM_PRICES.length > 1) {
                        for (var range in self.phpData.OFFERS[keyOffer].ITEM_PRICES) {
                            if (self.phpData.OFFERS[keyOffer].ITEM_PRICES[range].QUANTITY_FROM <= quantity) self.phpData.OFFERS[keyOffer].RECOMMEND_PRICE = self.phpData.OFFERS[keyOffer].ITEM_PRICES[range].PRICE;
                        }
                    }else{
                        self.phpData.OFFERS[keyOffer].RECOMMEND_PRICE = self.phpData.OFFERS[keyOffer].CATALOG_PRICE_2;
                    }
                    return self.phpData.OFFERS[keyOffer];
                }
            }

            return  false;
        },

        getActiveTypeOffers: function(thisOfferBlock){ // Соотносим свойства активного ТП с свойствами фильтра
            var self = this;
            var dataPropPosition = thisOfferBlock.getAttribute('data-position');
            var i = 1;
            self.arSkuKey = [];
            self.arSkuKey.push(self.phpData.OFFERS_ACTIVE.ID);
            for (var keyType in self.phpData.PROPERTY_SELECT) {
                var type = self.phpData.PROPERTY_SELECT[keyType];
                for (var keyProp in type.VALUES) {
                    var prop = type.VALUES[keyProp];
                    if(+i > +dataPropPosition) self.phpData.PROPERTY_SELECT[keyType].VALUES[keyProp].HIDDEN = 'Y';
                    if (self.skuCheck(prop.OFFERS)) {
                        self.phpData.PROPERTY_SELECT[keyType].VALUES[keyProp].HIDDEN = false;
                            if(self.phpData.OFFERS_ACTIVE.PROPERTIES[keyType].VALUE == prop.VALUE) {
                                self.phpData.PROPERTY_SELECT[keyType].VALUES[keyProp].CHECKED = 'Y';
                                self.phpData.PROPERTY_SELECT[keyType].CHECKED_VALUE = prop.VALUE_STRING;
                            }else{
                                self.phpData.PROPERTY_SELECT[keyType].VALUES[keyProp].CHECKED = false;
                            }
                    }else if(+i > +dataPropPosition){
                        self.phpData.PROPERTY_SELECT[keyType].VALUES[keyProp].HIDDEN = 'Y';
                    }else{
                        self.phpData.PROPERTY_SELECT[keyType].VALUES[keyProp].CHECKED = false;
                    }
                }
                i++;
            }
        },

        skuCheck: function(prop){ // Добовляем id товара в фильтр предложений
            var self = this;
            for (var keySku in self.arSkuKey) {
                if (searchStringInArray(self.arSkuKey[keySku], prop) >= 0) {
                    self.arSkuKey = self.arSkuKey.concat(prop);
                    return true;
                }
            }

            function searchStringInArray (str, strArray) {
                for (var j=0; j<strArray.length; j++) {
                    if (strArray[j].match(str)) return j;
                }
                return -1;
            }
            return false;
        },

        jsChangeColor: function() {
            const colorBlock = document.querySelector(dsCatalogDetail.selectors.colorBlock);
            const colorText = document.querySelector(dsCatalogDetail.selectors.colorText);
            const colorItem = document.querySelector(dsCatalogDetail.selectors.colorItem);
            let selected;
            let colorValue;

            colorItem.classList.add('active');

            colorBlock.addEventListener('click', (event) => {
                const target = event.target.closest(dsCatalogDetail.selectors.colorItem);
                colorValue = target.dataset.color;
                colorText.innerHTML = colorValue;
                hightlight(target);
            });

            function hightlight(node) {
                if (selected) {
                    selected.classList.remove('active');
                }
                selected = node;
                colorItem.classList.remove('active');
                selected.classList.add('active');
            }
        },

        jsAddToBasketAnimation: function() { // добовление в корзину
            var _this = this;
            const addToBasketBtn = document.querySelector(dsCatalogDetail.selectors.addToBasketBtn);
            addToBasketBtn.addEventListener('click', addToBasket);

            function addToBasket(e) {
                var quantity = 1;
                if(!!document.querySelector(_this.selectors.quantityInput)) quantity = document.querySelector(_this.selectors.quantityInput).value;
                var self = this;
                var post = {'sessionId':BX.bitrix_sessid(),'quantity':quantity,'productId':addToBasketBtn.getAttribute('data-product-id')};

                BX.ajax.post(
                    _this.templateFloder+'/ajax/addProduct.php',
                    post,
                    function () {
                        self.classList.add('active');
                        self.innerText = 'Добавлено в корзину';
                        self.setAttribute('disabled', 'disabled');

                        setTimeout(function() {
                            addToBasketBtn.classList.remove('active');
                            if(_this.phpData.PREPAYMENT.CHECK && +_this.phpData.OFFERS_ACTIVE.CATALOG_QUANTITY <= 0){
                                addToBasketBtn.innerText = 'Оформить предзаказ';
                            }else{
                                addToBasketBtn.innerText = 'Добавить в корзину';
                            }
                            addToBasketBtn.removeAttribute('disabled');
                        }, 2000);
                    }
                );
                BX.onCustomEvent('OnBasketChange');
            }
        },

        jsAddToFavorite : function () {
            const elem = this;
            let timeOut;
            const btnFavorite = document.querySelector(elem.selectors.addToFavorite);
            const notification = document.createElement(dsCatalogDetail.selectors.containerFavorite);

            const btnFavoriteText = btnFavorite.innerHTML;
            notification.className = dsCatalogDetail.selectors.addFavorite;

            btnFavorite.onclick = function () {
                clearTimeout(timeOut);
                const goodsName = document.querySelector('h1');

                document.body.appendChild(notification);
                if (btnFavorite.classList.contains('added')) {
                   elem.phpData.PRODUCT.FAVORITE = false;
                    $.ajax({
                        type: "POST",
                        url: elem.templateFloder+'/ajax/favorite.php',
                        data: {'productId' : this.getAttribute('data-product-id')},
                        success: function (msg) {
                        }
                    });

                    notification.classList.add(elem.selectors.removeFavorite);
                    notification.innerHTML = `<span>${goodsName.innerText}</span><p>Удалено из избранного</p>`;
                    btnFavorite.classList.remove('added');
                    btnFavorite.innerHTML = btnFavoriteText;

                } else {
                    elem.phpData.PRODUCT.FAVORITE = true;
                    notification.innerHTML = `<span>${goodsName.innerText}</span><p>Добавлено в избранное</p>`;
                    btnFavorite.classList.add('added');
                    $.ajax({
                        type: "POST",
                        url: elem.templateFloder+'/ajax/favorite.php',
                        data: {'productId' : this.getAttribute('data-product-id')},
                        success: function (msg) {
                        }
                    });
                }

                timeOut = setTimeout(function() {
                    notification.parentNode.removeChild(notification);
                }, 4000);
            }

        },

        jsScrollWidth : function() {
            const goodInfoScroll = document.querySelector(dsCatalogDetail.selectors.goodInfoScroll);
            const div = document.createElement('div');

            div.style.overflowY = 'scroll';
            div.style.width = '50px';
            div.style.height = '50px';
            div.style.visibility = 'hidden';

            document.body.appendChild(div);
            const scrollWidth = div.offsetWidth - div.clientWidth;
            document.body.removeChild(div);

            goodInfoScroll.style.width = 'calc(100% + ' + scrollWidth + 'px)';
        },

        jsCountGoodsHandler : function () {
            let elem = this,
                btnPlus = document.querySelector(elem.selectors.flagCountPlus),
                btnMinus = document.querySelector(elem.selectors.flagCountMinus);

            btnPlus.onclick = function() {
                let fieldValue = this.parentNode.querySelector(dsCatalogDetail.selectors.flagNumber);
                let quantity = +fieldValue.getAttribute('value') + 1;
                fieldValue.setAttribute('value',quantity);
                elem.priceOfQuantity(quantity);
                elem.handlebarsAction(elem.phpData); // использование шаблона "Усов"
            };

            btnMinus.onclick = function() {
                let fieldValue = this.parentNode.querySelector(dsCatalogDetail.selectors.flagNumber);

                if ( fieldValue.getAttribute('value') > 1 ) {
                    let quantity = +fieldValue.getAttribute('value') - 1;
                    fieldValue.setAttribute('value',quantity);
                    elem.priceOfQuantity(quantity);
                    elem.handlebarsAction(elem.phpData); // использование шаблона "Усов"

                } else {
                    return 1;
                }
            };
        },

        priceOfQuantity: function(quantity){
            let elem = this;
            let minPrice = elem.phpData.OFFERS_ACTIVE.RECOMMEND_PRICE;
            for (key in elem.phpData.OFFERS_ACTIVE.ITEM_PRICES) {
                let element = elem.phpData.OFFERS_ACTIVE.ITEM_PRICES[key];
                if(quantity >= element.MIN_QUANTITY){
                    minPrice = element.PRICE;
                }
            }
            elem.phpData.OFFERS_ACTIVE.RECOMMEND_PRICE = minPrice;
            elem.phpData.OFFERS_ACTIVE.RECOMMEND_QUANTITY = quantity;
        },

        newRecommend: function(){
            var self = this;
            $(document).on('click', this.selectors.recommend__add, function(){
                var btn = $(this);
                var page = btn.attr('data-next-page');
                var id = btn.attr('data-show-more');

                var data = {};
                data['PAGEN_'+id] = page;

                $.ajax({
                    type: "GET",
                    url: self.templateFloder+'/ajax/recommended.php',
                    data: data,
                    timeout: 3000,
                    success: function(data) {
                       btn.remove();
                       $(self.selectors.recommed_product_block).append(data);
                    }
                });
            });
        },

        handlebarsAction: function(data){ // использование шаблона "Усов"
            var self = this;
            $.ajax({
                type: "POST",
                url: self.templateFloder+'/handlebars.php',
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
                    self.jsChangeOffers();  // Выбор торгового предложения
                    self.jsCatalogDetailSlider(); // подключаем слайдер
                    self.jsAddToBasketAnimation();// добовление в корзину
                    self.jsCountGoodsHandler(); //
                    self.jsAddToFavorite(); // в избранное
                }
            });
        },

        handlebarsHelpers: function(){
            Handlebars.registerHelper("objectLength", function(json) {
                return Object.keys(json).length;
            });

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
                    case '>=':
                        bool = +a >= +b;
                        break;
                    case '<=':
                        bool = +a <= +b;
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

            Handlebars.registerHelper('FIFNOTEQ', function(a, b, opts) {
                return parseFloat(a) != parseFloat(b) ? opts.fn(this) : opts.inverse(this);
            });

            Handlebars.registerHelper('replace', function( find, replace, options) {
                var string = options.fn(this);
                return string.replace( find, replace );
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

        maskPhone: function(){ // маска для телефона
            $('.quick-order-modal [type="tel"]').inputmasks(window.maskOpts);
        },

        oneClick:function(){
            var _this = this,
                oneClick = document.querySelector(_this.selectors.onClickBasket),
                inputOnClickPhone = document.querySelector(_this.selectors.inputOnClickPhone),
                inputOnClickName = document.querySelector(_this.selectors.inputOnClickName),
                addToBasketBtn = document.querySelector(dsCatalogDetail.selectors.addToBasketBtn),
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

                var data = {
                    'name':  inputOnClickName.value,
                    'phone': inputOnClickPhone.value,
                    'quantity':document.querySelector(_this.selectors.quantityInput).value,
                    'productId':addToBasketBtn.getAttribute('data-product-id'),
                    'sessionId': BX.bitrix_sessid(),
                    'templatePath': _this.templateFloder+'/ajax/oneClick.php'
                };

                if(_this.autorized =='Y') {
                    _this.ajaxRequestOneClick(data);
                }else{
                    _this.checkPhone(data);
                }

                return false;
            }
        },

        checkPhone : function(data){
            var _this = this;
            purepopup.closePopup();
            window.saleConfirmPhone.onClick = data;
            window.saleConfirmPhone.confirmPhoneNumber(true);
        },

        ajaxRequestOneClick: function(data){
            var _this = this;

            $.ajax({
                url:data.templatePath,
                method:'POST',
                data:data,
                success: function(mes){
                    if(!isNaN(mes.trim())) {
                        $('body').trigger("oneClickDetail", mes);
                        window.location.href = "/order/thankyou/?ORDER_ID="+mes;
                        return false;
                    }else{
                        return mes;
                        return false;
                    }
                }
            });
        },

        init: function() {
            this.jsCatalogDetailSlider(); // подключаем слайдер
            this.jsShowMore();
            this.jsAddToBasketAnimation();
            this.jsAddToFavorite();
            this.jsScrollWidth();
            this.jsCountGoodsHandler();
            this.jsChangeOffers(); // Выбор торгового предложения
            this.handlebarsHelpers(); // Не забыит перенести хелперы со всех компонентов в один файл
            this.newRecommend();
            if (this.autorized != 'Y') window.saleConfirmPhone.conformCodeSms(true, 'detail');
        }
    };
})();

$( document ).ready(function() {
    dsCatalogDetail.init();
});