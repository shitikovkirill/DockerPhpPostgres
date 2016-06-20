<?php
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 21.04.16
 * Time: 8:47
 */

namespace SaveList;

use ListProducts\Request\Ajax;
use SaveList\Hook\Action;
use SaveList\Hook\Filter;
use SaveList\Hook\Filter_add_to_cart_validation;

class Plugin
{
    public function __construct(){
        new Ajax();
        new Filter_add_to_cart_validation();
        add_action( 'wp_enqueue_scripts', [$this, 'addJsAndCss'] );
    }

    public function addJsAndCss(){
        $factory = \Calendar\Resourses\ResourseFactory::singleton();
        $popup2 = $factory->getFactory('Popup2');
        $popup2 ->getCss()->addCss();
        $popup2 ->getJs()->addJs();
    }

}