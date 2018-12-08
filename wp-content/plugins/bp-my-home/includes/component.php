<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Main BP My Home Component Class
 *
 * Inspired by BuddyPress skeleton component
 */
class Bp_My_Home_Component extends BP_Component {

	/**
	 * Constructor method
	 *
	 * @package BP My Home
	 * @since 1.2
	 *
	 * @uses bp_my_home_get_slug() to get the slug of the plugin
	 * @uses bp_my_home_get_name() to get the name of the plugin
	 * @uses bp_my_home_get_includes_dir() to get plugin's include dir
	 * @uses buddypress() to get its main instance
	 */
	
	function __construct() {

		parent::start(
			bp_my_home_get_slug(),
			bp_my_home_get_name(),
			bp_my_home_get_includes_dir()
		);

	 	$this->includes();
		
		buddypress()->active_components[$this->id] = '1';
		
	}

	/**
	 * BP My Home needed files
	 *
	 * @package BP My Home
	 * @since 1.2
	 *
	 * @uses bp_my_home_is_root_blog() to check for root blog to only load the widgets there
	 */
	function includes() {

		// Files to include
		$includes = array(
			'filters.php',
			'template.php',
			'ajax.php',
			'sub-actions.php'
		);
		
		if( bp_my_home_is_root_blog() )
			$includes[] = 'widgets.php';

		parent::includes( $includes );

	}

	/**
	 * Set up BP My Home navigation.
	 * 
	 * @package BP My Home
	 * @since 1.2
	 *
	 * @uses bp_my_home_get_home_name() to get plugin's name
	 * @uses bp_my_home_get_widgets_slug() to get plugin's widgets subnav slug
	 * @uses bp_loggedin_user_domain() to get current user profile link
	 * @uses bp_my_home_get_widgets_name() to get plugin's widgets subnav name
	 * @uses bp_my_home_get_settings_name() to get plugin's my settings subnav name
	 * @uses bp_my_home_get_settings_slug() to get plugin's my settings subnav slug
	 */
	function setup_nav() {
		
		$main_nav = array(
			'name'                => bp_my_home_get_home_name(),
			'slug'                => $this->slug,
			'position'            => 0,
			'show_for_displayed_user' => false,
			'screen_function'     => 'bp_my_home_home',
			'default_subnav_slug' => bp_my_home_get_widgets_slug(),
			'item_css_id'         => $this->id
		);

		$my_home_link = trailingslashit( bp_loggedin_user_domain() . $this->slug );

		// Add the My Widgets and My Home nav item
		$sub_nav[] = array(
			'name'            => bp_my_home_get_widgets_name(),
			'slug'            => bp_my_home_get_widgets_slug(),
			'parent_url'      => $my_home_link,
			'parent_slug'     => $this->slug,
			'screen_function' => 'bp_my_home_home',
			'position'        => 10,
			'item_css_id'     => 'my-home-my-widgets'
		);
		$sub_nav[] = array(
			'name'            => bp_my_home_get_settings_name(),
			'slug'            => bp_my_home_get_settings_slug(),
			'parent_url'      => $my_home_link,
			'parent_slug'     => $this->slug,
			'screen_function' => 'bp_my_home_user_settings',
			'position'        => 20,
			'item_css_id'     => 'my-home-my-settings'
		);
		
		parent::setup_nav( $main_nav, $sub_nav );
		
	}
	
	/**
	 * Builds the user's navigation in WP Admin Bar
	 *
	 * @package BP My Home
	 * @since 2.0
	 *
	 * @uses is_user_logged_in() to check if the user is logged in
	 * @uses bp_loggedin_user_domain() to get current user's profile link
	 * @uses bp_my_home_get_widgets_slug() to get plugin's widgets subnav slug
	 * @uses bp_my_home_get_widgets_name() to get plugin's widgets subnav name
	 * @uses bp_my_home_get_settings_name() to get plugin's my settings subnav name
	 * @uses bp_my_home_get_settings_slug() to get plugin's my settings subnav slug
	 */
	function setup_admin_bar() {

		// Prevent debug notices
		$wp_admin_nav = array();

		// Menus for logged in user
		if ( is_user_logged_in() ) {

			// Setup the logged in user variables
			$my_home_link = trailingslashit( bp_loggedin_user_domain() . $this->slug );

			// Add main BuddyDrive menu
			$wp_admin_nav[] = array(
				'parent' => 'my-account-buddypress',
				'id'     => 'my-account-' . $this->slug,
				'title'  => $this->name,
				'href'   => trailingslashit( $my_home_link )
			);
			
			// Add BuddyDrive submenu
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . $this->slug,
				'id'     => 'my-account-' . $this->slug . '-' . bp_my_home_get_widgets_slug(),
				'title'  => bp_my_home_get_widgets_name(),
				'href'   => trailingslashit( $my_home_link )
			);
			
			
			// Add shared by friends BuddyDrive submenu
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . $this->slug,
				'id'     => 'my-account-' . $this->slug . '-' . bp_my_home_get_settings_slug(),
				'title'  => bp_my_home_get_settings_name(),
				'href'   => trailingslashit( $my_home_link . bp_my_home_get_settings_slug() )
			);

		}

		parent::setup_admin_bar( $wp_admin_nav );
	}

}

/**
 * Finally Loads the component into the main BuddyPress instance
 *
 * @uses buddypress()
 */
function bp_my_home_load_component() {
	buddypress()->myhome = new Bp_My_Home_Component;
}
add_action( 'bp_loaded', 'bp_my_home_load_component' );
?>