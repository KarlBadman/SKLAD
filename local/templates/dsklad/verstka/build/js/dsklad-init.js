window.dsBasket = {
  selectors : {
    flagRemove : ".js-basket-remove",
    containerRemove : ".ds-basket-good",
    divAlert : "alert alert--good-remove",
    link : ".ds-basket-good__descr a",

    flagWarranty : ".js-add-warranty",
    containerWarranty : "div",
    statusWarranty : ".warranty-status",
    containerTotal : ".js-warranty-total",
    fullWarranty : "warranty-full",
    removeWarranty : "alert--warranty-remove",
    addWarranty : "alert alert--warranty",

    flagCountPlus : ".js-count-plus",
    flagCountMinus : ".js-count-minus",
    flagNumber : ".js-number",
    containerAddMore : ".ds-basket-add-more",
    animateFadeIn : "fadein",
    animateFadeOut : "fadeout",

    slickInit: ".js-slider-recommend",

  },


  jsRemoveHandler : function () {
    let timeOut,
      _this = this,
      btnRemove = document.querySelectorAll(_this.selectors.flagRemove),
      div = document.createElement('divAlert');

    div.className = dsBasket.selectors.divAlert;

    for ( let i = 0; i < btnRemove.length; i++) {
      btnRemove[i].onclick = function(){
        let goodElement = this.closest(dsBasket.selectors.containerRemove);
        let deletedElement = this.parentNode.parentNode.querySelector(dsBasket.selectors.link).innerHTML;

        goodElement.remove();
        clearTimeout(timeOut);

        div.innerHTML = "<span>удалено из корзины</span><p>" + deletedElement + "</p>";

        document.body.appendChild(div);

        timeOut = setTimeout(function() {
          div.parentNode.removeChild(div);
        }, 4000);
      };
    }

  },

  jsAddWarrantyHandler : function () {
    let _this = this,
      timeOut,
      btnWarrantyText,
      btnWarranty = document.querySelector(_this.selectors.flagWarranty),
      notification = document.createElement(dsBasket.selectors.containerWarranty),
      itemStatus = document.querySelectorAll(dsBasket.selectors.statusWarranty),
      hidden = document.querySelector(dsBasket.selectors.containerTotal);

    btnWarrantyText = btnWarranty.innerHTML;
    notification.className = dsBasket.selectors.addWarranty;

    btnWarranty.onclick = function () {
      clearTimeout(timeOut);

      document.body.appendChild(notification);
      if (btnWarranty.classList.contains('added')) {
        notification.classList.add(_this.selectors.removeWarranty);
        notification.innerHTML = "<span>удалено из корзины</span><p>Расширенная гарантия на 6 мес.</p>";
        btnWarranty.classList.remove('added');
        btnWarranty.innerHTML = btnWarrantyText;

        hidden.classList.add('hidden');
        for (let i = 0; i < itemStatus.length; i++) {
          itemStatus[i].classList.remove(_this.selectors.fullWarranty);
          itemStatus[i].innerHTML = "Стандартная гарантия";
        }
      } else {
        notification.classList.remove(_this.selectors.fullWarranty);
        notification.innerHTML = "<span>добавлено в корзину</span><p>Расширенная гарантия на 6 мес.</p>";
        btnWarranty.classList.add('added');
        btnWarranty.innerHTML = "Отменить гарантию";

        hidden.classList.remove('hidden');

        for (let i = 0; i < itemStatus.length; i++) {
          itemStatus[i].classList.add(_this.selectors.fullWarranty);
          itemStatus[i].innerHTML = "Расширенная гарантия";
        }
      }

      //timeOut = setTimeout(function() {
      //	notification.parentNode.removeChild(notification);
      //}, 4000);
    }

  },

  jsCountGoodsHandler : function () {
    let _this = this,
      btnPlus = document.querySelectorAll(_this.selectors.flagCountPlus),
      btnMinus = document.querySelectorAll(_this.selectors.flagCountMinus);

    for (let i = 0; i < btnPlus.length; i++) {
      btnPlus[i].onclick = function() {
        let fieldValue = this.parentNode.querySelector(dsBasket.selectors.flagNumber);
        let boxNotification = this.parentNode.parentNode.parentNode.querySelector(dsBasket.selectors.containerAddMore);
        fieldValue.value++;

        // этот кусок нужен для того, чтобы показать анимацию при изменении количества товаров в корзине
        if ( fieldValue.value < 4 ) {
          boxNotification.classList.remove(dsBasket.selectors.animateFadeOut);
          boxNotification.classList.add(dsBasket.selectors.animateFadeIn);
        } else {
          boxNotification.classList.add(dsBasket.selectors.animateFadeOut);
          boxNotification.classList.remove(dsBasket.selectors.animateFadeIn);
        }// конец куска анимации при изменении количества товаров в корзине
      };
    }

    for (let i = 0; i < btnMinus.length; i++) {
      btnMinus[i].onclick = function() {
        let fieldValue = this.parentNode.querySelector(dsBasket.selectors.flagNumber);
        let boxNotification = this.parentNode.parentNode.parentNode.querySelector(dsBasket.selectors.containerAddMore);

        if ( fieldValue.value > 1 ) {
          fieldValue.value--;
        } else {
          return 1;
        }

        // этот кусок нужен для того, чтобы показать анимацию при изменении количества товаров в корзине
        if ( fieldValue.value < 4  ) {
          boxNotification.classList.remove(dsBasket.selectors.animateFadeOut);
          boxNotification.classList.add(dsBasket.selectors.animateFadeIn);
        } else if ( fieldValue.value !== 3 ) {
          boxNotification.classList.add(dsBasket.selectors.animateFadeOut);
          boxNotification.classList.remove(dsBasket.selectors.animateFadeIn);
        }// конец куска анимации при изменении количества товаров в корзине
      };
    }
  },

  jsBasketSlick: function() {
    $(dsBasket.selectors.slickInit).slick({
      appendArrows: ('.js-slider-recommend-arrows'),
      prevArrow: "<div class='ds-slider-arrows prev'></div>",
      nextArrow: "<div class='ds-slider-arrows next'></div>",
      infinite: false,
      arrows: true,
      dots: false,
      slidesToShow: 6,
      slidesToScroll: 6,
      responsive: [
        {
          breakpoint: 1280,
          settings: {
            slidesToShow: 5,
            slidesToScroll: 5,
          }
        },
        {
          breakpoint: 1140,
          settings: {
            slidesToShow: 4,
            slidesToScroll: 4,
          }
        },
        {
          breakpoint: 1000,
          settings: {
            slidesToShow: 5,
            slidesToScroll: 5,
          }
        },
        {
          breakpoint: 800,
          settings: {
            slidesToShow: 4,
            slidesToScroll: 4,
          }
        },
        {
          breakpoint: 630,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 3,
          }
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 2,
          }
        }
      ]}
    );
  },


  init : function () {
    this.jsRemoveHandler(); // анимация при удалении/восстановлении товара в корзине
    this.jsAddWarrantyHandler(); // расширенная/стандартная гарантия в корзине
    this.jsCountGoodsHandler(); // изменение количества товаров
    this.jsBasketSlick(); // слайдер в корзине
  }
};


dsBasket.init();





$('html').addClass((isMobile.any ? 'mobile' : 'desktop'));