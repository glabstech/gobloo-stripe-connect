<?php 

namespace GoblooStripeConnect;

use Stripe\Stripe;

use GoblooStripeConnect\GB_Stripe_Options;
use GoblooStripeConnect\CheckoutProcess;
use GoblooStripeConnect\InitiateRegistration;

include_once 'ajax-checkout-process.php';
include_once 'ajax-initiate-registration.php';



add_filter('init',function(){
    CheckoutProcess::listen();
    InitiateRegistration::listen();
});

