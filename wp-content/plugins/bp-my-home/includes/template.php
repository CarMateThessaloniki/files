<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * This is the screen function for my-home
 *
 * Uses BuddyPress plugins template
 *
 * @uses bp_my_home_get_css_url() to get the url of the css dir
 * @uses bp_my_home_get_version() to get the version of the plugin
 * @uses wp_enqueue_script() to enqueue the needed javascript
 * @uses bp_my_home_get_js_url() to get the url of the js dir
 * @uses wp_localize_script() to attach a var to the script and transport the user's saved state
 * @uses bp_my_home_collapsed_widgets() to get the user's saved widgets states
 * @uses bp_core_load_template() to ask for the plugins.php BuddyPress template
 */
function bp_my_home_home(){
	
    add_action( 'bp_template_title',   'bp_my_home_home_title'   );
    add_action( 'bp_template_content', 'bp_my_home_home_content' );

	wp_enqueue_style( 'bpmh-dragbox-css', bp_my_home_get_css_url() .'dragbox.css', false, bp_my_home_get_version() );
	wp_enqueue_script( 'bpmh-dragbox-js', bp_my_home_get_js_url() .'dragbox.js', array( 'jquery-ui-sortable' ), bp_my_home_get_version(), true );
	wp_localize_script( 'bpmh-dragbox-js', 'bpmh_vars', array( 'collapsed' => bp_my_home_collapsed_widgets() ) );
	
	
	bp_core_load_template( apply_filters( 'bp_my_home_home', 'members/single/plugins' ) );
}

/**
 * The title of my-home page
 *
 * @uses bp_my_home_name() to get the name of the plugin
 */
function bp_my_home_home_title() {
    bp_my_home_name();
}

/**
 * Content of my-home page ( let's display some widgets! )
 *
 * @uses bp_my_home_sidebars_empty_message() to print a message in case no widgets are set
 * @uses dynamic_sidebar() to load Widgets the WordPress way!
 */
function bp_my_home_home_content() {
    ?>
	<div id="bpmh-user-widgets">
		<?php bp_my_home_sidebars_empty_message();?>
		<div class="column" id="column1">
			<?php dynamic_sidebar( 'bpmh-left-sidebar' ); ?>
		</div>
		<div class="column" id="column2" >
			<?php dynamic_sidebar( 'bpmh-right-sidebar' ); ?>
		</div>
		<br style="clear:both;" />
	</div>
	<?php
}


/**
 * This is the screen function for my-settings subnav
 *
 * Uses BuddyPress plugins template
 *
 * @uses bp_core_load_template() to ask for the plugins.php BuddyPress template
 */
function bp_my_home_user_settings(){
	
    add_action( 'bp_template_title',   'bp_my_home_user_setting_title' );
    add_action( 'bp_template_content', 'bp_my_home_user_setting_content' );
	
	bp_core_load_template( apply_filters( 'bp_my_home_settings', 'members/single/plugins' ) );

}

/**
 * The title of my-settings page
 *
 * @uses bp_my_home_settings_name() to get the settings subnav name
 */
function bp_my_home_user_setting_title() {
	bp_my_home_settings_name();
}


/**
 * Content of my-settings page
 *
 * @global array $wp_registered_widgets
 * @uses bp_action_variables() to check we're not in single widget setting
 * @uses wp_get_sidebars_widgets() to get the sidebars widgets
 * @uses bp_loggedin_user_id() to get current user's id
 * @uses get_user_meta() to get user's preferences
 * @uses bpmh_widget_get_title() to get widgets titles
 * @uses bp_my_home_sidebars_empty_message() to eventually add a message if no widgets are set by admin
 * @uses bpmh_admin_allowed_home_page() to check admin allowes users to changing website landing page
 * @uses wp_nonce_field() for security reasons
 * @uses bpmh_the_widget() to load a single widget user's settings
 */
function bp_my_home_user_setting_content() {
	// global user settings
	if( !bp_action_variables() ) {
		global $wp_registered_widgets;
		
		$all_widgets = wp_get_sidebars_widgets();
		$params = array();

		if( empty( $all_widgets['bpmh-left-sidebar'] ) )
			$all_widgets['bpmh-left-sidebar'] = array();

		if( empty( $all_widgets['bpmh-right-sidebar'] ) )
			$all_widgets['bpmh-right-sidebar'] = array();
		
		$bpmh_widgets = array_merge( $all_widgets['bpmh-left-sidebar'], $all_widgets['bpmh-right-sidebar'] );
		
		$user_id = bp_loggedin_user_id();
		
		$user_settings = get_user_meta( $user_id, 'bpmh_user_activated_widgets', true );
		$user_widgets_pref = get_user_meta( $user_id, '_bpmh_user_saved_state', true );
		$user_home_pref = get_user_meta( $user_id, 'bpmh_user_home_page', true );
		
		if( empty( $user_settings ) )
			$user_settings = array();
		
		if( !empty( $user_widgets_pref ) ) {
			$user_widgets_diff = array_diff( $bpmh_widgets, array_keys( $user_widgets_pref ) );
			$user_settings = array_merge( $user_settings, $user_widgets_diff );
		}
		
		foreach( $bpmh_widgets as $widget_id ) {
			
			$title = false;
			
			if ( !isset($wp_registered_widgets[$widget_id]) ) continue;
			
			$title = bpmh_widget_get_title( $wp_registered_widgets[$widget_id]['callback'][0] );

			$params[] = array( 'widget_id' => $widget_id, 'widget_name' => $wp_registered_widgets[$widget_id]['name'], 'title' => $title );
		}
		?>
		<form action="" method="post" class="standard-form">
			
			<h4><?php _e( 'Uncheck the widget(s) you want to disable.', 'bp-my-home' );?></h4>
			
			<?php if( !empty( $params ) && is_array( $params ) && count( $params ) > 0 ):?>
				
				<input type="hidden" name="_bpmh_user_settings[sidebar]" value="1">
				<ul class="bpmh-user-settings-widgets-list">
					<?php foreach( $params as $widget_param ) :?>
						<li>
							<input type="checkbox" name="_bpmh_user_settings[widgets][]" value="<?php echo $widget_param['widget_id'];?>"
						 	<?php bpmh_checked_in_array( $user_settings, $widget_param['widget_id'] );?> 
							<?php echo apply_filters( 'bpmh_user_settings_widget_input', '', $widget_param );?>
							>
							<?php if( !empty( $widget_param['title'] ) ) :
								echo $widget_param['title'];
							else:
								echo $widget_param['widget_name'];
							endif;
							?>
						</li>
					<?php endforeach;?>
				</ul>
				
			<?php else:
				bp_my_home_sidebars_empty_message();
			endif;?>
			
			<?php if( bpmh_admin_allowed_home_page() ) :?>
				<h4><?php _e( 'Set your profile page as this website landing page.', 'bp-my-home' );?></h4>
				<p>
					<input type="checkbox" value="1" name="_bpmh_user_settings[homepage]" <?php checked( $user_home_pref, 1 );?>>
					<?php _e( 'Activate', 'bp-my-home' );?>
				</p>
			<?php endif;?>
			
			<div class="submit">
				<?php wp_nonce_field( 'bpmh_user_options', '_wpnonce_bpmh_user_options' ); ?>
				<input type="submit" name="_bpmh_user_settings[save]" value="<?php _e('Save settings', 'bp-my-home');?>"/>
			</div>
		<?php
	}
	
	// Single widget settings
	if( bp_action_variable( 0 ) == 'widget' && bp_action_variable( 1 ) ) {
		$widget_php_class = base64_decode( bp_action_variable( 1 ) );
		
		if( !empty( $widget_php_class ) )
			bpmh_the_widget( $widget_php_class );
	}
}