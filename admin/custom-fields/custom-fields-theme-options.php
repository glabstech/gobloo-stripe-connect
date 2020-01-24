<?php

// use Carbon_Fields;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'crb_attach_theme_options' );
function crb_attach_theme_options() {

    /**
     * MAIN CONTAINER in Options
     */
    $theme_options = Container::make( 'theme_options', 'Gobloo Settings');

    /**
     * STRIPE SETTINGS TAB
     */
    $theme_options->add_tab(
        __('Stripe Connect API'),
        [
            Field::make( 'checkbox', 'crb_stripe_live_mode', 'Live Mode' )->set_option_value( 'yes' ),
            Field::make( 'text', 'crb_stripe_live_publishable_key', 'Live Publishable Key' )    ->set_classes( 'admin-width-50p flex-unset') ,
            Field::make( 'text', 'crb_stripe_live_secret_key', 'Live Secret Key' )              ->set_classes( 'admin-width-50p flex-unset') ,
            Field::make( 'text', 'crb_stripe_dev_publishable_key', 'Dev Publishable Key' )      ->set_classes( 'admin-width-50p flex-unset') ,
            Field::make( 'text', 'crb_stripe_dev_secret_key', 'Dev Secret Key' )                ->set_classes( 'admin-width-50p flex-unset') ,
        ]
    );

    /**
     * PAYMENT FEE TAB
     */
    $theme_options->add_tab(
        __('Payment'),
        [
            Field::make( 'text', 'crb_platform_fee', 'Platform Fee' )
            ->set_classes( 'admin-width-50p flex-unset')  
            ->set_help_text( 'Platform fee is in Percentage Mode' )
        ]
    );

}

add_action( 'after_setup_theme', 'crb_load' );
function crb_load() {
    // require_once( ABSPATH . '/vendor/autoload.php' );
}