<?php
/*
Plugin Name: Simple Full Screen Background Image
Description: Easily set an automatically scaled full-screen background images
Version: 1.1
Author: Pippin Williamson
Author URI: http://pippinsplugins.com
*/

/*****************************************
* global
*****************************************/

$sfsb_options = get_option('fsb_settings');

/*****************************************
* includes
*****************************************/
include('includes/admin-page.php');
include('includes/display-image.php');
include('includes/scripts.php');
