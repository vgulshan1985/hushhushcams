<?php
/**
 * @package My Wish List
 * @version 1.4.1
 */
/*
Plugin Name: My Wish List
Plugin URI: http://nlb-creations.com/2011/12/30/wp-plug-in-my-wish-list/
Description: This plugin creates a new content type that can be used to set up and display a wish list on any page or post.
Author: Nikki Blight <nblight@nlb-creations.com>
Version: 1.4.1
Author URI: http://www.nlb-creations.com
*/

add_action( 'init', 'my_wish_create_post_types' );

//create a custom post type to hold wish list data
function my_wish_create_post_types() {
	register_post_type( 'wishlist',
		array(
			'labels' => array(
				'name' => __( 'My Wish Lists' ),
				'singular_name' => __( 'Wish List' ),
				'add_new' => __( 'Add Wish List'),
				'add_new_item' => __( 'Add Wish List'),
				'edit_item' => __( 'Edit Wish List' ),
				'new_item' => __( 'New Wish List' ),
				'view_item' => __( 'View Wish List' )
			),
			'show_ui' => true,
			'description' => 'Post type for Wish Lists',
			'menu_position' => 5,
			//'menu_icon' => WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) . '/qr-menu-icon.png',
			'public' => true,
			'exclude_from_search' => true,
			'supports' => array('title', 'editor'),
			'rewrite' => array('slug' => 'wish'),
			'can_export' => true
		)
	);
}

//add the stylesheet to the frontend theme
function my_wish_load_styles() {
	if ( !is_admin() ) {
		$dir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		
		//allow users to override the included css file with one of the same name in their active theme's folder
		$wishlist_css = get_stylesheet_directory_uri().'/mywishlist.css';
		if(!file_exists($wishlist_css)) {
			wp_enqueue_style( 'my-wish-list', $dir.'/styles/mywishlist.css' );
		}
		else {
			wp_enqueue_style( 'my-wish-list', $wishlist_css);
		}
	}
}
add_action('wp_enqueue_scripts', 'my_wish_load_styles');

// load the theme file for the playlist and show post types
function my_wish_load_templates($single_template) {
	global $post;

	if ($post->post_type == 'wishlist') {
		//first check to see if there's a template in the active theme's directory
		$user_theme = get_stylesheet_directory().'/single-wishlist.php';
		if(!file_exists($user_theme)) {
			$single_template = ABSPATH.'wp-content/plugins/my-wish-list/templates/single-wishlist.php';
		}
	}
	
	return $single_template;
}
add_filter( "single_template", "my_wish_load_templates" ) ;

//function for donors to promise to purchase items on the frontend
function my_wish_donor_process() {
	if(isset($_POST['wish_donor_add_update'])) {
		
		$wishmeta = get_post_meta($_POST['post_id'], 'my_wishes', true);
		
		$wishmeta[$_POST['wishindex']]['wishdonorname'] = $_POST['wishdonorname'];
		$wishmeta[$_POST['wishindex']]['wishdonoremail'] = $_POST['wishdonoremail'];
		
		$_POST['wish_donor_thank_you'] = 1;
		$_POST['wish_donor_donation'] = $wishmeta[$_POST['wishindex']]['wishitem'];
		
		update_post_meta($_POST['post_id'],'my_wishes',$wishmeta);
		
		//send an email to the site admin
		$admin_email = get_option('admin_email');
		$headers = 'From: My Wishlist <'.$admin_email.'>' . "\r\n";
		wp_mail($admin_email, 'Wishlist Item Promised', 'An item ('.$_POST['wishitem'].') from your wishlist has been promised by '.$_POST['wishdonorname'].'('.$_POST['wishdonoremail'].')', $headers);
	}
}
add_action( 'init', 'my_wish_donor_process' );

//shortcode function to show a wishlist in a post or page
function my_wish_show($atts) {
	extract( shortcode_atts( array(
		'id' => ''
	), $atts ) );
	
	//if no id is specified, we have nothing to display
	if(!$id) {
		return false;
	}
	
	$wishlist = get_post($id);
	$output = '';

	//if a non-existant id was specified, we have nothing to display
	if(!$wishlist) {
		return false;
	}
	
	//get the metadata
	$wishmeta = get_post_meta($id, 'my_wishes', true);
	$wishlist_instructions_link = get_post_meta($id,'wishlist_instructions_link',true);
	$wishlist_show_form = get_post_meta($id,'wishlist_show_form',true);
	$wishlist_show_donor = get_post_meta($id,'wishlist_show_donor',true);

	//thank you message for donors
	if(isset($_POST['wish_donor_thank_you'])) {
		$output .= '<div class="wishlist-thanks"> Thank you for pledging to get '.$_POST['wish_donor_donation'].' for us!  ';
		if($wishlist_instructions_link != '') {
			$output .= '<a href="'.$wishlist_instructions_link.'">Click here</a> for instructions on purchasing and sending the item.';
		}
		$output .= '</div>';
		unset($_POST['wish_donor_thank_you']);
	}
	
	//otherwise, output the wishlist
	$output .= '';
	
	if($wishlist->post_content != '') {
		$output .= '<div class="wishlist-desc">'.apply_filters('the_content',$wishlist->post_content).'</div>';
	}
	
	if($wishmeta) {
		foreach($wishmeta as $i => $meta) {
			//print_r($wishmeta);
			$output .= '<div class="wishlist-item">';
			$output .= '<h3>'.$meta['wishitem'].'</h3>';
			
			$output .= '<div class="wishlist-image-form">';
			
			if($meta['wishdonorname'] != '' || $meta['wishdonoremail'] != '') {
				if($wishlist_show_donor == "show") {
					$output .= '<span class="wishlist-promised">Item Promised by '.$meta['wishdonorname'].'!</span><br />';
				}
				else {
					$output .= '<span class="wishlist-promised">Item Promised!</span><br />';
				}
			}
			
			
			if($meta['wishfilename'] != '') {
				$thumb = wp_get_attachment_image_src( $meta['wishfilename'], 'medium');
				$output .= '<img src="'.$thumb[0].'" />';
				$output .= '<br />';
			}
			
			
			if($meta['wishdonorname'] == '' && $meta['wishdonoremail'] == '') {
				if($wishlist_show_form == "show") {
						$output .= 'I would like to get this item for you!  Tell me how!';
						$output .= '<form method="post" action="" class="wishlist-form">';
						$output .= '<table>';
						$output .= '<tr><td><span class="wishlist-form-label">Name:</span></td><td><input name="wishdonorname" type="text" class="wishlist-form-input" /></td></tr>';
						$output .= '<tr><td><span class="wishlist-form-label">Email:</span></td><td><input name="wishdonoremail" type="text" class="wishlist-form-input" /></td></tr>';
						$output .= '<tr><td colspan="2" class="wishlist-double-span">';
						$output .= '<input type="hidden" name="wishitem" value="'.$meta['wishitem'].'" />';
						$output .= '<input type="hidden" name="post_id" value="'.$id.'" />';
						$output .= '<input type="hidden" name="wishindex" value="'.$i.'" />';
						$output .= '<input type="hidden" name="wish_donor_add_update" value="1" />';
						$output .= '<input type="submit" value="Submit" class="wishlist-form-submit-button" />';
						$output .= '</td></tr>';
						$output .= '</table>';
						$output .= '</form>';
				}
			}
			
			
			$output .= '</div>';
			
			if($meta['wishprice'] != '') {
				$output .= '<span class="wishlist-label">Price:</span> '.$meta['wishprice'].'<br />';	
			}
			
			if($meta['wishsize'] != '') {
				$output .= '<span class="wishlist-label">Size:</span> '.$meta['wishsize'].'<br />';	
			}
			
			if($meta['wishcolor'] != '') {
				$output .= '<span class="wishlist-label">Color:</span> '.$meta['wishcolor'].'<br />';
			}
			
			if($meta['wishstore'] != '') {
				$output .= '<span class="wishlist-label">Available at:</span> '.$meta['wishstore'].'<br />';
			}
			
			if($meta['wishlink'] != '') {
				$output .= '<span class="wishlist-label">Link:</span> <a href="'.$meta['wishlink'].'">'.$meta['wishlink'].'</a><br />';	
			}
			$output .= '</div>';
			
			$output .= '<hr class="wishlist-hr" />';
			
		}
	}
	
	return $output;
}
add_shortcode( 'wishlist', 'my_wish_show');

/* Adds a box to the main column on the Post and Page edit screens */
function my_wish_dynamic_add_custom_box() {
    add_meta_box(
        'my_wish_dynamic_sectionid',
        __( 'Wishes', 'myplugin_textdomain' ),
        'my_wish_dynamic_inner_custom_box',
        'wishlist');
    
    add_meta_box(
    	'my_wish_dynamic_sidebarid',
    	__( 'Wishlist Settings', 'myplugin_textdomain' ),
    	'my_wish_dynamic_sidebar_custom_box',
    	'wishlist',
    	'side',
    	'high');
}
add_action( 'add_meta_boxes', 'my_wish_dynamic_add_custom_box' );

/* pulls up the media uploader... all of the code for image upload and attachment is borrowed from Elliot Condon's amazing Advanced Custom Fields Plugin */
function my_wish_popup_head() {
	if(isset($_GET["wish_type"]) && $_GET['wish_type'] == 'image') {
		$preview_size = 'medium';
		
		?>
		<style type="text/css">
			#media-upload-header #sidemenu li#tab-type_url,
			#media-upload-header #sidemenu li#tab-gallery {
				display: none;
			}
			
			#media-items tr.url,
			#media-items tr.align,
			#media-items tr.image_alt,
			#media-items tr.image-size,
			#media-items tr.post_excerpt,
			#media-items tr.post_content,
			#media-items tr.image_alt p,
			#media-items table thead input.button,
			#media-items table thead img.imgedit-wait-spin,
			#media-items tr.submit a.wp-post-thumbnail {
				display: none;
			} 

			.media-item table thead img {
				border: #DFDFDF solid 1px; 
				margin-right: 10px;
			}

		</style>
		<script type="text/javascript">
		(function($){
		
			$(document).ready(function(){
			
				$('#media-items').bind('DOMNodeInserted',function(){
					$('input[value="Insert into Post"]').each(function(){
						$(this).attr('value','<?php _e("Select Image",'my-wish-list'); ?>');
					});
				}).trigger('DOMNodeInserted');
				
				$('form#filter').each(function(){
					
					$(this).append('<input type="hidden" name="wish_preview_size" value="<?php echo $preview_size; ?>" />');
					$(this).append('<input type="hidden" name="wish_type" value="image" />');
					
				});
			});
						
		})(jQuery);
		</script>
		<?php
	}
}
add_action('admin_head-media-upload-popup', 'my_wish_popup_head');
	
function my_wish_media_send_to_editor($html, $id) {
	parse_str($_POST["_wp_http_referer"], $arr_postinfo);
	
	if(isset($arr_postinfo["wish_type"]) && $arr_postinfo["wish_type"] == "image") {
		
		$preview_size = 'medium';
		
		$file_src = wp_get_attachment_image_src($id, $preview_size);
		$file_src = $file_src[0];
	
		?>
		<script type="text/javascript">
			self.parent.my_wish_div.find('input.value').val('<?php echo $id; ?>');
		 	self.parent.my_wish_div.find('img').attr('src','<?php echo $file_src; ?>');
		 	self.parent.my_wish_div.find('img').attr('width', '');
		 	self.parent.my_wish_div.find('img').attr('height', '');
		 	self.parent.my_wish_div.addClass('active');
		 	
		 	// reset my_wish_div and return false
		 	self.parent.my_wish_div = null;
		 	self.parent.tb_remove();
			
		</script>
		<?php
		exit;
	} 
	else {
		return $html;
	}

}
add_filter('media_send_to_editor', 'my_wish_media_send_to_editor', 15, 2 );

/* Prints the box content */
function my_wish_dynamic_inner_custom_box() {
    global $post;
    // Use nonce for verification
    wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMeta_noncename' );
    ?>
    <div id="meta_inner" class="my_wish_list_list">
    <?php

    //get the saved meta as an array
    $wishes = get_post_meta($post->ID,'my_wishes',false);
    
    $c = 0;
    if (isset($wishes[0]) && count($wishes[0]) > 0 && $wishes[0] != ''){
        foreach($wishes[0] as $track ){
            if (isset($track['wishitem']) || isset($track['wishprice']) || isset($track['wishsize']) || isset($track['wishcolor']) || isset($track['wishstore']) || isset($track['wishlink'])){
                echo '<div style="border-bottom: 1px solid #DFDFDF; margin: 5px; padding: 5px;"><span class="remove button" style="cursor: pointer; float: right;">Remove</span><table style="border-collapse: collapse; padding: 5px;"><tr><td>Item Name:</td><td><input type="text" name="my_wishes['.$c.'][wishitem]" value="'.$track['wishitem'].'" /></td></tr><tr><td>Price:</td><td><input type="text" name="my_wishes['.$c.'][wishprice]" value="'.$track['wishprice'].'" /></td></tr><tr><td>Size:</td><td><input type="text" name="my_wishes['.$c.'][wishsize]" value="'.$track['wishsize'].'" /></td></tr><tr><td>Color:</td><td><input type="text" name="my_wishes['.$c.'][wishcolor]" value="'.$track['wishcolor'].'" /></td></tr><tr><td>Store:</td><td><input type="text" name="my_wishes['.$c.'][wishstore]" value="'.$track['wishstore'].'" /></td></tr><tr><td>Link:</td><td><input type="text" size="100" name="my_wishes['.$c.'][wishlink]" value="'.$track['wishlink'].'" /></td></tr><tr style="background-color: #cccccc;"><td>Promised By:</td><td><input type="text" size="100" name="my_wishes['.$c.'][wishdonorname]" value="'.$track['wishdonorname'].'" /></td></tr></tr><tr style="background-color: #cccccc;"><td>Email:</td><td><input type="text" size="100" name="my_wishes['.$c.'][wishdonoremail]" value="'.$track['wishdonoremail'].'" /></td></tr></table>';

                echo '<div class="my_wish_image_uploader" data-preview_size="medium">';
                if(wp_get_attachment_image( $track['wishfilename'], 'medium' )) {	
                	echo wp_get_attachment_image( $track['wishfilename'], 'medium' );
                }
                else {
                	echo '<img src="'.get_bloginfo('url').'/wp-content/plugins/my-wish-list/images/blank.png" />';
                }
				echo '<input class="value" type="hidden" name="my_wishes['.$c.'][wishfilename]" value="'.$track['wishfilename'].'" />';
				echo '<p><input type="button" class="button" value="'.__('Add Image','my_wish_list').'" /></p>';
				echo '<a href="#" class="remove_image">Remove Image</a>';
				
				echo '</div>';
                
                
                echo '</div>';
                $c = $c +1;
            }
        }
    }

    ?>
	<span id="here"></span>
	<span class="add button" style="cursor: pointer;"><?php echo __('Add Item to Wish List'); ?></span>
	<script>
	
	(function($){
		
		$('#poststuff .my_wish_image_uploader .button').live('click', function(){
			
			// vars
			var div = $(this).closest('.my_wish_image_uploader');
			var post_id = $('input#post_ID').val();
			var preview_size = 'medium';
			
			// set global var
			window.my_wish_div = div;
				
			// show the thickbox
			tb_show('Add Image to field', 'media-upload.php?post_id=' + post_id + '&type=image&wish_type=image&wish_preview_size=' + preview_size + 'TB_iframe=1');
		
			return false;
		});
			
		$('#poststuff .my_wish_image_uploader .remove_image').live('click', function(){
			
			// vars
			var div = $(this).closest('.my_wish_image_uploader');
			var blank = "<?php echo get_bloginfo('url').'/wp-content/plugins/my-wish-list/images/blank.png'; ?>";
			
			div.find('input.value').val('');
			div.find('img').attr('src', blank);
			div.find('img').attr('width', '');
			div.find('img').attr('height', '');
			div.removeClass('active');
			
			return false;
			
		});
			
	})(jQuery);


	//var $ =jQuery.noConflict();
	
    jQuery(document).ready(function() {
        var count = <?php echo $c; ?>;
        var blankimage = "<?php echo get_bloginfo('url').'/wp-content/plugins/my-wish-list/images/blank.png'; ?>";
        jQuery(".add").click(function() {
            count = count + 1;
            
            jQuery('#here').append('<div style="border-bottom: 1px solid #DFDFDF; margin: 5px; padding: 5px;"><span class="remove button" style="cursor: pointer; float: right;">Remove</span><table style="border-collapse: collapse;"><tr><td>Item Name:</td><td><input type="text" name="my_wishes['+count+'][wishitem]" value="" /></td></tr><tr><td>Price:</td><td><input type="text" name="my_wishes['+count+'][wishprice]" value="" /></td></tr><tr><td>Size:</td><td><input type="text" name="my_wishes['+count+'][wishsize]" value="" /></td></tr><tr><td>Color:</td><td><input type="text" name="my_wishes['+count+'][wishcolor]" value="" /></td></tr><tr><td>Store:</td><td><input type="text" name="my_wishes['+count+'][wishstore]" value="" /></td></tr><tr><td>Link:</td><td><input type="text" size="100" name="my_wishes['+count+'][wishlink]" value="" /></td></tr><tr style="background-color: #cccccc;"><td>Promised By:</td><td><input type="text" size="100" name="my_wishes['+count+'][wishdonorname]" value="" /></td></tr><tr style="background-color: #cccccc;"><td>Email:</td><td><input type="text" size="100" name="my_wishes['+count+'][wishdonoremail]" value="" /></td></tr></table>   <div class="my_wish_image_uploader" data-preview_size="medium"><img src="'+blankimage+'" alt=""/><input class="value" type="hidden" name="my_wishes['+count+'][wishfilename]" value="" /><p><input type="button" class="button" value="Add Image" /></p><a href="#" class="remove_image">Remove Image</a></div></div>' );

            
            return false;
        });
        jQuery(".remove").live('click', function() {
        	jQuery(this).parent().remove();
        });
    });
    </script>
</div><?php

}

/* Prints the sidebar box content */
function my_wish_dynamic_sidebar_custom_box() {
	global $post;
	
	$wishlist_instructions_link = get_post_meta($post->ID,'wishlist_instructions_link',true);
	$wishlist_show_form = get_post_meta($post->ID,'wishlist_show_form',true);
	$wishlist_show_donor = get_post_meta($post->ID,'wishlist_show_donor',true);
	?>
    <div id="meta_inner" class="my_wish_list_list">
    <strong>Shortcode:</strong> [wishlist id=<?php echo $post->ID; ?>]
    
    <br /><br />
	
    <strong>Link to instructions page:</strong><br />
    <input class="value" type="text" name="wishlist_instructions_link" value="<?php echo $wishlist_instructions_link; ?>" size="25" /><br />
    <em>Use this field to provide a link to a page with instructions on how/where to purchase items on your wish list and where to send them.</em>
    <br /><br />
    Show or Hide the "Will Purchase" form?<br />
    <select name="wishlist_show_form">
    	<option<?php if($wishlist_show_form == "hide") { echo ' selected="selected"'; } ?> value="hide">Hide</option>
    	<option<?php if($wishlist_show_form == "show") { echo ' selected="selected"'; } ?> value="show">Show</option>
    </select>
    <br /><br />
    Show or Hide purchaser names on the wishlist?<br />
    <select name="wishlist_show_donor">
    	<option<?php if($wishlist_show_donor == "hide") { echo ' selected="selected"'; } ?> value="hide">Hide</option>
    	<option<?php if($wishlist_show_donor == "show") { echo ' selected="selected"'; } ?> value="show">Show</option>
    </select>
	
</div><?php

}

/* When the post is saved, saves our custom data */
function my_wish_dynamic_save_postdata( $post_id ) {
    // verify if this is an auto save routine. 
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        return;

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if (isset($_POST['dynamicMeta_noncename'])){
        if ( !wp_verify_nonce( $_POST['dynamicMeta_noncename'], plugin_basename( __FILE__ ) ) )
            return;
    }else{return;}

    // OK, we're authenticated: we need to find and save the data
    $wishes = isset($_POST['my_wishes']) ? $_POST['my_wishes'] : '';
    
    if($wishes) {
	    foreach($wishes as $i => $wish) {
	    	if($wish['wishlink'] != '') {
		    	if(!stristr($wish['wishlink'], 'http://') && !stristr($wish['wishlink'], 'https://')) {
		    		$wishes[$i]['wishlink'] = 'http://'.$wish['wishlink'];
		    	}
	    	}
	    }
    }
    
    update_post_meta($post_id,'my_wishes',$wishes);
    update_post_meta($post_id,'wishlist_instructions_link',$_POST['wishlist_instructions_link']);
    update_post_meta($post_id,'wishlist_show_form',$_POST['wishlist_show_form']);
    update_post_meta($post_id,'wishlist_show_donor',$_POST['wishlist_show_donor']);
}
add_action( 'save_post', 'my_wish_dynamic_save_postdata' );


?>