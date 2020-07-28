// Analytics JS file
$(function () {
    window.analyticSystem = {
        // Object settings
        settings : {
            handCall : false,
            debug : false,
            scrollTimeout : 1600,
            orderID : '',
            calculatedRevenue : '',
            email : '',
            methodsFired : false,
            payOption : '',
            deliveryOption : '',
            baseInfoFilled : false,
            phoneConfirmed : false,
            stock_id : 4,
        },

        // Page type selectors TO EVENTS
        pageSelectors : {
            catalogListPage : "[data-page-type=\"catalog-list\"]",
            catalogDetailPage : "[data-page-type=\"catalog-detail\"]",
            orderBasePage : "[data-page-type=\"order-base\"]",
            orderCheckoutPage : "[data-page-type=\"order-checkout\"]",
            orderThanxtPage : "[data-page-type=\"order-thanx\"]",
            homePage : "[data-page-type=\"home-page\"]",
            searchPage : "[data-page-type=\"search-page\"]",
            otherPage : "[data-page-type=\"other-page\"]",
        },

        // element type selectors TO EVENTS
        elementTypeSelectors : {
            catalogListPageProductLnk : "[data-ga-analys-btn=\"to-detail-lnk\"]",
            basketAddItemProductBtn : "[data-ga-analys-btn=\"basket-add-item\"]",
            basketAddItemPreorderProductBtn : "[data-ga-analys-btn=\"basket-add-item-preorder\"]",
            basketDeleteItemProductBtn : "[data-ga-analys-btn=\"basket-delete-item\"]",
            favoriteAddItemProductBtn : "[data-ga-analys-btn=\"favorite\"]",
            orderDeliverySetBtn : "[data-ga-analys-btn=\"order-delivery-set\"]",
            orderPaymentSetBtn : "[data-ga-analys-btn=\"order-payment-set\"]",
            orderSubmitBtn : "[data-ga-analys-btn=\"order-submit\"]",
            orderOneClickSubmitBtn : "[data-ga-analys-btn=\"order-one-click-submit\"]",
            oneClickSubmitForm : "[data-ga-analys-btn=\"one-click-form\"]"
        },

        // Grub data into pages methods
        dataSets : {
            // Data selectors
            dataSelectors: {
                attrs: {
                    itemName: "data-item-name",
                    itemID: "data-item-id",
                    itemPrice: "data-item-price",
                    itemCategory: "data-item-category",
                    itemQuantity: "data-gaproduct-quantity",
                    email: "data-cur-email",
                    categoryId : "data-category-id",
                },
                detailPageAttr: {
                    itemName: "data-item-aproperty=\"name\"",
                    item: "[data-gaproductlist=\"item\"]",
                    itemID: "data-gaitem-id",
                    itemQuantity: "data-item-aproperty=\"quantity\"",
                    email: "data-cur-email",
                    offersId: "data-item-offersId",
                },
                anyPage: {
                    productContainer: "[data-product=\"container\"]",
                    impressionsContainer: "[data-product-impressions=\"container\"]",
                    item: "[data-galist=\"item\"]",
                    header: "[data-ga=\"page-header\"]",
                },
                orderPages: {
                    basketFieldSet: {
                        area: "[data-order-page=\"basket-area\"]",
                        container: "[data-order-page=\"basket-fieldset\"]",
                        item: "[data-order-page=\"basket-item-field\"]",
                        email: "[data-name=\"is_email\"]",
                    },
                    baseInfoFieldSet: {
                        container: "[data-order-baseinfo]",
                        name: "[data-order-baseinfo=\"user-name\"]",
                        email: "[data-order-baseinfo=\"user-email\"]",
                    },
                    paymentFieldSet: {
                        container: "[data-block-name=\"payment\"]",
                        option: "[data-order-page=\"payment-option-field\"]",
                    },
                    deliveryFieldSet: {
                        container: "[data-block-name=\"delivery\"]",
                        option: "[data-order-page=\"delivery-option-field\"]",
                    },
                    thanxFieldSet: {
                        container: "[data-order-page=\"thanx-fieldset\"]",
                        orderID: "data-order-id",
                        orderRevenue: "[data-order-revenue]",
                        orderProductsJson: "[data-order-products]",
                        orderEmail: "[data-order-email]",
                        shiping : "[data-order-shipping]"
                    },
                    checkoutFinishFieldSet: {
                        container: "[data-order-info=\"order_info\"]",
                        orderRevenue: "[data-order-revenue]",
                        shiping : "[data-order-shipping]"
                    },
                    checkoutOneClickFieldSet: {
                        orderRevenue: "[data-basket-revenue]",
                    },
                    totalFieldSet: {
                        summPrice: "[data-order-page=\"summ-field\"]",
                        shipping: "[data-order-page=\"shipping-field\"]"
                    }
                },
                personalPage:{
                    email:"[data-personal-page=\"email\"]",
                    subscrible:"[data-personal-page=\"subscrible\"]",
                }
            },

            // Get page title
            getPageTitle : function () {
                return $(this.dataSelectors.anyPage.header).length > 0 ? document.title + " | " + $(this.dataSelectors.anyPage.header).text() : document.title;
            },

            // Get impressions into catalog fieldset
            getImpressions : function () {
                var impressions = [], self = this;

                if (
                    $(self.dataSelectors.anyPage.impressionsContainer).length > 0
                    && $(self.dataSelectors.anyPage.impressionsContainer + " " + self.dataSelectors.anyPage.item).length > 0
                ) {
                    $(self.dataSelectors.anyPage.impressionsContainer + " " + self.dataSelectors.anyPage.item).each(function (i, e) {
                        if ($(this).visible()) {
                            impressions.push({
                                'name' : $(e).attr(self.dataSelectors.attrs.itemName) ? $(e).attr(self.dataSelectors.attrs.itemName) : "-nf-",
                                'id' : $(e).attr(self.dataSelectors.attrs.itemID) ? $(e).attr(self.dataSelectors.attrs.itemID) : "-nf-",
                                'price' : $(e).attr(self.dataSelectors.attrs.itemPrice) ? parseFloat($(e).attr(self.dataSelectors.attrs.itemPrice).replace(' ', '')).toString() : "-nf-",
                                'category' : $(e).attr(self.dataSelectors.attrs.itemCategory) ? $(e).attr(self.dataSelectors.attrs.itemCategory) : "-nf-",
                                'list' : self.getPageTitle(),
                                'brand' : 'Дизайн склад',
                                'quantity' : 1,
                                'position' : parseInt($(e).index())
                            });
                        }
                    });
                }

                if (analyticSystem.settings.debug) {
                    console.info('impressions list: ');
                    console.table(impressions);
                }
                return impressions;
            },


            // Get products into catalog fieldset
            getDetailProducts : function (item, type) {
                var products = [], self = this, itemsList = {};
                itemsList = Object.keys(item).length > 0 ? item :
                    $(self.dataSelectors.anyPage.productContainer + " " + self.dataSelectors.detailPageAttr.item).length > 0 ?
                        $(self.dataSelectors.anyPage.productContainer + " " + self.dataSelectors.detailPageAttr.item) : {length:0};

                if (
                    $(self.dataSelectors.anyPage.productContainer).length > 0
                    && $(self.dataSelectors.anyPage.productContainer + " " + self.dataSelectors.detailPageAttr.item).length > 0 // TODO
                    && itemsList.length > 0
                ) {
                    itemsList.each(function (i, e) {
                            products.push({
                                'name': $(e).find("[" + self.dataSelectors.detailPageAttr.itemName + "]") ? $(e).find("[" + self.dataSelectors.detailPageAttr.itemName + "]").text() : "-nf-",
                                'id': $(e).find("[" + self.dataSelectors.detailPageAttr.itemID + "]") ? $(e).find("[" + self.dataSelectors.detailPageAttr.itemID + "]").attr(self.dataSelectors.detailPageAttr.itemID) : "-nf-",
                                'price': $(e).find("[" + self.dataSelectors.attrs.itemPrice + "]") ? parseFloat($(e).find("[" + self.dataSelectors.attrs.itemPrice + "]").attr(self.dataSelectors.attrs.itemPrice).replace(' ', '')).toString() : "-nf-",
                                'category': $(e).attr(self.dataSelectors.attrs.itemCategory) ? $(e).attr(self.dataSelectors.attrs.itemCategory) : "-nf-",
                                'list': self.getPageTitle(),
                                'brand': 'Дизайн склад',
                                'quantity': $(e).find("[" + self.dataSelectors.detailPageAttr.itemQuantity + "]:visible").val() ? $(e).find("[" + self.dataSelectors.detailPageAttr.itemQuantity + "]:visible").val().replace(/[^-0-9]/gim, '') : 1,
                                'position': parseInt($(e).index()),
                                'offersId': $(e).find("[" + self.dataSelectors.detailPageAttr.offersId + "]") ? JSON.parse($(e).attr(self.dataSelectors.detailPageAttr.offersId)) : "-nf-",
                            });
                    });
                }

                if (analyticSystem.settings.debug) {
                    console.info('products list: ');
                    console.table(products);
                }

                return products;
            },

            // Get products into catalog fieldset
            getProducts : function (item, type) {
                var products = [], self = this, itemsList = {};

                itemsList = Object.keys(item).length > 0 ? item :
                    $(self.dataSelectors.anyPage.productContainer + " " + self.dataSelectors.anyPage.item).length > 0 ?
                        $(self.dataSelectors.anyPage.productContainer + " " + self.dataSelectors.anyPage.item) : {length:0};

                if (
                    $(self.dataSelectors.anyPage.productContainer).length > 0
                    && $(self.dataSelectors.anyPage.productContainer + " " + self.dataSelectors.anyPage.item).length > 0 // TODO
                    && itemsList.length > 0
                ) {
                    itemsList.each(function (i, e) {
                            products.push({
                                'name': $(e).attr(self.dataSelectors.attrs.itemName) ? $(e).attr(self.dataSelectors.attrs.itemName) : "-nf-",
                                'id': $(e).attr(self.dataSelectors.attrs.itemID) ? $(e).attr(self.dataSelectors.attrs.itemID) : "-nf-",
                                'price': $(e).attr(self.dataSelectors.attrs.itemPrice) ? parseFloat($(e).attr(self.dataSelectors.attrs.itemPrice).replace(' ', '')).toString() : "-nf-",
                                'category': $(e).attr(self.dataSelectors.attrs.itemCategory) ? $(e).attr(self.dataSelectors.attrs.itemCategory) : "-nf-",
                                'list': self.getPageTitle(),
                                'brand': 'Дизайн склад',
                                'quantity': $(e).find("[" + self.dataSelectors.attrs.itemQuantity + "]:visible").val() ? $(e).find("[" + self.dataSelectors.attrs.itemQuantity + "]:visible").val().replace(/[^-0-9]/gim, '') : 1,
                                'position': parseInt($(e).index())
                            });
                    });
                }

                if (analyticSystem.settings.debug) {
                    console.info('products list: ');
                    console.table(products);
                }

                return products;
            },

            // Get products into basket fieldset
            getBasketProducts : function (item) {
                var products = [], self = this, itemsList = {};

                if ($(analyticSystem.pageSelectors.orderBasePage).length > 0 || $(self.dataSelectors.orderPages.basketFieldSet.area).length > 0) {
                    itemsList = Object.keys(item).length > 0 ? item :
                        $(self.dataSelectors.orderPages.basketFieldSet.area + " " + self.dataSelectors.orderPages.basketFieldSet.container + " " + self.dataSelectors.orderPages.basketFieldSet.item).length > 0 ?
                            $(self.dataSelectors.orderPages.basketFieldSet.area + " " + self.dataSelectors.orderPages.basketFieldSet.container + " " + self.dataSelectors.orderPages.basketFieldSet.item) : {length:0};
                } else {
                    itemsList = Object.keys(item).length > 0 ? item :
                        $("header .basket_area" + self.dataSelectors.orderPages.basketFieldSet.container + " " + self.dataSelectors.orderPages.basketFieldSet.item).length > 0 ?
                            $("header .basket_area " + self.dataSelectors.orderPages.basketFieldSet.container + " " + self.dataSelectors.orderPages.basketFieldSet.item) : {length:0};
                }

                if (itemsList.length > 0) {
                    itemsList.each(function (i, e) {
                        products.push({
                            'name' : $(e).attr(self.dataSelectors.attrs.itemName) ? $(e).attr(self.dataSelectors.attrs.itemName) : "-nf-",
                            'id' : $(e).attr(self.dataSelectors.attrs.itemID) ? $(e).attr(self.dataSelectors.attrs.itemID) : "-nf-",
                            'price' : $(e).attr(self.dataSelectors.attrs.itemPrice) ? parseFloat($(e).attr(self.dataSelectors.attrs.itemPrice).replace(' ', '')).toString() : "-nf-" ,
                            'category' : $(e).attr(self.dataSelectors.attrs.itemCategory) ? $(e).attr(self.dataSelectors.attrs.itemCategory) : "-nf-",
                            'list' : self.getPageTitle(),
                            'brand' : 'Дизайн склад',
                            'quantity' : $(e).attr(self.dataSelectors.attrs.itemQuantity) ? $(e).attr(self.dataSelectors.attrs.itemQuantity).replace(/[^-0-9]/gim, '') : $(e).find("[" + self.dataSelectors.attrs.itemQuantity + "]:visible").val() ? $(e).find("[" + self.dataSelectors.attrs.itemQuantity + "]:visible").val().replace(/[^-0-9]/gim,'') : 1,
                            'position' : $(e).index() ? parseInt($(e).index()) : i
                        });
                    });
                }

                if (analyticSystem.settings.debug) {
                    console.info('basket products list:');
                    console.table(products);
                }

                return products;
            },

            // Get order products by thanx page
            getOrderThanxProducts : function () {
                var products = [], self = this, itemsList = {};

                if ($(analyticSystem.pageSelectors.orderThanxtPage).length > 0) {

                    if ($(this.dataSelectors.orderPages.thanxFieldSet.orderProductsJson).length > 0)
                        itemsList = $(this.dataSelectors.orderPages.thanxFieldSet.orderProductsJson).data('order-products');

                    if (itemsList.length > 0) {
                        $.each(itemsList, function (i, e) {
                            products.push({
                                'name': e.name ? e.name : "-nf-",
                                'id': e.id ? e.id : "-nf-",
                                'price': e.price ? parseFloat(e.price) : "-nf-",
                                'quantity': e.quantity ? parseInt(e.quantity) : 1,
                            });
                        });
                    }
                }

                if (analyticSystem.settings.debug) {
                    console.info('products list:');
                    console.table(products);
                }

                return products;
            },

            // Get action field object
            getActionField : function () {
                return {'list' : this.getPageTitle()};
            },

            // Get order base action field
            getOrderBaseActionField : function () {
                return {'step' : 1, 'list' : this.getPageTitle()};
            },

            // Get order base action field
            getOrderCheckoutActionField : function () {
                return {'step' : 2, 'list' : this.getPageTitle()};
            },

            // Get order delivery set action field
            getOrderPhoneConfirmSetActionField : function () {
                return {'step' : 3, 'list' : this.getPageTitle(), action : 'checkout'};
            },

            // Get order delivery set action field
            getOrderBaseInfoSetActionField : function () {
                return {'step' : 4, 'list' : this.getPageTitle(), action : 'checkout'};
            },

            // Get order delivery set action field
            getOrderDeliverySetActionField : function () {
                return {'step' : 5, 'list' : this.getPageTitle(), 'option' : analyticSystem.settings.deliveryOption, action : 'checkout'};
            },

            // Get order payment set action field
            getOrderPaymentSetActionField : function () {
                return {'step' : 6, 'list' : this.getPageTitle(), 'option' :
                        $(this.dataSelectors.orderPages.paymentFieldSet.container
                            + " " + this.dataSelectors.orderPages.paymentFieldSet.option + ".active .payment-info__info .header"
                        ).text(), action : 'checkout'
                };
            },

            // Get order action field
            getCheckoutFinishActionField : function () {
                return {
                    'id' : analyticSystem.settings.orderID,
                    'revenue' : $(this.dataSelectors.orderPages.checkoutFinishFieldSet.container + " " + this.dataSelectors.orderPages.checkoutFinishFieldSet.orderRevenue).data('order-revenue'),
                    'affiliation' : 'Purchase',
                    'shipping' : $(this.dataSelectors.orderPages.checkoutFinishFieldSet.container + " " + this.dataSelectors.orderPages.checkoutFinishFieldSet.shiping).data('order-shipping'),
                    'list' : this.getPageTitle()
                }
            },

            // Get one click order action field
            getOneClickCheckoutActionField : function () {
                return {
                    'id' : analyticSystem.settings.orderID,
                    'revenue' : $(this.dataSelectors.orderPages.checkoutOneClickFieldSet.orderRevenue).data('basket-revenue'),
                    'affiliation' : 'Purchase',
                    'shipping' : "-nf-",
                    'list' : this.getPageTitle()
                }
            },

            // Get product detail page one click order form submit action field
            getProductDetailOneClickOrderFormSubmitActionField : function () {
                var self = this;
                return {
                    'id' : analyticSystem.settings.orderID ? analyticSystem.settings.orderID : '-nf-',
                    'affiliation' : 'Purchase one click',
                    'revenue' : analyticSystem.settings.calculatedRevenue,
                    'list' : this.getPageTitle()
                }
            }
        },

        // Get base currency code
        getBaseCurrencyCode : function () {
            return "RUB";
        },

        // Catalog list page event handler
        catalogListPageEventHandler : function () {
            var self = this, timeoutId, dataLayerObject = {};

            if (
                $( self.pageSelectors.catalogListPage).length > 0
                || self.settings.handCall !== false
            ) {
                if (analyticSystem.settings.debug)
                    console.info('Catalog list page loaded');
                self.yandexGoalCatalogPageLoad();
                self.vkObject.VKPushData("view_category", []);
                self.gaObject.catalogListPageGaEventHandler(analyticSystem.dataSets.getImpressions());
                var products = self.dataSets.getProducts({});
                self.criteoObject.catalogListPageCriteoEventHandler(products);
                self.retailRocket.catalogListPageRRocketEventHandler();
            }
        },

        // Catalog detail page event handler
        catalogDetailPageEventHandler : function () {
            var self = this;
            if (
                $(self.pageSelectors.catalogDetailPage).length > 0
                || self.settings.handCall !== false
            ) {
                if (analyticSystem.settings.debug)
                    console.info('Catalog detail page loaded');

                self.vkObject.VKPushData("view_product", []);
                self.yandexGoalDetailPageLoad();
                var products = self.dataSets.getDetailProducts({});
                self.gaObject.catalogDetailPageGaEventHandler(products);
                self.mtObject.catalogDetailPageMyTargetEventHandler(products);
                self.criteoObject.catalogDetailPageCriteoEventHandler(products);
                self.retailRocket.catalogDetailPageRRocketEventHandler(products);
            }
        },

        // Order base page event handler
        orderBasePageEventHandler : function () {
            var self = this;

            if (
                $(self.pageSelectors.orderBasePage).length > 0
                || self.settings.handCall !== false
            ) {
                if (analyticSystem.settings.debug)
                    console.info('Order basket page loaded');

                var itemList= self.dataSets.getBasketProducts({});
                self.gaObject.orderBasePageGaEventHandler(itemList);
                self.mtObject.orderBasePageMyTargetEventHandler(itemList);
                self.vkObject.orderBasePageVkEventHandler(itemList);
                self.criteoObject.orderBasePageCriteoEventHandler(itemList);
            }
        },

        // Order base page event handler
        orderChekoutPageEventHandler : function () {
            var self = this;

            if (
                $(self.pageSelectors.orderCheckoutPage).length > 0
                || self.settings.handCall !== false
            ) {
                if (analyticSystem.settings.debug)
                    console.info('Order checkout page loaded');

                var itemList = self.dataSets.getBasketProducts({});
                self.gaObject.orderCheckoutPageGaEventHandler(itemList);
            }
        },

        //Order finish checkout btn click event handler
        orderFinishClickEventHandler : function () {
            var self = this;

            if (analyticSystem.settings.debug)
                console.info('Order finish checkout action');

            var itemList = self.dataSets.getBasketProducts({});

            self.yandexGoalCheckoutFinish();
            self.gaObject.orderFinishCheckoutGaHandler(itemList);
            self.fbObject.orderFinishCheckoutFbqHandler(itemList);
            self.mtObject.orderFinishCheckoutMyTargetHandler(itemList);
            self.vkObject.orderFinishCheckoutVKHandler(itemList);
            self.retailRocket.orderFinishCheckoutRRocketHandler(itemList);
            self.retailRocket.getEmailRRocketHandler();
            self.criteoObject.orderFinishCheckoutCriteoHandler(itemList);
        },

        //Order finish checkout btn click event handler
        orderOneClickCheckoutEventHandler : function () {
            var self = this;

            if (analyticSystem.settings.debug)
                console.info('Order one click checkout action');
            var itemList = self.dataSets.getBasketProducts({});

            self.yandexGoalOneClickCheckoutFinish();
            self.gaObject.orderOneClickCheckoutGaHandler(itemList);
            self.fbObject.orderOneClickCheckoutFbqHandler(itemList);
            self.mtObject.orderOneClickCheckoutMyTargetHandler(itemList);
            self.vkObject.orderOneClickCheckoutVKHandler(itemList);
            self.retailRocket.orderFinishCheckoutRRocketHandler(itemList);
            self.criteoObject.orderOneClickCheckoutCriteoHandler(itemList);
        },

        //page view handler
        pageViewEventHandler : function() {
            var self = this;

            if (
                $(analyticSystem.pageSelectors.homePage).length > 0
                || analyticSystem.settings.handCall !== false
            ) {
                if (analyticSystem.settings.debug)
                    console.info('Home page loaded');
                self.criteoObject.CriteoPushData('', {"event": "viewHome"});
                self.vkObject.VKPushData("view_home", []);
            }
            if (
                $(analyticSystem.pageSelectors.searchPage).length > 0
                || analyticSystem.settings.handCall !== false
            ) {
                if (analyticSystem.settings.debug)
                    console.info('Search page loaded');
                var products = self.dataSets.getProducts({});
                self.criteoObject.searchPageCriteoEventHandler(products);
                self.vkObject.VKPushData("view_search", []);
            }
            if (
                $(analyticSystem.pageSelectors.otherPage).length > 0
                || analyticSystem.settings.handCall !== false
            ) {
                if (analyticSystem.settings.debug)
                    console.info('Other page loaded');
                self.vkObject.VKPushData("view_other", []);
            }
            // not used yet
            //  add_to_wishlist – добавление товара в список желаний;
            //  remove_from_wishlist – удаление товара из списка желаний;
            //  remove_from_cart – удаление товара из корзины;
            //  add_payment_info – введение платежной информации;
        },

        // Catalog list 'CLICK' to product event handler
        catalogListProductClickEventHandler : function () {
            var self = this;

            if (
                $(analyticSystem.elementTypeSelectors.catalogListPageProductLnk).length > 0
                || analyticSystem.settings.handCall !== false
            ) {
                $('body').on('click', analyticSystem.elementTypeSelectors.catalogListPageProductLnk, function(e) {
                    var element = $(this); var parentItem = $(element).parents(analyticSystem.dataSets.dataSelectors.anyPage.item);

                    if (analyticSystem.settings.debug)
                        console.info('product list click event');

                    var products = self.dataSets.getProducts(parentItem);
                    self.gaObject.catalogListProductGaClickEvent(products);
                });
            }
        },

        // Basket add item event handler
        basketAddItemEventHandler : function () {
            var self = this;

            if (
                $(analyticSystem.elementTypeSelectors.basketAddItemProductBtn).length > 0
                || analyticSystem.settings.handCall !== false
            ) {
                $('body').on('click', analyticSystem.elementTypeSelectors.basketAddItemProductBtn, function (e) {
                    var element = $(this);

                    if (analyticSystem.settings.debug)
                        console.info('GA event item add to basket');
                    var products =  ($(element).parents(analyticSystem.dataSets.dataSelectors.detailPageAttr.item).length > 0) ?
                        self.dataSets.getDetailProducts($(element).parents(analyticSystem.dataSets.dataSelectors.detailPageAttr.item)) :
                        self.dataSets.getProducts($(element).parents(analyticSystem.dataSets.dataSelectors.anyPage.item));

                    self.gaObject.basketAddItemGaHandler(products);
                    self.fbObject.basketAddItemFbqHandler(products);
                    self.mtObject.basketAddItemMyTargetHandler(products);
                    self.vkObject.basketAddItemVkHandler(products);
                    self.retailRocket.basketAddItemRRocketHandler(products);
                });
            }
        },

        // Basket add item preorder event handler
        basketAddItemPreorderEventHandler : function () {
            var self = this;
            
            if (
                $(analyticSystem.elementTypeSelectors.basketAddItemPreorderProductBtn).length > 0
                || analyticSystem.settings.handCall !== false
            ) {
                $('body').on('click', analyticSystem.elementTypeSelectors.basketAddItemPreorderProductBtn, function (e) {
                    var element = $(this);

                    if (analyticSystem.settings.debug)
                        console.info('GA event item add to basket');

                    var products =  ($(element).parents(analyticSystem.dataSets.dataSelectors.detailPageAttr.item).length > 0) ?
                        self.dataSets.getDetailProducts($(element).parents(analyticSystem.dataSets.dataSelectors.detailPageAttr.item)) :
                        self.dataSets.getProducts($(element).parents(analyticSystem.dataSets.dataSelectors.anyPage.item));

                    self.gaObject.basketAddItemPreorderGaHandler(products);
                    self.fbObject.basketAddItemFbqHandler(products);
                    self.mtObject.basketAddItemMyTargetHandler(products);
                    self.vkObject.basketAddItemVkHandler(products);
                    self.retailRocket.basketAddItemRRocketHandler(products);
                });
            }
        },

        // Basket delete item event handler
        basketDeleteItemEventHandler : function () {
            var self = this;

            if (
                $(analyticSystem.elementTypeSelectors.basketDeleteItemProductBtn).length > 0
                || analyticSystem.settings.handCall !== false
            ) {
                $('body').on('click', analyticSystem.elementTypeSelectors.basketDeleteItemProductBtn, function (e) {
                    var element = $(this); var parentItem = $(element).parents(analyticSystem.dataSets.dataSelectors.orderPages.basketFieldSet.item);

                    if (analyticSystem.settings.debug)
                        console.info('GA event item delete to basket');

                    var products = self.dataSets.getBasketProducts(parentItem)
                    self.gaObject.basketDeleteItemGaHandler(products);
                });
            }
        },

        // Order phone confirm set event handler
        orderPhoneConfirmTriggerEventHandler : function () {
            var self = this;

            if (
                $(analyticSystem.elementTypeSelectors.orderSubmitBtn).length > 0
                || analyticSystem.settings.handCall !== false
            ) {
                $('body').on('phoneConfirmed', function () {
                    if (analyticSystem.settings.debug)
                        console.info('GA event phone confirmation');

                    if(!self.settings.phoneConfirmed){
                        self.settings.phoneConfirmed = true;
                        var products = self.dataSets.getBasketProducts({});
                        self.gaObject.getOrderPhoneConfirmSetGaEventHandler(products);
                    }
                });
            }
        },

        // Order base info set event handler
        orderBaseInfoTriggerEventHandler : function () {
            var self = this;

            if (
                $(analyticSystem.elementTypeSelectors.orderSubmitBtn).length > 0
                || analyticSystem.settings.handCall !== false
            ) {
                $('body').on('setBaseInfo', function () {
                    if (analyticSystem.settings.debug)
                        console.info('GA event base info set');

                    if(self.settings.phoneConfirmed && !self.settings.baseInfoFilled) {
                        self.settings.baseInfoFilled = true;
                        var products = self.dataSets.getBasketProducts({});
                        self.gaObject.getOrderBaseInfoSetGaEventHandler(products);
                        $('body').trigger("setDelivery");
                        $('body').trigger("setPayment");
                    }
                });
            }
        },

        // Order delivery set event handler
        orderDeliverySetTriggerEventHandler : function () {
            var self = this;

            if (
                $(analyticSystem.elementTypeSelectors.orderSubmitBtn).length > 0
                || analyticSystem.settings.handCall !== false
            ) {
                $('body').on('setDelivery',function (event, selectedOption) {
                    if (analyticSystem.settings.debug)
                        console.info('GA event delivery set');

                    if(self.settings.phoneConfirmed && self.settings.baseInfoFilled) {
                        if (typeof selectedOption !== "undefined") {
                            self.settings.deliveryOption = selectedOption.textContent;
                        } else {
                            self.settings.deliveryOption = $(self.dataSets.dataSelectors.orderPages.deliveryFieldSet.container
                                + " " + self.dataSets.dataSelectors.orderPages.deliveryFieldSet.option + ".active .delivery-info__header .header"
                            ).contents()[0].textContent;
                        }

                        var products = self.dataSets.getBasketProducts({});
                        self.gaObject.getOrderDeliverySetGaEventHandler(products);
                    }
                });
            }
        },

        // Order payment set event handler
        orderPaymentSetTriggerEventHandler : function () {
            var self = this;

            if (
                $(analyticSystem.elementTypeSelectors.orderSubmitBtn).length > 0
                || analyticSystem.settings.handCall !== false
            ) {
                $('body').on('setPayment', function () {
                    if (analyticSystem.settings.debug)
                        console.info('GA event payment set');

                    if(self.settings.phoneConfirmed && self.settings.baseInfoFilled) {
                        var products = self.dataSets.getBasketProducts({});
                        self.gaObject.orderPaymentSetGaEventHandler(products);
                    }
                });
            }
        },

        // Order submit btn click event handler
        orderSubmitTriggerEventHandler : function () {
            var self = this;

            if (
                $(analyticSystem.elementTypeSelectors.orderSubmitBtn).length > 0
                || analyticSystem.settings.handCall !== false
            ) {
                $('body').on('finishCheckout', function (event, orderNumber) {
                    if (analyticSystem.settings.debug)
                        console.info('finish checkout order submit');

                    self.settings.orderID = orderNumber;
                    analyticSystem.orderFinishClickEventHandler();
                });
            }
        },

        // Order submit btn click event handler
        orderOneClickSubmitTriggerEventHandler : function () {
            var self = this;

            if (
                $(analyticSystem.elementTypeSelectors.orderOneClickSubmitBtn).length > 0
                || analyticSystem.settings.handCall !== false
            ) {
                $('body').on('oneClickCheckout', function (event, orderNumber) {
                    if (analyticSystem.settings.debug)
                        console.info('one click checkout submit');

                    self.settings.orderID = orderNumber;
                    analyticSystem.orderOneClickCheckoutEventHandler();
                });
            }
        },

        // detail page one click order event handler
        catalogDetailOneClickOrderEventHandler : function () {
            var self = this;

            if (
                $(analyticSystem.pageSelectors.catalogDetailPage).length > 0
                || analyticSystem.settings.handCall !== false
            ) {

                $('body').on('oneClickDetail', function (event, orderNumber) {
                    if (analyticSystem.settings.debug)
                        console.info('detail page one click form submit');
                    var products = self.dataSets.getDetailProducts($(self.dataSets.dataSelectors.detailPageAttr.item))
                    self.settings.orderID = +orderNumber.toString();
                    self.settings.calculatedRevenue = (products[0].price * products[0].quantity).toString();

                    self.yandexGoalOneClickDetailFinish();
                    self.gaObject.catalogDetailOneClickOrderGaEventHandler(products);
                    self.fbObject.oneClickDetailFbqHandler(products);
                    self.mtObject.oneClickDetailMyTargetHandler(products);
                    self.vkObject.oneClickDetailVKHandler(products);

                    self.retailRocket.orderFinishCheckoutRRocketHandler(products);
                    self.criteoObject.catalogDetailOneClickCriteoHandler(products);
                });
            }
        },

        // personal page save event handler

        personalPageSaveEventHandler : function(){
            var self = this;

            $(document).on('savePersonalPage', function () {
                self.retailRocket.getEmailRRocketHandler();
            });
        },

        // ya counter is active
        isYaCounterActive: function() {
                if (typeof ym === "function") {
                    return true;
                }
                if (analyticSystem.settings.debug)
                    console.info('ya counter not initialized');
                return false;
        },

        //metrika checkout finish goal
        yandexGoalCheckoutFinish: function() {
            if (!this.isYaCounterActive()) {
                return false;
            }
            ym(26291919, 'reachGoal', 'checkout_finish'); return true; // заказ завершился нажатием кнопки и формированием номера
        },

        //metrika checkout finish goal
        yandexGoalOneClickCheckoutFinish: function() {
            if (!this.isYaCounterActive()) {
                return false;
            }
            ym(26291919, 'reachGoal', 'oneclick_checkout_finish'); return true; // заказ завершился нажатием кнопки и формированием номера
        },

        //metrika catalog page loaded

        yandexGoalCatalogPageLoad: function(){
            if (!this.isYaCounterActive()) {
                return false;
            }
            ym(26291919, 'reachGoal', 'catalog__page'); return true; // посещена страница каталога
        },

        //metrika detail page
        yandexGoalDetailPageLoad: function() {
            if (!this.isYaCounterActive()) {
                return false;
            }
            ym(26291919, 'reachGoal', 'catalog_detail_page'); return true; // посещена страница товара
        },

        //metrika detail finish goal
        yandexGoalOneClickDetailFinish: function() {
            if (!this.isYaCounterActive()) {
                return false;
            }
            ym(26291919, 'reachGoal', 'oneclick_detail_finish'); return true; // заказ завершился нажатием кнопки и формированием номера
        },

        //metrika goals
        yandexGoalsAll: function() {
            if (!this.isYaCounterActive()) {
                return false;
            }

            $('body').on('click', analyticSystem.elementTypeSelectors.basketAddItemProductBtn, function(){
                // Клик по кнопке "в корзину" со страницы карточки товара или с любой другой страницы
                ym(26291919, 'reachGoal', 'detail_tocart'); return true;
            });

            $('body').on('click', analyticSystem.elementTypeSelectors.basketAddItemPreorderProductBtn, function(){
                // Клик по кнопке "предзаказ" со страницы карточки товара или с любой другой страницы
                ym(26291919, 'reachGoal', 'detail_tocart'); return true;
            });

            $('body').on('click', '.ya-detail-one-click', function(){
                ym(26291919, 'reachGoal', 'cart_fastbuy'); return true;  // Клик на карточе товара по кнопке "В один клик"
            });

            $('body').on('click', '.ya-share2__item', function(){
              ym(26291919, 'reachGoal', 'share_block'); return true;  // Клик на карточе товара по блоку "Поделиться"
            });

            $('body').on('click', '.goods-insta__item', function(){
              ym(26291919, 'reachGoal', 'insta_block'); return true;  // Клик на карточе товара по блоку c Instagram
            });

            $('body').on('click', analyticSystem.elementTypeSelectors.favoriteAddItemProductBtn, function(){
                ym(26291919, 'reachGoal', 'favorite'); return true; // Клик по кнопке "В избранное"
            });

            $('.basket_area').on('click', '.checkout_start', function(){
                ym(26291919, 'reachGoal', 'checkout_header'); return true; // клик по кнопке "Оформить заказ" из шапки -> переход на страницу order
            });

            $(document).on('click', '.cart__popup .checkout_start', function(){
                ym(26291919, 'reachGoal', 'checkout_modal'); return true; // клик по кнопке "Оформить заказ" из попапа -> переход на страницу order
            });


            $('.ds-basket').on('click', '.ya-one-click', function(){
                ym(26291919, 'reachGoal', 'link_success_order_custom'); return true; // Клик в корзине по кнопке "В один клик"
            });

            $('.ds-basket').on('click', '.ya-to-order',function(){
                ym(26291919, 'reachGoal', 'to_order'); return true; // Новая корзина - переход К оформлению заказа (клик по кнопке К оформлению на странице
            });

            $('.ds-checkout__content').on('click', analyticSystem.elementTypeSelectors.orderSubmitBtn, function(){
                ym(26291919, 'reachGoal', 'checkout'); return true; // Новая корзина - Оформление заказа (клик по кнопке Оформить заказ на странице order)
            });
        },

        // check counters objects ready for work with them
        checkCountersObjects: function(skip) {
            var skip = skip || false,
                objectsReady = false;
            if (skip || (this.isYaCounterActive() !== 'undefined' && this.vkObject.isVKPixelActive() && this.fbObject.isFbqPixelActive())) {
                objectsReady = true;
            }
            return objectsReady;
        },

        // Init method
        init : function (skip) {
            var skip = skip || false;
            if(analyticSystem.checkCountersObjects(skip) && !this.settings.methodsFired) {
                if (analyticSystem.settings.debug)
                    console.info('init is fired and skip param is '+ skip + ' and objectsReady = ' + analyticSystem.checkCountersObjects());
                this.settings.methodsFired = true;
                this.gaObject.clearDatalayer();
                // Pages load event handlers call
                this.catalogListPageEventHandler();
                this.catalogDetailPageEventHandler();
                this.orderBasePageEventHandler();
                this.orderChekoutPageEventHandler();

                this.pageViewEventHandler();

                // Pages events handlers call
                this.catalogListProductClickEventHandler();
                this.basketAddItemEventHandler();
                this.basketAddItemPreorderEventHandler();
                this.basketDeleteItemEventHandler();

                this.orderPhoneConfirmTriggerEventHandler();
                this.orderBaseInfoTriggerEventHandler();
                this.orderDeliverySetTriggerEventHandler();
                this.orderPaymentSetTriggerEventHandler();

                this.orderSubmitTriggerEventHandler();

                this.orderOneClickSubmitTriggerEventHandler();

                this.catalogDetailOneClickOrderEventHandler();

                this.yandexGoalsAll();

                this.personalPageSaveEventHandler();
            }
        },

        fbObject : {
            name : 'fbObject',

            //Facebook pixel is active
            isFbqPixelActive : function(){
                if (typeof fbq === "function") {
                    return true;
                }
                if (analyticSystem.settings.debug)
                    console.info('fbq pixel not initialized');
                return false;
            },

            //Facebook add to cart event
            basketAddItemFbqHandler : function(products){
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': fb add to cart event handler started');
                var self = this, data = [], calculated_value = 0.00;
                if (!self.isFbqPixelActive()) {
                    return false;
                }
                products.forEach(function (i, e) {
                    data.push({
                        'id': i.id,
                        'quantity': i.quantity,
                        'item_price': i.price,
                    });
                    calculated_value += i.price;
                });
                calculated_value = parseFloat(calculated_value).toFixed(2);
                fbq('track', 'AddToCart', {
                    currency: analyticSystem.getBaseCurrencyCode(),
                    contents: data,
                    content_type: 'product',
                    value: calculated_value
                });
            },

            //Facebook one click detail action
            oneClickDetailFbqHandler : function(itemList){
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': fb one click detail action');
                var self = this, data = [];
                if (!self.isFbqPixelActive()) {
                    return false;
                }

                itemList.forEach(function (i, e) {
                    data.push({
                        'id': i.id,
                        'quantity': i.quantity,
                        'item_price': i.price,
                    });
                });
                fbq('track', 'Purchase', {
                    value: analyticSystem.settings.calculatedRevenue,
                    currency: analyticSystem.getBaseCurrencyCode(),
                    contents: data,
                    content_type: 'product',
                });
            },

            //Facebook finish checkout action
            orderFinishCheckoutFbqHandler : function(itemList){
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': fb finish checkout action');
                var self = this, data = [];
                if (!self.isFbqPixelActive()) {
                    return false;
                }

                itemList.forEach(function (i, e) {
                    data.push({
                        'id': i.id,
                        'quantity': i.quantity,
                        'item_price': i.price,
                    });
                });

                fbq('track', 'Purchase', {
                    value: $(analyticSystem.dataSets.dataSelectors.orderPages.checkoutFinishFieldSet.container + " " + analyticSystem.dataSets.dataSelectors.orderPages.checkoutFinishFieldSet.orderRevenue).data('order-revenue'),
                    currency: analyticSystem.getBaseCurrencyCode(),
                    contents: data,
                    content_type: 'product',
                });
            },

            //Facebook one click checkout action
            orderOneClickCheckoutFbqHandler : function(itemList){
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': fb one click checkout action');

                var self = this, data = [];
                if (!self.isFbqPixelActive()) {
                    return false;
                }

                itemList.forEach(function (i, e) {
                    data.push({
                        'id': i.id,
                        'quantity': i.quantity,
                        'item_price': i.price,
                    });
                });

                fbq('track', 'Purchase', {
                    value: $(analyticSystem.dataSets.dataSelectors.orderPages.checkoutOneClickFieldSet.orderRevenue).data('basket-revenue'),
                    currency: analyticSystem.getBaseCurrencyCode(),
                    contents: data,
                    content_type: 'product',
                });
            },
        },

        vkObject : {
            name : 'vkObject',

            // Get vk pricelist id
            getVkPriceListId : function () {
                return 2992;
            },
            //VK pixel is active
            isVKPixelActive : function() {
                if (typeof VK === "object") {
                    return true;
                }
                return false;
            },

            VKPushData : function(event_type, data) {
                var self = this;
                if (!this.isVKPixelActive()) {
                    return false;
                }
                VK.Retargeting.ProductEvent(self.getVkPriceListId(), event_type, data);
            },

            //vk finish checkout action
            orderFinishCheckoutVKHandler : function(itemList) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': vk finish checkout action');
                var self = this;
                var data = {
                    'products':[],
                };
                itemList.forEach(function (i, e) {
                    data.products.push({
                        "id": i.id,
                        "price": i.price
                    });
                });
                self.VKPushData("purchase", data);
            },

            //vk one click detail action
            oneClickDetailVKHandler : function(itemList) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': vk one click detail action');
                var self = this;
                var data = {
                    'products':[],
                };
                itemList.forEach(function (i, e) {
                    data.products.push({
                        "id": i.id,
                        "price": i.price
                    });
                });
                self.VKPushData("purchase", data);
            },

            //vk one click checkout action
            orderOneClickCheckoutVKHandler : function(itemList) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': vk one click checkout action');
                var self = this;
                var data = {
                    'products':[],
                };
                itemList.forEach(function (i, e) {
                    data.products.push({
                        "id": i.id,
                        "price": i.price
                    });
                });
                self.VKPushData("purchase", data);
            },

            //vk cart view
            orderBasePageVkEventHandler : function(itemList) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': vk cart page event handler started');
                var self = this;
                var data = {
                    'products':[],
                    "total_price": $(analyticSystem.dataSets.dataSelectors.orderPages.totalFieldSet.summPrice + " span").text().replace(/[^-0-9]/gim,'') - $(analyticSystem.dataSets.dataSelectors.orderPages.totalFieldSet.shipping + " span").text().replace(/[^-0-9]/gim,'')
                };
                itemList.forEach(function (i, e) {
                    data.products.push({
                        "id": i.id,
                        "price": i.price
                    });
                });
                self.VKPushData("init_checkout", data);
            },

            //vk add to cart
            basketAddItemVkHandler : function(products) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': vk add to cart event handler started');
                var self = this;
                var data = {'products':[
                        {
                            "id": products.map(function(o) { return o.id;})[0],
                            "price": products.map(function(o){ return o.price;})[0]
                        }
                    ]};
                self.VKPushData("add_to_cart", data);
            },
        },

        mtObject : {
            name : 'mtObject',

            // Get my target pricelist id
            getMyTargetPriceListId : function () {
                return 1;
            },

            //my target finish checkout action
            orderFinishCheckoutMyTargetHandler : function(itemList) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': mytarget finish checkout action');
                var self = this;
                var _tmr = _tmr || [];
                _tmr.push({
                    list: self.getMyTargetPriceListId(),
                    type: 'itemView',
                    pagetype: 'purchase',
                    productid: itemList.map(function(o) { return o.id;}),
                    totalvalue: $(analyticSystem.dataSets.dataSelectors.orderPages.checkoutFinishFieldSet.container + " " + analyticSystem.dataSets.dataSelectors.orderPages.checkoutFinishFieldSet.orderRevenue).data('order-revenue')
                });
            },

            //my target one click detail action
            oneClickDetailMyTargetHandler : function(itemList) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': mytarget one click detail action');
                var self = this;
                var _tmr = _tmr || [];
                _tmr.push({
                    list: self.getMyTargetPriceListId(),
                    type: 'itemView',
                    pagetype: 'purchase',
                    productid: itemList.map(function(o) { return o.id;}),
                    totalvalue: analyticSystem.settings.calculatedRevenue
                });
            },

            //my target one click checkout action
            orderOneClickCheckoutMyTargetHandler : function(itemList) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': mytarget one click checkout action');
                var self = this;
                var _tmr = _tmr || [];
                _tmr.push({
                    list: self.getMyTargetPriceListId(),
                    type: 'itemView',
                    pagetype: 'purchase',
                    productid: itemList.map(function(o) { return o.id;}),
                    totalvalue: $(analyticSystem.dataSets.dataSelectors.orderPages.checkoutOneClickFieldSet.orderRevenue).data('basket-revenue')
                });
            },

            //my target add to cart event
            basketAddItemMyTargetHandler : function(products) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': mytarget add to cart event handler started');
                var self = this;
                var _tmr = _tmr || [];
                _tmr.push({
                    list: self.getMyTargetPriceListId(),
                    type: 'itemView',
                    pagetype: 'cart',
                    productid: products.map(function(o) { return o.id;}),
                    totalvalue: products.map(function(o) { return o.price;})
                });
            },

            //my target cart view
            orderBasePageMyTargetEventHandler : function(itemList) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': mytarget cart page event handler started');
                var self = this;
                var _tmr = _tmr || [];
                _tmr.push({
                    list: self.getMyTargetPriceListId(),
                    type: 'itemView',
                    pagetype: 'cart',
                    productid: itemList.map(function(o) { return o.id;}),
                    totalvalue: $(analyticSystem.dataSets.dataSelectors.orderPages.totalFieldSet.summPrice + " span").text().replace(/[^-0-9]/gim,'') - $(analyticSystem.dataSets.dataSelectors.orderPages.totalFieldSet.shipping + " span").text().replace(/[^-0-9]/gim,''),
                });
            },

            //my target page view
            catalogDetailPageMyTargetEventHandler : function(products) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': mytarget detail page event handler started');

                var self = this;
                var _tmr = _tmr || [];
                _tmr.push({
                    list: self.getMyTargetPriceListId(),
                    type: 'itemView',
                    pagetype: 'product',
                    productid: products.map(function(o) { return o.id;})[0],
                    totalvalue: products.map(function(o) { return o.price;})[0]
                });
            },
        },

        retailRocket : {
            name : 'retailRocket',

            catalogDetailPageRRocketEventHandler : function (itemList) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': detail page event handler started');

                var self = this;
                var data = [];
                itemList.forEach(function (i, e) {
                    data = i.offersId;
                });
                (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
                    try{ rrApi.groupView(data,{"stockId": analyticSystem.settings.stock_id}); } catch(e) {}
                })
            },

            catalogListPageRRocketEventHandler : function () {
                if ($('[' + analyticSystem.dataSets.dataSelectors.attrs.categoryId + ']').attr(analyticSystem.dataSets.dataSelectors.attrs.categoryId) > 0) {
                    if (analyticSystem.settings.debug)
                        console.info(this.name + ': catalog category page event handler started');

                    var self = this;
                    (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function () {
                         try { rrApi.categoryView($('[' + analyticSystem.dataSets.dataSelectors.attrs.categoryId + ']').attr(analyticSystem.dataSets.dataSelectors.attrs.categoryId)); } catch(e) {}
                    })
                }
            },

            basketAddItemRRocketHandler : function(products) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': add to cart event handler started');
                var self = this;

                (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function () {
                    try { rrApi.addToBasket(parseInt(products.map(function(o) { return o.id;})[0]),{'stockId': analyticSystem.settings.stock_id}) } catch(e) {}
                })
            },

            orderFinishCheckoutRRocketHandler : function(itemList) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ':  finish checkout action');
                var self = this;
                var data = {
                    "transaction": analyticSystem.settings.orderID,
                    "items": []
                };
                itemList.forEach(function (i, e) {
                    data.items.push({
                        "id": i.id,
                        "qnt": i.quantity,
                        "price": i.price
                    });
                });
                (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
                    try {
                        rrApi.order(data);
                    } catch(e) {}
                });
            },

            getEmailRRocketHandler : function () {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ':  transmitted email');
                (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function () {
                    try {
                        if(!!$(window.analyticSystem.dataSets.dataSelectors.personalPage.email).val())
                            rrApi.setEmail($(window.analyticSystem.dataSets.dataSelectors.personalPage.email).val(),{"stockId": window.analyticSystem.settings.stock_id});
                        if(!!$(window.analyticSystem.dataSets.dataSelectors.orderPages.baseInfoFieldSet.email).val())
                            rrApi.setEmail($(window.analyticSystem.dataSets.dataSelectors.orderPages.baseInfoFieldSet.email).val(),{"stockId": window.analyticSystem.settings.stock_id});
                    }catch(e) {}
                });
            }
        },

        criteoObject : {
            name : 'criteoObject',

            // Get my target pricelist id
            getCriteoAccount : function () {
                return 44817;
            },
            
            //criteo Push
            CriteoPushData : function(email, data) {
                var self = this;
                window.criteo_q = window.criteo_q || [];
                var deviceType = /iPad/.test(navigator.userAgent) ? 't' : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? 'm' : 'd';
                email = email || analyticSystem.settings.email || '';
                window.criteo_q.push(
                    { "event": 'setAccount', "account": self.getCriteoAccount() },
                    { "event": 'setEmail', "email": email },
                    { "event": 'setSiteType', "type": deviceType },
                    data
                );
            },

            //criteo watch search page
            searchPageCriteoEventHandler : function(itemList) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': criteo search page event handler started');
                var self = this;
                var data = {
                    "event": 'viewList',
                    "item": []
                };
                itemList.forEach(function (i, e) {
                    data.item.push({
                        "id": i.id,
                        "price": i.price,
                        "quantity": i.quantity
                    });
                });
                self.CriteoPushData('', data);
            },

            //criteo product view
            catalogDetailPageCriteoEventHandler : function(itemList) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': criteo detail page event handler started');
                var self = this;
                var data = {
                    "event": 'viewItem',
                    "item": []
                };
                itemList.forEach(function (i, e) {
                    data.item.push({
                        "id": i.id,
                        "price": i.price,
                        "quantity": i.quantity
                    });
                });
                self.CriteoPushData('', data);
            },

            //criteo catalog list page view
            catalogListPageCriteoEventHandler : function(itemList) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': criteo catalog category page event handler started');
                var self = this;
                var data = {
                    "event": 'viewList',
                    "item": []
                };
                itemList.forEach(function (i, e) {
                    data.item.push({
                        "id": i.id,
                        "price": i.price,
                        "quantity": i.quantity
                    });
                });
                self.CriteoPushData('', data);
            },

            //criteo cart view
            orderBasePageCriteoEventHandler : function(itemList) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': criteo cart page event handler started');
                var self = this;
                var email = $(analyticSystem.dataSets.dataSelectors.orderPages.basketFieldSet.email).val() || '';
                var data = {
                    "event": 'viewBasket',
                    "item": []
                };
                itemList.forEach(function (i, e) {
                    data.item.push({
                        "id": i.id,
                        "price": i.price,
                        "quantity": i.quantity
                    });
                });
                self.CriteoPushData( email, data);
            },

            //criteo finish checkout action
            orderFinishCheckoutCriteoHandler : function(itemList) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': criteo finish checkout action');
                var self = this;
                var email = $(analyticSystem.dataSets.dataSelectors.orderPages.thanxFieldSet.orderEmail).data('order-email') || '';
                var data = {
                    "event": 'trackTransaction',
                    "id": analyticSystem.settings.orderID,
                    "item": []
                };
                itemList.forEach(function (i, e) {
                    data.item.push({
                        "id": i.id,
                        "price": i.price,
                        "quantity": i.quantity
                    });
                });
                self.CriteoPushData(email, data);
            },

            //criteo one click checkout action
            orderOneClickCheckoutCriteoHandler : function(itemList) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': criteo one click checkout action');
                var self = this;
                var email = $(analyticSystem.dataSets.dataSelectors.orderPages.thanxFieldSet.orderEmail).data('order-email') || '';
                var data = {
                    "event": 'trackTransaction',
                    "id": analyticSystem.settings.orderID,
                    "item": []
                };
                itemList.forEach(function (i, e) {
                    data.item.push({
                        "id": i.id,
                        "price": i.price,
                        "quantity": i.quantity
                    });
                });
                self.CriteoPushData(email, data);
            },

            //criteo buy in one click event
            catalogDetailOneClickCriteoHandler : function(itemList) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': criteo buy in one click event handler started');
                var self = this;
                var data = {
                    "event": 'trackTransaction',
                    "id": window.analyticSystem.settings.orderID,
                    "item": []
                };
                itemList.forEach(function (i, e) {
                    data.item.push({
                        "id": i.id,
                        "price": i.price,
                        "quantity": i.quantity
                    });
                });
                self.CriteoPushData(window.analyticSystem.settings.email, data);
            },
        },

        gaObject : {
            name : 'gaObject',

            // Get events non interaction flag
            getEventNonInteraction : function (a) {
                return a ? "True" : "False";
            },

            // Get GTM event category
            getGTMeventCategory : function () {
                return 'Enhanced Ecommerce';
            },

            // Clear dataLayer method
            clearDatalayer : function () {
                window.dataLayer = window.dataLayer || [];
            },

            //ga
            catalogListPageGaEventHandler : function (impressions) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': ga catalog list page event handler started');

                var self = this, dataLayerObject = {};

                dataLayerObject = {
                    'ecommerce' : {
                        'currencyCode' : analyticSystem.getBaseCurrencyCode(),
                        'actionField' : analyticSystem.dataSets.getActionField(),
                        'impressions' : impressions,
                    },
                    'event' : 'gtm-ee-event',
                    'gtm-ee-event-category' : self.getGTMeventCategory(),
                    'gtm-ee-event-action' : 'Product Impressions',
                    'gtm-ee-event-non-interaction' : self.getEventNonInteraction(true)
                };
                dataLayer.push(dataLayerObject);

                // Window on scroll event handler call
                self.addWindowOnScrollDataLayerPush(dataLayerObject);
            },

            //ga page view
            catalogDetailPageGaEventHandler : function(products) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': ga detail page event handler started');

                var self = this, dataLayerObject = {};
                dataLayerObject = {
                    'ecommerce' : {
                        'currencyCode' : analyticSystem.getBaseCurrencyCode(),
                        'detail' : {
                            'actionField' : analyticSystem.dataSets.getActionField(),
                            'products' : products,
                        },
                        'impressions' : analyticSystem.dataSets.getImpressions(),
                    },
                    'event' : 'gtm-ee-event',
                    'gtm-ee-event-category' : self.getGTMeventCategory(),
                    'gtm-ee-event-action' : 'Product Details',
                    'gtm-ee-event-non-interaction' : self.getEventNonInteraction(true)
                };
                dataLayer.push(dataLayerObject);
                self.addWindowOnScrollDataLayerPush(dataLayerObject);
            },

            // Add on window scroll dataLayer object into array
            addWindowOnScrollDataLayerPush : function (dataLayerObject) {
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': ga onscroll data push started');

                var timeoutId;

                $(window).scroll(function() {
                    if(timeoutId) clearTimeout(timeoutId);
                    timeoutId = setTimeout(function() {
                        dataLayerObject.ecommerce.impressions = analyticSystem.dataSets.getImpressions();
                        if (dataLayerObject.ecommerce.impressions.length > 0)
                            dataLayer.push(dataLayerObject);
                    }, analyticSystem.settings.scrollTimeout);
                });
            },

            //ga target cart view
            orderBasePageGaEventHandler : function(itemList) {
                var self = this;
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': ga basket page event handler started');
                dataLayer.push({
                    'ecommerce' : {
                        'currencyCode' : analyticSystem.getBaseCurrencyCode(),
                        'checkout' : {
                            'actionField' : analyticSystem.dataSets.getOrderBaseActionField(),
                            'products' : itemList,
                        }
                    },
                    'event' : 'gtm-ee-event',
                    'gtm-ee-event-category' : self.getGTMeventCategory(),
                    'gtm-ee-event-action' : 'Checkout Step 1',
                    'gtm-ee-event-non-interaction' : self.getEventNonInteraction(false)
                });
            },

            //ga target checkout view
            orderCheckoutPageGaEventHandler : function(itemList) {
                var self = this;
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': ga checkout page event handler started');
                dataLayer.push({
                    'ecommerce' : {
                        'currencyCode' : analyticSystem.getBaseCurrencyCode(),
                        'checkout' : {
                            'actionField' : analyticSystem.dataSets.getOrderCheckoutActionField(),
                            'products' : itemList,
                        }
                    },
                    'event' : 'gtm-ee-event',
                    'gtm-ee-event-category' : self.getGTMeventCategory(),
                    'gtm-ee-event-action' : 'Checkout Step 2',
                    'gtm-ee-event-non-interaction' : self.getEventNonInteraction(false)
                });
            },

            //ga finish checkout action handler
            orderFinishCheckoutGaHandler : function(itemList) {
                var self = this;
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': ga finish checkout action');
                dataLayer.push({
                    'ecommerce' : {
                        'currencyCode' : analyticSystem.getBaseCurrencyCode(),
                        'purchase' : {
                            'actionField' : analyticSystem.dataSets.getCheckoutFinishActionField(),
                            'products' : itemList,
                        }
                    },
                    'event' : 'gtm-ee-event',
                    'gtm-ee-event-category' : self.getGTMeventCategory(),
                    'gtm-ee-event-action' : 'Purchase',
                    'gtm-ee-event-non-interaction' : self.getEventNonInteraction(false)
                });
            },

            //ga one click checkout action handler
            orderOneClickCheckoutGaHandler : function(itemList) {
                var self = this;
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': ga one click checkout action handler');
                dataLayer.push({
                    'ecommerce' : {
                        'currencyCode' : analyticSystem.getBaseCurrencyCode(),
                        'purchase' : {
                            'actionField' : analyticSystem.dataSets.getOneClickCheckoutActionField(),
                            'products' : itemList,
                        }
                    },
                    'event' : 'gtm-ee-event',
                    'gtm-ee-event-category' : self.getGTMeventCategory(),
                    'gtm-ee-event-action' : 'Purchase',
                    'gtm-ee-event-non-interaction' : self.getEventNonInteraction(false)
                });
            },

            //ga product list click detail handler
            catalogListProductGaClickEvent : function(products) {
                var self = this;
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': ga product list page to detail page event handler started');

                dataLayer.push({
                    'ecommerce' : {
                        'currencyCode' : analyticSystem.getBaseCurrencyCode(),
                        'click' : {
                            'actionField' : analyticSystem.dataSets.getActionField(),
                            'products' : products
                        }
                    },
                    'event' : 'gtm-ee-event',
                    'gtm-ee-event-category' : self.getGTMeventCategory(),
                    'gtm-ee-event-action' : 'Product Clicks',
                    'gtm-ee-event-non-interaction' : self.getEventNonInteraction(false)
                });
            },

            //GA add to cart event
            basketAddItemGaHandler : function(products) {
                var self = this;
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': ga add to cart event handler started');

                dataLayer.push({
                    'ecommerce' : {
                        'currencyCode' : analyticSystem.getBaseCurrencyCode(),
                        'add' : {
                            'products' : products
                        }
                    },
                    'event' : 'gtm-ee-event',
                    'gtm-ee-event-category' : self.getGTMeventCategory(),
                    'gtm-ee-event-action' : 'AddToCart',
                    'gtm-ee-event-non-interaction' : self.getEventNonInteraction(false)
                });
            },
            
            // GA add to cart item preorder
            basketAddItemPreorderGaHandler : function(products) {
                var self = this;
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': ga add to cart preorder event handler started');

                dataLayer.push({
                    'ecommerce' : {
                        'currencyCode' : analyticSystem.getBaseCurrencyCode(),
                        'add' : {
                            'products' : products
                        }
                    },
                    'event' : 'gtm-ee-event',
                    'gtm-ee-event-category' : "pre_order",
                    'gtm-ee-event-action' : 'preorder',
                    'gtm-ee-event-non-interaction' : self.getEventNonInteraction(false)
                });
            },
            
            //GA remove from cart event
            basketDeleteItemGaHandler : function(products) {
                var self = this;
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': ga remove from cart event handler started');

                dataLayer.push({
                    'ecommerce' : {
                        'currencyCode' : analyticSystem.getBaseCurrencyCode(),
                        'remove' : {
                            'products' : products
                        }
                    },
                    'event' : 'gtm-ee-event',
                    'gtm-ee-event-category' : self.getGTMeventCategory(),
                    'gtm-ee-event-action' : 'RemoveFromCart',
                    'gtm-ee-event-non-interaction' : self.getEventNonInteraction(false)
                });
            },

            //GA order phone confirm info set event
            getOrderPhoneConfirmSetGaEventHandler : function (products) {
                var self = this;
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': ga order phone confirm set event handler started');

                dataLayer.push({
                    'ecommerce' : {
                        'currencyCode' : analyticSystem.getBaseCurrencyCode(),
                        'checkout' : {
                            'actionField' : analyticSystem.dataSets.getOrderPhoneConfirmSetActionField(),
                            'products' : products,
                        }
                    },
                    'event' : 'gtm-ee-event',
                    'gtm-ee-event-category' : self.getGTMeventCategory(),
                    'gtm-ee-event-action' : 'Checkout Step 3',
                    'gtm-ee-event-non-interaction' : self.getEventNonInteraction(false)
                });
            },

            //GA order base user info set event
            getOrderBaseInfoSetGaEventHandler : function (products) {
                var self = this;
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': ga order base info set event handler started');

                dataLayer.push({
                    'ecommerce' : {
                        'currencyCode' : analyticSystem.getBaseCurrencyCode(),
                        'checkout' : {
                            'actionField' : analyticSystem.dataSets.getOrderBaseInfoSetActionField(),
                            'products' : products,
                        }
                    },
                    'event' : 'gtm-ee-event',
                    'gtm-ee-event-category' : self.getGTMeventCategory(),
                    'gtm-ee-event-action' : 'Checkout Step 4',
                    'gtm-ee-event-non-interaction' : self.getEventNonInteraction(false)
                });
            },

            //GA order delivery set event
            getOrderDeliverySetGaEventHandler : function (products) {
                var self = this;
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': ga order delivery set event handler started');

                dataLayer.push({
                    'ecommerce' : {
                        'currencyCode' : analyticSystem.getBaseCurrencyCode(),
                        'checkout' : {
                            'actionField' : analyticSystem.dataSets.getOrderDeliverySetActionField(),
                            'products' : products,
                        }
                    },
                    'event' : 'gtm-ee-event',
                    'gtm-ee-event-category' : self.getGTMeventCategory(),
                    'gtm-ee-event-action' : 'Checkout Step 5',
                    'gtm-ee-event-non-interaction' : self.getEventNonInteraction(false)
                });
            },

            //GA order payment set event
            orderPaymentSetGaEventHandler : function (products) {
                var self = this;
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': ga order payment set event handler started');

                dataLayer.push({
                    'ecommerce' : {
                        'currencyCode' : analyticSystem.getBaseCurrencyCode(),
                        'checkout' : {
                            'actionField' : analyticSystem.dataSets.getOrderPaymentSetActionField(),
                            'products' : products,
                        }
                    },
                    'event' : 'gtm-ee-event',
                    'gtm-ee-event-category' : self.getGTMeventCategory(),
                    'gtm-ee-event-action' : 'Checkout Step 6',
                    'gtm-ee-event-non-interaction' : self.getEventNonInteraction(false)
                });
            },

            //GA detail page one click order
            catalogDetailOneClickOrderGaEventHandler : function (products) {
                var self = this;
                if (analyticSystem.settings.debug)
                    console.info(this.name + ': ga detail page oneclick order event handler started');
                dataLayer.push({
                    'ecommerce' : {
                        'currencyCode' : analyticSystem.getBaseCurrencyCode(),
                        'purchase' : {
                            'actionField' : analyticSystem.dataSets.getProductDetailOneClickOrderFormSubmitActionField(),
                            'products' : products
                        }
                    },
                    'event' : 'gtm-ee-event',
                    'gtm-ee-event-category' : self.getGTMeventCategory(),
                    'gtm-ee-event-action' : 'Purchase one click order form',
                    'gtm-ee-event-non-interaction' : self.getEventNonInteraction(false)
                });
            },
        }
    }

    // Call analytics init method
    try {
        analyticSystem.init();
    } catch(e) {
        console.warn('something goes wrong while init');
    }
});

document.onreadystatechange = function() {
    //check document state is full complete and sources ready
    if (document.readyState == 'complete' && typeof analyticSystem === 'object') {
        if (analyticSystem.settings.debug)
            console.info('readyState complete and skip counters checks');
        //fire with skip object check
        analyticSystem.init(true);
    }
};