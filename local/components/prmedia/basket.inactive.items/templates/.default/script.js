$(document).ready(function() {
    if ($('.inactive-items__popup').length > 0) {
        $.fancybox({
            content: $('.inactive-items__popup'),
            padding: 0,
            fitToView: false,
            helpers: {
                overlay: {
                    closeClick: false
                }
            },
            tpl: {
                closeBtn: '<a title="Close" class="fancybox-item fancybox-close" href="javascript:;" style="top: 15px;"><span class="icon__cross2"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/local/templates/dsklad/images/sprite.svg#cross2"></use></svg></span></a>'
            },
            afterClose: function() {
                location.reload();
            }
        });
    }

    $('.show-more').click(function() {
        $('.list-show-more').slideToggle();
        $(this).toggleClass('show');
    });

    $('.related').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        prevArrow: '<span class="slick-prev"><span class="icon__larr"><svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#larr2"></svg></span></span>',
        nextArrow: '<span class="slick-next"><span class="icon__rarr"><svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#rarr2"></svg></span></span>',
        responsive: [
            {
                breakpoint: 521,
                settings: {
                    slidesToShow: 3,
                }
            },
            {
                breakpoint: 441,
                settings: {
                    slidesToShow: 5,
                    arrows: false,
                    dots: true,
                }
            },
        ]
    });
});