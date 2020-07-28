$(function () {
    "use strict";

    window.dsCatalogDetail = {
        selectors: {
            slickForInit: ".js-double-slider-for",
            slickNavInit: ".js-double-slider-nav",
            showMoreBtn: ".js-btn-more",
            showMoreBlock: ".ds-catalog-descr",
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

        },

        jsCatalogDetailSlider: function() {
            $(dsCatalogDetail.selectors.slickForInit).slick({
                prevArrow: "<div class='ds-slider-arrows prev'></div>",
                nextArrow: "<div class='ds-slider-arrows next'></div>",
                fade: true,
                arrows: true,
                dots: false,
                lazyLoad: 'ondemand',
                slidesToShow: 1,
                slidesToScroll: 1,
                asNavFor: dsCatalogDetail.selectors.slickNavInit,
                responsive: [
                    {
                        breakpoint: 655,
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
            const showMoreBtn = document.querySelector(dsCatalogDetail.selectors.showMoreBtn);

            showMoreBtn.addEventListener('click', descrFull, false);

            function descrFull() {
                this.previousElementSibling.classList.add('full');
                this.hidden = true;
            }
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

        jsAddToBasketAnimation: function() {
            const addToBasketBtn = document.querySelector(dsCatalogDetail.selectors.addToBasketBtn);

            addToBasketBtn.addEventListener('click', addToBasket);

            function addToBasket() {
                this.classList.add('active');
                this.innerText = 'Добавлено в корзину';
                this.setAttribute('disabled', 'disabled');

                setTimeout(function() {
                    addToBasketBtn.classList.remove('active');
                    addToBasketBtn.innerText = 'В корзину';
                    addToBasketBtn.removeAttribute('disabled');
                }, 2000);
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
                    notification.classList.add(elem.selectors.removeFavorite);
                    notification.innerHTML = `<span>${goodsName.innerText}</span><p>Удалено из избранного</p>`;
                    btnFavorite.classList.remove('added');
                    btnFavorite.innerHTML = btnFavoriteText;

                } else {
                    notification.innerHTML = `<span>${goodsName.innerText}</span><p>Добавлено в избранное</p>`;
                    btnFavorite.classList.add('added');
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
                fieldValue.value++;
            };

            btnMinus.onclick = function() {
                let fieldValue = this.parentNode.querySelector(dsCatalogDetail.selectors.flagNumber);

                if ( fieldValue.value > 1 ) {
                    fieldValue.value--;
                } else {
                    return 1;
                }
            };
        },

        init: function() {
            this.jsCatalogDetailSlider();
            this.jsShowMore();
            this.jsChangeColor();
            this.jsAddToBasketAnimation();
            this.jsAddToFavorite();
            this.jsScrollWidth();
            this.jsCountGoodsHandler();
        }

    };

    dsCatalogDetail.init();
});
