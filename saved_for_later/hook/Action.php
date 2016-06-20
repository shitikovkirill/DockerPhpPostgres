<?php
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 05.05.16
 * Time: 21:28
 */

namespace SaveList\Hook;


use Symfony\Component\HttpFoundation\Request;

class Action
{
    public function __construct(){
        //add_action( 'woocommerce_add_to_cart', array( $this, 'remove_from_savelist_after_add_to_cart' ), 10, 2 );
    }

    public function remove_from_savelist_after_add_to_cart( $cart_item_key, $product_id ) {
        $request = Request::createFromGlobals();
        global $yith_wsfl_is_savelist;
        $remove_to_cart_after_save_list=$request->request->get('remove_to_cart_after_save_list');
        $add_to_cart=$request->request->get('add-to-cart');
        $variation_id = $request->request->get('variation_id');
        $fpd_product = $request->request->get('fpd_product');

        if( isset($remove_to_cart_after_save_list)) {
            $product_id = $request->request->get('remove_to_cart_after_save_list');
            //\Log::write(print_r('$remove_to_cart_after_save_list = '.$remove_to_cart_after_save_list,true));
        } elseif( !$yith_wsfl_is_savelist && isset( $add_to_cart ) ){
            $product_id = $add_to_cart;
            //$meta_data = $this->selectProductData( $product_id, $variation_id );
            //md5($fpd_product) == md5($meta_data['fpd_data']['fpd_product']);
            //\Log::write(print_r('!$yith_wsfl_is_savelist && isset( $add_to_cart ) = '.$yith_wsfl_is_savelist.'  '.$add_to_cart,true));
            //\Log::write(print_r('md5($fpd_product) == md5($meta_data) = '.md5($fpd_product).' == '.md5($meta_data['fpd_data']['fpd_product']),true));
            //\Log::write(print_r($fpd_product,true));
            //\Log::write(print_r($meta_data['fpd_data']['fpd_product'],true));
        }

        $this->remove($product_id, $variation_id);
    }

    public function selectProductData( $product_id, $variation_id=-1 ){
        if ( is_user_logged_in() ){
            global $wpdb;

            $user_id    =   get_current_user_id();

            $query  =   "SELECT meta_data
                             FROM {$wpdb->yith_wsfl_table}
                             WHERE {$wpdb->yith_wsfl_table}.product_id=%d AND {$wpdb->yith_wsfl_table}.user_id=%d";

            $parms  =   array(
                $product_id,
                $user_id
            );

            if( $variation_id!=-1 ){

                $query.=" AND {$wpdb->yith_wsfl_table}.variation_id=%d";

                $parms[]= $variation_id;
            }
            $results = $wpdb->get_var( $wpdb->prepare( $query, $parms ) );
            $results = unserialize($results);

            //\Log::write('$results = $wpdb->get_var( $wpdb->prepare( $query, $parms ) );'.print_r($results, true));

            return $results;
        }
    }

    /** remove product to savelist
     * @author YITHEMES
     * @since 1.0.0
     * @return string
     */
    public function remove($product_id, $variation_id){

        global $wpdb;

        if( is_user_logged_in() ){
            $user_id = get_current_user_id();
            $sql        =   "DELETE FROM {$wpdb->yith_wsfl_table} WHERE {$wpdb->yith_wsfl_table}.user_id=%d AND {$wpdb->yith_wsfl_table}.product_id=%d AND {$wpdb->yith_wsfl_table}.variation_id=%d ";
            $sql_parms  =   array(
                $user_id,
                $product_id,
                $variation_id
            );

            $result     =   $wpdb->query( $wpdb->prepare($sql,$sql_parms));

            if( $result )
                return "true";
            else
                return "false";
        }
        else
        {
            $savelist_cookie    =   yith_getcookie('yith_wsfl_savefor_list');

            foreach( $savelist_cookie as $key=> $item ){
                if( $item['product_id']==$product_id )
                    unset( $savelist_cookie[$key]);
            }
            yith_setcookie('yith_wsfl_savefor_list', $savelist_cookie );

            return "true";
        }
    }
}