$(function () {
  "use strict";

  window.voprosyOtvety = {
    selectors: {
      tabsGlobal : document.querySelector('.js-question-tags'),
      tabItems : document.querySelectorAll('.question-page__tab'),
      chapterGlobal : document.querySelector('.js-question-chapter'),
      chapterItems : document.querySelectorAll('.question-page__list-chapter'),
      body: document.querySelector('body'),
    },

    jsVoprosyOtvety : {
      voprosyOtvety : function() {
        const tabsGlobal = voprosyOtvety.selectors.tabsGlobal;
        const chapterGlobal = voprosyOtvety.selectors.chapterGlobal;
        const cleintWidth = document.body.clientWidth;

        if (!tabsGlobal) return;

        tabsGlobal.addEventListener('click', function(event){
          const target = event.target;
          const parent = target.closest('.question-page__tab');

          for(var tabItem of voprosyOtvety.selectors.tabItems){
            if (!parent.classList.contains('opened')) {
              tabItem.classList.remove('opened');
            }
          }

          if (!parent.classList.contains('opened')) {
            parent.classList.add('opened');
          }
        });

        chapterGlobal.addEventListener('click', function(event){
          const target = event.target;
          const parent = target.closest('.question-page__list-chapter');

          for(var chapterItem of voprosyOtvety.selectors.chapterItems){
            if (!parent.classList.contains('opened')) {
              chapterItem.classList.remove('opened');
            }
          }

          if (!parent.classList.contains('opened')) {
            parent.classList.add('opened');
          } else {
            if (!target.classList.contains('question-page__chapter-tab')) return;
            parent.classList.remove('opened');
          }

          if ( cleintWidth < 767 ) {
            if (!parent.classList.contains('opened')) return;
            $('html, body').animate({
              scrollTop: ($('.question-page__list-chapter.opened').offset().top) - 40
            }, 750);
          } else if ( cleintWidth > 767 && cleintWidth < 1023 ) {
            if (!parent.classList.contains('opened')) return;
            $('html, body').animate({
              scrollTop: ($('.question-page__list-chapter.opened').offset().top) - 12
            }, 750);
          }

        });

      },

      init : function () {
        this.voprosyOtvety();
      },
    },


    init: function() {
      this.jsVoprosyOtvety.init();
    }
  };

  voprosyOtvety.init();

});