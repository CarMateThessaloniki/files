<?php

function fb_head(){
    if( is_user_logged_in()) return;
    ?>

    <div id="fb-root"></div>
    <script>
      // Additional JS functions here
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '<?php echo sq_option('fb_app_id'); ?>', // App ID
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true,  // parse XFBML
          oauth      : true
        });

        // Additional init code here

      };

      // Load the SDK asynchronously
      (function(d){
         var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement('script'); js.id = id; js.async = true;
         js.src = "//connect.facebook.net/en_US/all.js";
         ref.parentNode.insertBefore(js, ref);
       }(document));
    </script>
    <?php
}
add_action( 'kleo_after_body', 'fb_head' );


function fb_footer(){
?>
    <script type="text/javascript">
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    jQuery('.facebook_connect').click(function(){
			var context = jQuery(this).closest("form");
			if (jQuery(".tos_register", context).length > 0)
			{
				if (! jQuery(".tos_register", context).is(":checked"))
				{
					alert('<?php echo apply_filters('kleo_fb_tos_alert',__("You must agree with the terms and conditions.",'kleo_framework'));?>');
					return false;
				}
			}
		
			FB.login(function(FB_response){
				if( FB_response.status === 'connected' ){
								fb_intialize(FB_response);
				}
			},
			{scope: 'email'});
    });

    function fb_intialize(FB_response){
		FB.api( '/me', 'GET', 
			{'fields':'id,email,username,verified,name'},
			function(FB_userdata){
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "fb_intialize", "FB_userdata": FB_userdata, "FB_response": FB_response},
					success: function(user){
						if( user.error ){
							alert( user.error );
						}
						else if( user.loggedin ){
							if( user.type === 'login' )
							{
								window.location.reload();
							}
							else if( user.type === 'register' )
							{
								window.location = user.url;
							}
						}
					}
				});
			}
		);
    };
    </script>
<?php
}
add_action( 'wp_footer', 'fb_footer' );

if (!function_exists('fb_button')):
function fb_button()
{
?>
    <a href="#" class="facebook_connect radius button facebook"><i class="icon-facebook-sign"></i> &nbsp;<?php _e("LOG IN WITH Facebook", 'kleo_framework');?></a>
<?php
}
endif;

if (!function_exists('fb_register_button')):
function fb_register_button()
{
?>
    <a href="#" class="facebook_connect radius small button facebook"><i class="icon-facebook-sign"></i> &nbsp;<?php _e("Register using Facebook", 'kleo_framework');?></a>
<?php
}
endif;

if (!function_exists('fb_register_button_front')):
function fb_register_button_front()
{
?>
    <a href="#" class="facebook_connect radius button facebook"><i class="icon-facebook"></i></a>
<?php
}
endif;

add_action('fb_popup_button', 'fb_button' );

if (sq_option('facebook_register', 0) == 1) {
    add_action('fb_popup_register_button', 'fb_register_button' );
    add_action('fb_popup_register_button_front', 'fb_register_button_front' );
}
        
function wp_ajax_fb_intialize(){
    @error_reporting( 0 ); // Don't break the JSON result
    header( 'Content-type: application/json' );

    if( !isset( $_REQUEST['FB_response'] ) || !isset( $_REQUEST['FB_userdata'] ))
    die( json_encode( array( 'error' => __('Authenication required.', 'kleo_framework') )));

    $FB_response = $_REQUEST['FB_response'];
    $FB_userdata = $_REQUEST['FB_userdata'];
    $FB_userid = (int) $FB_userdata['id'];

    if( !$FB_userid )
    die( json_encode( array( 'error' => __('Please connect your facebook account.', 'kleo_framework') )));

    global $wpdb;
    //check if we already have matched our facebook account
    $user_ID = $wpdb->get_var( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '_fbid' AND meta_value = '$FB_userid'" );
    
	
    //if facebook is not connected
    if( !$user_ID ){
        $user_email = $FB_userdata['email'];
        $user_ID = $wpdb->get_var( "SELECT ID FROM $wpdb->users WHERE user_email = '".$wpdb->escape($user_email)."'" );

        $redirect = '';
        
        //if we have a registered user with this Facebook email
        if(!$user_ID )
        {
            if ( !get_option( 'users_can_register' ))
            die( json_encode( array( 'error' => __('Registration is not open at this time. Please come back later.', 'kleo_framework') )));

			if (sq_option('facebook_register', 0) == 0)
			die( json_encode( array( 'error' => __('Registration using Facebook is not currently allowed. Please use our Register page', 'kleo_framework') )));

            extract( $FB_userdata );

            $display_name = $name;
            $user_login = $username;

            if( empty( $verified ) || !$verified )
            die( json_encode( array( 'error' => __('Your facebook account is not verified. You have to verify your account before proceed login or registering on this site.', 'kleo_framework') )));

            $user_email = $email;
            if ( empty( $user_email ))
            die( json_encode( array( 'error' => __('Please re-connect your facebook account as we couldn\'t find your email address.', 'kleo_framework') )));

            if( empty( $name ))
            die( json_encode( array( 'error' => 'empty_name', __('We didn\'t find your name. Please complete your facebook account before proceeding.', 'kleo_framework') )));

            if( empty( $user_login ))
            $user_login = sanitize_title_with_dashes( sanitize_user( $display_name, true ));

            if ( username_exists( $user_login ))
            $user_login = $user_login. time();

            $user_pass = wp_generate_password( 12, false );
            $userdata = compact( 'user_login', 'user_email', 'user_pass', 'display_name' );
			$userdata =  apply_filters('kleo_fb_register_data', $userdata);

            $user_ID = wp_insert_user( $userdata );
            if ( is_wp_error( $user_ID ))
            die( json_encode( array( 'error' => $user_ID->get_error_message())));
            
			//send email with password
			wp_new_user_notification( $user_ID, wp_unslash( $user_pass ) );

			//add Facebook image
			update_user_meta($user_ID, 'kleo_fb_picture', 'https://graph.facebook.com/' . $id . '/picture');
						
            do_action('fb_register_action',$user_ID);
            
            update_user_meta( $user_ID, '_fbid', (int) $id );
            $logintype = 'register';
            $redirect = apply_filters('kleo_fb_register_redirect',bp_core_get_user_domain( $user_ID ).'profile/edit/group/1/?fb=registered');
        }
        else
        {
            update_user_meta( $user_ID, '_fbid', (int) $FB_userdata['id'] );
            $logintype = 'login';
        }
    }
    else
    {
            $logintype = 'login';
    }

    wp_set_auth_cookie( $user_ID, false, false );
    die( json_encode( array( 'loggedin' => true, 'type' => $logintype, 'url' => $redirect )));
}
add_action( 'wp_ajax_nopriv_fb_intialize', 'wp_ajax_fb_intialize' );  
        
//If registered via Facebook -> show message
add_action( 'template_notices', 'kleo_fb_register_message' );
if (!function_exists('kleo_fb_register_message')):
    function kleo_fb_register_message()
    {
        if (isset($_GET['fb']) && $_GET['fb'] == 'registered')
        {
            echo '<div class="clearfix"></div><br><div class="alert-box success" id="message" data-alert>';
            echo __('Thank you for registering. Please make sure to complete your profile fields bellow.', 'kleo_framework');
            echo '</div>';
        }
    }
endif;

//display Facebook avatar
if(sq_option('facebook_avatar', 1) == 1) {
	//show Facebook avatar in WP
	add_filter('get_avatar', 'kleo_fb_show_avatar', 5, 5);
	//show Facebook avatar in Buddypress
	add_filter('bp_core_fetch_avatar', 'kleo_fb_bp_show_avatar', 3, 5);
	//show Facebook avatar in Buddypress - url version
	add_filter('bp_core_fetch_avatar_url','kleo_fb_bp_show_avatar_url', 3, 2);
}
function kleo_fb_show_avatar($avatar = '', $id_or_email, $size = 96, $default = '', $alt = false) {
  $id = 0;
  if (is_numeric($id_or_email)) {
    $id = $id_or_email;
  } else if (is_string($id_or_email)) {
    $u = get_user_by('email', $id_or_email);
    $id = $u->id;
  } else if (is_object($id_or_email)) {
    $id = $id_or_email->user_id;
  }
  if ($id == 0) return $avatar;
	
	//if we have an avatar uploaded and is not Gravatar return it
	if(strpos($avatar, home_url()) !== FALSE && strpos($avatar, 'gravatar') === FALSE) return $avatar;
	
	//if we don't have a Facebook photo
  $pic = get_user_meta($id, 'kleo_fb_picture', true);
  if (!$pic || $pic == '') return $avatar;
	
  $avatar = preg_replace('/src=("|\').*?("|\')/i', 'src=\'' . $pic . apply_filters('fb_show_avatar_params', '?width=580&height=580') . '\'', $avatar);
  return $avatar;
}

function kleo_fb_bp_show_avatar($avatar = '', $params, $id) {
    if(!is_numeric($id) || strpos($avatar, 'gravatar') === false) return $avatar;
		
		//if we have an avatar uploaded and is not Gravatar return it
		if(strpos($avatar, home_url()) !== FALSE && strpos($avatar, 'gravatar') === FALSE) return $avatar;
		
    $pic = get_user_meta($id, 'kleo_fb_picture', true);
    if (!$pic || $pic == '') return $avatar;
    $avatar = preg_replace('/src=("|\').*?("|\')/i', 'src=\'' . $pic. apply_filters('fb_show_avatar_params', '?width=580&height=580') . '\'', $avatar);
    return $avatar;
}

function kleo_fb_bp_show_avatar_url($gravatar, $params) {
	
	//if we have an avatar uploaded and is not Gravatar return it
	if(strpos($gravatar, home_url()) !== FALSE && strpos($gravatar, 'gravatar') === FALSE) return $gravatar;
	
  $pic = get_user_meta($params['item_id'], 'kleo_fb_picture', true);
  if (!$pic || $pic == '') return $gravatar;
	return $pic. apply_filters('fb_show_avatar_params', '?width=580&height=580');
}

?>
