// pure modal popups
$(function () {
    "use strict";
    window.purepopup = {
        settings : {
            dsModalWidth : '',
            body : document.querySelector('body'),
            overlay_selector: '.ds-modal-overlay',
            overlay : document.querySelector(".ds-modal-overlay"),
            dsModalTarget : document.querySelector(".ds-modal"),
            dsModalParentId : '',
            closeButton : document.querySelectorAll(".js-ds-modal-close"),
            openButton : ".js-ds-modal",
            fullscreen : false,
            scrollTop: 0,
            closeBtnHtml: '<span class="icon-svg ic-close ds-modal-close js-ds-modal-close" onclick="purepopup.closePopup();"></span>',
            modalInnerHeight : document.querySelector('.ds-modal__inner')
        },

        openPopup : function() {
            purepopup.settings.overlay.classList.remove("closed");
            purepopup.settings.dsModalTarget.classList.remove("closed");
            purepopup.settings.body.classList.add("ds-modal-active");
            if(purepopup.settings.fullscreen){
                purepopup.settings.dsModalTarget.classList.add('fullscreen');

                var closeBtn = document.querySelector('.fullscreen');
                closeBtn.insertAdjacentHTML('afterbegin', purepopup.settings.closeBtnHtml);

            }

            purepopup.settings.scrollTop = window.pageYOffset;
            purepopup.settings.body.style.top = `-${purepopup.settings.scrollTop}px`;
            purepopup.settings.body.style.position = `fixed`;
        },

        ajaxToModal : function(url, callback, new_width, content, fullscreen){
            purepopup.settings.fullscreen = fullscreen || false;
            purepopup.settings.dsModalTarget.style.width = new_width + 'px';
            purepopup.settings.dsModalParentId = '';
            if(content && !url){
                purepopup.settings.dsModalParentId = content.prop('id');
                if(purepopup.settings.dsModalParentId.length > 0){
                    callback(content);
                } else {
                    callback(content.html());
                }
            } else {
                $.ajax({
                    url: url
                }).done(function(response) {
                    callback(response);
                    if(typeof($('.js-ds-slider')) === 'object') {
                        $('.js-ds-slider').slick({
                            appendArrows: ('.js-ds-slider-arrows'),
                            prevArrow: "<div class='ds-slider-arrows prev'></div>",
                            nextArrow: "<div class='ds-slider-arrows next'></div>",
                            fade: true,
                            arrows: true,
                            lazyLoad: 'ondemand',
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            responsive: [
                                {
                                    breakpoint: 1000,
                                    settings: {
                                        arrows: false
                                    }
                                }
                            ]
                        });
                    }
                });
            }
            this.openPopup();
        },

        modalFilling : function(responseText) {
            if(purepopup.settings.dsModalParentId.length > 0) {
                responseText.contents().detach().appendTo(purepopup.settings.dsModalTarget.querySelector(".ds-modal__inner"));
            } else {
                purepopup.settings.dsModalTarget.querySelector(".ds-modal__inner").innerHTML = responseText.trim();
            }

            for (var i = 0; i < purepopup.settings.dsModalTarget.getElementsByTagName('script').length; i++) {
                //https://stackoverflow.com/questions/8097558/proper-way-to-execute-javascript-returned-via-ajax-no-jquery
                eval(purepopup.settings.dsModalTarget.getElementsByTagName('script')[i].innerText);
            }
        },

        closePopup : function() {
            this.settings.overlay.classList.add("closed");
            this.settings.dsModalTarget.classList.add("closed");
            if(purepopup.settings.dsModalParentId.length > 0) {
                $('#' + purepopup.settings.dsModalParentId).append(this.settings.dsModalTarget.querySelector(".ds-modal__inner").childNodes); // возвращаем форму назад
                purepopup.settings.dsModalParentId = '';
            }
            this.settings.dsModalTarget.querySelector(".ds-modal__inner").innerHTML = '';
            purepopup.settings.body.classList.remove("ds-modal-active");
            if(purepopup.settings.fullscreen){
                purepopup.settings.dsModalTarget.classList.remove('fullscreen');

                var closeBtn = document.querySelector('.js-ds-modal-close');
                closeBtn.remove();
            }

            purepopup.settings.body.removeAttribute('style');
            window.scrollTo(0, purepopup.settings.scrollTop);

            purepopup.settings.modalInnerHeight.style.height = 'auto';
            purepopup.settings.modalInnerHeight.removeAttribute('style');
        },

        orderViaManager : function () {
            if (
                $('.sum-order__popup').length > 0) {
                purepopup.ajaxToModal(false, purepopup.modalFilling, 500, $('.sum-order__popup'));
            }
        },


        init : function () {
            $(document).on('click', purepopup.settings.overlay_selector, function(){
                purepopup.closePopup();
            });
            for (let i = 0; i < purepopup.settings.closeButton.length; i++) {
                purepopup.settings.closeButton[i].onclick = function () {
                    if (!purepopup.settings.overlay.classList.contains('closed')) {
                        purepopup.closePopup();
                    }
                };
            }
            $(document).on('click', purepopup.settings.openButton, function(e){
                purepopup.settings.dsModalWidth = this.getAttribute("data-ds-modal-width");
                e.preventDefault();
                let url = this.getAttribute('data-href') || this.getAttribute('href');
                if (url !== null){
                    purepopup.ajaxToModal(url, purepopup.modalFilling, purepopup.settings.dsModalWidth);
                }
            });

            purepopup.orderViaManager();
            $('.basket__page').on('DOMNodeInserted', '.sum-order__popup', function(){
                purepopup.orderViaManager();
            });

            document.addEventListener('keydown', function (event) {
                if (event.defaultPrevented) {
                    return;
                }
                var key = event.key || event.keyCode;
                if (key === 'Escape' || key === 'Esc' || key === 27) {
                    purepopup.closePopup();
                }
            });
        }
    };

    // Call popup init method
    purepopup.init();
});