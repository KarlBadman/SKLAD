<?require ($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');?>
<a id="apple-pay" style="-webkit-appearance: -apple-pay-button; -apple-pay-button-type: plain; -apple-pay-button-style: black;" lang=ru href=""></a>
<textarea id="respond" rows="12" cols="12"></textarea>
<script type="text/javascript">

	if (window.ApplePaySession) {
	    var merchantIdentifier = 'merchant.ru.dsklad.www';
	    var promise = ApplePaySession.canMakePaymentsWithActiveCard(merchantIdentifier);
	    promise.then(function (canMakePayments) {
	        if (canMakePayments) {
	            $('#apple-pay').show();
	        }
	    });
	}

	$('#apple-pay').click(function (e) {

		e.preventDefault();
	    var request = {
	        countryCode: 'RU',
	        currencyCode: 'RUB',
	        supportedNetworks: ['visa', 'masterCard'],
	        merchantCapabilities: ['supports3DS'],
	        total: { label: 'Test payment', amount: '1.00' },
	    }

	    var session = new ApplePaySession(1, request);
	    session.onvalidatemerchant = function (event) {
	        $.get("/apple_test/start.php", {validationURL: event.validationURL}).then(function (r) {
	        	$('#respond').val(r);
	            session.completeMerchantValidation(JSON.parse(r));
	        });
	    };

	    session.onpaymentauthorized = function (event) {

	        var data = {
	            cryptogram: JSON.stringify(event.payment.token)
	        };

    		$('#respond').val(JSON.stringify(event.payment.token));

	        // $.post("/ApplePay/Pay", data).then(function (result) {
	        //     var status;
	        //     if (result.Success) {
	        //         status = ApplePaySession.STATUS_SUCCESS;
	        //     } else {
	        //         status = ApplePaySession.STATUS_FAILURE;
	        //     }

	        //     session.completePayment(status);
	        // });
	    };

	    session.begin();
	});

</script>
<?require ($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');?>
