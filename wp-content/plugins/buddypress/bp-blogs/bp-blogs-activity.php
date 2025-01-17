<?php

/**
 * BuddyPress Blogs Activity.
 *
 * @package BuddyPress
 * @subpackage BlogsActivity
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Register activity actions for the blogs component.
 *
 * @since BuddyPress (1.0.0)
 *
 * @global object $bp The BuddyPress global settings object.
 *
 * @return bool|null Returns false if activity component is not active.
 */
function bp_blogs_register_activity_actions() {
	global $bp;

	// Bail if activity is not active
	if ( ! bp_is_active( 'activity' ) ) {
		return false;
	}

	if ( is_multisite() ) {
		bp_activity_set_action(
			$bp->blogs->id,
			'new_blog',
			__( 'New site created', 'buddypress' ),
			'bp_blogs_format_activity_action_new_blog'
		);
	}

	bp_activity_set_action(
		$bp->blogs->id,
		'new_blog_post',
		__( 'New post published', 'buddypress' ),
		'bp_blogs_format_activity_action_new_blog_post'
	);

	bp_activity_set_action(
		$bp->blogs->id,
		'new_blog_comment',
		__( 'New post comment posted', 'buddypress' ),
		'bp_blogs_format_activity_action_new_blog_comment'
	);

	do_action( 'bp_blogs_register_activity_actions' );
}
add_action( 'bp_register_activity_actions', 'bp_blogs_register_activity_actions' );

/**
 * Format 'new_blog' activity actions.
 *
 * @since BuddyPress (2.0.0)
 *
 * @param string $action Static activity action.
 * @param obj $activity Activity data object.
 */
function bp_blogs_format_activity_action_new_blog( $action, $activity ) {
	$blog_url  = bp_blogs_get_blogmeta( $activity->item_id, 'url' );
	$blog_name = bp_blogs_get_blogmeta( $activity->item_id, 'name' );

	$action = sprintf( __( '%s created the site %s', 'buddypress' ), bp_core_get_userlink( $activity->user_id ), '<a href="' . esc_url( $blog_url ) . '">' . esc_html( $blog_name ) . '</a>' );

	// Legacy filter - requires the BP_Blogs_Blog object
	if ( has_filter( 'bp_blogs_activity_created_blog_action' ) ) {
		$user_blog = BP_Blogs_Blog::get_user_blog( $activity->user_id, $activity->item_id );
		if ( $user_blog ) {
			$recorded_blog = new BP_Blogs_Blog( $user_blog );
		}

		if ( isset( $recorded_blog ) ) {
			$action = apply_filters( 'bp_blogs_activity_created_blog_action', $action, $recorded_blog, $blog_name, bp_blogs_get_blogmeta( $activity->item_id, 'description' ) );
		}
	}

	return apply_filters( 'bp_blogs_format_activity_action_new_blog', $action, $activity );
}

/**
 * Format 'new_blog_post' activity actions.
 *
 * @since BuddyPress (2.0.0)
 *
 * @param string $action Static activity action.
 * @param obj $activity Activity data object.
 */
function bp_blogs_format_activity_action_new_blog_post( $action, $activity ) {
	$blog_url  = bp_blogs_get_blogmeta( $activity->item_id, 'url' );
	$blog_name = bp_blogs_get_blogmeta( $activity->item_id, 'name' );

	if ( empty( $blog_url ) || empty( $blog_name ) ) {
		$blog_url  = get_home_url( $activity->item_id );
		$blog_name = get_blog_option( $activity->item_id, 'blogname' );

		bp_blogs_update_blogmeta( $activity->item_id, 'url', $blog_url );
		bp_blogs_update_blogmeta( $activity->item_id, 'name', $blog_name );
	}

	$post_url = add_query_arg( 'p', $activity->secondary_item_id, trailingslashit( $blog_url ) );

	$post_title = bp_activity_get_meta( $activity->id, 'post_title' );

	// Should only be empty at the time of post creation
	if ( empty( $post_title ) ) {
		switch_to_blog( $activity->item_id );

		$post = get_post( $activity->secondary_item_id );
		if ( is_a( $post, 'WP_Post' ) ) {
			$post_title = $post->post_title;
			bp_activity_update_meta( $activity->id, 'post_title', $post_title );
		}

		restore_current_blog();
	}

	$post_link  = '<a href="' . $post_url . '">' . $post_title . '</a>';

	$user_link = bp_core_get_userlink( $activity->user_id );

	if ( is_multisite() ) {
		$action  = sprintf( __( '%1$s wrote a new post, %2$s, on the site %3$s', 'buddypress' ), $user_link, $post_link, '<a href="' . esc_url( $blog_url ) . '">' . esc_html( $blog_name ) . '</a>' );
	} else {
		$action  = sprintf( __( '%1$s wrote a new post, %2$s', 'buddypress' ), $user_link, $post_link );
	}

	// Legacy filter - requires the post object
	if ( has_filter( 'bp_blogs_activity_new_post_action' ) ) {
		switch_to_blog( $activity->item_id );
		$post = get_post( $activity->secondary_item_id );
		restore_current_blog();

		if ( ! empty( $post ) && ! is_wp_error( $post ) ) {
			$action = apply_filters( 'bp_blogs_activity_new_post_action', $action, $post, $post_url );
		}
	}

	return apply_filters( 'bp_blogs_format_activity_action_new_blog_post', $action, $activity );
}

/**
 * Format 'new_blog_comment' activity actions.
 *
 * @since BuddyPress (2.0.0)
 *
 * @param string $action Static activity action.
 * @param obj $activity Activity data object.
 */
function bp_blogs_format_activity_action_new_blog_comment( $action, $activity ) {
	$blog_url  = bp_blogs_get_blogmeta( $activity->item_id, 'url' );
	$blog_name = bp_blogs_get_blogmeta( $activity->item_id, 'name' );

	if ( empty( $blog_url ) || empty( $blog_name ) ) {
		$blog_url  = get_home_url( $activity->item_id );
		$blog_name = get_blog_option( $activity->item_id, 'blogname' );

		bp_blogs_update_blogmeta( $activity->item_id, 'url', $blog_url );
		bp_blogs_update_blogmeta( $activity->item_id, 'name', $blog_name );
	}

	$post_url   = bp_activity_get_meta( $activity->id, 'post_url' );
	$post_title = bp_activity_get_meta( $activity->id, 'post_title' );

	// Should only be empty at the time of post creation
	if ( empty( $post_url ) || empty( $post_title ) ) {
		switch_to_blog( $activity->item_id );

		$comment = get_comment( $activity->secondary_item_id );

		if ( ! empty( $comment->comment_post_ID ) ) {
			$post_url = add_query_arg( 'p', $comment->comment_post_ID, trailingslashit( $blog_url ) );
			bp_activity_update_meta( $activity->id, 'post_url', $post_url );

			$post = get_post( $comment->comment_post_ID );

			if ( is_a( $post, 'WP_Post' ) ) {
				$post_title = $post->post_title;
				bp_activity_update_meta( $activity->id, 'post_title', $post_title );
			}
		}

		restore_current_blog();
	}

	$post_link = '<a href="' . $post_url . '">' . $post_title . '</a>';
	$user_link = bp_core_get_userlink( $activity->user_id );

	if ( is_multisite() ) {
		$action  = sprintf( __( '%1$s commented on the post, %2$s, on the site %3$s', 'buddypress' ), $user_link, $post_link, '<a href="' . esc_url( $blog_url ) . '">' . esc_html( $blog_name ) . '</a>' );
	} else {
		$action  = sprintf( __( '%1$s commented on the post, %2$s', 'buddypress' ), $user_link, $post_link );
	}

	// Legacy filter - requires the comment object
	if ( has_filter( 'bp_blogs_activity_new_comment_action' ) ) {
		switch_to_blog( $activity->item_id );
		$comment = get_comment( $activity->secondary_item_id );
		restore_current_blog();

		if ( ! empty( $comment ) && ! is_wp_error( $comment ) ) {
			$action = apply_filters( 'bp_blogs_activity_new_comment_action', $action, $comment, $post_url . '#' . $activity->secondary_item_id );
		}
	}

	return apply_filters( 'bp_blogs_format_activity_action_new_blog_comment', $action, $activity );
}

/**
 * Fetch data related to blogs at the beginning of an activity loop.
 *
 * This reduces database overhead during the activity loop.
 *
 * @since BuddyPress (2.0.0)
 *
 * @param array $activities Array of activity items.
 * @return array
 */
function bp_blogs_prefetch_activity_object_data( $activities ) {
	if ( empty( $activities ) ) {
		return $activities;
	}

	$blog_ids = array();

	foreach ( $activities as $activity ) {
		if ( buddypress()->blogs->id !== $activity->component ) {
			continue;
		}

		$blog_ids[] = $activity->item_id;
	}

	if ( ! empty( $blog_ids ) ) {
		bp_blogs_update_meta_cache( $blog_ids );
	}

	return $activities;
}
add_filter( 'bp_activity_prefetch_object_data', 'bp_blogs_prefetch_activity_object_data' );

/**
 * Record blog-related activity to the activity stream.
 *
 * @since BuddyPress (1.0.0)
 *
 * @see bp_activity_add() for description of parameters.
 * @global object $bp The BuddyPress global settings object.
 *
 * @param array $args {
 *     See {@link bp_activity_add()} for complete description of arguments.
 *     The arguments listed here have different default values from
 *     bp_activity_add().
 *     @type string $component Default: 'blogs'.
 * }
 * @return int|bool On success, returns the activity ID. False on failure.
 */
function bp_blogs_record_activity( $args = '' ) {
	global $bp;

	// Bail if activity is not active
	if ( ! bp_is_active( 'activity' ) )
		return false;

	$defaults = array(
		'user_id'           => bp_loggedin_user_id(),
		'action'            => '',
		'content'           => '',
		'primary_link'      => '',
		'component'         => $bp->blogs->id,
		'type'              => false,
		'item_id'           => false,
		'secondary_item_id' => false,
		'recorded_time'     => bp_core_current_time(),
		'hide_sitewide'     => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	// Remove large images and replace them with just one image thumbnail
 	if ( !empty( $content ) )
		$content = bp_activity_thumbnail_content_images( $content, $primary_link, $r );

	if ( !empty( $action ) )
		$action = apply_filters( 'bp_blogs_record_activity_action', $action );

	if ( !empty( $content ) )
		$content = apply_filters( 'bp_blogs_record_activity_content', bp_create_excerpt( $content ), $content );

	// Check for an existing entry and update if one exists.
	$id = bp_activity_get_activity_id( array(
		'user_id'           => $user_id,
		'component'         => $component,
		'type'              => $type,
		'item_id'           => $item_id,
		'secondary_item_id' => $secondary_item_id
	) );

	return bp_activity_add( array( 'id' => $id, 'user_id' => $user_id, 'action' => $action, 'content' => $content, 'primary_link' => $primary_link, 'component' => $component, 'type' => $type, 'item_id' => $item_id, 'secondary_item_id' => $secondary_item_id, 'recorded_time' => $recorded_time, 'hide_sitewide' => $hide_sitewide ) );
}

/**
 * Delete a blog-related activity stream item.
 *
 * @since BuddyPress (1.0.0)
 *
 * @see bp_activity_delete() for description of parameters.
 * @global object $bp The BuddyPress global settings object.
 *
 * @param array $args {
 *     See {@link bp_activity_delete()} for complete description of arguments.
 *     The arguments listed here have different default values from
 *     bp_activity_add().
 *     @type string $component Default: 'blogs'.
 * }
 * @return bool True on success, false on failure.
 */
function bp_blogs_delete_activity( $args = true ) {
	global $bp;

	// Bail if activity is not active
	if ( ! bp_is_active( 'activity' ) )
		return false;

	$defaults = array(
		'item_id'           => false,
		'component'         => $bp->blogs->id,
		'type'              => false,
		'user_id'           => false,
		'secondary_item_id' => false
	);

	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );

	bp_activity_delete_by_item_id( array(
		'item_id'           => $item_id,
		'component'         => $component,
		'type'              => $type,
		'user_id'           => $user_id,
		'secondary_item_id' => $secondary_item_id
	) );
}

/**
 * Check if a blog post's activity item should be closed from commenting.
 *
 * This mirrors the {@link comments_open()} and {@link _close_comments_for_old_post()}
 * functions, but for use with the BuddyPress activity stream to be as
 * lightweight as possible.
 *
 * By lightweight, we actually mirror a few of the blog's commenting settings
 * to blogmeta and checks the values in blogmeta instead.  This is to prevent
 * multiple {@link switch_to_blog()} calls in the activity stream.
 *
 * @since BuddyPress (2.0.0)
 *
 * @param object $activity The BP_Activity_Activity object
 * @return bool
 */
function bp_blogs_comments_open( $activity ) {
	$open = true;

	$blog_id = $activity->item_id;

	// see if we've mirrored the close comments option before
	$days_old = bp_blogs_get_blogmeta( $blog_id, 'close_comments_days_old' );

	// we've never cached these items before, so do it now
	if ( '' === $days_old ) {
		switch_to_blog( $blog_id );

		// use comments_open()
		remove_filter( 'comments_open', 'bp_comments_open', 10, 2 );
		$open = comments_open( $activity->secondary_item_id );
		add_filter( 'comments_open', 'bp_comments_open', 10, 2 );

		// might as well mirror values to blogmeta since we're here!
		$thread_depth = get_option( 'thread_comments' );
		if ( ! empty( $thread_depth ) ) {
			$thread_depth = get_option( 'thread_comments_depth' );
		} else {
			// perhaps filter this?
			$thread_depth = 1;
		}

		bp_blogs_update_blogmeta( $blog_id, 'close_comments_for_old_posts', get_option( 'close_comments_for_old_posts' ) );
		bp_blogs_update_blogmeta( $blog_id, 'close_comments_days_old',      get_option( 'close_comments_days_old' ) );
		bp_blogs_update_blogmeta( $blog_id, 'thread_comments_depth',        $thread_depth );

		restore_current_blog();

	// check blogmeta and manually check activity item
	// basically a copy of _close_comments_for_old_post()
	} else {

		// comments are closed
		if ( 'closed' == bp_activity_get_meta( $activity->id, 'post_comment_status' ) ) {
			return false;
		}

		if ( ! bp_blogs_get_blogmeta( $blog_id, 'close_comments_for_old_posts' ) ) {
			return $open;
		}

		$days_old = (int) $days_old;
		if ( ! $days_old ) {
			return $open;
		}

		/* commenting out for now - needs some more thought...
		   should we add the post type to activity meta?

		$post = get_post($post_id);

		// This filter is documented in wp-includes/comment.php
		$post_types = apply_filters( 'close_comments_for_post_types', array( 'post' ) );
		if ( ! in_array( $post->post_type, $post_types ) )
			return $open;
		*/

		if ( time() - strtotime( $activity->date_recorded ) > ( $days_old * DAY_IN_SECONDS ) ) {
			return false;
		}

		return $open;
	}

	return $open;
}

/** POST COMMENT SYNCHRONIZATION ****************************************/

/**
 * Syncs activity comments and posts them back as blog comments.
 *
 * Note: This is only a one-way sync - activity comments -> blog comment.
 *
 * For blog post -> activity comment, see {@link bp_blogs_record_comment()}.
 *
 * @since BuddyPress (2.0.0)
 *
 * @param int $comment_id The activity ID for the posted activity comment.
 * @param array $params Parameters for the activity comment.
 * @param object Parameters of the parent activity item (in this case, the blog post).
 */
function bp_blogs_sync_add_from_activity_comment( $comment_id, $params, $parent_activity ) {
	// if parent activity isn't a blog post, stop now!
	if ( $parent_activity->type != 'new_blog_post' ) {
		return;
	}

	// if activity comments are disabled for blog posts, stop now!
	if ( bp_disable_blogforum_comments() ) {
		return;
	}

	// get userdata
	if ( $params['user_id'] == bp_loggedin_user_id() ) {
		$user = buddypress()->loggedin_user->userdata;
	} else {
		$user = bp_core_get_core_userdata( $params['user_id'] );
	}

	// see if a parent WP comment ID exists
	if ( ! empty( $params['parent_id'] ) ) {
		$comment_parent = bp_activity_get_meta( $params['parent_id'], 'bp_blogs_post_comment_id' );
	} else {
		$comment_parent = 0;
	}

	// comment args
	$args = array(
		'comment_post_ID'      => $parent_activity->secondary_item_id,
		'comment_author'       => bp_core_get_user_displayname( $params['user_id'] ),
		'comment_author_email' => $user->user_email,
		'comment_author_url'   => bp_core_get_user_domain( $params['user_id'], $user->user_nicename, $user->user_login ),
		'comment_content'      => $params['content'],
		'comment_type'         => '', // could be interesting to add 'buddypress' here...
		'comment_parent'       => (int) $comment_parent,
		'user_id'              => $params['user_id'],

		// commenting these out for now
		//'comment_author_IP'    => '127.0.0.1',
		//'comment_agent'        => '',

		'comment_approved'     => 1
	);

	// prevent separate activity entry being made
	remove_action( 'comment_post', 'bp_blogs_record_comment', 10, 2 );

	// handle multisite
	switch_to_blog( $parent_activity->item_id );

	// handle timestamps for the WP comment after we've switched to the blog
	$args['comment_date']     = current_time( 'mysql' );
	$args['comment_date_gmt'] = current_time( 'mysql', 1 );

	// post the comment
	$post_comment_id = wp_insert_comment( $args );

	// add meta to comment
	add_comment_meta( $post_comment_id, 'bp_activity_comment_id', $comment_id );

	// add meta to activity comment
	bp_activity_update_meta( $comment_id, 'bp_blogs_post_comment_id', $post_comment_id );

	// resave activity comment with WP comment permalink
	//
	// in bp_blogs_activity_comment_permalink(), we change activity comment
	// permalinks to use the post comment link
	//
	// @todo since this is done after AJAX posting, the activity comment permalink
	//       doesn't change on the frontend until the next page refresh.
	$resave_activity = new BP_Activity_Activity( $comment_id );
	$resave_activity->primary_link = get_comment_link( $post_comment_id );
	$resave_activity->save();

	// multisite again!
	restore_current_blog();

	// add the comment hook back
	add_action( 'comment_post', 'bp_blogs_record_comment', 10, 2 );

	do_action( 'bp_blogs_sync_add_from_activity_comment', $comment_id, $args, $parent_activity, $user );
}
add_action( 'bp_activity_comment_posted', 'bp_blogs_sync_add_from_activity_comment', 10, 3 );

/**
 * Deletes the blog comment when the associated activity comment is deleted.
 *
 * Note: This is hooked on the 'bp_activity_delete_comment_pre' filter instead
 * of the 'bp_activity_delete_comment' action because we need to fetch the
 * activity comment children before they are deleted.
 *
 * @since BuddyPress (2.0.0)
 *
 * @param bool $retval
 * @param int $parent_activity_id The parent activity ID for the activity comment.
 * @param int $activity_id The activity ID for the pending deleted activity comment.
 */
function bp_blogs_sync_delete_from_activity_comment( $retval, $parent_activity_id, $activity_id ) {
	// check if parent activity is a blog post
	$parent_activity = new BP_Activity_Activity( $parent_activity_id );
	if ( 'new_blog_post' != $parent_activity->type ) {
		return $retval;
	}

	// fetch the activity comments for the activity item
	$activity = bp_activity_get( array(
		'in'               => $activity_id,
		'display_comments' => 'stream',
	) );

	// get all activity comment IDs for the pending deleted item
	$activity_ids   = bp_activity_recurse_comments_activity_ids( $activity );
	$activity_ids[] = $activity_id;

	// handle multisite
	// switch to the blog where the comment was made
	switch_to_blog( $parent_activity->item_id );

	// remove associated blog comments
	bp_blogs_remove_associated_blog_comments( $activity_ids, current_user_can( 'moderate_comments' ) );

	// multisite again!
	restore_current_blog();

	// rebuild activity comment tree
	// emulate bp_activity_delete_comment()
	BP_Activity_Activity::rebuild_activity_comment_tree( $parent_activity_id );

	// we're overriding the default bp_activity_delete_comment() functionality
	// so we need to return false
	return false;
}
add_filter( 'bp_activity_delete_comment_pre', 'bp_blogs_sync_delete_from_activity_comment', 10, 3 );

/**
 * Updates the blog comment when the associated activity comment is edited.
 *
 * @since BuddyPress (2.0.0)
 *
 * @param BP_Activity_Activity $activity The activity object.
 */
function bp_blogs_sync_activity_edit_to_post_comment( BP_Activity_Activity $activity ) {
	// not an activity comment? stop now!
	if ( 'activity_comment' !== $activity->type ) {
		return;
	}

	// this is a new entry, so stop!
	// we only want edits!
	if ( empty( $activity->id ) ) {
		return;
	}

	// prevent recursion
	remove_action( 'bp_activity_before_save', 'bp_blogs_sync_activity_edit_to_post_comment', 20 );

	// Try to see if a corresponding blog comment exists
	$post_comment_id = bp_activity_get_meta( $activity->id, 'bp_blogs_post_comment_id' );

	if ( empty( $post_comment_id ) ) {
		return;
	}

	// fetch parent activity item
	$parent_activity = new BP_Activity_Activity( $activity->item_id );

	// sanity check
	if ( 'new_blog_post' !== $parent_activity->type ) {
		return;
	}

	// handle multisite
	switch_to_blog( $parent_activity->item_id );

	// update the blog post comment
	wp_update_comment( array(
		'comment_ID'      => $post_comment_id,
		'comment_content' => $activity->content
	) );

	restore_current_blog();
}
add_action( 'bp_activity_before_save', 'bp_blogs_sync_activity_edit_to_post_comment', 20 );

/**
 * When a post is trashed, remove each comment's associated activity meta.
 *
 * When a post is trashed and later untrashed, we currently don't reinstate
 * activity items for these comments since their activity entries are already
 * deleted when initially trashed.
 *
 * Since these activity entries are deleted, we need to remove the deleted
 * activity comment IDs from each comment's meta when a post is trashed.
 *
 * @since BuddyPress (2.0.0)
 *
 * @param int $post_id The post ID
 * @param array $statuses Array of comment statuses. The key is comment ID, the
 *        value is the $comment->comment_approved value.
 */
function bp_blogs_remove_activity_meta_for_trashed_comments( $post_id, $statuses ) {
	foreach ( $statuses as $comment_id => $comment_approved ) {
		delete_comment_meta( $comment_id, 'bp_activity_comment_id' );
	}
}
add_action( 'trashed_post_comments', 'bp_blogs_remove_activity_meta_for_trashed_comments', 10, 2 );

/**
 * Utility function to set up some variables for use in the activity loop.
 *
 * Grabs the blog's comment depth and the post's open comment status options
 * for later use in the activity and activity comment loops.
 *
 * This is to prevent having to requery these items later on.
 *
 * @since BuddyPress (2.0.0)
 *
 * @see bp_blogs_disable_activity_commenting()
 * @see bp_blogs_setup_comment_loop_globals_on_ajax()
 *
 * @param object $activity The BP_Activity_Activity object
 */
function bp_blogs_setup_activity_loop_globals( $activity ) {
	if ( ! is_object( $activity ) ) {
		return;
	}

	// parent not a blog post? stop now!
	if ( 'new_blog_post' !== $activity->type ) {
		return;
	}

	if ( empty( $activity->id ) ) {
		return;
	}

	// if we've already done this before, stop now!
	if ( isset( buddypress()->blogs->allow_comments[ $activity->id ] ) ) {
		return;
	}

	$allow_comments = bp_blogs_comments_open( $activity );
	$thread_depth   = bp_blogs_get_blogmeta( $activity->item_id, 'thread_comments_depth' );

	// initialize a local object so we won't have to query this again in the
	// comment loop
	if ( empty( buddypress()->blogs->allow_comments ) ) {
		buddypress()->blogs->allow_comments = array();
	}
	if ( empty( buddypress()->blogs->thread_depth ) ) {
		buddypress()->blogs->thread_depth   = array();
	}

	// cache comment settings in the buddypress() singleton to reference later in
	// the activity comment loop
	// @see bp_blogs_disable_activity_replies()
	//
	// thread_depth is keyed by activity ID instead of blog ID because when we're
	// in a comment loop, we don't have access to the blog ID...
	// should probably object cache these values instead...
	buddypress()->blogs->allow_comments[ $activity->id ] = $allow_comments;
	buddypress()->blogs->thread_depth[ $activity->id ]   = $thread_depth;
}

/**
 * Set up some globals used in the activity comment loop when AJAX is used.
 *
 * @since BuddyPress (2.0.0)
 *
 * @see bp_blogs_setup_activity_loop_globals()
 */
function bp_blogs_setup_comment_loop_globals_on_ajax() {
	// not AJAX? stop now!
	if ( ! defined( 'DOING_AJAX' ) ) {
		return;
	}
	if ( false === (bool) constant( 'DOING_AJAX' ) ) {
		return;
	}

	// get the parent activity item
	$comment         = bp_activity_current_comment();
	$parent_activity = new BP_Activity_Activity( $comment->item_id );

	// setup the globals
	bp_blogs_setup_activity_loop_globals( $parent_activity );
}
add_action( 'bp_before_activity_comment', 'bp_blogs_setup_comment_loop_globals_on_ajax' );

/**
 * Disable activity commenting for blog posts based on certain criteria.
 *
 * If activity commenting is enabled for blog posts, we still need to disable
 * commenting if:
 *  - comments are disabled for the WP blog post from the admin dashboard
 *  - the WP blog post is supposed to be automatically closed from comments
 *    based on a certain age
 *  - the activity entry is a 'new_blog_comment' type
 *
 * @since BuddyPress (2.0.0)
 *
 * @param bool $retval Is activity commenting enabled for this activity entry?
 * @return bool
 */
function bp_blogs_disable_activity_commenting( $retval ) {
	// if activity commenting is disabled, return current value
	if ( bp_disable_blogforum_comments() ) {
		return $retval;
	}

	// activity commenting is enabled for blog posts
	switch ( bp_get_activity_action_name() ) {

		// we still have to disable activity commenting for 'new_blog_comment' items
		// commenting should only be done on the parent 'new_blog_post' item
		case 'new_blog_comment' :
			$retval = false;

			break;

		// check if commenting is disabled for the WP blog post
		// we should extrapolate this and automate this for plugins... or not
		case 'new_blog_post' :
			global $activities_template;

			// setup some globals we'll need to reference later
			bp_blogs_setup_activity_loop_globals( $activities_template->activity );

			// if comments are closed for the WP blog post, we should disable
			// activity comments for this activity entry
			if ( empty( buddypress()->blogs->allow_comments[bp_get_activity_id()] ) ) {
				$retval = false;
			}

			break;
	}

	return $retval;
}
add_filter( 'bp_activity_can_comment', 'bp_blogs_disable_activity_commenting' );

/**
 * Check if an activity comment associated with a blog post can be replied to.
 *
 * By default, disables replying to activity comments if the corresponding WP
 * blog post no longer accepts comments.
 *
 * This check uses a locally-cached value set in {@link bp_blogs_disable_activity_commenting()}
 * via {@link bp_blogs_setup_activity_loop_globals()}.
 *
 * @since BuddyPress (2.0.0)
 *
 * @param bool $retval Are replies allowed for this activity reply?
 * @param object $comment The activity comment object
 * @return bool
 */
function bp_blogs_can_comment_reply( $retval, $comment ) {
	if ( is_array( $comment ) ) {
		$comment = (object) $comment;
	}

	// check comment depth and disable if depth is too large
	if ( isset( buddypress()->blogs->thread_depth[$comment->item_id] ) ){
		if ( $comment->mptt_left > buddypress()->blogs->thread_depth[$comment->item_id] ) {
			$retval = false;
		}
	}

	// check if we should disable activity replies based on the parent activity
	if ( isset( buddypress()->blogs->allow_comments[$comment->item_id] ) ){
		// the blog post has closed off commenting, so we should disable all activity
		// comments under the parent 'new_blog_post' activity entry
		if ( empty( buddypress()->blogs->allow_comments[$comment->item_id] ) ) {
			$retval = false;
		}
	}

	return $retval;
}
add_filter( 'bp_activity_can_comment_reply', 'bp_blogs_can_comment_reply', 10, 2 );

/**
 * Changes activity comment permalinks to use the blog comment permalink
 * instead of the activity permalink.
 *
 * This is only done if activity commenting is allowed and whether the parent
 * activity item is a 'new_blog_post' entry.
 *
 * @since BuddyPress (2.0.0)
 *
 * @param string $retval The activity comment permalink
 * @return string
 */
function bp_blogs_activity_comment_permalink( $retval ) {
	global $activities_template;

	if ( isset( buddypress()->blogs->allow_comments[$activities_template->activity->current_comment->item_id] ) ){
		$retval = $activities_template->activity->current_comment->primary_link;
	}

	return $retval;
}
add_filter( 'bp_get_activity_comment_permalink', 'bp_blogs_activity_comment_permalink' );

/**
 * Changes single activity comment entries to use the blog comment permalink.
 *
 * This is only done if the activity comment is associated with a blog comment.
 *
 * @since BuddyPress (2.0.1)
 *
 * @param string $retval The activity permalink
 * @param BP_Activity_Activity $activity
 * @return string
 */
function bp_blogs_activity_comment_single_permalink( $retval, $activity ) {
	if ( 'activity_comment' !== $activity->type ) {
		return $retval;
	}

	$blog_comment_id = bp_activity_get_meta( $activity->id, 'bp_blogs_post_comment_id' );

	if ( ! empty( $blog_comment_id ) ) {
		$retval = $activity->primary_link;
	}

	return $retval;
}
add_filter( 'bp_activity_get_permalink', 'bp_blogs_activity_comment_single_permalink', 10, 2 );

/**
 * Formats single activity comment entries to use the blog comment action.
 *
 * This is only done if the activity comment is associated with a blog comment.
 *
 * @since BuddyPress (2.0.1)
 *
 * @param string $retval The activity action
 * @param BP_Activity_Activity $activity
 * @return string
 */
function bp_blogs_activity_comment_single_action( $retval, $activity ) {
	if ( 'activity_comment' !== $activity->type ) {
		return $retval;
	}

	$blog_comment_id = bp_activity_get_meta( $activity->id, 'bp_blogs_post_comment_id' );

	if ( ! empty( $blog_comment_id ) ) {
		// fetch the parent blog post activity item
		$parent_blog_post_activity = new BP_Activity_Activity( $activity->item_id );

		// fake a 'new_blog_comment' activity object
		$object = $activity;

		// override 'item_id' to use blog ID
		$object->item_id = $parent_blog_post_activity->item_id;

		// override 'secondary_item_id' to use comment ID
		$object->secondary_item_id = $blog_comment_id;

		// now format the activity action using the 'new_blog_comment' action callback
		$retval = bp_blogs_format_activity_action_new_blog_comment( '', $object );
	}

	return $retval;
}
add_filter( 'bp_get_activity_action_pre_meta', 'bp_blogs_activity_comment_single_action', 10, 2 );
