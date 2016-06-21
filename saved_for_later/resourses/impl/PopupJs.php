<?php
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 24.04.16
 * Time: 21:17
 */

namespace Calendar\Resourses;

require_once __DIR__.'/../interfaces/Js.php';

class PopupJs implements Js
{
    public function addJs()
    {
        if(false){
            wp_enqueue_script(
                'popup',
                plugins_url( '/../js/popup/jquery.magnific-popup.js', __FILE__ ),
                array( 'jquery' )
            );
        } else {
            wp_enqueue_script(
                'popup',
                plugins_url( '/../js/popup/jquery.magnific-popup.min.js', __FILE__ ),
                array( 'jquery' )
            );
        }
    }
}