<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/wdonayre
 * @since      1.0.0
 *
 * @package    Gobloo_Stripe_Connect
 * @subpackage Gobloo_Stripe_Connect/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Gobloo_Stripe_Connect
 * @subpackage Gobloo_Stripe_Connect/admin
 * @author     William Donayre Jr <wdonayredroid+gobloo@gmail.com>
 */

use GoblooStripeConnect\GB_Products;

class Gobloo_Stripe_Connect_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Gobloo_Stripe_Connect_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gobloo_Stripe_Connect_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gobloo-stripe-connect-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Gobloo_Stripe_Connect_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gobloo_Stripe_Connect_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gobloo-stripe-connect-admin.js', array( 'jquery' ), $this->version, false );

	}

}



function new_modify_user_table( $column ) {
	$column['stripeConnect'] = 'Gobloo Actions';

    return $column;
}
add_filter( 'manage_users_columns', 'new_modify_user_table' );

function new_modify_user_table_row( $val, $column_name, $user_id ) {

	$refRole	= carbon_get_theme_option( 'crb_new_role' );
	$userObj 	= get_user_by('id',$user_id);
	$account_id = get_user_meta($user_id,'stripe_account_id',true);

	//echo '<script>console.log("'.$val.'","'.$column_name.'");</script>';
    switch ($column_name) {
		case 'stripeConnect' :
			$qoutePage 	= carbon_get_theme_option( 'crb_qoute_page' );
			if(in_array($refRole,$userObj->roles)){
				if(!empty($qoutePage)) $qoutePage = reset($qoutePage)['id'];

				$availableProducts = GB_Products::Products($user_id);

				$defaultProduct = [
					'prefix'  => get_option( '_crb_plan_prefix' ),
					'price' => get_option( '_crb_global_monthly' ),
					'label' => get_option( '_crb_plan_name' )
				];
				$ret = '<select class="gobloo-admin-selector"><option selected>Choose Action</option><option data-product="'.$defaultProduct['prefix'].'" data-href="'.get_permalink($qoutePage).'?id='.$user_id.'&pid='.$defaultProduct['prefix'].'" >Open Quote: '.$defaultProduct['label'].'</option>';
				//<a target="_blank" href="'.get_permalink($qoutePage).'/?id='.$user_id.'">Open Qoute Page</a>
				foreach($availableProducts as $key => $product){
					$ret = $ret.'<option value="'.$product['prefix'].'" data-href="'.get_permalink($qoutePage).'?id='.$user_id.'&pid='.$product['prefix'].'" >Open Quote: '.$product['name'].'</option>';
				}
				$ret = $ret. '<option disabled>------</option><option value="access-stripe" data-href="'.get_site_url().'/scapi?state=accountlink&aid='.$account_id.'">Access Billing</option>';
				return $ret.'</select><a target="_blank" href="" class="hidden-qoute-link" style="display:none;"></a>';
			} else {
				return 'N/A';
			}
			break;

		// case 'stripeAccountLink' :
		// 	if(in_array($refRole,$userObj->roles)){
		// 		return '<a target="_blank" href="'.get_site_url().'/scapi?state=accountlink&aid='.$account_id.'">Access</a>';	
		// 	}
		// 	break;
		default:

    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'new_modify_user_table_row', 10, 3 );