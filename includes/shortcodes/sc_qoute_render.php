<?php 

use GoblooStripeConnect\GB_Stripe_Options;
use GoblooStripeConnect\GB_Products;

use Stripe\Account;

/**
 * Displays a Link that redirects to Advertiser Registration via Stripe Express
 */

 function sc_func_goblooQouteRender($atts){
    
    // $stripe_settings = GB_Stripe_Options::Stripe();

    $role = get_option( '_crb_new_role' );

    $a = shortcode_atts( array(

    ), $atts );
    // var_dump('test');

    if(isset($_GET['id'])){
        $user_id = $_GET['id'];
        $pid = $_GET['pid'];

        //get available Products
        $availableProducts = GB_Products::Products($_GET['id']);
        $defaultProduct = carbon_get_theme_option('crb_plan_prefix');


        // var_dump($availableProducts);
        // if($availableProducts){
        //    // $initialSelection = $availableProducts[0]['prefix'].$availableProducts[0]['price'];

        //     $plan_name = get_option( '_crb_plan_name' );
        //     $plan_prefix = get_option( '_crb_plan_prefix' );
        //     $plan_price = get_option( '_crb_global_monthly' );

        //     $options = '<option data-price="'.$plan_price.'" value="'.$plan_prefix.$plan_price.'" selected>'.$plan_name.'</option>';
        //     foreach($availableProducts as $availableProduct){
        //         $options=$options.'<option data-price="'.$availableProduct['price'].'" value="'.$availableProduct['prefix'].$availableProduct['price'].'">'.$availableProduct['name'].'</option>';
        //     }
        // }
   
        if(isset($_GET['acc']) && !empty($_GET['acc'])){
            $account_id = $_GET['acc'];
        } else {
            $account_id = get_user_meta($user_id,'stripe_account_id',true);
        }

        $response = Account::retrieve(
            $account_id
        );
        $accountDisabled = ( $response->verification->disabled_reason ) ? true :false ;
        update_user_meta($user_id,'stripe_account_disabled',$accountDisabled);

        $disabled = '';
        if($accountDisabled) $disabled = ' disabled ';


        //get qoute template
        $qoute_template = do_shortcode( carbon_get_theme_option('crb_qoute_template') );
        //get image
        $image = carbon_get_user_meta($user_id,'crb_user_photo');
        if(!$image){
            $image = 'http://0.gravatar.com/avatar/c9baeed993a7e9fb22b4a773c22e52ca?s=96&d=mm&r=g';
        }
        //get location
        $location = carbon_get_user_meta($user_id,'crb_location');

        //get global monthly rate
        if((isset($_GET['pid']) && !empty( $_GET['pid'] )) && ($_GET['pid']!= $defaultProduct)){
            $monthly = $availableProducts[$pid]['price'];
        } else {
            $monthly = carbon_get_theme_option('crb_global_monthly');
        }

        $userObj = get_userdata($user_id);
        
        $first_name = $userObj->first_name;
        $last_name = $userObj->last_name;
        $joined = $userObj->user_registered;

        // $dataObject = [
        //     'access_token'      => get_user_meta($user_id, 'stripe_access_token', true),
        //     'account_id'        => $account_id,
        //     'stripe_pub_key'    => get_user_meta($user_id, 'stripe_pub_key', true)
        // ];

        $qoute_template = str_replace("{{fullname}}", $first_name.' '.$last_name, $qoute_template);
        $qoute_template = str_replace("{{location}}", $location, $qoute_template);
        $qoute_template = str_replace("{{joined_date}}", date('M jS, Y', strtotime($joined) ), $qoute_template);
        $qoute_template = str_replace("{{photo}}", $image, $qoute_template);
        $qoute_template = str_replace("{{monthly}}", $monthly, $qoute_template);
        $qoute_template = str_replace("{{start_date_field}}", '<input name="start_date" type="date" />', $qoute_template);

        // if($availableProducts){
        //     $qoute_template = str_replace("{{add_payment_method}}", '<div class="gobloo-product-selector-outer"><select class="gobloo-product-selector">'.$options.'</select></div><a  data-fullancer-id="'.$_GET['id'].'"  data-fullancer-stripe="'.$account_id.'" " class="add-payment-method">Add Payment Method</a>', $qoute_template);
        // } else {
        //     $qoute_template = str_replace("{{add_payment_method}}", '<a data-fullancer-id="'.$_GET['id'].'" data-fullancer-stripe="'.$account_id.'" class="add-payment-method">Add Payment Method</a>', $qoute_template);
        // }

        if( (isset($_GET['pid']) && !empty( $_GET['pid'] )) && ($_GET['pid']!= $defaultProduct )){
            // var_dump($availableProducts);

            // $productKey = str_replace($product_price,"",$productKey);
            $product_price = $availableProducts[$pid]['price'];
            $product_label = $availableProducts[$pid]['name'];
            
            if($accountDisabled == false){
                $qoute_template = str_replace("{{add_payment_method}}", '<a data-product-label="'.$product_label.'" data-product="'.$pid.$product_price.'" data-product-price="'.$product_price.'" data-fullancer-id="'.$_GET['id'].'" data-fullancer-stripe="'.$account_id.'" class="add-payment-method"><span class="text">Add Payment Method</span><span class="loader loader-3"><span></span></span></a>', $qoute_template);   
            } else {
                $notif =  '<div style="background:orange; padding:10px; text-align:center; color:white;">Quote page not available for this Advertiser as of the moment. If you are the advertiser, please <a href="/login" target="_blank">Login</a> to know more about the issue. </div>';
                $qoute_template =  str_replace("{{add_payment_method}}", $notif , $qoute_template);
            }

        } else {
            if($accountDisabled == false){
                $qoute_template = str_replace("{{add_payment_method}}", '<a data-fullancer-id="'.$_GET['id'].'" data-fullancer-stripe="'.$account_id.'" class="add-payment-method"><span class="text">Add Payment Method</span><span class="loader loader-3"><span></span></span></a>', $qoute_template);
            } else {
                $notif =  '<div style="background:orange; padding:10px; text-align:center; color:white;">Quote page not available for this Advertiser as of the moment. If you are the advertiser, please <a href="/login" target="_blank">Login</a> to know more about the issue. </div>';
                $qoute_template =  str_replace("{{add_payment_method}}", $notif , $qoute_template);
            }
        }

        ob_start();
        echo $qoute_template;
        ?>

        <?php
        return ob_get_clean();

    }

 }
 add_shortcode('gobloo-quote', 'sc_func_goblooQouteRender'); 