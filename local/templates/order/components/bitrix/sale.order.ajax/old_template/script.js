BX.saleOrderAjax = { // bad solution, actually, a singleton at the page

	BXCallAllowed: false,

	options: {},
	indexCache: {},
	controls: {},

	modes: {},
	properties: {},

	mapactiveid: '',
	isLocationProEnabled: '',

	// called once, on component load
	init: function (options) {
		var ctx = this;
		this.options = options;

		BX(function () {
			ctx.initDeferredControl();
		});
		BX(function () {
			ctx.BXCallAllowed = true; // unlock form refresher
		});

		this.controls.scope = BX('order_form_div');

		// user presses "add location" when he cannot find location in popup mode
		BX.bindDelegate(this.controls.scope, 'click', {className: '-bx-popup-set-mode-add-loc'}, function () {

			var input = BX.create('input', {
				attrs: {
					type: 'hidden',
					name: 'PERMANENT_MODE_STEPS',
					value: '1'
				}
			});

			BX.prepend(input, BX('ORDER_FORM'));
			ctx.BXCallAllowed = false;
		});
	},

	cleanUp: function () {

		for (var k in this.properties) {
			if (this.properties.hasOwnProperty(k)) {
				if (typeof this.properties[k].input != 'undefined') {
					BX.unbindAll(this.properties[k].input);
					this.properties[k].input = null;
				}

				if (typeof this.properties[k].control != 'undefined')
					BX.unbindAll(this.properties[k].control);
			}
		}

		this.properties = {};
	},

	addPropertyDesc: function (desc) {
		this.properties[desc.id] = desc.attributes;
		this.properties[desc.id].id = desc.id;
	},

	// called each time form refreshes
	initDeferredControl: function () {
		var ctx = this,
			k,
			row,
			input,
			locPropId,
			m,
			control,
			code,
			townInputFlag,
			adapter;

		// first, init all controls
		if (typeof window.BX.locationsDeferred != 'undefined') {

			this.BXCallAllowed = false;

			for (k in window.BX.locationsDeferred) {

				window.BX.locationsDeferred[k].call(this);
				window.BX.locationsDeferred[k] = null;
				delete (window.BX.locationsDeferred[k]);

				this.properties[k].control = window.BX.locationSelectors[k];
				delete (window.BX.locationSelectors[k]);
			}
		}

		for (k in this.properties) {

			// zip input handling
			if (this.properties[k].isZip) {
				row = this.controls.scope.querySelector('[data-property-id-row="' + k + '"]');
				if (BX.type.isElementNode(row)) {

					input = row.querySelector('input[type="text"]');
					if (BX.type.isElementNode(input)) {
						this.properties[k].input = input;

						// set value for the first "location" property met
						locPropId = false;
						for (m in this.properties) {
							if (this.properties[m].type == 'LOCATION') {
								locPropId = m;
								break;
							}
						}

						if (locPropId !== false) {
							BX.bindDebouncedChange(input, function (value) {

								input = null;
								row = null;

								if (BX.type.isNotEmptyString(value) && /^\s*\d+\s*$/.test(value) && value.length > 3) {

									ctx.getLocationByZip(value, function (locationId) {
										ctx.properties[locPropId].control.setValueByLocationId(locationId);
									}, function () {
										try {
											ctx.properties[locPropId].control.clearSelected(locationId);
										} catch (e) {
										}
									});
								}
							});
						}
					}
				}
			}

			// location handling, town property, etc...
			if (this.properties[k].type == 'LOCATION') {

				if (typeof this.properties[k].control != 'undefined') {

					control = this.properties[k].control; // reference to sale.location.selector.*
					code = control.getSysCode();

					// we have town property (alternative location)
					if (typeof this.properties[k].altLocationPropId != 'undefined') {
						if (code == 'sls') // for sale.location.selector.search
						{
							// replace default boring "nothing found" label for popup with "-bx-popup-set-mode-add-loc" inside
							control.replaceTemplate('nothing-found', this.options.messages.notFoundPrompt);
						}

						if (code == 'slst')  // for sale.location.selector.steps
						{
							(function (k, control) {

								// control can have "select other location" option
								control.setOption('pseudoValues', ['other']);

								// insert "other location" option to popup
								control.bindEvent('control-before-display-page', function (adapter) {

									control = null;

									var parentValue = adapter.getParentValue();

									// you can choose "other" location only if parentNode is not root and is selectable
									if (parentValue == this.getOption('rootNodeValue') || !this.checkCanSelectItem(parentValue))
										return;

									var controlInApater = adapter.getControl();

									if (typeof controlInApater.vars.cache.nodes['other'] == 'undefined') {
										controlInApater.fillCache([{
											CODE: 'other',
											DISPLAY: ctx.options.messages.otherLocation,
											IS_PARENT: false,
											VALUE: 'other'
										}], {
											modifyOrigin: true,
											modifyOriginPosition: 'prepend'
										});
									}
								});

								townInputFlag = BX('LOCATION_ALT_PROP_DISPLAY_MANUAL[' + parseInt(k) + ']');

								control.bindEvent('after-select-real-value', function () {

									// some location chosen
									if (BX.type.isDomNode(townInputFlag))
										townInputFlag.value = '0';
								});
								control.bindEvent('after-select-pseudo-value', function () {

									// option "other location" chosen
									if (BX.type.isDomNode(townInputFlag))
										townInputFlag.value = '1';
								});

								// when user click at default location or call .setValueByLocation*()
								control.bindEvent('before-set-value', function () {
									if (BX.type.isDomNode(townInputFlag))
										townInputFlag.value = '0';
								});

								// restore "other location" label on the last control
								if (BX.type.isDomNode(townInputFlag) && townInputFlag.value == '1') {

									// a little hack: set "other location" text display
									adapter = control.getAdapterAtPosition(control.getStackSize() - 1);

									if (typeof adapter != 'undefined' && adapter !== null)
										adapter.setValuePair('other', ctx.options.messages.otherLocation);
								}

							})(k, control);
						}
					}
				}
			}
		}

		this.BXCallAllowed = true;
	},

	checkMode: function (propId, mode) {

		if (mode == 'altLocationChoosen') {

			if (this.checkAbility(propId, 'canHaveAltLocation')) {

				var input = this.getInputByPropId(this.properties[propId].altLocationPropId);
				var altPropId = this.properties[propId].altLocationPropId;

				if (input !== false && input.value.length > 0 && !input.disabled && this.properties[altPropId].valueSource != 'default') {

					//this.modes[propId][mode] = true;
					return true;
				}
			}
		}

		return false;
	},

	checkAbility: function (propId, ability) {

		if (typeof this.properties[propId] == 'undefined')
			this.properties[propId] = {};

		if (typeof this.properties[propId].abilities == 'undefined')
			this.properties[propId].abilities = {};

		if (typeof this.properties[propId].abilities != 'undefined' && this.properties[propId].abilities[ability])
			return true;

		if (ability == 'canHaveAltLocation') {

			if (this.properties[propId].type == 'LOCATION') {

				// try to find corresponding alternate location prop
				if (typeof this.properties[propId].altLocationPropId != 'undefined' && typeof this.properties[this.properties[propId].altLocationPropId]) {

					var altLocPropId = this.properties[propId].altLocationPropId;

					if (typeof this.properties[propId].control != 'undefined' && this.properties[propId].control.getSysCode() == 'slst') {

						if (this.getInputByPropId(altLocPropId) !== false) {
							this.properties[propId].abilities[ability] = true;
							return true;
						}
					}
				}
			}

		}

		return false;
	},

	getInputByPropId: function (propId) {
		if (typeof this.properties[propId].input != 'undefined')
			return this.properties[propId].input;

		var row = this.getRowByPropId(propId);
		if (BX.type.isElementNode(row)) {
			var input = row.querySelector('input[type="text"]');
			if (BX.type.isElementNode(input)) {
				this.properties[propId].input = input;
				return input;
			}
		}

		return false;
	},

	getRowByPropId: function (propId) {

		if (typeof this.properties[propId].row != 'undefined')
			return this.properties[propId].row;

		var row = this.controls.scope.querySelector('[data-property-id-row="' + propId + '"]');
		if (BX.type.isElementNode(row)) {
			this.properties[propId].row = row;
			return row;
		}

		return false;
	},

	getAltLocPropByRealLocProp: function (propId) {
		if (typeof this.properties[propId].altLocationPropId != 'undefined')
			return this.properties[this.properties[propId].altLocationPropId];

		return false;
	},

	toggleProperty: function (propId, way, dontModifyRow) {

		var prop = this.properties[propId];

		if (typeof prop.row == 'undefined')
			prop.row = this.getRowByPropId(propId);

		if (typeof prop.input == 'undefined')
			prop.input = this.getInputByPropId(propId);

		if (!way) {
			if (!dontModifyRow)
				BX.hide(prop.row);
			prop.input.disabled = true;
		} else {
			if (!dontModifyRow)
				BX.show(prop.row);
			prop.input.disabled = false;
		}
	},

	submitFormProxy: function (item, control) {
		var propId = false;
		for (var k in this.properties) {
			if (typeof this.properties[k].control != 'undefined' && this.properties[k].control == control) {
				propId = k;
				break;
			}
		}

		// turning LOCATION_ALT_PROP_DISPLAY_MANUAL on\off

		if (item != 'other') {

			if (this.BXCallAllowed) {

				this.BXCallAllowed = false;
				submitForm();
			}

		}
	},

	getPreviousAdapterSelectedNode: function (control, adapter) {

		var index = adapter.getIndex();
		var prevAdapter = control.getAdapterAtPosition(index - 1);

		if (typeof prevAdapter !== 'undefined' && prevAdapter != null) {
			var prevValue = prevAdapter.getControl().getValue();

			if (typeof prevValue != 'undefined') {
				var node = control.getNodeByValue(prevValue);

				if (typeof node != 'undefined')
					return node;

				return false;
			}
		}

		return false;
	},
	getLocationByZip: function (value, successCallback, notFoundCallback) {
		if (typeof this.indexCache[value] != 'undefined') {
			successCallback.apply(this, [this.indexCache[value]]);
			return;
		}

		ShowWaitWindow();

		var ctx = this;

		BX.ajax({

			url: this.options.source,
			method: 'post',
			dataType: 'json',
			async: true,
			processData: true,
			emulateOnload: true,
			start: true,
			data: {'ACT': 'GET_LOC_BY_ZIP', 'ZIP': value},
			//cache: true,
			onsuccess: function (result) {

				CloseWaitWindow();
				if (result.result) {

					ctx.indexCache[value] = result.data.ID;

					successCallback.apply(ctx, [result.data.ID]);

				} else
					notFoundCallback.call(ctx);

			},
			onfailure: function (type, e) {

				CloseWaitWindow();
				// on error do nothing
			}

		});
	},

	mapActive: function (mapactive) {

		$('.tab').removeClass('active');

		if (mapactive) {
			$('.pickup.tab').addClass("active");
		} else {
			$('.delivery.tab').addClass("active");
		}
	},

	reloadMap: function (plas) {

		dskladMapYndex.objectInstance.removeAll();

		if (plas !== false) {
			dskladMapYndex.objectInstance.add(JSON.stringify(plas));
			dskladMapYndex.alignCardSize();
		}
	},

	reloadAjax: function (res, classname = 'ajaxreload', map = false) {
		if(!map && dskladMapYndex.objectInstance.__proto__.__proto__ != null) {
			BX.saleOrderAjax.reloadMap(window.plasmark);
		}

		BX.saleOrderAjax.activeDeliveryNoPickup(window.terminalOk);

		var parser = new DOMParser();

		var doc = parser.parseFromString(res, "text/html");

		var docElement = doc.getElementsByClassName(classname);

		var documentElement = document.getElementsByClassName(classname);

		for (var i = 0, max = documentElement.length; i < max; i++) {

			if(!!documentElement[i].innerHTML && !!docElement[i].innerHTML)
			{
				documentElement[i].innerHTML = docElement[i].innerHTML;
			}
		}

		return false;
	},

	submitForm: function (val, isLocationProEnabled, mapDevilery) {

		var self = this;

		if (BXFormPosting === true)
			return true;

		BXFormPosting = true;

		if (val != 'Y') {
			BX('confirmorder').value = 'N';
		}

		if($('.confirm-phone-button').hasClass('js-need-confirm')){
		    $('#phoneOk').val('');
        }else{
            $('#phoneOk').val('OK');
        }

		var orderForm = BX('ORDER_FORM');

		$('.wrap_container_spinner').show();

		if (isLocationProEnabled) BX.saleOrderAjax.cleanUp();

		BX.ajax.submit(orderForm, self.ajaxResult);

		BX.saleOrderAjax.mapactiveid = mapDevilery;

		BX.saleOrderAjax.isLocationProEnabled = mapDevilery;

		return true;
	},

	activeDeliveryNoPickup: function (terminalOk){

	    //.log(terminalOk);

		if(terminalOk == 'N'){
			$('.tab.pickup').removeClass('active');
			$('.tab.delivery').addClass('active');
		}
	},

	submitFormMap: function () {

		var self = this;

		BX('confirmorder').value = 'N';

		var orderForm = BX('ORDER_FORM');

		BX.ajax.submit(orderForm, self.ajaxResultMap);
	},

	ajaxResultMap: function (res) {

		var self = this;

		BX.saleOrderAjax.reloadAjax(res, 'mapreload', true);

	},

	ajaxResult: function (res) {

		var orderForm = BX('ORDER_FORM');

		try {
			// if json came, it obviously a successfull order submit

			var json = JSON.parse(res);
			if (json.error) {
				BXFormPosting = false;
				return;
			} else if (json.redirect) {
				window.top.location.href = json.redirect;
			}
		} catch (e) {
			// json parse failed, so it is a simple chunk of html

			BX.saleOrderAjax.reloadAjax(res, 'ajaxreload', false); // перегружаем типа аяксом и режем на ноды (класс для нод ajaxreload)

			BXFormPosting = false;

			newChusenMobile();

			BX.saleOrderAjax.autocompleteDpdCity('#sale_order_props #autocomplete');

			if (BX.saleOrderAjax.isLocationProEnabled) BX.saleOrderAjax.initDeferredControl();

			BX.saleOrderAjax.initMask();

			SaleConfirmPhoneController.reinit();

			BX.saleOrderAjax.delivery2doorAddressSuggestion();

            if($('label[data-name="pickup"]').hasClass('active')){
                $('.tab.pickup').addClass('active');
                $('.tab.delivery').removeClass('active');
            }

		}

		BX.closeWait();
		BX.onCustomEvent(orderForm, 'onAjaxSuccess');

		$('.wrap_container_spinner').hide();
	},

	quantityChange: function (quantity, productId, del, sessionId) {

		var self = this;

		if (productId != 0 && productId != '' && sessionId != '') {
			var data = {
				'quantity': quantity,
				'productId': productId,
				'sessionId': sessionId,
				'del': del
			};

			$.ajax({
				type: "POST",
				url: "/local/components/dsklad/crutch_for_order/ajax/quantityChange.php",
				data: data,
				success: function (msg) {
					if (msg.trim() == 'Y') {
						submitForm();
						return false;
					} else {

					}
				}
			});
		}
	},

	basketServiceСhange: function (productId, sessionId) {

		if (productId != 0 && productId != '' && sessionId != '') {
			var data = {
				'productId': productId,
				'sessionId': sessionId,
			};

			$.ajax({
				type: "POST",
				url: "/local/components/dsklad/crutch_for_order/ajax/basketServiceСhange.php",
				data: data,
				success: function (msg) {
					if (msg.trim() == 'Y') {
						submitForm();
					} else {

					}
				}
			});
		}
	},
	autocompleteDpdCity: function (selector) {
		$(selector).autocomplete({
			serviceUrl: window.suggest,
            minChars:3,
			onSelect: function (suggestion) {
				$('input[name=city_id]').val(suggestion.data);
				BX.saleOrderAjax.clickCityAutoComplete(suggestion.data);
                $('#city_link').html($(this).val().split('(')[0]);
                $('input[data-name="is_address"]').val('');
			}
		});

		window.cityValOld = get_cookie('DPD_CITY');
	},

	clickCityAutoComplete: function (city_id) {

		if (cityValOld != city_id) {

			var obj = {};
			obj.intLocationID = city_id;
			$.ajax({
				url: '/local/components/dsklad/crutch_for_order/ajax/recity.php',
				dataType: "text",
				data: obj,
				async: false,
				type: "post",
				success: function (ans) {
					submitForm();
				}
			});
		}
	},
	setCoupon: function (coupon, sessionId) {
		if (coupon && sessionId != '') {
			var data = {
				'coupon': coupon,
				'sessionId': sessionId,
			};

			$.ajax({
				type: "POST",
				url: "/local/components/dsklad/crutch_for_order/ajax/setCoupon.php",
				data: data,
				success: function (msg) {
					submitForm();
				}
			});
		}
	},

	ubdateUserSpan: function (span, sessionId) {
		if (sessionId != '') {
			var data = {
				'span': span,
				'sessionId': sessionId,
			};

			$.ajax({
				type: "POST",
				url: "/local/components/dsklad/crutch_for_order/ajax/ubdateUserSpan.php",
				data: data,
				success: function (msg) {
					console.log(msg);
				}
			});
		}
	},

	changeTerminal: function (terminalId, sessionId, minPack) {

		var self = this;

		if (terminalId != '' && sessionId != '') {
			var data = {
				'terminalId': terminalId,
				'sessionId': sessionId,
                'minPack':minPack,
			};

			$.ajax({
				type: "POST",
				url: "/local/components/dsklad/crutch_for_order/ajax/changeTerminal.php",
				data: data,
				success: function (msg) {
					$('select[data-name="not_terminal"]').val($('select[data-name="not_terminal"] option[data-city="' + msg.trim() + '"]').val());
					self.submitFormMap();
				}
			});
		}
	},

	setCursorPosition: function (pos, elem) { // отслеживание курсора
		elem.focus();
		if (elem.setSelectionRange) elem.setSelectionRange(pos, pos);
		else if (elem.createTextRange) {
			var range = elem.createTextRange();
			range.collapse(true);
			range.moveEnd("character", pos);
			range.moveStart("character", pos);
			range.select()
		}
	},

	maskjs: function (event) { // маска телефона
		if (this.selectionStart < 3) event.preventDefault();
		var matrix = "+7 (___) ___ ____",
			i = 0,
			def = matrix.replace(/\D/g, ""),
			val = this.value.replace(/\D/g, "");

		if (def.length >= val.length) val = def;
		this.value = matrix.replace(/[_\d]/g, function (a) {
			return i < val.length ? val.charAt(i++) : a
		});
		i = this.value.indexOf("_");
		if (event.keyCode == 8) i = this.value.lastIndexOf(val.substr(-1)) + 1;

		if (i != -1) {
			i < 5 && (i = 3);
			this.value = this.value.slice(0, i);
		} else {
			if(this.parentElement.classList.contains('error')){
				this.parentElement.classList.remove('error');
			}
		}
		if (event.type == "blur") {
			if (this.value.length < 5) this.value = ""
		} else BX.saleOrderAjax.setCursorPosition(this.value.length, this);

	},

	emailMask: function(event) {
		var address = event.srcElement.value;
		var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		if(reg.test(address) == false) {
			event.srcElement.parentElement.classList.add('error');
			return false;
		}else{
			event.srcElement.parentElement.classList.remove('error');
			return false;
		}
	},

	initMask: function () { // инициализация маски
		var event = new Event('load');

		var input = document.querySelector("input[data-name='is_phone']");
		['load', 'input', 'focus', 'blur', 'keydown'].forEach(function(e) {
			input.addEventListener(e, BX.saleOrderAjax.maskjs, false);
		});
		input.dispatchEvent(event);

		var inputEmail = document.querySelector("input[data-name='is_email']");
		[,'blur', 'keyup'].forEach(function(e) {
			inputEmail.addEventListener(e, BX.saleOrderAjax.emailMask, false);
		});
	},

	delivery2doorAddressSuggestion : function () {
		$("input[data-name='is_address']").suggestions({
	        token: "48d88f32802f15df9b9ad44dcf41c9fed9502b64",
	        type: "ADDRESS",
	        bounds: "city-settlement-street-house",
	        count: 10,
	        constraints : {
	            locations : $('#city_id').val() == '78000000000' ?
	                [{"kladr_id": "47"}, {"kladr_id": "78"}] :
	                    $('#city_id').val() == '77000000000' ?
	                [{"kladr_id": "50"}, {"kladr_id": "77"}] : {kladr_id : $('#city_id').val() + '00'},
	        },
	        onSelect: function(suggestion) {
	            var address = suggestion.data, city_id = '';
	            if ($('[name=\"city_name\"]').val() !== suggestion.data.city && suggestion.data.city_kladr_id !== null) {
	                city_id = suggestion.data.city_kladr_id;
					$('input[name=city_id]').val(city_id.substring(0, city_id.length-2));
					BX.saleOrderAjax.clickCityAutoComplete(city_id.substring(0, city_id.length-2));
                    $('#city_link').html($(this).val().split('(')[0]);
	            }
	        },
	        restrict_value : true
	    });
	},

    initSaleOrderAjax: function () {
        $(document).on('click','input[name="DELIVERY_ID"]',function () { // Показать карту при выборе доставке
            mapactive = false;

            for (var i in BX.saleOrderAjax.mapactiveid) {
                if(BX.saleOrderAjax.mapactiveid[i] == $(this).val()){mapactive = true};
            }

            BX.saleOrderAjax.mapActive(mapactive);
        });

        $(document).on('mymap.eventreadyinstance', function () { // клик по метки карты

            dskladMapYndex.setMapInstanceEventsListner("overlayClickEventHandler", function (e) {
                var plasId = e.get('objectId');

                $('#input-delivery-point option').removeAttr('selected');

                $('#input-delivery-point option[value="'+plasId+'"]').prop('selected', true);

                $('#input-delivery-point').trigger("chosen:updated");

                BX.saleOrderAjax.changeTerminal(plasId,$('#sessid').val(),$('input[name="MIN_PACK"]').val());

            });

        });

        $(document).on('change','#input-delivery-point',function () { // выбор терминала через селект
            dskladMapYndex.ballonOpenId($(this).val());
            BX.saleOrderAjax.changeTerminal($(this).val(),$('#sessid').val(),$('input[name="MIN_PACK"]').val());
            dskladMapYndex.ballonOpenId($(this).val());

        });

        $(document).on('click','.promo-text',function(){ // показать промокод
            $(this).hide();
            $('.promo-form').show();
        });

        $(document).on('focus','input[data-name="product"]',function () { // убрать количество товара
            $(this).val('');
        });

        $(document).on('blur','input[data-name="product"]',function () { // добавить шт. к количеству товара
            $(this).val($(this).attr('data-gaproduct-quantity')+$('.counter__widget[data-product_id="'+$(this).attr('product_id')+'"]').attr('data-measure'));
        });

        $(document).on('change','input[data-name="product"]',function () { // изменить количества товара
            $(this).attr('data-gaproduct-quantity',$(this).val());
            BX.saleOrderAjax.quantityChange($(this).val(),$(this).attr('product_id'),'N',$('#sessid').val());
        });

        $(document).on('click','a.remove_basket_items',function () { // удалить товар из корзины
            BX.saleOrderAjax.quantityChange(0,$(this).attr('href'),'Y',$('#sessid').val());
            return false;
        });

        $(document).on('change','input[data-name="service"]',function () { // добавить доп гарантию
            BX.saleOrderAjax.basketServiceСhange($(this).val(),$('#sessid').val());
            return false;
        });

        $(document).on('click','.count_a',function () { // изменить количество товаров при клике + и -
            input = $('input[product_id="'+$(this).attr('product_id')+'"]');
            quantity = +input.attr('data-gaproduct-quantity') + +$(this).attr('data-add');
            input.attr('data-gaproduct-quantity',quantity);
            input.val(quantity+$('.counter__widget[data-product_id="'+$(this).attr('product_id')+'"]').attr('data-measure'));
            BX.saleOrderAjax.quantityChange(input.val(),$(this).attr('product_id'),'N',$('#sessid').val());
            return false;
        });

        $(document).on('click','#promo',function () { // добавить промокод
            coupon = $('input[name="promo"]');
            if(coupon != '') {
                BX.saleOrderAjax.setCoupon($(coupon).val(), $('#sessid').val());
            }
        });

        $('.js-city-name-element#autocomplete').autocomplete({ // добавить список городов
            serviceUrl: window.suggest,
            onSelect: function (suggestion) {
                $('input[name=city_id]').val(suggestion.data);
                BX.saleOrderAjax.clickCityAutoComplete(suggestion.data);
                $('#city_link').html($(this).val().split('(')[0]);
            }
        });

        $(document).on('change','input[name="subscrible"]',function () { // подписаться на рассылку
            if ($(this).is(':checked')) {
                BX.saleOrderAjax.ubdateUserSpan('Y', $('#sessid').val());
            }else{
                BX.saleOrderAjax.ubdateUserSpan('N', $('#sessid').val());
            }
        });

        window.cityValOld = get_cookie('DPD_CITY'); // добавить id города в cookie

        BX.saleOrderAjax.delivery2doorAddressSuggestion();
    }
}

$(document).ready(function () {
    BX.saleOrderAjax.initSaleOrderAjax();
});
