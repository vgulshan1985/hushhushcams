=== Jellyfish backdrop ===
Contributors: toxicToad
Author URI: http://strawberryjellyfish.com/
Donate link: http://strawberryjellyfish.com/donate/
Plugin URI: http://strawberryjellyfish.com/wordpress-plugin-jellyfish-backdrop/
Tags: background, fullscreen, gallery, slideshow, image
Requires at least: 3.5
Tested up to: 4.1
Stable tag: 0.6.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Fullscreen background images and background slideshows on any WordPress
post or page. Easily upload and select images using the media library.


== Description ==

Easily create full screen image backgrounds and fading slideshows that stretch
and adapt to the size of your page.

You can either use it as a global "cover" style background that will be
displayed on all pages and posts of your WordPress website or you can give
individual posts or pages including custom post types their own specific
background – great for giving parts of a website a whole different look.

Jellyfish Backdrop makes fullscreen background slideshows easy by allowing
you to assign multiple backgrounds to any page or post.

* Unlimited number of post and slideshows.
* Create different slideshows or backgrounds or different areas of your site.
* Easy to use admin panels with sortable images and media library integration.
* Display images in any page element allowing slideshows anywhere, not just as
a background!

= Demo =

See the plugin homepage for demos and full details:
http://strawberryjellyfish.com/wordpress-plugins/jellyfish-backdrop/


== Installation ==

Either install and activate the plugin via your WordPress Admin

Or

Extract the zip file and just drop the contents in the wp-content/plugins/
directory of your WordPress installation and then activate the Plugin from
Plugins page.

After the plugin is activated you'll find a new Backdrop Slideshow settings
page under the Settings menu of your WordPress admin. Here you can configure
the global options.


== Usage ==

Visit the settings page to set up the global and default options. Here you'll
be able to set up a default background image that will be displayed on every
page of your blog, enable or disable the global background or individual post
and page background support as well as set up the default options.

To add a unique background image or slideshow to a specific post or page,
first make sure that in the *Enable Page and Post Type Slideshows* section
on the setting page, you check any post types you want to allow slideshows
to appear on. You'll now find a new area on the post / page edit screens
where you can add images to the Backdrop Slideshow and alter fade time,
slide display time and where the images will appear. Jellyfish Backdrop uses
the WordPress image uploader and Media Library, so it's easy to choose the
images you want to use. You can also reorder the images by dragging.
Once you've finished adding and arranging your images, be sure to save the
post or your changes will not be saved.

Page / Post backgrounds will override the default background so it's possible
to define one background to be used site wide and another on a specific page.

By default images are shown as the main page background (body), however you
can make the images appear as backgrounds to other areas of the page by
supplying any valid element id or class name in the Container field on the
admin page. eg. #main, .header


== Frequently Asked Questions ==

= Why does the slideshow appear under my page content? =
This is what it's primarily intended to do, how this appears on your site
depends on the theme you are using. It works best on theme pages with large
areas of little content.

= Can I use this as a normal slideshow? =
Yes, create a div element or other suitable container on your page and configure
your slideshow to use the element's id or class. Make sure you've styled the
element to give it a suitable size.

= Can I have a default slideshow that shows on every page? =
No, slideshows must be defined on an individual post or page basis.


== Screenshots ==

1. Easily set up images and slideshows using the new admin panel on post and page edit screens.

2. Settings page, to set up default options.

== Changelog ==

= 0.6.8 =
* Bugfix: Addresses "Invalid argument supplied for foreach()" warning when
  post/page images were not defined

= 0.6.7 =
* Bugfix: Global settings were being overridden by post type settings when
  there were no images. Now correctly uses global settings for container,
  duration etc.

= 0.6.6 =
* Bugfix: Fixed error caused by missing settings introduced in 0.6.5.
  (Plugin was broken for new installs, thanks to peckjon for pointing this out)

= 0.6.5 =
* Added ability to select which post types to allow slideshows on,
including custom post-types.

= 0.6.1 =
* Fixed bug saving slider values (slide duration and fade speed)

= 0.6 =
* First release via WordPress.org
* Easy to use admin panel on post / page editor to create slideshows
* Now uploads and add images through the WordPress Medial Library
* Refactored the whole plugin into classes.

= 0.5 =
* Added container element option
* Code cleanups
* Tested up to WordPress 4.0

= 0.4 =
* changed JavaScript enqueue so it ONLY shows up when required
* updated to latest  jQuery.backstretch.min.js  - v2.0.4

= 0.3 =
* fixed incorrectly queued JavaScript. Changed form input validation method and
  reworked check box handling to clean up some undefined index warnings

= 0.2 =
* Initial Release.


== Upgrade Notice ==

Big changes everywhere in the plugin, if you were using a pre Wordpress.org (0.6)
release version your existing images and slideshows will need reconfiguring.
