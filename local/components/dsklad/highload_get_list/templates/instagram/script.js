// Script js file
$(function () {
    window.instaWidget = {
        settings : {
            debug : false,
        },
        selectors : {
            jsSlider : {
                container : ".js-slider"
            },
            jsPopup : {
                flag : ".js-popup-init",
                container : ".instagram-popup"
            }
        },

        jsSliderHandler : function () {
            var _this = this, noOfSlides = 6, docWidth = $(document).width(),
                winWidth = $(window).width();

            if (_this.settings.debug)
                console.info("JS Slider handler function execute");

            $(_this.selectors.jsSlider.container).slick({
                rows: 2,
                slidesToShow: 3,
                responsive: [{
                    breakpoint: 1000,
                    settings: {
                        infinite: true,
                        rows: 2,
                        slidesToShow: 3
                    }
                }, {
                    breakpoint: 700,
                    settings: {
                        infinite: true,
                        rows: 1,
	                    fade: true,
	                    dots: true,
                        slidesToShow: 1
                    }
                }]
            });

            if(docWidth < 700 && noOfSlides > 5 ) {
                $(_this.selectors.jsSlider.container).slick('slickRemove',5);
                noOfSlides--;
            }
        },

        jsPopupHandler : function () {
            var _this = this;

            if (_this.settings.debug)
                console.info("JS popup handler function execute");

            $(_this.selectors.jsPopup.flag).on('click', function() {
                var popupInit = $(this).parent();
                popupInit.find(_this.selectors.jsPopup.container).fadeIn();
            });
        },

        init : function () {
            this.jsSliderHandler();
            this.jsPopupHandler();
        }
    }
    instaWidget.init();
});