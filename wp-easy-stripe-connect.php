<?php

/**
 *
 * @link              https://github.com/wdonayre
 * @since             1.1.3
 * @package           WP_Easy_Stripe_Connect
 *
 * @wordpress-plugin
 * Plugin Name:       WP Easy Stripe Connect
 * Plugin URI:        https://github.com/glabstech/wp-easy-stripe-connect
 * Description:       'Stripe Connect Integred'
 * Version:           1.1.3
 * Author:            William Donayre Jr
 * Author URI:        https://github.com/wdonayre
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-easy-stripe-connect
 * Domain Path:       /languages
 */


require __DIR__ . '/vendor/autoload.php';

use Stripe\Account;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_EASY_STRIPE_CONNECT_VERSION', '1.1.3' );
define( 'GOBLOO_STRIPE_CONNECT_VERSION', '1.1.3' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-gobloo-stripe-connect-activator.php
 */
function activate_wp_easy_stripe_connect() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-gobloo-stripe-connect-activator.php';


	Gobloo_Stripe_Connect_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-gobloo-stripe-connect-deactivator.php
 */
function deactivate_wp_easy_stripe_connect() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gobloo-stripe-connect-deactivator.php';
	Gobloo_Stripe_Connect_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_easy_stripe_connect' );
register_deactivation_hook( __FILE__, 'deactivate_wp_easy_stripe_connect' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-gobloo-stripe-connect.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

use GoblooStripeConnect\GB_Stripe_Options;


function run_gobloo_stripe_connect() {

	$plugin = new Gobloo_Stripe_Connect();
	$plugin->run();

}
run_gobloo_stripe_connect();

function ui_new_role() {  
 
    //add the new user role
    add_role(
        'power_member',
        'Power Member',
        array(
            'read'         => true,
            'delete_posts' => false
        )
    );
 
}
// add_action('admin_init', 'ui_new_role');

function wps_remove_role() {
    remove_role( 'power_member' );
}
add_action( 'init', 'wps_remove_role' );
