<?php
$settings = backdrop::getSettings();
if(isset($_POST['update'])){
	unset($_POST['update']);
	if(isset($_POST['background_type'])){ // Your updateing the background, I could have checked update but I diden't
		// Heres all the code that determines what is needed and makes the file that will be used.
		require_once(WP_PLUGIN_DIR.'/backdrop/generator.class.php');
		$generator = new backdropGenerator();
		$generator->_set_zindex($settings['z-index']);
		$_POST['needs_foot']='no';
		$_POST['needs_js']='no';
		switch($_POST['background_type']){
			case 'color':
				$generator->color($_POST['backdrop']['color']);
			break;
			case 'image':
				if($_POST['backdrop']['image']['attachment']=='parallax' || $_POST['backdrop']['image']['attachment']=='slide'){
					$_POST['needs_js']='yes';
				}
				if($_POST['backdrop']['image']['attachment']=='fullscreen'){
					$_POST['needs_foot']='yes';
				}
				$generator->image($_POST['backdrop']['image']);
			break;
			case 'css':
				if($_POST['backdrop']['css']['application']=='custom'){
					$_POST['needs_foot']='true';
				}
				$generator->css($_POST['backdrop']['css']);
			break;
			default:
				$_POST['background_type']='nulled';
			break;
		}
		$generator->write();
		$_POST['last_updated']=time();
		echo '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>Site Background Updated<em>!</em></strong></p></div>';
	}else{ // Must be talking about advanced settings
		echo '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>Advanced Settings Updated<em>!</em></strong></p></div>';
	}
	$settings = backdrop::updateSettings($_POST);
}
?>
<style>
h2 > small{
	font-size:.5em;
	color:#999999;
}

input[type="number"]{
	width:50px;
}
textarea{
	width: 540px;
	height: 100px;
}
</style>
<div class="wrap" id="custom-background">
<div id="icon-themes" class="icon32"><br></div><h2>Backdrop <small><em>Version <?= $settings['version'] ?></em></small></h2>

<form method="post" action="">
	<input type="hidden" name="update" value="backdrop" />
	<h3>Site Background</h3>

	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">Background Type</th>
				<td>
					<input type="radio" <?= ($settings['background_type']=='color'?'checked="checked"':'') ?> name="background_type" value="color" id="background_type_color" /> <span class="description"><label for="background_type_color">Color</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= ($settings['background_type']=='image'?'checked="checked"':'') ?> name="background_type" value="image" id="background_type_image" /> <span class="description"><label for="background_type_image">Image</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= ($settings['background_type']=='css'?'checked="checked"':'') ?> name="background_type" value="css" id="background_type_css" /> <span class="description"><label for="background_type_css">CSS</label></span> &nbsp; &nbsp; &nbsp;
				</td>
			</tr>
		</tbody>
	</table>

	<table class="form-table backdrop_options" id="color" style="display:none;">
		<tbody>
			<tr valign="top">
				<th scope="row"><label for="backdrop_color_color">Color</label></th>
				<td>
					<input type="color" id="backdrop_color_color" name="backdrop[color][color]" value="<?= $settings['backdrop']['color']['color'] ?>" style="vertical-align:middle;"/>
					<span class="description"> Any valid CSS color (HEX or RGB(A), cool browsers see a color picker here).
				</td>
			</tr>
		</tbody>
	</table>

	<table class="form-table backdrop_options" id="image" style="display:none;">
		<tbody>
			<tr valign="top" class="all">
				<th scope="row"><label for="itm">Image</label></th>
				<td>
					<input id="backdrop_upload_button" value="Upload/Select Image" type="button" class="button" />
					<input type="hidden" name="backdrop[image][img]" id="backdrop_image_img" value="<?= $settings['backdrop']['image']['img'] ?>" />
					<span class="description">Selected: <a href="<?= $settings['backdrop']['image']['img'] ?>" target="_blank"><?= $settings['backdrop']['image']['img'] ?></a></span>
				</td>
			</tr>
			<tr valign="top" class="adaptor">
				<th scope="row">Image Attachment</th>
				<td>
					<input type="radio" <?= @($settings['backdrop']['image']['attachment']=='scroll'?'checked="checked"':'') ?> name="backdrop[image][attachment]" value="scroll" id="background_image_attachment_scroll" /> <span class="description"><label for="background_image_attachment_scroll">Scroll</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['attachment']=='parallax'?'checked="checked"':'') ?> name="backdrop[image][attachment]" value="parallax" id="background_image_attachment_parallax" /> <span class="description"><label for="background_image_attachment_parallax">Parallax</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['attachment']=='fixed'?'checked="checked"':'') ?> name="backdrop[image][attachment]" value="fixed" id="background_image_attachment_fixed" /> <span class="description"><label for="background_image_attachment_fixed">Fixed</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['attachment']=='slide'?'checked="checked"':'') ?> name="backdrop[image][attachment]" value="slide" id="background_image_attachment_slide" /> <span class="description"><label for="background_image_attachment_slide">Slide</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['attachment']=='fullscreen'?'checked="checked"':'') ?> name="backdrop[image][attachment]" value="fullscreen" id="background_image_attachment_fullscreen" /> <span class="description"><label for="background_image_attachment_fullscreen">Fullscreen</label></span> &nbsp; &nbsp; &nbsp;
				</td>
			</tr>
			<tr valign="top" class="scroll parallax fixed slide">
				<th scope="row">Image Repeat</th>
				<td>
					<input type="radio" <?= @($settings['backdrop']['image']['repeat']=='xy'?'checked="checked"':'') ?> name="backdrop[image][repeat]" value="xy" id="background_image_repeat_xy" /> <span class="description"><label for="background_image_repeat_xy">Repeat X + Y</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['repeat']=='x'?'checked="checked"':'') ?> name="backdrop[image][repeat]" value="x" id="background_image_repeat_x" /> <span class="description"><label for="background_image_repeat_x">Repeat X</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['repeat']=='y'?'checked="checked"':'') ?> name="backdrop[image][repeat]" value="y" id="background_image_repeat_y" /> <span class="description"><label for="background_image_repeat_y">Repeat Y</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['repeat']=='none'?'checked="checked"':'') ?> name="backdrop[image][repeat]" value="none" id="background_image_repeat_none" /> <span class="description"><label for="background_image_repeat_none">No Repeat</label></span> &nbsp; &nbsp; &nbsp;
				</td>
			</tr>
			<tr valign="top" class="scroll parallax fixed">
				<th scope="row">Image Horizontal Position</th>
				<td>
					<input type="radio" <?= @($settings['backdrop']['image']['hpos']=='left'?'checked="checked"':'') ?> name="backdrop[image][hpos]" value="left" id="background_image_hpos_left" /> <span class="description"><label for="background_image_hpos_left">Left</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['hpos']=='center'?'checked="checked"':'') ?> name="backdrop[image][hpos]" value="center" id="background_image_hpos_center" /> <span class="description"><label for="background_image_hpos_center">Center</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['hpos']=='right'?'checked="checked"':'') ?> name="backdrop[image][hpos]" value="right" id="background_image_hpos_right" /> <span class="description"><label for="background_image_hpos_right">Right</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['hpos']=='stretch'?'checked="checked"':'') ?> name="backdrop[image][hpos]" value="stretch" id="background_image_hpos_stretch" /> <span class="description"><label for="background_image_hpos_stretch">Stretch</label></span> &nbsp; &nbsp; &nbsp;
				</td>
			</tr>
			<tr valign="top" class="scroll fixed">
				<th scope="row">Image Vertical Position</th>
				<td>
					<input type="radio" <?= @($settings['backdrop']['image']['vpos']=='top'?'checked="checked"':'') ?> name="backdrop[image][vpos]" value="top" id="background_image_vpos_top" /> <span class="description"><label for="background_image_vpos_top">Top</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['vpos']=='center'?'checked="checked"':'') ?> name="backdrop[image][vpos]" value="center" id="background_image_vpos_center" /> <span class="description"><label for="background_image_vpos_center">Center</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['vpos']=='bottom'?'checked="checked"':'') ?> name="backdrop[image][vpos]" value="bottom" id="background_image_vpos_bottom" /> <span class="description"><label for="background_image_vpos_bottom">Bottom</label></span> &nbsp; &nbsp; &nbsp;
				</td>
			</tr>
			<tr valign="top" class="scroll parallax fixed slide" id="retinize">
				<th scope="row">Retinize Background</th>
				<td>
					<input type="hidden" name="backdrop[image][retinize]" value="no" />
					<input type="checkbox" name="backdrop[image][retinize]" id="backdrop_image_retinize" value="yes" <?= @($settings['backdrop']['image']['retinize']=='yes'?'checked="checked"':'') ?> /> <span class="description"><label for="backdrop_image_retinize">Will cut background in half to maintain a sharp image on retina displays (at the cost of larger file size and smaller patterns).</label></span> &nbsp; &nbsp; &nbsp;
				</td>
			</tr>
			<tr valign="top" class="parallax">
				<th scope="row"><label for="backdrop_image_parallax_adjustment">Parallax Adjustment</a></th>
				<td>
					<input type="number" min="0" max="100" step="1" name="backdrop[image][parallax_adjustment]" value="<?= $settings['backdrop']['image']['parallax_adjustment'] ?>" id="backdrop_image_parallax_adjustment" />
					<span class="description">Controls how different the parallax scroll speed is, 0 would be the same as fixed, 100 would be normal scroll behavior.</span>
				</td>
			</tr>
			<tr valign="top" class="slide">
				<th scope="row">Image Slide Direction</th>
				<td>
					<input type="radio" <?= @($settings['backdrop']['image']['slide']=='up'?'checked="checked"':'') ?> name="backdrop[image][slide]" value="up" id="background_image_slide_up" /> <span class="description"><label for="background_image_slide_up">Up</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['slide']=='up-right'?'checked="checked"':'') ?> name="backdrop[image][slide]" value="up-right" id="background_image_slide_up-right" /> <span class="description"><label for="background_image_slide_up-right">Up-Right</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['slide']=='right'?'checked="checked"':'') ?> name="backdrop[image][slide]" value="right" id="background_image_slide_right" /> <span class="description"><label for="background_image_slide_right">Right</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['slide']=='down-right'?'checked="checked"':'') ?> name="backdrop[image][slide]" value="down-right" id="background_image_slide_down-right" /> <span class="description"><label for="background_image_slide_down-right">Down-Right</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['slide']=='down'?'checked="checked"':'') ?> name="backdrop[image][slide]" value="down" id="background_image_slide_down" /> <span class="description"><label for="background_image_slide_down">Down</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['slide']=='down-left'?'checked="checked"':'') ?> name="backdrop[image][slide]" value="down-left" id="background_image_slide_down-left" /> <span class="description"><label for="background_image_slide_down-left">Down-Left</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['slide']=='left'?'checked="checked"':'') ?> name="backdrop[image][slide]" value="left" id="background_image_slide_left" /> <span class="description"><label for="background_image_slide_left">Left</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= @($settings['backdrop']['image']['slide']=='up-left'?'checked="checked"':'') ?> name="backdrop[image][slide]" value="up-left" id="background_image_slide_up-left" /> <span class="description"><label for="background_image_slide_up-left">Up-Left</label></span> &nbsp; &nbsp; &nbsp;
				</td>
			</tr>
			<tr valign="top" class="slide">
				<th scope="row"><label for="backdrop_image_slide_speed">Slide Speed</a></th>
				<td>
					<input type="number" min="0" max="1000" step="1" name="backdrop[image][slide_speed]" value="<?= $settings['backdrop']['image']['slide_speed'] ?>" id="backdrop_image_slide_speed" />
					<span class="description">Controls how fast the image slides, 0 is no slide, 100 is 1-for-1 with downward scroll speed.</span>
				</td>
			</tr>

			<tr valign="top" class="all">
				<th scope="row"><label for="backdrop_image_color">Backup Color</label></th>
				<td>
					<input type="color" id="backdrop_image_color" name="backdrop[image][color]" value="<?= $settings['backdrop']['image']['color'] ?>" style="vertical-align:middle;"/>
					<span class="description"> Seen if image is slow loading, is set to repeate other than X + Y, or if it 404s, any valid CSS color (HEX or RGB(A), cool browsers see a color picker here).
				</td>
			</tr>
		</tbody>
	</table>

	<table class="form-table backdrop_options" id="css" style="display:none;">
		<tbody>
			<tr valign="top">
				<th scope="row"><label for="backdrop_css_css">CSS Code</label></th>
				<td>
					<textarea id="backdrop_css_css" name="backdrop[css][css]"><?= stripslashes($settings['backdrop']['css']['css']) ?></textarea><br/>
					<span class="description">Use whatever custom css you want, Selectors will automatically be added based on below selection.
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="backdrop_css_apply_styles_to">Apply Styles To</label></th>
				<td>
					<input type="radio" <?= ($settings['backdrop']['css']['application']=='body'?'checked="checked"':'') ?> name="backdrop[css][application]" value="body" id="backdrop_css_application_body" /> <span class="description"><label for="backdrop_css_application_body">Body Element</label></span> &nbsp; &nbsp; &nbsp;
					<input type="radio" <?= ($settings['backdrop']['css']['application']=='custom'?'checked="checked"':'') ?> name="backdrop[css][application]" value="custom" id="backdrop_css_application_custom" /> <span class="description"><label for="backdrop_css_application_custom">Custom Element (see notes below)</label></span> &nbsp; &nbsp; &nbsp;
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="backdrop_css_helpfull_links">Helpfull Links</label></th>
				<td>
					<span class="description">
						Make CSS3 image gradients using the <a href="http://www.colorzilla.com/gradient-editor/" target="_blank">Colorzilla Ultimate CSS Gradient Generaor</a>, the best gradient genrator around.<br/>
						Try out some fancy CSS3 patters, Lea Verou has gathered a great selection at her <a href="http://lea.verou.me/css3patterns/" target="_blank">CSS3 Patterns Gallery</a>.<br/>
						Don't forget all those browser specific prefixes, you might want to run it through <a href="http://prefixr.com/" target="_blank">Prefixr</a>.
					</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="backdrop_css_notes">Notes</label></th>
				<td>
					<span class="description">Where you apply your custom CSS background generally controls how it behaves when scrolling. If applied to the body, it will move with the page (same as an image background set to scroll), if it's applied to the custom element it will say where it is (same as an image background set to fixed).<br/><br/>
					The custom element has base styles applied to it to make it fill the page and places it beheind the main page content (using the z-index defined in the advanced settings below). Placing it in a custom element also allows for the ability to run more than one background simultanously (using a semi-transparent custom background with a default image background for instance), although this technique is more advanced and requires CSS knowledge.</span>
				</td>
			</tr>
		</tbody>
	</table>

	<p class="submit">
		<input type="submit" class="button-primary" value="Update Backdrop">
	</p>
</form>

<form method="post" action="">
	<input type="hidden" name="update" value="advanced_settings" />
	<h3>Advanced Settings</h3>

	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><label for="adv_zindex">Foot Overlay z-index</a></th>
				<td>
					<input type="number" min="-1000000" max="1000000" step="1" name="z-index" value="<?= $settings['z-index'] ?>" id="adv_zindex" />
					<span class="description">Controls the Z positioning of the background overlay div, usually -1 works, but you can change this number if your theme is not normal.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Show Stock Background Customizer</th>
				<td>
					<input type="hidden" name="show_default" value="no" />
					<input type="checkbox" name="show_default" id="show_default" value="yes" <?= @($settings['show_default']=='yes'?'checked="checked"':'') ?> /> <span class="description"><label for="show_default">Will bring back the default background options in aidition to the backdrop options, lets you get fancy with dual backgrounds if thats your thing.</label></span> &nbsp; &nbsp; &nbsp;
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Active Includes</th>
				<td>
					<!-- There would be one for CSS here, but it always needs to be active -->
					<input type="hidden" name="needs_js" value="no" />
					<input type="hidden" name="needs_foot" value="no" />
					<input type="checkbox" checked="checked" name="needs_css" value="yes" id="adv_needs_css" disabled="disabled" /> <span class="description"><label for="adv_needs_css">CSS</label></span> &nbsp; &nbsp; &nbsp;
					<input type="checkbox" <?= ($settings['needs_js']=='yes'?'checked="checked"':'') ?> name="needs_js" value="yes" id="adv_needs_js" /> <span class="description"><label for="adv_needs_js">JavaScript</label></span> &nbsp; &nbsp; &nbsp;
					<input type="checkbox" <?= ($settings['needs_foot']=='yes'?'checked="checked"':'') ?> name="needs_foot" value="yes" id="adv_needs_foot" /> <span class="description"><label for="adv_needs_foot">Footer Div</label></span> &nbsp; &nbsp; &nbsp;
					<span class="description">What is currently being added to your page. This is automatically updated when the backdrop is, changing these will probably break things.</span>
				</td>
			</tr>
			<!-- This is actually very usefull, woudlrather have a shorter page
			<tr valign="top">
				<th scope="row">Show Debug Information</th>
				<td>
					<input type="checkbox" name="show_debug" id="show_debug" value="show" onclick="$('pre#debug_data_box').toggle()" />
					<span class="description"><label for="show_debug">This option does not save and does not change anything, it simply toggles a box below with your plugin settings and POST data.</label></span><br/>
					<pre id="debug_data_box" style="font-family:monospace;font-size:.75em;line-height:1.25em;display:none;"><?php
						print_r($settings);
						print_r($_POST);
					?></pre>
				</td>
			</tr> -->

		</tbody>
	</table>

	<p class="submit">
		<input type="submit" class="button-primary" value="Update Advanced Settings">
	</p>
</form>

<h3 class="title">Miscellaneous</h3>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="nothing">Have a problem</label></th>
		<td>
			Having trouble getting the plug-in working? Expected results? Feel like direction your repentant rage at someone far far away? Just have a general usage question? You can try the <a href="http://wordpress.org/support/plugin/backdrop" target="_blank">plug-ins support form</a> or, if you want an answer from the source, feel free to email me at <a href="mailto:phillip.gooch@gmail.com" target="_blank">phillip.gooch@gmail.com</a>.
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="nothing">Check out the code</label></th>
		<td>
			Want to see how it all works, you can check out the on the <a href="http://plugins.svn.wordpress.org/backdrop/trunk/" target="_blank">WordPress SVN</a> or, even better, <a href="https://github.com/pgooch/backdrop" target="_blank">fork it on GitHub</a>. Feel free to make changes and submit pull requests, good ideas will be added to the master branch.
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Donate</th>
		<td>
			Like the plug-in and want to support further development? Thanks! You can use the paypal button below to donate any amount you want. Don't like PayPal? send me an email, we can figure something out.<br/>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
				<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
				<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
		</td>
	</tr>
</table>

<script>
	var $ = jQuery;
	// Handels switching based on background type
	$(document).ready(function(){$('input[name="background_type"]:checked').click();});
	$('input[name="background_type"]').click(function(){
		$('table.backdrop_options').hide();
		$('table#'+$(this).val()).show();
		$('table.backdrop_options input, table.backdrop_options textarea').attr('disabled',true);
		$('table#'+$(this).val()+'.backdrop_options input, table#'+$(this).val()+'.backdrop_options textarea').attr('disabled',false);
		$(document).ready(function(){$('#background_image_hpos_stretch').trigger('click',true);});
	});
	// Handels switching based on any sub type (remvoed all table rows that don't have the class "all" or input value)
	$(document).ready(function(){$('tr.adaptor input:checked').click();});
	$('tr.adaptor input').click(function(){
		var selector = $(this).val();
		$(this).parent().parent().parent().parent().find('tr').show();
		$(this).parent().parent().parent().parent().find('tr:not(.all, .adaptor, .'+selector+')').hide();
		$(document).ready(function(){$('#background_image_hpos_stretch').trigger('click',true);});
	});
	// A special little one, if you have Horizontal Position set to Stretch then you have effectively disabled the Retinizer
	$(document).ready(function(){$('#background_image_hpos_stretch').trigger('click',true);});
	$('tr [name="backdrop[image][hpos]"]').click(function(e,ignore){
		$('#retinize').show();
		if($('input[name="backdrop[image][attachment]"]').val()=='scroll' || $('input[name="backdrop[image][attachment]"]').val()=='parallax' || $('input[name="backdrop[image][attachment]"]').val()=='fixed'){
			if($('#background_image_hpos_stretch').is(':checked')){
				$('#retinize').hide();
			}
		}
		if(ignore){
			e.preventDefault();
		}
	});
	// Handels image uploading
	$(document).ready(function(){
		$("input#backdrop_upload_button").click(function(){
			tb_show('','media-upload.php?type=image&post_id=1&TB_iframe=true&flash=0&backdrop=true');
			return false;
		})
		window.send_to_editor=function(html){
			var img = $('<div>'+html+'</div>').find('img').attr('src');
			$('#backdrop_image_img').val(img);
			tb_remove();
		};
	});
</script>