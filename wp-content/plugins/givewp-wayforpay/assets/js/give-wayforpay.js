jQuery(document).ready(function ($) {
	$(document).on('submit', '.give-form', function (event) {
		// event.preventDefault();

		let gateway = $('[name="payment-mode"]').val()
		console.log(gateway)
		const hash = $('[name="payment_hash"]').val();
		const reqquring = $('[name="wfp_reqquring_donation_on"]:checked').val();
		if ('wayworpay-gateway' == gateway) {
			console.log(event);
			const redirect='/wfp-pay/?type=wfp&hash=' + hash +'&reqquring='+reqquring;
			setTimeout(function () {
				window.top.location.href = redirect;
			}, 400)
		}

	});

	setTimeout(function () {
		$('#give-gateway-wayworpay-gateway-17-1').change();
	}, 1000)
});
