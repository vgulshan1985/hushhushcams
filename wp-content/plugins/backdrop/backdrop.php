<?php
/*
Plugin Name: Backdrop
Plugin URI: http://www.fatfolderdesign.com/wp-plugins/backdrop/
Description: Backdrop is an improved site background customizer allowing for all manner of fancy things.
Version: 1.3.6
Author: Phillip Gooch
Author URI: mailto:phillip@pgiauto.com
License: GNU General Public License v2
*/

class backdrop {
	public $version = '1.3.4'; // Update this string every version
	public $option_name = 'Backdrop Settings';
	public $generated_location = '';

	public function __construct(){
		// I but your just _hooked_ on learning how this all works.
		add_action('admin_menu',array(&$this,'addMenu'));
		add_action('admin_enqueue_scripts',array(&$this,'subtractMenu'));
		add_action('wp_footer',array(&$this,'funkyFoot'));
		add_action('wp_print_styles',array(&$this,'hipsterStyle'));
		add_action('wp_enqueue_scripts',array(&$this,'shakespeareanScript'));
		// For the media uploader
		add_action('admin_print_scripts',array(&$this,'uploaderScripts'));
		add_action('admin_print_styles',array(&$this,'uploaderStyles'));
		add_filter('attachment_fields_to_edit',array(&$this,'cleanupUploader'),1000000);
		add_filter('media_upload_tabs',array(&$this,'cleanupUploaderAgain'),1000000);
	}
	static function __activate(){
		$that = new backdrop(); // If you want to call any class functions (and we do) you need to create a new instance
		// Runs on plugin activation/update, uses version numbers stored in settings to determine if it actually has to do anything.
		$settings = $that->getSettings();
		if($settings['version']!=$that->version){
			$defaults = array(
				'version'=>$that->version,
				'show_default'=>'no',
				'needs_js'=>'no',
				'needs_foot'=>'no',
				'z-index'=>-1,
				'background_type'=>'color',
				'backdrop'=>array(
					'color'=>array(
						'color'=>'transparent', // The default color is "transparent"
					),
					'image'=>array(
						'img'=>'',
						'attachment'=>'scroll', // The default is same as the WordPress default (I think)
						'color'=>'#FFFFFF',
						'hpos'=>'left',
						'vpos'=>'top',
						'repeat'=>'xy',
						'retinize'=>'no',
						'parallax_adjustment'=>'25',
						'slide'=>'right',
						'slide_speed'=>'100',
					),
					'css'=>array(
						'css'=>'', // Were going to start with a clean slate
						'application'=>'body',
					),
				),
				'last_updated'=>0,
			);// Thats nothing, you should see the (not nearly as well formed) default array on pgi-inventory-plugin (or the DB create for that matter)
			// This takes care of adding new items to the settings array
			$settings = array_merge($defaults,$settings);
			// And this makes sure the version number always gets updated (or else it'll just keep doing this)
			$settings['version'] = $that->version;
			// And now we can update...
			$that->updateSettings($settings);
		}
	}

	// Heres where all of Backdrop's real functionality is
	public function addMenu(){
		// Lets add a new (sub) menu item;
		add_submenu_page('themes.php','Backdrop','Backdrop','edit_theme_options','backdrop',array(&$this,'settingsPage'));
	}
	public function settingsPage(){
		// Loads the other page, it's much simpler this way.
		require_once('backdrop-settings.php');
	}
	public function subtractMenu(){
		// Grr, really WordPress, really?
		$settings = $this->getSettings();
		if($settings['show_default']!='yes'){
			echo '
			<script>
			/* Did your know that background doesnt show up in the $submenu global?
			   Makes removal a bit more bothersom. :(
			   I\'m, really sorry about doing it this way but I can\' think of a better way.
			   If you know of a way let me know, and I\'ll forever refer to you as the Wordpress Administrative Menu Master (WAMM!) */
			window.onload=function(){
				jQuery("li#menu-appearance ul li a:contains(\'Background\')").parent().hide();
			}
			</script>
			';
		}
	}
	public function funkyFoot(){
		// Yo man wash that thing, it stanks!
		$settings = $this->getSettings();
		if($settings['needs_foot']=='yes'){
			echo '<div id="backdrop_funky_foot"></div>';
		}
	}
	public function hipsterStyle(){
		// Seattle hipsters don't drink Starbucks - to mainstream.
		$settings = $this->getSettings();
		// I'd check to see if we _need_ the css, but we always do.
		wp_enqueue_style('Backdrop hipsterStyle',get_bloginfo('wpurl').'/wp-content/backdrop/generated.css?time='.$settings['last_updated'],array(),false,'all');
	}
	public function shakespeareanScript(){
		// Okay, thats probably overplaying it a bit.
		$settings = $this->getSettings();
		if($settings['needs_js']=='yes'){
			wp_enqueue_script('backdrop_shakespeareanScript',get_bloginfo('wpurl').'/wp-content/backdrop/generated.js?time='.$settings['last_updated'],array(),false,'all');
		}
	}
	// The following functiosn are to get the WordPress media upload installed and activated
	public function uploaderScripts(){
		// The uploader needs two scripts, one for the fancy box, and one for the uploader itself
		wp_enqueue_script('thinkbox');\
		wp_enqueue_script('media-upload');
	}
	public function backdrop_uploader_styles(){
		wp_enqueue_style('thickbox');
	}
	public function uploaderStyles(){
		// The uploader only needs one style
		wp_enqueue_style('thickbox');
	}
	public function cleanupUploader($f){ // $f it all the fields the uplaoder filter provies, and were going to remvoe them all
		$f['post_title']['input']='hidden';
		$f['image_alt']['input']='hidden';
		$f['post_excerpt']['input']='hidden';
		$f['post_content']['input']='hidden';
		$f['url']['input']='hidden';
		$f['menu_order']['input']='hidden';
		$f['image_url']['input']='hidden';
		$f['image_description']['input']='hidden';
		$f['align']['input']='hidden';
		$f['url']['input']='hidden';
		// Alas to my dismay there is no way to modify the insert button, or to remove the link to make it the featured image.
		return $f;
	}
	public function cleanupUploaderAgain($t){ // $t is all the uploader tabs, were going to remove the gallery as it's of no use to us.
		unset($t['gallery']);
		return $t;
	}

	// The following 3 functions handle getting, updating, and saving the options.
	public  function getSettings(){
		$no_settings = '{"version":"0.0.0"}'; // Since we know there will never be a version 0.0.0 we can safly use it to check if there are any settings saved
		$settings=get_option($this->option_name,$no_settings);
		if($settings==''){$settings=$no_settings;}
		return json_decode($settings,true);
	}
	public function updateSettings($new,$update=true){
		$s=$this->getSettings();
		$s=array_merge($s,$new);
		if($update){
			$this->saveSettings($s);
		}
		return $s;
	}
	public function saveSettings($settings){
		return update_option($this->option_name,json_encode($settings));
	}
}
// The final peice(s) of the puzzle.
$backdrop=new backdrop();
register_activation_hook(__FILE__,array('backdrop','__activate'));



add_filter("attribute_escape", "myfunction", 10, 2);
function myfunction($safe_text, $text) {
    return str_replace(__('Insert into Post'), __('Use this image'), $text);
}

//Use as featured image
