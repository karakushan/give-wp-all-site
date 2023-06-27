jQuery(document).ready(function ($) {
	$(document).on('submit', '.give-form', function (event) {
		let gateway = $('[name="payment-mode"]').val()
		const hash = $('[name="payment_hash"]').val();
		if ('wayworpay-gateway' === gateway) {
			console.log(event);
			window.top.location.href = '/wfp-pay?type=wfp&hash=' + hash;
			console.log(gateway);
		}

	});

	setTimeout(function () {
		$('#give-gateway-wayworpay-gateway-9-1').change();
	}, 1000)
});
