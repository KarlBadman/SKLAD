$(function () {
    "use strict";

    window.catalogSectionList = {
        selectors: {
            catalog_menu_block:'.js-catalog-menu',
            catalogOwlSlider: '.js-catalog-owl-slider',
        },

        slySlider : function () {
            var self = this;
            $(self.selectors.catalog_menu_block).each(function(){

                var slideNum = $(this).data('slide');
                var  $frame  = $(self.selectors.catalog_menu_block);

                (function () {
                    var $wrap = $frame.parent();

                    $(self.selectors.catalog_menu_block+'[data-slide="'+slideNum+'"]').sly({
                        horizontal: 1,
                        itemNav: 'basic',
                        smart: 1,
                        activateOn: 'click',
                        mouseDragging: 1,
                        touchDragging: 1,
                        releaseSwing: 1,
                        startAt: 0,
                        scrollBy: 1,
                        scrollTrap: false,
                        speed: 300,
                        elasticBounds: 1,
                        easing: 'easeOutExpo',
                        dragHandle: 1,
                        dynamicHandle: 1,
                        clickBar: 1,

                        prevPage: $wrap.find('.prev[data-slide="'+slideNum+'"]'),
                        nextPage: $wrap.find('.next[data-slide="'+slideNum+'"]')
                    });
                }());
            });
        },

        owlCatalogSlider : function() {
          var owlSlider = $(catalogSectionList.selectors.catalogOwlSlider);

          owlSlider.owlCarousel({
            items: 1,
          });
        },

        init: function () {
            this.slySlider();
            this.owlCatalogSlider();
        }
    };
});

$(document).ready(function () {
    window.catalogSectionList.init();
});