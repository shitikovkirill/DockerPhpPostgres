<?php
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 05.05.16
 * Time: 21:26
 */

namespace SaveList\Hook;

use Katzgrau\KLogger\Logger;
use PHPHtmlParser\Dom;

class Filter
{
    function __construct(){
        add_filter('woocommerce_loop_add_to_cart_link', array($this, 'add_to_cart_link'), 1000);
    }

    public function add_to_cart_link($html){
        /*
        $logger = new Logger(YWSFL_DIR.'/logs/add-to-cart');
        $logger->info('add_to_cart_link', [
            '$html'=>$html,
        ]);
        //*/
        $dom = new Dom;
        $dom->load($html);
        $a = $dom->find('a')[0];
        $result = str_replace( $a->text(),__("Add to cart", 'yith-woocommerce-save-for-later'), $html);
        return $result;
    }
}