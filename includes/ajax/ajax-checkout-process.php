<?php 


namespace GoblooStripeConnect;

use GoblooStripeConnect\WPAjax;
use GoblooStripeConnect\GB_Stripe_Options;
use GoblooStripeConnect\GB_Products;

use Stripe\Plan;
use Stripe\Checkout\Session;

class CheckoutProcess extends WPAjax
{
    protected $action = 'CheckoutProcess';  

    protected function run(){    

        //create product and plan
        $plan = self::createPlan([
            'fullancer_id'      => $_POST['fullancer_id'], 
            'fullancer_stripe'  => $_POST['fullancer_stripe'],
            'product'           => $_POST['product'],
            'price'             => $_POST['price'],
            'label'             => $_POST['label']
        ]);

        if( (isset($plan->success) && $plan->success) || !isset($plan->success) ){
            // wp_send_json_success($plan->data);
            $session = self::createCheckoutSession($plan->plan, $_POST['fullancer_id'] );
            if( (isset($session->success) && $session->success) || !isset($session->success)){
                wp_send_json_success((object)['pk'=>$session->pk,'session'=>$session->session]);
            }
            else {
                wp_send_json_error($session->msg,400);        
            }
            
        } 
        else {
            wp_send_json_error($plan->msg,400);    
        }
        
        // if(!empty($_POST['fullancer_id']) && !empty($_POST['fullancer_stripe'])){

        //     $plan_name = get_option( '_crb_plan_name' );
        //     $plan_monthly_rate = get_option( '_crb_global_monthly' );

        //     if(!$plan_name && !$plan_monthly_rate){
        //         wp_send_json_error('Gobloo Settings: Please check Plan Name or Plan Monthly Rate if it has defined values.');
        //         wp_die();
        //     }
        //     $plan_name = $plan_name.'-'.$plan_monthly_rate;

        //     try{
        //         $plan = Plan::retrieve($plan_name);
                
		// 	} catch(\Exception $e){
		// 		$plan = Plan::create([
		// 			'id' => $plan_name,
		// 			'currency' => 'usd',
		// 			'interval' => 'month',
		// 			'product' => [
		// 				'name' => 'Gobloo Monthly $'.$plan_monthly_rate
		// 			],
		// 			'nickname' => 'Gobloo Monthly - $'.$plan_monthly_rate. ' Plan',
		// 			'amount' => (int)$plan_monthly_rate*100,
		// 		]);
        //     }
            
        //     wp_send_json_success($plan);
        // }

    }

    //static methods
    private static function createPlan($args){

        if(!isset($args['fullancer_id']) || empty($args['fullancer_id'])){
            return (object)['success'=>false, 'msg' => 'Fullancer ID parameter cannot be null.'];
        }
        if(!isset($args['fullancer_stripe']) || empty($args['fullancer_stripe'])){
            return (object)['success'=>false, 'msg' => 'Fullancer Stripe Account parameter cannot be null.'];
        }

        $plan_name = get_option( '_crb_plan_prefix' );
        $plan_monthly_rate = get_option( '_crb_global_monthly' );
        $plan_label = get_option( '_crb_plan_name' );

        if(!$plan_name && !$plan_monthly_rate){
            return (object)['success'=>false, 'msg' => 'Gobloo Settings: Please check Plan Name or Plan Monthly Rate if it has defined values.'];
        }

        if($args['product'] && $args['price']){
            $plan_name = $args['product'];
            $plan_monthly_rate = $args['price'];
            $plan_label = $args['label'];
        } else {
            $plan_name = $plan_name.$plan_monthly_rate;
        }

        $secret_key = get_user_meta($args['fullancer_id'], 'stripe_access_token', true);

        try{
            $plan = (object)['success'=>true,'plan'=>Plan::retrieve($plan_name,["api_key" => $secret_key])];
            
        } catch(\Exception $e){
            $plan = (object)[
                'success' => true,
                'plan'=>Plan::create([
                    'id' => $plan_name,
                    'currency' => 'usd',
                    'interval' => 'month',
                    'product' => [
                        'name' => $plan_label
                    ],
                    'nickname' => $plan_label,
                    'amount' => (int)$plan_monthly_rate*100,
                ],
                ["api_key" => $secret_key])
            ];
            
        }
        return $plan;
    }

    /**
     * Create Checkout Session
    */
    private static function createCheckoutSession($plan,$fullancer_id){

        if(empty($plan)) return (object)['status'=>false, 'msg'=>'Plan parameter cannot be null'];

        $redirect_success = get_option( '_crb_redirect_success' );
        if(!wp_http_validate_url( $redirect_success ) ){
            $redirect_success = wp_http_validate_url( get_site_url().$redirect_success ); 
        }

        $redirect_cancel = get_option( '_crb_redirect_cancel' );
        if(!wp_http_validate_url( $redirect_cancel ) ){
            $redirect_cancel = wp_http_validate_url( get_site_url().$redirect_cancel ); 
        }

        $secret_key     = get_user_meta($fullancer_id, 'stripe_access_token', true);
        $stripe_account = get_user_meta($fullancer_id, 'stripe_account_id', true);
        $pk             = get_user_meta($fullancer_id, 'stripe_pub_key', true);

        $platformFee = (int)get_option('_crb_platform_fee');
        $session = Session::create([
            'payment_method_types' => ['card'],
            'subscription_data' => [
              'items' => [[
                'plan' => $plan['id'],
              ]],
                'application_fee_percent' => $platformFee,
                // 'transfer_data' => [
                //     'destination' => $stripe_account,
                // ]
            ],
            'success_url' => get_site_url().'/scapi?state=paid',
            'cancel_url' =>  get_site_url().'/scapi?state=paymentCancelled',
            // 'transfer_data' => [
            //     'destination' => $stripe_account,
            // ]
            ] ,['stripe_account'=> $stripe_account ]); /*"api_key" => $secret_key*/

        return (object)[
            'success' => true,
            'session' => $session,
            'pk' =>$pk
        ];
    }
}