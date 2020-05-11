<?php 

namespace GoblooStripeConnect;

use GoblooStripeConnect\GB_Stripe_Options;
use GoblooStripeConnect\GBStripeRegistration;

use Stripe\Stripe;
use Stripe\Event;

use Stripe\Transfer;
use Stripe\OAuth;
use Stripe\Account;
use Stripe\Charge;

use Stripe\Plan;

class GoblooStripeWebhooks {

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
		$vars[] = '__hooks';
		$vars[] = 'url';
		return $vars;
	}

	/** Add API Endpoint
	*	This is where the magic happens - brush up on your regex skillz
	*	@return void
	*/
	public function add_endpoint(){
		add_rewrite_rule('^hooks/?([0-9]+)?/?','index.php?__hooks=1&args=$matches[1]','top');
	}

	/**	Sniff Requests
	*	This is where we hijack all API requests
	* 	If $_GET['__api'] is set, we kill WP and serve up pug bomb awesomeness
	*	@return die if API request
	*/
	public function sniff_requests(){
        global $wp;
		if(isset($wp->query_vars['__hooks'])){
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
        
        $payload = @file_get_contents('php://input');
        $event = null;

        try {
            $event = Event::constructFrom(
                json_decode($payload, true)
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                //$paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                // Then define and call a method to handle the successful payment intent.
                // handlePaymentIntentSucceeded($paymentIntent);
                $test = '1';
                break;
            default:
                // Unexpected event type
                http_response_code(400);
                exit();
        }
        
        http_response_code(200);

        // error_log('error test');
        // var_dump($payload);
        // error_log(json_encode( $payload ));

    }

}

new GoblooStripeWebhooks();