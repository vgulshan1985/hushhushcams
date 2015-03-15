<?php if (session_id() == "") {session_start();}

/*
 * Check if the Connection string has been set
 */
    // Store any error messages
    $error_messages = array();

    $db_check_settings = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "avchat3_general_settings");
    $db_grab_conn_string = $db_check_settings[0]->connection_string;

    // Pull from DB and do a small check to see if the user has filled Connection string
    if ($db_grab_conn_string === "rtmp://" || empty($db_grab_conn_string))
    {
        $error_messages[] = "Please fill in the <code>Connection string</code> first in Video Chat - Settings - Plugin Settings.";
    }

    // Check if the user has uploaded the AVChat software files
    if (file_exists(plugin_dir_path(__FILE__) . 'audio_video_quality_profiles') == false)
    {
        $error_messages[] = "You need to upload the AVChat software into <code>wp-contents/plugins/avchat-3</code> folder.";
    }

    // If the user hasn't filled Connection string, start nagging
    if (empty($error_messages) === false) {
        foreach ($error_messages as $error_message) {
            echo "
            <div class='wrap'>
                <div class='error form-invalid'>
                    <p>
                        {$error_message}
                    </p>
                </div>
            </div>
            ";
        }
    }

/*
 * Main function for outputting the chat in backend
 */
function start_output_in_admin() {
    $embed = '';

    // Make sure that there are no errors stored => display the chat
    if (isset($error_messages) === false) {
        $display_mode = 'embed';

        $user_info = avchat3_get_user_details();
        avchat3_set_user_details_on_session($user_info);
        avchat3_set_avchat3_general_settings_on_session();

        // Is admin, load admin login
        $movie_param = 'admin.swf';

        // Blank for the moment
        $FB_appId = '';

        $embed = '
                <div id="myContent">
                    <div id="av_message">
                        You need to have JavaScript enabled and <a target="_blank" href="http://get2.adobe.com/flashplayer/">the latest version of Flash Player</a> for the chat to work.
                    </div>
                </div>
                <input type="hidden" name="FB_appId" id="FB_appId" value="' . $FB_appId . '" />
                <script type="text/javascript" src="' . get_bloginfo('url') . '/wp-content/plugins/avchat-3/tinycon.min.js"></script>
                <script type="text/javascript" src="' . get_bloginfo('url') . '/wp-content/plugins/avchat-3/facebook_integration.js"></script>
                <script type="text/javascript" src="' . get_bloginfo('url') . '/wp-content/plugins/avchat-3/swfobject.js"></script>
                <script type="text/javascript" src="' . get_bloginfo('url') . '/wp-content/plugins/avchat-3/new_message.js"></script>
                <script type="text/javascript">
                var plugin_path = "' . get_bloginfo('url') . '/wp-content/plugins/avchat-3/"; var embed = "' . $display_mode . '";
                </script>
                <script type="text/javascript">
                    var flashvars = {
                        lstext : "Loading Settings...",
                        sscode : "php",
                        userId : ""
                    };
                    var params = {
                        wmode : "transparent",
                        quality : "high",
                        bgcolor : "#272727",
                        play : "true",
                        loop : "false",
                        allowFullScreen : "true",
                        base : "' . get_bloginfo("url") . '/wp-content/plugins/avchat-3/"
                    };
                    var attributes = {
                        name : "index_embed",
                        id :   "index_embed",
                        align : "middle"
                    };
                </script>
                <script type="text/javascript">
                swfobject.embedSWF("' . get_bloginfo('url') . '/wp-content/plugins/avchat-3/' . $movie_param . '", "myContent", "100%", "600", "11.4.0", "", flashvars, params, attributes);
                </script>';
        $embed .= '<script type="text/javascript" src="' . get_bloginfo('url') . '/wp-content/plugins/avchat-3/find_player.js"></script>';

        return $embed;

    }// End empty($error_messages)

} // End start_output_in_admin()


?>

<!DOCTYPE html>
<html>
<body>

<div class="wrap">

    <?php
    /* -- Start -- */

    // Parse the chat area
    echo start_output_in_admin();
    
    /* -- End -- */
    ?>

</div>

</body>
</html>