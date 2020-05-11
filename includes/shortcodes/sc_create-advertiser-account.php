<?php 

use GoblooStripeConnect\GB_Stripe_Options;

/**
 * Displays a Link that redirects to Advertiser Registration via Stripe Express
 */

 function sc_func_goblooAdvertiserRegisterLink($atts){
    
    $stripe_settings = GB_Stripe_Options::Stripe();
    
    $a = shortcode_atts( array(
        'class' => '',
        'text' => 'Advertiser Registration',
        'redirect_uri' => ''
    ), $atts );
    
    // $stripe_live_mode   = carbon_get_theme_option( 'crb_stripe_live_mode' );
    // var_dump($stripe_live_mode);
    //$client_id          = carbon_get_theme_option();

    if($a['redirect_uri']){
        $redirect = 'redirect_uri='.$a['redirect_uri'];
    } else {
        $redirect = '';
    }

    // https://connect.stripe.com/express/oauth/authorize?redirect_uri=https://gobloo.com/store-manager/settings/&client_id=ca_GRfsmWyLLhNwUZYf22wfBAhlfgYveWnO&state={STATE_VALUE}
    // https://dashboard.stripe.com/express/oauth/authorize?response_type=code&client_id=ca_GRfsmWyLLhNwUZYf22wfBAhlfgYveWnO&scope=read_write
    // $oAuthExpress = 'https://connect.stripe.com/express/oauth/authorize?'.$redirect.'&client_id='.$stripe_settings['client_id'];
    
    $oAuthExpress = 'https://connect.stripe.com/express/oauth/authorize?'.$redirect.'&stripe_user[country]=US&client_id='.$stripe_settings['client_id'].'&suggested_capabilities[]=transfers&suggested_capabilities[]=card_payments';

    ob_start();
    
    ?>
        <div class="sc-global-wrapper">
            <div class="sc-field-group">
                <label for="sc-first-name">First Name</label>
                <input type="text" id="sc-first-name" name="sc-first-name" placeholder="" required />
            </div>
            <div class="sc-field-group">
                <label for="sc-first-name">Last Name</label>
                <input type="text" id="sc-last-name" name="sc-last-name" placeholder="" required />
            </div>
            <div class="sc-field-group">
                <label for="sc-email">Email</label>
                <input type="email" id="sc-email" name="sc-email" placeholder="" required />
            </div>
            <a id="oauth-link" class="<?php echo $a['class'] ?>" data-url="<?php echo $oAuthExpress ?>"><span class="text"><?php echo $a['text'] ?></span><span class="loader loader-3"><span></span></span></a>
        </div>
    <?php
    
    return ob_get_clean();
 }
 add_shortcode('gobloo-advertiser-register', 'sc_func_goblooAdvertiserRegisterLink'); 