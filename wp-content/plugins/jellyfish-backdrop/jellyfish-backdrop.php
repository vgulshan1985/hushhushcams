<?php
/*
  Plugin Name: Jellyfish Backdrop
  Plugin URI: http://strawberryjellyfish.com/wordpress-plugins/jellyfish-backdrop/
  Description: Add fullscreen background images and background slideshows to any WordPress page element.
  Author: Robert Miller <rob@strawberryjellyfish.com>
  Version: 0.6.8
  Author URI: http://strawberryjellyfish.com/
*/

/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
Online: http://www.gnu.org/licenses/gpl.txt
*/
?>
<?php
if ( !class_exists( 'Jellyfish_Backdrop' ) ) {
  class Jellyfish_Backdrop {
    public function __construct() {
      if (!defined('JELLYFISH_BACKDROP_VERSION_NUM'))
        define('JELLYFISH_BACKDROP_VERSION_NUM', '0.6.8');

      update_option('jellyfish_backdrop_version', JELLYFISH_BACKDROP_VERSION_NUM);

      // Set up admin pages
      require_once(sprintf("%s/admin.php", dirname(__FILE__)));
      $jellyfish_backdrop_admin = new Jellyfish_Backdrop_Admin();

      if ( !is_admin() ) {
        // enqueue slideshow scripts on front end only
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
      }
    }

    public static function activate() {
      // Do nothing (yet)
    }

    public static function deactivate() {
      // Do nothing (yet)
    }

    function enqueue_script() {

      // only include js where actually needed
      $options = get_option( 'jellyfish_backdrop' );
      $current_post_type = get_post_type( get_the_ID() );
      $js_needed = false;

      if ( $options['show_default'] == true ) {
        // we always need to print scripts when show_default is enabled
        $js_needed = true;

      } elseif ($current_post_type) {
        if ( (array_key_exists($current_post_type, (array)$options['use_postmeta']) &&  $options['use_postmeta'][$current_post_type] == true )
          && ( is_single() or is_page() )
          && ( get_post_meta( get_the_ID(), '_jellyfish_backdrop_images', true ) ) ) {
          $js_needed = true;
        }
      }

      if ( $js_needed ) {
        // enqueue jQuery library and js to page footer
        wp_register_script( 'backstretch', plugins_url( 'js/jquery.backstretch.min.js' , __FILE__ ), array( 'jquery' ), '', true );
        wp_enqueue_script( 'backstretch' );
        add_action( 'wp_footer', array( $this, 'print_script' ), 100 );
      }
    }

    function print_script() {
      // Print out backdrop JavaScript
      global $wpdb, $post_id;
      $options = get_option( 'jellyfish_backdrop' );

      $fade_speed = is_numeric( $options['fade_speed'] ) ? $options['fade_speed'] : 0.5;

      $slide_duration = is_numeric( $options['slide_duration'] ) ? $options['slide_duration'] : 5;

      $container = isset($options['container']) ? $options['container'] : 'body';

      $image_array = array();

      $current_post_type = get_post_type( get_the_ID() );

      if ( (array_key_exists($current_post_type, (array)$options['use_postmeta'])
        && $options['use_postmeta'][$current_post_type] == true )
        && ( is_single() or is_page() ) ) {

        // look at post meta for image and slideshow data (override default)
        $images = get_post_meta( get_the_ID(), '_jellyfish_backdrop_images', true );
        if (is_array($images)) {
          foreach ( $images as $arr ) {
            array_push( $image_array, $arr['_jellyfish_backdrop_image']['url'] );
          }
        }

        if ( !empty( $image_array ) ) {
          $post_container = get_post_meta( get_the_ID(), '_jellyfish_backdrop_container', true );
          if ( $post_container ) $container = $post_container;

          $post_fade_speed = get_post_meta( get_the_ID(), '_jellyfish_backdrop_fade_speed', true );
          if ( $post_fade_speed ) $fade_speed = $post_fade_speed;

          $post_slide_duration = get_post_meta( get_the_ID(), '_jellyfish_backdrop_slide_duration', true );
          if ( $post_slide_duration ) $slide_duration = $post_slide_duration;
        }
      }

      // seconds to milliseconds
      $fade_speed *= 1000;
      $slide_duration *= 1000;

      if ( ( $options['show_default'] == true ) && isset($options['url']) && empty( $image_array ) ) {
        // only use the default if it's selected and we don't have any post images
        array_push( $image_array, $options['url'] );
      }

      // print out the javascript if we have some images
      if ( !empty( $image_array ) ) {
        echo "<script>jQuery(document).ready(function($) { var sjb_backgrounds = " . json_encode( $image_array ) . "; $('$container').backstretch(sjb_backgrounds,{speed: $fade_speed, duration: $slide_duration}); });</script>";
      }
    }
  }
}

if ( class_exists( 'Jellyfish_Backdrop' ) ) {
  register_activation_hook( __FILE__, array( 'Jellyfish_Backdrop', 'activate' ) );
  register_deactivation_hook( __FILE__, array( 'Jellyfish_Backdrop', 'deactivate' ) );
  $jellyfish_backdrop = new Jellyfish_Backdrop();
}
?>