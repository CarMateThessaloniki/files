<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * gets the version of the plugin
 * 
 * @uses bp_my_home() to get it!
 */
function bp_my_home_get_version() {
	return bp_my_home()->version;
}

/**
 * displays the slug of the plugin
 * 
 * @uses bp_my_home_get_slug() to get it!
 */
function bp_my_home_slug() {
	echo bp_my_home_get_slug();
}
	
	/**
	 * Gets the slug of the plugin
	 * 
	 * @uses bp_get_option() to eventually use community admin prefs
	 * @uses bp_my_home()
	 * @return string the slug
	 */
	function bp_my_home_get_slug() {
		
		$bp_my_home_slug = bp_get_option( '_bpmh_custom_home_slug', bp_my_home()->bp_my_home_slug );
		
		return apply_filters( 'bp_my_home_get_slug', $bp_my_home_slug );
	}

/**
 * displays the name of the plugin
 * 
 * @uses bp_my_home_get_name() to get it!
 */
function bp_my_home_name() {
	echo bp_my_home_get_name();
}

	/**
	 * Gets the name of the plugin
	 * 
	 * @uses bp_get_option() to eventually use community admin prefs
	 * @uses bp_my_home()
	 * @return string the name
	 */
	function bp_my_home_get_name() {
		
		$bp_my_home_name = bp_get_option( '_bpmh_custom_home_name', bp_my_home()->bp_my_home_name );
		
		return apply_filters( 'bp_my_home_get_name', $bp_my_home_name );
	}

/**
 * displays the name of home nav
 * 
 * @uses bp_my_home_get_home_name() to get it!
 */	
function bp_my_home_home_name() {
	echo bp_my_home_get_home_name();
}
	
	/**
	 * Gets the name of home nav
	 * 
	 * @uses bp_get_option() to eventually use community admin prefs
	 * @uses bp_my_home()
	 * @return string the name
	 */
	function bp_my_home_get_home_name() {

		$bp_my_home_home_name = bp_get_option( '_bpmh_custom_nav_home_name', bp_my_home()->nav_my_home_name );

		return apply_filters( 'bp_my_home_get_home_name', $bp_my_home_home_name );
	}
	
/**
 * displays the slug of widgets subnav
 * 
 * @uses bp_my_home_get_widgets_slug() to get it!
 */
function bp_my_home_widgets_slug() {
	echo bp_my_home_get_widgets_slug();
}

	/**
	 * Gets the slug of widgets subnav
	 * 
	 * @uses bp_get_option() to eventually use community admin prefs
	 * @uses bp_my_home()
	 * @return string the slug
	 */
	function bp_my_home_get_widgets_slug() {
		
		$bp_my_home_widgets_slug = bp_get_option( '_bpmh_custom_nav_widgets_slug', bp_my_home()->nav_my_widgets_slug );

		return apply_filters( 'bp_my_home_get_widgets_slug', $bp_my_home_widgets_slug );
	}
	
/**
 * displays the name of widgets subnav
 * 
 * @uses bp_my_home_get_widgets_name() to get it!
 */
function bp_my_home_widgets_name() {
	echo bp_my_home_get_widgets_name();
}

	/**
	 * Gets the name of widgets subnav
	 * 
	 * @uses bp_get_option() to eventually use community admin prefs
	 * @uses bp_my_home()
	 * @return string the name
	 */
	function bp_my_home_get_widgets_name() {

		$bp_my_home_widgets_name = bp_get_option( '_bpmh_custom_nav_widgets_name', bp_my_home()->nav_my_widgets_name );

		return apply_filters( 'bp_my_home_get_widgets_name', $bp_my_home_widgets_name );
	}

/**
 * displays the slug of settings subnav
 * 
 * @uses bp_my_home_get_settings_slug() to get it!
 */	
function bp_my_home_settings_slug() {
	echo bp_my_home_get_settings_slug();
}

	/**
	 * Gets the slug of settings subnav
	 * 
	 * @uses bp_get_option() to eventually use community admin prefs
	 * @uses bp_my_home()
	 * @return string the slug
	 */
	function bp_my_home_get_settings_slug() {

		$bp_my_home_settings_slug = bp_get_option( '_bpmh_custom_nav_settings_slug', bp_my_home()->nav_my_settings_slug );

		return apply_filters( 'bp_my_home_get_settings_slug', $bp_my_home_settings_slug );
	}

/**
 * displays the name of settings subnav
 * 
 * @uses bp_my_home_get_widgets_name() to get it!
 */		
function bp_my_home_settings_name() {
	echo bp_my_home_get_settings_name();
}

	/**
	 * Gets the name of settings subnav
	 * 
	 * @uses bp_get_option() to eventually use community admin prefs
	 * @uses bp_my_home()
	 * @return string the name
	 */
	function bp_my_home_get_settings_name() {

		$bp_my_home_settings_name = bp_get_option( '_bpmh_custom_nav_settings_name', bp_my_home()->nav_my_settings_name );

		return apply_filters( 'bp_my_home_get_settings_name', $bp_my_home_settings_name );
	}
	

/**
 * What is the path of the plugin dir ?
 *
 * @uses  bp_my_home()
 * @return string the path
 */
function bp_my_home_get_plugin_dir() {
	return bp_my_home()->plugin_dir;
}

/**
 * What is the url to the plugin dir ?
 *
 * @uses  bp_my_home()
 * @return string the url
 */
function bp_my_home_get_plugin_url() {
	return bp_my_home()->plugin_url;
}

/**
 * What is the path to the includes dir ?
 *
 * @uses  bp_my_home()
 * @return string the path
 */
function bp_my_home_get_includes_dir() {
	return bp_my_home()->includes_dir;
}

/**
 * What is the url of includes dir ?
 *
 * @uses  bp_my_home()
 * @return string the url
 */
function bp_my_home_get_includes_url() {
	return bp_my_home()->includes_url;
}

/**
 * What is the url of css dir ?
 *
 * @uses  bp_my_home()
 * @return string the url
 */
function bp_my_home_get_css_url() {
	return apply_filters( 'bp_my_home_get_css_url', bp_my_home()->css_url );
}

/**
 * What is the url of js dir ?
 *
 * @uses  bp_my_home()
 * @return string the url
 */
function bp_my_home_get_js_url() {
	return bp_my_home()->js_url;
}

/**
 * Are we on root blog ?
 *
 * @uses  bp_get_root_blog_id() to check it
 * @return boolean true|false
 */
function bp_my_home_is_root_blog() {
	global $blog_id;
	
	if( $blog_id == bp_get_root_blog_id() )
		return true;
	else
		return false;
}

/**
 * Displays the url to user's my home
 *
 * @uses  bp_my_home_get_user_home_link() to get it
 */
function bp_my_home_user_home_link() {
	echo bp_my_home_get_user_home_link();
}
	
	/**
	 * Gets the url to user's my home ?
	 *
	 * @uses  bp_loggedin_user_domain() to get user's domain url
	 * @uses  bp_my_home_get_slug() to get the slug of my home
	 * @return string the link
	 */
	function bp_my_home_get_user_home_link() {
		$link = trailingslashit( bp_loggedin_user_domain() . bp_my_home_get_slug() );
	
		return apply_filters( 'bp_my_home_get_user_home_link', $link );
	}


/**
 * Displays the url to user's my settings
 *
 * @uses  bp_my_home_get_user_settings_link() to get it
 */
function bp_my_home_user_settings_link() {
	echo bp_my_home_get_user_settings_link();
}

	/**
	 * Gets the url to user's my settings ?
	 *
	 * @uses  bp_loggedin_user_domain() to get user's domain url
	 * @uses  bp_my_home_get_settings_slug() to get the slug of my settings
	 * @return string the link
	 */
	function bp_my_home_get_user_settings_link() {
		$link = trailingslashit( bp_my_home_get_user_home_link() . bp_my_home_get_settings_slug() );
	
		return apply_filters( 'bp_my_home_get_user_settings_link', $link );
	}


/**
 * BP My Home is the new Member's default component
 *
 * @uses  bp_get_option() to check for a different admin setting 
 * @return boolean true|false
 */
function bpmh_is_default_component() {
	$admin_enable = bp_get_option( '_bpmh_is_default_member_component' );
	
	$is_default = true;
	
	if( !empty( $admin_enable ) )
		$is_default = false;
	
	return apply_filters( 'bpmh_is_default_component', $is_default );
}

/**
 * Checks if the loggedin user choose my home as the website landing page
 *
 * @since  2.0.1
 * @see  http://wordpress.org/support/topic/use-as-home-page-landing-upsets-ms-subdomains-possibly
 *
 * @global blog_id current blog id
 * @uses  is_user_logged_in() to check if the user is logged in
 * @uses  bp_get_option() to check for a different admin setting
 * @uses  bp_loggedin_user_id() to get current user's id
 * @uses  get_user_meta() to get user's preference
 * @uses  bpmh_admin_allowed_home_page() to check if the admin authorized this behavior
 * @return boolean true|false
 */
function bpmh_is_root_home_page() {
	global $blog_id;

	$retval = false;

	if( $blog_id != bp_get_root_blog_id() )
		return $retval;

	if( is_user_logged_in() && is_front_page() ) {

		$user_id = bp_loggedin_user_id();

		$user_setting = get_user_meta( $user_id, 'bpmh_user_home_page', true );

		if( !empty( $user_setting ) )
			$retval = true;

		if( !bpmh_admin_allowed_home_page() )
			$retval = false;

	}

	return apply_filters( 'bpmh_is_root_home_page', $retval );
}

/**
 * Checks if the admin authorized users to choose my home as their website landing page
 *
 * @uses  bp_get_option() to check for a admin setting 
 * @return boolean true|false
 */
function bpmh_admin_allowed_home_page() {
	$admin_settings = bp_get_option( '_bpmh_deactivate_redirect_to_home', 0 );
	
	if( !empty( $admin_settings ) )
		return false;
	else
		return true;
}

/**
 * Checks if we're on my home user's page
 *
 * @uses  bp_is_current_component() to check my home is the current component
 * @uses  bp_my_home_get_slug() to get the slug of my home
 * @uses  bp_current_action() to check we're not on my settings subpage
 * @uses  bp_my_home_get_settings_slug() to get the slug of my settings
 * @uses  bp_action_variables() to check no action variables was passed
 * @return boolean true|false
 */
function bp_my_home_current_is_widgets() {
	if( bp_is_current_component( bp_my_home_get_slug() ) && bp_current_action() != bp_my_home_get_settings_slug() && !bp_action_variables() )
		return true;
	else
		return false;
}

/**
 * Checks if we're on my setting user's page
 *
 * @uses  bp_is_current_component() to check my home is the current component
 * @uses  bp_my_home_get_slug() to get the slug of my home
 * @uses  bp_current_action() to check we're on my settings subpage
 * @uses  bp_my_home_get_settings_slug() to get the slug of my settings
 * @uses  bp_action_variables() to check no action variables was passed
 * @return boolean true|false
 */
function bp_my_home_current_is_settings() {
	if( bp_is_current_component( bp_my_home_get_slug() ) && bp_current_action() == bp_my_home_get_settings_slug() && !bp_action_variables() )
		return true;
	else
		return false;
}

/**
 * Checks if we're on a widget setting user's page
 *
 * @uses  bp_is_current_component() to check my home is the current component
 * @uses  bp_my_home_get_slug() to get the slug of my home
 * @uses  bp_current_action() to check we're on my settings subpage
 * @uses  bp_my_home_get_settings_slug() to get the slug of my settings
 * @uses  bp_action_variables() to check action variables was passed
 * @return boolean true|false
 */
function bp_my_home_current_is_widget_settings() {
	if( bp_is_current_component( bp_my_home_get_slug() ) && bp_current_action() == bp_my_home_get_settings_slug() && bp_action_variable( 0 ) == 'widget' && bp_action_variable( 1 ) )
		return true;
	else
		return false;
}

/**
 * Displays a message if no widgets was set by admin
 *
 * @uses  wp_get_sidebars_widgets() to get all active widgets before filtering it
 * @return string html message
 */
function bp_my_home_sidebars_empty_message() {
	$all_widgets = wp_get_sidebars_widgets();

	if( empty( $all_widgets['bpmh-left-sidebar'] ) )
		$all_widgets['bpmh-left-sidebar'] = array();

	if( empty( $all_widgets['bpmh-right-sidebar'] ) )
		$all_widgets['bpmh-right-sidebar'] = array();
	
	$bpmh_widgets = array_merge( $all_widgets['bpmh-left-sidebar'], $all_widgets['bpmh-right-sidebar'] );
	
	if( !empty( $bpmh_widgets ) )
		return false;
		
	?>
	<div id="message" class="info">
		<p><?php _e( 'Sorry, the administrator has not activated any widget so far.', 'bp-my-home' ); ?></p>
	</div>
	<?php
}

/**
 * Gets the collapsed state of widgets and checks if admin changed widgets
 *
 * @uses  bp_loggedin_user_id() to get current user's id
 * @uses  get_user_meta() to get user's preference
 * @uses  wp_get_sidebars_widgets() to get all active widgets before filtering it
 * @uses  update_user_meta() to eventually update user's preference
 * @return array the collapsed state for each widget
 */	
function bp_my_home_collapsed_widgets() {
	$collapsed = array();
	$admin_changed = false;
	
	$user_id = bp_loggedin_user_id();
	$user_widgets_pref = get_user_meta( $user_id, '_bpmh_user_saved_state', true );
	
	if( empty( $user_widgets_pref ) || !is_array( $user_widgets_pref ) )
		return $collapsed;
	
	/* if widget ids has changed due to admin manipulation in appearance widgets menu, we need to delete the saved states */
	$all_widgets = wp_get_sidebars_widgets();

	if( empty( $all_widgets['bpmh-left-sidebar'] ) )
		$all_widgets['bpmh-left-sidebar'] = array();

	if( empty( $all_widgets['bpmh-right-sidebar'] ) )
		$all_widgets['bpmh-right-sidebar'] = array();

	$bpmh_widgets = array_merge( $all_widgets['bpmh-left-sidebar'], $all_widgets['bpmh-right-sidebar'] );
	
	$widget_id_check = array_keys( $user_widgets_pref );
	
	foreach( $widget_id_check as $widget_id ) {
		if( !empty( $bpmh_widgets ) && !in_array( $widget_id, $bpmh_widgets ) ) {
			$admin_changed = true;
			unset( $user_widgets_pref[$widget_id] );
		}
	}
	
	if( $admin_changed )
		update_user_meta( $user_id, '_bpmh_user_saved_state', $user_widgets_pref );
	
	if( count( $user_widgets_pref ) < 1 )
		return $collapsed;
	
	foreach( $user_widgets_pref as $key => $pref ){
		$collapsed[] = array( 'widget_id' => $key, 'collapsed' => $pref['collapsed'] );
	}
	
	return $collapsed;
}

/**
 * Displays a single BPMH widget in order to set user's preferences for it
 *
 * @global object $wp_widget_factory
 * @param string $widget the name of the widget
 * @param array $instance
 * @param array $args
 * @uses  wp_parse_args() to merge args with defaults one
 * @uses  bpmh_widget_get_title() to get widget's title
 */
function bpmh_the_widget( $widget, $instance = array(), $args = array() ) {
	global $wp_widget_factory;

	$widget_obj = $wp_widget_factory->widgets[$widget];
	if ( !is_a($widget_obj, 'WP_Widget') )
		return;

	$before_widget = sprintf('<div class="widget %s">', $widget_obj->widget_options['classname'] );
	$default_args = array( 'before_widget' => $before_widget, 'after_widget' => "</div>", 'before_title' => '<h2 class="widgettitle">', 'after_title' => '</h2>' );
	
	$args = wp_parse_args($args, $default_args);
	
	$default_instance = array( 'title' => bpmh_widget_get_title( $widget_obj ) );
	$instance = wp_parse_args( $instance, $default_instance );

	do_action( 'bpmh_the_widget', $widget );

	$widget_obj->_set(-1);
	$widget_obj->user_settings( $args, $instance );
}

/**
 * Displays a single BPMH widget in order to set user's preferences for it
 *
 * @param object $widget the widget
 * @uses  sanitize_text_field() to sanitize the title
 */
function bpmh_widget_get_title( $widget ) {
	$title = false;
	
	if( empty( $widget ) || !is_object( $widget ) )
		return $title;
	
	$settings = $widget->get_settings();
	$number = $widget->number;
	
	if( !empty( $settings[$number]['title'] ) )
		$title = sanitize_text_field( $settings[$number]['title'] );
		
	return $title;
}

/**
 * Displays a single BPMH widget in order to set user's preferences for it
 *
 * @param array $user_settings the setting to search in
 * @param $current the setting to search for
 * @uses  checked() to eventually activate the checkbox
 * @return string checked or not ?
 */
function bpmh_checked_in_array( $user_settings, $current ) {
	
	return checked( false, in_array( $current, $user_settings) );
}

/**
 * Checks if a BPMH widget is active
 *
 * @param string $widget widget's id base to check
 * @uses  is_active_widget() to check if the widget is active
 * @uses  is_admin() to be sure we're not in blog's backend
 * @uses  is_network_admin() to be sure we're not in network's backend
 * @return boolean true|false
 */
function bp_my_home_is_activated_widget( $widget = '' ) {
	$retval = false;
	
	if( empty( $widget )  )
		return false;
		
	if( is_active_widget( false, false, $widget ) && !is_admin() && !is_network_admin() )
		$retval = true;
		
	return $retval;
		
}

/**
 * Checks if a user hasn't deactivated a widget
 *
 * @global int $blog_id the current blog id
 * @global object $wp_widget_factory
 * @param string $widget widget's class name
 * @uses  bp_get_option() to check for admin setting
 * @uses  get_user_meta() to get user's preference
 * @uses  bp_loggedin_user_id() to get current user's id
 * @uses  bp_get_root_blog_id() to get root blog id
 * @uses  is_active_widget() to check the widget is active
 * @return boolean true|false
 */
function bp_my_home_user_activated_widget( $widget = '' ) {
	global $blog_id, $wp_widget_factory;
	
	if( empty( $widget ) )
		return false;
	
	$admin_auto_rss = bp_get_option( '_bpmh_auto_rss_option' );
	$admin_auto_bkmk = apply_filters( 'bp_my_home_admin_bkmk_button', bp_get_option( '_bpmh_auto_bkmk_option' ) );
	
	if( $widget == 'BPMH_Widget_Rss' && !empty( $admin_auto_rss ) )
		return false;
	if( $widget == 'BPMH_Widget_Bookmark' && !empty( $admin_auto_bkmk ) )
		return false;
	
	$src = $retval = false;
	$user_deactivated = get_user_meta( bp_loggedin_user_id(), 'bpmh_user_activated_widgets', true );
	
	if( empty( $user_deactivated ) )
		$user_deactivated = array();
	
	if( $blog_id != bp_get_root_blog_id() ) {
		//switch_to_blog() cannot help there as it's not loading the sidebars of the switched blog..
		$sidebars = bp_get_option( 'sidebars_widgets' );
		
		switch( $widget ) {
			case 'BPMH_Widget_Bookmark' :
				$src = 'bpmh_bookmark';
				break;
			case 'BPMH_Widget_Rss' :
				$src = 'bpmh_rss';
				break;
		}
		
		$bpmh_widgets = array_merge( $sidebars['bpmh-left-sidebar'], $sidebars['bpmh-right-sidebar'] );
		
		if( is_array( $bpmh_widgets ) && count( $bpmh_widgets ) > 0 && !empty( $src ) ) {
			
			foreach( $bpmh_widgets as $item ) {
				if( strpos( $item, $src ) === 0 ) {
					if( !in_array( $item, $user_deactivated ) )
						$retval = true;
				}
			}
			
		} else {
			$retval = false;
		}
		
	} else {
		$widget_obj = $wp_widget_factory->widgets[$widget];
		if ( !is_a($widget_obj, 'WP_Widget') )
			return false;

		if( is_active_widget( false, false, $widget_obj->id_base ) && !in_array( $widget_obj->id, $user_deactivated ) )
			$retval = true;
	}

	return apply_filters( 'bp_my_home_user_activated_widget', $retval, $widget );
	
}

/**
 * Search in a subarray to check for a value
 *
 * @return boolean true|false
 */
function bpmh_is_in_array( $search, $user_option ) {
	$retval = false;
	
	if( empty( $search ) || empty( $user_option ) )
		return false;
		
	if( is_array( $user_option ) && count( $user_option ) > 0 ) {
		foreach( $user_option as $option ) {
			if( in_array( $search, $option ) )
				$retval = true;
		}
	}
	
	return $retval;
}

/**
 * Displays a link to add a bookmark in the BPMH Bkmk Widget
 *
 * @uses  bpmh_bkmks_add_in_content() to build the link
 */
function the_bpmh_bkmks_tag(){
	// if the admin use this tag, then he really wants to !
	add_filter( 'bp_my_home_admin_bkmk_button', '__return_false' );
	echo bpmh_bkmks_add_in_content( '' );
	remove_filter( 'bp_my_home_admin_bkmk_button', '__return_false' );
}

/**
 * Displays a link to add a feed in the BPMH rss Widget
 *
 * @global object $wp_query
 * @global object $post
 * @global object $query_string
 * @param string $feedalias the alias of the feed
 * @param string $feedlink the link of the feed
 * @uses  bp_my_home_user_activated_widget() to check the user hasn't deactivated the widget
 * @uses  is_ssl() to check for the protocol
 * @uses  get_option() to check for current blog option
 * @uses  get_bloginfo() to get some info about the blog
 * @uses  is_search() are we performing a search ?
 * @uses  is_category() are we on the category template ?
 * @uses  is_tag() are we on the tag template ?
 * @uses  is_single() are we on the single template ?
 * @uses  get_the_title() to get the title of the post
 * @uses  get_user_meta() to get user's preference
 * @uses  bp_loggedin_user_id() to get current user's id
 * @uses  bpmh_is_in_array() to check if the feed is already saved by user
 * @uses  esc_url_raw() to escape the url
 * @uses  wp_nonce_field() for security reason
 * @return the html part for the Subscribe button
 */
function the_bpmh_rss_button( $feedalias = '', $feedlink = '' ){
	global $wp_query, $post, $query_string;
	
	if( !bp_my_home_user_activated_widget( 'BPMH_Widget_Rss' ) )
		return false;

	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	
	if( empty( $feedalias ) && empty( $feedlink ) ) {
		
		$permalink_structure = get_option( 'permalink_structure' );
		$feedalias = get_bloginfo( 'name' );
		
		if( !empty( $permalink_structure ) && !is_search() ){
			$feedlink = $current_url . 'feed';
		}
		else{
			$feedlink = $current_url. '&feed=rss2';
		}
		
		if ( is_category() ) {
			$cat = $wp_query->get_queried_object();
			$feedalias .= " | ".$cat->name;
		} elseif ( is_tag() ) {
			$tags = $wp_query->get_queried_object();
			$feedalias .= " | ".$tags->slug;
		} elseif( is_single() ){
			$title = get_the_title( $post->ID );
			$feedalias .= " | ".$title;
		} elseif( is_search() ){
			$search_title = explode("=",$query_string);
			$feedalias .= " | ".__('Results for ','bp-my-home').$search_title[1];
		}
	}

	$getuser_feeds = get_user_meta( bp_loggedin_user_id(), 'bpmh_rss_feeds', true );
	
	if( empty( $getuser_feeds ) )
		$getuser_feeds = array();
		
	if( bpmh_is_in_array( esc_url_raw( $feedlink ), $getuser_feeds ) ) {
		?>
		<a class="bpmh-add-rss" href="<?php bp_my_home_user_home_link();?>" title="<?php _e('Bookmarked', 'bp-my-home' );?>"><span class="icon-rss"></span><span class="bpmh-message"><?php _e( 'Subscribed', 'bp-my-home' );?></span></a>
		<?php
		
	} else {
		?>
		<a class="bpmh-add-rss" href="#" data-bpmhfeed="<?php echo esc_url( $feedlink );?>" data-bpmhalias="<?php echo esc_attr( $feedalias );?>" title="<?php _e( 'Subscribe', 'bp-my-home' );?>"><span class="icon-rss"></span><span class="bpmh-message"><?php _e( 'Subscribe', 'bp-my-home' );?></span><?php wp_nonce_field( 'bpmh_rss', '_wpnoncebpmh_rss' );?></a>
		<?php
	}

}

/**
 * Builds 5 entries from a rss feed
 *
 * @param string $feed the url of the feed to fetch
 * @uses  fetch_feed() to get the feed
 * @uses  esc_html() to sanitize content
 * @uses  esc_url() to sanitize url
 * @uses  get_option() to get current blog option
 * @return string the html part for the 5 entries
 */
function bpmh_rss_fetch_items( $feed = '' ) {
	$display_feed = array();

	if( empty( $feed ) )
		return $display_feed;
	
	$reader = fetch_feed( html_entity_decode( $feed ) );

	if( !empty( $reader->errors ) )
		return array( '<p>' . sprintf( __( '<h5>Error:</h5> Oops something went wrong with this feed %s', 'bp-my-home' ), $feed ) .'</p>' );
	
	$site = esc_html( $reader->get_title() );
	$items = $reader->get_items( 0, 5 );

	if( empty( $items ) )
		return array( '<p>' . sprintf( __( '<h5>Error:</h5> No items were found in this feed %s', 'bp-my-home' ), $feed ) .'</p>' );
	
	for( $i=0; $i < count( $items ); $i++ ) {
		$item = $items[$i];
		$idrss = $item->get_date( 'YmdHis' );
		$daterss = $item->get_date( 'd/m/Y, H:i' );
		$link = esc_url( $item->get_link() );
		$title = esc_html( $item->get_title() );
		$description = esc_html( strip_tags( @html_entity_decode( $item->get_description(), ENT_QUOTES, get_option( 'blog_charset' ) ) ) );
		$rss_content = '<h5><a href='.$link.' title="'.__('Go to:', 'bpmh-rss').' '.$title.'">'.$title.'</a></h5><p style="margin-bottom:5px">'.$site.'&nbsp;'.$daterss.'</p><p>'.$description.'</p>';
		
		$display_feed[$idrss] = $rss_content;
	}
	
	return $display_feed;
}

/**
 * Builds an array of feed to loop in
 *
 * @param string $selected the url of the feed to filter on
 * @uses  get_user_meta() to get user's preference
 * @uses  bp_loggedin_user_id() to get current user's id
 * @uses  bpmh_rss_fetch_items() to populate the array
 * @return array the different feeds ordered by date
 */
function bpmh_rss_grab_feed( $selected = "" ){
	$feeds_to_parse = array();
	$feedsparsed = $temp_feeds = array();
	
	if( !empty( $selected ) && $selected == 'all' )
		$selected = false;
	
	// if none passed get all !
	if( empty( $selected ) ) {
		
		$user_id = bp_loggedin_user_id();
		$feeds_to_parse = get_user_meta( $user_id, 'bpmh_rss_feeds', true );
		
		if( !empty( $feeds_to_parse ) && is_array( $feeds_to_parse ) && count( $feeds_to_parse ) > 0 ) {
			foreach( $feeds_to_parse as $feed ) {
				$temp_feeds[] = bpmh_rss_fetch_items( $feed['url'] );;
			}
		}
		
		foreach( $temp_feeds as $tidyfeed ) {
			foreach( $tidyfeed as $key => $value ) {
				$feedsparsed[$key] = $value;
			}
		}
		
	} else {
		$feedsparsed = bpmh_rss_fetch_items( $selected );
	}
	
	krsort( $feedsparsed );
	return $feedsparsed;
}

/**
 * Builds an array of available blogs
 *
 * @uses  bp_is_active() to check BuddyPress blogs component is active
 * @uses  the BuddyPress blogs loop
 * @return array the list of blogs for the network
 */
function bpmh_list_blogs() {
	
	if( !bp_is_active( 'blogs') )
		return false;
		
	$blogs = array();
	
	// see bp_has_blogs to get the available arguments and use this filter to customise..
	$args = apply_filters( 'bpmh_list_blogs_args', array( 'user_id' => false ) );
	
	if ( bp_has_blogs( $args ) ) :

		while ( bp_blogs() ) : bp_the_blog();
		
		$blogs[] = array( 'blog_id' => bp_get_blog_id() , 'blog_name' => bp_get_blog_name() );

		endwhile;
		
	endif;
	
	return apply_filters( 'bpmh_list_blogs', $blogs );
	
}

/**
 * Builds an array of comments for the blog requested
 *
 * @global object $wpdb
 * @param int $user_id
 * @param int the blog_id
 * @uses  bp_loggedin_user_id() to get current user's id
 * @return array the list of comments
 */
function bpmh_db_list_comments( $user_id = 0, $blog_id = 0 ) {
	global $wpdb;
	
	if( empty( $user_id ) )
		$user_id = bp_loggedin_user_id();
		
	$blog_prefix = $wpdb->base_prefix;
		
	if( !empty( $blog_id ) )
		$blog_prefix = $wpdb->base_prefix . $blog_id . '_';
	
	$sql_comment = $wpdb->get_results("SELECT DISTINCT({$blog_prefix}posts.ID) AS the_post_id, {$blog_prefix}posts.post_title, (SELECT comment_ID
		FROM {$blog_prefix}comments
		WHERE comment_post_id = the_post_id AND user_id='$user_id'
		ORDER BY comment_date DESC 
		LIMIT 1
		) AS the_comment_id, {$blog_prefix}posts.comment_count,{$blog_prefix}comments.comment_ID, {$blog_prefix}comments.comment_content, {$blog_prefix}comments.comment_date as date_comment, (SELECT COUNT({$blog_prefix}comments.comment_id) FROM {$blog_prefix}comments WHERE {$blog_prefix}comments.comment_date > date_comment AND {$blog_prefix}comments.comment_post_id = the_post_id AND {$blog_prefix}comments.comment_approved=1) as diff_comment, {$blog_prefix}comments.comment_approved
		FROM {$blog_prefix}posts LEFT JOIN {$blog_prefix}comments ON({$blog_prefix}posts.ID = {$blog_prefix}comments.comment_post_id) WHERE {$blog_prefix}comments.user_id='$user_id' AND {$blog_prefix}posts.post_status='publish' AND {$blog_prefix}comments.comment_approved IN('0','1') HAVING the_comment_id IS NOT NULL AND {$blog_prefix}comments.comment_ID = the_comment_id ORDER BY {$blog_prefix}comments.comment_date DESC LIMIT 5");
	
	return $sql_comment;
}

/**
 * Displays the html part for the BPMH Comments widget
 *
 * @uses bpmh_db_list_comments() to get the comments
 * @uses  bp_loggedin_user_id() to get current user's id
 * @uses  switch_to_blog() to safely change the blog
 * @uses  get_permalink() to build the link to the post
 * @uses  restore_current_blog() to restore the blog
 * @return string the html part
 */
function bpmh_comments_display_comment_list() {
	$blog_id = !empty( $_POST['blog_id'] ) ? intval( $_POST['blog_id'] ) : 0;
	
	if( $blog_id == 1 )
		$blog_id = 0;
		
	$comments = bpmh_db_list_comments( bp_loggedin_user_id(), $blog_id );
	
	if( empty( $comments ) || !is_array( $comments ) || count( $comments ) < 1 ) {
		echo 'no comments so far';
		return false;
	}
	?>
		<table id="bpmh-table-comments">
		
		<?php if( !empty( $blog_id ) ) switch_to_blog( $blog_id ) ;?>
		
		<?php foreach( $comments as $comment ):?>
			<tr>
				<td>
					<?php _e( 'On :', 'bp-my-home' );?> <a href="<?php echo get_permalink( $comment->the_post_id );?>" title="<?php echo sprintf( __( 'Go to : %s', 'bp-my-home' ), $comment->post_title );?>"><?php echo $comment->post_title;?></a>
					<a href="#" data-commentid="<?php echo $comment->the_comment_id;?>" title="<?php _e( 'Show/hide comment content', 'bpmh-comments' );?>" class="bpmh-comments-view"><?php _e( 'View comment', 'bp-my-home' );?></a>
				</td>
				<td>
					<a title="<?php _e( 'Comments Count', 'bp-my-home' );?>"><span class="bpmh-comments-count"><?php _e( 'Comments Count', 'bp-my-home' );?></span><?php echo $comment->comment_count;?></a>
				</td>
				<td>
					<a title="<?php _e( 'Follow Up', 'bp-my-home' );?>"><span class="bpmh-comments-followup"><?php _e( 'Follow Up', 'bp-my-home' );?></span><?php echo $comment->diff_comment;?></a>
				</td>
				<td>
					<?php if( $comment->comment_approved == 1 ):?>
						<a title="<?php _e( 'Published', 'bp-my-home' );?>"><span class="bpmh-comments-published"><?php _e( 'Published', 'bp-my-home' );?></span></a>
					<?php elseif( $comment->comment_approved == 0 ) :?>
						<a title="<?php _e( 'Awaiting moderation', 'bp-my-home' );?>"><span class="bpmh-comments-awaiting"><?php _e( 'Awaiting moderation', 'bp-my-home' );?></span></a>
					<?php endif;?>
				</td>
			</tr>
			<tr class="bpmh-comment-content">
				<td colspan="4">
					<div id="comment_<?php echo $comment->the_comment_id;?>_details">
						<?php echo nl2br( stripslashes( $comment->comment_content ) );?>
					</div>
				</td>
			<tr>
		<?php endforeach;?>
		
		</table>
		<?php 
		if( !empty( $blog_id ) ) restore_current_blog();
}