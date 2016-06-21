<?php
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 05.04.16
 * Time: 11:44
 */

namespace Calendar\Resourses;

interface ComponentFactory
{
    function getCss();
    function getJs();
}