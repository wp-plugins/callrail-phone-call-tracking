<?php
/*
Plugin Name: CallRail Phone Call Tracking
Plugin URI: http://www.callrail.com/docs/web-integration/wordpress-plugin/
Description: Dynamically swap CallRail tracking phone numbers based on the visitor's referring source.
Author: Last Mile Metrics, LLC.
Version: 0.3.6
Author URI: http://www.callrail.com
*/

add_action('admin_menu', 'callrail_menu');
add_action('admin_notices', 'callrail_admin_notice' );
add_action('wp_footer', 'callrail_footer');

function callrail_menu() {
	add_options_page('CallRail Options', 'CallRail', 'manage_options', 'callrail', 'callrail_options');
}

function callrail_admin_notice() {
	$api_key = trim(get_option('masked_id_and_access_key'));
	$is_plugins_page = (substr( $_SERVER["PHP_SELF"], -11 ) == 'plugins.php') ? true : false;
	if( $is_plugins_page && !$api_key && function_exists( "admin_url" ) )
		echo '<div class="error"><p><strong>' . sprintf( __('<a href="%s">Enter your CallRail API key</a> to enable dynamic tracking number insertion.', 'callrail' ), admin_url( 'options-general.php?page=callrail' ) ) . '</strong></p></div>';
}


function callrail_options() {

	//must check that the user has the required capability 
	if (!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	// Read in existing option value from database
	$masked_id_and_access_key = get_option('masked_id_and_access_key');
      

      // See if the user has posted us some information
      // If they did, this hidden field will be set to 'Y'
      if( isset($_POST['callrail_hidden_field']) && $_POST['callrail_hidden_field'] == 'Y' ) {
          // Read their posted value
          $masked_id_and_access_key = trim($_POST['masked_id_and_access_key']);
          // Change the delimiter from x to /
          // x allows double clicking to copy and paste
          $masked_id_and_access_key = str_replace('x', '/', $masked_id_and_access_key);

          // Save the posted value in the database
          update_option('masked_id_and_access_key', $masked_id_and_access_key );

          // Put an settings updated message on the screen

  ?>
  <div class="updated"><p><strong>Your CallRail settings were saved successfully.</strong></p></div>
<?php
  
  }
  // Before showing it back to the user, change the delimeter from / to x
  $masked_id_and_access_key = str_replace('/', 'x', $masked_id_and_access_key);
?>
	<div class="wrap">
  	<h2>CallRail Settings</h2>
  	<p>Dynamically swap CallRail phone numbers based on the referring source.</p>
  	<form method="POST" action="">
  	<input type="hidden" name="callrail_hidden_field" value="Y">
  	<table class="form-table" cellpadding="0" cellspacing="0"> 
      <tr valign="top"> 
        <th scope="row" style="padding-left: 0px">
          <label for="masked_id_and_access_key">CallRail API Key</label>
        </th> 
        <td>
          <input name="masked_id_and_access_key" type="text" id="masked_id_and_access_key" class="regular-text code" size="20" value="<?php echo $masked_id_and_access_key ?>" /> 
        </td> 
      </tr> 
      <tr>
        <td colspan="2" style="padding-left: 0px"><span class="description">You can find this value in your <a href="http://app.callrail.com/wordpress" target="_blank">CallRail account</a>.</span></td>
      </tr> 
    </table>
    <p class="submit"> 
    	<input type="submit" name="Submit" class="button-primary" value="Save Changes" /> 
    </p>
  	</form>
  </div>

<?php
}

function callrail_footer() {
	$api_key = get_option('masked_id_and_access_key');
	if (trim($api_key)) {
		echo "<script type=\"text/javascript\" src=\"//cdn.callrail.com/companies/{$api_key}/10/swap.js\"></script>";
	}
}