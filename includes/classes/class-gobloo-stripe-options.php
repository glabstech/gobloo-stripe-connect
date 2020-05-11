<?php 

namespace GoblooStripeConnect;

use Stripe\Stripe;
class GB_Stripe_Options {

    public static function Stripe(){



        $mode = carbon_get_theme_option( 'crb_stripe_live_mode' );
        
        if($mode){
            $publishable_key    = carbon_get_theme_option( 'crb_stripe_live_publishable_key' );
            $secret_key         = carbon_get_theme_option( 'crb_stripe_live_secret_key' );
            $client_id          = carbon_get_theme_option( 'crb_stripe_live_client_id' );
        } else {
            $publishable_key    = carbon_get_theme_option( 'crb_stripe_dev_publishable_key' );
            $secret_key         = carbon_get_theme_option( 'crb_stripe_dev_secret_key' );
            $client_id          = carbon_get_theme_option( 'crb_stripe_dev_client_id' );
        }

        if(empty($publishable_key)){
            echo '!!Publishable Key is empty in Gobloo Stripe Settings!!';
            return false;
        }
        if(empty($secret_key)){
            echo '!!Secret Key is empty in Gobloo Stripe Settings!!';
            return false;
        }
        if(empty($client_id)){
            echo '!!Client ID is empty in Gobloo Stripe Settings!!';
            return false;
        }

        //init Stripe
        // Stripe::setApiKey(self::Stripe[['secret_key']]);   
        
        return [
            'mode'              => $mode,
            'publishable_key'   => $publishable_key,
            'secret_key'        => $secret_key,
            'client_id'         => $client_id

        ];
    }

}