<?php



/* Define a constant that can be checked to see if the component is installed or not. */

define ( 'BP_BADGE_IS_INSTALLED', 1 );

/* Define a constant that will hold the current version number of the component */

define ( 'BP_BADGE_VERSION', '1.0' );

/* Define a slug constant that will be used to view this components pages (http://example.org/SLUG) */

if ( !defined( 'BP_BADGE_SLUG' ) )

	define ( 'BP_BADGE_SLUG', 'badge' );


if ( file_exists( dirname( __FILE__ ) . '/languages/buddypress-badge-' . get_locale() . '.mo' ) )

	load_textdomain( 'bp-badge', dirname( __FILE__ ) . '/languages/buddypress-badge-' . get_locale() . '.mo' );
	

/**

 * Sets up global variables for your component.

 */

function bp_badge_setup_globals() {

	global $bp, $wpdb;

	

	if ( !defined( 'BP_BADGE_IMAGE_PATH' ) )

		define ( 'BP_BADGE_IMAGE_PATH', bp_badge_image_path() );



	/* For internal identification */

	$bp->badge->id = 'badge';
	$bp->badge->slug = BP_BADGE_SLUG;

	/* Register this in the active components array */

	$bp->active_components[$bp->badge->slug] = $bp->badge->id;

}



add_action( 'wp', 'bp_badge_setup_globals', 2 );
add_action( 'admin_menu', 'bp_badge_setup_globals', 2 );



/**

 * This function will add a WordPress wp-admin admin menu for your component under the

 * "BuddyPress" menu.

 */

function bp_badge_add_admin_menu() {

	global $bp;

	if ( !$bp->loggedin_user->is_site_admin )

		return false;

	require ( dirname( __FILE__ ) . '/bp-badge-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'Badge Setup', 'bp-badge' ), __( 'Badge Setup', 'bp-badge' ), 'manage_options', 'bp-badge-settings', 'bp_badge_admin' );

}

add_action( 'admin_menu', 'bp_badge_add_admin_menu' );



/**

 * Sets up the user profile navigation items for the component. This adds the top level nav

 * item and all the sub level nav items to the navigation array. This is then

 * rendered in the template.

 */

function bp_badge_setup_nav() {

	global $bp;


	$profile_link = $bp->loggedin_user->domain . $bp->profile->slug . '/';

	/* Create sub nav item for this component */

	bp_core_new_subnav_item( array(

		'name' => __( 'Badge', 'bp-badge' ),

		'slug' => $bp->badge->slug,

		'parent_slug' => $bp->profile->slug,

		'parent_url' => $profile_link,

		'screen_function' => 'bp_badge_screen',

		'position' => 50,

		'user_has_access' => bp_is_my_profile()

	) );

}

/***
 * In versions of BuddyPress 1.2.2 and newer you will be able to use:
 * add_action( 'bp_setup_nav', 'bp_example_setup_nav' );
 */

add_action( 'wp', 'bp_badge_setup_nav', 2 );
add_action( 'admin_menu', 'bp_badge_setup_nav', 2 );

/**
 *
 * Sets up and displays the screen output for the sub nav item "badge/screen"
 */

function bp_badge_screen() {

	global $bp;

	/* Add a do action here, so your component can be extended by others. */

	do_action( 'bp_badge_screen' );

	/* This is going to look in wp-content/plugins/[plugin-name]/includes/templates/ first */

	add_action( 'bp_template_content', 'bp_badge_screen_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );

}


function bp_badge_screen_content() {

	?><h4><?php _e( 'Badge Generator', 'bp-badge' ) ?></h4>

<?php

/* If the form has been submitted and the admin referrer checks out, save the settings */

	if ( isset( $_POST['submit'] ) && check_admin_referer('badge-usersetting') ) {

		/* update user layout option */

		bp_badge_updateusermeta($_POST);
		bp_badge_createbadge();
	}

	// check if no badge usermeta, create from default option

	bp_badge_inituseroption();
				?>

<!--print user option-->

<div style="float:left; width:200px;">
	<?php bp_badge_useroptionform(); ?>
</div>
<div>
<p><label><b><?php _e( 'Copy and Pasts the following code on your website :', 'bp-badge' ) ?></b></label>&nbsp;</p>
<textarea name="badgescript" cols="50" style="overflow:auto" ><?php echo bp_badge_badgescript(); ?></textarea>
</div>
<div><br />
<p><label><b><?php _e( 'Preview :', 'bp-badge' ) ?></b></label>&nbsp;</p>

<?php 

bp_badge_userbadge(); ?>

</div>
<?php
}
 

function bp_badge_image_path(){

	if ( bp_core_is_multisite() )

		$path = ABSPATH . get_blog_option( BP_ROOT_BLOG, 'upload_path' );

	else {

		$upload_path = get_option( 'upload_path' );

		$upload_path = trim($upload_path);

		if ( empty($upload_path) || 'wp-content/uploads' == $upload_path) {

			$path = WP_CONTENT_DIR . '/uploads';

		} else {

			$path = $upload_path;

			if ( 0 !== strpos($path, ABSPATH) ) {

				// $dir is absolute, $upload_path is (maybe) relative to ABSPATH

				$path = path_join( ABSPATH, $path );

			}

		}

	}

	

	$path .= '/badge';

	return apply_filters( 'bp_badge_image_path', $path );

}



function bp_badge_hex2rgb($color){    

	if ($color[0] == '#')        

		$color = substr($color, 1);    

	if (strlen($color) == 6)        

	list($r, $g, $b) = array($color[0].$color[1], $color[2].$color[3], $color[4].$color[5]);    

	elseif (strlen($color) == 3)        

	list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);

	else return false;    

	$r = hexdec($r); 

	$g = hexdec($g); 

	$b = hexdec($b);    

	return array($r, $g, $b);

}



function bp_badge_updateusermeta($post){

	global $bp;

	

	if ( get_site_option('bp_badge_userselectlayout') == true ) {

		if (isset( $post['badgelayout'])) {

		update_usermeta( $bp->loggedin_user->id, 'bp_badge_userlayout',$post['badgelayout']);

		}

	} else {
		if (get_site_option('bp_badge_defaultlayout') == '1') {
			update_usermeta( $bp->loggedin_user->id, 'bp_badge_userlayout', 'vertical');
		} else {
			update_usermeta( $bp->loggedin_user->id, 'bp_badge_userlayout', 'horizontal');
		}
	}

	/* update user xprofile option */

	 

		$bp_badge_useroption = 'logo';

		if ( get_site_option('bp_badge_useraddphoto') == true ) {

			if (isset( $post['photo'])) {

			$bp_badge_useroption .= '&photo';

			}

		} elseif (get_site_option('bp_badge_defaultphoto') == true) {

			$bp_badge_useroption .= '&photo';

		}

		if ( get_site_option('bp_badge_useraddxprofile') == true ) {

		if (isset( $post['xprofile_selected'])) {

			foreach ($post['xprofile_selected'] as $key => $val){

			$bp_badge_useroption .= '&'.$val;

			}

		}

		}

	update_usermeta( $bp->loggedin_user->id, 'bp_badge_useroption',$bp_badge_useroption);

}



function bp_badge_isuseroption($option){

	global $bp;

	

	$bp_badge_useroption = explode("&", get_usermeta( $bp->loggedin_user->id, 'bp_badge_useroption' ));

	if ( in_array($option,$bp_badge_useroption) ){

	return true;

	}

}



function bp_badge_getxprofile (){

	global $bp;

	

	$xprofile_array = array();

	if ( function_exists('xprofile_get_profile') ) : 

		if ( bp_has_profile() ) :

			while ( bp_profile_groups() ) : bp_the_profile_group();

			if ( bp_profile_group_has_fields() ) : 

				if ( 1 != bp_get_the_profile_group_id() ) : 

				endif; 

			while ( bp_profile_fields() ) : bp_the_profile_field(); 

		if ( bp_field_has_data() ) : 														

		$bp_fieldname = strip_tags(bp_get_the_profile_field_name());															

		$bp_fieldvalue = strip_tags(bp_get_the_profile_field_value());									

		$xprofile_array[$bp_fieldname] = $bp_fieldname.' : '.$bp_fieldvalue; 														

		endif;					

			endwhile; 



			endif; 



			endwhile; 



 		endif;

		endif; 

	return $xprofile_array;

}


function bp_badge_getexcludefield() {
	global $bp;
	
	$excludefield_array = explode(",", get_site_option('bp_badge_excludefield'));
	
	return $excludefield_array;
}

function bp_badge_inituseroption() {

	global $bp;

	

	if (get_usermeta( $bp->loggedin_user->id, 'bp_badge_useroption' ) == ''){

		update_usermeta( $bp->loggedin_user->id, 'bp_badge_useroption',get_site_option('bp_badge_default'));

		update_usermeta( $bp->loggedin_user->id, 'bp_badge_userlayout','vertical'); 

		// create initial badge

		bp_badge_createbadge();

	}				

}



function bp_badge_badgescript(){

	global $bp;



$badgeurl = bp_badge_userbadgeurl();

list($badge_width, $badge_height) = getimagesize($badgeurl); 

//echo $badgeurl;

if ( get_site_option('bp_badge_islongcode') == true ) {

$badgescript = '&lt;!-- '.__('Buddypress Badge START','bp-badge').' --&gt;&lt;a href=&quot;'. $bp->loggedin_user->domain .'&quot; title=&quot;'.$bp->loggedin_user->fullname.'&quot; target=&quot;_TOP&quot; style=&quot;font-family: &amp;quot;lucida grande&amp;quot;,tahoma,verdana,arial,sans-serif; font-size: 11px; font-variant: normal; font-style: normal; font-weight: normal; color: #3B5998; text-decoration: none;&quot;&gt;'.$bp->loggedin_user->fullname.'&lt;/a&gt;&lt;br/&gt;&lt;a href=&quot;'. $bp->loggedin_user->domain .'&quot; title=&quot;'.$bp->loggedin_user->fullname.'&quot; target=&quot;_TOP&quot;&gt;&lt;img src=&quot;'.$badgeurl.'&quot; width=&quot;'.$badge_width.'&quot; height=&quot;'.$badge_height.'&quot; style=&quot;border: 0px;&quot; /&gt;&lt;/a&gt;&lt;br/&gt;&lt;br/&gt;&lt;a href=&quot;'.bp_get_root_domain().'&quot; title=&quot;Make your own badge!&quot; target=&quot;_TOP&quot; style=&quot;font-family: &amp;quot;lucida grande&amp;quot;,tahoma,verdana,arial,sans-serif; font-size: 11px; font-variant: normal; font-style: normal; font-weight: normal; color: #3B5998; text-decoration: none;&quot;&gt;'.__('Create Your Badge','bp-badge').'&lt;/a&gt;&lt;!-- '.__('Buddypress Badge END','bp-badge').' --&gt;'; 

} else {

$badgescript = '&lt;!-- '.__('Buddypress Badge START','bp-badge').' --&gt;&lt;a href=&quot;'. $bp->loggedin_user->domain .'&quot; title=&quot;'.$bp->loggedin_user->fullname.'&quot; target=&quot;_TOP&quot;&gt;&lt;img src=&quot;'.$badgeurl.'&quot; width=&quot;'.$badge_width.'&quot; height=&quot;'.$badge_height.'&quot; style=&quot;border: 0px;&quot; /&gt;&lt;/a&gt;&lt;br/&gt;&lt;!-- '.__('Buddypress Badge END','bp-badge').' --&gt;'; 

}



	return $badgescript;

}

function bp_badge_userbadge(){

	global $bp;
	
	$badgeurl = bp_badge_userbadgeurl();
	
	$upload_dir = wp_upload_dir();

	if ( get_site_option('bp_badge_showonlylogo') == false ) {

	echo '<img src="'.$badgeurl.'?'.time().'" />';

	} else {

	echo '<img src="'.bp_get_root_domain() .'/wp-content/plugins/buddypress-badge/includes/images/logo.png'.'" />';

	}

}



function bp_badge_userbadgeurl(){

	global $bp;
	
	$upload_dir = wp_upload_dir();

	if ( get_site_option('bp_badge_showonlylogo') == false ) {
	
		if ( bp_core_is_multisite() ) {

			$badgeurl = bp_get_root_domain().'/'.get_blog_option( BP_ROOT_BLOG, 'upload_path' ).'/badge/'.$bp->displayed_user->id.'.png';

		} else {
		
		$badgeurl = $upload_dir['baseurl'] .'/badge/'.$bp->displayed_user->id.'.png';
		
		}
	
	} else {

	$badgeurl = bp_get_root_domain().'/wp-content/plugins/buddypress-badge/includes/images/logo.png';

	}

	return $badgeurl;

}

function bp_badge_useroptionform(){

	global $bp;

	if ( get_site_option('bp_badge_showonlylogo') == false ) {

	?>

	<form name="badge-edit-form" id="badge-edit-form" method="post">

	<?php if ( get_site_option('bp_badge_userselectlayout') == true ) { ?>

<p><label><b><?php _e( 'Layout :', 'bp-badge' ) ?></b></label>&nbsp;</p>

<input type="radio" name="badgelayout" value="vertical" <?php if ( get_usermeta( $bp->loggedin_user->id, 'bp_badge_userlayout' ) == 'vertical' ) : ?>checked="checked"<?php endif; ?>/> 

<?php _e( 'Vertical', 'bp-badge' ) ?><br />

<input type="radio" name="badgelayout" value="horizontal" <?php if ( get_usermeta( $bp->loggedin_user->id, 'bp_badge_userlayout' ) == 'horizontal' ) : ?>checked="checked"<?php endif; ?>/> 

<?php _e( 'Horizontal', 'bp-badge' ) ?><br />

<br />

	<?php } ?>



<?php if ( get_site_option('bp_badge_useraddphoto') == true or get_site_option('bp_badge_useraddxprofile') == true) { ?>	

<p><label><b><?php _e( 'Information :', 'bp-badge' ) ?></b></label>&nbsp;</p>

<?php } ?>

	

<p>

<?php if ( get_site_option('bp_badge_useraddphoto') == true ) { ?>

<input type="checkbox" name="photo" value="photo" <?php if ( bp_badge_isuseroption('photo') ) : ?>checked="checked"<?php endif; ?>/> 

<?php _e( 'Photo', 'bp-badge' ) ?><br />

	<?php } ?>

<?php

if ( get_site_option('bp_badge_useraddxprofile') == true ) { 

//$backgroundcolor

$xprofile_array = bp_badge_getxprofile();
$excludefield_array = bp_badge_getexcludefield();


foreach ($xprofile_array as $key => $val){?>

<?php   if ( !(in_array($key,$excludefield_array)) ){ ?>  

    <input type="checkbox" name="xprofile_selected[]" value="<?php echo $key ?>" <?php if ( bp_badge_isuseroption($key) ) : ?>checked="checked"<?php endif; ?>/>

  <?php echo $key ?><br />

<?php } 
         }
} ?>

</p>

<p class="submit">

				<input type="submit" name="submit" value="<?php _e( 'Update Badge', 'bp-badge' ) ?>"/>

			</p>

<?php /* This is very important, don't leave it out. */

wp_nonce_field( 'badge-usersetting' );

?>

</form>

	<?php

	}

}





/********************************************

 * Function to create Badge Image using GD Library 

 ********************************************/

 

function bp_badge_createbadge(){

	global $bp;



if ( get_site_option('bp_badge_showonlylogo') == false ) {



$xprofile_array = bp_badge_getxprofile ();


if ( get_site_option('bp_badge_showthumbimage') == true ) {

$avatar_url = bp_get_loggedin_user_avatar( 'html=false&type=thumb' );

} else {

$avatar_url = bp_get_loggedin_user_avatar( 'html=false&type=full' );

}



$bp_badge_useroption = explode("&", get_usermeta( $bp->loggedin_user->id, 'bp_badge_useroption' ));



// check avatar image file type

$imagetype = explode(".", $avatar_url);

$avatartype = substr($imagetype[count($imagetype)-1], 0, 3);



// get profile image information

$profileimage = '';

$profile_width = 0; 

$profile_height = 0;

if (in_array('photo',$bp_badge_useroption)){

	if ($avatartype == 'jpg' or $avatartype == 'jpe'){

		$profileimage = imagecreatefromjpeg($avatar_url);

	} elseif ($avatartype == 'png'){

		$profileimage = imagecreatefrompng($avatar_url);

	} elseif ($avatartype == 'gif'){

		$profileimage = imagecreatefromgif($avatar_url);

	}

	

	if ($profileimage != '') {

		$profile_width = imagesx($profileimage);  

		$profile_height = imagesy($profileimage); 

	}

}



/* Font setting */

$font = WP_PLUGIN_DIR.'/buddypress-badge/includes/fonts/tahoma.ttf';

$fontbold = WP_PLUGIN_DIR.'/buddypress-badge/includes/fonts/tahomabd.ttf';

$fontsize = 10;

$fontsizebold = $fontsize-1;



// Start Create Vertical or Horizontal Badge



/* Vertical badge */



if (get_usermeta( $bp->loggedin_user->id, 'bp_badge_userlayout' ) == 'vertical') {

// get logo information

$logoimage = imagecreatefrompng(WP_PLUGIN_DIR.'/buddypress-badge/includes/images/logo.png');  

$logo_width = imagesx($logoimage);  

$logo_height = imagesy($logoimage); 



// get xprofile information



foreach ($xprofile_array as $key => $val){

	if ( in_array($key,$bp_badge_useroption) ){

	$xprofile .= $val.'

';

	}

}

// remove unwant string - http://

$xprofile = str_replace('http://','',$xprofile);



$wrapcount = $logo_width/8;

$wraptext = wordwrap($xprofile, $wrapcount, "\n", true);

$boxsize = imagettfbbox($fontsize, 0, $font, $wraptext); //calculate the pixel of the string

$textwidth = $boxsize[4]-$boxsize[6];

$textheight = $boxsize[1]-$boxsize[7];

$text_dest_x = 10;

$text_dest_y = $logo_height+10+$profile_height+20;



$badge_width = $logo_width;

$badge_height = $logo_height + $profile_height + 10 + $textheight + 20;



$badge = imagecreatetruecolor($badge_width, $badge_height);

$badge_bg = imagecreate($badge_width, $badge_height);

$bgcolor = bp_badge_hex2rgb(get_site_option('bp_badge_bgcolor'));

$change_background = imagecolorallocate($badge_bg, $bgcolor[0], $bgcolor[1], $bgcolor[2]);



/* set text and border color */

$color = bp_badge_hex2rgb(get_site_option('bp_badge_textcolor'));

$textcolor = imagecolorallocate($badge, $color[0], $color[1], $color[2]);



$color = bp_badge_hex2rgb(get_site_option('bp_badge_fieldtextcolor'));

$fieldtextcolor = imagecolorallocate($badge, $color[0], $color[1], $color[2]);



$color = bp_badge_hex2rgb(get_site_option('bp_badge_bordercolor'));

$bordercolor = imagecolorallocate($badge, $color[0], $color[1], $color[2]);



$logo_dest_x = 0;

$logo_dest_y = 0;

$profile_dest_x = ($badge_width - $profile_width)/2;

$profile_dest_y = $logo_height+5;



imagecopy($badge, $badge_bg, 0, 0, 0,0, $badge_width, $badge_height); 

if (in_array('photo',$bp_badge_useroption) and $profileimage != ''){

imagecopy($badge, $profileimage, $profile_dest_x, $profile_dest_y, 0,0, $profile_width, $profile_height);  

}

imagecopy($badge, $logoimage, $logo_dest_x, $dest_y, 0,0, $logo_width, $logo_height);  



if ($wraptext != '') {

$exptext = explode("\n",$wraptext);

$textline_height = $textheight/(count($exptext)-1);

foreach ($exptext as $text){

	if (strstr($text,':') != false){

	   $fieldtext = explode(':',$text,2);

	   $fieldtext[0] = $fieldtext[0].' : ';

	   $fieldtextsize = imagettfbbox($fontsizebold, 0, $fontbold, $fieldtext[0]);

	   $fieldtextwidth = $fieldtextsize[4]-$fieldtextsize[6];

	   if ( get_site_option('bp_badge_boldfieldtext') == true ) {

	   	imagettftext($badge, $fontsizebold, 0, $text_dest_x, $text_dest_y, $fieldtextcolor, $fontbold, $fieldtext[0]);

	   } else {

	   	imagettftext($badge, $fontsize, 0, $text_dest_x, $text_dest_y, $fieldtextcolor, $font, $fieldtext[0]);

	   }

	   imagettftext($badge, $fontsize, 0, $text_dest_x + $fieldtextwidth, $text_dest_y, $textcolor, $font, $fieldtext[1]);

	} else {

	imagettftext($badge, $fontsize, 0, $text_dest_x, $text_dest_y, $textcolor, $font, $text);

	}

	$text_dest_y = $text_dest_y + $textline_height;

}

}

//imagettftext($badge, $fontsize, 0, $text_dest_x, $text_dest_y, $textcolor, $font, $wraptext);



if ( get_site_option('bp_badge_showbadgeborder') == true ) {

imageline($badge,0,0,$badge_width,0,$bordercolor);

imageline($badge,$badge_width-1,0,$badge_width-1,$badge_height,$bordercolor);

imageline($badge,$badge_width-1,$badge_height-1,0,$badge_height-1,$bordercolor);

imageline($badge,0,$badge_height-1,0,0,$bordercolor);

}



/* save image to file */

$badgedir = BP_BADGE_IMAGE_PATH;

if ( !file_exists( $badgedir ) )

		wp_mkdir_p( $badgedir );

$save_image = imagepng($badge,$badgedir.'/'.$bp->displayed_user->id.'.png');



imagedestroy($logoimage);

if (in_array('photo',$bp_badge_useroption) and $profileimage != ''){

imagedestroy($profileimage);

}

imagedestroy($badge);



} else {



/* Horizontal badge */



// get logo information

$logoimage = imagecreatefrompng(WP_PLUGIN_DIR.'/buddypress-badge/includes/images/logo.png');  

$logoimage = imagerotate($logoimage, 90, 0);

$logo_width = imagesx($logoimage);  

$logo_height = imagesy($logoimage); 



// get xprofile information



$column_width = 160;

$wrapcount = $column_width/8;

$column_no = 1;

$column_height = 0;

$xprofile[$column_no] = '';



foreach ($xprofile_array as $key => $val){



// remove unwant string - http://

$val = str_replace('http://','',$val);



	if ( in_array($key,$bp_badge_useroption) ){

$wrapval = wordwrap($val, $wrapcount, "\n", true);

$valsize = imagettfbbox($fontsize, 0, $font, $wrapval); //calculate the pixel of the string

$valwidth = $valsize[4]-$valsize[6];

$valheight = $valsize[1]-$valsize[7];

	if (($column_height + $valheight) > $logo_height){

	$column_no = $column_no + 1;

	$column_height = 0;

	}

$xprofile[$column_no] .= $wrapval.'

';

$column_height = $column_height + $valheight;

	}

}







$text_dest_x = $logo_width + 10 + $profile_width + 10;

$text_dest_y = 15;

// if no xprofile data do not create column

if ($xprofile[1] == '') {$column_width = 0;}

$badge_width = $logo_width + 10 + $profile_width + 10 + ($column_width*$column_no) + 20 ;

$badge_height = $logo_height;

$badge = imagecreatetruecolor($badge_width, $badge_height);

$badge_bg = imagecreate($badge_width, $badge_height);

$bgcolor = bp_badge_hex2rgb(get_site_option('bp_badge_bgcolor'));

$change_background = imagecolorallocate($badge_bg, $bgcolor[0], $bgcolor[1], $bgcolor[2]);



/* set text and border color */

$color = bp_badge_hex2rgb(get_site_option('bp_badge_textcolor'));

$textcolor = imagecolorallocate($badge, $color[0], $color[1], $color[2]);



$color = bp_badge_hex2rgb(get_site_option('bp_badge_fieldtextcolor'));

$fieldtextcolor = imagecolorallocate($badge, $color[0], $color[1], $color[2]);



$color = bp_badge_hex2rgb(get_site_option('bp_badge_bordercolor'));

$bordercolor = imagecolorallocate($badge, $color[0], $color[1], $color[2]);



$logo_dest_x = 0;

$logo_dest_y = 0;

$profile_dest_x = $logo_width+5;

$profile_dest_y = ($badge_height - $profile_height)/2;



imagecopy($badge, $badge_bg, 0, 0, 0,0, $badge_width, $badge_height);

if (in_array('photo',$bp_badge_useroption) and $profileimage != ''){

imagecopy($badge, $profileimage, $profile_dest_x, $profile_dest_y, 0,0, $profile_width, $profile_height);  

}

imagecopy($badge, $logoimage, $logo_dest_x, $dest_y, 0,0, $logo_width, $logo_height);  



if ($xprofile[1] != '') {

foreach ($xprofile as $key => $val){

$valsize = imagettfbbox($fontsize, 0, $font, $val); //calculate the pixel of the string

$valwidth = $valsize[4]-$valsize[6];

$valheight = $valsize[1]-$valsize[7];



$exptext = explode("\n",$val);



$textline_height = $valheight/(count($exptext)-1);

	foreach ($exptext as $text){

	if (strstr($text,':') != false){

	   $fieldtext = explode(':',$text,2);

	   $fieldtext[0] = $fieldtext[0].' : ';

	   $fieldtextsize = imagettfbbox($fontsizebold, 0, $fontbold, $fieldtext[0]);

	   $fieldtextwidth = $fieldtextsize[4]-$fieldtextsize[6];

	   if ( get_site_option('bp_badge_boldfieldtext') == true ) {

	   	imagettftext($badge, $fontsizebold, 0, $text_dest_x+($column_width*($key-1)), $text_dest_y, $fieldtextcolor, $fontbold, $fieldtext[0]);

	   } else {

	   	imagettftext($badge, $fontsize, 0, $text_dest_x+($column_width*($key-1)), $text_dest_y, $fieldtextcolor, $font, $fieldtext[0]);

	   }

	   imagettftext($badge, $fontsize, 0, $text_dest_x+($column_width*($key-1))+$fieldtextwidth, $text_dest_y, $textcolor, $font, $fieldtext[1]);

	} else {

	imagettftext($badge, $fontsize, 0, $text_dest_x+($column_width*($key-1)), $text_dest_y, $textcolor, $font, $text);

	}

	$text_dest_y = $text_dest_y + $textline_height;

	}

	// reset y position

	$text_dest_y = 15;

}

}



if ( get_site_option('bp_badge_showbadgeborder') == true ) {

imageline($badge,0,0,$badge_width,0,$bordercolor);

imageline($badge,$badge_width-1,0,$badge_width-1,$badge_height,$bordercolor);

imageline($badge,$badge_width-1,$badge_height-1,0,$badge_height-1,$bordercolor);

imageline($badge,0,$badge_height-1,0,0,$bordercolor);

}



/* save image to file */

$badgedir = BP_BADGE_IMAGE_PATH;

if ( !file_exists( $badgedir ) )

		wp_mkdir_p( $badgedir );

$save_image = imagepng($badge,$badgedir.'/'.$bp->displayed_user->id.'.png');





imagedestroy($logoimage);

if (in_array('photo',$bp_badge_useroption) and $profileimage != ''){

imagedestroy($profileimage);

}

imagedestroy($badge);



}

}

}

?>