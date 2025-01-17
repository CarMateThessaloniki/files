<?php

/**
 * BuddyPress Admin Settings
 *
 * @package BuddyPress
 * @subpackage CoreAdministration
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Main settings section description for the settings page
 *
 * @since BuddyPress (1.6)
 */
function bp_admin_setting_callback_main_section() { }

/**
 * Admin bar for logged out users setting field
 *
 * @since BuddyPress (1.6)
 *
 * @uses bp_form_option() To output the option value
 */
function bp_admin_setting_callback_admin_bar() {
?>

	<input id="hide-loggedout-adminbar" name="hide-loggedout-adminbar" type="checkbox" value="1" <?php checked( !bp_hide_loggedout_adminbar( false ) ); ?> />
	<label for="hide-loggedout-adminbar"><?php _e( 'Show the Toolbar for logged out users', 'buddypress' ); ?></label>

<?php
}

/**
 * Allow members to delete their accounts setting field
 *
 * @since BuddyPress (1.6)
 *
 * @uses checked() To display the checked attribute
 */
function bp_admin_setting_callback_account_deletion() {
?>

	<input id="bp-disable-account-deletion" name="bp-disable-account-deletion" type="checkbox" value="1" <?php checked( !bp_disable_account_deletion( false ) ); ?> />
	<label for="bp-disable-account-deletion"><?php _e( 'Allow registered members to delete their own accounts', 'buddypress' ); ?></label>

<?php
}

/**
 * If user has upgraded to 1.6 and chose to retain their BuddyBar, offer then a switch to change over
 * to the WP Toolbar.
 *
 * @since BuddyPress (1.6)
 */
function bp_admin_setting_callback_force_buddybar() {
?>

	<input id="_bp_force_buddybar" name="_bp_force_buddybar" type="checkbox" value="1" <?php checked( ! bp_force_buddybar( true ) ); ?> />
	<label for="_bp_force_buddybar"><?php _e( 'Switch to WordPress Toolbar', 'buddypress' ); ?></label>

<?php
}

/** Activity *******************************************************************/

/**
 * Groups settings section description for the settings page
 *
 * @since BuddyPress (1.6)
 */
function bp_admin_setting_callback_activity_section() { }

/**
 * Allow Akismet setting field
 *
 * @since BuddyPress (1.6)
 *
 * @uses checked() To display the checked attribute
 */
function bp_admin_setting_callback_activity_akismet() {
?>

	<input id="_bp_enable_akismet" name="_bp_enable_akismet" type="checkbox" value="1" <?php checked( bp_is_akismet_active( true ) ); ?> />
	<label for="_bp_enable_akismet"><?php _e( 'Allow Akismet to scan for activity stream spam', 'buddypress' ); ?></label>

<?php
}

/**
 * Allow activity comments on blog posts and forum posts
 *
 * @since BuddyPress (1.6)
 */
function bp_admin_setting_callback_blogforum_comments() {
?>

	<input id="bp-disable-blogforum-comments" name="bp-disable-blogforum-comments" type="checkbox" value="1" <?php checked( !bp_disable_blogforum_comments( false ) ); ?> />
	<label for="bp-disable-blogforum-comments"><?php _e( 'Allow activity stream commenting on blog and forum posts', 'buddypress' ); ?></label>

<?php
}

/**
 * Allow Heartbeat to refresh activity stream.
 *
 * @since BuddyPress (2.0.0)
 */
function bp_admin_setting_callback_heartbeat() {
?>

	<input id="_bp_enable_heartbeat_refresh" name="_bp_enable_heartbeat_refresh" type="checkbox" value="1" <?php checked( bp_is_activity_heartbeat_active( true ) ); ?> />
	<label for="_bp_enable_heartbeat_refresh"><?php _e( 'Automatically check for new items while viewing the activity stream', 'buddypress' ); ?></label>

<?php
}

/**
 * Sanitization for _bp_force_buddyvar
 *
 * If upgraded to 1.6 and you chose to keep the BuddyBar, a checkbox asks if you want to switch to
 * the WP Toolbar. The option we store is 1 if the BuddyBar is forced on, so we use this function
 * to flip the boolean before saving the intval.
 *
 * @since BuddyPress (1.6)
 * @access Private
 */
function bp_admin_sanitize_callback_force_buddybar( $value = false ) {
	return $value ? 0 : 1;
}

/**
 * Sanitization for bp-disable-blogforum-comments setting
 *
 * In the UI, a checkbox asks whether you'd like to *enable* blog/forum activity comments. For
 * legacy reasons, the option that we store is 1 if these comments are *disabled*. So we use this
 * function to flip the boolean before saving the intval.
 *
 * @since BuddyPress (1.6)
 */
function bp_admin_sanitize_callback_blogforum_comments( $value = false ) {
	return $value ? 0 : 1;
}

/** XProfile ******************************************************************/

/**
 * Profile settings section description for the settings page
 *
 * @since BuddyPress (1.6)
 */
function bp_admin_setting_callback_xprofile_section() { }

/**
 * Enable BP->WP profile syncing field
 *
 * @since BuddyPress (1.6)
 *
 * @uses bp_form_option() To output the option value
 */
function bp_admin_setting_callback_profile_sync() {
?>

	<input id="bp-disable-profile-sync" name="bp-disable-profile-sync" type="checkbox" value="1" <?php checked( !bp_disable_profile_sync( false ) ); ?> />
	<label for="bp-disable-profile-sync"><?php _e( 'Enable BuddyPress to WordPress profile syncing', 'buddypress' ); ?></label>

<?php
}

/**
 * Allow members to upload avatars field
 *
 * @since BuddyPress (1.6)
 *
 * @uses checked() To display the checked attribute
 */
function bp_admin_setting_callback_avatar_uploads() {
?>

	<input id="bp-disable-avatar-uploads" name="bp-disable-avatar-uploads" type="checkbox" value="1" <?php checked( !bp_disable_avatar_uploads( false ) ); ?> />
	<label for="bp-disable-avatar-uploads"><?php _e( 'Allow registered members to upload avatars', 'buddypress' ); ?></label>

<?php
}

/** Groups Section ************************************************************/

/**
 * Groups settings section description for the settings page
 *
 * @since BuddyPress (1.6)
 */
function bp_admin_setting_callback_groups_section() { }

/**
 * Allow all users to create groups field
 *
 * @since BuddyPress (1.6)
 *
 * @uses checked() To display the checked attribute
 */
function bp_admin_setting_callback_group_creation() {
?>

	<input id="bp_restrict_group_creation" name="bp_restrict_group_creation" type="checkbox"value="1" <?php checked( !bp_restrict_group_creation( false ) ); ?> />
	<label for="bp_restrict_group_creation"><?php _e( 'Enable group creation for all users', 'buddypress' ); ?></label>
	<p class="description"><?php _e( 'Administrators can always create groups, regardless of this setting.', 'buddypress' ); ?></p>

<?php
}

/** Forums Section ************************************************************/

/**
 * Forums settings section description for the settings page
 *
 * @since BuddyPress (1.6)
 */
function bp_admin_setting_callback_bbpress_section() { }

/**
 * bb-config.php location field
 *
 * @since BuddyPress (1.6)
 * @uses checked() To display the checked attribute
 * @uses bp_get_option() To get the config location
 * @uses bp_form_option() To get the sanitized form option
 */
function bp_admin_setting_callback_bbpress_configuration() {

	$config_location = bp_get_option( 'bb-config-location' );
	$file_exists     = (bool) ( file_exists( $config_location ) || is_file( $config_location ) ); ?>

	<input name="bb-config-location" type="text" id="bb-config-location" value="<?php bp_form_option( 'bb-config-location', '' ); ?>" class="medium-text" style="width: 300px;" />

	<?php if ( false === $file_exists ) : ?>

		<a class="button" href="<?php bp_admin_url( 'admin.php?page=bb-forums-setup&repair=1' ); ?>" title="<?php esc_attr_e( 'Attempt to save a new config file.', 'buddypress' ); ?>"><?php _e( 'Repair', 'buddypress' ) ?></a>
		<span class="attention"><?php _e( 'File does not exist', 'buddypress' ); ?></span>

	<?php endif; ?>

	<p class="description"><?php _e( 'Absolute path to your bbPress configuration file.', 'buddypress' ); ?></p>

<?php
}

/** Settings Page *************************************************************/

/**
 * The main settings page
 *
 * @since BuddyPress (1.6)
 *
 * @uses screen_icon() To display the screen icon
 * @uses settings_fields() To output the hidden fields for the form
 * @uses do_settings_sections() To output the settings sections
 */
function bp_core_admin_settings() {

	// We're saving our own options, until the WP Settings API is updated to work with Multisite
	$form_action = add_query_arg( 'page', 'bp-settings', bp_get_admin_url( 'admin.php' ) );

	?>

	<div class="wrap">

		<?php screen_icon( 'buddypress' ); ?>

		<h2 class="nav-tab-wrapper"><?php bp_core_admin_tabs( __( 'Settings', 'buddypress' ) ); ?></h2>

		<form action="<?php echo $form_action ?>" method="post">

			<?php settings_fields( 'buddypress' ); ?>

			<?php do_settings_sections( 'buddypress' ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'buddypress' ); ?>" />
			</p>
		</form>
	</div>

<?php
}

/**
 * Save our settings
 *
 * @since BuddyPress (1.6)
 */
function bp_core_admin_settings_save() {
	global $wp_settings_fields;

	if ( isset( $_GET['page'] ) && 'bp-settings' == $_GET['page'] && !empty( $_POST['submit'] ) ) {
		check_admin_referer( 'buddypress-options' );

		// Because many settings are saved with checkboxes, and thus will have no values
		// in the $_POST array when unchecked, we loop through the registered settings
		if ( isset( $wp_settings_fields['buddypress'] ) ) {
			foreach( (array) $wp_settings_fields['buddypress'] as $section => $settings ) {
				foreach( $settings as $setting_name => $setting ) {
					$value = isset( $_POST[$setting_name] ) ? $_POST[$setting_name] : '';

					bp_update_option( $setting_name, $value );
				}
			}
		}

		// Some legacy options are not registered with the Settings API
		$legacy_options = array(
			'bp-disable-account-deletion',
			'bp-disable-avatar-uploads',
			'bp_disable_blogforum_comments',
			'bp-disable-profile-sync',
			'bp_restrict_group_creation',
			'hide-loggedout-adminbar',
		);

		foreach( $legacy_options as $legacy_option ) {
			// Note: Each of these options is represented by its opposite in the UI
			// Ie, the Profile Syncing option reads "Enable Sync", so when it's checked,
			// the corresponding option should be unset
			$value = isset( $_POST[$legacy_option] ) ? '' : 1;
			bp_update_option( $legacy_option, $value );
		}

		bp_core_redirect( add_query_arg( array( 'page' => 'bp-settings', 'updated' => 'true' ), bp_get_admin_url( 'admin.php' ) ) );
	}
}
add_action( 'bp_admin_init', 'bp_core_admin_settings_save', 100 );

/**
 * Output settings API option
 *
 * @since BuddyPress (1.6)
 *
 * @uses bp_get_bp_form_option()
 *
 * @param string $option
 * @param string $default
 * @param bool $slug
 */
function bp_form_option( $option, $default = '' , $slug = false ) {
	echo bp_get_form_option( $option, $default, $slug );
}
	/**
	 * Return settings API option
	 *
	 * @since BuddyPress (1.6)
	 *
	 * @uses bp_get_option()
	 * @uses esc_attr()
	 * @uses apply_filters()
	 *
	 * @param string $option
	 * @param string $default
	 * @param bool $slug
	 */
	function bp_get_form_option( $option, $default = '', $slug = false ) {

		// Get the option and sanitize it
		$value = bp_get_option( $option, $default );

		// Slug?
		if ( true === $slug )
			$value = esc_attr( apply_filters( 'editable_slug', $value ) );

		// Not a slug
		else
			$value = esc_attr( $value );

		// Fallback to default
		if ( empty( $value ) )
			$value = $default;

		// Allow plugins to further filter the output
		return apply_filters( 'bp_get_form_option', $value, $option );
	}
