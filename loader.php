<?php

/*

Plugin Name: BuddyPress Badge

Plugin URI: http://wordpress.org/extend/plugins/buddypress-badge

Description: Create Badge for buddypress.

Version: 1.5

Revision Date: 10 09, 2010

Requires at least: WP 2.9.2, BuddyPress 1.2.1

Tested up to: WP 3.0.1, BuddyPress 1.2.5.2

License: (Example: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html)

Author: Warut Sudpoothong

Author URI: http://www.warutsoft.com

Site Wide Only: false

*/





/* Only load the component if BuddyPress is loaded and initialized. */

function bp_badge_init() {

	require( dirname( __FILE__ ) . '/includes/bp-badge-core.php' );

}

add_action( 'bp_init', 'bp_badge_init' );



/* Put setup procedures to be run when the plugin is activated in the following function */

function bp_badge_activate() {

	global $wpdb;



	if ( !empty($wpdb->charset) )

		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";



	/**

	 * If you want to create new tables you'll need to install them on

	 * activation.

	 *

	 * You should try your best to use existing tables if you can. The

	 * activity stream and meta tables are very flexible.

	 *

	 * Write your table definition below, you can define multiple

	 * tables by adding SQL to the $sql array.

	 */



	require_once( ABSPATH . 'wp-admin/upgrade-functions.php' );

	

	/* update default site options */

	

	update_site_option('bp_badge_default','logo&photo');

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



	/**

	 * The dbDelta call is commented out so the example table is not installed.

	 * Once you define the SQL for your new table, uncomment this line to install

	 * the table. (Make sure you increment the BP_EXAMPLE_DB_VERSION constant though).

	 */

	// dbDelta($sql);



}

register_activation_hook( __FILE__, 'bp_badge_activate' );



/* On deacativation, clean up anything your component has added. */

function bp_badge_deactivate() {

	/* You might want to delete any options or tables that your component created. */

}

register_deactivation_hook( __FILE__, 'bp_badge_deactivate' );

?>