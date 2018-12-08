<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Redirects user in case he's not logged in and tries to directly access to my home
 *
 * @uses bp_my_home_current_is_widgets() to check we're on widgets page
 * @uses bp_my_home_current_is_settings() to check we're on user's settings page
 * @uses bp_my_home_current_is_widget_settings() to check we're on widget's settings page
 * @uses is_user_logged_in() to check user's in loggedin
 * @uses bp_core_no_access() to ask the user to logg in and redirect him back
 * @uses bp_is_my_profile() to check current user is on his profile
 * @uses bp_displayed_user_domain() to get user's profile page
 */
function bp_my_home_redirect_if_no_access() {
	if( bp_my_home_current_is_widgets() || bp_my_home_current_is_settings() || bp_my_home_current_is_widget_settings() ) {
		if( !is_user_logged_in() ) {
			
			bp_core_no_access();
			
		} elseif ( !bp_is_my_profile() ) {
			
			bp_core_no_access( array(
				'message'  => __( 'You do not have access to this page.', 'bp-my-home' ),
				'root'     => bp_displayed_user_domain(),
				'redirect' => false
			) );
		
		}
	}
}

add_action( 'bpmh_actions', 'bp_my_home_redirect_if_no_access', 1 );

/**
 * Saves user's setting and eventually refreshed the states of widgets
 *
 * @uses is_user_logged_in() to check user's in loggedin
 * @uses bp_my_home_current_is_settings() to check we're on user's settings page
 * @uses check_admin_referer() for security reasons
 * @uses wp_get_sidebars_widgets() to get the widgets
 * @uses bp_loggedin_user_id() to get current user's id
 * @uses get_user_meta() to get user's preference
 * @uses update_user_meta() to save user's preference
 * @uses delete_user_meta() to remove user's preference
 * @uses bp_core_add_message() to add a notice to inform user
 * @uses bp_core_redirect() to redirect user's and avoids double posting
 * @uses bp_my_home_get_user_settings_link() to get user's my home setting link
 */
function bp_my_home_save_user_widgets() {
	
	if( !is_user_logged_in() )
		return false;
	
	if( bp_my_home_current_is_settings() ) {
		
		if( isset( $_POST['_bpmh_user_settings']['save'] ) ) {
			
			// Check the nonce
			check_admin_referer( 'bpmh_user_options', '_wpnonce_bpmh_user_options' );	
			
			$all_widgets = wp_get_sidebars_widgets();
			$params = $user_widgets_pref = array();

			$bpmh_widgets = array_merge( $all_widgets['bpmh-left-sidebar'], $all_widgets['bpmh-right-sidebar'] );

			$user_id = bp_loggedin_user_id();
			
			if( !empty( $_POST['_bpmh_user_settings']['widgets'] ) ) {
				
				$user_choice = array_map( 'sanitize_text_field', $_POST['_bpmh_user_settings']['widgets'] );
				
				$user_widgets_pref = get_user_meta( $user_id, '_bpmh_user_saved_state', true );
				
				if( empty( $user_widgets_pref ) )
					$user_widgets_pref = array();
				
				$check_user_pref = array_keys( $user_widgets_pref );
				$user_pref_not_set = array_diff( $user_choice, $check_user_pref );
				
				if( !empty( $user_pref_not_set ) && count( $user_pref_not_set ) > 0 ) {
					if( empty( $user_widgets_pref ) ) {
						foreach( $user_pref_not_set as $not_set ) {
							if( in_array( $not_set, $all_widgets['bpmh-left-sidebar'] ) ){
								$column = 'column1';
								$find_order = array_flip( $all_widgets['bpmh-left-sidebar'] );
								$order = (string) $find_order[$not_set];
							} elseif( in_array( $not_set, $all_widgets['bpmh-right-sidebar'] ) ) {
								$column = 'column2';
								$find_order = array_flip( $all_widgets['bpmh-right-sidebar'] );
								$order = (string) $find_order[$not_set];
							}
							
							$user_widgets_pref[$not_set] = array( "col" => $column, "order" => $order, "collapsed" => '0' );
						}
						
						update_user_meta( $user_id, '_bpmh_user_saved_state', $user_widgets_pref );
					} else {
						foreach( $user_pref_not_set as $not_set ) {
							$column = in_array( $not_set, $all_widgets['bpmh-left-sidebar'] ) ? 'column1' : 'column2';
							$order = 0;
							foreach( $user_widgets_pref as $set ) {
								if( !empty( $set['col'] ) && $set['col'] == $column )
									$order += intval( $set['order'] );
							}
							$order += 1;
							$user_widgets_pref[$not_set] = array( "col" => $column, "order" => (string) $order, "collapsed" => '0' );
						}
						update_user_meta( $user_id, '_bpmh_user_saved_state', $user_widgets_pref );
					}
				}
					
				$deactivated_widgets = array_diff( $bpmh_widgets, $user_choice );
				
				update_user_meta( $user_id, 'bpmh_user_activated_widgets', $deactivated_widgets );
			} else {
				if( !empty( $_POST['_bpmh_user_settings']['sidebar'] ) )
					delete_user_meta( $user_id, 'bpmh_user_activated_widgets' );
			}
			
			$user_home_pref = get_user_meta( $user_id, 'bpmh_user_home_page', true );
			
			if( !empty( $_POST['_bpmh_user_settings']['homepage'] ) ) {
				update_user_meta( $user_id, 'bpmh_user_home_page', intval( $_POST['_bpmh_user_settings']['homepage'] ) );
			} elseif( !empty( $user_home_pref ) ) {
				delete_user_meta( $user_id, 'bpmh_user_home_page' );
			}
			
			// let's finally redirect
			bp_core_add_message( __( 'Settings saved successfully', 'bp-my-home' ) );
			bp_core_redirect( bp_my_home_get_user_settings_link() );
		}
	}
}

add_action( 'bpmh_actions', 'bp_my_home_save_user_widgets', 10 );