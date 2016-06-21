<?php
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 24.04.16
 * Time: 21:17
 */

namespace Calendar\Resourses;

require_once __DIR__.'/../interfaces/Js.php';

class Popup2Js implements Js
{
    public function addJs()
    {
        if(false){
            wp_enqueue_script(
                'popup2',
                plugins_url( '/../js/popup2/jquery.popup.js', __FILE__ ),
                array( 'jquery' )
            );
        } else {
            wp_enqueue_script(
                'popup2',
                plugins_url( '/../js/popup2/jquery.popup.min.js', __FILE__ ),
                array( 'jquery' )
            );
        }
    }
}