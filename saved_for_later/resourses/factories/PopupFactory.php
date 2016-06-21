<?php
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 24.04.16
 * Time: 21:08
 */
namespace Calendar\Resourses;

require_once 'ComponentFactory.php';
require_once __DIR__.'/../impl/PopupCss.php';
require_once __DIR__.'/../impl/PopupJs.php';

class PopupFactory implements ComponentFactory
{

    function getCss()
    {
        return new PopupCss();
    }

    function getJs()
    {
        return new PopupJs();
    }
}