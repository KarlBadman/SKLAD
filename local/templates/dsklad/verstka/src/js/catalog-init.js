$(function () {
  "use strict";

  window.catalog = {
    selectors: {
      filterBtn : document.querySelector('.js-filter-btn'),
      filterContent : document.querySelector('.ds-catalog__filter'),
      filterCloseBtn : document.querySelector('.js-filter-close'),
      html : document.querySelector('html'),
      body : document.querySelector('body'),

      filterSubmenuInit : document.querySelector('.js-subfilter'),
      filterSubmenuItems : document.querySelectorAll('.ds-filter__item'),

      sorting : document.querySelector('.js-catalog-sorting'),
      sortingItems : document.querySelectorAll('.ds-catalog-sorting__item'),
      sortingChosen : document.querySelector('.ds-catalog-sorting__chosen'),

      chosenSelect : document.querySelectorAll('.js-chosen-select'),

      addToFavorites: document.querySelectorAll('.js-add-to-favorite'),
    },

    jsSlickSlider: {
      slickSlider : function () {
        var $slider = $('.js-catalog-slider');

        if ($slider.length) {
          var currentSlide;
          var slidesCount;
          var sliderCounter = document.createElement('div');
          sliderCounter.classList.add('ds-catalog-slider__counter');

          var updateSliderCounter = function(slick, currentIndex) {
            currentSlide = slick.slickCurrentSlide() + 1;
            slidesCount = slick.slideCount;
            $(sliderCounter).text(currentSlide + '/' +slidesCount)
          };

          $slider.on('init', function(event, slick) {
            $slider.append(sliderCounter);
            updateSliderCounter(slick);
          });

          $slider.on('afterChange', function(event, slick, currentSlide) {
            updateSliderCounter(slick, currentSlide);
          });

          $slider.slick({
            prevArrow: "<div class='ds-catalog-slider-arrows prev'></div>",
            nextArrow: "<div class='ds-catalog-slider-arrows next'></div>",
            adaptiveHeight: true,
          });
        }
      },

      init: function () {
        this.slickSlider();
      }
    },

    jsSelectize: {
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

      init: function () {
        this.selectize();
      }
    },

    jsAddToFavorite : {
      addToFavorite: function () {
        const favorites = catalog.selectors.addToFavorites;

        for (var favorite of favorites) {
          favorite.addEventListener('click', function () {
            this.classList.toggle('added');
          })
        }

      },

      init: function () {
        this.addToFavorite();
      }
    },

    init: function () {
      this.jsSlickSlider.init();
      this.jsSelectize.init();
      this.jsAddToFavorite.init();
    }

  };

  catalog.init();
});
