<?php
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 24.04.16
 * Time: 21:11
 */

namespace Calendar\Resourses;

require_once __DIR__.'/../interfaces/Css.php';

class Popup2Css implements Css
{
    public function addCss()
    {
            wp_enqueue_style (
                'popup2',
                plugins_url('/../css/popup2/popup.css', __FILE__)
            );
    }
}