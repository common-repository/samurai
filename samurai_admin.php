<?php
	// 1.0.8 Added function to display images. 
	if(!defined('ABSPATH')) exit;	// Exit if accessed directly
	if(!current_user_can('administrator')) exit;  // Security check
	
	$Max_Cnt = 20;				//Max Item
	$db_name = 'samurai';		//Database Name
	$Samurai_Url = plugins_url( "", __FILE__ ) . '/';	//Plugin Path
	$Add_Delete_Cmd = 0;
	$Nn_actin = 'samurai_nonce_action_negi';
	$Nn_Name  = 'samurai_nonce_wpnonce_negi';
	//If it fails, check_admin_referer() function will automatically display the "failed" page and die.
	if (!(!empty($_POST) && check_admin_referer($Nn_actin,$Nn_Name))) {}
	if(isset($_POST['samurai_hidden']) and 
	   (sanitize_text_field($_POST['samurai_hidden']) == 'Y')) {
		//Update Form data sent
		if(sanitize_text_field(isset($_POST['samurai_display']))){
			$db_get['SR_DISPLAY']  = sanitize_text_field($_POST['samurai_display']);
		}else{
			$db_get['SR_DISPLAY']  = '';
		}
		if(sanitize_text_field(isset($_POST['samurai_preview']))){
			$db_get['SR_PREVIEW']  = sanitize_text_field($_POST['samurai_preview']);
		}else{
			$db_get['SR_PREVIEW']  = '';
		}
		$db_get['SR_CHECK']    = sanitize_text_field($_POST['samurai_check']);
		$db_get['SR_UPDATE']   = getdate();
		$cnt = ($db_get['SR_COUNT'] = sanitize_text_field($_POST['samurai_count']));
		if(sanitize_text_field(isset($_POST['add_x'])) and 
			(sanitize_text_field($_POST['add_x'])))	{	
			if($cnt < $Max_Cnt) $db_get['SR_COUNT'] = ++$cnt;
			$Add_Delete_Cmd = 1;
		}
		if(sanitize_text_field(isset($_POST['delete_y'])) and 
			(sanitize_text_field($_POST['delete_y']))){	
			if($cnt > 1)  $db_get['SR_COUNT'] = --$cnt;
			$Add_Delete_Cmd = -1;
		}
		for ($i=1 ; $i<=$cnt ; $i++) {
			//The following data('samurai_comment') includes CSS and HTML code.
			if(isset($_POST['samurai_comment' . $i])){
				list($db_get['SR_COMMENT'. $i], $flgs[$i]) = samurai_sanitize($_POST['samurai_comment' . $i]);
			}else{
				list($db_get['SR_COMMENT'. $i], $flgs[$i]) = '';
			}
		}
		if($db_get['SR_CHECK'] > $cnt) $db_get['SR_CHECK'] = $cnt;
		if($Add_Delete_Cmd == 0){
			update_option($db_name, $db_get);
			?>
			<div class="updated"><p><strong><?php _e('Options saved.','samurai' ); ?></strong></p></div>
			<?php
		}
	} else {
		//Normal Option Setting page display
		$db_get  = get_option($db_name);
		if(! $db_get['SR_COUNT']) {  //SQLite Bag Fix Script
			include('samurai_com.php');
		}
		for ($i=1 ; $i<=$db_get['SR_COUNT'] ; $i++) $flgs[$i] = TRUE;
	}
	// Disable malicious code other than HTML and CSS.
	function samurai_sanitize($data) {
		$search = array(
			"<?php"       ,"<script"       ,"</script>"     ,
			"<iframe"     ,"</iframe>"     ,"<applet"       ,"</applet>");
		$replace = array(
			"<!--?php--"  ,"<!--script--"  ,"<!--/script-->",
			"<!--iframe--","<!--/iframe-->","<!--applet--"  , "<!--/applet-->");
		$out = str_ireplace($search,$replace,$data); //Replace
		$flg = (mb_strlen($out) == mb_strlen($data));
		if(!$flg) $out = '<!-- ' . __('Inappropriate elements have been invalidated (commented).','samurai') .' -->' . $out;
		return array($out, $flg);
	}
?>

<div class="wrap">
<h2><?php echo __('SAMURAI Settings', 'samurai') . '  ' . $Version . ''; ?></h2>

<form name="samurai_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<input type="hidden" name="samurai_hidden" value="Y">
	<input type="hidden" name="samurai_count" value="<?php echo $db_get['SR_COUNT'] ?>">
	<hr />
<?php
	for ($i=1 ; $i<=$db_get['SR_COUNT'] ; $i++) {
		echo '<p><strong style="vertical-align:top;font-size:15px;line-height:1.0em;">' . $i . '.</strong>  <input type="radio" name="samurai_check" value="' . $i . '" ';
		if($db_get['SR_CHECK'] == $i) echo 'checked';
		echo ' style="vertical-align:top;" />  <textarea name="samurai_comment' . $i . '" cols="90" rows="4" style="resize:both;">';
		$wk2 = stripslashes($db_get['SR_COMMENT'. $i]);
		echo $wk2 . '</textarea></p>';
		if(isset($flgs[$i]) and !$flgs[$i]) echo '<strong style="margin-left:40px;font-size:20px;">' . __('Inappropriate elements have been invalidated (commented).','samurai') . '</strong></br></br>';
		// View HTML + CSS + CSS
		if($db_get['SR_PREVIEW'] == '1' && mb_strlen($wk2) > 0) echo '<div style="margin-left:40px;border-style:solid;border-width:1px;max-width:550px;">' . $wk2 . '</div></br>';
	}
	wp_nonce_field($db_name);
?>
	<p><input type="image" name="add"    src="<?php echo $Samurai_Url . 'add.png'; ?>" alt="<?php _e('Add','samurai'); ?>" style="vertical-align:middle;" title="<?php _e('Add','samurai'); ?>" <?php if($db_get['SR_COUNT'] == $Max_Cnt) echo ' disabled '; ?> />
	<input type="image" name="delete" src="<?php echo $Samurai_Url . 'delete.png'; ?>" alt="<?php _e('Delete','samurai'); ?>" style="vertical-align:middle;" title="<?php _e('Delete','samurai'); ?>" <?php if($db_get['SR_COUNT'] == 1) echo ' disabled '; ?> />
	<span style="vertical-align:middle;">&nbsp;&nbsp;<?php echo __('Max:','samurai') . ' ' . $Max_Cnt; ?></span></p>
	<p><input type="checkbox" name="samurai_display" value="1" <?php if($db_get['SR_DISPLAY'] == '1') echo ("checked"); ?> > : <?php echo __('Display the setting contents in the comment of the post.','samurai'); ?></p>
	<p><input type="checkbox" name="samurai_preview" value="1" <?php if($db_get['SR_PREVIEW'] == '1') echo ("checked"); ?> > : <?php echo __('Display preview in the setting.','samurai'); ?></p>
	<p class="submit">
	<input type="submit" name="Submit" style="cursor: pointer;" value="<?php _e('Update Options', 'samurai' ) ?>" />
	</p>
	<p><?php echo __('Click','samurai')?> -&gt; <a href="http://wordpress.nnn2.com/?p=554" target="_blank"><?php echo __('HTML Sample','samurai')?></a></p>
	<?php wp_nonce_field($Nn_actin,$Nn_Name); ?>
</form>
<img src="<?php echo $Samurai_Url . 'setting-01.png'; ?>" alt="SAMURAI Plugin" title="SAMURAI Plugin"  width="350" height="336"/>
</div>
