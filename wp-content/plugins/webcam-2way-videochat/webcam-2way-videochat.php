<?php
/*
Plugin Name: Webcam 2 Way Videochat
Plugin URI: http://www.videowhisper.com/?p=WordPress-Webcam-2Way-VideoChat
Description: VideoWhisper Webcam 2 Way Video Chat
Version: 4.41
Author: VideoWhisper.com
Author URI: http://www.videowhisper.com/
Contributors: videowhisper, VideoWhisper.com
*/

if (!class_exists("VWvideoChat"))
{
	class VWvideoChat {

		function VWvideoChat() { //constructor


		}


		function settings_link($links) {
			$settings_link = '<a href="options-general.php?page=webcam-2way-videochat.php">'.__("Settings").'</a>';
			array_unshift($links, $settings_link);
			return $links;
		}

		function init()
		{
			$plugin = plugin_basename(__FILE__);
			add_filter("plugin_action_links_$plugin",  array('VWvideoChat','settings_link') );

			wp_register_sidebar_widget('videoChatWidget','VideoWhisper Videochat', array('VWvideoChat', 'widget') );

			//shortcodes
			add_shortcode('videowhisper_videochat_manage',array( 'VWvideoChat', 'videochat_room'));

			$options = VWvideoChat::getAdminOptions();
			$page_id = get_option("vw_2vc_page_room");
			if (!$page_id || ($page_id=="-1" && $options['disablePage']=='0'))
				add_action('wp_loaded', array('VWvideoChat','updatePages'));

			add_action( 'wp_ajax_v2wvc', array('VWvideoChat','v2wvc_callback') );
			add_action( 'wp_ajax_nopriv_v2wvc', array('VWvideoChat','v2wvc_callback') );

			//check db
			$vw2vc_db_version = "1.1";

			global $wpdb;
			$table_name = $wpdb->prefix . "vw_2wsessions";
			$table_name3 = $wpdb->prefix . "vw_2wrooms";

			$installed_ver = get_option( "vw2vc_db_version" );

			if( $installed_ver != $vw2vc_db_version )
			{
				$wpdb->flush();

				$sql = "DROP TABLE IF EXISTS `$table_name`;
		CREATE TABLE `$table_name` (
		  `id` int(11) NOT NULL auto_increment,
		  `session` varchar(64) NOT NULL,
		  `username` varchar(64) NOT NULL,
		  `room` varchar(64) NOT NULL,
		  `message` text NOT NULL,
		  `sdate` int(11) NOT NULL,
		  `edate` int(11) NOT NULL,
		  `status` tinyint(4) NOT NULL,
		  `type` tinyint(4) NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `status` (`status`),
		  KEY `type` (`type`),
		  KEY `room` (`room`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Video Whisper: Sessions - 2009@videowhisper.com' AUTO_INCREMENT=1 ;

		DROP TABLE IF EXISTS `$table_name3`;
		CREATE TABLE `$table_name3` (
		  `id` int(11) NOT NULL auto_increment,
		  `name` varchar(64) NOT NULL,
		  `owner` int(11) NOT NULL,
		  `sdate` int(11) NOT NULL,
		  `edate` int(11) NOT NULL,
		  `status` tinyint(4) NOT NULL,
		  `type` tinyint(4) NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `name` (`name`),
		  KEY `status` (`status`),
		  KEY `type` (`type`),
		  KEY `owner` (`owner`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Video Whisper: Rooms - 2009@videowhisper.com' AUTO_INCREMENT=1 ;
		";

				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);

				if (!$installed_ver) add_option("vw2vc_db_version", $vw2vc_db_version);
				else update_option( "vw2vc_db_version", $vw2vc_db_version );

				$wpdb->flush();

			}


		}


		function v2wvc_callback()
		{

			function sanV(&$var, $file=1, $html=1, $mysql=1) //sanitize variable depending on use
				{
				if (!$var) return;

				if (get_magic_quotes_gpc()) $var = stripslashes($var);

				if ($file)
				{
					$var=preg_replace("/\.{2,}/","",$var); //allow only 1 consecutive dot
					$var=preg_replace("/[^0-9a-zA-Z\.\-\s_]/","",$var); //do not allow special characters
				}

				if ($html&&!$file)
				{
					$var=strip_tags($var);
					$forbidden=array("<", ">");
					foreach ($forbidden as $search)  $var=str_replace($search,"",$var);
				}

				if ($mysql&&!$file)
				{
					$forbidden=array("'", "\"", "´", "`", "\\", "%");
					foreach ($forbidden as $search)  $var=str_replace($search,"",$var);
					$var=mysql_real_escape_string($var);
				}
			}


			//ob_clean();

			switch ($_GET['task'])
			{
			case '2_login':

				$options = get_option('VWvideoChatOptions');

				$rtmp_server = $options['rtmp_server'];
				$rtmp_amf = $options['rtmp_amf'];
				$userName =  $options['userName']; if (!$userName) $userName='user_nicename';
				$canWatch = $options['canWatch'];
				$watchList = $options['watchList'];

				$webKey = $options['webKey'];

				$serverRTMFP = $options['serverRTMFP'];
				$p2pGroup = $options['p2pGroup'];
				$supportRTMP = $options['supportRTMP'];
				$supportP2P = $options['supportP2P'];
				$alwaystRTMP = $options['alwaystRTMP'];
				$alwaystP2P = $options['alwaystP2P'];
				$disableBandwidthDetection = $options['disableBandwidthDetection'];



				//room
				$room=$_GET['room_name'];
				sanV($room);

				$loggedin=0;
				$msg="";


				global $current_user;
				get_currentuserinfo();




				//if any key matches any listing
				function inList($keys, $data)
				{
					$list=explode(",",$data);

					foreach ($keys as $key)
						foreach ($list as $listing)
							if ( trim($key) == trim($listing) ) return 1;

							return 0;
				}

				//username
				if ($current_user->$userName) $username=urlencode($current_user->$userName);
				sanV($username);

				//access keys
				$userkeys = $current_user->roles;
				$userkeys[] = $current_user->user_login;
				$userkeys[] = $current_user->ID;
				$userkeys[] = $current_user->user_email;
				$userkeys[] = $current_user->display_name;

				switch ($canWatch)
				{
				case "all":
					$loggedin=1;
					if (!$username)
					{
						$username="VW".base_convert((time()-1224350000).rand(0,10),10,36);
						$visitor=1; //ask for username
					}
					break;
				case "members":
					if ($username) $loggedin=1;
					else $msg=urlencode("<a href=\"/\">Please login first or register an account if you don't have one! Click here to return to website.</a>");
					break;
				case "list";
					if ($username)
						if (inList($userkeys, $watchList)) $loggedin=1;
						else $msg=urlencode("<a href=\"/\">$username, you are not in the allowed watchers list.</a>");
						else $msg=urlencode("<a href=\"/\">Please login first or register an account if you don't have one! Click here to return to website.</a>");
						break;
				}


				if (!$room)
				{
					$loggedin=0;
					$message=urlencode("<a href=\"/\">Can't enter: Room missing!</a>");
				}

				if (!$username)
				{
					$loggedin=0;
					$message=urlencode("<a href=\"/\">Can't enter: Username missing!</a>");
				}

				if ($loggedin)
				{
					//this generates a session file record for rtmp login check
					$uploadsPath = $options['uploadsPath'];
					if (!$uploadsPath) $uploadsPath='uploads';

					sanV($username);

					if ($username)
					{
						$ztime=time();
						$info = "VideoWhisper=1&login=1&webKey=$webKey&start=$ztime&canKick=$canKick";

						$dir=$uploadsPath . "/";
						if (!file_exists($dir)) mkdir($dir);
						//@chmod($dir, 0777); 
						$dir.="/_sessions";
						if (!file_exists($dir)) mkdir($dir);
						//@chmod($dir, 0777); 

						$dfile = fopen($dir."/$username","w");
						fputs($dfile,$info);
						fclose($dfile);
						$debug = "$username-sessionCreated";
					}
				}

				//replace bad words or expression
				$filterRegex=urlencode("(?i)(fuck|cunt)(?-i)");
				$filterReplace=urlencode(" ** ");

function path2url($file, $Protocol='http://') {
    return $Protocol.$_SERVER['HTTP_HOST'].str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);
}
$uploadsPath = $options['uploadsPath'];
if (!$uploadsPath) $uploadsPath='uploads';

				$day=date("y-M-j",time());
				$chat=path2url($uploadsPath."/$room/Log$day.html");
				$chatlog="The transcript of this conversation, including snapshots is available at <U><A HREF=\"$chat\" TARGET=\"_blank\">$chat</A></U>.";

				if (!$welcome) $welcome="Welcome to $room! This will try to use P2P video streaming if possible between peers and stream trough server if that's not possible: use connection button to toggle if available. High quality snapshots of other person can be taken on request. $chatlog";

				//verboseLevel (higher reports more to user):
				//0 = Nothing
				//1 = Failure
				//2 = Warning / Recoverable Failure
				//3 = Success
				//4 = Action

				//layout obtained by sending in public chat box "/videowhisper layout"; fill in new line between layoutEND markers
				$layoutCode=<<<layoutEND
id=soundfx&x=766&y=571; id=bFul&x=15&y=105; id=VideoSlot2&x=510&y=140; id=ChatSlot&x=250&y=505; id=VideoSlot1&x=10&y=140; id=TextInput&x=250&y=670; id=head2&x=510&y=100; id=logo&x=389&y=25; id=bSnd&x=920&y=107; id=head&x=10&y=100; id=next&x=186&y=521; id=bVid&x=885&y=109; id=connection&x=186&y=571; id=bLogout&x=950&y=10; id=bFul2&x=955&y=105; id=bSwap&x=120&y=111; id=bSwap2&x=850&y=111; id=snapshot&x=766&y=621; id=camera&x=186&y=621; id=bCam&x=85&y=109; id=bMic&x=50&y=107; id=buzz&x=766&y=521
layoutEND;

				$chatTextColor = "#";
				for ($i=0;$i<3;$i++) $chatTextColor .= rand(0,70);

				?>fixOutput=decoy&server=<?php echo $rtmp_server?>&serverAMF=<?php echo $rtmp_amf?>&serverProxy=best&serverRTMFP=<?php echo $rtmfp_server?>&room=<?php echo urlencode($room)?>&welcome=<?php echo urlencode($welcome)?>&username=<?php echo $username?>&msg=<?php echo $message?>&loggedin=<?php echo $loggedin?>&showTimer=1&showCredit=1&disconnectOnTimeout=1&camWidth=320&camHeight=240&camFPS=15&camBandwidth=<?php echo $options['camBandwidth']?>&limitByBandwidth=1&camPicture=0&showCamSettings=1&camMaxBandwidth=<?php echo $options['camMaxBandwidth']?>&disableBandwidthDetection=<?php echo $disableBandwidthDetection?>&disableUploadDetection=<?php echo $disableBandwidthDetection?>&verboseLevel=4&disableVideo=0&disableSound=0&bufferLive=0.1&bufferFull=0.1&bufferLivePlayback=0.1&bufferFullPlayback=0.1&videoCodec=<?php echo $options['videoCodec']?>&codecProfile=<?php echo $options['codecProfile']?>&codecLevel=<?php echo $options['codecLevel']?>&soundCodec=<?php echo $options['soundCodec']?>&soundQuality=<?php echo $options['soundQuality']?>&micRate=<?php echo $options['micRate']?>&silenceLevel=0&silenceTimeout=0&micGain=50&filterRegex=<?php echo $filterRegex?>&filterReplace=<?php echo $filterReplace?>&disableEmoticons=0&showTextChat=1&sendTextChat=1&webfilter=0&enableP2P=<?php echo $supportP2P?>&enableServer=<?php echo $supportRTMP?>&configureConnection=1&configureSource=0&enableNext=0&enableBuzz=1&enableSoundFx=1&requestSnapshot=1&enableButtonLabels=1&enableFullscreen=1&enableSwap=1&enableLogout=1&enableLogo=1&enableHeaders=1&enableTitles=1&videoW=480&videoH=365&video2W=480&video2H=365&layoutCode=<?php echo urlencode($layoutCode)?>&chatTextColor=<?php echo $chatTextColor?>&autoSnapshots=1&snapshotsTime=20000&adServer=<?php echo urlencode($options['adServer'])?>&adsInterval=600000&adsTimeout=20000&loadstatus=1&ajax=
<?php
				break;
				// end 2_login

			case '2_status':

				$room=$_POST[r];
				$session=$_POST[s];
				$username=$_POST[u];

				$currentTime=$_POST[ct];
				$lastTime=$_POST[lt];

				$maximumSessionTime=0; //900000ms=15 minutes (in free mode this is forced)

				$redirect_url=urlencode(""); //disconnect and redirect to url
				$disconnect=urlencode(""); //disconnect with that message to standard disconnect page
				$message=urlencode(""); //show this message to user
				$send_message=urlencode(""); //user sends this message to room
				$next_room=urlencode(""); //user is moved to this room

				$s=$_POST['s'];
				$u=$_POST['u'];
				$r=$_POST['r'];
				$m=$_POST['m'];

				//sanitize variables
				sanV($s);
				sanV($u);
				sanV($r);
				sanV($m, 0, 0);

				//exit if no valid session name or room name
				if (!$s) exit;
				if (!$r) exit;

				global $wpdb;
				$table_name = $wpdb->prefix . "vw_2wsessions";
				$wpdb->flush();

				$ztime=time();

				$sql = "SELECT * FROM $table_name where session='$s' and status='1'";
				$session = $wpdb->get_row($sql);
				if (!$session)
				{
					$sql="INSERT INTO `$table_name` ( `session`, `username`, `room`, `message`, `sdate`, `edate`, `status`, `type`) VALUES ('$s', '$u', '$r', '$m', $ztime, $ztime, 1, 1)";
					$wpdb->query($sql);
				}
				else
				{
					$sql="UPDATE `$table_name` set edate=$ztime, room='$r', username='$u', message='$m' where session='$s' and status='1' LIMIT 1";
					$wpdb->query($sql);
				}

				$exptime=$ztime-30;
				$sql="DELETE FROM `$table_name` WHERE edate < $exptime";
				$wpdb->query($sql);


				?>timeTotal=<?php echo $maximumSessionTime?>&timeUsed=<?php echo $currentTime?>&lastTime=<?php echo $currentTime?>&disconnect=<?php echo $disconnect?>&message=<?php echo $message?>&send_message=<?php echo $send_message?>&redirect_url=<?php echo $redirect_url?>&loadstatus=1&ajax=<?php
				break;

			case 'chatfilter':
			$message = $_POST['m'];
$session=$_POST['s'];
$username=$_POST['u'];

$filtered = ucwords($message) . " (web filter test - ucwords)";
$filtered = urlencode($filtered);

?>m=<?php echo $filtered; ?>&ajax=<?php
				break;

			case 'vc_chatlog':
			//Public and private chat logs
$private=$_POST['private']; //private chat username, blank if public chat
$username=$_POST['u'];
$session=$_POST['s'];
$room=$_POST['r'];
$message=$_POST['msg'];
$time=$_POST['msgtime'];

$options = get_option('VWvideoChatOptions');
$uploadsPath = $options['uploadsPath'];
if (!$uploadsPath) $uploadsPath='uploads';

//do not allow uploads to other folders
sanV($room);
sanV($private);
sanV($session);
if (!$room) exit;

//generate same private room folder for both users
if ($private) 
{
	if ($private>$session) $proom=$session ."_". $private; else $proom=$private ."_". $session;
}

//create folder to store logs
$dir=$uploadsPath;
if (!file_exists($dir)) mkdir($dir);
//@chmod($dir, 0777); 
$dir.="/$room";
if (!file_exists($dir)) mkdir($dir);
//@chmod($dir, 0777); 
if ($proom) $dir.="/$proom";
if (!file_exists($dir)) mkdir($dir);
//@chmod($dir, 0777); 

$day=date("y-M-j",time());

$dfile = fopen($dir."/Log$day.html","a");
fputs($dfile,$message."<BR>");
fclose($dfile);
?>loadstatus=1&ajax=<?php
				break;

			case 'vc_snapshots':
if (isset($GLOBALS["HTTP_RAW_POST_DATA"]))
{
  $room=$_GET['room'];
  $stream=$_GET['name'];
  

  sanV($stream);
  sanV($room);
  if (!$stream) exit;
  if (!$room) exit;

  $options = get_option('VWvideoChatOptions');
  $uploadsPath = $options['uploadsPath'];
  if (!$uploadsPath) $uploadsPath='uploads';


  //create folder to store logs
  $dir=$uploadsPath;
  if (!file_exists($dir)) mkdir($dir);
  //@chmod($dir, 0777); 
  $dir.="/$room";
  if (!file_exists($dir)) mkdir($dir);
  //@chmod($dir, 0777); 

  // get bytearray
  $jpg = $GLOBALS["HTTP_RAW_POST_DATA"];

  // save file
  $filename=$stream.".".time().".jpg";
  $picture=$dir."/".$filename;
  $fp=fopen($picture,"w");
  if ($fp)
  {
    fwrite($fp,$jpg);
    fclose($fp);
  }
  
    //add it to chat log
    $message="<IMG SRC=\"$filename\" ALT=\"$stream\" TITLE=\"$stream\" ALIGN=\"RIGHT\">";
	//get daily log name
	$day=date("y-M-j",time());
	
	$chat=$dir."/Log$day.html";
	$dfile = fopen($chat,"a");
	fputs($dfile,$message."<BR>");
	fclose($dfile);
	
	$chat=urlencode($chat);
	$picture=urlencode($picture);
}
?>chat=<?=$chat?>&picture=<?=$pic?>&loadstatus=1&ajax=<?php
				break;

			case 'rtmp_login':
			//rtmp server should check login like rtmp_login.php?s=$session
$session = $_GET['s'];
sanV($session);
if (!$session) exit;
  
$options = get_option('VWvideoChatOptions');
$uploadsPath = $options['uploadsPath'];
if (!$uploadsPath) $uploadsPath='uploads';

$filename1 = uploadsPath."/_sessions/$session";
if (file_exists($filename1)) 
{
	echo implode('', file($filename1));
}
else 
{
	echo "VideoWhisper=1&login=0&ajax=";
}
				break;

			case 'rtmp_logout':
	//rtmp server notifies client disconnect here
$session = $_GET['s'];
sanV($session);
if (!$session) exit;

$options = get_option('VWvideoChatOptions');
$uploadsPath = $options['uploadsPath'];
if (!$uploadsPath) $uploadsPath='uploads';

echo "logout=";
$filename1 = $uploadsPath. "/_sessions/$session";
if (file_exists($filename1)) 
{
	echo unlink($filename1) .'&ajax=';
}
				break;


			case '2_ads':
			/* Sample local ads serving script ; Or use http://adinchat.com compatible ads server to setup http://adinchat.com/v/your-campaign-id

POST Variables:
u=Username
s=Session, usually same as username
r=Room
ct=session time (in milliseconds)
lt=last session time received from this script in (milliseconds)

*/

$room=$_POST[r];
$session=$_POST[s];
$username=$_POST[u];

$currentTime=$_POST[ct];
$lastTime=$_POST[lt];

$cam=$_POST['cam'];
$mic=$_POST['mic'];

$webcam=0;
if ($cam==2) $webcam=1;

$ztime=time();

//fill ad to show
$ad="<B>Sample Ad</B><BR>See <a href=\"http://www.adinchat.com\" target=\"_blank\"><U><B>AD in Chat</B></U></a> compatible ad management server.";

?>x=1&ad=<?=urlencode($ad)?>&loadstatus=1&ajax=<?php
				break;

			case '2_next':
			/*
This script implements a custom Next button function that can be used for various implementations.

POST Variables:
u=Username
s=Session, usually same as username
r=Room
cam, mic = 0 none, 1 disabled, 2 enabled
*/

$room=$_POST['r'];
$session=$_POST['s'];
$username=$_POST['u'];
$cam=$_POST['cam'];
$mic=$_POST['mic'];

$next_room="next_test";
$day=date("y-M-j",time());
$chat="uploads/$next_room/Log$day.html";	
$chatlog="The transcript of this conversation, including snapshots is available at <U><A HREF=\"$chat\" TARGET=\"_blank\">$chat</A></U>.";

//these produce actions if defined
$redirect_url=urlencode(""); //disconnect and redirect to url
$disconnect=urlencode(""); //disconnect with that message to standard disconnect page
$message=urlencode("Next button pressed. This feature can be programmed from 2_next.php or disabled from 2_login.php parameters. $chatlog"); //show this message to user
$send_message=urlencode("I pressed next."); //user sends this message to room
$next_room=urlencode($next_room); //user moves to this room
?>firstParameter=1&next_room=<?=$next_room?>&message=<?=$message?>&send_message=<?=$send_message?>&redirect_url=<?=$redirect_url?>&disconnect=<?=$disconnect?>&loadstatus=1&ajax=<?php
				break;

			case 'translations':
			?>
			<translations>
<t text="Successfully connected to RTMFP server." translation="Successfully connected to RTMFP server."/>
<t text="External Encoder" translation="External Encoder"/>
<t text="Toggle Sound Effects" translation="Toggle Sound Effects"/>
<t text="Buzz!" translation="Buzz!"/>
<t text="Swap Panels" translation="Swap Panels"/>
<t text="LogOut" translation="LogOut"/>
<t text="Sound Effects" translation="Sound Effects"/>
<t text="Toggle Audio" translation="Toggle Audio"/>
<t text="Camera" translation="Camera"/>
<t text="Toggle Video" translation="Toggle Video"/>
<t text="Next!" translation="Next!"/>
<t text="Server Connection" translation="Server Connection"/>
<t text="Microphone" translation="Microphone"/>
<t text="Server / P2P" translation="Server / P2P"/>
<t text="Entering room" translation="Entering room"/>
<t text="Successfully connected to RTMP server." translation="Successfully connected to RTMP server."/>
<t text="Connecting to RTMFP server." translation="Connecting to RTMFP server."/>
<t text="FullScreen" translation="FullScreen"/>
<t text="Toggle Microphone" translation="Toggle Microphone"/>
<t text="Toggle Webcam" translation="Toggle Webcam"/>
<t text="joined" translation="joined"/>
<t text="Save Photo in Logs" translation="Save Photo in Logs"/>
<t text="Translation XML was copied to clipboard. Just paste it in a text editor." translation="Translation XML was copied to clipboard. Just paste it in a text editor."/>
<t text="Snapshot" translation="Snapshot"/>
<t text="No Connection" translation="No Connection"/>
<t text="Webcam / External Encoder" translation="Webcam / External Encoder"/>
</translations>
			<?php
				break;
				
			case '2_logout':
			wp_redirect( home_url());
			exit;
			break;

			default:
				echo $_GET['task'] . '&ajax=';
			}

			//die();
		}





		function videochat_room()
		{

?>

		<script language="JavaScript">
		function censorName()
			{
				document.adminForm.room.value = document.adminForm.room.value.replace(/^[\s]+|[\s]+$/g, '');
				document.adminForm.room.value = document.adminForm.room.value.replace(/[^0-9a-zA-Z_\-]+/g, '-');
				document.adminForm.room.value = document.adminForm.room.value.replace(/\-+/g, '-');
				document.adminForm.room.value = document.adminForm.room.value.replace(/^\-+|\-+$/g, '');
				if (document.adminForm.room.value.length>0) return true;
				else
				{
				alert("A room name is required!");
				document.adminForm.button.disabled=false;
				document.adminForm.button.value="Create";
				return false;
				}
			}
			</script>

		<?php

			global $wpdb;

			$this_page    =   $_SERVER['REQUEST_URI'];

			//can user create room?
			$options = get_option('VWvideoChatOptions');
			$canBroadcast = $options['canBroadcast'];
			$broadcastList = $options['broadcastList'];
			$userName =  $options['userName']; if (!$userName) $userName='user_nicename';

			$loggedin=0;

			global $current_user;
			get_currentuserinfo();
			if ($current_user->$userName) $username=urlencode($current_user->$userName);

			//if any key matches any listing
			function inList($keys, $data)
			{
				$list=explode(",",$data);

				foreach ($keys as $key)
					foreach ($list as $listing)
						if ( trim($key) == trim($listing) ) return 1;

						return 0;
			}

			//access keys
			$userkeys = $current_user->roles;
			$userkeys[] = $current_user->user_login;
			$userkeys[] = $current_user->ID;
			$userkeys[] = $current_user->user_email;
			$userkeys[] = $current_user->display_name;

			switch ($canBroadcast)
			{
			case "members":
				if ($username) $loggedin=1;
				else echo "<a href=\"/\">Please login first or register an account if you don't have one!</a>";
				break;
			case "list";
				if ($username)
					if (inList($userkeys, $broadcastList)) $loggedin=1;
					else echo "<a href=\"/\">$username, you are not allowed to setup rooms.</a>";
					else echo "<a href=\"/\">Please login first or register an account if you don't have one!</a>";
					break;
			}


			if (!$loggedin)
				{?>
<p>This pages allows creating and managing video chat rooms for register members that have this feature enabled.
</p>
<?php
			}

			if ($loggedin)
			{
				$table_name = $wpdb->prefix . "vw_2wsessions";
				$table_name3 = $wpdb->prefix . "vw_2wrooms";

				//delete
				if ($delid=(int) $_GET['delete'])
				{
					$sql = $wpdb->prepare("DELETE FROM $table_name3 where owner='".$current_user->ID."' AND id='%d'", array($delid));
					$wpdb->query($sql);
					$wpdb->flush();
					echo "<div class='update'>Room #$delid was deleted.</div>";
				}

				//create
				if ($room = $_POST['room'])
				{
					global $wpdb;
					$wpdb->flush();
					$ztime=time();

					$sql = $wpdb->prepare("SELECT owner FROM $table_name3 where name='%s'", array($room));
					$rdata = $wpdb->get_row($sql);
					if (!$rdata)
					{
						$sql=$wpdb->prepare("INSERT INTO `$table_name3` ( `name`, `owner`, `sdate`, `edate`, `status`, `type`) VALUES ('%s', '".$current_user->ID."', '$ztime', '0', 1, 1)",array($room));
						$wpdb->query($sql);
						$wpdb->flush();
						echo "<div class='update'>Room '$room' was created.</div>";
					}
					else
					{
						echo "<div class='error'>Room name '$room' is already in use. Please choose another name!</div>";
						$room="";
					}
				}

function path2url($file, $Protocol='http://') {
    return $Protocol.$_SERVER['HTTP_HOST'].str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);
}

				//list
				$wpdb->flush();
				$sql = "SELECT * FROM $table_name3 where owner='".$current_user->ID."'";
				$rooms=$wpdb->get_results($sql);

				echo "<H3>My Rooms</H3>";
				if (count($rooms))
				{
					echo "<table>";
					echo "<tr><th>Room</th><th>Link</th><th>Online</th><th>Manage</th></tr>";
					$root_url = plugins_url() . "/";
					foreach ($rooms as $rd)
					{
						$rm=$wpdb->get_row("SELECT count(*) as no, group_concat(username separator ' <BR> ') as users, room as room FROM `$table_name` where status='1' and type='1' AND room='".$rd->name."' GROUP BY room");

						echo "<tr> <td><a href='" . $root_url ."webcam-2way-videochat/2wvc/?r=".urlencode($rd->name)."'><B>".$rd->name."</B></a></td> <td>" . $root_url ."webcam-2way-videochat/2wvc/?r=".urlencode($rd->name)."</td> <td>".($rm->no>0?$rm->users:'0')."</td> <td><a href='".$this_page.(strstr($this_page,'?')?'&':'?')."delete=".$rd->id."'>Delete</a> <BR><a href='" . path2url($options['uploadsPath']).'/'.urlencode($rd->name)."/'>Logs</a> </td> </tr>";
					}
					echo "</table>";

				}
				else echo "You don't currently have any rooms.";


				//create form
				if (!$room)
					echo '<h3>Setup a New Room</h3><form method="post" action="' . $this_page .'"  name="adminForm">
		  <input name="room" type="text" id="room" value="Room_'.base_convert((time()-1225000000).rand(0,10),10,36).'" size="20" maxlength="64" onChange="censorName()"/>
  <input type="submit" name="button" id="button" value="Create" onclick="censorName(); adminForm.submit();"/>
		</form>
		';
			}

		}


		function updatePages()
		{

			global $user_ID;
			$page = array();
			$page['post_type']    = 'page';
			$page['post_content'] = '[videowhisper_videochat_manage]';
			$page['post_parent']  = 0;
			$page['post_author']  = $user_ID;
			$page['post_status']  = 'publish';
			$page['post_title']   = 'Video Chat';

			$page_id = get_option("vw_2vc_page_room");
			if ($page_id>0) $page['ID'] = $page_id;

			$pageid = wp_insert_post ($page);

			update_option( "vw_2vc_page_room", $pageid);
		}

		function deletePages()
		{
			$page_id = get_option("vw_2vc_page_room");
			if ($page_id > 0)
			{
				wp_delete_post($page_id);
				update_option( "vw_2vc_page_room", -1);
			}
		}

		function widgetContent()
		{
			global $wpdb;
			$table_name = $wpdb->prefix . "vw_2wsessions";

			$root_url = plugins_url();

			//clean recordings
			$exptime=time()-30;
			$sql="DELETE FROM `$table_name` WHERE edate < $exptime";
			$wpdb->query($sql);
			$wpdb->flush();

			$items =  $wpdb->get_results("SELECT count(*) as no, group_concat(username separator ', ') as users, room as room FROM `$table_name` where status='1' and type='1' GROUP BY room");

			echo "<ul>";

			if ($items)
				foreach ($items as $item)
				{
					if ($item->no<2) echo "<li><a href='" . $root_url ."webcam-2way-videochat/2wvc/?r=".urlencode($item->room)."'><B>".$item->room."</B> (".($item->users).") ".($item->message?": ".$item->message:"") ."</a></li>";
					else echo "<li><B>".$item->room."</B> (".($item->users).") ".($item->message?": ".$item->message:"") ."</li>";
				}
			else echo "<li>No users online.</li>";
			echo "</ul>";

?>
	<?php

			$options = get_option('VWvideoChatOptions');
			$state = 'block' ;
			if (!$options['videowhisper']) $state = 'none';
			echo '<div id="VideoWhisper" style="display: ' . $state . ';"><p>Powered by VideoWhisper <a href="http://www.videowhisper.com/?p=WordPress-Webcam-2Way-VideoChat">WordPress VideoChat</a>.</p></div>';
		}

		function widget($args) {
			extract($args);
			echo $before_widget;
			echo $before_title;?>Video Chat<?php echo $after_title;
			VWvideoChat::widgetContent();
			echo $after_widget;
		}

		function menu() {
			add_options_page('VideoChat Options', 'Video Chat', 9, basename(__FILE__), array('VWvideoChat', 'options'));
		}

		function getAdminOptions() {

			$upload_dir = wp_upload_dir();
			
			//$root_url = plugins_url();
			//$root_path = plugin_dir_path( __FILE__ );
			$root_ajax = admin_url( 'admin-ajax.php?action=v2wvc&task=');

			$adminOptions = array(
				'userName' => 'display_name',
				'rtmp_server' => 'rtmp://localhost/videowhisper',
				'rtmp_amf' => 'AMF3',

				'canBroadcast' => 'members',
				'broadcastList' => 'Super Admin, Administrator, Editor, Author',
				'canWatch' => 'all',
				'watchList' => 'Super Admin, Administrator, Editor, Author, Contributor, Subscriber',
				'onlyVideo' => '0',
				'noEmbeds' => '0',

				'camBandwidth' => '40960',
				'camMaxBandwidth' => '81920',

				'videoCodec'=>'H264',
				'codecProfile' => 'main',
				'codecLevel' => '3.1',

				'soundCodec'=> 'Speex',
				'soundQuality' => '9',
				'micRate' => '22',

				'overLogo' => $root_url .'webcam-2way-videochat/2wvc/logo.png',
				'overLink' => 'http://www.videowhisper.com',

				'tokenKey' => 'VideoWhisper',
				'webKey' => 'VideoWhisper',

				'serverRTMFP' => 'rtmfp://stratus.adobe.com/f1533cc06e4de4b56399b10d-1a624022ff71/',
				'p2pGroup' => 'VideoWhisper',
				'supportRTMP' => '1',
				'supportP2P' => '0',
				'alwaysRTMP' => '0',
				'alwaysP2P' => '0',
				'disableBandwidthDetection' => '1',
				'videowhisper' => 0,
				'disablePage' => '0',
				'uploadsPath' => $upload_dir['basedir'] . '/2wvc',
				'adServer' => $root_ajax .'2_ads'
			);

			$options = get_option('VWvideoChatOptions');
			if (!empty($options)) {
				foreach ($options as $key => $option)
					$adminOptions[$key] = $option;
			}
			update_option('VWvideoChatOptions', $adminOptions);
			return $adminOptions;
		}

		function options()
		{
		
			$options = VWvideoChat::getAdminOptions();

			if (isset($_POST['updateSettings']))
			{
				if (isset($_POST['rtmp_server'])) $options['rtmp_server'] = $_POST['rtmp_server'];
				if (isset($_POST['noEmbeds'])) $options['noEmbeds'] = $_POST['noEmbeds'];
				if (isset($_POST['onlyVideo'])) $options['onlyVideo'] = $_POST['onlyVideo'];
				if (isset($_POST['userName'])) $options['userName'] = $_POST['userName'];

				if (isset($_POST['canBroadcast'])) $options['canBroadcast'] = $_POST['canBroadcast'];
				if (isset($_POST['broadcastList'])) $options['broadcastList'] = $_POST['broadcastList'];
				if (isset($_POST['canWatch'])) $options['canWatch'] = $_POST['canWatch'];
				if (isset($_POST['watchList'])) $options['watchList'] = $_POST['watchList'];


				if (isset($_POST['camBandwidth'])) $options['camBandwidth'] = $_POST['camBandwidth'];
				if (isset($_POST['camMaxBandwidth'])) $options['camMaxBandwidth'] = $_POST['camMaxBandwidth'];


				if (isset($_POST['videoCodec'])) $options['videoCodec'] = $_POST['videoCodec'];
				if (isset($_POST['codecProfile'])) $options['codecProfile'] = $_POST['codecProfile'];
				if (isset($_POST['codecLevel'])) $options['codecLevel'] = $_POST['codecLevel'];

				if (isset($_POST['soundCodec'])) $options['soundCodec'] = $_POST['soundCodec'];
				if (isset($_POST['soundQuality'])) $options['soundQuality'] = $_POST['soundQuality'];
				if (isset($_POST['micRate'])) $options['micRate'] = $_POST['micRate'];

				if (isset($_POST['overLogo'])) $options['overLogo'] = $_POST['overLogo'];
				if (isset($_POST['overLink'])) $options['overLink'] = $_POST['overLink'];

				if (isset($_POST['tokenKey'])) $options['tokenKey'] = $_POST['tokenKey'];
				if (isset($_POST['webKey'])) $options['webKey'] = $_POST['webKey'];

				if (isset($_POST['serverRTMFP'])) $options['serverRTMFP'] = $_POST['serverRTMFP'];
				if (isset($_POST['p2pGroup'])) $options['p2pGroup'] = $_POST['p2pGroup'];
				if (isset($_POST['supportRTMP'])) $options['supportRTMP'] = $_POST['supportRTMP'];
				if (isset($_POST['supportP2P'])) $options['supportP2P'] = $_POST['supportP2P'];
				if (isset($_POST['alwaystRTMP'])) $options['alwaystRTMP'] = $_POST['alwaystRTMP'];
				if (isset($_POST['alwaystP2P'])) $options['alwaystP2P'] = $_POST['alwaystP2P'];
				if (isset($_POST['disableBandwidthDetection'])) $options['disableBandwidthDetection'] = $_POST['disableBandwidthDetection'];
				if (isset($_POST['videowhisper'])) $options['videowhisper'] = $_POST['videowhisper'];
				if (isset($_POST['disablePage'])) $options['disablePage'] = $_POST['disablePage'];
				if (isset($_POST['uploadsPath'])) $options['uploadsPath'] = $_POST['uploadsPath'];
				if (isset($_POST['adServer'])) $options['adServer'] = $_POST['adServer'];
				
				$page_id = get_option("vw_2vc_page_room");
				if ($page_id != '-1' && $options['disablePage']!='0') VWvideoChat::deletePages();

				update_option('VWvideoChatOptions', $options);
			}

?>
<div class="wrap">
<div id="icon-options-general" class="icon32"><br></div>
<h2>VideoWhisper Webcam 2 Way VideoChat Settings</h2>
</div>

<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">

<h3>Streaming Server</h3>
<h5>RTMP Address</h5>
<p>To run this, make sure your hosting environment meets all <a href="http://www.videowhisper.com/?p=Requirements" target="_blank">requirements</a>.  If you don't have a videowhisper rtmp address yet (from a managed rtmp host), go to <a href="http://www.videowhisper.com/?p=RTMP+Applications" target="_blank">RTMP Application   Setup</a> for  installation details.</p>
<input name="rtmp_server" type="text" id="rtmp_server" size="80" maxlength="256" value="<?php echo $options['rtmp_server']?>"/>*
<br>This is usually the only setting you really need to fill to run this plugin.

<h5>Disable Bandwidth Detection</h5>
<p>Required on some rtmp servers that don't support bandwidth detection and return a Connection.Call.Fail error.</p>
<select name="disableBandwidthDetection" id="disableBandwidthDetection">
  <option value="0" <?php echo $options['disableBandwidthDetection']?"":"selected"?>>No</option>
  <option value="1" <?php echo $options['disableBandwidthDetection']?"selected":""?>>Yes</option>
</select>

<h5>Web Key</h5>
<input name="webKey" type="text" id="webKey" size="32" maxlength="64" value="<?php echo $options['webKey']?>"/>
<BR>A web key can be used for <a href="http://www.videochat-scripts.com/videowhisper-rtmp-web-authetication-check/">VideoWhisper RTMP Web Session Check</a>.
<?php
			$root_ajax = admin_url( 'admin-ajax.php?action=v2wvc&task=');
			//$root_url = plugins_url() . "/webcam-2way-videochat/2wvc/";
			echo "<BR>webLogin: $root_ajax"."rtmp_login&s=";
			echo "<BR>webLogout: $root_ajax"."rtmp_logout&s=";
?>


<h5>RTMFP Address</h5>
<p> Get your own independent RTMFP address by registering for a free <a href="https://www.adobe.com/cfusion/entitlement/index.cfm?e=cirrus" target="_blank">Adobe Cirrus developer key</a>. This is required for P2P support.</p>
<input name="serverRTMFP" type="text" id="serverRTMFP" size="80" maxlength="256" value="<?php echo $options['serverRTMFP']?>"/>
<h5>P2P Group</h5>
<input name="p2pGroup" type="text" id="p2pGroup" size="32" maxlength="64" value="<?php echo $options['p2pGroup']?>"/>
<h5>Support RTMP Streaming</h5>
<select name="supportRTMP" id="supportRTMP">
  <option value="0" <?php echo $options['supportRTMP']?"":"selected"?>>No</option>
  <option value="1" <?php echo $options['supportRTMP']?"selected":""?>>Yes</option>
</select>
<h5>Support P2P Streaming</h5>
<select name="supportP2P" id="supportP2P">
  <option value="0" <?php echo $options['supportP2P']?"":"selected"?>>No</option>
  <option value="1" <?php echo $options['supportP2P']?"selected":""?>>Yes</option>
</select>



<h3>General Settings</h3>
<h5>Username</h5>
<select name="userName" id="userName">
  <option value="display_name" <?php echo $options['userName']=='display_name'?"selected":""?>>Display Name</option>
  <option value="user_login" <?php echo $options['userName']=='user_login'?"selected":""?>>Login (Username)</option>
  <option value="user_nicename" <?php echo $options['userName']=='user_nicename'?"selected":""?>>Nicename</option>
</select>

<h5>Page</h5>
<p>Add videochat management page (Page ID <a href='post.php?post=<?php echo get_option("vw_2vc_page_room"); ?>&action=edit'><?php echo get_option("vw_2vc_page_room"); ?></a>) with shortcode [videowhisper_videochat_manage]</p>
<select name="disablePage" id="disablePage">
  <option value="0" <?php echo $options['disablePage']=='0'?"selected":""?>>Yes</option>
  <option value="1" <?php echo $options['disablePage']=='1'?"selected":""?>>No</option>
</select>


<h5>Show VideoWhisper Powered by</h5>
<select name="videowhisper" id="videowhisper">
  <option value="0" <?php echo $options['videowhisper']?"":"selected"?>>No</option>
  <option value="1" <?php echo $options['videowhisper']?"selected":""?>>Yes</option>
</select>

<h5>Uploads Path</h5>
<p>Path where logs and snapshots will be uploaded. You can use a location outside plugin folder to avoid losing logs on updates and plugin uninstallation.</p>
<input name="uploadsPath" type="text" id="uploadsPath" size="80" maxlength="256" value="<?php echo $options['uploadsPath']?>"/>

<h5>Ad Server</h5>
<p>URL to serve ads from in chatbox. See <a href='http://adinchat.com/'>Ad in Chat</a> ads server and rotator.</p>
<input name="adServer" type="text" id="adServer" size="80" maxlength="256" value="<?php echo $options['adServer']?>"/>


<h3>Streaming Settings</h3>

<h5>Video Stream Size</h5>
<input name="camBandwidth" type="text" id="camBandwidth" size="8" maxlength="8" value="<?php echo $options['camBandwidth']?>"/> (bytes/s) Higher bandwidth means higher quality but must be supported by client connection.
<h5>Maximum Video Stream Size</h5>
<input name="camMaxBandwidth" type="text" id="camMaxBandwidth" size="8" maxlength="8" value="<?php echo $options['camMaxBandwidth']?>"/> (bytes/s) Maximum bandwidth that can be configured at runtime.

<h5>Video Codec</h5>
<select name="videoCodec" id="videoCodec">
  <option value="H264" <?php echo $options['videoCodec']=='H264'?"selected":""?>>H264</option>
  <option value="H263" <?php echo $options['videoCodec']=='H263'?"selected":""?>>H263</option>
</select>

<h5>H264 Video Codec Profile</h5>
<select name="codecProfile" id="codecProfile">
  <option value="main" <?php echo $options['codecProfile']=='main'?"selected":""?>>main</option>
  <option value="baseline" <?php echo $options['codecProfile']=='baseline'?"selected":""?>>baseline</option>
</select>

<h5>H264 Video Codec Level</h5>
<input name="codecLevel" type="text" id="codecLevel" size="32" maxlength="64" value="<?php echo $options['codecLevel']?>"/> (1, 1b, 1.1, 1.2, 1.3, 2, 2.1, 2.2, 3, 3.1, 3.2, 4, 4.1, 4.2, 5, 5.1)

<h5>Sound Codec</h5>
<select name="soundCodec" id="soundCodec">
  <option value="Speex" <?php echo $options['soundCodec']=='Speex'?"selected":""?>>Speex</option>
  <option value="Nellymoser" <?php echo $options['soundCodec']=='Nellymoser'?"selected":""?>>Nellymoser</option>
</select>

<h5>Speex Sound Quality</h5>
<input name="soundQuality" type="text" id="soundQuality" size="3" maxlength="3" value="<?php echo $options['soundQuality']?>"/> (0-10)

<h5>Nellymoser Sound Rate</h5>
<input name="micRate" type="text" id="micRate" size="3" maxlength="3" value="<?php echo $options['micRate']?>"/> (11/22/44)

<h3>Room Setup</h3>
<h5>Who can create rooms</h5>
<select name="canBroadcast" id="canBroadcast">
  <option value="members" <?php echo $options['canBroadcast']=='members'?"selected":""?>>All Members</option>
  <option value="list" <?php echo $options['canBroadcast']=='list'?"selected":""?>>Members in List *</option>
</select>
<h5>* Members in List: allowed to broadcast video (comma separated user names, roles, emails, IDs)</h5>
<textarea name="broadcastList" cols="64" rows="3" id="broadcastList"><?php echo $options['broadcastList']?>
</textarea>

<h3>Participants</h3>
<h5>Who can enter videochat</h5>
<select name="canWatch" id="canWatch">
  <option value="all" <?php echo $options['canWatch']=='all'?"selected":""?>>Anybody</option>
  <option value="members" <?php echo $options['canWatch']=='members'?"selected":""?>>All Members</option>
  <option value="list" <?php echo $options['canWatch']=='list'?"selected":""?>>Members in List *</option>
</select>
<h5>* Members in List: Allowed to participate (comma separated user names, roles, emails, IDs)</h5>
<textarea name="watchList" cols="64" rows="3" id="watchList"><?php echo $options['watchList']?>
</textarea>

<div class="submit">
  <input type="submit" name="updateSettings" id="updateSettings" value="<?php _e('Update Settings', 'VWvideoChat') ?>" />
</div>

</form>

	 <?php
		}

	}
}

//instantiate
if (class_exists("VWvideoChat"))
{
	$videoChat = new VWvideoChat();
}

//Actions and Filters
if (isset($videoChat))
{
	add_action("plugins_loaded", array(&$videoChat, 'init'));
	add_action('admin_menu', array(&$videoChat, 'menu'));
}



?>
