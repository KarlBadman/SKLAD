<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	use Bitrix\Main\Localization\Loc;
	Loc::loadMessages(__FILE__);
?>

<?if ($this->isPaid) : ?>

<?else : ?>
	<?if ($this->getConfig('PS_MODE') == 'apple_pay') : ?>
		<style>
			.link_success_order_custom{display: none !important;}
			.number_success_order_custom{margin-right: 10px;}
		</style>
		<a data-applepay-button class="apple-pay-btn" style="-webkit-appearance: -apple-pay-button; -apple-pay-button-type: buy; -apple-pay-button-style: black; display:none; width: 250px; height: 50px; margin-top:10px;" lang=ru href=""></a>
		<script type="text/javascript">
			// apple pay js handler
			(function () {

				window.applePayJsHandler = {

					selectors : {

						btn : "[data-applepay-button]",

					},

					settings : {

						getMerchantResponsePath : "", // ON SELF
						getAuthorisedResponsePath : "", // ON SELF
						merchantIdentifier : <?=CUtil::PhpToJSObject($this->getConfig('PRODUCTION_MERCHANTIDENTIFIER'))?>,
						request : {

							countryCode : "RU",
							currencyCode : "RUB",
							supportedNetworks : ["visa", "masterCard"],
							merchantCapabilities : ["supports3DS"],
							total : {

								label: "<?='Payment order #'.$this->getOrderID().' by payment ID #'.$this->getOrderPaymentID()?>",
								amount: "<?=$this->getAmount();?>"

							}

						}

					},

					checkSession : function () {
						var _this = this;
						if (window.ApplePaySession) {
							var promise = window.ApplePaySession.canMakePaymentsWithActiveCard(_this.settings.merchantIdentifier);
							promise.then(function (canMakePayments) {
								if (canMakePayments) {
									$(_this.selectors.btn).show();
									// $(document).trigger('order.onSuccessSaved');
								} else {
									$(_this.selectors.btn).hide();
								}
						    });
						} else {
							$(_this.selectors.btn).hide();
						}
					},

					loadPaymentHandlerByEvent : function () {
						var _this = this;

						if ($(_this.selectors.btn).length > 0) {
							$(document).on('click', _this.selectors.btn, function (e) {
								e.preventDefault();
								_this.createPaymentSession();
							});
						}

						$(document).on('order.onSuccessSaved', function () {
							_this.createPaymentSession();
						});
					},

					createPaymentSession : function () {
						var _this = this;
						var session = new ApplePaySession(1, _this.settings.request);

						session.onvalidatemerchant = function (e) {
							$.post(_this.getMerchantResponsePath, {

								ACTION: "APPLEPAYVALIDATEMERCHANT",
								validationURL: e.validationURL

							}).then(function (r) {
					            session.completeMerchantValidation(JSON.parse(r));
					        });
						};

						session.onpaymentauthorized = function (e) {
					        $.post(_this.settings.getAuthorisedResponsePath, {

								ACTION: "APPLEPAYAUTHORISEPAYMENT",
					        	cryptogram: JSON.stringify(e.payment.token)

					        }).then(function (r) {
								r = JSON.parse(r);
								session.completePayment(r.Success === true ? window.ApplePaySession.STATUS_SUCCESS : window.ApplePaySession.STATUS_FAILURE);
								window.location.reload();
					        });
						}

						session.begin();
					},

					init : function () {
						this.checkSession();
						this.loadPaymentHandlerByEvent();
					}

				}.init();

			})();
		</script>
	<?endif;?>

<?endif;?>
