<?php 

use Stripe\Stripe;

use GoblooStripeConnect\GB_Stripe_Options;
use GoblooStripeConnect\GB_Products;

include_once 'class-gobloo-stripe-options.php';
include_once 'class-gobloo-stripe-registration.php';
include_once 'class-gobloo-products.php';
include_once 'class-gobloo-ajax.php';



/**
 * Initialize Stripe Key
 */
function class_init_stripe(){
    //init Stripe
    Stripe::setApiKey(GB_Stripe_Options::Stripe()['secret_key']);   //publishable_key
}
add_action('init','class_init_stripe');

function class_init_products(){
    $prod = GB_Products::_();
}
add_action('init','class_init_products');


function initOnFooter(){
    $pubKey = GB_Stripe_Options::Stripe()['publishable_key'];
    echo '<script> var gobloo_stripe = Stripe("'.$pubKey.'"); </script>';
}
add_action('wp_footer','initOnFooter');

