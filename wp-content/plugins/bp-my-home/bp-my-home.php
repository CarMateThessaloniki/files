<?php
/*
Plugin Name: BP My Home
Plugin URI: http://imathi.eu/tag/bp-my-home/
Description: BP My Home adds a widgetized area for your BuddyPress members.
Version: 2.1
Requires at least: 3.8
Tested up to: 3.8
License: GNU/GPL 2
Author: imath
Author URI: http://imathi.eu/
Network: true
Text Domain: bp-my-home
Domain Path: /languages/
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


if ( !class_exists( 'Bp_My_Home' ) ) :

/**
 * Main BP My Home Class
 *
 * Inspired by bbpress 2.3
 */
class Bp_My_Home {
	
	private $data;

	private static $instance;

	/**
	 * Main BP My Home Instance
	 *
	 * Inspired by bbpress 2.3
	 *
	 * Avoids the use of a global
	 *
	 * @package BP My Home
	 * @since 2.0
	 *
	 * @uses Bp_My_Home::setup_globals() to set the global needed
	 * @uses Bp_My_Home::includes() to include the required files
	 * @uses Bp_My_Home::setup_actions() to set up the hooks
	 * @return object the instance
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Bp_My_Home;
			self::$instance->setup_globals();
			self::$instance->includes();
			self::$instance->setup_actions();
		}
		return self::$instance;
	}

	
	private function __construct() { /* Do nothing here */ }
	
	public function __clone() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'bp-my-home' ), '2.1' ); }

	public function __wakeup() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'bp-my-home' ), '2.1' ); }

	public function __isset( $key ) { return isset( $this->data[$key] ); }

	public function __get( $key ) { return isset( $this->data[$key] ) ? $this->data[$key] : null; }

	public function __set( $key, $value ) { $this->data[$key] = $value; }

	public function __unset( $key ) { if ( isset( $this->data[$key] ) ) unset( $this->data[$key] ); }

	public function __call( $name = '', $args = array() ) { unset( $name, $args ); return null; }


	/**
	 * Some usefull vars
	 *
	 * @package BP My Home
	 * @since 2.0
	 *
	 * @uses plugin_basename()
	 * @uses plugin_dir_path() to build BP My Home plugin path
	 * @uses plugin_dir_url() to build BP My Home plugin url
	 */
	private function setup_globals() {

		/** Version ***********************************************************/

		$this->version    = '2.1';

		/** Paths *************************************************************/

		// Setup some base path and URL information
		$this->file       = __FILE__;
		$this->basename   = apply_filters( 'bp_my_home_plugin_basename', plugin_basename( $this->file ) );
		$this->plugin_dir = apply_filters( 'bp_my_home_plugin_dir_path', plugin_dir_path( $this->file ) );
		$this->plugin_url = apply_filters( 'bp_my_home_plugin_dir_url',  plugin_dir_url ( $this->file ) );

		// Includes
		$this->includes_dir = apply_filters( 'bp_my_home_includes_dir', trailingslashit( $this->plugin_dir . 'includes'  ) );
		$this->includes_url = apply_filters( 'bp_my_home_includes_url', trailingslashit( $this->plugin_url . 'includes'  ) );
		$this->images_url   = apply_filters( 'bp_my_home_images_url',   trailingslashit( $this->plugin_url . 'images'    ) );
		$this->js_url       = apply_filters( 'bp_my_home_images_url',   trailingslashit( $this->plugin_url . 'js'        ) );
		$this->css_url      = apply_filters( 'bp_my_home_images_url',   trailingslashit( $this->plugin_url . 'css'       ) );

		// Languages
		$this->lang_dir     = apply_filters( 'bp_my_home_lang_dir',     trailingslashit( $this->plugin_dir . 'languages' ) );
		
		// BuddyDrive slugs and names
		$this->bp_my_home_slug      = apply_filters( 'bp_my_home_slug', 'my-home' );
		$this->bp_my_home_name      = apply_filters( 'bp_my_home_name', __( 'Home', 'bp-my-home' ) );
		$this->nav_my_home_name     = apply_filters( 'bp_my_home_nav_name', __( 'My Home', 'bp-my-home' ) );
		$this->nav_my_widgets_slug  = apply_filters( 'bp_my_home_widgets_slug', 'my-widgets' );
		$this->nav_my_widgets_name  = apply_filters( 'bp_my_home_widgets_name', __( 'My Widgets', 'bp-my-home' ) );
		$this->nav_my_settings_slug = apply_filters( 'bp_my_home_settings_slug', 'my-settings' );
		$this->nav_my_settings_name = apply_filters( 'bp_my_home_settings_name', __( 'My Settings', 'bp-my-home' ) );


		/** Misc **************************************************************/

		$this->domain         = 'bp-my-home';
		$this->errors         = new WP_Error(); // Feedback
		
	}
	
	/**
	 * includes the needed files
	 *
	 * @package BP My Home
	 * @since 2.0
	 *
	 * @uses is_admin() for the settings files
	 */
	private function includes() {
		require( $this->includes_dir . 'actions.php'        );
		require( $this->includes_dir . 'functions.php'        );
		require( $this->includes_dir . 'component.php'        );
		
		if( is_admin() )
			require( $this->includes_dir . 'admin/admin.php'        );
	}
	

	/**
	 * It's about hooks!
	 *
	 * @package BP My Home
	 * @since 2.0
	 *
	 * @uses is_admin() to check for admin part.
	 */
	private function setup_actions() {
		
		add_action( 'bp_init',               array( $this, 'load_textdomain' ),         6 );
		add_action( 'bp_core_setup_globals', array( $this, 'maybe_set_as_default' ),   10 );
		add_action( 'bp_template_redirect',  array( $this, 'maybe_redirect_to_home' ), 10 );
		add_action( 'bpmh_enqueue_scripts',  array( $this, 'load_global_css_js'     ), 10 );
		
		if( is_admin() )
			add_action( 'bp_loaded',         array( $this, 'load_admin' ),             10 );

		do_action_ref_array( 'bp_my_home_after_setup_actions', array( &$this ) );
	}
	
	/**
	 * Admin Class
	 *
	 * @package BP My Home
	 * @since 2.0
	 */
	public function load_admin() {
		$this->admin = new BP_My_Home_Admin();
	}
	
	/**
	 * Loads the translation
	 *
	 * @package BP My Home
	 * @since 2.0
	 *
	 * @uses get_locale()
	 * @uses load_textdomain()
	 */
	public function load_textdomain() {
		// try to get locale
		$locale = apply_filters( 'bp_my_home_load_textdomain_get_locale', get_locale() );

		// if we found a locale, try to load .mo file
		if ( !empty( $locale ) ) {
			// default .mo file path
			$mofile_default = sprintf( '%s/languages/%s-%s.mo', $this->plugin_dir, $this->domain, $locale );
			// final filtered file path
			$mofile = apply_filters( 'bp_my_home_textdomain_mofile', $mofile_default );
			// make sure file exists, and load it
			if ( file_exists( $mofile ) ) {
				load_textdomain( $this->domain, $mofile );
			}
		}
	}
	
	/**
	 * Checks for admin settings and by default define BP My Home as default member component
	 *
	 * @package BP My Home
	 * @since 2.0
	 *
	 * @uses bpmh_is_default_component() to check for admin settings
	 * @uses bp_is_my_profile() to check the user is on his profile
	 * @uses bp_my_home_get_slug() to get BP My Home main slug
	 */
	public function maybe_set_as_default() {
		if( bpmh_is_default_component() && bp_is_my_profile() ) {
			define( 'BP_DEFAULT_COMPONENT', bp_my_home_get_slug() );
		}		
	}
	
	/**
	 * Checks for admin settings and user preference to eventually load the BP My home member's page instead of /
	 *
	 * @package BP My Home
	 * @since 2.0
	 *
	 * @uses bpmh_is_root_home_page() to check for admin settings and user's preference
	 * @uses bp_core_redirect() to redirect the user on his profile
	 * @uses bp_my_home_get_user_home_link() to get BP My Home main nav link for user
	 */
	public function maybe_redirect_to_home() {
		if( bpmh_is_root_home_page() ) {
			bp_core_redirect( bp_my_home_get_user_home_link() );
		}
	}
	
	/**
	 * Loads some js and css
	 *
	 * @package BP My Home
	 * @since 2.0
	 *
	 * @uses is_admin() to check we're not in back end
	 * @uses wp_enqueue_style() to enqueue the needed style
	 * @uses bp_my_home_get_css_url() to get the url of the css dir
	 * @uses bp_my_home_get_version() to get the version of the plugin
	 * @uses wp_enqueue_script() to enqueue the needed javascript
	 * @uses bp_my_home_get_js_url() to get the url of the js dir
	 */
	public function load_global_css_js() {
		if( !is_admin() ) {
			wp_enqueue_style( 'css-bp-my-home', bp_my_home_get_css_url() . 'bp-my-home.css', false, bp_my_home_get_version() );
			wp_enqueue_script( 'js-bp-my-home', bp_my_home_get_js_url() . 'bp-my-home.js', array( 'jquery' ), bp_my_home_get_version(), true );
		}
	}
	
}

/**
 * Hooks bp_include to safely load the plugin
 *
 * @uses Bp_My_Home::instance()
 * @return object instance
 */
function bp_my_home() {
	return bp_my_home::instance();
}

add_action( 'bp_include', 'bp_my_home' );


endif;