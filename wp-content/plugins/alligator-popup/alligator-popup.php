<?php
/*
Plugin Name: Alligator Popup
Plugin URI: http://cubecolour.co.uk/alligator-popup
Description: Shortcode to open a link inside a popup browser window
Author: cubecolour
Version: 1.2.0
Author URI: http://cubecolour.co.uk/
License: GPLv3

  Copyright 2013-2014 Michael Atkins

  Licenced under the GNU GPL:

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


// ==============================================
//  Prevent Direct Access of this file
// ==============================================

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if this file is accessed directly

// ==============================================
//	Get Plugin Version
// ==============================================

function cc_popup_plugin_version() {
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file = basename( ( __FILE__ ) );
	return $plugin_folder[$plugin_file]['Version'];
}

// ==============================================
//	Add Links in Plugins Table
// ==============================================
 
add_filter( 'plugin_row_meta', 'cc_popup_meta_links', 10, 2 );
function cc_popup_meta_links( $links, $file ) {

	$plugin = plugin_basename(__FILE__);
	
// create the links
	if ( $file == $plugin ) {
		
		$supportlink = 'https://wordpress.org/support/plugin/alligator-popup';
		$donatelink = 'http://cubecolour.co.uk/wp';
		$reviewlink = 'https://wordpress.org/support/view/plugin-reviews/alligator-popup?rate=5#postform';
		$iconstyle = 'style="-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;font-size: 14px;margin: 4px 0 -4px;"';
		$twitterlink = 'http://twitter.com/cubecolour';
		
		return array_merge( $links, array(
			'<a href="' . $donatelink . '"><span class="dashicons dashicons-heart"' . $iconstyle . 'title="Donate"></span></a>',
			'<a href="' . $supportlink . '"> <span class="dashicons dashicons-lightbulb" ' . $iconstyle . 'title="Support"></span></a>',
			'<a href="' . $twitterlink . '"><span class="dashicons dashicons-twitter" ' . $iconstyle . 'title="Cubecolour on Twitter"></span></a>',
			'<a href="' . $reviewlink . '"><span class="dashicons dashicons-star-filled"' . $iconstyle . 'title="Review"></span></a>'
		) );
	}
	
	return $links;
}

// ==============================================
// Register and Enqueue popup script
// ==============================================

function cc_popup_script() {
	wp_register_script( 'popup', plugins_url() . "/" . basename(dirname(__FILE__)) . '/js/popup.js', array('jquery'), cc_popup_plugin_version(), false );
	wp_enqueue_script( 'popup' );
 }
 
add_action('wp_enqueue_scripts', 'cc_popup_script');

// ==============================================
// shortcode to add a link with the 'popup' class with data attributes for height, width and scrollbars
// ==============================================

function cc_popup_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'url' => '#',
		'width' => '400',
		'height' => '400',
		'scrollbars' => 'yes',
		'alt' => ''
	), $atts ) );
	
	$showscrollbars = esc_attr( $scrollbars );
	
	if (strtolower( $showscrollbars ) == 'no') {
		$showscrollbars = '0'; 
	}
	
	if ($showscrollbars != '0') {
		$showscrollbars = '1'; 
	}
	
	return '<a href="' . esc_url( $url ) . '" class="popup" data-width="' . absint( $width ) . '" data-height="' . absint( $height ) . '" data-scrollbars="' . $showscrollbars . '" alt="' . esc_attr( $alt ) . '">' . $content . '</a>';
}

add_shortcode( 'popup', 'cc_popup_shortcode' );
