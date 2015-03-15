<?php
/*
Copyright (C) 2009-2015 AVChat Software, avchat.net

This WordPress Plugin is distributed under the terms of the GNU General Public License.
You can redistribute it and/or modify it under the terms of the GNU General Public License
as published by the Free Software Foundation, either version 3 of the License, or any later version.

You should have received a copy of the GNU General Public License
along with this plugin.  If not, see <http://www.gnu.org/licenses/>.
 */

/*
 * Feedback form
 */

global $current_user;
get_currentuserinfo();


/*
 * Check $_POST and proceed accordingly
 */
//---------------------------------------------------------------------------------------------
	if (isset($_POST) && !empty($_POST)) {

		// Incoming!
		$fb_name = (!empty($_POST['name'])) ? htmlspecialchars($_POST['name']) : 'Name not set';
		$fb_email = htmlspecialchars($_POST['email']);
		$fb_message = htmlspecialchars($_POST['message']);
		$fb_subscribe = (!is_null($_POST['subscribe'])) ? '1' : '0';
		$fb_website = trim($_POST['website']);

		$fb_to = 'contact@avchat.net';
		$fb_subject = "Message from {$fb_name} - via WordPress backend";
		//$fb_headers = "From: {$fb_name} <{$fb_email}>" . "\r\n";
		$fb_content = <<<EOT
							From: {$fb_name} <br />
							URL: {$fb_website} <br />
							Email: {$fb_email} <br />
							Subscribe: {$fb_subscribe} <br />
							Message: <br />
							<p>
							<strong>{$fb_message}</strong>
							</p>
EOT;

		// Start hooking
		function set_html_content_type() {
			return 'text/html';
		}

		add_filter('wp_mail_content_type', 'set_html_content_type');
		$status = wp_mail($fb_to, $fb_subject, $fb_content, $fb_headers); // returns 1 or 0
		remove_filter('wp_mail_content_type', 'set_html_content_type');

		// Adds a nice message in the header
		function feedback_add_message($type) {
			if ($type === 'success') {
				$output = "<div class='wrap'>";
				$output .= "<div class='updated'>";
				$output .= "<p>";
				$output .= "Thank you! Your message has been sent.";
				$output .= "</p>";
				$output .= "</div>";
				$output .= "</div>";
				return $output;
			} else {
				$output = "<div class='wrap'>";
				$output .= "<div class='error'>";
				$output .= "<p>";
				$output .= "Error sending message. You can always check our <a href='http://avchat.net/forum/?utm_source=wp-plugin&utm_medium=wp-backend-standard&utm_content=contact-form-error&utm_campaign=avchat-wp-standard'>forums<a/>.";
				$output .= "</p>";
				$output .= "</div>";
				$output .= "</div>";
				return $output;
			}
		}

		// Check if mail was sent. Displays an appropriate message.
		if ($status) {
			echo feedback_add_message('success');
		} else {
			echo feedback_add_message('failed'); // Not yet implemented
		}

	}
//---------------------------------------------------------------------------------------------
// End feedback.php $_POST processing

?>
<style type="text/css">
	form {display: table;}
	label {display: table-row;}
	input {display: table-cell;}
</style>

<div class="wrap">
	<h1>Send us your feedback</h1>
	<h3>We'll answer your question in 24 hours, except for weekends.</h3>

	<hr />

	<form name="form1" method="post" action="">

		<span class="description">Your name:</span>
		<label for="name"></label>
		<input type="text" name="name" id="name" value="<?php echo $current_user->user_firstname; ?>" />

		<br />

		<span class="description">Your e-mail:</span>
		<label for="email"></label>
		<input type="email" name="email" id="email" value="<?php echo $current_user->user_email; ?>" />

		<br />

		<span class="description">Your message</span>
		<label for="message"></label>
		<textarea id="message" name="message" cols="45" rows="15"></textarea>

		<label for="checkbox"></label>
		<input type="checkbox" name="subscribe" id="checkbox">
		<span class="description">Notify me when there are new versions or special offers.</span>

		<input type="hidden" name="website" value="<?php echo get_site_url(); ?>"/>
		<p class="submit">
			<input type="submit" value="Send feedback" class="button-primary" />
		</p>

	</form>

</div>
