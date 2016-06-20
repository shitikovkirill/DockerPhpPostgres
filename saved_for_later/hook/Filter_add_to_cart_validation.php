<?php
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 20.06.16
 * Time: 11:39
 */

namespace SaveList\Hook;


class Filter_add_to_cart_validation
{
    function __construct(){
        //add_filter('woocommerce_add_to_cart_validation', array($this, 'add_to_cart_validation'));
    }
    public function add_to_cart_validation($val, $product_id, $quantity, $variation_id){
        return false;
    }
}