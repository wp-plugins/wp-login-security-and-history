<?
include_once "controls.php";
global $wpdb,$table_prefix; 

if($_POST['clear_login_history']!='')
	{
	 $table_name = $table_prefix . 'user_login_history';
	 $sql=" TRUNCATE TABLE $table_name";
	 $wpdb->query($sql);
	}
?>
<form method="POST">
<?

 
 $images_path  = WPS_get_url_path() . "images/";
 
 	$grid = new datagrid();
	$grid->set_data_source($table_prefix . 'user_login_history');
	$grid->add_select_field('Status');
	$grid->set_order('ID desc');
	$grid->pagination->set_rows(15);
	
	$grid->set_table_attr('width','100%');

	$grid->set_col_attr(1,'align','center');
	//$grid->set_col_attr(3,'width','100');
	
	$grid->add_html_col("<img width='18px' height='18px' src='".$images_path . "{db_Status}.gif' />",'Status');
	$grid->add_data_col('Username','Username');
	$grid->add_data_col('Name','Name');
	$grid->add_data_col('Usertype','User type');
	$grid->add_data_col('Sinon_time','Date & Time');
	$grid->add_data_col('logged_out_time','Logged out');
	
	$grid->add_data_col('Country','Country');
	$grid->add_data_col('IP','IP');
	$grid->add_data_col('OS','OS');
		
	$grid->add_data_col('Browser','Browser');
	

	$grid->run();
	
	
 
 	
?>
<input class="button-primary" type="submit" value="  Clear History  " name="clear_login_history">
</form>