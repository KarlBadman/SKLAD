$(function () {
    window.banner = {

        slickSlider: function () {
            var $slider = $('.js-catalog-slider');

            if ($slider.length) {
                var currentSlide;
                var slidesCount;
                var sliderCounter = document.createElement('div');
                sliderCounter.classList.add('ds-catalog-slider__counter');

                var updateSliderCounter = function (slick, currentIndex) {
                    currentSlide = slick.slickCurrentSlide() + 1;
                    slidesCount = slick.slideCount;
                    $(sliderCounter).text(currentSlide + '/' + slidesCount)
                };

                $slider.on('init', function (event, slick) {
                    $slider.append(sliderCounter);
                    updateSliderCounter(slick);
                });

                $slider.on('afterChange', function (event, slick, currentSlide) {
                    updateSliderCounter(slick, currentSlide);
                });

                $slider.slick({
                    prevArrow: "<div class='ds-catalog-slider-arrows prev'></div>",
                    nextArrow: "<div class='ds-catalog-slider-arrows next'></div>",
                    adaptiveHeight: true,
                });
            }
        },

        init: function(){
            this.slickSlider();
        },
    }
});

$(document).ready(function () {
    window.banner.init();
});