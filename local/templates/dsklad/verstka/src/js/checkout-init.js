"use strict";

window.dsCheckout = {
	selectors : {
		taxpayerBtn: ".js-taxpayer",
		taxpayerInput: ".js-taxpayer-input",
		taxpayerPerson: ".payment-info__item--person",
		taxpayerLegal: ".payment-info__item--legal",

		recieverBtn: ".js-reciever",
		recieverBlock: ".js-reciever-block",
		recieverBlockClose: ".js-reciever-block-close",

		authorizClass: ".js-authoriz",

		radioBtn: ".js-delivery-radio",
		radioBlock: '.delivery-info',

		slickInit: ".js-slider-checkout"
	},

	jsTaxpayerAdd : function () {
		let taxpayerBtn = document.querySelector(dsCheckout.selectors.taxpayerBtn),
			taxpayerInput = document.querySelector(dsCheckout.selectors.taxpayerInput),
			taxpayerPerson = document.querySelectorAll(dsCheckout.selectors.taxpayerPerson),
			taxpayerLegal = document.querySelectorAll(dsCheckout.selectors.taxpayerLegal);

		taxpayerBtn.onclick = function () {
			if (this.classList.contains('active')) {
				let text = "Заказать как юр.лицо";
				taxpayerBtn.innerHTML = text;
				this.classList.remove('active');
				taxpayerInput.classList.add('hidden');

				for (let i = 0; i < taxpayerPerson.length; i++) {
					taxpayerPerson[i].classList.remove('hidden');
				}
				for (let i = 0; i < taxpayerLegal.length; i++) {
					taxpayerLegal[i].classList.add('hidden');
				}
			} else {
				let text = "Заказать как физ.лицо";
				taxpayerBtn.innerHTML = text;
				this.classList.add('active');
				taxpayerInput.classList.remove('hidden');

				for (let i = 0; i < taxpayerPerson.length; i++) {
					taxpayerPerson[i].classList.add('hidden');
				}
				for (let i = 0; i < taxpayerLegal.length; i++) {
					taxpayerLegal[i].classList.remove('hidden');
				}
			}

		};
	},


	jsReciever : function () {
		let recieverBtn = document.querySelector(dsCheckout.selectors.recieverBtn),
			recieverBlock = document.querySelector(dsCheckout.selectors.recieverBlock),
			recieverBlockClose = document.querySelector(dsCheckout.selectors.recieverBlockClose);

		if (document.querySelector(dsCheckout.selectors.authorizClass)) {
			recieverBtn.onclick = function () {
				recieverBlock.classList.remove('hidden');
				recieverBtn.classList.add('hidden');
			};

			recieverBlockClose.onclick = function () {
				recieverBlock.classList.add('hidden');
				recieverBtn.classList.remove('hidden');
			}
		}
	},


	jsDeliveryChosen : function() {
		let deliveryParent = $('.delivery-info__item');
		let deliveryHeader = $('.delivery-info__header');
		let deliveryResult = $('.delivery-info__item .delivery-info-result');
		let deliveryScope = $('.ds-checkout-form');

		deliveryScope.on('click', '.js-delivery-radio', function(){
			let elem = $(this).parents('.delivery-info__header');
			let active = $(this).parents('.delivery-info__item');
			deliveryParent.removeClass('active');
			deliveryHeader.removeClass('checked js-ds-modal');
			deliveryResult.addClass('hidden');
			$('.subheader').removeClass('hidden');
			$('.ds-checkout-form__item .ds-price').addClass('hidden');
			elem.addClass('checked js-ds-modal');
			active.addClass('active');
			$('.ds-checkout-form__item').removeClass('error');
		});

		deliveryScope.on('click', '.js-delivery-here', function () {
			let checkedHeader = $('.checked');
			$('.js-ds-modal-close').trigger('click');
			checkedHeader.next().removeClass('hidden');
			checkedHeader.find('.subheader').addClass('hidden');
			$('.checked .ds-price').removeClass('hidden');
		});

		deliveryParent.on('click', '.js-delivery-change', function () {
			deliveryHeader.trigger('click');
		});

		deliveryScope.on('click', '.js-courier-delivery-add', function () {
			let _this = $(this);

			_this.toggleClass('active');

			(_this.hasClass('active')) ? _this.text('Удалить') : _this.text('Добавить')

		})
	},


	jsPaymentChosen : function() {

		$('.js-payment').on('click', function(){
			let elem = $(this);
			$('.payment-info__item').removeClass('active');

			elem.addClass('active');
		});

	},


	jsPromo : function() {
		let value = $('.js-promo').val();
		if (value !== '') {
			let btn = $('.js-promo').parents('.active').find('button');
			btn.attr("disabled", true);
		}
	},

	jsSlick: function() {
		$(dsCheckout.selectors.slickInit).slick({
			appendArrows: ('.js-slider-arrows'),
			prevArrow: "<div class='ds-slider-arrows prev'></div>",
			nextArrow: "<div class='ds-slider-arrows next'></div>",
			infinite: false,
			arrows: true,
			dots: false,
			slidesToShow: 5,
			slidesToScroll: 5,
			responsive: [
				{
					breakpoint: 1000,
					settings: {
						slidesToShow: 7,
						slidesToScroll: 7,
					}
				},
				{
					breakpoint: 800,
					settings: {
						slidesToShow: 6,
						slidesToScroll: 6,
					}
				},
				{
					breakpoint: 640,
					settings: {
						slidesToShow: 5,
						slidesToScroll: 5,
					}
				},
				{
					breakpoint: 480,
					settings: {
						slidesToShow: 4,
						slidesToScroll: 4,
					}
				},
				{
					breakpoint: 400,
					settings: {
						slidesToShow: 3,
						slidesToScroll: 3,
					}
				}
			]}
		);
	},


	jsCheckoutModals : function() {

		function calculateScrollBarWidth() {
			return window.innerWidth - document.body.clientWidth;
		}

		$('.js-checkout-modal').on('click', function () {
			let _this = $(this);
			let modalTarget = _this.attr('data-target');
			let modalWidth = _this.attr('data-ds-modal-width');
			$('.ds-modal').addClass('closed');
			$('#' + modalTarget).removeClass('closed').css('width', modalWidth + 'px');
			$('.ds-modal-overlay').removeClass('closed');

			$('html').css({'padding-right' : calculateScrollBarWidth, 'overflow' : 'hidden'});
		});


		$('.js-ds-modal-close, .ds-modal-overlay').on('click', function () {
			closeModal();
		});

		document.onkeydown = function(evt) {
			evt = evt || window.event;
			if (evt.keyCode === 27) {
				closeModal();
			}
		};

		function closeModal() {
			$('.ds-modal').addClass('closed');
			$('.ds-modal-overlay').addClass('closed');
			$('html').css({'padding-right' : 0, 'overflow' : 'visible'});
		}

	},


	init : function () {
		this.jsTaxpayerAdd(); // Добавление ИНН для юрлиц и оплату
		this.jsReciever(); // Добавление другого получателя
		this.jsDeliveryChosen(); // Выбор типа доставки
		this.jsPaymentChosen(); // Выбор типа оплаты
		this.jsPromo(); // Промо успех или неудача
		this.jsSlick(); // слайдер в корзине
		this.jsCheckoutModals(); // модалки на странице checkout
 	}
};


dsCheckout.init();


window.Parsley.on('field:error', function() {
	$('.inp-info').addClass('hidden');


	if ( !$('.delivery-info__item').hasClass('active') ) {
		$('#checkout-delivery').addClass('error');
	}

});

$('#tel').parsley().on('field:success', function() {
	$('.inp-info').removeClass('hidden');
});

$('#station').parsley().on('field:success', function() {
	$('#checkout-delivery').removeClass('error');
});


