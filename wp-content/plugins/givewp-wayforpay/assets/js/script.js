jQuery(document).ready(function ($) {
	$(document).on('submit', '.give-form', function (event) {
		let gateway = $('[name="payment-mode"]').val()
		console.log(gateway)
		const hash = $('[name="payment_hash"]').val();
		const reqquring = $('[name="wfp_reqquring_donation_on"]:checked').val();
		if ('manual' === gateway) {
			// console.log(event);
			setTimeout(function () {
				window.top.location.href = '/wfp-pay/?type=wfp&hash=' + hash +'&reqquring='+reqquring;
			}, 100)
		}

	});

	setTimeout(function () {
		$('#give-gateway-wayworpay-gateway-17-1').change();
	}, 1000)
});
