<?php
/*
Plugin Name: CallRail
Plugin URI: http://www.callrail.com/docs/wordpress
Description: Dynamically swap CallRail phone numbers based on the referring source.
Author: Last Mile Metrics, LLC.
Version: 0.2
Author URI: http://www.callrail.com
*/

add_action('admin_menu', 'callrail_menu');
add_action('wp_footer', 'callrail_footer');

function callrail_menu() {
	add_options_page('CallRail Options', 'CallRail', 'manage_options', 'callrail', 'callrail_options');
}

function callrail_options() {

      //must check that the user has the required capability 
      if (!current_user_can('manage_options'))
      {
        wp_die( __('You do not have sufficient permissions to access this page.') );
      }

      // Read in existing option value from database
      $masked_id_and_access_key = get_option('masked_id_and_access_key');
      

      // See if the user has posted us some information
      // If they did, this hidden field will be set to 'Y'
      if( isset($_POST['callrail_hidden_field']) && $_POST['callrail_hidden_field'] == 'Y' ) {
          // Read their posted value
          $masked_id_and_access_key = $_POST['masked_id_and_access_key'];
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
  	<table class="form-table"> 
      <tr valign="top"> 
        <th scope="row">
          <label for="masked_id_and_access_key">CallRail Wordpress ID</label>
        </th> 
        <td>
          <input name="masked_id_and_access_key" type="text" id="masked_id_and_access_key" class="regular-text code" size="20" value="<?php echo $masked_id_and_access_key ?>" /> 
        </td> 
      </tr> 
      <tr>
        <td colspan='2'><span class="description">You can find this value in your <a href="http://app.callrail.com/wordpress">CallRail account</a>.</span></td>
      </tr> 
    </table>
    <p class="submit"> 
    	<input type="submit" name="Submit" class="button-primary" value="Save Changes" /> 
    </p>
  	</form>
  </div>

<?php
}

function callrail_footer(){
?>
<script type="text/javascript" src="//app.callrail.com/companies/<?php echo get_option('masked_id_and_access_key') ?>/1/swap.js"></script>
<?php
}