<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * filters the body class as admin can change the name of the plugin, we need a fixed value for styles
 *
 * @param array $classes (WordPress ones)
 * @param array $classes (Buddyress ones)
 * @uses bp_my_home_get_slug() to get plugin's slug
 */
function bp_my_home_the_body_class( $classes, $bp_classes ) {
	
	if( in_array( bp_my_home_get_slug(), $bp_classes ) )
		$classes[] = bp_my_home()->bp_my_home_slug;
		
	return $classes;
	
}

add_filter( 'bp_get_the_body_class', 'bp_my_home_the_body_class', 10, 2 );

/**
 * Filters the bpmh sidebars widgets to apply user's saved state
 *
 * @param array $sidebars_widgets 
 * @uses is_admin() to be sure we're not in backend
 * @uses bp_is_current_component() to check for my home component
 * @uses bp_my_home_get_slug() to get the slug of the plugin
 * @uses bp_is_current_action() to check current action is widget
 * @uses bp_my_home_get_widgets_slug() to get the my widgets slug
 * @uses bp_loggedin_user_id() to get current user's id
 * @uses get_user_meta() to get user's preferences
 * @return array the reorganized widgets
 */
function bpmh_filter_widget_display( $sidebars_widgets ) {
	
	if( !is_admin() && bp_is_current_component( bp_my_home_get_slug() ) && bp_is_current_action( bp_my_home_get_widgets_slug() ) ) {
		
		//now let's load user's preference !
		$user_id = bp_loggedin_user_id();
		$user_widgets_pref = get_user_meta( $user_id, '_bpmh_user_saved_state', true );
		$deactivated = get_user_meta( $user_id, 'bpmh_user_activated_widgets', true );
		if( empty( $deactivated ) )
			$deactivated =array();
		$temp_widgets = array();

		if( empty( $sidebars_widgets['bpmh-left-sidebar'] ) )
			$sidebars_widgets['bpmh-left-sidebar'] = array();

		if( empty( $sidebars_widgets['bpmh-right-sidebar'] ) )
			$sidebars_widgets['bpmh-right-sidebar'] = array();

		$is_active = array_merge( $sidebars_widgets['bpmh-left-sidebar'], $sidebars_widgets['bpmh-right-sidebar'] );
		
		if( empty( $user_widgets_pref ) || !is_array( $user_widgets_pref ) || count( $user_widgets_pref ) < 1 )
			return $sidebars_widgets;
		
		foreach( $user_widgets_pref as $key => $pref ){
			
			// Let's check the admin hasn't deactivate the widget
			if( in_array( $key, $is_active ) && !in_array( $key, $deactivated ) ) {
				
				if( $pref['col'] == 'column1' ) {
					$temp_widgets['bpmh-left-sidebar'][$pref['order']] = $key;
				} else {
					$temp_widgets['bpmh-right-sidebar'][$pref['order']] = $key;
				}
			}
			
		}
		
		if( !empty( $temp_widgets ) && is_array( $temp_widgets ) ) {
			
			// we unset the sidebar widgets
			unset( $sidebars_widgets['bpmh-left-sidebar'] );
			unset( $sidebars_widgets['bpmh-right-sidebar'] );
			
			// and populates it with our temp_widgets
			if( !empty( $temp_widgets['bpmh-left-sidebar'] ) )
				$sidebars_widgets['bpmh-left-sidebar'] = $temp_widgets['bpmh-left-sidebar'];
			if( !empty( $temp_widgets['bpmh-right-sidebar'] ) )
				$sidebars_widgets['bpmh-right-sidebar'] = $temp_widgets['bpmh-right-sidebar'];
		}
		
	}
	
	return $sidebars_widgets;
}

add_filter( 'sidebars_widgets', 'bpmh_filter_widget_display', 10, 1 );


/**
 * Adds an extra div to non bpmh widgets to apply a scroll
 *
 * @param array $params 
 * @return array the new $params
 */
function bpmh_not_bpmh_widget_extra_div( $params ) {
	if( in_array( $params[0]['id'], array( 'bpmh-left-sidebar', 'bpmh-right-sidebar' ) ) ) {

		if( strpos( $params[0]['widget_id'], 'bpmh_' ) !== 0 ) {
			$params[0]['after_title'] .= '<div id="not-bpmh-wigdet">';
			$params[0]['after_widget'] .= '</div>';
		}

	}

	return $params;
}

add_filter( 'dynamic_sidebar_params', 'bpmh_not_bpmh_widget_extra_div', 10, 1 );

/**
 * Filters the content to append the Bookmark button
 *
 * @param string $content 
 * @uses is_single() to check for single template
 * @uses bp_is_blog_page() to check we're on a blog page
 * @uses is_page() to check we're on a page template
 * @uses is_front_page() to check we're not on landing page
 * @uses is_user_logged_in() to check the user is logged in
 * @uses bp_my_home_user_activated_widget() to check the Bookmark widget is available for the user
 * @uses get_user_meta() to get user's bookmark list
 * @uses bp_loggedin_user_id() to get current user's id
 * @uses bpmh_is_in_array() to check if the link is not already in user's list
 * @uses esc_url_raw() to sanitize url
 * @uses get_permalink() to get the link of the displayed page
 * @uses esc_url() to sanitize url
 * @uses bp_my_home_get_user_home_link() to get user's bp my home page
 * @uses esc_attr() to sanitize the title
 * @uses get_the_title() to get the title of the displayed page
 * @uses wp_nonce_field() for security reasons
 * @return string html the content and the button
 */
function bpmh_bkmks_add_in_content( $content ) {
  	if( ( is_single() || ( bp_is_blog_page() && is_page() ) ) && !is_front_page() && is_user_logged_in() ){
    	
		if( !bp_my_home_user_activated_widget( 'BPMH_Widget_Bookmark' ) )
			return $content;
		
		$getuser_bkmks = get_user_meta( bp_loggedin_user_id(), 'bpmh_bkmks_list', true );
		
		if( empty( $getuser_bkmks ) )
			$getuser_bkmks = array();
			
		if( bpmh_is_in_array( esc_url_raw( get_permalink() ), $getuser_bkmks ) ) {
			
			$content .= '<a class="bpmh-add-bkmk" href="'. esc_url( bp_my_home_get_user_home_link() ) .'" title="'. __('Bookmarked', 'bp-my-home' ) .'"><span class="icon-bookmark"></span><span class="bpmh-message">'. __('Bookmarked', 'bp-my-home' ) .'</span></a>';
			
		} else {
			
			$content .= '<a class="bpmh-add-bkmk" href="#" data-bpmhurl="'. esc_url( get_permalink() ) .'" data-bpmhalias="'. esc_attr( get_the_title() ) .'" title="'. __('Bookmark', 'bp-my-home' ) .'"><span class="icon-bookmark"></span><span class="bpmh-message">'. __('Bookmark', 'bp-my-home' ) .'</span>'. wp_nonce_field( 'bpmh_bkmk', '_wpnoncebpmh_bkmk', true, false ).'</a>';
			
		}
	}
	
    return $content;
}

add_filter( 'the_content', 'bpmh_bkmks_add_in_content', 10, 1 );