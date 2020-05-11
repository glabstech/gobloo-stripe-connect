<?php 

namespace GoblooStripeConnect;

class GB_Products {

    private static $instance = null;
    private static $products = [];

    private function _construct(){}

    public static function _(){
        if(!self::$instance){
            self::$instance = new GB_Products();

            $products = carbon_get_theme_option( 'crb_products' );
            // var_dump($products);
            $_products = [];

            foreach($products as $product){

                $advertisers = [];
                foreach($product['crb_product_advertisers']  as $advertiser){
                    $advertisers[] =    $advertiser['id'];
                }

                $_products[ $product['crb_product_prefix'] ] = [
                    'name' => $product['crb_product_name'],
                    'prefix' => $product['crb_product_prefix'],
                    'price' => $product['crb_product_price'],
                    'advertisers' => $advertisers        
                ];   

            }
            self::$products = $_products;
            
            
        }
        return self::$instance;
    }

    public static function Products($advertiser_id=''){
        
        // $products = carbon_get_theme_option( 'crb_products' );
  
        if(!empty( $advertiser_id )){
            $ret = [];
            foreach(self::$products as $key => $product){
                if( in_array($advertiser_id, $product['advertisers'] ) ){
                    $ret[$key]=$product;
                }
            }
            return $ret;
        }

        return self::$products;
        
        
    }

}