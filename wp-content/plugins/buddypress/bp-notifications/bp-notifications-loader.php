<?php

/**
 * BuddyPress Member Notifications Loader.
 *
 * Initializes the Notifications component.
 *
 * @package BuddyPress
 * @subpackage NotificationsLoader
 * @since BuddyPress (1.9.0)
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class BP_Notifications_Component extends BP_Component {

	/**
	 * Start the notifications component creation process.
	 *
	 * @since BuddyPress (1.9.0)
	 */
	public function __construct() {
		parent::start(
			'notifications',
			__( 'Notifications', 'buddypress' ),
			buddypress()->plugin_dir,
			array(
				'adminbar_myaccount_order' => 30
			)
		);
	}

	/**
	 * Include notifications component files.
	 *
	 * @since BuddyPress (1.9.0)
	 *
	 * @see BP_Component::includes() for a description of arguments.
	 *
	 * @param array $includes See BP_Component::includes() for a description.
	 */
	public function includes( $includes = array() ) {
		$includes = array(
			'actions',
			'classes',
			'screens',
			'adminbar',
			'buddybar',
			'template',
			'functions',
			'cache',
		);

		parent::includes( $includes );
	}

	/**
	 * Setup globals
	 *
	 * The BP_FRIENDS_SLUG constant is deprecated, and only used here for
	 * backwards compatibility.
	 *
	 * @since BuddyPress (1.9.0)
	 *
	 * @see BP_Component::setup_globals() for a description of arguments.
	 *
	 * @param array $args See BP_Component::setup_globals() for a description.
	 */
	public function setup_globals( $args = array() ) {
		// Define a slug, if necessary
		if ( !defined( 'BP_NOTIFICATIONS_SLUG' ) ) {
			define( 'BP_NOTIFICATIONS_SLUG', $this->id );
		}

		// Global tables for the notifications component
		$global_tables = array(
			'table_name' => bp_core_get_table_prefix() . 'bp_notifications'
		);

		// All globals for the notifications component.
		// Note that global_tables is included in this array.
		$args = array(
			'slug'          => BP_NOTIFICATIONS_SLUG,
			'has_directory' => false,
			'search_string' => __( 'Search Notifications...', 'buddypress' ),
			'global_tables' => $global_tables,
		);

		parent::setup_globals( $args );
	}

	/**
	 * Set up component navigation.
	 *
	 * @since BuddyPress (1.9.0)
	 *
	 * @see BP_Component::setup_nav() for a description of arguments.
	 *
	 * @param array $main_nav Optional. See BP_Component::setup_nav() for
	 *        description.
	 * @param array $sub_nav Optional. See BP_Component::setup_nav() for
	 *        description.
	 */
	public function setup_nav( $main_nav = array(), $sub_nav = array() ) {

		// Only grab count if we're on a user page and current user has access
		if ( bp_is_user() && bp_user_has_access() ) {
			$count    = bp_notifications_get_unread_notification_count( bp_displayed_user_id() );
			$class    = ( 0 === $count ) ? 'no-count' : 'count';
			$nav_name = sprintf( __( 'Notifications <span class="%s">%s</span>', 'buddypress' ), esc_attr( $class ), number_format_i18n( $count ) );
		} else {
			$nav_name = __( 'Notifications', 'buddypress' );
		}

		// Add 'Notifications' to the main navigation
		$main_nav = array(
			'name'                    => $nav_name,
			'slug'                    => $this->slug,
			'position'                => 30,
			'show_for_displayed_user' => bp_core_can_edit_settings(),
			'screen_function'         => 'bp_notifications_screen_unread',
			'default_subnav_slug'     => 'unread',
			'item_css_id'             => $this->id,
		);

		// Determine user to use
		if ( bp_displayed_user_domain() ) {
			$user_domain = bp_displayed_user_domain();
		} elseif ( bp_loggedin_user_domain() ) {
			$user_domain = bp_loggedin_user_domain();
		} else {
			return;
		}

		$notifications_link = trailingslashit( $user_domain . bp_get_notifications_slug() );

		// Add the subnav items to the notifications nav item
		$sub_nav[] = array(
			'name'            => __( 'Unread', 'buddypress' ),
			'slug'            => 'unread',
			'parent_url'      => $notifications_link,
			'parent_slug'     => bp_get_notifications_slug(),
			'screen_function' => 'bp_notifications_screen_unread',
			'position'        => 10,
			'item_css_id'     => 'notifications-my-notifications',
			'user_has_access' => bp_core_can_edit_settings(),
		);

		$sub_nav[] = array(
			'name'            => __( 'Read',   'buddypress' ),
			'slug'            => 'read',
			'parent_url'      => $notifications_link,
			'parent_slug'     => bp_get_notifications_slug(),
			'screen_function' => 'bp_notifications_screen_read',
			'position'        => 20,
			'user_has_access' => bp_core_can_edit_settings(),
		);

		parent::setup_nav( $main_nav, $sub_nav );
	}

	/**
	 * Set up the component entries in the WordPress Admin Bar.
	 *
	 * @since BuddyPress (1.9.0)
	 *
	 * @see BP_Component::setup_nav() for a description of the $wp_admin_nav
	 *      parameter array.
	 *
	 * @param array $wp_admin_nav See BP_Component::setup_admin_bar() for a
	 *        description.
	 */
	public function setup_admin_bar( $wp_admin_nav = array() ) {

		// Menus for logged in user
		if ( is_user_logged_in() ) {

			// Setup the logged in user variables
			$notifications_link = trailingslashit( bp_loggedin_user_domain() . $this->slug );

			// Pending notification requests
			$count = bp_notifications_get_unread_notification_count( bp_loggedin_user_id() );
			if ( ! empty( $count ) ) {
				$title  = sprintf( __( 'Notifications <span class="count">%s</span>', 'buddypress' ), number_format_i18n( $count ) );
				$unread = sprintf( __( 'Unread <span class="count">%s</span>',        'buddypress' ), number_format_i18n( $count ) );
			} else {
				$title  = __( 'Notifications', 'buddypress' );
				$unread = __( 'Unread',        'buddypress' );
			}

			// Add the "My Account" sub menus
			$wp_admin_nav[] = array(
				'parent' => buddypress()->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => $title,
				'href'   => trailingslashit( $notifications_link ),
			);

			// Unread
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . $this->id,
				'id'     => 'my-account-' . $this->id . '-unread',
				'title'  => $unread,
				'href'   => trailingslashit( $notifications_link ),
			);

			// Read
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . $this->id,
				'id'     => 'my-account-' . $this->id . '-read',
				'title'  => __( 'Read', 'buddypress' ),
				'href'   => trailingslashit( $notifications_link . 'read' ),
			);
		}

		parent::setup_admin_bar( $wp_admin_nav );
	}

	/**
	 * Set up the title for pages and <title>.
	 *
	 * @since BuddyPress (1.9.0)
	 */
	public function setup_title() {
		$bp = buddypress();

		// Adjust title
		if ( bp_is_notifications_component() ) {
			if ( bp_is_my_profile() ) {
				$bp->bp_options_title = __( 'Notifications', 'buddypress' );
			} else {
				$bp->bp_options_avatar = bp_core_fetch_avatar( array(
					'item_id' => bp_displayed_user_id(),
					'type'    => 'thumb',
					'alt'     => sprintf( __( 'Profile picture of %s', 'buddypress' ), bp_get_displayed_user_fullname() )
				) );
				$bp->bp_options_title = bp_get_displayed_user_fullname();
			}
		}

		parent::setup_title();
	}
}

/**
 * Bootstrap the Notifications component.
 *
 * @since BuddyPress (1.9.0)
 */
function bp_setup_notifications() {
	buddypress()->notifications = new BP_Notifications_Component();
}
add_action( 'bp_setup_components', 'bp_setup_notifications', 6 );
