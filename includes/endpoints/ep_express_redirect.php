<?php 

//client_id ca_FQG392oAdEf08w4AbRRb6MfV3kla8Bq1
//redirect_uri https://safety.pwdcdev.com/profile?access_token=ac_FSLGNB4KZoMXyMYWKQiXJeBXqPVNKoUo

namespace GoblooStripeConnect;

use GoblooStripeConnect\GB_Stripe_Options;
use GoblooStripeConnect\GBStripeRegistration;

use Stripe\Stripe;
use Stripe\Transfer;
use Stripe\OAuth;
use Stripe\Account;
use Stripe\Charge;

use Stripe\Plan;

class ExpressRedirect {

    protected $loader;

	/** Hook WordPress
	*	@return void
	*/
	public function __construct(){
        global $plugin;
		add_filter('query_vars', array($this, 'add_query_vars'), 0);
		add_action('parse_request', array($this, 'sniff_requests'), 0);
        add_action('init', array($this, 'add_endpoint'), 0);
        
    }

	/** Add public query vars
	*	@param array $vars List of current public query vars
	*	@return array $vars
	*/
	public function add_query_vars($vars){
		$vars[] = '__scapi';
		$vars[] = 'url';
		return $vars;
	}

	/** Add API Endpoint
	*	This is where the magic happens - brush up on your regex skillz
	*	@return void
	*/
	public function add_endpoint(){
		add_rewrite_rule('^scapi/?([0-9]+)?/?','index.php?__scapi=1&args=$matches[1]','top');
	}

	/**	Sniff Requests
	*	This is where we hijack all API requests
	* 	If $_GET['__api'] is set, we kill WP and serve up pug bomb awesomeness
	*	@return die if API request
	*/
	public function sniff_requests(){
        global $wp;
		if(isset($wp->query_vars['__scapi'])){
			$this->handle_request();
			exit;
		}
	}

	/** Handle Requests
	*	This is where we send off for an intense pug bomb package
	*	@return void
	*/
    protected function handle_request(){
		global $wp;

		$stripe_settings = GB_Stripe_Options::Stripe();

        $state = isset($_GET['state'])?$_GET['state']:null;

		
		if(empty($state)) return header("Location: /"); 
		
		

		Stripe::setApiKey( $stripe_settings['secret_key'] );

        if($state === 'register'){
			$code = $_GET['code'];	
			GBStripeRegistration::completeExpress($code);
		} else if($state === 'test'){
			// $account_id = $_GET['aid'];
			$registerSuccess = carbon_get_theme_option( 'crb_redirect_register_success' );

			if(!empty($registerSuccess)) $registerSuccess = reset($registerSuccess)['id'];
			// var_dump( $registerSuccess );
			// var_dump(wp_http_validate_url('/'.get_permalink($registerSuccess)));
			// // header("Location: /".get_permalink($registerSuccess)); wp_http_validate_url
		}
		else if($state==="account"){
			$account_id = $_GET['aid'];
			$response = Account::retrieve(
				$account_id
			);

			$registerSuccess = carbon_get_theme_option( 'crb_redirect_register_success' );
			if(!empty($registerSuccess)) $registerSuccess = reset($registerSuccess)['id'];

			if($response->verification->disabled_reason){
				$account_link = Account::createLoginLink($account_id);
				if($account_link->url){
					//header('Location: '. $account_link->url);
					//echo '<script> window.open("'. $account_link->ur .'","_blank")</script>';
					//echo '<script> location.replace("'. $registerSuccess .'"); </script>';
				} //else {
					//$registerSuccess = carbon_get_theme_option( 'crb_redirect_register_success' );
					//if(!empty($registerSuccess)) $registerSuccess = reset($registerSuccess)['id'];
					
					//header("Location: ".get_permalink($registerSuccess) );	
				//}

				//echo '<script> location.replace("'. $registerSuccess .'"); </script>';

			} //else {
				// $registerSuccess = get_option( '_crb_redirect_register_success' );
				//$registerSuccess = carbon_get_theme_option( 'crb_redirect_register_success' );
				//if(!empty($registerSuccess)) $registerSuccess = reset($registerSuccess)['id'];
				//header("Location: ".get_permalink($registerSuccess) );	
				//echo '<script> location.replace("'. $registerSuccess .'"); </script>';
			//}
			echo '<script> location.replace("'. get_permalink($registerSuccess) .'"); </script>';
		}
		else if($state==='accountlink'){
			$account_id = $_GET['aid'];
			// // \Stripe\Stripe::setApiKey('sk_test_XyskHsirdyMQsCINCI5VgGC7');
			// // var_dump($account_id);
			// \Stripe\Account::retrieve(
			// 	'acct_1DlMLCAuiTuwgS1s'
			//   );
			// var_dump($reponse);
			$response = Account::createLoginLink($account_id);

			header("Location: ".($response->url) );	
			// if($response){
			// 	wp_send_json_success($response->url);
			// }
			
		}
		else if($state === 'paid'){
			
			if(!isset($_GET['session_id']) || empty($_GET['session_id']) ){
				header("Location: ".get_site_url() );		
			}

			$paymentSuccess = carbon_get_theme_option( 'crb_redirect_success_page' );
			if(!empty($paymentSuccess)) $paymentSuccess = reset($paymentSuccess)['id'];
			//header("Location: ".get_permalink($paymentSuccess) );
			echo '<script> location.replace("'. get_permalink($paymentSuccess) .'"); </script>';
			
			
		}
		else if($state === 'paymentCancelled'){
			
			if(!isset($_GET['session_id']) || empty($_GET['session_id']) ){
				header("Location: ".get_site_url() );		
			}

			$paymentCancelled = carbon_get_theme_option( 'crb_redirect_cancel_page' );
			if(!empty($paymentCancelled)) $paymentCancelled = reset($paymentCancelled)['id'];
			header("Location: ".get_permalink($paymentCancelled) );
			
			
		}
		else if($state === 'plan'){

			$account_id = $_GET['aid'];
			
			try{
				$plan = Plan::retrieve('gobloo-monthly-2001');
			} catch(\Exception $e){
				$new_plan = Plan::create([
					'id' => 'gobloo-monthly-2001',
					'currency' => 'usd',
					'interval' => 'month',
					'product' => [
						'name' => 'Monthly 2001'
					],
					'nickname' => 'Monthly 2001 - Nickname',
					'amount' => 3000,
				]);
			}

			$new_plan = NULL;



			// var_dump($new_plan);

		}
		else if($state=="login"){
			$user_id = get_current_user_id();	
			$account_id = get_user_meta($user_id,'stripe_account_id',true); 
			$response = Account::createLoginLink($account_id);
			header("Location: ".($response->url) );	
		}
    }

}

new ExpressRedirect();