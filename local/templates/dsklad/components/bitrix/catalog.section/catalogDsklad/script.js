$(function () {
    window.catalogSection = {
        selectors: {
            catalogSectionBox:'[data-name="catalog_section_list"]',
            catalogSectionItemBox: '[data-name="catalog_section_item_box"]',
            showMoreSelector: '[data-name="next_page"]',
            navBox: '[data-name="nav_box"]',
            addToFavorite: '.js-add-to-favorite',
            containerFavorite : "div",
            addFavorite : "alert alert--favorite",
            arrowNextPage: ".ds-pagination__next",
            sortSelect: "[name=\"SORT_CATALOG\"]",
            catalogItem: ".ds-catalog-item",
        },
        countElement: 16,

        jsAddToFavorite : function () {
            const elem = this;
            let timeOut;
            const btnFavorite = document.querySelectorAll(elem.selectors.addToFavorite);
            const notification = document.querySelector(catalogSection.selectors.containerFavorite);


            for (var i = 0; i < btnFavorite.length; i++) {
                btnFavorite[i].onclick = function () {
                    clearTimeout(timeOut);
                    const goodsName = this.getAttribute('data-product-name');
                    var productId = this.getAttribute('data-product-id');
                    document.body.appendChild(notification);
                    if ( this.classList.contains('ic-favorite_on')) {
                        $.ajax({
                            type: "POST",
                            url: window.templateFloderCatalogSection + '/ajax/favorite.php',
                            data: {'productId': productId},
                            success: function (msg) {
                            }
                        });

                        notification.classList.add(elem.selectors.removeFavorite);
                        notification.innerHTML = `<span>${goodsName}</span><p>Удалено из избранного</p>`;
                        notification.className = catalogSection.selectors.addFavorite;
                        this.classList.remove('ic-favorite_on');
                        this.classList.add('ic-favorite_off');

                    } else {
                        notification.innerHTML = `<span>${goodsName}</span><p>Добавлено в избранное</p>`;
                        notification.className = catalogSection.selectors.addFavorite;
                        this.classList.add('ic-favorite_on');
                        this.classList.remove('ic-favorite_off');
                        $.ajax({
                            type: "POST",
                            url: window.templateFloderCatalogSection + '/ajax/favorite.php',
                            data: {'productId': productId},
                            success: function (msg) {
                            }
                        });
                    }

                    timeOut = setTimeout(function () {
                        notification.parentNode.removeChild(notification);
                    }, 4000);
                }
            }

        },

        nextPage: function() {
            var self = this;
            var catalogSectionBox = $(self.selectors.catalogSectionItemBox);
            var newPage = $(self.selectors.showMoreSelector);
            var navBox = $(self.selectors.navBox);

            newPage.on('click', function(){
                $(this).prop("disabled", true);
                document.cookie = "ajax_get_page=Y;path=/";
                var ajaxurl = document.querySelector(self.selectors.arrowNextPage).getAttribute('href');
                if(ajaxurl!==undefined) {
                    request = $.ajax({
                        url: ajaxurl,
                        type: "POST"
                    });
                    request.done(function (response, textStatus) {
                        if(textStatus=='success'){
                            var doc = $(response);
                            catalogSectionBox.append(doc.find(self.selectors.catalogSectionItemBox).html());
                            navBox.html(doc.find(self.selectors.navBox).html());
                            self.jsAddToFavorite();
                            self.nextPage();
                        }
                        $(this).prop("disabled", false);
                    });
                }
            });
        },

        selectize: function() {
            $('.js-chosen-select').selectize({
                plugins: ['remove_button'],
                delimiter: ',',
                persist: false,
                create: function(input) {
                    return {
                        value: input,
                        text: input
                    }
                }
            });
        },

        sortCatalog: function(){
            $(document).on('change',this.selectors.sortSelect,function(){
                document.cookie = "SORT_CATALOG="+$(this).val()+";path=/";
                console.log(window.location);
                document.location = window.location.pathname;
            });
        },

        smartFilterAjax: function(url){
            var self = this;
            var catalogSectionBox = $(self.selectors.catalogSectionItemBox);
            if(url == undefined) return false;
            document.cookie = "COUNT_ELEMENT=" + $(self.selectors.catalogItem).length + ";path=/";
            document.cookie = "ajax_get_page=Y;path=/";
            var request = $.ajax({
                url: url,
                type: "POST"
            });
            request.done(function (response, textStatus) {
                if (textStatus == 'success') {
                    var doc = $(response);
                    catalogSectionBox.html(doc.find(self.selectors.catalogSectionItemBox).html());
                    self.jsAddToFavorite();
                }
            });
        },

        init: function () {
            this.jsAddToFavorite();
            this.nextPage();
            this.selectize();
            this.sortCatalog();
        }
    };
});

$(document).ready(function () {
    window.catalogSection.init();
});