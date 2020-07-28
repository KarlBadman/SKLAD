(function () {
    window.saleConfirmPhone = {
        options: {
            wait_time: 240,  //время до повторной отправки
            code_length: 4,  //длина кода
            debug: false,
            reload: 'Y',
            no_confirm_code: [],
            reinited : false
        },

        selectors: {
            phone: '[data-name="is_phone"]',
            confirmPhoneButton:  '[data-name="phoneButton"]',
            info: '.inp-info',
            phoneModal: '[data-name="phone_modal"]',
            phoneText: '[data-name="phone_text"]',
            confirmCode: '[data-name="confirm_code"]',
            modal_phone: '[data-name="modal_phone"]',
        },
        countdown: $('#countdown'),
    
        reinited : function (a) {
            var _this = this;
            _this.options.reinited = a ? a : false;
        },
    
        sendCodeRequest: function() { //Запрос на отправку кода подтверждения
            var _this = this;
            $.ajax({
                url: '/ajax/confirm_phone.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    'action': 'send',
                    'phone': _this.phone,
                    'oneClick': _this.onClick.noAuto,
                },
                beforeSend: function () {
                    //_this.confirm_phone_button.addClass('send');
                },
                success: function (data) {

                    if (data.status === 'ok') {
                        _this.countdown.data('time', data.time + '000');  //нули обязательны, т.к. php возращет время в секундах, а js нужны милисекунды
                        if(data.type == 'success'){
                            _this.initCountdown();
                        }
                    }else if(data.status == 'exceptions'){
                        if(_this.options.reload =="Y") {
                            window.location.reload();
                        }else{
                            window.dsCheckout.getOrderJson();
                            window.purepopup.closePopup();
                        }
                    } else {
                        console.warn(data.message);
                    }
                }
            });
        },

        checkCode: function(oneClick = false,component = false) { // Проверка кода
            var _this = this;
            $.ajax({
                url: '/ajax/confirm_phone.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    'action': 'check',
                    'phone': _this.phone,
                    'code': $(_this.selectors.confirmCode).val(),
                    'oneClick':_this.onClick.noAuto,
                },
                beforeSend: function() {
                    // _this.confirm_input_wrap.addClass('send');
                    // _this.confirm_input.attr('disabled', 'disabled');
                },
                success: function(data) {
                    // _this.confirm_input_wrap.removeClass('send');
                    // _this.confirm_input.removeAttr('disabled');

                    if (data.status === 'ok') {
                        switch (data.type) {
                            case 'success': // Код подошел
                                _this.setConfirmWrapSuccessMode();
                                if(_this.options.reload =="Y") {
                                    window.location.reload();
                                }else{
                                    if(oneClick && component == 'basket'){
                                        window.dsBasket.requestBxAjax(
                                            _this.onClick,
                                            _this.onClick.templatePath,
                                            'onClick'
                                        );
                                      if ( $('.spinner') ) {
                                        $('.spinner').removeClass('hidden');
                                      }
                                    }else if(oneClick && component == 'detail'){
                                        window.dsCatalogDetail.ajaxRequestOneClick(_this.onClick);
                                        if ( $('.spinner') ) {
                                          $('.spinner').removeClass('hidden');
                                        }
                                    }else{
                                       window.dsCheckout.getOrderJson();
                                    }
                                    window.purepopup.closePopup();
                                }
                                break;
                            case 'another_phone': // Другой номер
                                _this.setConfirmWrapErrorCodeMode();
                                break;
                            case 'tries_over':
                                _this.setConfirmWrapErrorCodeMode();
                                break;
                            case 'wrong_code': // неверный код
                                _this.setConfirmWrapErrorCodeMode();
                                break;
                        }
                    } else {
                        console.error(data.message);
                    }
                }
            });
        },


        setConfirmWrapProgressMode: function(){ // неверный код
            var _this = this,
                phoneParent = $(_this.selectors.confirmCode).parent();
            phoneParent.removeClass('error');
            if (!phoneParent.hasClass('progress')){
                phoneParent .addClass('progress');
            }
        },

        setConfirmWrapSuccessMode: function(){ // неверный код
            var _this = this,
                phoneParent = $(_this.selectors.confirmCode).parent();
            phoneParent.removeClass('error, progress');
            phoneParent.addClass('success');
        },

        setConfirmWrapErrorCodeMode: function(){ // неверный код
            var _this = this,
                phoneParent = $(_this.selectors.confirmCode).parent();
            phoneParent.removeClass('progress');
            phoneParent.addClass('error');
        },

        initCountdown: function() { // Инициализация обратного отсчёта
            var _this = this;
            var startDate = new Date(parseInt(this.countdown.data('time')));
            var finalDate = startDate.setSeconds(startDate.getSeconds() + parseInt(this.options.wait_time));
            this.countdown.countdown(finalDate)
                .on('update.countdown', function(event) {
                    if(event.strftime('%M:%S') != '00:01'){
                        $(this).html(event.strftime('%M:%S'));
                    }else{
                       $('[data-block="timer"]').hide();
                       $('[data-block="code_again"]').show();
                    }
                });
        },

        confirmPhoneNumber:function(onClick = false){
            var _this = this;
            if(!onClick){
                _this.phone = $(_this.selectors.phone).val();
                _this.onClick = {'noAuto':'N'};
            }else{
                _this.phone = _this.onClick.phone;
                _this.onClick.noAuto = 'Y';
            }
            if(!!$(_this.selectors.phone).parsley()){
                $(_this.selectors.phone).parsley().removeError('phoneerror');
            }

            if(_this.phone != '' &&  $('#tel').inputmask("isComplete")){
                $(_this.selectors.info).show();

                if(!_this.checkCodeForExceptions()){
                    purepopup.ajaxToModal(false, purepopup.modalFilling, 396, $(_this.selectors.phoneModal));
                    $(_this.selectors.phoneText).html(_this.phone);
                    _this.initCountdown();
                }
                _this.sendCodeRequest();
            }else{
                $(_this.selectors.phone).parsley().addError('phoneerror', {message: 'Сначала введите телефон', updateClass: true});
                $(_this.selectors.info).hide();
            }
        },

        clickSmsGo: function(){ // Клик по кнопке авторизации
            var _this = this;

            $(_this.selectors.confirmPhoneButton).on('click', function () {
                _this.confirmPhoneNumber();
            });

            $(_this.selectors.phone).on('keyup', function (event) {
                if(event.keyCode == 13) {
                    event.preventDefault();
                    _this.confirmPhoneNumber();
                }
            });
        },

        conformCodeSms: function(oneClick = false, component = false){
            var _this = this;
            $(_this.selectors.confirmCode).on('keyup, input', function () {
                if(+$(this).val().length == +_this.options.code_length){
                    _this.checkCode(oneClick,component);
                    return false;
                } else if($(this).val().length <_this.options.code_length) {
                    _this.setConfirmWrapProgressMode();
                }
                this.value = this.value.slice(0,4);
            });
        },

        codeAgain:function(){
          var _this = this;
          $(document).on('click','[data-block="code_again"]',function () {
            _this.sendCodeRequest();
              $('[data-name="confirm_code"]').val('');
              $('[data-name="modal_phone"]').removeClass('error', 'progress');
              $('[data-block="timer"]').show();
              $('[data-block="code_again"]').hide();
              return false;
          });
        },

        jsCleanInputs: function() {
            var elem = $('.form-group__item input');

            elem.on('input', function() {
                if( $(this).val() > '' ) {
                    $(this).prev().addClass('focused');
                }
            });

            $('.form-group__item').on('click', '.js-input-clean', function(){
                $(this).next().val('').focus();
                $(this).removeClass('focused');
            });
        },

        checkCodeForExceptions: function(){ // Проверяем является ли код исключением
            var _this = this;
            for (var code in _this.options.no_confirm_code) {

                if(_this.phone.indexOf(this.options.no_confirm_code[code]) + 1 == 1) {
                    return true;
                }
            }
            return  false;

        },

        init : function () {
            var _this = this;
            
            _this.clickSmsGo();
            if (_this.options.reinited) {
                return true;
            }
            
            _this.conformCodeSms();
            _this.codeAgain();
            _this.jsCleanInputs();
            _this.reinited(true);
        },
    };
})();
