<?php
/**
 * Plugin Name:     GiveWP Wayforpay
 * Plugin URI:      https://github.com/karakushan/givewp-wayforpay
 * Description:        Wayforpay payment gateway for GiveWP
 * Author:          Vitaliy Karakushan
 * Author URI:      https://github.com/karakushan
 * Text Domain:     give-wayforpay
 * Domain Path:     /languages
 * Version:         0.2.0
 *
 * @package         Givewp_Liqpay_Gateway
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

if (!defined('GWFP_PLUGIN_VERSION')) define('GWFP_PLUGIN_VERSION', '0.2.0');

require_once __DIR__ . '/vendor/autoload.php';

if (!in_array('give/give.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	return;
}

add_action('plugins_loaded',  'give_wfp_load_textdomain');
function give_wfp_load_textdomain()
{
	load_plugin_textdomain('give-wayforpay', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
}

if (class_exists('GiveWFPGateway\WFP_Init')) {
	$init = new GiveWFPGateway\WFP_Init();
}




