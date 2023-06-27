<?php

namespace GiveWFPGateway;

use Give\Donations\Models\Donation;
use Give\Donations\Models\DonationNote;
use Give\Donations\ValueObjects\DonationStatus;
use Give\Framework\Exceptions\Primitives\Exception;
use Give\Framework\PaymentGateways\CommandHandlers\RespondToBrowserHandler;
use Give\Framework\PaymentGateways\Commands\GatewayCommand;
use Give\Framework\PaymentGateways\Commands\PaymentComplete;
use Give\Framework\PaymentGateways\Commands\PaymentProcessing;
use Give\Framework\PaymentGateways\Commands\PaymentRefunded;
use Give\Framework\PaymentGateways\Commands\RedirectOffsite;
use Give\Framework\PaymentGateways\Commands\RespondToBrowser;
use Give\Framework\PaymentGateways\Commands\SubscriptionProcessing;
use Give\Framework\PaymentGateways\Exceptions\PaymentGatewayException;
use Give\Framework\PaymentGateways\PaymentGateway;
use Give\Framework\Http\Response\Types\RedirectResponse;

class WFP_Gateway extends PaymentGateway
{
	/*
* @inheritDoc
*/
	public static function id(): string
	{
		return 'wayworpay-gateway';
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string
	{
		return self::id();
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): string
	{
		return __('WayForPay', 'give-wayforpay');
	}

	/**
	 * @inheritDoc
	 */
	public function getPaymentMethodLabel(): string
	{
		return __('WayForPay', 'give-wayforpay');
	}

	/**
	 * @inheritDoc
	 */
	public function getLegacyFormFieldMarkup(int $formId, array $args): string
	{
		// Step 1: add any gateway fields to the form using html.  In order to retrieve this data later the name of the input must be inside the key gatewayData (name='gatewayData[input_name]').
		// Step 2: you can alternatively send this data to the $gatewayData param using the filter `givewp_create_payment_gateway_data_{gatewayId}`.

		return give_get_option('wfp_description');
	}

	/**
	 * @inheritDoc
	 */
	public function createPayment(Donation $donation, $gatewayData): GatewayCommand
	{
		try {
			// Step 1: Validate any data passed from the gateway fields in $gatewayData.  Throw the PaymentGatewayException if the data is invalid.
			if (empty($_POST['payment_hash'])) {
				throw new PaymentGatewayException(__('payment_hash is required.', 'give-wayforpay'));
			}
			global $wpdb;
			$wpdb->insert('wp_give_donationmeta', [
				'donation_id' => $donation->id,
				'meta_key' => 'trx_hash',
				'meta_value' => $_POST['payment_hash']
			]);

			return new RespondToBrowser([]);
		} catch (\Exception  $e) {
			// Step 4: If an error occurs, you can update the donation status to something appropriate like failed, and finally throw the PaymentGatewayException for the framework to catch the message.
			$errorMessage = $e->getMessage();
			$donation->status = DonationStatus::FAILED();
			$donation->save();

			DonationNote::create([
				'donationId' => $donation->id,
				'content' => sprintf(esc_html__('Donation failed. Reason: %s', 'give-wayforpay'), $errorMessage)
			]);

			throw new PaymentGatewayException($errorMessage);
		}

	}

	/**
	 * @inerhitDoc
	 */
	public function refundDonation(Donation $donation): PaymentRefunded
	{
		// Step 1: refund the donation with your gateway.
		// Step 2: return a command to complete the refund.
		return new PaymentRefunded();
	}


	/**
	 * An example of using a secureRouteMethod for extending the Gateway API to handle a redirect.
	 *
	 * @throws Exception
	 */
	protected function handleCreatePaymentRedirect(array $queryParams): RedirectResponse
	{
		file_put_contents('test-params.json', json_encode($queryParams));
		file_put_contents('test-post.json', json_encode($_POST));

		// Step 1: Use the $queryParams to get the data you need to complete the donation.
		$donationId = $queryParams['givewp-donation-id'];
		$gatewayTransactionId = $queryParams['givewp-gateway-transaction-id'];
		$successUrl = $queryParams['givewp-success-url'];

		// Step 2: Typically you will find the donation from the donation ID.
		/** @var Donation $donation */
		$donation = Donation::find($donationId);

		// Step 3: Use the Donation model to update the donation based on the transaction and response from the gateway.
		$donation->status = DonationStatus::COMPLETE();
		$donation->gatewayTransactionId = $gatewayTransactionId;
		$donation->save();

		DonationNote::create([
			'donationId' => $donation->id,
			'content' => 'Donation Completed from Example-Test Gateway Offsite.'
		]);

		// Step 4: Return a RedirectResponse to the GiveWP success page.
		return new RedirectResponse($successUrl);
	}


	/**
	 * Example request to gateway
	 */
	private function exampleRequest(array $data): array
	{
		return array_merge([
			'success' => true,
			'transaction_id' => '1234567890',
			'subscription_id' => '0987654321',
		], $data);
	}
}
