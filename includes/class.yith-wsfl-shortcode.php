<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly


if( !class_exists( 'YITH_WSFL_Shortcode') ){

    class YITH_WSFL_Shortcode {

        public static function saveforlater( $atts, $content=null ){
            global $yith_wsfl_is_savelist;
            global $YIT_Save_For_Later;
            $default_attr   =   array(
                'per_page'      =>  10,
                'pagination'    =>  'no',
            );

           $atts    =   shortcode_atts( $default_attr, $atts );
           extract( $atts );

           $items  =   $YIT_Save_For_Later->get_savelist_by_user();

           $is_wishlist_install =   defined( 'YITH_WCWL' )  ?   'yes'   :   'no';

           $extra_attr  =   array(
               'show_add_to_wishlist'   =>  $is_wishlist_install,
               'savelist_items'         =>  $items,
               'title_list'            =>  __('Saved for later ', 'yith-woocommerce-save-for-later'),
               'template_part'          =>  'view',
               'current_page'           =>  1
           );

            $atts = array_merge(
                $atts,
                $extra_attr
            );

            // adds attributes list to params to extract in template, so it can be passed through a new get_template()
            $atts['atts'] = $atts;
            $yith_wsfl_is_savelist=true;

            $template =  yit_plugin_get_template(YWSFL_DIR, 'saveforlater.php', $atts, true );

            $yith_wsfl_is_savelist=false;
            return apply_filters( 'yith_wsfl_saveforlater_html', $template, array(), true );

        }

    }
}

add_shortcode( 'yith_wsfl_saveforlater', array( 'YITH_WSFL_Shortcode', 'saveforlater' ) );