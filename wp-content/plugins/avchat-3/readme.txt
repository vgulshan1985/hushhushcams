=== Video Chat Plugin ===
Tags: chat, video chat, chatroom, flash, flash video chat, flash chat, videochat, streaming, image, images, red5, fms, wowza, avchat, buddypress, group chat
Requires at least: 3.0.0
Tested up to: 4.1.1
Stable tag: 2.0.1
Version : 2.0.1
Contributors: stefan.avchat, naicuoctavian, radu.patron, lucian.alexandru
License: GPLv2 or later

The Video Chat Plugin Standard for WordPress handles the integration between WordPress and AVChat - a powerful web video chat solution. 

== Description ==

The Video Chat Plugin Standard handles the integration between AVChat and your WordPress website. Once installed it increases members engagement through live audio, video, text chat in public and private chat rooms, music, emoticons, YouTube videos, file sharing, etc. and can also be setup to increase web site revenues through ads, memberships or ppv.

NEW 2.0 VERSION JANUARY 2015: All new backend interface with access to the video chat, feedback form, detailed descriptions for each setting and tabbed menu for easier navigation.

 ★★★★ 2000+ clients already use AVChat on their WordPress websites ★★★★
 
 ★★★★ 5 STAR RATING on wordpress.org, check out the reviews below: ★★★★
 
 * "What a great experience, from discovering a plugin that did exactly what I needed, to a team that goes way above and beyond helping me get set up" by *mindbodyseries*
 * "Wow, I'd like to give a five-star to AVChat" by *icndream*
 * "The service is second to none" by  *pssawhney*

Check out all the WP reviews at <a href = "http://wordpress.org/support/view/plugin-reviews/avchat-3">http://wordpress.org/support/view/plugin-reviews/avchat-3</a>

<a href='http://avchat.net' onclick="window.open(this.href,"_blank");return false;" target="_blank" title='AVChat video chat software'>AVChat is a highly flexible video chat software</a> that can be installed on your website. It supports, rooms, moderators, private messages, public and private video chat, etc. . For a complete list of features check out the <a href='http://avchat.net/features' target="_blank" title='AVChat'>AVChat features page</a>. For pricing check out the <a href='http://avchat.net/buy-now' title='Buy AVChat' onclick="window.open(this.href,"_blank");return false;" target="_blank">AVChat pricing page</a>.

This plugin will take care of :

* username integration (users logged in the WordPress web site will not have to login again in the video chat )
* permissions & features for each user role (you can change them from your WordPress admin area)
* BuddyPress compatibility: BuddyPress avatars are automatically recognized and added to users profiles in the video chat (user profiles can be accessed directly from AVChat also)
* Facebook and Twitter integration
* iPhone/iPad/Android detection: the plugin will detect if the member is on a mobile device and deliver the mobile version of AVChat

Other features include :

* simple install: it installs as any other WP plugin
* compatible with SEO friendly URL's in WordPress
* customizable design: the chat looks and feel can be changed to fit with your existing WordPress theme

The above features are part of the **Standard** edition of this plugin which you can download for free from this page.

> #### The PRO version
> With the PRO version you get more than 28 new configuration options and permissions that you can customize for each user role, **multisite support**, better BuddyPress integration, a free installation and 3 months of full support and updates, and of course, our gratitude for helping us sustain this plugin and for the countless late-night hours that have been poured into it's development. 
> **<a href='http://avchat.net/integrations/wordpress' title='AVChat plugin for WordPress'>Find out more about the PRO version at http://avchat.net/integrations/wordpress</a>**.


The Video Chat Plugin Standard for WordPress is licensed under GPL v2. See the full text of the GPL v2 license in license.txt.

> #***"Our users are very happy with the chat system and we see that more and more are using it every day... outstanding email support... quick to reply and always extremely helpful...easy to setup and reliable chat system"*** -
Mike Johnsen.  

★★★TOP 5 REASONS WHY THIS IS THE BEST PLUGIN FOR YOUR WEBSITE:★★★

① Increase members engagement

② Increase time spent on site 4X

③ Increase premium ad space (below and above the chat)

④ Increase membership revenue

⑤ Hassle free video chat for your members

== Installation ==
These instructions cover the Standard version of the plugin. The installation instructions for the <a href='http://avchat.net/integrations/wordpress'>PRO version</a> are at <a href='http://avchat.net/support/wordpress'>avchat.net/support/wordpress</a>.  
  
= Part 1: Installing the Video Chat Plugin Standard =  
1. In your WordPress backend (admin area), go to **Plugins -> Add New** and search for **AVChat**. Click **Install**.  
If you prefer to install it manually, download the plugin archive from above and upload its contents to your `/wp-content/plugins` directory.  
2. **Activate it** as you would do with any other plugin.

The plugin is now installed and activated but before the chat will show up on your website we still have to add the actual AVChat video chat software, connect it to a media server and place it in pages and blog posts. Let’s do it following the steps below!
  
= Part 2: Installing AVChat 3.5 Software (the actual video chat software) =  
You will need the AVChat software and a license key (trial or purchased). You can purchase AVChat from <a href='http://avchat.net/buy-now'>avchat.net/buy-now</a> but you can also get a <a href='http://avchat.net/15daystrial/'>15 days free trial</a>. After the order is made or a trial requested, you will receive an email with a link to your private client area from where you can download the AVChat software.  

1. **Download AVChat Software** from your private client area.  
2. **Unzip and upload** the contents of `Files to upload to your web site` to your new `/wp-content/plugins/avchat-3` folder. Don’t worry, no folder or file will be overwritten!  
  
= Part 3: Setting up the Media Server (Red5) =  
AVChat uses a media server to send audio, video and text between users. AVChat supports the top three media servers: Red5 (free and open source, version 0.8 and 1.0RC1), Wowza (commercial, $55/month, free trial) and AMS from Adobe (commercial, $4500, free trial).

To install any media server you need a cloud/VPS server with root ssh access. At DigitalOcean.com, VPS servers start at $5/month, use <a href='https://www.digitalocean.com/?refcode=cd50d47eef55'>this link</a> to get a $10 credit (2 months free). 

**Installing Red5 1.0.5 on your own VPS:**

1. Download Red5 1.0.5 for your platform from <a href='https://github.com/Red5/red5-server/releases/tag/v1.0.5-RELEASE' target='_blank'>GitHub</a> or <a href="https://mega.co.nz/#!cAVGCJJR!flZqMKTB9mcD1_nvbQ_xlG88ADTEkFk7-jtuGw2uNOc" target="_blank">Mega.co.nz</a>.   
2. If your cloud/VPS server runs Linux, unzip the Red5 archive and upload its contents to `/opt/red5`. If it runs Windows, install using the `.exe` file. 
3. The AVChat archive downloaded in **Part 2** contains a `Files to upload to your media server (Red5)` folder. You’ll need to upload the `avchat30` folder inside it to `red5/webapps`. Your final folder tree should look like this: `red5/webapps/avchat30`.  
4. Start Red5 by running `./start.sh` on Linux terminal/shell or run `start.bat` on Windows. You will find these files in the main `red5` folder.  

AVChat will use a **connection string** to know the media server it needs to connect to. Yours will be **rtmp://my-media-server.com/avchat30/\_definst\_** where **my-media-server.com** is the server’s domain name or IP address. 
  
= Part 4: Finalizing installation =  

  
1. Go to **Settings** -> **AVChat 3 Video Chat** in your WordPress backend.
1. In the **Connection string** field insert the connection string from **Part 3** and click [Update Options] at the bottom.  
2. Now, to place the chat on your website, add the **[chat]** short code anywhere in your WordPress **pages** or **posts**. Visiting these pages or posts will now bring up the AVChat 3 Video Chat.  
3.  The first time you’ll login, you’ll be asked for a license key. You can find it in the **private client area** mentioned in **Part 2**.  
  
That’s it! If you need any help, we’re there for you. <a href='https://wordpress.org/support/plugin/avchat-3'>Ask us on Wordpress.org</a> or <a href='http://nusofthq.com/forum/index.php?/forum/10-avchat-3-integrations-joomla-social-engine-wordpress-drupal-phpfox-etc/'>post in our forum</a>.
  

== Frequently Asked Questions ==

= What do I need to use this plugin ? =

A WordPress web site, AVChat 3 from http://avchat.net/ and a media server.

= Why do I need a media server ? =

AVChat - and all video chat web apps - uses a media server to send real time audio video and text between users.

= What media servers does AVChat support ? =

Red5, AMS Pro and Wowza. For a complete list of supported versions please visit http://avchat.net/support/requirements#fms .
Red5 is free and open source: http://code.google.com/p/red5/
Wowza: http://www.wowza.com/
Adobe Media Server Pro: http://www.adobe.com/products/adobe-media-server-professional.html
We also provide VPS servers with Red5 pre-installed and configured at http://avchat.net/hosting

= What if I need help ? =

1. Post a question in the Support area of this page: http://wordpress.org/support/plugin/avchat-3
2. Post a question in our official support forum: http://nusofthq.com/forum/index.php?/forum/3-avchat-3-httpavchatnet/ (free account needed).

AVChat customers also receive dedicated tech support by email.

== Screenshots ==
  
1. Video Chat Plugin Standard for WordPress
  
== Changelog ==

= 2.0.1 (09.03.2015) =
* Corrected small typos
* Prepared plugin for the new AVChat 3.6 release
* Updated links and description accordingly

= 2.0.0 (19.01.2015) =
* The plugin now has it's own menu in the WP backend (previous: inside Settings)
* Interface redesign for the plugin's backend area
* Plugin's settings have now been reorganized in a tabbed menu
* All options are now explained in detail, many with examples
* Fixed small bugs and typos
* Included a feedback form where you can send your questions or suggestions
* You can now access AVChat's admin area from the WP backend

= 1.4.3 (05.09.2014) =
* Fixed issue with only admins getting access to the chat by default (and only to the user interface) when 1st installing the plugin. Now all user roles have access by default to the chat user interface and the admin to the chat admin interface.
* Changed wording from "You do not have sufficient privileges to access this page" to a softer "USER_ROLE can not access the chat"
* Upped the Flash Player requirement in the JS code to 11.4 (AVChat Build 3396 needs it)
* Updated installation instructions and description
* Updated supported WordPress version to 4.0
* Replaced "AVChat Software" mentions with "avchat.net"
* Removed all AVChat trial mentions from description since at the moment AVChat can not be obtained under a 15 days trial anymore.

= 1.4.2 (14.01.2014) =
* Fixed security issue

= 1.4.1 (28.10.2013) = 
* Fixed issue with tablets not receiving the mobile version *
* Fixed issue with permissions not loading correctly in some cases *
* Fixed issue with "Headers already sent" error when using AVChat in pop up *
* Visitors and logged in members can not change their chat username (visitors get a random visitor_XYZ username) *
* Admins do not get 99 maxRoomsOneCanBeIn, 99 maxStreams and unlimited freeVideoTime anymore. All their abilities are controlled from the WP backend *

= 1.4.0 (10.05.2013) = 
* Updated plugin description in WP Plugins backend area
* Added FP 11.1 requirement to match the FP requirement in the recent 2330 AVChat 3 build: http://avchathq.com/blog/avchat-build-2330-introduces-h-264-support/
* Better detection for missing JavaScript and Flash Player version
* Better AVChat 3 files detection
* Better mobile device detection
* FB application ID is now sent to AVChat 3 even when the plugin is configured to open AVChat 3 in a pop-up
* Removed default FB app id
* Better JS code for opening up AVChat 3 in an pop-up window
* WP Administrators are now granted access by default, through a pre-checked permission, to the admin area of AVChat 3 (admin.swf) 
* Fixed issue that made it impossible to deny WP Administrators access to the admin area of AVChat 3 (admin.swf)
* Fixed issue that prevented the permissions from being applied to WP Administrators
* Default visitors usernames will now have the "visitor_" prefix instead of the "user_" prefix
* The admin column was not present in the AVChat 3 Settings & Permissions page
* Better explanation of AVChat 3 permissions and options in the AVChat 3 Settings & Permissions page

= 1.3.3 (11.04.2013) =
* fixed some minor typos including removing the "PRO" from the plugin name as it shows up in the WP backend

= 1.3.2 (06.03.2013) =

= 1.3.1 (21.02.2013) =

= 1.3 (17.12.2012) =
* *NEW!*     HTML5 mobile version, now available for iOS and Android. Automatic recognition of mobile version.
* Added support for Administrators user role.
* Don't miss new messages. Now you will see if you have notifications in the browser tab, while browsing other tabs.
* Fixed issue with chat appearing lower on the page.

= 1.2.1 (29.11.2012) =
* Added support to know when the AVChat files are not copied into the plugin directory.
* Fixed bug with javascripts missing.
* Removed padding in AVChat Settings in WordPress backend.
* More explicit texts in AVChat Settings in WordPress backend.
 
= 1.2 (27.11.2012) =
* Added Facebook integration.
* Added iPad detection.
* Added new "Visitors" column to better control what a visitor can have access to.
* Added lots of new features in WordPress backend.
* Now the changes made to the background are made from the style.css and not from the WordPress backend settings.
* Fixed the bug where guests can access the admin area of AVChat.
* Fixed the bug where the added user roles were not recognized by the AVChat and were not saved the changes made in WordPress backend.
 
= 1.1 (29.10.2012) =
* Fixed bug with "session already sent".
* Fixed the bug where IE didn't recognize the path to the video chat and couldn't log in.
* Updated the documentation.
 
= 1.0 (12.06.2012) =
* First release in WordPress plugin directory.