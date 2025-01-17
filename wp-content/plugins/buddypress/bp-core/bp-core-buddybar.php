<?php

/**
 * Core BuddyPress Navigational Functions.
 *
 * @package BuddyPress
 * @subpackage Core
 * @todo Deprecate BuddyBar functions.
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Add an item to the main BuddyPress navigation array.
 *
 * @global BuddyPress $bp The one true BuddyPress instance.
 *
 * @param array $args {
 *     Array describing the new nav item.
 *     @type string $name Display name for the nav item.
 *     @type string $slug Unique URL slug for the nav item.
 *     @type bool|string $item_css_id Optional. 'id' attribute for the nav
 *           item. Default: the value of $slug.
 *     @type bool $show_for_displayed_user Optional. Whether the nav item
 *           should be visible when viewing a member profile other than your
 *           own. Default: true.
 *     @type bool $site_admin_only Optional. Whether the nav item should be
 *           visible only to site admins (those with the 'bp_moderate' cap).
 *           Default: false.
 *     @type int $position Optional. Numerical index specifying where the item
 *           should appear in the nav array. Default: 99.
 *     @type callable $screen_function The callback function that will run
 *           when the nav item is clicked.
 *     @type bool|string $default_subnav_slug Optional. The slug of the default
 *           subnav item to select when the nav item is clicked.
 * }
 * @return bool|null Returns false on failure.
 */
function bp_core_new_nav_item( $args = '' ) {
	global $bp;

	$defaults = array(
		'name'                    => false, // Display name for the nav item
		'slug'                    => false, // URL slug for the nav item
		'item_css_id'             => false, // The CSS ID to apply to the HTML of the nav item
		'show_for_displayed_user' => true,  // When viewing another user does this nav item show up?
		'site_admin_only'         => false, // Can only site admins see this nav item?
		'position'                => 99,    // Index of where this nav item should be positioned
		'screen_function'         => false, // The name of the function to run when clicked
		'default_subnav_slug'     => false  // The slug of the default subnav item to select when clicked
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	// If we don't have the required info we need, don't create this subnav item
	if ( empty( $name ) || empty( $slug ) )
		return false;

	// If this is for site admins only and the user is not one, don't create the subnav item
	if ( !empty( $site_admin_only ) && !bp_current_user_can( 'bp_moderate' ) )
		return false;

	if ( empty( $item_css_id ) )
		$item_css_id = $slug;

	$bp->bp_nav[$slug] = array(
		'name'                    => $name,
		'slug'                    => $slug,
		'link'                    => trailingslashit( bp_loggedin_user_domain() . $slug ),
		'css_id'                  => $item_css_id,
		'show_for_displayed_user' => $show_for_displayed_user,
		'position'                => $position,
		'screen_function'         => &$screen_function,
		'default_subnav_slug'	  => $default_subnav_slug
	);

 	/**
	 * If this nav item is hidden for the displayed user, and
	 * the logged in user is not the displayed user
	 * looking at their own profile, don't create the nav item.
	 */
	if ( empty( $show_for_displayed_user ) && !bp_user_has_access() )
		return false;

	/**
 	 * If the nav item is visible, we are not viewing a user, and this is a root
	 * component, don't attach the default subnav function so we can display a
	 * directory or something else.
 	 */
	if ( ( -1 != $position ) && bp_is_root_component( $slug ) && !bp_displayed_user_id() )
		return;

	// Look for current component
	if ( bp_is_current_component( $slug ) || bp_is_current_item( $slug ) ) {

		// The requested URL has explicitly included the default subnav
		// (eg: http://example.com/members/membername/activity/just-me/)
		// The canonical version will not contain this subnav slug.
		if ( !empty( $default_subnav_slug ) && bp_is_current_action( $default_subnav_slug ) && !bp_action_variable( 0 ) ) {
			unset( $bp->canonical_stack['action'] );
		} elseif ( ! bp_current_action() ) {

			// Add our screen hook if screen function is callable
			if ( is_callable( $screen_function ) ) {
				add_action( 'bp_screens', $screen_function, 3 );
			}

			if ( !empty( $default_subnav_slug ) ) {
				$bp->current_action = apply_filters( 'bp_default_component_subnav', $default_subnav_slug, $r );
			}
		}
	}

	do_action( 'bp_core_new_nav_item', $r, $args, $defaults );
}

/**
 * Modify the default subnav item that loads when a top level nav item is clicked.
 *
 * @global BuddyPress $bp The one true BuddyPress instance.
 *
 * @param array $args {
 *     @type string $parent_slug The slug of the nav item whose default is
 *           being changed.
 *     @type callable $screen_function The new default callback function that
 *           will run when the nav item is clicked.
 *     @type string $subnav_slug The slug of the new default subnav item.
 * }
 */
function bp_core_new_nav_default( $args = '' ) {
	global $bp;

	$defaults = array(
		'parent_slug'     => false, // Slug of the parent
		'screen_function' => false, // The name of the function to run when clicked
		'subnav_slug'     => false  // The slug of the subnav item to select when clicked
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	if ( $function = $bp->bp_nav[$parent_slug]['screen_function'] ) {
		// Remove our screen hook if screen function is callable
		if ( is_callable( $function ) ) {
			remove_action( 'bp_screens', $function, 3 );
		}
	}

	$bp->bp_nav[$parent_slug]['screen_function'] = &$screen_function;

	if ( bp_is_current_component( $parent_slug ) ) {

		// The only way to tell whether to set the subnav is to peek at the unfiltered_uri
		// Find the component
		$component_uri_key = array_search( $parent_slug, $bp->unfiltered_uri );

		if ( false !== $component_uri_key ) {
			if ( !empty( $bp->unfiltered_uri[$component_uri_key + 1] ) ) {
				$unfiltered_action = $bp->unfiltered_uri[$component_uri_key + 1];
			}
		}

		// No subnav item has been requested in the URL, so set a new nav default
		if ( empty( $unfiltered_action ) ) {
			if ( !bp_is_current_action( $subnav_slug ) ) {
				if ( is_callable( $screen_function ) ) {
					add_action( 'bp_screens', $screen_function, 3 );
				}

				$bp->current_action = $subnav_slug;
				unset( $bp->canonical_stack['action'] );
			}

		// The URL is explicitly requesting the new subnav item, but should be
		// directed to the canonical URL
		} elseif ( $unfiltered_action == $subnav_slug ) {
			unset( $bp->canonical_stack['action'] );

		// In all other cases (including the case where the original subnav item
		// is explicitly called in the URL), the canonical URL will contain the
		// subnav slug
		} else {
			$bp->canonical_stack['action'] = bp_current_action();
		}
	}

	return;
}

/**
 * Sort the navigation menu items.
 *
 * The sorting is split into a separate function because it can only happen
 * after all plugins have had a chance to register their navigation items.
 *
 * @global BuddyPress $bp The one true BuddyPress instance
 *
 * @return bool|null Returns false on failure.
 */
function bp_core_sort_nav_items() {
	global $bp;

	if ( empty( $bp->bp_nav ) || !is_array( $bp->bp_nav ) )
		return false;

	$temp = array();

	foreach ( (array) $bp->bp_nav as $slug => $nav_item ) {
		if ( empty( $temp[$nav_item['position']]) ) {
			$temp[$nav_item['position']] = $nav_item;
		} else {
			// increase numbers here to fit new items in.
			do {
				$nav_item['position']++;
			} while ( !empty( $temp[$nav_item['position']] ) );

			$temp[$nav_item['position']] = $nav_item;
		}
	}

	ksort( $temp );
	$bp->bp_nav = &$temp;
}
add_action( 'wp_head',    'bp_core_sort_nav_items' );
add_action( 'admin_head', 'bp_core_sort_nav_items' );

/**
 * Add a subnav item to the BuddyPress navigation.
 *
 * @global BuddyPress $bp The one true BuddyPress instance.
 *
 * @param array $args {
 *     Array describing the new subnav item.
 *     @type string $name Display name for the subnav item.
 *     @type string $slug Unique URL slug for the subnav item.
 *     @type string $parent_slug Slug of the top-level nav item under which the
 *           new subnav item should be added.
 *     @type string $parent_url URL of the parent nav item.
 *     @type bool|string $item_css_id Optional. 'id' attribute for the nav
 *           item. Default: the value of $slug.
 *     @type bool $user_has_access Optional. True if the logged-in user has
 *           access to the subnav item, otherwise false. Can be set dynamically
 *           when registering the subnav; eg, use bp_is_my_profile() to restrict
 *           access to profile owners only. Default: true.
 *     @type bool $site_admin_only Optional. Whether the nav item should be
 *           visible only to site admins (those with the 'bp_moderate' cap).
 *           Default: false.
 *     @type int $position Optional. Numerical index specifying where the item
 *           should appear in the subnav array. Default: 90.
 *     @type callable $screen_function The callback function that will run
 *           when the nav item is clicked.
 *     @type string $link Optional. The URL that the subnav item should point
 *           to. Defaults to a value generated from the $parent_url + $slug.
 * }
 * @return bool|null Returns false on failure.
 */
function bp_core_new_subnav_item( $args = '' ) {
	global $bp;

	$defaults = array(
		'name'            => false, // Display name for the nav item
		'slug'            => false, // URL slug for the nav item
		'parent_slug'     => false, // URL slug of the parent nav item
		'parent_url'      => false, // URL of the parent item
		'item_css_id'     => false, // The CSS ID to apply to the HTML of the nav item
		'user_has_access' => true,  // Can the logged in user see this nav item?
		'site_admin_only' => false, // Can only site admins see this nav item?
		'position'        => 90,    // Index of where this nav item should be positioned
		'screen_function' => false, // The name of the function to run when clicked
		'link'            => ''     // The link for the subnav item; optional, not usually required.
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	// If we don't have the required info we need, don't create this subnav item
	if ( empty( $name ) || empty( $slug ) || empty( $parent_slug ) || empty( $parent_url ) || empty( $screen_function ) )
		return false;

	// Link was not forced, so create one
	if ( empty( $link ) ) {
		$link = $parent_url . $slug;

		// If this sub item is the default for its parent, skip the slug
		if ( ! empty( $bp->bp_nav[$parent_slug]['default_subnav_slug'] ) && $slug == $bp->bp_nav[$parent_slug]['default_subnav_slug'] ) {
			$link = $parent_url;
		}
	}

	// If this is for site admins only and the user is not one, don't create the subnav item
	if ( !empty( $site_admin_only ) && !bp_current_user_can( 'bp_moderate' ) )
		return false;

	if ( empty( $item_css_id ) )
		$item_css_id = $slug;

	$bp->bp_options_nav[$parent_slug][$slug] = array(
		'name'            => $name,
		'link'            => trailingslashit( $link ),
		'slug'            => $slug,
		'css_id'          => $item_css_id,
		'position'        => $position,
		'user_has_access' => $user_has_access,
		'screen_function' => &$screen_function
	);

	/**
	 * The last step is to hook the screen function for the added subnav item. But this only
	 * needs to be done if this subnav item is the current view, and the user has access to the
	 * subnav item. We figure out whether we're currently viewing this subnav by checking the
	 * following two conditions:
	 *   (1) Either:
	 *	     (a) the parent slug matches the current_component, or
	 *	     (b) the parent slug matches the current_item
	 *   (2) And either:
	 *	     (a) the current_action matches $slug, or
	 *       (b) there is no current_action (ie, this is the default subnav for the parent nav)
	 *	     and this subnav item is the default for the parent item (which we check by
	 *	     comparing this subnav item's screen function with the screen function of the
	 *	     parent nav item in $bp->bp_nav). This condition only arises when viewing a
	 *	     user, since groups should always have an action set.
	 */

	// If we *don't* meet condition (1), return
	if ( ! bp_is_current_component( $parent_slug ) && ! bp_is_current_item( $parent_slug ) )
		return;

	// If we *do* meet condition (2), then the added subnav item is currently being requested
	if ( ( bp_current_action() && bp_is_current_action( $slug ) ) || ( bp_is_user() && ! bp_current_action() && ( $screen_function == $bp->bp_nav[$parent_slug]['screen_function'] ) ) ) {

		// Before hooking the screen function, check user access
		if ( !empty( $user_has_access ) ) {
			// Add our screen hook if screen function is callable
			if ( is_callable( $screen_function ) ) {
				add_action( 'bp_screens', $screen_function, 3 );
			}
		} else {

			// When the content is off-limits, we handle the situation
			// differently depending on whether the current user is logged in
			if ( is_user_logged_in() ) {
				if ( !bp_is_my_profile() && empty( $bp->bp_nav[$bp->default_component]['show_for_displayed_user'] ) ) {

					// This covers the edge case where the default component is
					// a non-public tab, like 'messages'
					if ( bp_is_active( 'activity' ) && isset( $bp->pages->activity ) ) {
						$redirect_to = trailingslashit( bp_displayed_user_domain() . bp_get_activity_slug() );
					} else {
						$redirect_to = trailingslashit( bp_displayed_user_domain() . ( 'xprofile' == $bp->profile->id ? 'profile' : $bp->profile->id ) );
					}

					$message     = '';
				} else {
					$message     = __( 'You do not have access to this page.', 'buddypress' );
					$redirect_to = bp_displayed_user_domain();
				}

				// Off-limits to this user. Throw an error and redirect to the displayed user's domain
				bp_core_no_access( array(
					'message'  => $message,
					'root'     => $redirect_to,
					'redirect' => false
				) );

			// Not logged in. Allow the user to log in, and attempt to redirect
			} else {
				bp_core_no_access();
			}
		}
	}
}

/**
 * Sort all subnavigation arrays.
 *
 * @global BuddyPress $bp The one true BuddyPress instance
 *
 * @return bool|null Returns false on failure.
 */
function bp_core_sort_subnav_items() {
	global $bp;

	if ( empty( $bp->bp_options_nav ) || !is_array( $bp->bp_options_nav ) )
		return false;

	foreach ( (array) $bp->bp_options_nav as $parent_slug => $subnav_items ) {
		if ( !is_array( $subnav_items ) )
			continue;

		foreach ( (array) $subnav_items as $subnav_item ) {
			if ( empty( $temp[$subnav_item['position']]) )
				$temp[$subnav_item['position']] = $subnav_item;
			else {
				// increase numbers here to fit new items in.
				do {
					$subnav_item['position']++;
				} while ( !empty( $temp[$subnav_item['position']] ) );

				$temp[$subnav_item['position']] = $subnav_item;
			}
		}
		ksort( $temp );
		$bp->bp_options_nav[$parent_slug] = &$temp;
		unset( $temp );
	}
}
add_action( 'wp_head',    'bp_core_sort_subnav_items' );
add_action( 'admin_head', 'bp_core_sort_subnav_items' );

/**
 * Check whether a given nav item has subnav items.
 *
 * @since BuddyPress (1.5.0)
 *
 * @param string $nav_item The slug of the top-level nav item whose subnav
 *        items you're checking. Default: the current component slug.
 * @return bool $has_subnav True if the nav item is found and has subnav
 *        items; false otherwise.
 */
function bp_nav_item_has_subnav( $nav_item = '' ) {
	global $bp;

	if ( !$nav_item )
		$nav_item = bp_current_component();

	$has_subnav = isset( $bp->bp_options_nav[$nav_item] ) && count( $bp->bp_options_nav[$nav_item] ) > 0;

	return apply_filters( 'bp_nav_item_has_subnav', $has_subnav, $nav_item );
}

/**
 * Remove a nav item from the navigation array.
 *
 * @param int $parent_id The slug of the parent navigation item.
 * @param bool Returns false on failure, ie if the nav item can't be found.
 */
function bp_core_remove_nav_item( $parent_id ) {
	global $bp;

	// Unset subnav items for this nav item
	if ( isset( $bp->bp_options_nav[$parent_id] ) && is_array( $bp->bp_options_nav[$parent_id] ) ) {
		foreach( (array) $bp->bp_options_nav[$parent_id] as $subnav_item ) {
			bp_core_remove_subnav_item( $parent_id, $subnav_item['slug'] );
		}
	}

	if ( empty( $bp->bp_nav[ $parent_id ] ) )
		return false;

	if ( $function = $bp->bp_nav[$parent_id]['screen_function'] ) {
		// Remove our screen hook if screen function is callable
		if ( is_callable( $function ) ) {
			remove_action( 'bp_screens', $function, 3 );
		}
	}

	unset( $bp->bp_nav[$parent_id] );
}

/**
 * Remove a subnav item from the navigation array.
 *
 * @param string $parent_id The slug of the parent navigation item.
 * @param string $slug The slug of the subnav item to be removed.
 */
function bp_core_remove_subnav_item( $parent_id, $slug ) {
	global $bp;

	$screen_function = isset( $bp->bp_options_nav[$parent_id][$slug]['screen_function'] ) ? $bp->bp_options_nav[$parent_id][$slug]['screen_function'] : false;

	if ( ! empty( $screen_function ) ) {
		// Remove our screen hook if screen function is callable
		if ( is_callable( $screen_function ) ) {
			remove_action( 'bp_screens', $screen_function, 3 );
		}
	}

	unset( $bp->bp_options_nav[$parent_id][$slug] );

	if ( isset( $bp->bp_options_nav[$parent_id] ) && !count( $bp->bp_options_nav[$parent_id] ) )
		unset($bp->bp_options_nav[$parent_id]);
}

/**
 * Clear all subnav items from a specific nav item.
 *
 * @global BuddyPress $bp The one true BuddyPress instance.
 *
 * @param string $parent_slug The slug of the parent navigation item.
 */
function bp_core_reset_subnav_items( $parent_slug ) {
	global $bp;

	unset( $bp->bp_options_nav[$parent_slug] );
}

/** BuddyBar Template functions ***********************************************/

/**
 * Wrapper function for rendering the BuddyBar.
 *
 * @return bool|null Returns false if the BuddyBar is disabled.
 */
function bp_core_admin_bar() {
	global $bp;

	if ( defined( 'BP_DISABLE_ADMIN_BAR' ) && BP_DISABLE_ADMIN_BAR )
		return false;

	if ( (int) bp_get_option( 'hide-loggedout-adminbar' ) && !is_user_logged_in() )
		return false;

	$bp->doing_admin_bar = true;

	echo '<div id="wp-admin-bar"><div class="padder">';

	// **** Do bp-adminbar-logo Actions ********
	do_action( 'bp_adminbar_logo' );

	echo '<ul class="main-nav">';

	// **** Do bp-adminbar-menus Actions ********
	do_action( 'bp_adminbar_menus' );

	echo '</ul>';
	echo "</div></div><!-- #wp-admin-bar -->\n\n";

	$bp->doing_admin_bar = false;
}

/**
 * Output the BuddyBar logo.
 */
function bp_adminbar_logo() {
	echo '<a href="' . bp_get_root_domain() . '" id="admin-bar-logo">' . get_blog_option( bp_get_root_blog_id(), 'blogname' ) . '</a>';
}

/**
 * Output the "Log In" and "Sign Up" names to the BuddyBar.
 *
 * Visible only to visitors who are not logged in.
 *
 * @return bool|null Returns false if the current user is logged in.
 */
function bp_adminbar_login_menu() {

	if ( is_user_logged_in() )
		return false;

	echo '<li class="bp-login no-arrow"><a href="' . wp_login_url() . '">' . __( 'Log In', 'buddypress' ) . '</a></li>';

	// Show "Sign Up" link if user registrations are allowed
	if ( bp_get_signup_allowed() )
		echo '<li class="bp-signup no-arrow"><a href="' . bp_get_signup_page() . '">' . __( 'Sign Up', 'buddypress' ) . '</a></li>';
}

/**
 * Output the My Account BuddyBar menu.
 *
 * @return bool|null Returns false on failure.
 */
function bp_adminbar_account_menu() {
	global $bp;

	if ( !$bp->bp_nav || !is_user_logged_in() )
		return false;

	echo '<li id="bp-adminbar-account-menu"><a href="' . bp_loggedin_user_domain() . '">';
	echo __( 'My Account', 'buddypress' ) . '</a>';
	echo '<ul>';

	// Loop through each navigation item
	$counter = 0;
	foreach( (array) $bp->bp_nav as $nav_item ) {
		$alt = ( 0 == $counter % 2 ) ? ' class="alt"' : '';

		if ( -1 == $nav_item['position'] )
			continue;

		echo '<li' . $alt . '>';
		echo '<a id="bp-admin-' . $nav_item['css_id'] . '" href="' . $nav_item['link'] . '">' . $nav_item['name'] . '</a>';

		if ( isset( $bp->bp_options_nav[$nav_item['slug']] ) && is_array( $bp->bp_options_nav[$nav_item['slug']] ) ) {
			echo '<ul>';
			$sub_counter = 0;

			foreach( (array) $bp->bp_options_nav[$nav_item['slug']] as $subnav_item ) {
				$link = $subnav_item['link'];
				$name = $subnav_item['name'];

				if ( bp_displayed_user_domain() )
					$link = str_replace( bp_displayed_user_domain(), bp_loggedin_user_domain(), $subnav_item['link'] );

				if ( isset( $bp->displayed_user->userdata->user_login ) )
					$name = str_replace( $bp->displayed_user->userdata->user_login, $bp->loggedin_user->userdata->user_login, $subnav_item['name'] );

				$alt = ( 0 == $sub_counter % 2 ) ? ' class="alt"' : '';
				echo '<li' . $alt . '><a id="bp-admin-' . $subnav_item['css_id'] . '" href="' . $link . '">' . $name . '</a></li>';
				$sub_counter++;
			}
			echo '</ul>';
		}

		echo '</li>';

		$counter++;
	}

	$alt = ( 0 == $counter % 2 ) ? ' class="alt"' : '';

	echo '<li' . $alt . '><a id="bp-admin-logout" class="logout" href="' . wp_logout_url( home_url() ) . '">' . __( 'Log Out', 'buddypress' ) . '</a></li>';
	echo '</ul>';
	echo '</li>';
}

function bp_adminbar_thisblog_menu() {
	if ( current_user_can( 'edit_posts' ) ) {
		echo '<li id="bp-adminbar-thisblog-menu"><a href="' . admin_url() . '">';
		_e( 'Dashboard', 'buddypress' );
		echo '</a>';
		echo '<ul>';

		echo '<li class="alt"><a href="' . admin_url() . 'post-new.php">' . __( 'New Post', 'buddypress' ) . '</a></li>';
		echo '<li><a href="' . admin_url() . 'edit.php">' . __( 'Manage Posts', 'buddypress' ) . '</a></li>';
		echo '<li class="alt"><a href="' . admin_url() . 'edit-comments.php">' . __( 'Manage Comments', 'buddypress' ) . '</a></li>';

		do_action( 'bp_adminbar_thisblog_items' );

		echo '</ul>';
		echo '</li>';
	}
}

/**
 * Output the Random BuddyBar menu.
 *
 * Not visible for logged-in users.
 */
function bp_adminbar_random_menu() {
?>

	<li class="align-right" id="bp-adminbar-visitrandom-menu">
		<a href="#"><?php _e( 'Visit', 'buddypress' ) ?></a>
		<ul class="random-list">
			<li><a href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_members_root_slug() ) . '?random-member' ?>" rel="nofollow"><?php _e( 'Random Member', 'buddypress' ) ?></a></li>

			<?php if ( bp_is_active( 'groups' ) ) : ?>

				<li class="alt"><a href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() ) . '?random-group' ?>"  rel="nofollow"><?php _e( 'Random Group', 'buddypress' ) ?></a></li>

			<?php endif; ?>

			<?php if ( is_multisite() && bp_is_active( 'blogs' ) ) : ?>

				<li><a href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_blogs_root_slug() ) . '?random-blog' ?>"  rel="nofollow"><?php _e( 'Random Site', 'buddypress' ) ?></a></li>

			<?php endif; ?>

			<?php do_action( 'bp_adminbar_random_menu' ) ?>

		</ul>
	</li>

	<?php
}

/**
 * Retrieve the Toolbar display preference of a user based on context.
 *
 * This is a direct copy of WP's private _get_admin_bar_pref()
 *
 * @since BuddyPress (1.5.0)
 *
 * @uses get_user_option()
 *
 * @param string $context Context of this preference check. 'admin' or 'front'.
 * @param int $user Optional. ID of the user to check. Default: 0 (which falls
 *        back to the logged-in user's ID).
 * @return bool True if the toolbar should be showing for this user.
 */
function bp_get_admin_bar_pref( $context, $user = 0 ) {
	$pref = get_user_option( "show_admin_bar_{$context}", $user );
	if ( false === $pref )
		return true;

	return 'true' === $pref;
}

/**
 * Enqueue the BuddyBar CSS.
 */
function bp_core_load_buddybar_css() {
	global $wp_styles;

	if ( bp_use_wp_admin_bar() || ( (int) bp_get_option( 'hide-loggedout-adminbar' ) && !is_user_logged_in() ) || ( defined( 'BP_DISABLE_ADMIN_BAR' ) && BP_DISABLE_ADMIN_BAR ) )
		return;

	$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	if ( file_exists( get_stylesheet_directory() . '/_inc/css/adminbar.css' ) ) { // Backwards compatibility
		$stylesheet = get_stylesheet_directory_uri() . '/_inc/css/adminbar.css';
	} else {
		$stylesheet = buddypress()->plugin_url . "bp-core/css/buddybar{$min}.css";
	}

	wp_enqueue_style( 'bp-admin-bar', apply_filters( 'bp_core_buddybar_rtl_css', $stylesheet ), array(), bp_get_version() );
	$wp_styles->add_data( 'bp-admin-bar', 'rtl', true );
	if ( $min )
		$wp_styles->add_data( 'bp-admin-bar', 'suffix', $min );
}
add_action( 'bp_init', 'bp_core_load_buddybar_css' );
