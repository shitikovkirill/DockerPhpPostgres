<?php

/*
Plugin Name: Saved For Later
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: kirill
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

use SaveList\Plugin;
require_once( 'vendor/autoload.php');
require_once( 'Plugin.php');
require_once( 'hook/Filter.php');
require_once( 'hook/Filter_add_to_cart_validation.php');
require_once( 'hook/Action.php');
require_once( 'request/Ajax.php');
require_once( 'resourses/ResourseFactory.php');

new Plugin();