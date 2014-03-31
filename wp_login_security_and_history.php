<?php
/*
Plugin Name: WP Login Security and History
Plugin URI: http://www.clogica.com
Description: By this smart plugin you can protect your login page from Brute-force attacks also you can track login history.  
Author: Fakhri Alsadi
Version: 1.0
Author URI: http://www.clogica.com
*/

session_start();

define( 'WPS_OPTIONS', 'wps-options-group' );

require_once ('functions.php');


add_action('login_form', 'WPS_show_captcha');
add_filter('wp_authenticate_user', 'WPS_captcha_check' ,10,2);
add_action( 'wp_login_failed', 'WPS_login_failed' );
add_action( 'wp_login', 'WPS_login_success', 10, 2 );
add_action( 'wp_logout', 'WPS_logout' );
add_action( 'login_head', 'WPS_check_block_login' );
add_action('admin_menu', 'WPS_admin_menu');
add_action('admin_head', 'header_code');

register_activation_hook( __FILE__ , 'WPS_install' );
register_uninstall_hook( __FILE__ , 'WPS_uninstall' );
 
wp_register_style( 'WPS_admin_css_f', WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)) . '/' . "style.css" );


//----------------------------------------------------

function WPS_admin_menu() {
	add_options_page('WP Login Security and History', 'WP Login Security & History', 10, basename(__FILE__), 'WPS_options_menu'  );
}
//----------------------------------------------------
function WPS_options_menu() {
	
	if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		
	$options_path= get_current_parameters('tab');	
	
	if($_GET['tab']=='')
	{
		$_GET['tab']=1;
	}
	
	echo '<div class="wrap"><h2>WP Login Security and History</h2><br/>';
	echo '<ul class="tabs">';
	
	if($_GET['tab']==1)
  	echo '<li class="active"><a href="' . $options_path . '&tab=1">Login History</a></li>';
  	else
  	echo '<li><a href="' . $options_path . '&tab=1">Login History</a></li>';
  	
  	if($_GET['tab']==2)
    	echo '<li class="active"><a href="' . $options_path . '&tab=2">Login Security Options</a></li>';
    	else
    	echo '<li><a href="' . $options_path . '&tab=2">Login Security Options</a></li>';

echo '</ul>
 <div class="tabContainer">
   	<div id="tab1" class="tabContent">';
   
	$page_num=intval($_GET['tab']);
	include "option_page" . $page_num . ".php";  
    	
echo '</div>
    <!-- / END #tab1 -->
    
 </div>
 <!-- / END .tabContainer -->'; 
	

	
	echo '</div>';
}
//----------------------------------------------------

function header_code()
{
	wp_enqueue_style('WPS_admin_css_f');
}

//----------------------------------------------------

function WPS_show_captcha()
{

		if(WPS_is_captcha()){
			echo '
			<label>Captcha <br/>
			<script>
			function caprefresh()
			{
					document.getElementById("imgcap").src="' . WPS_get_url_path() . 'captcha.php?r="+Math.random() ;
			}
			</script>
			<table border="0" width="100%" id="table1" cellpadding="0" style="border-collapse: collapse;  margin-top:3px;">
				<tr>
					<td valign="middle" width="75"><img id="imgcap" src="' . WPS_get_url_path() . 'captcha.php" ></td>
				<td valign="middle" width="25" align="center" ><a href="javascript:caprefresh();"><img id="imgref" src="' . WPS_get_url_path() . 'images/refresh.gif"  border="0" ></a></td>
				<td valign="middle" style="padding-left:5px;" > <input type="text" name="captcha" id="captcha" size="20" tabindex="21" style="height: 20px; width: 80px; margin-top:3px; font-size:14px" ></td>
				</tr>
			</table></label><br/><br/>';				
		}
}


//----------------------------------------------------

function WPS_error_shake( $shake_codes ){
 
     $shake_codes[] = 'denied';
     return $shake_codes;
}

//----------------------------------------------------

function WPS_captcha_check($user, $password )
{
	
	if (!is_a($user, 'WP_User')) { 
		return $user; 
	}
	
	
		
	
	    if(isset($_POST['captcha'])){
    
	    if($_POST['captcha']!=$_SESSION['capkey'])
		{
	        $error = new WP_Error( 'denied', __("<strong>ERROR</strong>: Please input the five characters shown in the image correctly!") );
	        return $error;
	        
	     }
     }
	
	
   return $user;
		
} 

//----------------------------------------------------

function WPS_login_failed($username )
{
	WPS_add_login_row($username,0);	
	WPS_increment_show_captcha_option();
} 

//----------------------------------------------------

function WPS_login_success($username)
{

	WPS_add_login_row($username,1);
	
} 

//----------------------------------------------------

function WPS_logout()
{
	global $wpdb,$table_prefix,$current_user;
    get_currentuserinfo();
    $username=$current_user->user_login;
	$table_name = $table_prefix . 'user_login_history';
	$sql=" select ID from $table_name where Username='$username' and Status=1 order by ID desc limit 1 ; ";
	$results = $wpdb->get_results($sql);
	$result=$results[0];
	$ID = $result->ID;	
	if($ID)	
	{
		$sql=" update $table_name set logged_out_time=NOW() where ID=$ID ; ";
		$wpdb->query($sql);
	}
	
} 

//----------------------------------------------------

function WPS_check_block_login()
{
	if(WPS_is_blocked()){
			echo '<br/><br/><br/><br/><br/><br/><br/><br/><br/><p align="center"><img border="0" src="' . WPS_get_url_path() . 'images/warning.gif" align="middle"  >&nbsp;' . WPS_login_blocked_msg() . '</p>';
	exit(0);
	}
} 

//---------------------------------------------------------------

function WPS_install(){
	global $wpdb,$table_prefix ; 	
	$table_name = $table_prefix . 'user_login_history';
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			$sql = "CREATE TABLE `$table_name` (
					`ID` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					`Username` VARCHAR( 255 ) NOT NULL ,
					`Name` VARCHAR( 255 ) NOT NULL ,
					`Usertype` VARCHAR( 255 ) NOT NULL ,
					`Sinon_time` DATETIME NOT NULL ,
					`long_time` INT( 20 ) UNSIGNED NOT NULL ,
					`IP` VARCHAR( 20 ) NOT NULL ,
					`Country` VARCHAR(100) NOT NULL,
					`Status` INT( 1 ) NOT NULL DEFAULT '0',
					`logged_out_time` DATETIME NULL ,
					`OS` VARCHAR( 255 ) NOT NULL ,
					`Browser` VARCHAR( 255 ) NOT NULL
					);";
			$wpdb->query($sql);
		}
	
}


//---------------------------------------------------------------

function WPS_uninstall(){

	global $wpdb,$table_prefix; 
	$table_name = $table_prefix . 'user_login_history';
	$sql = " DROP TABLE `$table_name` ";
	$wpdb->query($sql);
}

//---------------------------------------------------------------







?>