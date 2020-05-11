<?php 


namespace GoblooStripeConnect;

use GoblooStripeConnect\WPAjax;


class InitiateRegistration extends WPAjax
{
    protected $action = 'InitiateRegistration';  

    protected function run(){  
        $transient_prefix = 'gobloo_registration__';

        $data = [];
        if( empty( $_POST['email'] ) || empty( $_POST['first_name'] )  || empty( $_POST['last_name'] ) ){
            wp_send_json_error(['msg'=>'Please verify if the info is valid or not empty'],400);
            wp_die();
        }

        $email = $_POST['email'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $url = $_POST['url'];
        
        
        
        if(email_exists($email)){
            wp_send_json_error(['msg'=>'User already exist. Please use unique user details.'],400);   
            wp_die();
        }
        
        delete_transient($transient_prefix . $email);

        $url = $url.'&stripe_user[first_name]='.$first_name;
        $url = $url.'&stripe_user[last_name]='.$last_name;
        $url = $url.'&stripe_user[email]='.urlencode( $email );
        //if the details are new and doesnt exist in the system.
        $transient_data = [
            'email' => $email,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'url' => $url
        ];

        
        set_transient( $transient_prefix . $email, json_encode( $transient_data ), 60*60*12 );

        $data = get_transient( $transient_prefix . $email );

        wp_send_json_success((object)['transient' => $data]);
        wp_die();


    }
}