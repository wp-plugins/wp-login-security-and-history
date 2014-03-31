<?
	
	$options= WPS_my_options();
	
	if($_POST['can_show_captcha_option']!='')
	{
		$saved_options = "Options Saved! ";
		$newoptions['can_show_captcha_option']= intval($_POST['can_show_captcha_option']);
		$newoptions['can_block_login_trials']= intval($_POST['can_block_login_trials']);
		$newoptions['login_blocked_msg']= $_POST['login_blocked_msg'];
		$newoptions['show_captcha_count_option']= intval($_POST['show_captcha_count_option']);
		
		if( intval($_POST['login_max_trials']) >= 3)
		{
		 $newoptions['login_max_trials']= intval($_POST['login_max_trials']);
		}else
		{
		 $newoptions['login_max_trials']=$options['login_max_trials'];
		 $saved_options = $saved_options . "<br/>Note that 'Login Blocker Trials' does not changed because it must be 3 or greater!";
		}
		

		if( intval($_POST['login_block_time']) >= 3)
		{
		 $newoptions['login_block_time']= intval($_POST['login_block_time']);
		}else
		{
		 $newoptions['login_block_time']=$options['login_block_time'];
		 $saved_options = $saved_options . "<br/>Note that 'Login Blocker Time' does not changed because it must be 3 or greater!";
		}	
			
		
		WPS_update_my_options($newoptions);
		WPS_option_msg($saved_options);
	}
	
	
	$options= WPS_my_options();
	
	
?>

<form method="POST">
	<h3>Login Captcha Options<hr/></h3>
	
	Login Captcha:
	<select size="1" name="can_show_captcha_option" id="can_show_captcha_option">
	<option value="1">Enabled</option>
	<option value="0">Disabled</option>
	</select>
	<br/>
	Show Captcha After
		<input type="text" name="show_captcha_count_option" id="show_captcha_count_option" size="10" value="<?=$options['show_captcha_count_option']?>"> 
	Login Trials <small><font color="#999999">(ex 1,2,3,.. put 0 to show captcha every time)</font></small><br/>
	<br/><br/>

	<h3>Login Blocker Options<hr/></h3>
	
	Login Blocker:
	<select size="1" name="can_block_login_trials" id="can_block_login_trials">
	<option value="1">Enabled</option>
	<option value="0">Disabled</option>
	</select>
	<br/>
	Block Login After
	<input type="text" name="login_max_trials" id="login_max_trials" size="10" value="<?=$options['login_max_trials']?>"> Login Trials <small> 
	<font color="#999999">(3 or greater)</font></small> 
	<br/>Login Blocker Time &nbsp;<input type="text" name="login_block_time" id="login_block_time" size="10" value="<?=$options['login_block_time']?>"> 
	Minutes <font color="#999999"> <small> (3 or greater)</small></font><br/>
	Blocker page Message
	<input type="text" name="login_blocked_msg" id="login_blocked_msg" size="108" value="<?=$options['login_blocked_msg']?>">
		
	<script>
	document.getElementById('can_block_login_trials').value = "<?=$options['can_block_login_trials']?>";
	document.getElementById('can_show_captcha_option').value = "<?=$options['can_show_captcha_option']?>";
	</script>
	
	<br/><br/><br/>
	<input  class="button-primary" type="submit" value="  Update Options  " name="Save_Options">

</form>