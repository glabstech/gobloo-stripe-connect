<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/wdonayre
 * @since      1.0.0
 *
 * @package    Gobloo_Stripe_Connect
 * @subpackage Gobloo_Stripe_Connect/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Gobloo_Stripe_Connect
 * @subpackage Gobloo_Stripe_Connect/includes
 * @author     William Donayre Jr <wdonayredroid+gobloo@gmail.com>
 */
class Gobloo_Stripe_Connect_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'gobloo-stripe-connect',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
