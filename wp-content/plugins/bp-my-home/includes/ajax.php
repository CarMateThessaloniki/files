<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Save user's widget state
 *
 * It also includes widgets the user deactivated to be sure they show up if he changes his mind
 *
 * @uses wp_get_sidebars_widgets() to get the widgets
 * @uses bp_loggedin_user_id() to get current user's id
 * @uses update_user_meta() to store the state in user's preference
 * @return sting ok|not ok
 */
function bp_my_home_save_user_state(){

	$all_widgets = wp_get_sidebars_widgets();
	$array_parameters = array();
	
	$user_id = bp_loggedin_user_id();
	
	
	$user_state=$_POST['state'];
	$widget_state = explode( '[',$user_state );
	$column_indice = 0;
	
	for( $i=0; $i < count( $widget_state ) -1; $i++ ){
		$params = explode( "]", $widget_state[$i] );
		foreach( $params as $param ){
			$key_val_params = explode( "|", $param );
			if( $key_val_params[0] == "id" ) $idw = $key_val_params[1];
			if( $key_val_params[0] == "collapsed" ) $collapsed = $key_val_params[1];
			if( $key_val_params[0] == "order" ) $order = $key_val_params[1];
			if( $key_val_params[0] == "column" ) $column = $key_val_params[1];
				
			$column_indice += 1;
			
		}
		$array_parameters[$idw] = array( "col" => $column, "order" => $order, "collapsed" => $collapsed );
	}
	
	$bpmh_widgets = array_merge( $all_widgets['bpmh-left-sidebar'], $all_widgets['bpmh-right-sidebar'] );
	
	foreach( $bpmh_widgets as $widget ) {
		
		if( empty( $array_parameters[$widget] ) ) {
			$init_column = ( !empty( $all_widgets['bpmh-left-sidebar'] ) && in_array( $widget, $all_widgets['bpmh-left-sidebar'] ) ) ? 'column1' : 'column2';
			
			$array_parameters[$widget] = array( "col" => $init_column, "order" => $column_indice, "collapsed" => "0" );
		}	
		
	}
	
	if( update_user_meta( $user_id, '_bpmh_user_saved_state', $array_parameters ) ){
		echo "ok";
	}
	else _e('Oops, something went wrong !','bp-my-home');
	die();
}

add_action( 'wp_ajax_bp_my_home_save_state', 'bp_my_home_save_user_state' );


/** Ajax for widgets **/

/* bookmark */

/**
 * Adds a bookmark to user's list in case the Bookmark button is clicked
 *
 * @uses check_admin_referer() for security reasons
 * @uses bp_loggedin_user_id() to get current user's id
 * @uses get_user_meta() to get previously saved user's preference
 * @uses esc_html() to sanitize alias of the link
 * @uses esc_url_raw() to sanitize the link
 * @uses update_user_meta() to store the bookmark
 * @uses bp_my_home_get_user_home_link() to get user's bp my home link
 * @return string the json encoded result
 */
function bpmh_bookmark_ajax_add() {
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;
		
	check_admin_referer( 'bpmh_bkmk', '_wpnoncebpmh_bkmk' );
	
	$updated = false;
	$new_bookmark = $result = array();
	$user_id = bp_loggedin_user_id();
	
	$bpmh_bookmarks_list = get_user_meta( $user_id, 'bpmh_bkmks_list', true );
	
	if( !empty( $_POST['alias'] ) && !empty( $_POST['link'] ) ) {
		
		$new_bookmark[] = array( 'alias' => esc_html( $_POST['alias'] ), 'url' => esc_url_raw( $_POST['link'] ) );
		
		if( empty( $bpmh_bookmarks_list ) )
			$bpmh_bookmarks_list = $new_bookmark;
		else
			$bpmh_bookmarks_list = array_merge( $bpmh_bookmarks_list, $new_bookmark );
		
	}
	
	if( !empty( $bpmh_bookmarks_list ) && !empty( $new_bookmark ) ) {
		update_user_meta( $user_id, 'bpmh_bkmks_list', $bpmh_bookmarks_list );
		$updated = true;
	}
		
	
	$result['info'] = __( 'Error, try to refresh the page before trying again', 'bp-my-home' );
		
	if( !empty( $updated ) ) {
		$result['newlink'] = bp_my_home_get_user_home_link();
		$result['newtitle'] = __( 'View All Bookmarks', 'bp-my-home' );
		$result['info'] = __( 'Bookmarked', 'bp-my-home' );
	}
		
	exit( json_encode( $result ) );
}

add_action( 'wp_ajax_bpmh_bookmark_add', 'bpmh_bookmark_ajax_add' );

/* rss */

/**
 * Adds a feed to user's list
 *
 * @uses check_admin_referer() for security reasons
 * @uses bp_loggedin_user_id() to get current user's id
 * @uses get_user_meta() to get previously saved user's preference
 * @uses esc_html() to sanitize alias of the link
 * @uses esc_url_raw() to sanitize the link
 * @uses update_user_meta() to store the feed
 * @uses bp_my_home_get_user_home_link() to get user's bp my home link
 * @return string the json encoded result
 */
function bpmh_rss_ajax_add(){
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;
		
	check_admin_referer( 'bpmh_rss', '_wpnoncebpmh_rss' );
	
	$updated = false;
	$new_feed = $result = array();
	$user_id = bp_loggedin_user_id();
	
	$bpmh_feeds_list = get_user_meta( $user_id, 'bpmh_rss_feeds', true );
	
	if( !empty( $_POST['alias'] ) && !empty( $_POST['link'] ) ) {
		
		$new_feed[] = array( 'alias' => esc_html( $_POST['alias'] ), 'url' => esc_url_raw( $_POST['link'] ) );
		
		if( empty( $bpmh_feeds_list ) )
			$bpmh_feeds_list = $new_feed;
		else
			$bpmh_feeds_list = array_merge( $bpmh_feeds_list, $new_feed );
		
	}
	
	if( !empty( $bpmh_feeds_list ) ) {
		update_user_meta( $user_id, 'bpmh_rss_feeds', $bpmh_feeds_list );
		$updated = true;
	}
	
	$result['info'] = __( 'Error, try to refresh the page before trying again', 'bp-my-home' );
		
	if( !empty( $updated ) ) {
		$result['newlink'] = bp_my_home_get_user_home_link();
		$result['newtitle'] = __( 'View All Feeds', 'bp-my-home' );
		$result['info'] = __( 'Subscribed', 'bp-my-home' );
	}
		
	exit( json_encode( $result ) );
}

add_action( 'wp_ajax_bpmh_rss_add', 'bpmh_rss_ajax_add' );

/**
 * Loads a feed on feed selector change
 *
 * @uses bpmh_rss_grab_feed() to get the 5 latest items of the feed
 * @return html the list of items of the feed
 */
function bpmh_rss_reader_load_feed() {
	if( empty( $_POST['feed'] ) )
		exit( 0 );
	
	$feed = bpmh_rss_grab_feed( $_POST['feed'] );
	
	foreach( $feed as $item ) {
		echo $item;
	}
	die();
}

add_action( 'wp_ajax_bpmh_rss_refresh', 'bpmh_rss_reader_load_feed' );

/* comments */

/**
 * Refreshes the html of the follow up comments widget
 *
 * @uses bpmh_comments_display_comment_list() to list the comments for the selected blog
 */
function bpmh_rss_comments_load_comments() {
	if( empty( $_POST['blog_id'] ) )
		exit( 0 );
	
	bpmh_comments_display_comment_list();
	
	die();
}

add_action( 'wp_ajax_bpmh_comments_refresh', 'bpmh_rss_comments_load_comments' );

/* Notepad */

/**
 * Saves the notes taken by the user
 *
 * @uses bp_loggedin_user_id() to get current user's id
 * @uses esc_html() to sanitize the note
 * @uses update_user_meta() to save the note in user's preference
 * @return string the note taken by the user
 */
function bpmh_notepad_ajax_put_handle(){
	$user_id = bp_loggedin_user_id();
	$notes_filter = esc_html( $_POST['value'] );
	$notes_in_db = update_user_meta( $user_id, 'bpmh_notepad_notes', $notes_filter);
	$bpmh_note = nl2br( stripslashes( $notes_filter ) );

	exit( $bpmh_note );
}

add_action( 'wp_ajax_bpmh_notepad_ajax_put', 'bpmh_notepad_ajax_put_handle');

/**
 * Retrieves the notes taken by the user
 *
 * @uses bp_loggedin_user_id() to get current user's id
 * @uses get_user_meta() to get the notes in user's preference
 * @return string the notes taken by the user
 */
function bpmh_notepad_ajax_get_handle(){
	$user_id = bp_loggedin_user_id();
	$notes_in_db = get_user_meta( $user_id, 'bpmh_notepad_notes',true );
	$notes_in_db = str_replace( "&#039;", "'", htmlspecialchars_decode( $notes_in_db ) );
	
	exit( $notes_in_db );
}

add_action( 'wp_ajax_bpmh_notepad_ajax_get', 'bpmh_notepad_ajax_get_handle');