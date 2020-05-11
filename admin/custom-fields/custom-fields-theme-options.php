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
            Field::make( 'text', 'crb_stripe_live_publishable_key', 'Live Publishable Key' )    ->set_classes( 'admin-width-33p flex-unset') ,
            Field::make( 'text', 'crb_stripe_live_secret_key', 'Live Secret Key' )              ->set_classes( 'admin-width-33p flex-unset') ,
            Field::make( 'text', 'crb_stripe_live_client_id', 'Live Client ID' )                ->set_classes( 'admin-width-33p flex-unset')->set_help_text('Client ID can be found <a target="_blank" href="https://dashboard.stripe.com/account/applications/settings">here</a>.') ,
            Field::make( 'text', 'crb_stripe_dev_publishable_key', 'Test Publishable Key' )     ->set_classes( 'admin-width-33p flex-unset') ,
            Field::make( 'text', 'crb_stripe_dev_secret_key', 'Test Secret Key' )               ->set_classes( 'admin-width-33p flex-unset') ,
            Field::make( 'text', 'crb_stripe_dev_client_id', 'Test Client ID' )                 ->set_classes( 'admin-width-33p flex-unset')->set_help_text('Client ID can be found <a target="_blank" href="https://dashboard.stripe.com/account/applications/settings">here</a>.') ,
        ]
    );

    /**
     * Stripe Connect Account Registration
     */
    $theme_options->add_tab(
        __('Stripe Connect Registration'),
        [
            Field::make( 'select', 'crb_new_role', 'Role for newly stripe connect registered account' )
            ->set_options( 'getAvailableRoles' ),
            Field::make( 'association', 'crb_redirect_register_success', 'Register Success Redirect' )
            ->set_types([['type'=>'post','post_type'=>'page']])
            ->set_max(1)
            ->set_help_text( 'Redirect to the page assigned after advertiser account creation' ),

        ]
    );

    /**
     * Product for Subscription
     */
    $theme_options->add_tab(
        __('Product for Subscription'),
        [
            Field::make( 'text', 'crb_plan_name', 'Plan/Product Name' )
            ->set_classes( 'admin-width-33p flex-unset'),
            Field::make( 'text', 'crb_plan_prefix', 'Plan/Product Prefix' )
            ->set_classes( 'admin-width-33p flex-unset'),
            Field::make( 'text', 'crb_global_monthly', 'Plan Monthly Rate' )
            ->set_classes( 'admin-width-33p flex-unset'),
            Field::make( 'complex', 'crb_products' )
            ->add_fields( array(
                Field::make( 'text', 'crb_product_name' )
                ->set_classes( 'admin-width-33p flex-unset'),

                Field::make( 'text', 'crb_product_prefix','Product ID Prefix' )
                ->set_classes( 'admin-width-33p flex-unset'),

                Field::make( 'text', 'crb_product_price') 
                ->set_classes( 'admin-width-33p flex-unset')
                ->set_attribute( 'type', 'number' ),

                Field::make( 'association', 'crb_product_advertisers' )
                ->set_types( [['type'=>'user']] )
                
            ) )
        ]
    );

    /**
     * PAYMENT FEE TAB
     */
    $theme_options->add_tab(
        __('Payment/Checkout'),
        [
            Field::make( 'text', 'crb_platform_fee', 'Platform Fee' )
            ->set_classes( 'admin-width-50p flex-unset')  
            ->set_help_text( 'Platform fee is in Percentage Mode' ), 

            Field::make( 'association', 'crb_redirect_success_page', 'Redirect Success Page' )
            ->set_types([['type'=>'post','post_type'=>'page']])
            ->set_max(1)
            ->set_help_text( 'Redirect to the page assigned after successful payment' ),


            Field::make( 'association', 'crb_redirect_cancel_page', 'Redirect Cancel Page' )
            ->set_types([['type'=>'post','post_type'=>'page']])
            ->set_max(1)
            ->set_help_text( 'Redirect to the page when payment is cancelled' ),

        ]
    );


     /**
     * Quote Page Settings
     */
    $theme_options->add_tab(
        __('Quote Page Settings'),
        [
            Field::make( 'association', 'crb_qoute_page', 'Assign Quote Page' )
            ->set_types([['type'=>'post','post_type'=>'page']])
            ->set_max(1)
            ->set_help_text( 'Be sure to put the shortcode [gobloo-quote] to render advertiser details on that page' ),
            Field::make( 'rich_text', 'crb_qoute_template', 'Quote Layout Template' )
            ->set_help_text( 'Use following replaceable properties: {{fullname}}, {{location}}, {{location}}, {{joined_date}}, {{photo}}, {{monthly}}, {{start_date_field}}, {{add_payment_method}}' )
        ]
    );
    

}

add_action( 'carbon_fields_register_fields', 'crb_attach_user_meta' );
function crb_attach_user_meta(){
    $user_meta = Container::make( 'user_meta', 'Address' );
    
    /**
     * Add Image
    */
    $user_meta->add_fields( array(
        Field::make( 'image', 'crb_user_photo', 'Advertisers Photo' )
        ->set_value_type( 'url' ),
        Field::make( 'text', 'crb_location', 'Location' )
    ) );
}

add_action( 'after_setup_theme', 'crb_load' );
function crb_load() {
    // require_once( ABSPATH . '/vendor/autoload.php' );
}


/**
 * OPTIONS : Fetch List of Roles
 */
function getAvailableRoles(){
    // var_dump(get_editable_roles()); 
    $editableRoles = \get_editable_roles();
    $options = [];
    foreach($editableRoles as $key => $value){
        $options[$key] = $key;
    }
    return $options;
}