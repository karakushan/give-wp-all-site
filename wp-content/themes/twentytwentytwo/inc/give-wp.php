<?php

/**
 * Register payment method.
 *
 * @param array $gateways List of registered gateways.
 *
 * @return array
 * @since 1.0.0
 *
 */

// change the prefix insta_for_give here to avoid collisions with other functions
function liqpay_for_give_register_payment_method($gateways)
{

    // Duplicate this section to add support for multiple payment method from a custom payment gateway.
    $gateways['instamojo'] = array(
        'admin_label' => __('LiqPay', 'instamojo-for-give'), // This label will be displayed under Give settings in admin.
        'checkout_label' => __('LiqPay', 'instamojo-for-give'), // This label will be displayed on donation form in frontend.
    );

    return $gateways;
}

add_filter('give_payment_gateways', 'liqpay_for_give_register_payment_method');

/**
 * Register Section for Payment Gateway Settings.
 *
 * @param array $sections List of payment gateway sections.
 *
 * @return array
 * @since 1.0.0
 *
 */

// change the insta_for_give prefix to avoid collisions with other functions.
function insta_for_give_register_payment_gateway_sections($sections)
{

    // `instamojo-settings` is the name/slug of the payment gateway section.
    $sections['liqpay'] = __('Liqpay', 'instamojo-for-give');

    return $sections;
}

add_filter('give_get_sections_gateways', 'insta_for_give_register_payment_gateway_sections');

/**
 * Register Admin Settings.
 *
 * @param array $settings List of admin settings.
 *
 * @return array
 * @since 1.0.0
 *
 */
// change the insta_for_give prefix to avoid collisions with other functions.
function liqpay_for_give_register_payment_gateway_setting_fields($settings)
{

    switch (give_get_current_setting_section()) {

        case 'liqpay':
            $settings[] = array(
                'name' => __('Public Key', 'instamojo-for-give'),
                'desc' => '',
                'id' => 'liqpay_public_key',
                'type' => 'text',
            );

            $settings[] = array(
                'name' => __('Private Key', 'instamojo-for-give'),
                'desc' => '',
                'id' => 'liqpay_private_key',
                'type' => 'text',
            );


            break;

    } // End switch().

    return $settings;
}

// change the insta_for_give prefix to avoid collisions with other functions.
add_filter('give_get_settings_gateways', 'liqpay_for_give_register_payment_gateway_setting_fields');
