<?php



/***

 * This file is used to add site administration menus to the WordPress backend.

 *

 * If you need to provide configuration options for your component that can only

 * be modified by a site administrator, this is the best place to do it.

 *

 * However, if your component has settings that need to be configured on a user

 * by user basis - it's best to hook into the front end "Settings" menu.

 */



/**

 * Checks for form submission, saves component settings and outputs admin screen HTML.

 */

function bp_badge_admin() {

	global $bp;



	/* If the form has been submitted and the admin referrer checks out, save the settings */

	if ( isset( $_POST['submit'] ) && check_admin_referer('badge-settings') ) {

		update_site_option( 'bp_badge_showonlylogo', $_POST['bp_badge_showonlylogo'] );

		update_site_option( 'bp_badge_userselectlayout', $_POST['bp_badge_userselectlayout'] );

		update_site_option( 'bp_badge_useraddphoto', $_POST['bp_badge_useraddphoto'] );

		update_site_option( 'bp_badge_useraddxprofile', $_POST['bp_badge_useraddxprofile'] );

		update_site_option( 'bp_badge_defaultlayout', $_POST['bp_badge_defaultlayout'] );

		update_site_option( 'bp_badge_defaultphoto', $_POST['bp_badge_defaultphoto'] );

		update_site_option( 'bp_badge_showthumbimage', $_POST['bp_badge_showthumbimage'] );

		update_site_option( 'bp_badge_boldfieldtext', $_POST['bp_badge_boldfieldtext'] );
		update_site_option( 'bp_badge_excludefield', $_POST['bp_badge_excludefield'] );

		if ($_POST['bp_badge_bgcolor'] == '' or strlen($_POST['bp_badge_bgcolor']) != 6){

			update_site_option( 'bp_badge_bgcolor', 'FFFFFF' ); } else {

			update_site_option( 'bp_badge_bgcolor', $_POST['bp_badge_bgcolor'] );

			}

		if ($_POST['bp_badge_textcolor'] == '' or strlen($_POST['bp_badge_textcolor']) != 6){

			update_site_option( 'bp_badge_textcolor', '000000' ); } else {

			update_site_option( 'bp_badge_textcolor', $_POST['bp_badge_textcolor'] );

		}

		if ($_POST['bp_badge_fieldtextcolor'] == '' or strlen($_POST['bp_badge_fieldtextcolor']) != 6){

			update_site_option( 'bp_badge_fieldtextcolor', '000000' ); } else {

			update_site_option( 'bp_badge_fieldtextcolor', $_POST['bp_badge_fieldtextcolor'] );

		}

		update_site_option( 'bp_badge_showbadgeborder', $_POST['bp_badge_showbadgeborder'] );

		if ($_POST['bp_badge_bordercolor'] == '' or strlen($_POST['bp_badge_bordercolor']) != 6){

			update_site_option( 'bp_badge_bordercolor', '6666FF' ); } else {

			update_site_option( 'bp_badge_bordercolor', $_POST['bp_badge_bordercolor'] );

		}

		update_site_option( 'bp_badge_islongcode', $_POST['bp_badge_islongcode'] );

		$update = true;

	}

	

	/* If no default setting create one */

	if (get_site_option('bp_badge_showonlylogo') == '') { update_site_option( 'bp_badge_showonlylogo', '0' );}

	if (get_site_option('bp_badge_userselectlayout') == '') { update_site_option( 'bp_badge_userselectlayout', '1' );}

	if (get_site_option('bp_badge_useraddphoto') == '') { update_site_option( 'bp_badge_useraddphoto', '1' );}

	if (get_site_option('bp_badge_useraddxprofile') == '') { update_site_option( 'bp_badge_useraddxprofile', '1' );}

	if (get_site_option('bp_badge_defaultlayout') == '') { update_site_option( 'bp_badge_defaultlayout', '1' );}

	if (get_site_option('bp_badge_defaultphoto') == '') { update_site_option( 'bp_badge_defaultphoto', '1' );}

	if (get_site_option('bp_badge_showthumbimage') == '') { update_site_option( 'bp_badge_showthumbimage', '1' );}

	if (get_site_option('bp_badge_boldfieldtext') == '') { update_site_option( 'bp_badge_boldfieldtext', '1' );}

	if (get_site_option('bp_badge_bgcolor') == '') { update_site_option( 'bp_badge_bgcolor', 'FFFFFF' );}

	if (get_site_option('bp_badge_textcolor') == '') { update_site_option( 'bp_badge_textcolor', '000000' );}

	if (get_site_option('bp_badge_fieldtextcolor') == '') { update_site_option( 'bp_badge_fieldtextcolor', '000000' );}

	if (get_site_option('bp_badge_showbadgeborder') == '') { update_site_option( 'bp_badge_showbadgeborder', '1' );}

	if (get_site_option('bp_badge_bordercolor') == '') { update_site_option( 'bp_badge_bordercolor', '000000' );}

	if (get_site_option('bp_badge_islongcode') == '') { update_site_option( 'bp_badge_islongcode', '1' );}

	



?>

	<div class="wrap">

		<h2><?php _e( 'Badge Admin', 'bp-badge' ) ?></h2>

		<br />



		<?php if ( isset($updated) ) : ?><?php echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-badge' ) . "</p></div>" ?><?php endif; ?>



		<form action="<?php echo site_url() . '/wp-admin/admin.php?page=bp-badge-settings' ?>" name="badge-settings-form" id="badge-settings-form" method="post">



			<h3><?php _e( 'General option', 'bp-badge' ) ?></h3>

			<table class="form-table">

				<tr valign="top">

					<th scope="row"><label for="target_uri"><?php _e( 'Show only website logo', 'bp-badge' ) ?></label></th>

					<td>

						<input type="radio" name="bp_badge_showonlylogo" value="1" <?php if ( get_site_option('bp_badge_showonlylogo') == true ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'Yes', 'bp-badge' ) ?>

						<input type="radio" name="bp_badge_showonlylogo" value="0" <?php if ( get_site_option('bp_badge_showonlylogo') == false ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'No', 'bp-badge' ) ?><br />

					</td>

				</tr>

				<tr valign="top">

					<th scope="row"><label for="target_uri"><?php _e( 'User can select layout', 'bp-badge' ) ?></label></th>

					<td>

						<input type="radio" name="bp_badge_userselectlayout" value="1" <?php if ( get_site_option('bp_badge_userselectlayout') == true ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'Yes', 'bp-badge' ) ?>

						<input type="radio" name="bp_badge_userselectlayout" value="0" <?php if ( get_site_option('bp_badge_userselectlayout') == false ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'No', 'bp-badge' ) ?><br />

					</td>

				</tr>

				<tr valign="top">

					<th scope="row"><label for="target_uri"><?php _e( 'User can add profile image', 'bp-badge' ) ?></label></th>

					<td>

						<input type="radio" name="bp_badge_useraddphoto" value="1" <?php if ( get_site_option('bp_badge_useraddphoto') == true ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'Yes', 'bp-badge' ) ?>

						<input type="radio" name="bp_badge_useraddphoto" value="0" <?php if ( get_site_option('bp_badge_useraddphoto') == false ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'No', 'bp-badge' ) ?><br />

					</td>

				</tr>

				<tr valign="top">

					<th scope="row"><label for="target_uri"><?php _e( 'User can add xprofile information', 'bp-badge' ) ?></label></th>

					<td>

						<input type="radio" name="bp_badge_useraddxprofile" value="1" <?php if ( get_site_option('bp_badge_useraddxprofile') == true ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'Yes', 'bp-badge' ) ?>

						<input type="radio" name="bp_badge_useraddxprofile" value="0" <?php if ( get_site_option('bp_badge_useraddxprofile') == false ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'No', 'bp-badge' ) ?><br />

					</td>

				</tr>

				</table>

				<h3><?php _e( 'Display option', 'bp-badge' ) ?></h3>

				<table class="form-table">

				<tr valign="top">

					<th scope="row"><label for="target_uri"><?php _e( 'Default layout', 'bp-badge' ) ?></label></th>

					<td>

						<input type="radio" name="bp_badge_defaultlayout" value="1" <?php if ( get_site_option('bp_badge_defaultlayout') == true ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'Vertical', 'bp-badge' ) ?>

						<input type="radio" name="bp_badge_defaultlayout" value="0" <?php if ( get_site_option('bp_badge_defaultlayout') == false ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'Horizontal', 'bp-badge' ) ?><br />

					</td>

				</tr>

				<tr valign="top">

					<th scope="row"><label for="target_uri"><?php _e( 'Default profile image', 'bp-badge' ) ?></label></th>

					<td>

						<input type="radio" name="bp_badge_defaultphoto" value="1" <?php if ( get_site_option('bp_badge_defaultphoto') == true ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'Show', 'bp-badge' ) ?>

						<input type="radio" name="bp_badge_defaultphoto" value="0" <?php if ( get_site_option('bp_badge_defaultphoto') == false ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'Hide', 'bp-badge' ) ?><br />

					</td>

				</tr>

				<tr valign="top">

					<th scope="row"><label for="target_uri"><?php _e( 'Profile image size', 'bp-badge' ) ?></label></th>

					<td>

						<input type="radio" name="bp_badge_showthumbimage" value="1" <?php if ( get_site_option('bp_badge_showthumbimage') == true ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'Thumbnail', 'bp-badge' ) ?>

						<input type="radio" name="bp_badge_showthumbimage" value="0" <?php if ( get_site_option('bp_badge_showthumbimage') == false ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'Full', 'bp-badge' ) ?><br />

					</td>

				</tr>

				<tr valign="top">

					<th scope="row"><label for="target_uri"><?php _e( 'Background color', 'bp-badge' ) ?></label></th>

					<td>

						#<input name="bp_badge_bgcolor" type="text" id="bp_badge_bgcolor" value="<?php echo attribute_escape( get_site_option('bp_badge_bgcolor') ); ?>" size="6" maxlength="6" />

					</td>

				</tr>

					<th scope="row"><label for="target_uri"><?php _e( 'Text color', 'bp-badge' ) ?></label></th>

					<td>

						#<input name="bp_badge_textcolor" type="text" id="bp_badge_textcolor" value="<?php echo attribute_escape( get_site_option('bp_badge_textcolor') ); ?>" size="6" maxlength="6" />

					</td>

				</tr>

				<tr valign="top">

					<th scope="row"><label for="target_uri"><?php _e( 'Bold field text', 'bp-badge' ) ?></label></th>

					<td>

						<input type="radio" name="bp_badge_boldfieldtext" value="1" <?php if ( get_site_option('bp_badge_boldfieldtext') == true ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'Yes', 'bp-badge' ) ?>

						<input type="radio" name="bp_badge_boldfieldtext" value="0" <?php if ( get_site_option('bp_badge_boldfieldtext') == false ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'No', 'bp-badge' ) ?><br />

					</td>

				</tr>

				<tr valign="top">

					<th scope="row"><label for="target_uri"><?php _e( 'Field text color', 'bp-badge' ) ?></label></th>

					<td>

						#<input name="bp_badge_fieldtextcolor" type="text" id="bp_badge_fieldtextcolor" value="<?php echo attribute_escape( get_site_option('bp_badge_fieldtextcolor') ); ?>" size="6" maxlength="6" />

					</td>

				</tr>

				<tr valign="top">

					<th scope="row"><label for="target_uri"><?php _e( 'Show badge border', 'bp-badge' ) ?></label></th>

					<td>

						<input type="radio" name="bp_badge_showbadgeborder" value="1" <?php if ( get_site_option('bp_badge_showbadgeborder') == true ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'Yes', 'bp-badge' ) ?>

						<input type="radio" name="bp_badge_showbadgeborder" value="0" <?php if ( get_site_option('bp_badge_showbadgeborder') == false ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'No', 'bp-badge' ) ?><br />

					</td>

				</tr>

				<tr valign="top">

					<th scope="row"><label for="target_uri"><?php _e( 'Border color', 'bp-badge' ) ?></label></th>

					<td>

						#<input name="bp_badge_bordercolor" type="text" id="bp_badge_bordercolor" value="<?php echo attribute_escape( get_site_option('bp_badge_bordercolor') ); ?>" size="6" maxlength="6" />

					</td>

				</tr>

				<tr valign="top">

					<th scope="row"><label for="target_uri"><?php _e( 'Badge code', 'bp-badge' ) ?></label></th>

					<td>

						<input type="radio" name="bp_badge_islongcode" value="1" <?php if ( get_site_option('bp_badge_islongcode') == true ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'Long', 'bp-badge' ) ?>

						<input type="radio" name="bp_badge_islongcode" value="0" <?php if ( get_site_option('bp_badge_islongcode') == false ) : ?>checked="checked"<?php endif; ?>/><?php _e( 'Short', 'bp-badge' ) ?><br />

					</td>

				</tr>
				<tr valign="top">

					<th scope="row"><label for="target_uri"><?php _e( 'Exclude field (seperate by ,)', 'bp-badge' ) ?></label></th>

					<td>

						<input name="bp_badge_excludefield" type="text" id="bp_badge_excludefield" value="<?php echo attribute_escape( get_site_option('bp_badge_excludefield') ); ?>" size="18" />

					</td>

				</tr>

			</table>

			<p class="submit">

				<input type="submit" name="submit" value="<?php _e( 'Save Settings', 'bp-badge' ) ?>"/>

			</p>



			<?php

			/* This is very important, don't leave it out. */

			wp_nonce_field( 'badge-settings' );

			?>

		</form>

	</div>

<?php

}

?>