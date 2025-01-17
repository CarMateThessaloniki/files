<?php
/**
 * BP Wall Filters
 *
 * @package BP-Wall
 */
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

//disable the multilevel commenting system of buddypress
add_filter('bp_activity_can_comment_reply','__return_false');

// this filter is replaced by bp_get_activity_action_pre_meta from 1.9.2
//add_filter('bp_get_activity_action', 'bp_wall_read_filter');
add_filter('bp_activity_after_save', 'bp_wall_input_filter');
//add_filter('bp_legacy_theme_ajax_querystring', 'bp_wall_qs_filter',  99, 2 );
add_filter('bp_ajax_querystring', 'bp_wall_qs_filter', 999);

add_filter('bp_get_activity_action_pre_meta', 'bp_wall_get_activity_action_pre_meta',10,3);


function bp_wall_get_activity_action_pre_meta( $action, $activity, $args ){
	$bp_wall_action = bp_activity_get_meta( $activity->id, 'bp_wall_action' );
	if ( isset($bp_wall_action) && !empty($bp_wall_action) ) {
		 $action = $bp_wall_action;
	}

	return $action;

}
/**
 * filters wall actions
 *
 */
function bp_wall_read_filter( $action ) {
	global $bp_wall, $activities_template;

	$current_activity_id = $activities_template->current_activity;
	$activity_id = $activities_template->activities[$current_activity_id]->id;
	//$bp_wall_action = bp_activity_get_meta( $activity_id, 'bp_wall_action' );
	return ($bp_wall_action) ? $bp_wall_action : $action;

}

/**
 * New Filter for activity stream
 *
 */
function bp_wall_input_filter( &$activity ) {
	global $bp, $bp_wall;
	
	$loggedin_user = $bp->loggedin_user;
	$displayed_user  = $bp->displayed_user;
	$new_action = false;

	// If we're on an activity page (loggedin_user's own profile or a friends), check for a target ID
	if ( $bp->current_action == 'just-me' && 
		( !isset($displayed_user->id) || 
		$displayed_user->id == 0 ) ) return;

	// It's either an @ mention, status update, or forum post.
	if ( ( $bp->current_action == 'just-me' && 
		   $loggedin_user->id == $displayed_user->id) || 
		   $bp->current_action == 'forum' ) {
		
		if ( !empty($activity->content) ) {
			$mentioned = bp_activity_find_mentions($activity->content);
			$uids = array();
			$usernames = array();
			
			// Get all the mentions and store valid usernames in a new array
			foreach( (array)$mentioned as $mention ) {
				if ( bp_is_username_compatibility_mode() )
					$user_id = username_exists( $mention );
				else
					$user_id = bp_core_get_userid_from_nicename( $mention );
	
				if ( empty( $user_id ) )
					continue;
				
				$uids[] = $user_id;
				$usernames[] = $mention;
			}
			
			$len = count($uids);
			$mentions_action = '';
			
			// It's mentioning one person
			if($len == 1) {
				$user_id = $displayed_user = bp_core_get_core_userdata( (int) $uids[0] );
				$user_url  = '<a href="'.$loggedin_user->domain.'">'.$loggedin_user->fullname.'</a>';
				$displayed_user_url  = '<a href="'.bp_core_get_userlink( $uids[0], false, true ).'">@'.$displayed_user->user_login.'</a>';

				$mentions_action = " mentioned ". $displayed_user_url;
			}
			
			// It's mentioning multiple people
			elseif($len > 1)
			{
				$user_url  = '<a href="'.$loggedin_user->domain.'">'.$loggedin_user->fullname.'</a>';
				$un = '@'.join(',@', $usernames);
				$mentions_action = $user_url. " mentioned " . $len . " people";
			}
			
			// If it's a forum post let's define some forum topic text
			if ( $bp->current_action == 'forum' )
			{
				$new_action = str_replace( ' replied to the forum topic', $mentions_action . ' in the forum topic', $activity->action);
			}
			
			// If it's a plublic message let's define that text as well
			elseif ( $len > 0 ) {
				$new_action = $user_url . $mentions_action .' in a public message';
			}
			
			// Otherwise it's a normal status update
			else {
				$new_action = false;
			}
			
		}
	}
	
	// It's a normal wall post because the displayed ID doesn't match the logged in ID
	// And we're on activity page
	elseif ( $bp->current_action == 'just-me' && 
		     $loggedin_user->id != $displayed_user->id ) {

		$user_url  = '<a href="'.$loggedin_user->domain.'">'.$loggedin_user->fullname.'</a>';
		$displayed_user_url  = '<a href="'.$displayed_user->domain.'">'.$displayed_user->fullname.'\'s</a>';

		// if a user is on his own page it is an update
		$new_action = sprintf( __( '%1$s wrote on %2$s Wall', 'bp-wall' ), $user_url, $displayed_user_url);

		//add a new notification if notifications component is active
		if ( bp_is_active( 'notifications' ) ) {
	        bp_notifications_add_notification( array(
	            'user_id'           => $displayed_user->id,
	            'item_id'           => $activity->id,
	            'secondary_item_id' => $loggedin_user->id,
	            'component_name'    => $bp->activity->id,
	            'component_action'  => 'new_at_wall',
	            'date_notified'     => bp_core_current_time(),
	            'is_new'            => 1,
	        ) );
	    }
	}
	
	if ( isset($new_action) && !empty($new_action) ) {
		bp_activity_update_meta( $activity->id, 'bp_wall_action', $new_action );
	}
}

/**
 * bp_ajax_querystring filter
 * 
 */ 
function bp_wall_qs_filter( $query_string ) {
	global $bp, $bp_wall;

	$action = $bp->current_action;


	// if we're on a different page than wall pass query_string as is
	if ( $action != "just-me" &&  $action != "news-feed" ) {
		return $query_string;
	}

	// if we have a page string in the query_string
	$page_str  = preg_match("/page=\d+/", $query_string, $m);
	// so grab the number
	$page = intval(str_replace("page=", "", $m[0])); 

	// load the activities for this page
	if ( $action == "just-me" )
		$activities = $bp_wall->get_timeline_activities($page); 
	elseif ( $action == "news-feed" )
		$activities = $bp_wall->get_newsfeed_activities($page); 

	$new_query_string = "include=$activities";
	return $new_query_string;
	
}


add_filter('bp_get_the_notification_description', 'bp_wall_get_the_notification_description');
function bp_wall_get_the_notification_description( $description ){
	global $bp;
	$notification = $bp->notifications->query_loop->notification;

	if ( $notification->component_name == $bp->activity->id && 
		 $notification->component_action == 'new_at_wall' ) {

		$user_id = $notification->user_id;
		$activity_id      = $notification->item_id;
		$poster_user_id   = $notification->secondary_item_id;
		$user_fullname = bp_core_get_user_displayname( $poster_user_id );
		//$at_wall_link  = bp_loggedin_user_domain() . bp_get_activity_slug() . '/p/' . $activity_id;
		$at_wall_link  = bp_displayed_user_domain() . bp_get_activity_slug() . '/p/' . $activity_id;

		$text = sprintf( __( '%s wrote on your wall', 'bp-wall' ), $user_fullname );
		$description = '<a href="' . esc_url( $at_wall_link ) . '">' . esc_html( $text ) . '</a>';

	}
	
	return $description;

}