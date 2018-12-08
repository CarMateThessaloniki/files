<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'BP_My_Home_Admin') ) :
/**
 * Main BP My Home Admin Class
 *
 * inspired by the beautifull bbPress & BuddyPress admin classes
 *
 * @since BP My Home 2.0
 */
class BP_My_Home_Admin {
	
	/** Directory *************************************************************/

	/**
	 * @var string Path to the BP My Home admin directory
	 */
	public $admin_dir = '';

	/** URL ******************************************************************/

	/**
	 * @var string URL to the BP My Home admin directory
	 */
	public $admin_url = '';
	
	/** setting page ******************************************************************/
	
	/**
	 * @var the BP My Home settings page for admin or network admin
	 */
	public $settings_page ='';
	
	/** should extend the widget admin ******************************************************************/
	
	/**
	 * @var the BP My Home check to load or not our admin-widget.js
	 */
	public $widget_replacement = '';

	/**
	 * Constructs the admin area
	 *
	 * @uses BP_My_Home_Admin::setup_globals() to define some globals.
	 * @uses BP_My_Home_Admin::includes() to include the needed file
	 * @uses BP_My_Home_Admin::setup_actions() to reference some key actions
	 */
	public function __construct() {
		$this->setup_globals();
		$this->includes();
		$this->setup_actions();
	}
	
	/**
	 * Sets some globals
	 *
	 * @uses bp_my_home() to get the main instance of the plugin
	 * @uses bp_core_do_network_admin() to check which page to add a submenu to
	 * @uses bp_get_option() to get the admin settings for admin-widget.js
	 */
	private function setup_globals() {
		$bp_my_home = bp_my_home();
		$this->admin_dir     = trailingslashit( $bp_my_home->includes_dir . 'admin'  ); // Admin path
		$this->admin_url     = trailingslashit( $bp_my_home->includes_url . 'admin'  ); // Admin url
		$this->settings_page = bp_core_do_network_admin() ? 'settings.php' : 'options-general.php';
		$this->widget_replacement = bp_get_option( '_bp_my_home_register_admin_widget_js' );
	}

	/**
	 * Includes the needed admin settings file
	 * 
	 */
	public function includes() {
		require( $this->admin_dir . 'settings.php' );
	}

	/**
	 * Adds some key actions to build the admin area of the plugin
	 * 
	 * @uses bp_my_home_is_root_blog() to load our actions into the main blog widget manager
	 */
	public function setup_actions() {

		add_action( 'bp_admin_menu',                     array( $this, 'admin_menus'                ), 10 );
		add_action( 'bpmh_admin_register_settings',      array( $this, 'register_admin_settings'    ), 10 );
		add_filter( 'plugin_action_links',               array( $this, 'modify_plugin_action_links' ), 10, 2 );
		add_filter( 'network_admin_plugin_action_links', array( $this, 'modify_plugin_action_links' ), 10, 2 );
		
		if( bp_my_home_is_root_blog() ) {
			
			if( !empty( $this->widget_replacement ) )
				return;
			
			add_action( 'load-widgets.php',             array( $this, 'manage_widget_scripts'   ), 10 );
			add_action( 'sidebar_admin_setup',          array( $this, 'register_admin_sidebar'  ), 10 );
			add_action( 'sidebar_admin_page',           array( $this, 'list_bpmh_widgets'       ), 10 );
		}
		

	}
	
	/**
	 * Builds the settings submenu and eventually stores version in db
	 * 
	 * @uses add_submenu_page() to add the settings submenu
	 * @uses bp_get_option() to get the stored version
	 * @uses bp_my_home_get_version() to get the current version of the plugin
	 * @uses bp_update_option() to eventually update the db version
	 */
	public function admin_menus() {
		
		$settings = add_submenu_page(  
			$this->settings_page,
			__( 'BP My Home options', 'bp-my-home' ), 
			__( 'BP My Home options', 'bp-my-home' ), 
			'manage_options', 
			'bp-my-home', 
			'bp_my_home_settings' );
			
		if( bp_get_option( 'bp-my-home-version', '' ) != bp_my_home_get_version() ) {
			
			do_action( 'bpmh_do_upgrade' );
			
			bp_update_option( 'bp-my-home-version', bp_my_home_get_version() );
		}
		
	}
	
	/**
	 * Registers admin settings
	 * 
	 * @uses bp_my_home_admin_get_settings_sections() to get the settings section
	 * @uses bp_current_user_can() to check for user's capability
	 * @uses bp_my_home_admin_get_settings_fields_for_section to get the fields
	 * @uses add_settings_section() to add the settings section
	 * @uses add_settings_field() to add the fields
	 * @uses register_setting() to fianlly register the settings
	 */
	public static function register_admin_settings() {

		// Bail if no sections available
		$sections = bp_my_home_admin_get_settings_sections();

		if ( empty( $sections ) )
			return false;

		// Loop through sections
		foreach ( (array) $sections as $section_id => $section ) {

			// Only proceed if current user can see this section
			if ( ! bp_current_user_can( 'manage_options' ) )
				continue;

			// Only add section and fields if section has fields
			$fields = bp_my_home_admin_get_settings_fields_for_section( $section_id );
			if ( empty( $fields ) )
				continue;

			// Add the section
			add_settings_section( $section_id, $section['title'], $section['callback'], $section['page'] );

			// Loop through fields for this section
			foreach ( (array) $fields as $field_id => $field ) {

				// Add the field
				add_settings_field( $field_id, $field['title'], $field['callback'], $section['page'], $section_id, $field['args'] );

				// Register the setting
				register_setting( $section['page'], $field_id, $field['sanitize_callback'] );
			}
		}
	}

	/**
	 * Replaces Built in admin-widget.js by BP My Home one and load some style and script
	 * 
	 * @uses wp_deregister_script() to deregister built in script
	 * @uses wp_register_script() to register BP My Home one
	 * @uses wp_enqueue_script() to enqueue the needed javascript
	 * @uses wp_enqueue_style() to enqueue the needed style 
	 */
	public function manage_widget_scripts() {
		$suffix = SCRIPT_DEBUG ? '' : '.min';
		
		wp_deregister_script( 'admin-widgets' );
		wp_register_script( 'admin-widgets', bp_my_home_get_js_url() ."widget$suffix.js", array( 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable' ), false, 1 );
		
		wp_enqueue_script('bpmh-admin-script', bp_my_home_get_js_url() .'admin.js', array( 'jquery' ) );
		wp_enqueue_style('bpmh-admin-style', bp_my_home_get_css_url() .'admin.css' );
	}
	
	/**
	 * Register a new sidebar in Widget's Admin to house BPMH Widgets
	 * 
	 * @uses register_sidebar() to register the new admin left sidebar
	 */
	public function register_admin_sidebar() {
		
		register_sidebar( array(
			'name' => __( 'BP My Home Widgets', 'bp-my-home' ),
			'id' => 'bp_my_home_widgets',
			'class' => 'inactive-sidebar bpmh-sidebar ui-droppable',
			'description' => __( 'List of available widgets you can drag (only one instance of each) in one of the two BP MY Home sidebars on the right', 'bp-my-home' ),
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '',
			'after_title' => '',
		) );
		
	}
	
	/**
	 * Register a new sidebar in Widget's Admin to house BPMH Widgets
	 * 
	 * @global $wp_registered_widgets
	 * @global $wp_registered_sidebars
	 * @uses esc_html() to sanitize widget's description
	 * @uses wp_list_widgets() to list the filtered widgets (only BPMH ones)
	 */
	public function list_bpmh_widgets() {
		global $wp_registered_widgets, $wp_registered_sidebars;

		$wp_bpmh_restore = $wp_registered_widgets;
		$wp_bpmh_widgets = false;

		foreach( $wp_registered_widgets as $key => $widget ) {
			if( strpos( $widget['id'], 'bpmh_' ) === 0 ) {
				$wp_bpmh_widgets[$key] = $widget;
				unset( $wp_registered_widgets[$key] );
			}
		}
		
		if( empty( $wp_bpmh_widgets ) || !is_array( $wp_bpmh_widgets ) || count( $wp_bpmh_widgets ) < 1 )
			return false;

		$wp_registered_widgets = $wp_bpmh_widgets;
		?>
		<div id="bpmh-widgets-list">
			<div id="bpmh-widgets">
			<?php wp_list_widgets(); ?>
			</div>
		</div>
		<?php
		$wp_registered_widgets = $wp_bpmh_restore;
	}
	
	/**
	 * Adds 2 new links to the plugin's row in plugins list
	 *
	 * @uses plugin_basename()
	 * @uses bp_my_home() to get plugin's main instance
	 * @uses add_query_arg() to add our plugin's page
	 * @uses esc_html__() to sanitize translation
	 * @uses admin_url() to get the widgets manager url
	 * @return array the new links
	 */
	public function modify_plugin_action_links( $links, $file ) {
		// Return normal links if not BP My Home
		if ( plugin_basename( bp_my_home()->file ) != $file )
			return $links;

		// Add a few links to the existing links array
		return array_merge( $links, array(
			'settings' => '<a href="' . add_query_arg( array( 'page' => 'bp-my-home'   ), $this->settings_page ) . '">' . esc_html__( 'Settings', 'bp-my-home' ) . '</a>',
			'widgets'    => '<a href="' . admin_url( 'widgets.php' ) . '">' . esc_html__( 'Manage Widgets', 'bp-my-home' ) . '</a>'
		) );
	}
	
}

endif;
