<?php

namespace GiveWFPGateway;

use Give\Donations\Models\Donation;
use WayForPay\SDK\Collection\ProductCollection;
use WayForPay\SDK\Credential\AccountSecretCredential;
use WayForPay\SDK\Domain\Client;
use WayForPay\SDK\Domain\Product;
use WayForPay\SDK\Wizard\PurchaseWizard;
use WayForPay\SDK\Exception\WayForPaySDKException;
use WayForPay\SDK\Handler\ServiceUrlHandler;
use Give_Payment as Payment;

class WFP_Init
{
	public function __construct()
	{
		add_action('givewp_register_payment_gateway', static function ($paymentGatewayRegister) {
			$paymentGatewayRegister->registerGateway(WFP_Gateway::class);
		});

		add_action('wp_print_scripts', [$this, 'wfp_for_give_register_plugin_includes_admin']);
		add_action('init', [$this, 'custom_endpoint_init']);
		add_action('template_redirect', [$this, 'custom_endpoint_content']);
		add_action('rest_api_init', [$this, 'register_api_route']);

		// Settings in admin
		add_filter('give_get_sections_gateways', [$this, 'admin_payment_gateway_sections']);
		add_filter('give_get_settings_gateways', [$this, 'admin_payment_gateway_setting_fields']);

		add_action('give_payment_mode_after_gateways', [$this, 'wfp_for_give_add_payment_mode_after_gateways']);

		add_action( 'give_donation_form_top',[$this,'give_donation_form_top'], 10, 1 );
	}

	function give_donation_form_top(){
		 ?>
		<input type='hidden' name='payment_hash' value='<?php echo esc_attr(uniqid()) ?>'>
		<?php
	}

	function wfp_for_give_add_payment_mode_after_gateways(){

		?>
		<style>
			#give-gateway-option-wayworpay-gateway:after {
				content: "";
				background-image: url(<?php echo  esc_url( give_get_option('wfp_logo'))?>);
				width: 40px;
				height: 40px;
				background-size: cover;
				top: -6px;
			}
		</style>
		<?php
	}

	function register_api_route()
	{
		register_rest_route('wfp', '/callback', array(
			'methods' => 'POST',
			'callback' => [$this, 'wfp_callback_handler']
		));
	}

	/**
	 * Register Section for Payment Gateway Settings.
	 *
	 * @param array $sections List of payment gateway sections.
	 *
	 * @return array
	 * @since 1.0.0
	 *
	 */
	function admin_payment_gateway_sections($sections)
	{
		$sections['wayforpay'] = __('Wayforpay', 'give-wayforpay');

		return $sections;
	}

	function admin_payment_gateway_setting_fields($settings)
	{
		switch (give_get_current_setting_section()) {

			case 'wayforpay':
				$settings = array(
					array(
						'id' => 'wfp_give_title',
						'type' => 'title',
					),
				);
				$settings[] = array(
					'name' => __('Logo', 'give-wayforpay'),
					'desc' => '',
					'id' => 'wfp_logo',
					'type' => 'media',
				);

				$settings[] = array(
					'name' => __('Account', 'give-wayforpay'),
					'desc' => '',
					'id' => 'wfp_api_account',
					'type' => 'text',
				);

				$settings[] = array(
					'name' => __('Secret', 'give-wayforpay'),
					'desc' => '',
					'id' => 'wfp_api_secret',
					'type' => 'text',
				);

				$settings[] = array(
					'name' => __('Description', 'give-wayforpay'),
					'desc' => '',
					'id' => 'wfp_description',
					'type' => 'textarea',
				);

				$settings[] = array(
					'id' => 'wfp_give_title',
					'type' => 'sectionend',
				);

				break;

		}

		return $settings;
	}

	function wfp_callback_handler(\WP_REST_Request $request)
	{
		$credential = new AccountSecretCredential(give_get_option('wfp_api_account'), give_get_option('wfp_api_secret'));

		try {
			global $wpdb;
			$handler = new ServiceUrlHandler($credential);
			$response = $handler->parseRequestFromPostRaw();

			$order_id = $response->getTransaction()->getOrderReference();
			$donation = $wpdb->get_row("SELECT * FROM wp_give_donationmeta WHERE meta_key='trx_hash' AND meta_value='$order_id'");
			$donation_id = $donation->donation_id;

			$donation = new Payment($donation_id);
			$donation->update_status('publish');
			// add payment note
			$donation->add_note(
				sprintf(
				/* translators: %s: payment transaction id */
					__('Payment complete. Transaction ID: %s', 'give'),
					$order_id
				)
			);

			$donation->save();
		} catch (WayForPaySDKException $e) {
			file_put_contents('wfp-trx-error.json', $e->getMessage());
		}
	}

	function custom_endpoint_content()
	{
		if (isset($_GET['type']) && $_GET['type'] === 'wfp' && !empty($_GET['hash'])) {
			global $wpdb;
			$order_id = $_GET['hash'];
			$donation = $wpdb->get_row("SELECT * FROM wp_give_donationmeta WHERE meta_key='trx_hash' AND meta_value='$order_id'");
			$donation_id = $donation->donation_id;
			$donation = Donation::find($donation_id);

			$credential = new AccountSecretCredential(give_get_option('wfp_api_account'), give_get_option('wfp_api_secret'));

			$form = PurchaseWizard::get($credential)
				->setOrderReference($order_id)
				->setAmount($donation->amount->getAmount() / 100)
				->setCurrency('UAH')
				->setOrderDate(new \DateTime())
				->setMerchantDomainName($_SERVER['HTTP_HOST'])
				->setClient(new Client(
					$donation->firstName,
					$donation->lastName,
					$donation->email,
					'',
					'UA'
				))
				->setProducts(new ProductCollection(array(
					new Product($donation->formTitle, $donation->amount->getAmount() / 100, 1)
				)))
				->setReturnUrl(urlencode(give_get_success_page_url()))
				->setServiceUrl(site_url('/wp-json/wfp/callback'))
				->getForm()
				->getAsString();

			printf('<div id="wfp-form">%s</div>
<style>#wfp-form{ visibility: hidden; }</style>
<script>
var form = document.getElementById("wfp-form");
			form.getElementsByTagName("form")[0].submit();
</script>
', $form);
			exit;
		}
	}

	function custom_endpoint_init()
	{
		add_rewrite_endpoint('wfp-pay', EP_ROOT);
	}

	function wpdocs_dequeue_script()
	{
		wp_deregister_script('give');
		wp_dequeue_script('give');
	}


	public function wfp_for_give_register_plugin_includes_admin()
	{

		wp_register_script('wfp_for_give', plugins_url('../assets/js/script.js', __FILE__), ['jquery'], false, true);
		wp_enqueue_script('wfp_for_give');

		wp_enqueue_script('wayforpay','https://secure.wayforpay.com/server/pay-widget.js');

		// add ajax url
		wp_localize_script('wfp_for_give', 'givewayforpay', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
		));

	}

	function give_remove_fieldsets()
	{
		remove_action('give_cc_form', 'give_get_cc_form');
		add_action('give_cc_form', [$this, 'give_get_cc_form'], 11, 2);
	}

	function give_get_cc_form($form_id, $args = [])
	{

		ob_start();

		/**
		 * Fires while rendering credit card info form, before the fields.
		 *
		 * @param int $form_id The form ID.
		 *
		 * @since 1.0
		 */
		do_action('give_before_cc_fields', $form_id);
		?>
		<fieldset id="give_cc_fields-<?php echo $form_id; ?>" class="give-do-validate">
			<?php echo give_get_option('wfp_description') ?>
		</fieldset>
		<?php
		/**
		 * Fires while rendering credit card info form, before the fields.
		 *
		 * @param int $form_id The form ID.
		 *
		 * @since 1.0
		 */
		// do_action( 'give_after_cc_fields', $form_id );

		echo ob_get_clean();
	}
}
