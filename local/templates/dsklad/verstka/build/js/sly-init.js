$('.js-catalog-menu').each(function(){

  var slideNum = $(this).data('slide');
  var  $frame  = $('.js-catalog-menu');

  (function () {
    var $wrap = $frame.parent();

    $('.js-catalog-menu[data-slide="'+slideNum+'"]').sly({
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



$(window).on('resize', function () {
  var $frame  = $('.js-catalog-menu');

  $frame.sly('reload');
});


(function () {
  var $frame = $('#cycleitems');
  var $wrap  = $frame.parent();

  // Call Sly on frame
  $frame.sly({
    horizontal: 1,
    itemNav: 'forceCentered',
    smart: 1,
    activateOn: 'click',
    mouseDragging: 1,
    touchDragging: 1,
    releaseSwing: 1,
    startAt: 1,
    scrollBy: 1,
    speed: 500,
    elasticBounds: 1,
    easing: 'easeOutExpo',
    dragHandle: 1,
    dynamicHandle: 1,
    clickBar: 1,

    // Cycling
    cycleBy: 'items',
    cycleInterval: 5000,
    pauseOnHover: 1,
  });

}());

$(window).on('resize', function () {
  var $frame  = $('#cycleitems');

  $frame.sly('reload');
});

$('.js-actual-offers-close').on('click', function () {
  goBack();
});

function goBack() {
  if (document.referrer === "") {
    window.location.href = '/';
  } else {
    window.history.back();
  }
}
