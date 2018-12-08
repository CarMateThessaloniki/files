<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * The main settings arguments
 * 
 * @return array
 */
function bp_my_home_admin_get_settings_sections() {
	return (array) apply_filters( 'bp_my_home_admin_get_settings_sections', array(
		'bp_my_home_settings_main' => array(
			'title'    => __( 'Main Settings', 'bp-my-home' ),
			'callback' => 'bp_my_home_admin_setting_callback_main_section',
			'page'     => 'bp-my-home',
		),
		'bp_my_home_settings_custom' => array(
			'title'    => __( 'Customization', 'bp-my-home' ),
			'callback' => 'bp_my_home_admin_setting_callback_custom_section',
			'page'     => 'bp-my-home',
		)
	) );
}

/**
 * The different fields for the main settings
 * 
 * @return array
 */
function bp_my_home_admin_get_settings_fields() {
	return (array) apply_filters( 'bp_my_home_admin_get_settings_fields', array(

		/** Main Section ******************************************************/

		'bp_my_home_settings_main' => array(

			'_bpmh_is_default_member_component' => array(
				'title'             => __( 'Disable BP my Home as the default component of member&#39;s profile area', 'bp-my-home' ),
				'callback'          => 'bp_my_home_admin_setting_callback_register_default_member_component',
				'sanitize_callback' => 'absint',
				'args'              => array()
			),
			'_bpmh_deactivate_redirect_to_home' => array(
				'title'             => __( 'Do not allow users to set their profile page as the landing page of this website', 'bp-my-home' ),
				'callback'          => 'bp_my_home_admin_setting_callback_register_redirect_to_home',
				'sanitize_callback' => 'absint',
				'args'              => array()
			),
			'_bpmh_auto_bkmk_option' => array(
				'title'             => __( 'Disable the Bookmark button in page or posts', 'bp-my-home' ),
				'callback'          => 'bp_my_home_admin_setting_callback_register_auto_bkmk_option',
				'sanitize_callback' => 'absint',
				'args'              => array()
			),
			'_bpmh_auto_rss_option' => array(
				'title'             => __( 'Disable the use of the Subscribe button in templates', 'bp-my-home' ),
				'callback'          => 'bp_my_home_admin_setting_callback_register_auto_rss_option',
				'sanitize_callback' => 'absint',
				'args'              => array()
			),
			'_bp_my_home_register_admin_widget_js' => array(
				'title'             => __( 'Disable The admin-widget js replacement', 'bp-my-home' ),
				'callback'          => 'bp_my_home_admin_setting_callback_register_admin_widget_js',
				'sanitize_callback' => 'absint',
				'args'              => array()
			)
		),
		'bp_my_home_settings_custom' => array(

			'_bpmh_custom_home_slug' => array(
				'title'             => __( 'Custom slug for the component main url', 'bp-my-home' ),
				'callback'          => 'bp_my_home_admin_setting_callback_register_custom_home_slug',
				'sanitize_callback' => 'bp_my_home_sanitize_custom_slug',
				'args'              => array()
			),
			'_bpmh_custom_home_name' => array(
				'title'             => __( 'Custom name for the component', 'bp-my-home' ),
				'callback'          => 'bp_my_home_admin_setting_callback_register_custom_home_name',
				'sanitize_callback' => 'bp_my_home_sanitize_custom_name',
				'args'              => array()
			),
			'_bpmh_custom_nav_home_name' => array(
				'title'             => __( 'Custom name for main nav', 'bp-my-home' ),
				'callback'          => 'bp_my_home_admin_setting_callback_register_custom_nav_home_name',
				'sanitize_callback' => 'bp_my_home_sanitize_custom_name',
				'args'              => array()
			),
			'_bpmh_custom_nav_widgets_slug' => array(
				'title'             => __( 'Custom slug for the widgets subnav', 'bp-my-home' ),
				'callback'          => 'bp_my_home_admin_setting_callback_register_custom_widgets_slug',
				'sanitize_callback' => 'bp_my_home_sanitize_custom_slug',
				'args'              => array()
			),
			'_bpmh_custom_nav_widgets_name' => array(
				'title'             => __( 'Custom name for the widgets subnav', 'bp-my-home' ),
				'callback'          => 'bp_my_home_admin_setting_callback_register_custom_widgets_name',
				'sanitize_callback' => 'bp_my_home_sanitize_custom_name',
				'args'              => array()
			),
			'_bpmh_custom_nav_settings_slug' => array(
				'title'             => __( 'Custom slug for the user settings subnav', 'bp-my-home' ),
				'callback'          => 'bp_my_home_admin_setting_callback_register_custom_settings_slug',
				'sanitize_callback' => 'bp_my_home_sanitize_custom_slug',
				'args'              => array()
			),
			'_bpmh_custom_nav_settings_name' => array(
				'title'             => __( 'Custom name for the user settings subnav', 'bp-my-home' ),
				'callback'          => 'bp_my_home_admin_setting_callback_register_custom_settings_name',
				'sanitize_callback' => 'bp_my_home_sanitize_custom_name',
				'args'              => array()
			)
		)
	) );
}


/**
 * Gives the setting fields for section
 * 
 * @param  string $section_id 
 * @uses bp_my_home_admin_get_settings_fields() to get the fields
 * @return array  the fields
 */
function bp_my_home_admin_get_settings_fields_for_section( $section_id = '' ) {

	// Bail if section is empty
	if ( empty( $section_id ) )
		return false;

	$fields = bp_my_home_admin_get_settings_fields();
	$retval = isset( $fields[$section_id] ) ? $fields[$section_id] : false;

	return (array) apply_filters( 'bp_my_home_admin_get_settings_fields_for_section', $retval, $section_id );
}

/**
 * Some text to introduce the settings section
 * 
 * @return string html
 */
function bp_my_home_admin_setting_callback_main_section() {
?>

	<p><?php _e( 'Set BP My Home !', 'bp-my-home' ); ?></p>
	<p style="padding:1em;border-radius:3px;border:solid 1px #ccc;background:#f1f1f1;font-weight:bold"><?php printf( __( 'You need to set widgets ? Since version 2.0, BP My Home uses the <a href="%s">WordPress built-in Widgets manager</a>.', 'bp-my-home' ), admin_url( 'widgets.php' ) );?>

<?php
}


/**
 * Some text to introduce the customization section
 * 
 * @return string html
 */
function bp_my_home_admin_setting_callback_custom_section() {
?>

	<h4><?php _e( 'Customize BP My Home !', 'bp-my-home' ); ?></h4>
	<p class="description"> <?php _e( 'If you do not like the default slugs or names, you can use theses options to make BP My Home a little more customized. Make sure to avoid the use of other BuddyPress components.', 'bp-my-home' );?></p>

<?php
}

/**
 * Let the admin sets the member's default component
 *
 * @uses bp_get_option() to get the previously saved setting
 * @return string html
 */
function bp_my_home_admin_setting_callback_register_default_member_component() {
	$default_component = intval( bp_get_option( '_bpmh_is_default_member_component', 0 ) );
	?>
	<p>
		<input name="_bpmh_is_default_member_component" type="radio" id="_bpmh_is_default_member_component_yes" value="1" <?php checked( $default_component, 1 );?> />
			<label for="_bpmh_is_default_member_component_yes"><?php _e( 'Yes', 'bp-my-home' ); ?></label>
	</p>
	<p>
		<input name="_bpmh_is_default_member_component" type="radio" id="_bpmh_is_default_member_component_no" value="0" <?php checked( $default_component, 0 );?> />
		<label for="_bpmh_is_default_member_component_no"><?php _e( 'No', 'bp-my-home' ); ?></label>
	</p>
	<p class="description"> <?php _e( 'BP My home, by default, is replacing the activity or profile component as the default member&#39;s component. Setting this option to Yes will disable this feature and the default navigation will be the activity or profile one.', 'bp-my-home' );?></p>
	<?php
}

/**
 * Let the admin deactivate the ability to set user's page as landing page
 *
 * @uses bp_get_option() to get the previously saved setting
 * @return string html
 */
function bp_my_home_admin_setting_callback_register_redirect_to_home() {
	$home_redirect = intval( bp_get_option( '_bpmh_deactivate_redirect_to_home', 0 ) );
	?>
	<p>
		<input name="_bpmh_deactivate_redirect_to_home" type="radio" id="_bpmh_deactivate_redirect_to_home_yes" value="1" <?php checked( $home_redirect, 1 );?> />
			<label for="_bpmh_deactivate_redirect_to_home_yes"><?php _e( 'Yes', 'bp-my-home' ); ?></label>
	</p>
	<p>
		<input name="_bpmh_deactivate_redirect_to_home" type="radio" id="_bpmh_deactivate_redirect_to_home_no" value="0" <?php checked( $home_redirect, 0 );?> />
		<label for="_bpmh_deactivate_redirect_to_home_no"><?php _e( 'No', 'bp-my-home' ); ?></label>
	</p>
	<p class="description"> <?php _e( 'BP My home, by default, allow users to set their BP My Home page as the landing page of your website. Setting this option to Yes will deactivate this feature.', 'bp-my-home' );?></p>
	<?php
}

/**
 * Let the admin deactivate the bookmark button
 *
 * @uses bp_get_option() to get the previously saved setting
 * @return string html
 */
function bp_my_home_admin_setting_callback_register_auto_bkmk_option() {
	$bkmk = intval( bp_get_option( '_bpmh_auto_bkmk_option', 0 ) );
	?>
	<p>
		<input name="_bpmh_auto_bkmk_option" type="radio" id="_bpmh_auto_bkmk_option_yes" value="1" <?php checked( $bkmk, 1 );?> />
			<label for="_bpmh_auto_bkmk_option_yes"><?php _e( 'Yes', 'bp-my-home' ); ?></label>
	</p>
	<p>
		<input name="_bpmh_auto_bkmk_option" type="radio" id="_bpmh_auto_bkmk_option_no" value="0" <?php checked( $bkmk, 0 );?> />
		<label for="_bpmh_auto_bkmk_option_no"><?php _e( 'No', 'bp-my-home' ); ?></label>
	</p>
	<p class="description"> <?php _e( 'BP My home, by default, is adding a Bookmark button at the end of your posts or pages while viewing them in their singular templates. You can disable this feature by setting this option to Yes. If you did so, yon can still add a bookmark button using this template tag <code>&lt;?php if( function_exists( &#39;the_bpmh_bkmks_tag&#39; ) ) the_bpmh_bkmks_tag() ; ?&gt;</code> in the single or page template.', 'bp-my-home' );?></p>
	<?php
}

/**
 * Let the admin deactivate the subscribe button
 *
 * @uses bp_get_option() to get the previously saved setting
 * @return string html
 */
function bp_my_home_admin_setting_callback_register_auto_rss_option() {
	$rss = intval( bp_get_option( '_bpmh_auto_rss_option', 0 ) );
	?>
	<p>
		<input name="_bpmh_auto_rss_option" type="radio" id="_bpmh_auto_rss_option_yes" value="1" <?php checked( $rss, 1 );?> />
			<label for="_bpmh_auto_rss_option_yes"><?php _e( 'Yes', 'bp-my-home' ); ?></label>
	</p>
	<p>
		<input name="_bpmh_auto_rss_option" type="radio" id="_bpmh_auto_rss_option_no" value="0" <?php checked( $rss, 0 );?> />
		<label for="_bpmh_auto_rss_option_no"><?php _e( 'No', 'bp-my-home' ); ?></label>
	</p>
	<p class="description"> <?php _e( 'If this tag <code>&lt;?php if( function_exists( &#39;the_bpmh_rss_button&#39; ) ) the_bpmh_rss_button() ; ?&gt;</code> is added to your category, archive, single or search template, then BP My home, by default, will add a Subscribe button to let your members easily add some of your feeds to the rss reader widget. You can disable this feature by setting this option to Yes.', 'bp-my-home' );?></p>
	<?php
}

/**
 * Let the admin deactivate the admin-widget.js replacement
 *
 * @uses bp_get_option() to get the previously saved setting
 * @return string html
 */
function bp_my_home_admin_setting_callback_register_admin_widget_js() {
	$admin_widget_js = bp_get_option( '_bp_my_home_register_admin_widget_js', 0 );
	$admin_widget_js = intval( $admin_widget_js );
	?>

	<p>
		<input name="_bp_my_home_register_admin_widget_js" type="radio" id="_bp_my_home_register_admin_widget_js_yes" value="1" <?php checked( $admin_widget_js, 1 );?> />
			<label for="_bp_my_home_register_admin_widget_js_yes"><?php _e( 'Yes', 'bp-my-home' ); ?></label>
	</p>
	<p>
		<input name="_bp_my_home_register_admin_widget_js" type="radio" id="_bp_my_home_register_admin_widget_js_no" value="0" <?php checked( $admin_widget_js, 0 );?> />
		<label for="_bp_my_home_register_admin_widget_js_no"><?php _e( 'No', 'bp-my-home' ); ?></label>
	</p>
	<p class="description"> <?php _e( 'BP My home, by default, is replacing the core admin-widget.js script by its own, and extends the widget admin area to ease the process of managing BP My Home widgets. Setting this option to Yes will disable this feature, and you will need to make sure only one instance of each of the BPMH Widgets is in the BPMH Sidebars and only these sidebars.', 'bp-my-home' );?></p>
	<?php
}

/**
 * Let the admin customize the slug of the plugin
 *
 * @uses bp_my_home() to get default value
 * @uses bp_my_home_get_slug() to get customized value
 * @uses esc_attr() to sanitize
 * @return string html
 */
function bp_my_home_admin_setting_callback_register_custom_home_slug() {
	$default_home_slug = bp_my_home()->bp_my_home_slug;
	
	$home_slug = bp_my_home_get_slug();
	?>
	<p>
		<input name="_bpmh_custom_home_slug" type="text" id="_bpmh_custom_home_slug" value="<?php echo esc_attr( $home_slug );?>" class="regular-text code">
	</p>
	<?php
}

/**
 * Let the admin customize the name of the plugin
 *
 * @uses bp_my_home() to get default value
 * @uses bp_my_home_get_name() to get customized value
 * @uses esc_attr() to sanitize
 * @return string html
 */
function bp_my_home_admin_setting_callback_register_custom_home_name() {
	$default_home_name = bp_my_home()->bp_my_home_name;
	
	$home_name = bp_my_home_get_name();
	?>
	<p>
		<input name="_bpmh_custom_home_name" type="text" id="_bpmh_custom_home_name" value="<?php echo esc_attr( $home_name );?>" class="regular-text code">
	</p>
	<?php
}

/**
 * Let the admin customize the main nav name of the plugin
 *
 * @uses bp_my_home() to get default value
 * @uses bp_my_home_get_home_name() to get customized value
 * @uses esc_attr() to sanitize
 * @return string html
 */
function bp_my_home_admin_setting_callback_register_custom_nav_home_name() {
	$default_nav_home_name = bp_my_home()->nav_my_home_name;
	
	$nav_home_name = bp_my_home_get_home_name();
	?>
	<p>
		<input name="_bpmh_custom_nav_home_name" type="text" id="_bpmh_custom_nav_home_name" value="<?php echo esc_attr( $nav_home_name );?>" class="regular-text code">
	</p>
	<?php
}

/**
 * Let the admin customize the widgets subnav slug of the plugin
 *
 * @uses bp_my_home() to get default value
 * @uses bp_my_home_get_widgets_slug() to get customized value
 * @uses esc_attr() to sanitize
 * @return string html
 */
function bp_my_home_admin_setting_callback_register_custom_widgets_slug() {
	$default_nav_widgets_slug = bp_my_home()->nav_my_widgets_slug;
	
	$nav_widgets_slug = bp_my_home_get_widgets_slug();
	?>
	<p>
		<input name="_bpmh_custom_nav_widgets_slug" type="text" id="_bpmh_custom_nav_widgets_slug" value="<?php echo esc_attr( $nav_widgets_slug );?>" class="regular-text code">
	</p>
	<?php
}

/**
 * Let the admin customize the widgets subnav name of the plugin
 *
 * @uses bp_my_home() to get default value
 * @uses bp_my_home_get_widgets_name() to get customized value
 * @uses esc_attr() to sanitize
 * @return string html
 */
function bp_my_home_admin_setting_callback_register_custom_widgets_name() {
	$default_nav_widgets_name = bp_my_home()->nav_my_widgets_name;
	
	$nav_widgets_name = bp_my_home_get_widgets_name();
	?>
	<p>
		<input name="_bpmh_custom_nav_widgets_name" type="text" id="_bpmh_custom_nav_widgets_name" value="<?php echo esc_attr( $nav_widgets_name );?>" class="regular-text code">
	</p>
	<?php
}

/**
 * Let the admin customize the settings subnav slug of the plugin
 *
 * @uses bp_my_home() to get default value
 * @uses bp_my_home_get_settings_slug() to get customized value
 * @uses esc_attr() to sanitize
 * @return string html
 */
function bp_my_home_admin_setting_callback_register_custom_settings_slug() {
	$default_nav_settings_slug = bp_my_home()->nav_my_settings_slug;
	
	$nav_settings_slug = bp_my_home_get_settings_slug();
	?>
	<p>
		<input name="_bpmh_custom_nav_settings_slug" type="text" id="_bpmh_custom_nav_settings_slug" value="<?php echo esc_attr( $nav_settings_slug );?>" class="regular-text code">
	</p>
	<?php
}

/**
 * Let the admin customize the settings subnav name of the plugin
 *
 * @uses bp_my_home() to get default value
 * @uses bp_my_home_get_settings_name() to get customized value
 * @uses esc_attr() to sanitize
 * @return string html
 */
function bp_my_home_admin_setting_callback_register_custom_settings_name() {
	$default_nav_settings_name = bp_my_home()->nav_my_settings_name;
	
	$nav_settings_name = bp_my_home_get_settings_name();
	?>
	<p>
		<input name="_bpmh_custom_nav_settings_name" type="text" id="_bpmh_custom_nav_settings_name" value="<?php echo esc_attr( $nav_settings_name );?>" class="regular-text code">
	</p>
	<?php
}

/**
 * Sanitize the slugs and check they does not already exist in active components
 *
 * @param int $option 
 * @uses buddypress() to get its main instance
 * @uses bp_my_home_get_slug() to exclude BP My Home slug
 * @uses sanitize_title() to sanitize the slug
 * @return int the slug
 */
function bp_my_home_sanitize_custom_slug( $option ) {
	$active_buddypress_components = array_keys( buddypress()->active_components );
	
	$active_buddypress_components = array_diff( $active_buddypress_components, array( bp_my_home_get_slug() ) );
	
	$option = sanitize_title( $option );
	
	if( in_array( $option, $active_buddypress_components ) )
		return false;
	else
		return $option;
}

/**
 * Sanitize the names
 *
 * @param int $option 
 * @uses sanitize_text_field() to sanitize the name
 * @return int the slug
 */
function bp_my_home_sanitize_custom_name( $option ) {
	$option = sanitize_text_field( $option );
	
	return $option;
}

/**
 * Displays the settings page
 * 
 * @uses is_multisite() to check for multisite
 * @uses add_query_arg() to add arguments to query in case of multisite
 * @uses bp_get_admin_url to build the settings url in case of multisite
 * @uses screen_icon() to show default icon
 * @uses settings_fields()
 * @uses do_settings_sections()
 * @uses wp_nonce_field() for security reason in case of multisite
 */
function bp_my_home_settings() {
	$form_action = 'options.php';
	
	if( is_multisite() ) {
		do_action( 'bpmh_multisite_options' );
		
		$form_action = add_query_arg( 'page', 'bp-my-home', bp_get_admin_url( 'settings.php' ) );
	}
?>

	<div class="wrap">

		<?php screen_icon( 'options-general'); ?>

		<h2><?php _e( 'BP My Home Settings', 'bp-my-home' ) ?></h2>

		<form action="<?php echo $form_action;?>" method="post">

			<?php settings_fields( 'bp-my-home' ); ?>

			<?php do_settings_sections( 'bp-my-home' ); ?>

			<p class="submit">
				<?php if( is_multisite() ) :?>
					<?php wp_nonce_field( 'bp_my_home_settings', '_wpnonce_bp_my_home_settings' ); ?>
				<?php endif;?>
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'bp-my-home' ); ?>" />
			</p>
		</form>
	</div>

<?php
}


/**
 * Loops threw $_POST to save or delete settings in case of multisite
 *
 * @uses check_admin_referer() to check the nonce
 * @uses bp_my_home_sanitize_custom_slug() to sanitize slugs
 * @uses bp_my_home_sanitize_custom_name() to sanitize names
 * @uses bp_update_option() to save the options in root blog
 * @uses bp_delete_option() to delete the option if empty
 */
function bp_my_home_handle_settings_in_multisite() {
	
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;
	
	check_admin_referer( 'bp_my_home_settings', '_wpnonce_bp_my_home_settings' );
		
	foreach( $_POST as $key => $option ) {
		$bpmh_option = false;
		
		if( preg_match( '/_bpmh_custom.*slug/', $key ) ) {
			$bpmh_option = bp_my_home_sanitize_custom_slug( $option );
		} else if( preg_match( '/_bpmh_custom.*name/', $key ) ) {
			$bpmh_option = bp_my_home_sanitize_custom_name( $option );
		} else {
			$bpmh_option = intval( $option );
		}
		
		if( !empty( $bpmh_option ) )
			bp_update_option( $key, $bpmh_option );
		else
			bp_delete_option( $key );
		
	}
	
	?>
	<div id="message" class="updated"><p><?php _e( 'Settings saved', 'bp-my-home' );?></p></div>
	<?php
	
}

add_action( 'bpmh_multisite_options', 'bp_my_home_handle_settings_in_multisite', 0 );