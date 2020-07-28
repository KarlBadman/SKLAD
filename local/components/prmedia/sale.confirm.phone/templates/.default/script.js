var SaleConfirmPhone = function(options) {
    //Настройки модуля по умолчанию
    var defaultOptions = {
        payments: '.payment .options label',  //css-селектор кнопок выбора способа оплаты
        payment_confirm: '#l_3',  //css-селектор кнопки способа оплаты, при котором нужно подтверждать телефон
        phone_input: 'input[data-name="is_phone"]',  //css-селектор поля с номером телефона
        confirm_phone_button: '.confirm-phone-button',
        confirm_wrap: '.field-confirm-code',
        wait_time: 240,  //время до повторной отправки
        code_length: 4,  //длина кода
        debug: false,
    };
    // Объединяет настройки по умолчанию и настройки переданные в конструктор
    this.options = $.extend({}, defaultOptions, options);

    this.options.wait_time = parseInt(this.options.wait_time);
    this.options.code_length = parseInt(this.options.code_length);

    //Точка входа
    var _this = this;
    if (window.frameCacheVars !== undefined) {
        BX.addCustomEvent('onFrameDataReceived' , function() {
            _this.init();
        });
    } else {
        BX.ready(function() {
            _this.init();
        });
    }
};

SaleConfirmPhone.prototype = {
    init: function() {
        if(this.options.debug)
            console.info('phone confirm initiated');

        this.vars();
        this.addPhoneButton();
        this.handlers();

        this.confirm_input.mask('9'.repeat(this.options.code_length));
    },

    reinit : function () {
        var _this = this;

        if(this.options.debug)
            console.info('phone confirm re initiated');

        this.vars();
        this.addPhoneButton();
        if ($(this.options.payment_confirm + " input:checked").length > 0) {
            _this.checkStatus();
        } else {
            _this.hideConfirm();
        }

    },

    vars: function() {
        this.payments = $(this.options.payments);
        this.payment_confirm = $(this.options.payment_confirm);
        this.phone_input = $(this.options.phone_input);

        this.confirm_wrap = $(this.options.confirm_wrap);
        this.confirm_input_wrap = this.confirm_wrap.find('.input');
        this.confirm_input = this.confirm_wrap.find('input');
        this.confirm_success = this.confirm_wrap.find('.success');
        this.confirm_error = this.confirm_wrap.find('.error');
        this.confirm_error_phone = this.confirm_wrap.find('.js-error-phone');
        this.confirm_error_tries = this.confirm_wrap.find('.js-error-tries');
        this.confirm_error_code = this.confirm_wrap.find('.js-error-code');

        this.countdown = $('#countdown');
    },

    handlers: function() {
        var _this = this;

        /**
         * Переключение способов оплаты
         */
        $(document).on('click', this.options.payments, function() {
            if ($(this)[0] === _this.payment_confirm[0]) {
                _this.checkStatus();
            } else {
                _this.hideConfirm();
            }
        });

        /**
         * Кнопка "Подтвердить" в поле номера телефона
         */
        $(document).on('click', this.options.confirm_phone_button, function() {
            if ($(this).hasClass('confirmed')) {
                return;
            }
            _this.phone_input.blur();
            if (!_this.phone_input.parent().hasClass('error')) {
                _this.sendCodeRequest();
            }
        });

        /**
         * Ввод кода
         */
        $(document).on('keyup', this.options.confirm_wrap + ' input', function() {
            var cleanVal = _this.confirm_input.val().replace(/_/g, '');

            if (cleanVal.length === _this.options.code_length) {
                _this.checkCode();
            }
        });

        /**
         * Изменение номера телефона
         */
        $(document).on('change', this.options.phone_input, function() {
            if (_this.confirm_phone_button.is(':visible')) {
                _this.checkStatus();
            }
        });
    },

    /**
     * Добавляет кнопку в поле номера телефона
     */
    addPhoneButton: function() {
        if(this.options.debug)
            console.info('phone button adding started');

        this.phone_input.after('<div class="confirm-phone-button"></div>');
        this.confirm_phone_button = $('.confirm-phone-button');
    },

    /**
     * Отображает форму для ввода кода
     */
    showConfirmWrap: function() {
        if(this.options.debug)
            console.info('show confirm wrap started');
        this.initCountdown();
        this.confirm_input.val('');
        this.confirm_wrap.show();
        this.confirm_input.focus();
    },

    hideConfirmWrap: function() {
        this.confirm_wrap.hide();
    },

    /**
     * Скрывает все элементы (кнопка + форма) для подтверждения телефона
     */
    hideConfirm: function() {
        this.confirm_wrap.hide();
        this.confirm_phone_button.removeClass('js-need-confirm');
        this.confirm_phone_button.removeClass('confirmed');
        this.confirm_phone_button.hide();
    },

    /**
     * Скрывает все сообщения об ошибках в форме подтверждения кода
     */
    hideErrors: function() {
        this.confirm_input_wrap.removeClass('error');
        this.confirm_error.hide();
    },

    /**
     * Переключает состояние формы ввода кода в стандартное (без ошибок)
     */
    setConfirmWrapSuccessMode: function() {
        this.confirm_success.show();
        this.hideErrors();
        this.confirm_input_wrap.show();
    },

    /**
     * Переключает состояние формы ввода кода в "Телефон изменился"
     */
    setConfirmWrapErrorPhoneMode: function() {
        this.confirm_success.hide();
        this.hideErrors();
        this.confirm_error_phone.show();
        this.confirm_input_wrap.hide();
    },

    /**
     * Переключает состояние формы ввода кода в "Закончились попытки ввода"
     */
    setConfirmWrapErrorTriesMode: function() {
        this.confirm_success.hide();
        this.hideErrors();
        this.confirm_error_tries.show();
        this.confirm_input_wrap.hide();
    },

    /**
     * Переключает состояние формы ввода кода в "Неверный код"
     */
    setConfirmWrapErrorCodeMode: function() {
        this.hideErrors();
        this.confirm_error_code.show();
        this.confirm_input_wrap.addClass('error');
    },

    /**
     * Переключает состояние кнопки у номера телефона в "Подтвердить"
     */
    setConfirmButtonSendMode: function() {
        this.confirm_phone_button.text('подтвердить');
        this.confirm_phone_button.removeClass('confirmed');
        this.confirm_phone_button.addClass('js-need-confirm');
        this.confirm_phone_button.parents('.field__widget').css('margin-bottom', '15px');
        this.confirm_phone_button.after('<div class="error-text">Необходимо подтвердить номер телефона</div>');
    },

    /**
     * Переключает состояние кнопки у номера телефона в "Подтверждён"
     */
    setConfirmButtonConfirmedMode: function() {
        this.confirm_phone_button.text('подтверждён');
        this.confirm_phone_button.removeClass('js-need-confirm');
        this.confirm_phone_button.addClass('confirmed');
    },

    /**
     * Запрос на отправку кода подтверждения
     */
    sendCodeRequest: function() {
        if(this.options.debug)
            console.info('code sending started');

        var _this = this;

        $.ajax({
            url: '/ajax/confirm_phone.php',
            method: 'POST',
            dataType: 'json',
            data: {
                'action': 'send',
                'phone': this.phone_input.val()
            },
            beforeSend: function() {
                _this.confirm_phone_button.addClass('send');
            },
            success: function(data) {
                _this.confirm_phone_button.removeClass('send');

                if (data.status === 'ok') {
                    _this.countdown.data('time', data.time + '000');  //нули обязательны, т.к. php возращет время в секундах, а js нужны милисекунды

                    switch (data.type) {
                        case 'success':  //отправлен новый код либо ждём ввода кода
                            _this.setConfirmWrapSuccessMode();
                            break;
                        case 'another_phone':
                            _this.setConfirmWrapErrorPhoneMode();
                            break;
                        case 'tries_over':
                            _this.setConfirmWrapErrorTriesMode();
                            break;
                    }

                    _this.showConfirmWrap();
                } else {
                    console.warn(data.message);
                }
            }
        });
    },

    /**
     * Запрос текущего состояния для формы подтверждения
     */
    checkStatus: function() {
        if(this.options.debug)
            console.info('check status started');

        var _this = this;

        $.ajax({
            url: '/ajax/confirm_phone.php',
            method: 'POST',
            dataType: 'json',
            data: {
                'action': 'validate',
                'phone': this.phone_input.val()
            },
            beforeSend: function() {
                _this.confirm_phone_button.addClass('send');
            },
            success: function(data) {
                _this.confirm_phone_button.removeClass('send');
                _this.setConfirmButtonSendMode();

                if (data.status === 'ok') {
                    switch (data.type) {
                        case 'full':  //отобразить и кнопку и форму ввода кода
                            _this.setConfirmWrapSuccessMode();
                            _this.showConfirmWrap();
                            break;
                        case 'wait_phone':  //сменился телефон
                            _this.setConfirmWrapErrorPhoneMode();
                            _this.showConfirmWrap();
                            break;
                        case 'wait_tries':  //достингут лимит попыток ввода кода
                            _this.setConfirmWrapErrorTriesMode();
                            _this.showConfirmWrap();
                            break;
                        case 'confirmed':  //телефон уже подтверждён
                            _this.setConfirmButtonConfirmedMode();
                            break;
                    }

                    _this.confirm_phone_button.show();
                } else {
                    console.error(data.message);
                }
            }
        });
    },

    /**
     * Проверка кода
     */
    checkCode: function() {
        if(this.options.debug)
            console.info('check code status started');

        var _this = this;

        $.ajax({
            url: '/ajax/confirm_phone.php',
            method: 'POST',
            dataType: 'json',
            data: {
                'action': 'check',
                'phone': this.phone_input.val(),
                'code': this.confirm_input.val()
            },
            beforeSend: function() {
                _this.confirm_input_wrap.addClass('send');
                _this.confirm_input.attr('disabled', 'disabled');
            },
            success: function(data) {
                _this.confirm_input_wrap.removeClass('send');
                _this.confirm_input.removeAttr('disabled');

                if (data.status === 'ok') {
                    switch (data.type) {
                        case 'success':
                            _this.confirm_wrap.hide();
                            _this.setConfirmButtonConfirmedMode();
                            break;
                        case 'another_phone':
                            _this.setConfirmWrapErrorPhoneMode();
                            break;
                        case 'tries_over':
                            _this.setConfirmWrapErrorTriesMode();
                            break;
                        case 'wrong_code':
                            _this.setConfirmWrapErrorCodeMode();
                            break;
                    }
                } else {
                    console.error(data.message);
                }
            }
        });
    },

    /**
     * Инициализация обратного отсчёта
     */
    initCountdown: function() {
        if(this.options.debug)
            console.info('countdown started');

        var _this = this;
        var startDate = new Date(parseInt(this.countdown.data('time')));
        var finalDate = startDate.setSeconds(startDate.getSeconds() + parseInt(this.options.wait_time));

        this.countdown.countdown(finalDate)
            .on('update.countdown', function(event) {
                $(this).html(event.strftime('%M:%S'));
            })
            .on('finish.countdown', function(event) {
                _this.hideConfirmWrap();
            });
    },
};
