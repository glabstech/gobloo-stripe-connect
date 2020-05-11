<?php

namespace GoblooStripeConnect;

use Stripe\OAuth;
use Stripe\Account;

class GBStripeRegistration
{

    private static $instance = null;  
    
    public static function completeExpress($code){

        $transient_prefix = 'gobloo_registration__';

        try{
            $response = false;
            
            if(!empty( $code )){
                
                //get role from settings page
                $new_role = get_option( '_crb_new_role' );

                $response = OAuth::token([
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                ]);
                // $response = true;

                if($response){

                    if(!empty($response->stripe_user_id)){
                        $accountObj = Account::retrieve( $response->stripe_user_id );

                        if($accountObj->email){
                            $email = $accountObj->email;
                            $display_name = $accountObj->display_name;
                            $first_name = $accountObj->first_name;
                            $last_name = $accountObj->last_name;
                            
                            $transient = get_transient( $transient_prefix . $email );
                            if($transient){
                                $transient = (object)json_decode( $transient );

                                $display_name = $transient->first_name.' '.$transient->last_name;
                                $first_name = $transient->first_name;
                                $last_name = $transient->last_name;

                                delete_transient( $transient_prefix . $email );
                            }

                            
                        }

                        //check if email already exist
                        if(null == username_exists($email)){
                            var_dump('FNAME >> '. $first_name);
                            var_dump('LNAME >> '. $last_name);
                            var_dump('EMAIL >> '. $email);
                            $user_id = wp_create_user ( urlencode( $first_name . " " . $last_name ), '1234567890', $email );    //TODO should be accessible from the gobloo options
                            wp_update_user(
                                array(
                                  'ID'       => $user_id,
                                  'nickname' => $email,
                                  'first_name' => $first_name,
                                  'last_name' => $last_name,
                                  'role' => $new_role,
                                  'display_name' => $display_name
                                )
                            );

                            add_user_meta($user_id,'stripe_account_id',$response->stripe_user_id);
                            add_user_meta($user_id,'stripe_access_token',$response->access_token);
                            add_user_meta($user_id,'stripe_refresh_token',$response->refresh_token);
                            add_user_meta($user_id,'stripe_pub_key',$response->stripe_publishable_key);
                        }

                    }

                    // $registerSuccess = carbon_get_theme_option( 'crb_redirect_register_success' );

                    // if(!empty($registerSuccess)) $registerSuccess = reset($registerSuccess)['id'];

                    // header("Location: ".get_permalink($registerSuccess) );

                    if (headers_sent()) {
                        echo '<script> alert("SUCCESS >> '. get_site_url( ).'/scapi?state=account&aid='.$response->stripe_user_id .'"); location.replace("'.get_site_url( ).'/scapi?state=account&aid='.$response->stripe_user_id.'"); </script>';
                    }
                    else{
                        header("Location: /scapi?state=account&aid=".$response->stripe_user_id);
                    }                    
                }
            } else {
                if (headers_sent()) {
                    alert('EMPTY CODE!!');
                    echo '<script> location.replace("'.get_site_url().'"); </script>';
                }
                else{
                    header("Location: /");
                } 
                // header("Location: /");
                // var_dump('empty');
            }
        } catch (\Exception $e){
            //TODO if invalid grant exception. Should do something to warn the user
            // var_dump(($e));
            // return $e;
            if (headers_sent()) {
                alert('Exception!!!');
                echo '<script> location.replace("'.get_site_url().'/#has-error-contact-admin"); </script>';
            }
            else{
                header("Location: /#has-error-contact-admin");
            } 
            
        }
        return $response;
    }

}