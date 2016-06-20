<?php
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 05.04.16
 * Time: 15:29
 */

namespace ListProducts\Request;
use Katzgrau\KLogger\Logger;
use Symfony\Component\HttpFoundation\Request;

class Ajax
{
    public function __construct(){
        //add_action( 'wp_ajax_add_to_cart_variable_product', array( $this, 'add_to_cart_variable_product' ) );
        //add_action( 'wp_ajax_nopriv_add_to_cart_variable_product', array( $this, 'add_to_cart_variable_product' ) );
        add_action( 'wp_ajax_add_to_saveforlater', array( $this, 'add_to_saveforlater_ajax' ) );
        add_action( 'wp_ajax_nopriv_add_to_saveforlater', array( $this, 'add_to_saveforlater_ajax' ) );
    }

    /**call ajax for add a new product in savelist
     * @author YITHEMES
     * @since 1.0.0
     */
    public function add_to_saveforlater_ajax(){
        //*
        $logger = new Logger(YWSFL_DIR.'/logs/add-to-saveforlater');
        $logger->info('add_to_saveforlater_ajax', [
            '$_POST'=>$_POST,
        ]);
        //*/
        $request = Request::createFromGlobals();
        $save_for_later = $request->request->get('save_for_later');
        $remove_item = $request->request->get('remove_item');
        $user_id = get_current_user_id();
        $product_id  =   isset($save_for_later) ? $save_for_later    :   -1;
        $variation_id = $request->request->get('variation_id');
        $quantity = $request->request->get('quantity');


        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        $return = false;
        foreach($items as $item => $values) {
            if(
                $remove_item == $item &&
                $product_id == $values['product_id'] &&
                $variation_id == $values['variation_id']
            ){
                $meta_data = serialize($values);

                $user_id    =   isset( $user_id )?$user_id:-1;
                $quantity   =   isset( $quantity )?$quantity:1;
                $variation_id   =   isset( $variation_id ) ? $variation_id: -1;

                $return = $this->add($user_id, $product_id, $quantity, $variation_id, $meta_data);

                //*
                $logger->info('add_to_saveforlater_ajax', [
                    '$user_id'=>$user_id,
                    '$variation_id'=>$variation_id,
                    '$quantity' => $quantity,
                ]);
                //*/
            }
        }

        $message = '';
        if( $return == 'true' ){
            $message = __('Product added', 'yith-woocommerce-save-for-later');
        }
        elseif( $return == 'exists' ){
            $message = __('Product already in Save for later', 'yith-woocommerce-save-for-later') ;
        }elseif( $return == 'error' ){
            $message = __('Error', 'yith-woocommerce-save-for-later') ;
        }

        wp_send_json(
            array(
                'result' => $return,
                'message' => $message,
                'template'  => \YITH_WSFL_Shortcode::saveforlater( array())
            )
        );
    }

    public function add($user_id, $product_id, $quantity, $variation_id, $meta_data){
        global $wpdb;

        if( $product_id==-1 )
            return "error";

        //if( $this->is_product_in_savelist( $product_id, $variation_id ) )
        //  return "exists";

        if( $user_id!=-1 ){

            $args   =   array(
                'product_id'    =>  $product_id,
                'user_id'       =>  $user_id,
                'quantity'      =>  $quantity,
                'variation_id'  =>  $variation_id,
                'meta_data'     => $meta_data,
                'date_added'    =>  date( 'Y-m-d H:i:s' )
            );

            $res    =   $wpdb->insert( YITH_WSFL_Install()->_table_name, $args );

        }
        else
        {
            $cookie =   array(
                'product_id'    =>  $product_id,
                'quantity'      =>  $quantity,
                'variation_id'  =>  $variation_id
            );

            $savelist_cookie    =   yith_getcookie('yith_wsfl_savefor_list');

            $savelist_cookie[]=$cookie;

            yith_setcookie( 'yith_wsfl_savefor_list', $savelist_cookie );

            $res    =   true;
        }

        if( $res ) {

            return "true";
        }
        else
        {
            return "error";
        }

    }

    /**check if a product is in savelist
     * @author YITHEMES
     * @since 1.0.0
     * @param $product_id
     * @return bool
     */
    public function is_product_in_savelist( $product_id, $variation_id=-1 ){
        $exist =   false;

        if ( is_user_logged_in() ){
            global $wpdb;

            $user_id    =   get_current_user_id();

            $query  =   "SELECT COUNT(*) as cnt
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

            return (bool) ( $results > 0 );
        }
        else
        {
            $cookie =   yith_getcookie('yith_wsfl_savefor_list');

            foreach( $cookie as $key=>$item ){
                if( $item['product_id']==$product_id )
                    $exist  =   true;
            }
            return $exist;
        }

    }

    public function add_to_cart_variable_product() {
        $request = Request::createFromGlobals();
        $product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $request->request->get('product_id')));
        $quantity = empty( $request->request->get('quantity')) ? 1 : apply_filters( 'woocommerce_stock_amount', $request->request->get('quantity') );
        $variation_id = $request->request->get('variation_id');
        $variation  = $request->request->get('variation');
        WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation );
        var_dump( $product_id, $quantity, $variation_id, $variation );
        die;

    }
}