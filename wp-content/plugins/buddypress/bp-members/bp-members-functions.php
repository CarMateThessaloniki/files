<?php

/**
 * BuddyPress Member Functions
 *
 * Functions specific to the members component.
 *
 * @package BuddyPress
 * @subpackage MembersFunctions
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Checks $bp pages global and looks for directory page
 *
 * @since BuddyPress (1.5)
 *
 * @global BuddyPress $bp The one true BuddyPress instance
 * @return bool True if set, False if empty
 */
function bp_members_has_directory() {
	global $bp;

	return (bool) !empty( $bp->pages->members->id );
}

/**
 * Define the slugs used for BuddyPress pages, based on the slugs of the WP pages used.
 * These can be overridden manually by defining these slugs in wp-config.php.
 *
 * The fallback values are only used during initial BP page creation, when no slugs have been
 * explicitly defined.
 *
 * @package BuddyPress Core Core
 * @global BuddyPress $bp The one true BuddyPress instance
 */
function bp_core_define_slugs() {
	global $bp;

	// No custom members slug
	if ( !defined( 'BP_MEMBERS_SLUG' ) ) {
		if ( !empty( $bp->pages->members ) ) {
			define( 'BP_MEMBERS_SLUG', $bp->pages->members->slug );
		} else {
			define( 'BP_MEMBERS_SLUG', 'members' );
		}
	}

	// No custom registration slug
	if ( !defined( 'BP_REGISTER_SLUG' ) ) {
		if ( !empty( $bp->pages->register ) ) {
			define( 'BP_REGISTER_SLUG', $bp->pages->register->slug );
		} else {
			define( 'BP_REGISTER_SLUG', 'register' );
		}
	}

	// No custom activation slug
	if ( !defined( 'BP_ACTIVATION_SLUG' ) ) {
		if ( !empty( $bp->pages->activate ) ) {
			define( 'BP_ACTIVATION_SLUG', $bp->pages->activate->slug );
		} else {
			define( 'BP_ACTIVATION_SLUG', 'activate' );
		}
	}
}
add_action( 'bp_setup_globals', 'bp_core_define_slugs', 11 );

/**
 * Return an array of users IDs based on the parameters passed.
 *
 * Since BuddyPress 1.7, bp_core_get_users() uses BP_User_Query. If you
 * need backward compatibility with BP_Core_User::get_users(), filter the
 * bp_use_legacy_user_query value, returning true.
 *
 * @package BuddyPress Core
 */
function bp_core_get_users( $args = '' ) {

	// Parse the user query arguments
	$params = wp_parse_args( $args, array(
		'type'            => 'active',     // active, newest, alphabetical, random or popular
		'user_id'         => false,        // Pass a user_id to limit to only friend connections for this user
		'exclude'         => false,        // Users to exclude from results
		'search_terms'    => false,        // Limit to users that match these search terms
		'meta_key'        => false,        // Limit to users who have this piece of usermeta
		'meta_value'      => false,        // With meta_key, limit to users where usermeta matches this value
		'include'         => false,        // Pass comma separated list of user_ids to limit to only these users
		'per_page'        => 20,           // The number of results to return per page
		'page'            => 1,            // The page to return if limiting per page
		'populate_extras' => true,         // Fetch the last active, where the user is a friend, total friend count, latest update
		'count_total'     => 'count_query' // What kind of total user count to do, if any. 'count_query', 'sql_calc_found_rows', or false
	) );

	// For legacy users. Use of BP_Core_User::get_users() is deprecated.
	if ( apply_filters( 'bp_use_legacy_user_query', false, __FUNCTION__, $params ) ) {
		extract( $params, EXTR_SKIP );
		$retval = BP_Core_User::get_users( $type, $per_page, $page, $user_id, $include, $search_terms, $populate_extras, $exclude, $meta_key, $meta_value );

	// Default behavior as of BuddyPress 1.7
	} else {

		// Get users like we were asked to do...
		$users = new BP_User_Query( $params );

		// ...but reformat the results to match bp_core_get_users() behavior.
		$retval = array(
			'users' => array_values( $users->results ),
			'total' => $users->total_users
		);
	}

	return apply_filters( 'bp_core_get_users', $retval, $params );
}

/**
 * Returns the domain for the passed user: e.g. http://domain.com/members/andy/
 *
 * @package BuddyPress Core
 * @global $current_user WordPress global variable containing current logged in user information
 * @param int $user_id The ID of the user.
 */
function bp_core_get_user_domain( $user_id, $user_nicename = false, $user_login = false ) {

	if ( empty( $user_id ) ) {
		return;
	}

	$username = bp_core_get_username( $user_id, $user_nicename, $user_login );

	if ( bp_is_username_compatibility_mode() ) {
		$username = rawurlencode( $username );
	}

	$after_domain = bp_core_enable_root_profiles() ? $username : bp_get_members_root_slug() . '/' . $username;
	$domain       = trailingslashit( bp_get_root_domain() . '/' . $after_domain );

	// Don't use this filter.  Subject to removal in a future release.
	// Use the 'bp_core_get_user_domain' filter instead.
	$domain       = apply_filters( 'bp_core_get_user_domain_pre_cache', $domain, $user_id, $user_nicename, $user_login );

	return apply_filters( 'bp_core_get_user_domain', $domain, $user_id, $user_nicename, $user_login );
}

/**
 * Fetch everything in the wp_users table for a user, without any usermeta.
 *
 * @package BuddyPress Core
 * @param int $user_id The ID of the user.
 * @uses BP_Core_User::get_core_userdata() Performs the query.
 */
function bp_core_get_core_userdata( $user_id ) {
	if ( empty( $user_id ) )
		return false;

	if ( !$userdata = wp_cache_get( 'bp_core_userdata_' . $user_id, 'bp' ) ) {
		$userdata = BP_Core_User::get_core_userdata( $user_id );
		wp_cache_set( 'bp_core_userdata_' . $user_id, $userdata, 'bp' );
	}
	return apply_filters( 'bp_core_get_core_userdata', $userdata );
}

/**
 * Returns the user id for the user that is currently being displayed.
 * eg: http://andy.domain.com/ or http://domain.com/andy/
 *
 * @package BuddyPress Core
 * @uses bp_core_get_userid_from_user_login() Returns the user id for the username passed
 * @return int The user id for the user that is currently being displayed, return zero if this is not a user home and just a normal blog.
 */
function bp_core_get_displayed_userid( $user_login ) {
	return apply_filters( 'bp_core_get_displayed_userid', bp_core_get_userid( $user_login ) );
}

/**
 * Returns the user_id for a user based on their username.
 *
 * @package BuddyPress Core
 * @param string $username Username to check.
 * @global $wpdb WordPress DB access object.
 * @return int|bool The ID of the matched user, or false.
 */
function bp_core_get_userid( $username ) {
	global $wpdb;

	if ( empty( $username ) )
		return false;

	return apply_filters( 'bp_core_get_userid', $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->users} WHERE user_login = %s", $username ) ), $username );
}

/**
 * Returns the user_id for a user based on their user_nicename.
 *
 * @package BuddyPress Core
 * @param string $username Username to check.
 * @global $wpdb WordPress DB access object.
 * @return int|bool The ID of the matched user, or false.
 */
function bp_core_get_userid_from_nicename( $user_nicename ) {
	global $wpdb;

	if ( empty( $user_nicename ) )
		return false;

	return apply_filters( 'bp_core_get_userid_from_nicename', $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->users} WHERE user_nicename = %s", $user_nicename ) ), $user_nicename );
}

/**
 * Returns the username for a user based on their user id.
 *
 * @package BuddyPress Core
 * @param int $uid User ID to check.
 * @uses bp_core_get_core_userdata() Fetch the userdata for a user ID
 * @return string|bool The username of the matched user, or false.
 */
function bp_core_get_username( $user_id = 0, $user_nicename = false, $user_login = false ) {
	$bp = buddypress();

	// Check cache for user nicename
	$username = wp_cache_get( 'bp_user_username_' . $user_id, 'bp' );
	if ( false === $username ) {

		// Cache not found so prepare to update it
		$update_cache = true;

		// Nicename and login were not passed
		if ( empty( $user_nicename ) && empty( $user_login ) ) {

			// User ID matches logged in user
			if ( bp_loggedin_user_id() == $user_id ) {
				$userdata = &$bp->loggedin_user->userdata;

			// User ID matches displayed in user
			} elseif ( bp_displayed_user_id() == $user_id ) {
				$userdata = &$bp->displayed_user->userdata;

			// No user ID match
			} else {
				$userdata = false;
			}

			// No match so go dig
			if ( empty( $userdata ) ) {

				// User not found so return false
				if ( !$userdata = bp_core_get_core_userdata( $user_id ) ) {
					return false;
				}
			}

			// Update the $user_id for later
			$user_id       = $userdata->ID;

			// Two possible options
			$user_nicename = $userdata->user_nicename;
			$user_login    = $userdata->user_login;
		}

		// Pull an audible and maybe use the login over the nicename
		$username = bp_is_username_compatibility_mode() ? $user_login : $user_nicename;

	// Username found in cache so don't update it again
	} else {
		$update_cache = false;
	}

	// Add this to cache
	if ( ( true === $update_cache ) && !empty( $username ) ) {
		wp_cache_set( 'bp_user_username_' . $user_id, $username, 'bp' );

	// @todo bust this cache if no $username found?
	//} else {
	//	wp_cache_delete( 'bp_user_username_' . $user_id );
	}

	return apply_filters( 'bp_core_get_username', $username );
}

/**
 * Returns the user_nicename for a user based on their user_id. This should be
 * used for linking to user profiles and anywhere else a sanitized and unique
 * slug to a user is needed.
 *
 * @since BuddyPress (1.5)
 *
 * @package BuddyPress Core
 * @param int $uid User ID to check.
 * @global $userdata WordPress user data for the current logged in user.
 * @uses get_userdata() WordPress function to fetch the userdata for a user ID
 * @return string|bool The username of the matched user, or false.
 */
function bp_members_get_user_nicename( $user_id ) {
	global $bp;

	if ( !$user_nicename = wp_cache_get( 'bp_members_user_nicename_' . $user_id, 'bp' ) ) {
		$update_cache = true;

		// User ID matches logged in user
		if ( bp_loggedin_user_id() == $user_id ) {
			$userdata = &$bp->loggedin_user->userdata;

		// User ID matches displayed in user
		} elseif ( bp_displayed_user_id() == $user_id ) {
			$userdata = &$bp->displayed_user->userdata;

		// No user ID match
		} else {
			$userdata = false;
		}

		// No match so go dig
		if ( empty( $userdata ) ) {

			// User not found so return false
			if ( !$userdata = bp_core_get_core_userdata( $user_id ) ) {
				return false;
			}
		}

		// User nicename found
		$user_nicename = $userdata->user_nicename;

	// Nicename found in cache so don't update it again
	} else {
		$update_cache = false;
	}

	// Add this to cache
	if ( true == $update_cache && !empty( $user_nicename ) )
		wp_cache_set( 'bp_members_user_nicename_' . $user_id, $user_nicename, 'bp' );

	return apply_filters( 'bp_members_get_user_nicename', $user_nicename );
}

/**
 * Returns the email address for the user based on user ID
 *
 * @package BuddyPress Core
 * @param int $uid User ID to check.
 * @uses get_userdata() WordPress function to fetch the userdata for a user ID
 * @return string The email for the matched user. Empty string if no user matched the $uid.
 */
function bp_core_get_user_email( $uid ) {

	if ( !$email = wp_cache_get( 'bp_user_email_' . $uid, 'bp' ) ) {
		// User exists
		if ( $ud = bp_core_get_core_userdata( $uid ) )
			$email = $ud->user_email;

		// User was deleted
		else
			$email = '';

		wp_cache_set( 'bp_user_email_' . $uid, $email, 'bp' );
	}

	return apply_filters( 'bp_core_get_user_email', $email );
}

/**
 * Returns a HTML formatted link for a user with the user's full name as the link text.
 * eg: <a href="http://andy.domain.com/">Andy Peatling</a>
 * Optional parameters will return just the name or just the URL.
 *
 * @param int $user_id User ID to check.
 * @param bool $no_anchor Disable URL and HTML and just return full name. Default false.
 * @param bool $just_link Disable full name and HTML and just return the URL text. Default false.
 * @return string|bool The link text based on passed parameters, or false on no match.
 * @todo This function needs to be cleaned up or split into separate functions
 */
function bp_core_get_userlink( $user_id, $no_anchor = false, $just_link = false ) {
	$display_name = bp_core_get_user_displayname( $user_id );

	if ( empty( $display_name ) )
		return false;

	if ( $no_anchor )
		return $display_name;

	if ( !$url = bp_core_get_user_domain( $user_id ) )
		return false;

	if ( $just_link )
		return $url;

	return apply_filters( 'bp_core_get_userlink', '<a href="' . $url . '" title="' . $display_name . '">' . $display_name . '</a>', $user_id );
}

/**
 * Fetch the display name for a group of users.
 *
 * Uses the 'Name' field in xprofile if available. Falls back on WP
 * display_name, and then user_nicename.
 *
 * @since BuddyPress (2.0.0)
 *
 * @param array $user_ids
 */
function bp_core_get_user_displaynames( $user_ids ) {

	// Sanitize
	$user_ids = wp_parse_id_list( $user_ids );

	// Remove dupes and empties
	$user_ids = array_unique( array_filter( $user_ids ) );

	if ( empty( $user_ids ) ) {
		return array();
	}

	$uncached_ids = array();
	foreach ( $user_ids as $user_id ) {
		if ( false === wp_cache_get( 'bp_user_fullname_' . $user_id, 'bp' ) ) {
			$uncached_ids[] = $user_id;
		}
	}

	// Prime caches
	if ( ! empty( $uncached_ids ) ) {
		if ( bp_is_active( 'xprofile' ) ) {
			$fullname_data = BP_XProfile_ProfileData::get_value_byid( 1, $uncached_ids );

			// Key by user_id
			$fullnames = array();
			foreach ( $fullname_data as $fd ) {
				if ( ! empty( $fd->value ) ) {
					$fullnames[ intval( $fd->user_id ) ] = $fd->value;
				}
			}

			// If xprofiledata is not found for any users,  we'll look
			// them up separately
			$no_xprofile_ids = array_diff( $uncached_ids, array_keys( $fullnames ) );
		} else {
			$fullnames = array();
			$no_xprofile_ids = $user_ids;
		}

		if ( ! empty( $no_xprofile_ids ) ) {
			// Use WP_User_Query because we don't need BP information
			$query = new WP_User_Query( array(
				'include'     => $no_xprofile_ids,
				'fields'      => array( 'ID', 'user_nicename', 'display_name', ),
				'count_total' => false,
				'blog_id'     => 0,
			) );

			foreach ( $query->results as $qr ) {
				$fullnames[ $qr->ID ] = ! empty( $qr->display_name ) ? $qr->display_name : $qr->user_nicename;

				// If xprofile is active, set this value as the
				// xprofile display name as well
				if ( bp_is_active( 'xprofile' ) ) {
					xprofile_set_field_data( 1, $qr->ID, $fullnames[ $qr->ID ] );
				}
			}
		}

		foreach ( $fullnames as $fuser_id => $fname ) {
			wp_cache_set( 'bp_user_fullname_' . $fuser_id, $fname, 'bp' );
		}
	}

	$retval = array();
	foreach ( $user_ids as $user_id ) {
		$retval[ $user_id ] = wp_cache_get( 'bp_user_fullname_' . $user_id, 'bp' );
	}

	return $retval;
}

/**
 * Fetch the display name for a user.
 *
 * @param int|string $user_id_or_username User ID or username.
 * @return string|bool The display name for the user in question, or false if
 *         user not found.
 */
function bp_core_get_user_displayname( $user_id_or_username ) {
	global $bp;

	$fullname = '';

	if ( empty( $user_id_or_username ) ) {
		return false;
	}

	if ( ! is_numeric( $user_id_or_username ) ) {
		$user_id = bp_core_get_userid( $user_id_or_username );
	} else {
		$user_id = $user_id_or_username;
	}

	if ( empty( $user_id ) ) {
		return false;
	}

	$display_names = bp_core_get_user_displaynames( array( $user_id ) );

	if ( ! isset( $display_names[ $user_id ] ) ) {
		$fullname = false;
	} else {
		$fullname = $display_names[ $user_id ];
	}

	return apply_filters( 'bp_core_get_user_displayname', $fullname, $user_id );
}
add_filter( 'bp_core_get_user_displayname', 'strip_tags', 1 );
add_filter( 'bp_core_get_user_displayname', 'trim'          );
add_filter( 'bp_core_get_user_displayname', 'stripslashes'  );
add_filter( 'bp_core_get_user_displayname', 'esc_html'      );

/**
 * Returns the user link for the user based on user email address
 *
 * @package BuddyPress Core
 * @param string $email The email address for the user.
 * @uses bp_core_get_userlink() BuddyPress function to get a userlink by user ID.
 * @uses get_user_by() WordPress function to get userdata via an email address
 * @return string The link to the users home base. False on no match.
 */
function bp_core_get_userlink_by_email( $email ) {
	$user = get_user_by( 'email', $email );
	return apply_filters( 'bp_core_get_userlink_by_email', bp_core_get_userlink( $user->ID, false, false, true ) );
}

/**
 * Returns the user link for the user based on the supplied identifier
 *
 * @param string $username If BP_ENABLE_USERNAME_COMPATIBILITY_MODE is set, this will be user_login, otherwise it will be user_nicename.
 * @return string|bool The link to the users home base, false on no match.
 */
function bp_core_get_userlink_by_username( $username ) {
	if ( bp_is_username_compatibility_mode() )
		$user_id = bp_core_get_userid( $username );
	else
		$user_id = bp_core_get_userid_from_nicename( $username );

	return apply_filters( 'bp_core_get_userlink_by_username', bp_core_get_userlink( $user_id, false, false, true ) );
}

/**
 * Returns the total number of members for the installation.
 *
 * @package BuddyPress Core
 * @return int The total number of members.
 */
function bp_core_get_total_member_count() {
	global $wpdb;

	if ( !$count = wp_cache_get( 'bp_total_member_count', 'bp' ) ) {
		$status_sql = bp_core_get_status_sql();
		$count = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->users} WHERE {$status_sql}" );
		wp_cache_set( 'bp_total_member_count', $count, 'bp' );
	}

	return apply_filters( 'bp_core_get_total_member_count', $count );
}

/**
 * Returns the total number of members, limited to those members with last_activity
 *
 * @return int The number of active members
 */
function bp_core_get_active_member_count() {
	global $wpdb;

	if ( !$count = get_transient( 'bp_active_member_count' ) ) {
		$bp = buddypress();

		// Avoid a costly join by splitting the lookup
		if ( is_multisite() ) {
			$sql = "SELECT ID FROM {$wpdb->users} WHERE (user_status != 0 OR deleted != 0 OR user_status != 0)";
		} else {
			$sql = "SELECT ID FROM {$wpdb->users} WHERE user_status != 0";
		}

		$exclude_users     = $wpdb->get_col( $sql );
		$exclude_users_sql = !empty( $exclude_users ) ? "AND user_id NOT IN (" . implode( ',', wp_parse_id_list( $exclude_users ) ) . ")" : '';
		$count             = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(user_id) FROM {$bp->members->table_name_last_activity} WHERE component = %s AND type = 'last_activity' {$exclude_users_sql}", $bp->members->id ) );

		set_transient( 'bp_active_member_count', $count );
	}

	return apply_filters( 'bp_core_get_active_member_count', $count );
}

/**
 * Process a spammed or unspammed user.
 *
 * This function is called from three places:
 *
 * - in bp_settings_action_capabilities() (from the front-end)
 * - by bp_core_mark_user_spam_admin()    (from wp-admin)
 * - bp_core_mark_user_ham_admin()        (from wp-admin)
 *
 * @since BuddyPress (1.6.0)
 *
 * @param int $user_id The ID of the user being spammed/hammed.
 * @param string $status 'spam' if being marked as spam, 'ham' otherwise.
 * @param bool $do_wp_cleanup True to force the cleanup of WordPress content
 *        and status, otherwise false. Generally, this should only be false if
 *        WordPress is expected to have performed this cleanup independently,
 *        as when hooked to 'make_spam_user'.
 * @return bool True on success, false on failure.
 */
function bp_core_process_spammer_status( $user_id, $status, $do_wp_cleanup = true ) {
	global $wpdb;

	// Bail if no user ID
	if ( empty( $user_id ) )
		return;

	// Bail if user ID is super admin
	if ( is_super_admin( $user_id ) )
		return;

	// Get the functions file
	if ( is_multisite() ) {
		require_once( ABSPATH . 'wp-admin/includes/ms.php' );
	}

	$is_spam = ( 'spam' == $status );

	// Only you can prevent infinite loops
	remove_action( 'make_spam_user', 'bp_core_mark_user_spam_admin' );
	remove_action( 'make_ham_user',  'bp_core_mark_user_ham_admin'  );

	// Determine if we are on an admin page
	$is_admin = is_admin();
	if ( $is_admin && ! defined( 'DOING_AJAX' ) ) {
		$is_admin = (bool) ( buddypress()->members->admin->user_page !== get_current_screen()->id );
	}

	// When marking as spam in the Dashboard, these actions are handled by WordPress
	if ( $do_wp_cleanup ) {

		// Get the blogs for the user
		$blogs = get_blogs_of_user( $user_id, true );

		foreach ( (array) $blogs as $key => $details ) {

			// Do not mark the main or current root blog as spam
			if ( 1 == $details->userblog_id || bp_get_root_blog_id() == $details->userblog_id ) {
				continue;
			}

			// Update the blog status
			update_blog_status( $details->userblog_id, 'spam', $is_spam );
		}

		// Finally, mark this user as a spammer
		if ( is_multisite() ) {
			update_user_status( $user_id, 'spam', $is_spam );
		}

		// Always set single site status
		$wpdb->update( $wpdb->users, array( 'user_status' => $is_spam ), array( 'ID' => $user_id ) );

		// Call multisite actions in single site mode for good measure
		if ( !is_multisite() ) {
			$wp_action = ( true === $is_spam ) ? 'make_spam_user' : 'make_ham_user';
			do_action( $wp_action, bp_displayed_user_id() );
		}
	}

	// Hide this user's activity
	if ( ( true === $is_spam ) && bp_is_active( 'activity' ) ) {
		bp_activity_hide_user_activity( $user_id );
	}

	// We need a special hook for is_spam so that components can delete data at spam time
	$bp_action = ( true === $is_spam ) ? 'bp_make_spam_user' : 'bp_make_ham_user';
	do_action( $bp_action, $user_id );

	// Allow plugins to do neat things
	do_action( 'bp_core_process_spammer_status', $user_id, $is_spam );

	// Put things back how we found them
	add_action( 'make_spam_user', 'bp_core_mark_user_spam_admin' );
	add_action( 'make_ham_user',  'bp_core_mark_user_ham_admin'  );

	return true;
}

/**
 * Hook to WP's make_spam_user and run our custom BP spam functions
 *
 * @since BuddyPress (1.6)
 *
 * @param int $user_id The user id passed from the make_spam_user hook
 */
function bp_core_mark_user_spam_admin( $user_id ) {
	bp_core_process_spammer_status( $user_id, 'spam', false );
}
add_action( 'make_spam_user', 'bp_core_mark_user_spam_admin' );

/**
 * Hook to WP's make_ham_user and run our custom BP spam functions
 *
 * @since BuddyPress (1.6)
 *
 * @param int $user_id The user id passed from the make_ham_user hook
 */
function bp_core_mark_user_ham_admin( $user_id ) {
	bp_core_process_spammer_status( $user_id, 'ham', false );
}
add_action( 'make_ham_user', 'bp_core_mark_user_ham_admin' );

/**
 * Checks if the user has been marked as a spammer.
 *
 * @package BuddyPress Core
 * @param int $user_id int The id for the user.
 * @return bool True if spammer, False if not.
 */
function bp_is_user_spammer( $user_id = 0 ) {

	// No user to check
	if ( empty( $user_id ) )
		return false;

	$bp = buddypress();

	// Assume user is not spam
	$is_spammer = false;

	// Setup our user
	$user = false;

	// Get locally-cached data if available
	switch ( $user_id ) {
		case bp_loggedin_user_id() :
			$user = ! empty( $bp->loggedin_user->userdata ) ? $bp->loggedin_user->userdata : false;
			break;

		case bp_displayed_user_id() :
			$user = ! empty( $bp->displayed_user->userdata ) ? $bp->displayed_user->userdata : false;
			break;
	}

	// Manually get userdata if still empty
	if ( empty( $user ) ) {
		$user = get_userdata( $user_id );
	}

	// No user found
	if ( empty( $user ) ) {
		$is_spammer = false;

	// User found
	} else {

		// Check if spam
		if ( !empty( $user->spam ) )
			$is_spammer = true;

		if ( 1 == $user->user_status )
			$is_spammer = true;
	}

	return apply_filters( 'bp_is_user_spammer', (bool) $is_spammer );
}

/**
 * Checks if the user has been marked as deleted.
 *
 * @package BuddyPress Core
 * @param int $user_id int The id for the user.
 * @return bool True if deleted, False if not.
 */
function bp_is_user_deleted( $user_id = 0 ) {

	// No user to check
	if ( empty( $user_id ) )
		return false;

	$bp = buddypress();

	// Assume user is not deleted
	$is_deleted = false;

	// Setup our user
	$user = false;

	// Get locally-cached data if available
	switch ( $user_id ) {
		case bp_loggedin_user_id() :
			$user = ! empty( $bp->loggedin_user->userdata ) ? $bp->loggedin_user->userdata : false;
			break;

		case bp_displayed_user_id() :
			$user = ! empty( $bp->displayed_user->userdata ) ? $bp->displayed_user->userdata : false;
			break;
	}

	// Manually get userdata if still empty
	if ( empty( $user ) ) {
		$user = get_userdata( $user_id );
	}

	// No user found
	if ( empty( $user ) ) {
		$is_deleted = true;

	// User found
	} else {

		// Check if deleted
		if ( !empty( $user->deleted ) )
			$is_deleted = true;

		if ( 2 == $user->user_status )
			$is_deleted = true;

	}

	return apply_filters( 'bp_is_user_deleted', (bool) $is_deleted );
}

/**
 * Checks if user is active
 *
 * @since BuddyPress (1.6)
 *
 * @uses is_user_logged_in() To check if user is logged in
 * @uses bp_loggedin_user_id() To get current user ID
 * @uses bp_is_user_spammer() To check if user is spammer
 * @uses bp_is_user_deleted() To check if user is deleted
 *
 * @param int $user_id The user ID to check
 * @return bool True if public, false if not
 */
function bp_is_user_active( $user_id = 0 ) {

	// Default to current user
	if ( empty( $user_id ) && is_user_logged_in() )
		$user_id = bp_loggedin_user_id();

	// No user to check
	if ( empty( $user_id ) )
		return false;

	// Check spam
	if ( bp_is_user_spammer( $user_id ) )
		return false;

	// Check deleted
	if ( bp_is_user_deleted( $user_id ) )
		return false;

	// Assume true if not spam or deleted
	return true;
}

/**
 * Checks if user is not active.
 *
 * @since BuddyPress (1.6)
 *
 * @uses is_user_logged_in() To check if user is logged in
 * @uses bp_get_displayed_user_id() To get current user ID
 * @uses bp_is_user_active() To check if user is active
 *
 * @param int $user_id The user ID to check
 * @return bool True if inactive, false if active
 */
function bp_is_user_inactive( $user_id = 0 ) {

	// Default to current user
	if ( empty( $user_id ) && is_user_logged_in() )
		$user_id = bp_loggedin_user_id();

	// No user to check
	if ( empty( $user_id ) )
		return false;

	// Return the inverse of active
	return !bp_is_user_active( $user_id );
}

/**
 * Update a user's last activity.
 *
 * @since BuddyPress (1.9.0)
 *
 * @param int $user_id ID of the user being updated.
 * @param string $time Time of last activity, in 'Y-m-d H:i:s' format.
 * @return bool True on success, false on failure.
 */
function bp_update_user_last_activity( $user_id = 0, $time = '' ) {
	// Fall back on current user
	if ( empty( $user_id ) ) {
		$user_id = bp_loggedin_user_id();
	}

	// Bail if the user id is 0, as there's nothing to update
	if ( empty( $user_id ) ) {
		return false;
	}

	// Fall back on current time
	if ( empty( $time ) ) {
		$time = bp_core_current_time();
	}

	// As of BuddyPress 2.0, last_activity is no longer stored in usermeta.
	// However, we mirror it there for backward compatibility. Do not use!
	// Remove our warning and re-add.
	remove_filter( 'update_user_metadata', '_bp_update_user_meta_last_activity_warning', 10, 4 );
	remove_filter( 'get_user_metadata', '_bp_get_user_meta_last_activity_warning', 10, 3 );
	update_user_meta( $user_id, 'last_activity', $time );
	add_filter( 'update_user_metadata', '_bp_update_user_meta_last_activity_warning', 10, 4 );
	add_filter( 'get_user_metadata', '_bp_get_user_meta_last_activity_warning', 10, 3 );

	return BP_Core_User::update_last_activity( $user_id, $time );
}

/**
 * Backward compatibility for 'last_activity' usermeta fetching.
 *
 * In BuddyPress 2.0, user last_activity data was moved out of usermeta. For
 * backward compatibility, we continue to mirror the data there. This function
 * serves two purposes: it warns plugin authors of the change, and it returns
 * the data from the proper location.
 *
 * @since BuddyPress (2.0.0)
 *
 * @access private For internal use only.
 *
 * @param null $retval
 * @param int $object_id ID of the user.
 * @param string $meta_key Meta key being fetched.
 */
function _bp_get_user_meta_last_activity_warning( $retval, $object_id, $meta_key ) {
	static $warned;

	if ( 'last_activity' === $meta_key ) {
		// Don't send the warning more than once per pageload
		if ( empty( $warned ) ) {
			_doing_it_wrong( 'get_user_meta( $user_id, \'last_activity\' )', __( 'User last_activity data is no longer stored in usermeta. Use bp_get_user_last_activity() instead.', 'buddypress' ), '2.0.0' );
			$warned = 1;
		}

		return bp_get_user_last_activity( $object_id );
	}

	return $retval;
}
add_filter( 'get_user_metadata', '_bp_get_user_meta_last_activity_warning', 10, 3 );

/**
 * Backward compatibility for 'last_activity' usermeta setting.
 *
 * In BuddyPress 2.0, user last_activity data was moved out of usermeta. For
 * backward compatibility, we continue to mirror the data there. This function
 * serves two purposes: it warns plugin authors of the change, and it updates
 * the data in the proper location.
 *
 * @since BuddyPress (2.0.0)
 *
 * @access private For internal use only.
 *
 * @param int $meta_id ID of the just-set usermeta row.
 * @param int $object_id ID of the user.
 * @param string $meta_key Meta key being fetched.
 * @param string $meta_value Active time.
 */
function _bp_update_user_meta_last_activity_warning( $meta_id, $object_id, $meta_key, $meta_value ) {
	if ( 'last_activity' === $meta_key ) {
		_doing_it_wrong( 'update_user_meta( $user_id, \'last_activity\' )', __( 'User last_activity data is no longer stored in usermeta. Use bp_update_user_last_activity() instead.', 'buddypress' ), '2.0.0' );
		bp_update_user_last_activity( $object_id, $meta_value );
	}
}
add_filter( 'update_user_metadata', '_bp_update_user_meta_last_activity_warning', 10, 4 );

/**
 * Get the last activity for a given user.
 *
 * @param int $user_id The ID of the user.
 * @return string Time of last activity, in 'Y-m-d H:i:s' format, or an empty
 *         string if none is found.
 */
function bp_get_user_last_activity( $user_id = 0 ) {
	$activity = '';

	$last_activity = BP_Core_User::get_last_activity( $user_id );
	if ( ! empty( $last_activity[ $user_id ] ) ) {
		$activity = $last_activity[ $user_id ]['date_recorded'];
	}

	return apply_filters( 'bp_get_user_last_activity', $activity, $user_id );
}

/**
 * Migrate last_activity data from the usermeta table to the activity table.
 *
 * Generally, this function is only run when BP is upgraded to 2.0. It can also
 * be called directly from the BuddyPress Tools panel.
 *
 * @since BuddyPress (2.0.0)
 */
function bp_last_activity_migrate() {
	global $wpdb;

	$bp = buddypress();

	// The "NOT IN" clause prevents duplicates
	$sql = "INSERT INTO {$bp->members->table_name_last_activity} (`user_id`, `component`, `type`, `action`, `content`, `primary_link`, `item_id`, `date_recorded` ) (
		  SELECT user_id, '{$bp->members->id}' as component, 'last_activity' as type, '' as action, '' as content, '' as primary_link, 0 as item_id, meta_value AS date_recorded
		  FROM {$wpdb->usermeta}
		  WHERE
		    meta_key = 'last_activity'
		    AND
		    user_id NOT IN (
		      SELECT user_id
		      FROM {$bp->members->table_name_last_activity}
		      WHERE component = '{$bp->members->id}' AND type = 'last_activity'
		    )
	);";

	return $wpdb->query( $sql );
}

/**
 * Fetch every post that is authored by the given user for the current blog.
 *
 * @package BuddyPress Core
 * @global $wpdb WordPress user data for the current logged in user.
 * @return array of post ids.
 */
function bp_core_get_all_posts_for_user( $user_id = 0 ) {
	global $wpdb;

	if ( empty( $user_id ) )
		$user_id = bp_displayed_user_id();

	return apply_filters( 'bp_core_get_all_posts_for_user', $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_author = %d AND post_status = 'publish' AND post_type = 'post'", $user_id ) ) );
}

/**
 * Allows a user to completely remove their account from the system
 *
 * @package BuddyPress Core
 * @uses wpmu_delete_user() Deletes a user from the system on multisite installs.
 * @uses wp_delete_user() Deletes a user from the system on singlesite installs.
 */
function bp_core_delete_account( $user_id = 0 ) {

	// Use logged in user ID if none is passed
	if ( empty( $user_id ) )
		$user_id = bp_loggedin_user_id();

	// Site admins cannot be deleted
	if ( is_super_admin( $user_id ) )
		return false;

	// Extra checks if user is not deleting themselves
	if ( bp_loggedin_user_id() !== absint( $user_id ) ) {

		// Bail if current user cannot delete any users
		if ( ! bp_current_user_can( 'delete_users' ) ) {
			return false;
		}

		// Bail if current user cannot delete this user
		if ( ! current_user_can_for_blog( bp_get_root_blog_id(), 'delete_user', $user_id ) ) {
			return false;
		}
	}

	do_action( 'bp_core_pre_delete_account', $user_id );

	// Specifically handle multi-site environment
	if ( is_multisite() ) {
		require_once( ABSPATH . '/wp-admin/includes/ms.php'   );
		require_once( ABSPATH . '/wp-admin/includes/user.php' );

		$retval = wpmu_delete_user( $user_id );

	// Single site user deletion
	} else {
		require_once( ABSPATH . '/wp-admin/includes/user.php' );
		$retval = wp_delete_user( $user_id );
	}

	do_action( 'bp_core_deleted_account', $user_id );

	return $retval;
}

/**
 * Delete a user's avatar when the user is deleted.
 *
 * @since BuddyPress (1.9.0)
 *
 * @param int $user_id ID of the user who is about to be deleted.
 * @return bool True on success, false on failure.
 */
function bp_core_delete_avatar_on_user_delete( $user_id ) {
	return bp_core_delete_existing_avatar( array(
		'item_id' => $user_id,
		'object'  => 'user',
	) );
}
add_action( 'wpmu_delete_user', 'bp_core_delete_avatar_on_user_delete' );
add_action( 'delete_user', 'bp_core_delete_avatar_on_user_delete' );

/**
 * Localization safe ucfirst() support.
 *
 * @package BuddyPress Core
 */
function bp_core_ucfirst( $str ) {
	if ( function_exists( 'mb_strtoupper' ) && function_exists( 'mb_substr' ) ) {
		$fc = mb_strtoupper( mb_substr( $str, 0, 1 ) );
		return $fc.mb_substr( $str, 1 );
	} else {
		return ucfirst( $str );
	}
}

/**
 * When a user logs in, check if they have been marked as a spammer. If yes then simply
 * redirect them to the home page and stop them from logging in.
 *
 * @param WP_User|WP_Error $user Either the WP_User object or the WP_Error object
 * @return WP_User|WP_Error If the user is not a spammer, return the WP_User object. Otherwise a new WP_Error object.
 *
 * @since BuddyPress (1.1.2)
 */
function bp_core_boot_spammer( $user ) {
	// check to see if the $user has already failed logging in, if so return $user as-is
	if ( is_wp_error( $user ) || empty( $user ) )
		return $user;

	// the user exists; now do a check to see if the user is a spammer
	// if the user is a spammer, stop them in their tracks!
	if ( is_a( $user, 'WP_User' ) && ( ( is_multisite() && (int) $user->spam ) || 1 == $user->user_status ) )
		return new WP_Error( 'invalid_username', __( '<strong>ERROR</strong>: Your account has been marked as a spammer.', 'buddypress' ) );

	// user is good to go!
	return $user;
}
add_filter( 'authenticate', 'bp_core_boot_spammer', 30 );

/**
 * Deletes usermeta for the user when the user is deleted.
 *
 * @package BuddyPress Core
 * @param int $user_id The user id for the user to delete usermeta for
 * @uses bp_delete_user_meta() deletes a row from the wp_usermeta table based on meta_key
 */
function bp_core_remove_data( $user_id ) {

	// Remove last_activity data
	BP_Core_User::delete_last_activity( $user_id );

	// Flush the cache to remove the user from all cached objects
	wp_cache_flush();
}
add_action( 'wpmu_delete_user',  'bp_core_remove_data' );
add_action( 'delete_user',       'bp_core_remove_data' );
add_action( 'bp_make_spam_user', 'bp_core_remove_data' );

function bp_core_can_edit_settings() {
	if ( bp_is_my_profile() )
		return true;

	if ( is_super_admin( bp_displayed_user_id() ) && ! is_super_admin() ) {
		return false;
	}

	if ( bp_current_user_can( 'bp_moderate' ) || current_user_can( 'edit_users' ) )
		return true;

	return false;
}

/** Sign-up *******************************************************************/

/**
 * Flush illegal names by getting and setting 'illegal_names' site option
 */
function bp_core_flush_illegal_names() {
	$illegal_names = get_site_option( 'illegal_names' );
	update_site_option( 'illegal_names', $illegal_names );
}

/**
 * Filter the illegal_names site option and make sure it includes a few
 * specific BuddyPress and Multi-site slugs
 *
 * @param array|string $value Illegal names from field
 * @param array|string $oldvalue The value as it is currently
 * @return array Merged and unique array of illegal names
 */
function bp_core_get_illegal_names( $value = '', $oldvalue = '' ) {

	// Make sure $value is array
	if ( empty( $value ) ) {
		$db_illegal_names = array();
	}

	if ( is_array( $value ) ) {
		$db_illegal_names = $value;
	} elseif ( is_string( $value ) ) {
		$db_illegal_names = explode( ' ', $value );
	}

	// Add the core components' slugs to the banned list even if their components aren't active.
	$bp_component_slugs = array(
		'groups',
		'members',
		'forums',
		'blogs',
		'activity',
		'profile',
		'friends',
		'search',
		'settings',
		'notifications',
		'register',
		'activate'
	);

	// Core constants
	$slug_constants = array(
		'BP_GROUPS_SLUG',
		'BP_MEMBERS_SLUG',
		'BP_FORUMS_SLUG',
		'BP_BLOGS_SLUG',
		'BP_ACTIVITY_SLUG',
		'BP_XPROFILE_SLUG',
		'BP_FRIENDS_SLUG',
		'BP_SEARCH_SLUG',
		'BP_SETTINGS_SLUG',
		'BP_NOTIFICATIONS_SLUG',
		'BP_REGISTER_SLUG',
		'BP_ACTIVATION_SLUG',
	);
	foreach( $slug_constants as $constant ) {
		if ( defined( $constant ) ) {
			$bp_component_slugs[] = constant( $constant );
		}
	}

	// Add our slugs to the array and allow them to be filtered
	$filtered_illegal_names = apply_filters( 'bp_core_illegal_usernames', array_merge( array( 'www', 'web', 'root', 'admin', 'main', 'invite', 'administrator' ), $bp_component_slugs ) );

	// Merge the arrays together
	$merged_names           = array_merge( (array) $filtered_illegal_names, (array) $db_illegal_names );

	// Remove duplicates
	$illegal_names          = array_unique( (array) $merged_names );

	return apply_filters( 'bp_core_illegal_names', $illegal_names );
}
add_filter( 'pre_update_site_option_illegal_names', 'bp_core_get_illegal_names', 10, 2 );

/**
 * Check that an email address is valid for use
 *
 * Performs the following checks:
 *   - Is the email address well-formed?
 *   - Is the email address already used?
 *   - If there's an email domain blacklist, is the current domain on it?
 *   - If there's an email domain whitelest, is the current domain on it?
 *
 * @since BuddyPress (1.6.2)
 *
 * @param string $user_email The email being checked
 * @return bool|array True if the address passes all checks; otherwise an array
 *   of error codes
 */
function bp_core_validate_email_address( $user_email ) {
	$errors = array();

	$user_email = sanitize_email( $user_email );

	// Is the email well-formed?
	if ( ! is_email( $user_email ) )
		$errors['invalid'] = 1;

	// Is the email on the Banned Email Domains list?
	// Note: This check only works on Multisite
	if ( function_exists( 'is_email_address_unsafe' ) && is_email_address_unsafe( $user_email ) )
		$errors['domain_banned'] = 1;

	// Is the email on the Limited Email Domains list?
	// Note: This check only works on Multisite
	$limited_email_domains = get_site_option( 'limited_email_domains' );
	if ( is_array( $limited_email_domains ) && empty( $limited_email_domains ) == false ) {
		$emaildomain = substr( $user_email, 1 + strpos( $user_email, '@' ) );
		if ( ! in_array( $emaildomain, $limited_email_domains ) ) {
			$errors['domain_not_allowed'] = 1;
		}
	}

	// Is the email alreday in use?
	if ( email_exists( $user_email ) )
		$errors['in_use'] = 1;

	$retval = ! empty( $errors ) ? $errors : true;

	return $retval;
}

/**
 * Add the appropriate errors to a WP_Error object, given results of a validation test
 *
 * Functions like bp_core_validate_email_address() return a structured array
 * of error codes. bp_core_add_validation_error_messages() takes this array and
 * parses, adding the appropriate error messages to the WP_Error object.
 *
 * @since BuddyPress (1.7)
 * @see bp_core_validate_email_address()
 *
 * @param WP_Error $errors WP_Error object
 * @param array $validation_results The return value of a validation function
 *   like bp_core_validate_email_address()
 */
function bp_core_add_validation_error_messages( WP_Error $errors, $validation_results ) {
	if ( ! empty( $validation_results['invalid'] ) )
		$errors->add( 'user_email', __( 'Please check your email address.', 'buddypress' ) );

	if ( ! empty( $validation_results['domain_banned'] ) )
		$errors->add( 'user_email',  __( 'Sorry, that email address is not allowed!', 'buddypress' ) );

	if ( ! empty( $validation_results['domain_not_allowed'] ) )
		$errors->add( 'user_email', __( 'Sorry, that email address is not allowed!', 'buddypress' ) );

	if ( ! empty( $validation_results['in_use'] ) )
		$errors->add( 'user_email', __( 'Sorry, that email address is already used!', 'buddypress' ) );
}

/**
 * Validate a user name and email address when creating a new user.
 *
 * @param string $user_name Username to validate
 * @param string $user_email Email address to validate
 * @return array Results of user validation including errors, if any
 */
function bp_core_validate_user_signup( $user_name, $user_email ) {

	// Make sure illegal names include BuddyPress slugs and values
	bp_core_flush_illegal_names();

	// WordPress Multisite has its own validation. Use it, so that we
	// properly mirror restrictions on username, etc.
	if ( function_exists( 'wpmu_validate_user_signup' ) ) {
		$result = wpmu_validate_user_signup( $user_name, $user_email );

	// When not running Multisite, we perform our own validation. What
	// follows reproduces much of the logic of wpmu_validate_user_signup(),
	// minus the multisite-specific restrictions on user_login
	} else {
		$errors = new WP_Error();

		// Apply any user_login filters added by BP or other plugins before validating
		$user_name = apply_filters( 'pre_user_login', $user_name );

		// User name can't be empty
		if ( empty( $user_name ) ) {
			$errors->add( 'user_name', __( 'Please enter a username', 'buddypress' ) );
		}

		// user name can't be on the blacklist
		$illegal_names = get_site_option( 'illegal_names' );
		if ( in_array( $user_name, (array) $illegal_names ) ) {
			$errors->add( 'user_name', __( 'That username is not allowed', 'buddypress' ) );
		}

		// User name must pass WP's validity check
		if ( ! validate_username( $user_name ) ) {
			$errors->add( 'user_name', __( 'Usernames can contain only letters, numbers, ., -, and @', 'buddypress' ) );
		}

		// Minimum of 4 characters
		if ( strlen( $user_name ) < 4 ) {
			$errors->add( 'user_name',  __( 'Username must be at least 4 characters', 'buddypress' ) );
		}

		// No underscores. @todo Why not?
		if ( false !== strpos( ' ' . $user_name, '_' ) ) {
			$errors->add( 'user_name', __( 'Sorry, usernames may not contain the character "_"!', 'buddypress' ) );
		}

		// No usernames that are all numeric. @todo Why?
		$match = array();
		preg_match( '/[0-9]*/', $user_name, $match );
		if ( $match[0] == $user_name ) {
			$errors->add( 'user_name', __( 'Sorry, usernames must have letters too!', 'buddypress' ) );
		}

		// Check into signups
		$signups = BP_Signup::get( array(
			'user_login' => $user_name,
		) );

		$signup = isset( $signups['signups'] ) && ! empty( $signups['signups'][0] ) ? $signups['signups'][0] : false;

		// Check if the username has been used already.
		if ( username_exists( $user_name ) || ! empty( $signup ) ) {
			$errors->add( 'user_name', __( 'Sorry, that username already exists!', 'buddypress' ) );
		}

		// Validate the email address and process the validation results into
		// error messages
		$validate_email = bp_core_validate_email_address( $user_email );
		bp_core_add_validation_error_messages( $errors, $validate_email );

		// Assemble the return array
		$result = array(
			'user_name'  => $user_name,
			'user_email' => $user_email,
			'errors'     => $errors,
		);

		// Apply WPMU legacy filter
		$result = apply_filters( 'wpmu_validate_user_signup', $result );
	}

 	return apply_filters( 'bp_core_validate_user_signup', $result );
}

function bp_core_validate_blog_signup( $blog_url, $blog_title ) {
	if ( !is_multisite() || !function_exists( 'wpmu_validate_blog_signup' ) )
		return false;

	return apply_filters( 'bp_core_validate_blog_signup', wpmu_validate_blog_signup( $blog_url, $blog_title ) );
}

function bp_core_signup_user( $user_login, $user_password, $user_email, $usermeta ) {
	global $bp;

	// We need to cast $user_id to pass to the filters
	$user_id = false;

	// Multisite installs have their own install procedure
	if ( is_multisite() ) {
		wpmu_signup_user( $user_login, $user_email, $usermeta );

	} else {
		// Format data
		$user_login     = preg_replace( '/\s+/', '', sanitize_user( $user_login, true ) );
		$user_email     = sanitize_email( $user_email );
		$activation_key = substr( md5( time() . rand() . $user_email ), 0, 16 );

		/**
		 * WordPress's default behavior is to create user accounts
		 * immediately at registration time. BuddyPress uses a system
		 * borrowed from WordPress Multisite, where signups are stored
		 * separately and accounts are only created at the time of
		 * activation. For backward compatibility with plugins that may
		 * be anticipating WP's default behavior, BP silently creates
		 * accounts for registrations (though it does not use them). If
		 * you know that you are not running any plugins dependent on
		 * these pending accounts, you may want to save a little DB
		 * clutter by defining setting the BP_SIGNUPS_SKIP_USER_CREATION
		 * to true in your wp-config.php file.
		 */
		if ( ! defined( 'BP_SIGNUPS_SKIP_USER_CREATION' ) || ! BP_SIGNUPS_SKIP_USER_CREATION ) {
			$user_id = BP_Signup::add_backcompat( $user_login, $user_password, $user_email, $usermeta );

			if ( is_wp_error( $user_id ) ) {
				return $user_id;
			}

			$activation_key = wp_hash( $user_id );
			update_user_meta( $user_id, 'activation_key', $activation_key );
		}

		$args = array(
			'user_login'     => $user_login,
			'user_email'     => $user_email,
			'activation_key' => $activation_key,
			'meta'           => $usermeta,
		);

		BP_Signup::add( $args );

		if ( apply_filters( 'bp_core_signup_send_activation_key', true, $user_id, $user_email, $activation_key, $usermeta ) ) {
			bp_core_signup_send_validation_email( $user_id, $user_email, $activation_key );
		}
	}

	$bp->signup->username = $user_login;

	do_action( 'bp_core_signup_user', $user_id, $user_login, $user_password, $user_email, $usermeta );

	return $user_id;
}

function bp_core_signup_blog( $blog_domain, $blog_path, $blog_title, $user_name, $user_email, $usermeta ) {
	if ( !is_multisite() || !function_exists( 'wpmu_signup_blog' ) )
		return false;

	return apply_filters( 'bp_core_signup_blog', wpmu_signup_blog( $blog_domain, $blog_path, $blog_title, $user_name, $user_email, $usermeta ) );
}

function bp_core_activate_signup( $key ) {
	global $wpdb;

	$user = false;

	// Multisite installs have their own activation routine
	if ( is_multisite() ) {
		$user = wpmu_activate_signup( $key );

		// If there were errors, add a message and redirect
		if ( ! empty( $user->errors ) ) {
			return $user;
		}

		$user_id = $user['user_id'];

	} else {
		$signups = BP_Signup::get( array(
			'activation_key' => $key,
		) );

		if ( empty( $signups['signups'] ) ) {
			return new WP_Error( 'invalid_key', __( 'Invalid activation key.', 'buddypress' ) );
		}

		$signup = $signups['signups'][0];

		if ( $signup->active ) {
			if ( empty( $signup->domain ) ) {
				return new WP_Error( 'already_active', __( 'The user is already active.', 'buddypress' ), $signup );
			} else {
				return new WP_Error( 'already_active', __( 'The site is already active.', 'buddypress' ), $signup );
			}
		}

		// password is hashed again in wp_insert_user
		$password = wp_generate_password( 12, false );

		$user_id = username_exists( $signup->user_login );

		// Create the user
		if ( ! $user_id ) {
			$user_id = wp_create_user( $signup->user_login, $password, $signup->user_email );

		// If a user ID is found, this may be a legacy signup, or one
		// created locally for backward compatibility. Process it.
		} else if ( $key == wp_hash( $user_id ) ) {
			// Change the user's status so they become active
			if ( ! $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->users} SET user_status = 0 WHERE ID = %d", $user_id ) ) ) {
				return new WP_Error( 'invalid_key', __( 'Invalid activation key', 'buddypress' ) );
			}

			bp_delete_user_meta( $user_id, 'activation_key' );

			$member = get_userdata( $user_id );
			$member->set_role( get_option('default_role') );

			$user_already_created = true;

		} else {
			$user_already_exists = true;
		}

		if ( ! $user_id ) {
			return new WP_Error( 'create_user', __( 'Could not create user', 'buddypress' ), $signup );
		}

		// Fetch the signup so we have the data later on
		$signups = BP_Signup::get( array(
			'activation_key' => $key,
		) );

		$signup = isset( $signups['signups'] ) && ! empty( $signups['signups'][0] ) ? $signups['signups'][0] : false;

		// Activate the signup
		BP_Signup::validate( $key );

		if ( isset( $user_already_exists ) ) {
			return new WP_Error( 'user_already_exists', __( 'That username is already activated.', 'buddypress' ), $signup );
		}

		// Set up data to pass to the legacy filter
		$user = array(
			'user_id'  => $user_id,
			'password' => $signup->meta['password'],
			'meta'     => $signup->meta,
		);

		// Notify the site admin of a new user registration
		wp_new_user_notification( $user_id );

		if ( isset( $user_already_created ) ) {
			do_action( 'bp_core_activated_user', $user_id, $key, $user );
			return $user_id;
		}
	}

	// Set any profile data
	if ( bp_is_active( 'xprofile' ) ) {
		if ( ! empty( $user['meta']['profile_field_ids'] ) ) {
			$profile_field_ids = explode( ',', $user['meta']['profile_field_ids'] );

			foreach( (array) $profile_field_ids as $field_id ) {
				$current_field = isset( $user['meta']["field_{$field_id}"] ) ? $user['meta']["field_{$field_id}"] : false;

				if ( !empty( $current_field ) )
					xprofile_set_field_data( $field_id, $user_id, $current_field );

				// Save the visibility level
				$visibility_level = ! empty( $user['meta']['field_' . $field_id . '_visibility'] ) ? $user['meta']['field_' . $field_id . '_visibility'] : 'public';
				xprofile_set_field_visibility_level( $field_id, $user_id, $visibility_level );
			}
		}
	}

	// Update the display_name
	wp_update_user( array(
		'ID'           => $user_id,
		'display_name' => bp_core_get_user_displayname( $user_id ),
	) );

	// Set the password on multisite installs
	if ( ! empty( $user['meta']['password'] ) ) {
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->users} SET user_pass = %s WHERE ID = %d", $user['meta']['password'], $user_id ) );
	}

	do_action( 'bp_core_activated_user', $user_id, $key, $user );

	return $user_id;
}

/**
 * Migrate signups from pre-2.0 configuration to wp_signups.
 *
 * @since BuddyPress (2.0.1)
 */
function bp_members_migrate_signups() {
	global $wpdb;

	$status_2_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->users} WHERE user_status = '2'" );

	if ( ! empty( $status_2_ids ) ) {
		$signups = get_users( array(
			'fields'  => array(
				'ID',
				'user_login',
				'user_pass',
				'user_registered',
				'user_email',
				'display_name',
			),
			'include' => $status_2_ids,
		) );

		// Fetch activation keys separately, to avoid the all_with_meta
		// overhead
		$status_2_ids_sql = implode( ',', $status_2_ids );
		$ak_data = $wpdb->get_results( "SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'activation_key' AND user_id IN ({$status_2_ids_sql})" );

		// Rekey
		$activation_keys = array();
		foreach ( $ak_data as $ak_datum ) {
			$activation_keys[ intval( $ak_datum->user_id ) ] = $ak_datum->meta_value;
		}

		unset( $status_2_ids_sql, $status_2_ids, $ak_data );

		// Merge
		foreach ( $signups as &$signup ) {
			if ( isset( $activation_keys[ $signup->ID ] ) ) {
				$signup->activation_key = $activation_keys[ $signup->ID ];
			}
		}

		// Reset the signup var as we're using it to process the migration
		unset( $signup );

	} else {
		return;
	}

	foreach ( $signups as $signup ) {
		$meta = array();

		// Rebuild the activation key, if missing
		if ( empty( $signup->activation_key ) ) {
			$signup->activation_key = wp_hash( $signup->ID );
		}

		if ( bp_is_active( 'xprofile' ) ) {
			$meta['field_1'] = $signup->display_name;
		}

		$meta['password'] = $signup->user_pass;

		$user_login = preg_replace( '/\s+/', '', sanitize_user( $signup->user_login, true ) );
		$user_email = sanitize_email( $signup->user_email );

		BP_Signup::add( array(
			'user_login'     => $user_login,
			'user_email'     => $user_email,
			'registered'     => $signup->user_registered,
			'activation_key' => $signup->activation_key,
			'meta'           => $meta
		) );

		// Deleting these options will remove signups from users count
		delete_user_option( $signup->ID, 'capabilities' );
		delete_user_option( $signup->ID, 'user_level'   );
	}
}

function bp_core_new_user_activity( $user ) {
	if ( empty( $user ) || !bp_is_active( 'activity' ) )
		return false;

	if ( is_array( $user ) )
		$user_id = $user['user_id'];
	else
		$user_id = $user;

	if ( empty( $user_id ) )
		return false;

	$userlink = bp_core_get_userlink( $user_id );

	bp_activity_add( array(
		'user_id'   => $user_id,
		'component' => 'xprofile',
		'type'      => 'new_member'
	) );
}
add_action( 'bp_core_activated_user', 'bp_core_new_user_activity' );

function bp_core_map_user_registration( $user_id ) {

	// Only map data when the site admin is adding users, not on registration.
	if ( !is_admin() )
		return false;

	// Add the user's fullname to Xprofile
	if ( bp_is_active( 'xprofile' ) ) {
		$firstname = bp_get_user_meta( $user_id, 'first_name', true );
		$lastname = ' ' . bp_get_user_meta( $user_id, 'last_name', true );
		$name = $firstname . $lastname;

		if ( empty( $name ) || ' ' == $name )
			$name = bp_get_user_meta( $user_id, 'nickname', true );

		xprofile_set_field_data( 1, $user_id, $name );
	}
}
add_action( 'user_register', 'bp_core_map_user_registration' );

function bp_core_signup_avatar_upload_dir() {
	global $bp;

	if ( !$bp->signup->avatar_dir )
		return false;

	$path  = bp_core_avatar_upload_path() . '/avatars/signups/' . $bp->signup->avatar_dir;
	$newbdir = $path;

	if ( !file_exists( $path ) )
		@wp_mkdir_p( $path );

	$newurl = bp_core_avatar_url() . '/avatars/signups/' . $bp->signup->avatar_dir;
	$newburl = $newurl;
	$newsubdir = '/avatars/signups/' . $bp->signup->avatar_dir;

	return apply_filters( 'bp_core_signup_avatar_upload_dir', array( 'path' => $path, 'url' => $newurl, 'subdir' => $newsubdir, 'basedir' => $newbdir, 'baseurl' => $newburl, 'error' => false ) );
}

function bp_core_signup_send_validation_email( $user_id, $user_email, $key ) {
	$activate_url = bp_get_activation_page() ."?key=$key";
	$activate_url = esc_url( $activate_url );

	$message = sprintf( __( "Thanks for registering! To complete the activation of your account please click the following link:\n\n%1\$s\n\n", 'buddypress' ), $activate_url );
	$subject = bp_get_email_subject( array( 'text' => __( 'Activate Your Account', 'buddypress' ) ) );

	// Send the message
	$to      = apply_filters( 'bp_core_signup_send_validation_email_to',     $user_email, $user_id                );
	$subject = apply_filters( 'bp_core_signup_send_validation_email_subject', $subject,    $user_id                );
	$message = apply_filters( 'bp_core_signup_send_validation_email_message', $message,    $user_id, $activate_url );

	wp_mail( $to, $subject, $message );

	do_action( 'bp_core_sent_user_validation_email', $subject, $message, $user_id, $user_email, $key );
}

/**
 * Display a "resend email" link when an unregistered user attempts to log in.
 *
 * @param WP_User|WP_Error $user Either the WP_User or the WP_Error object
 * @param string $username The inputted, attempted username.
 * @param string $password The inputted, attempted password.
 * @return WP_User|WP_Error
 *
 * @since BuddyPress (1.2.2)
 */
function bp_core_signup_disable_inactive( $user = null, $username = '', $password ='' ) {
	// login form not used
	if ( empty( $username ) && empty( $password ) ) {
		return $user;
	}

	// An existing WP_User with a user_status of 2 is either a legacy
	// signup, or is a user created for backward compatibility. See
	// {@link bp_core_signup_user()} for more details.
	if ( is_a( $user, 'WP_User' ) && 2 == $user->user_status ) {
		$user_login = $user->user_login;

	// If no WP_User is found corresponding to the username, this
	// is a potential signup
	} elseif ( is_wp_error( $user ) && 'invalid_username' == $user->get_error_code() ) {
		$user_login = $username;

	// This is an activated user, so bail
	} else {
		return $user;
	}

	// Look for the unactivated signup corresponding to the login name
	$signup = BP_Signup::get( array( 'user_login' => sanitize_user( $user_login ) ) );

	// No signup or more than one, something is wrong. Let's bail.
	if ( empty( $signup['signups'][0] ) || $signup['total'] > 1 ) {
		return $user;
	}

	// Unactivated user account found!
	// Set up the feedback message
	$signup_id = $signup['signups'][0]->signup_id;

	$resend_url_params = array(
		'action' => 'bp-resend-activation',
		'id'     => $signup_id,
	);

	$resend_url = wp_nonce_url(
		add_query_arg( $resend_url_params, wp_login_url() ),
		'bp-resend-activation'
	);

	$resend_string = '<br /><br />' . sprintf( __( 'If you have not received an email yet, <a href="%s">click here to resend it</a>.', 'buddypress' ), $resend_url );

	return new WP_Error( 'bp_account_not_activated', __( '<strong>ERROR</strong>: Your account has not been activated. Check your email for the activation link.', 'buddypress' ) . $resend_string );
}
add_filter( 'authenticate', 'bp_core_signup_disable_inactive', 30, 3 );

/**
 * On the login screen, resends the activation email for a user.
 *
 * @since BuddyPress (2.0.0)
 *
 * @see bp_core_signup_disable_inactive()
 */
function bp_members_login_resend_activation_email() {
	global $error;

	if ( empty( $_GET['id'] ) || empty( $_GET['_wpnonce'] ) ) {
		return;
	}

	// verify nonce
	if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'bp-resend-activation' ) ) {
		die( 'Security check' );
	}

	$signup_id = (int) $_GET['id'];

	// resend the activation email
	// also updates the 'last sent' and '# of emails sent' values
	$resend = BP_Signup::resend( array( $signup_id ) );

	// add feedback message
	if ( ! empty( $resend['errors'] ) ) {
		$error = __( '<strong>ERROR</strong>: Your account has already been activated.', 'buddypress' );
	} else {
		$error = __( 'Activation email resent!  Please check your inbox or spam folder.', 'buddypress' );
	}
}
add_action( 'login_form_bp-resend-activation', 'bp_members_login_resend_activation_email' );

/**
 * Kill the wp-signup.php if custom registration signup templates are present
 */
function bp_core_wpsignup_redirect() {

	// Bail in admin or if custom signup page is broken
	if ( is_admin() || ! bp_has_custom_signup_page() )
		return;

	$action = !empty( $_GET['action'] ) ? $_GET['action'] : '';

	// Not at the WP core signup page and action is not register
	if ( ! empty( $_SERVER['SCRIPT_NAME'] ) && false === strpos( $_SERVER['SCRIPT_NAME'], 'wp-signup.php' ) && ( 'register' != $action ) )
		return;

	bp_core_redirect( bp_get_signup_page() );
}
add_action( 'bp_init', 'bp_core_wpsignup_redirect' );

/**
 * Stop a logged-in user who is marked as a spammer.
 *
 * When an admin marks a live user as a spammer, that user can still surf
 * around and cause havoc on the site until that person is logged out.
 *
 * This code checks to see if a logged-in user is marked as a spammer.  If so,
 * we redirect the user back to wp-login.php with the 'reauth' parameter.
 *
 * This clears the logged-in spammer's cookies and will ask the spammer to
 * reauthenticate.
 *
 * Note: A spammer cannot log back in - {@see bp_core_boot_spammer()}.
 *
 * Runs on 'bp_init' at priority 5 so the members component globals are setup
 * before we do our spammer checks.
 *
 * This is important as the $bp->loggedin_user object is setup at priority 4.
 *
 * @since BuddyPress (v1.8)
 */
function bp_stop_live_spammer() {
	// if we're on the login page, stop now to prevent redirect loop
	$is_login = false;
	if ( isset( $_GLOBALS['pagenow'] ) && false !== strpos( $GLOBALS['pagenow'], 'wp-login.php' ) ) {
		$is_login = true;
	} else if ( isset( $_SERVER['SCRIPT_NAME'] ) && false !== strpos( $_SERVER['SCRIPT_NAME'], 'wp-login.php' ) ) {
		$is_login = true;
	}

	if ( $is_login ) {
		return;
	}

	// user isn't logged in, so stop!
	if ( ! is_user_logged_in() ) {
		return;
	}

	// if spammer, redirect to wp-login.php and reauthorize
	if ( bp_is_user_spammer( bp_loggedin_user_id() ) ) {
		// setup login args
		$args = array(
			// custom action used to throw an error message
			'action' => 'bp-spam',

			// reauthorize user to login
			'reauth' => 1
		);

		// setup login URL
		$login_url = apply_filters( 'bp_live_spammer_redirect', add_query_arg( $args, wp_login_url() ) );

		// redirect user to login page
		wp_redirect( $login_url );
		die();
	}
}
add_action( 'bp_init', 'bp_stop_live_spammer', 5 );

/**
 * Show a custom error message when a logged-in user is marked as a spammer.
 *
 * @since BuddyPress (v1.8)
 */
function bp_live_spammer_login_error() {
	global $error;

	$error = __( '<strong>ERROR</strong>: Your account has been marked as a spammer.', 'buddypress' );

	// shake shake shake!
	add_action( 'login_head', 'wp_shake_js', 12 );
}
add_action( 'login_form_bp-spam', 'bp_live_spammer_login_error' );
