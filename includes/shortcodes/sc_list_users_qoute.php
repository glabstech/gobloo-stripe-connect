<?php 

use GoblooStripeConnect\GB_Stripe_Options;

/**
 * Displays a Link that redirects to Advertiser Registration via Stripe Express
 */

 function sc_func_goblooUsersQoute($atts){
    
    $stripe_settings = GB_Stripe_Options::Stripe();

    $role = get_option( '_crb_new_role' );

    $a = shortcode_atts( array(

    ), $atts );
    
    $advertisers = get_users( 'orderby=email&role='.$role );
    $qoutePage = carbon_get_theme_option( 'crb_qoute_page' );

    if(!empty($qoutePage)) $qoutePage = reset($qoutePage)['id'];

    ob_start();
    echo '<ul class="advertisers-list">';
    // Array of WP_User objects.
    foreach ( $advertisers as $advertiser ) {
        $display_name = '';
        $userObj = get_userdata($advertiser->ID);
        if($userObj->first_name || $userObj->last_name){
            $display_name = $userObj->first_name.' '. $userObj->last_name;
            $display_name = trim($display_name);
        }
        // var_dump($advertiser);
        if(!$display_name){
            $display_name = $advertiser->data->user_email;
        }
        $account_id = get_user_meta($advertiser->ID,'stripe_account_id',true);

        $access_token = get_user_meta($advertiser->ID,'stripe_account_id',true);
        $pub_key = get_user_meta($advertiser->ID,'stripe_pub_key',true);

        // $dataObject = [
        //     'access_token' => $access_token,
        //     'account_id' => $account_id,
        //     'stripe_pub_key' => $pub_key
        // ];
        

        echo '<li>'.$display_name.' <a target="_blank" href="'.get_permalink($qoutePage).'/?id='.$advertiser->ID.'&acc='.$account_id.'">Open Qoute Page</a></li>';
    }
    echo '</ul>';
    
    
    ?>
        
    <?php

    return ob_get_clean();
 }
 add_shortcode('gobloo-advertiser-list', 'sc_func_goblooUsersQoute'); 