<?php
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 24.04.16
 * Time: 21:08
 */
namespace Calendar\Resourses;

require_once 'ComponentFactory.php';
require_once __DIR__.'/../impl/Popup2Css.php';
require_once __DIR__.'/../impl/Popup2Js.php';

class Popup2Factory implements ComponentFactory
{

    function getCss()
    {
        return new Popup2Css();
    }

    function getJs()
    {
        return new Popup2Js();
    }
}