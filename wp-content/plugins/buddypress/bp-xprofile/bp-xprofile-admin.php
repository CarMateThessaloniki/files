<?php

/**
 * BuddyPress XProfile Admin
 *
 * @package BuddyPress
 * @subpackage XProfileAdmin
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Creates the administration interface menus and checks to see if the DB
 * tables are set up.
 *
 * @package BuddyPress XProfile
 * @uses bp_current_user_can() returns true if the current user is a site admin, false if not
 * @uses add_users_page() Adds a submenu tab to a top level tab in the admin area
 * @return
 */
function xprofile_add_admin_menu() {

	if ( !bp_current_user_can( 'bp_moderate' ) )
		return false;

	add_users_page( __( 'Profile Fields', 'buddypress' ), __( 'Profile Fields', 'buddypress' ), 'manage_options', 'bp-profile-setup', 'xprofile_admin' );
}
add_action( bp_core_admin_hook(), 'xprofile_add_admin_menu' );

/**
 * Handles all actions for the admin area for creating, editing and deleting
 * profile groups and fields.
 */
function xprofile_admin( $message = '', $type = 'error' ) {

	$type = preg_replace( '|[^a-z]|i', '', $type );

	$groups = BP_XProfile_Group::get( array(
		'fetch_fields' => true
	) );

	if ( isset( $_GET['mode'] ) && isset( $_GET['group_id'] ) && 'add_field' == $_GET['mode'] )
		xprofile_admin_manage_field( $_GET['group_id'] );

	else if ( isset( $_GET['mode'] ) && isset( $_GET['group_id'] ) && isset( $_GET['field_id'] ) && 'edit_field' == $_GET['mode'] )
		xprofile_admin_manage_field( $_GET['group_id'], $_GET['field_id'] );

	else if ( isset( $_GET['mode'] ) && isset( $_GET['field_id'] ) && 'delete_field' == $_GET['mode'] )
		xprofile_admin_delete_field( $_GET['field_id'], 'field');

	else if ( isset( $_GET['mode'] ) && isset( $_GET['option_id'] ) && 'delete_option' == $_GET['mode'] )
		xprofile_admin_delete_field( $_GET['option_id'], 'option' );

	else if ( isset( $_GET['mode'] ) && 'add_group' == $_GET['mode'] )
		xprofile_admin_manage_group();

	else if ( isset( $_GET['mode'] ) && isset( $_GET['group_id'] ) && 'delete_group' == $_GET['mode'] )
		xprofile_admin_delete_group( $_GET['group_id'] );

	else if ( isset( $_GET['mode'] ) && isset( $_GET['group_id'] ) && 'edit_group' == $_GET['mode'] )
		xprofile_admin_manage_group( $_GET['group_id'] );

	else { ?>

	<div class="wrap">

		<?php screen_icon( 'users' ); ?>

		<h2>
			<?php _e( 'Profile Fields', 'buddypress'); ?>
			<a id="add_group" class="add-new-h2" href="users.php?page=bp-profile-setup&amp;mode=add_group"><?php _e( 'Add New Field Group', 'buddypress' ); ?></a>
		</h2>

		<p><?php echo sprintf( __( 'Fields in the "%s" group will appear on the signup page.', 'buddypress' ), esc_html( stripslashes( bp_get_option( 'bp-xprofile-base-group-name' ) ) ) ) ?></p>

		<form action="" id="profile-field-form" method="post">

			<?php

			wp_nonce_field( 'bp_reorder_fields', '_wpnonce_reorder_fields'        );
			wp_nonce_field( 'bp_reorder_groups', '_wpnonce_reorder_groups', false );

			if ( !empty( $message ) ) :
				$type = ( $type == 'error' ) ? 'error' : 'updated'; ?>

				<div id="message" class="<?php echo $type; ?> fade">
					<p><?php echo esc_html( esc_attr( $message ) ); ?></p>
				</div>

			<?php endif; ?>

			<div id="tabs">
				<ul id="field-group-tabs">

					<?php if ( !empty( $groups ) ) : foreach ( $groups as $group ) : ?>

						<li id="group_<?php echo $group->id; ?>"><a href="#tabs-<?php echo $group->id; ?>" class="ui-tab"><?php echo esc_attr( $group->name ); ?><?php if ( !$group->can_delete ) : ?> <?php _e( '(Primary)', 'buddypress'); endif; ?></a></li>

					<?php endforeach; endif; ?>

				</ul>

				<?php if ( !empty( $groups ) ) : foreach ( $groups as $group ) : ?>

					<noscript>
						<h3><?php echo esc_attr( $group->name ); ?></h3>
					</noscript>

					<div id="tabs-<?php echo $group->id; ?>" class="tab-wrapper">
						<div class="tab-toolbar">
							<div class="tab-toolbar-left">
								<a class="button-primary" href="users.php?page=bp-profile-setup&amp;group_id=<?php echo esc_attr( $group->id ); ?>&amp;mode=add_field"><?php _e( 'Add New Field', 'buddypress' ); ?></a>
								<a class="button edit" href="users.php?page=bp-profile-setup&amp;mode=edit_group&amp;group_id=<?php echo esc_attr( $group->id ); ?>"><?php _e( 'Edit Group', 'buddypress' ); ?></a>

								<?php if ( $group->can_delete ) : ?>

									<a class="confirm submitdelete deletion ajax-option-delete" href="users.php?page=bp-profile-setup&amp;mode=delete_group&amp;group_id=<?php echo esc_attr( $group->id ); ?>"><?php _e( 'Delete Group', 'buddypress' ); ?></a>

								<?php endif; ?>

							</div>
						</div>

						<fieldset id="<?php echo $group->id; ?>" class="connectedSortable field-group">

							<?php if ( $group->description ) : ?>

								<legend><?php echo esc_attr( $group->description ) ?></legend>

							<?php endif;

							if ( !empty( $group->fields ) ) :
								foreach ( $group->fields as $field ) {

									// Load the field
									$field = new BP_XProfile_Field( $field->id );

									$class = '';
									if ( !$field->can_delete )
										$class = ' core nodrag';

									/* This function handles the WYSIWYG profile field
									* display for the xprofile admin setup screen
									*/
									xprofile_admin_field( $field, $group, $class );

								} // end for

							else : // !$group->fields ?>

								<p class="nodrag nofields"><?php _e( 'There are no fields in this group.', 'buddypress' ); ?></p>

							<?php endif; // end $group->fields ?>

						</fieldset>
					</div>

				<?php endforeach; else : ?>

					<div id="message" class="error"><p><?php _e( 'You have no groups.', 'buddypress' ); ?></p></div>
					<p><a href="users.php?page=bp-profile-setup&amp;mode=add_group"><?php _e( 'Add New Group', 'buddypress' ); ?></a></p>

				<?php endif; ?>

				<div id="tabs-bottom">&nbsp;</div>
			</div>
		</form>
	</div>

<?php
	}
}

/**
 * Handles the adding or editing of groups.
 */
function xprofile_admin_manage_group( $group_id = null ) {
	global $message, $type;

	$group = new BP_XProfile_Group( $group_id );

	if ( isset( $_POST['save_group'] ) ) {
		if ( BP_XProfile_Group::admin_validate( $_POST ) ) {
			$group->name		= wp_filter_kses( $_POST['group_name'] );
			$group->description	= !empty( $_POST['group_description'] ) ? wp_filter_kses( $_POST['group_description'] ) : '';

			if ( !$group->save() ) {
				$message = __( 'There was an error saving the group. Please try again', 'buddypress' );
				$type    = 'error';
			} else {
				$message = __( 'The group was saved successfully.', 'buddypress' );
				$type    = 'success';

				if ( 1 == $group_id )
					bp_update_option( 'bp-xprofile-base-group-name', $group->name );

				do_action( 'xprofile_groups_saved_group', $group );
			}

			unset( $_GET['mode'] );
			xprofile_admin( $message, $type );

		} else {
			$group->render_admin_form( $message );
		}
	} else {
		$group->render_admin_form();
	}
}

/**
 * Handles the deletion of profile data groups.
 */
function xprofile_admin_delete_group( $group_id ) {
	global $message, $type;

	$group = new BP_XProfile_Group( $group_id );

	if ( !$group->delete() ) {
		$message = __( 'There was an error deleting the group. Please try again', 'buddypress' );
		$type    = 'error';
	} else {
		$message = __( 'The group was deleted successfully.', 'buddypress' );
		$type    = 'success';

		do_action( 'xprofile_groups_deleted_group', $group );
	}

	unset( $_GET['mode'] );
	xprofile_admin( $message, $type );
}

/**
 * Handles the adding or editing of profile field data for a user.
 */
function xprofile_admin_manage_field( $group_id, $field_id = null ) {
	global $bp, $wpdb, $message, $groups;

	$field           = new BP_XProfile_Field( $field_id );
	$field->group_id = $group_id;

	if ( isset( $_POST['saveField'] ) ) {
		if ( BP_XProfile_Field::admin_validate() ) {
			$field->name        = wp_filter_kses( $_POST['title'] );
			$field->description = !empty( $_POST['description'] ) ? wp_filter_kses( $_POST['description'] ) : '';
			$field->is_required = wp_filter_kses( $_POST['required'] );
			$field->type        = wp_filter_kses( $_POST['fieldtype'] );

			if ( !empty( $_POST["sort_order_{$field->type}"] ) )
				$field->order_by = wp_filter_kses( $_POST["sort_order_{$field->type}"] );

			$field->field_order = $wpdb->get_var( $wpdb->prepare( "SELECT field_order FROM {$bp->profile->table_name_fields} WHERE id = %d", $field_id ) );

			if ( !$field->field_order ) {
				$field->field_order = (int) $wpdb->get_var( $wpdb->prepare( "SELECT max(field_order) FROM {$bp->profile->table_name_fields} WHERE group_id = %d", $group_id ) );
				$field->field_order++;
			}

			// For new profile fields, set the $field_id. For existing profile fields,
			// this will overwrite $field_id with the same value.
			$field_id = $field->save();

			if ( !$field_id ) {
				$message = __( 'There was an error saving the field. Please try again', 'buddypress' );
				$type = 'error';

				unset( $_GET['mode'] );

				xprofile_admin( $message, $type );
			} else {
				$message = __( 'The field was saved successfully.', 'buddypress' );
				$type = 'success';

				if ( 1 == $field_id )
					bp_update_option( 'bp-xprofile-fullname-field-name', $field->name );

				if ( !empty( $_POST['default-visibility'] ) ) {
					bp_xprofile_update_field_meta( $field_id, 'default_visibility', $_POST['default-visibility'] );
				}

				if ( !empty( $_POST['allow-custom-visibility'] ) ) {
					bp_xprofile_update_field_meta( $field_id, 'allow_custom_visibility', $_POST['allow-custom-visibility'] );
				}

				unset( $_GET['mode'] );

				do_action( 'xprofile_fields_saved_field', $field );

				$groups = BP_XProfile_Group::get();
				xprofile_admin( $message, $type );
			}
		} else {
			$field->render_admin_form( $message );
		}
	} else {
		$field->render_admin_form();
	}
}

/**
 * Handles the deletion of a profile field (or field option)
 *
 * @since BuddyPress (1.0)
 * @global string $message The feedback message to show
 * @global $type The type of feedback message to show
 * @param int $field_id The field to delete
 * @param string $field_type The type of field being deleted
 * @param bool $delete_data Should the field data be deleted too?
 */
function xprofile_admin_delete_field( $field_id, $field_type = 'field', $delete_data = false ) {
	global $message, $type;

	// Switch type to 'option' if type is not 'field'
	// @todo trust this param
	$field_type  = ( 'field' == $field_type ) ? __( 'field', 'buddypress' ) : __( 'option', 'buddypress' );
	$field       = new BP_XProfile_Field( $field_id );

	if ( !$field->delete( (bool) $delete_data ) ) {
		$message = sprintf( __( 'There was an error deleting the %s. Please try again', 'buddypress' ), $field_type );
		$type    = 'error';
	} else {
		$message = sprintf( __( 'The %s was deleted successfully!', 'buddypress' ), $field_type );
		$type    = 'success';

		do_action( 'xprofile_fields_deleted_field', $field );
	}

	unset( $_GET['mode'] );
	xprofile_admin( $message, $type );
}

/**
 * Handles the ajax reordering of fields within a group
 */
function xprofile_ajax_reorder_fields() {

	// Check the nonce
	check_admin_referer( 'bp_reorder_fields', '_wpnonce_reorder_fields' );

	if ( empty( $_POST['field_order'] ) )
		return false;

	parse_str( $_POST['field_order'], $order );

	$field_group_id = $_POST['field_group_id'];

	foreach ( (array) $order['field'] as $position => $field_id ) {
		xprofile_update_field_position( (int) $field_id, (int) $position, (int) $field_group_id );
	}
}
add_action( 'wp_ajax_xprofile_reorder_fields', 'xprofile_ajax_reorder_fields' );

/**
 * Handles the reordering of field groups
 */
function xprofile_ajax_reorder_field_groups() {

	// Check the nonce
	check_admin_referer( 'bp_reorder_groups', '_wpnonce_reorder_groups' );

	if ( empty( $_POST['group_order'] ) )
		return false;

	parse_str( $_POST['group_order'], $order );

	foreach ( (array) $order['group'] as $position => $field_group_id ) {
		xprofile_update_field_group_position( (int) $field_group_id, (int) $position );
	}
}
add_action( 'wp_ajax_xprofile_reorder_groups', 'xprofile_ajax_reorder_field_groups' );

/**
 * Handles the WYSIWYG display of each profile field on the edit screen
 */
function xprofile_admin_field( $admin_field, $admin_group, $class = '' ) {
	global $field;

	$field = $admin_field; ?>

	<fieldset id="field_<?php echo esc_attr( $field->id ); ?>" class="sortable<?php echo ' ' . $field->type; if ( !empty( $class ) ) echo ' ' . $class; ?>">
		<legend><span><?php bp_the_profile_field_name(); ?> <?php if( !$field->can_delete ) : ?> <?php _e( '(Primary)', 'buddypress' ); endif; ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(Required)', 'buddypress' ) ?><?php endif; ?></span></legend>
		<div class="field-wrapper">

			<?php
			if ( in_array( $field->type, array_keys( bp_xprofile_get_field_types() ) ) ) {
				$field_type = bp_xprofile_create_field_type( $field->type );
				$field_type->admin_field_html();

			} else {
				do_action( 'xprofile_admin_field', $field, 1 );
			}
			?>

			<?php if ( $field->description ) : ?>

				<p class="description"><?php echo esc_attr( $field->description ); ?></p>

			<?php endif; ?>

			<div class="actions">
				<a class="button edit" href="users.php?page=bp-profile-setup&amp;group_id=<?php echo esc_attr( $admin_group->id ); ?>&amp;field_id=<?php echo esc_attr( $field->id ); ?>&amp;mode=edit_field"><?php _e( 'Edit', 'buddypress' ); ?></a>

				<?php if ( $field->can_delete ) : ?>

					<a class="confirm submit-delete deletion" href="users.php?page=bp-profile-setup&amp;field_id=<?php echo esc_attr( $field->id ); ?>&amp;mode=delete_field"><?php _e( 'Delete', 'buddypress' ); ?></a>

				<?php endif; ?>
			</div>
		</div>
	</fieldset>

<?php
}

/**
 * Print <option> elements containing the xprofile field types.
 *
 * @param string $select_field_type The name of the field type that should be selected. Will defaults to "textbox" if NULL is passed.
 * @since BuddyPress (2.0.0)
 */
function bp_xprofile_admin_form_field_types( $select_field_type ) {
	$categories = array();

	if ( is_null( $select_field_type ) ) {
		$select_field_type = 'textbox';
	}

	// Sort each field type into its category
	foreach ( bp_xprofile_get_field_types() as $field_name => $field_class ) {
		$field_type_obj = new $field_class;
		$the_category   = $field_type_obj->category;

		// Fallback to a catch-all if category not set
		if ( ! $the_category ) {
			$the_category = _x( 'Other', 'xprofile field type category', 'buddypress' );
		}

		if ( isset( $categories[$the_category] ) ) {
			$categories[$the_category][] = array( $field_name, $field_type_obj );
		} else {
			$categories[$the_category] = array( array( $field_name, $field_type_obj ) );
		}
	}

	// Sort the categories alphabetically. ksort()'s SORT_NATURAL is only in PHP >= 5.4 :((
	uksort( $categories, 'strnatcmp' );

	// Loop through each category and output form <options>
	foreach ( $categories as $category => $fields ) {
		printf( '<optgroup label="%1$s">', esc_attr( $category ) );  // Already i18n'd in each profile type class

		// Sort these fields types alphabetically
		uasort( $fields, create_function( '$a, $b', 'return strnatcmp( $a[1]->name, $b[1]->name );' ) );

		foreach ( $fields as $field_type_obj ) {
			$field_name     = $field_type_obj[0];
			$field_type_obj = $field_type_obj[1];

			printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $field_name ), selected( $select_field_type, $field_name, false ), esc_html( $field_type_obj->name ) );
		}

		printf( '</optgroup>' );
	}
}

if ( ! class_exists( 'BP_XProfile_User_Admin' ) ) :
/**
 * Load xProfile Profile admin area.
 *
 * @package BuddyPress
 * @subpackage xProfileAdministration
 *
 * @since BuddyPress (2.0.0)
 */
class BP_XProfile_User_Admin {

	/**
	 * Setup xProfile User Admin.
	 *
	 * @access public
	 * @since BuddyPress (2.0.0)
	 *
	 * @uses buddypress() to get BuddyPress main instance
	 */
	public static function register_xprofile_user_admin() {
		if( ! is_admin() )
			return;

		$bp = buddypress();

		if( empty( $bp->profile->admin ) ) {
			$bp->profile->admin = new self;
		}

		return $bp->profile->admin;
	}

	/**
	 * Constructor method.
	 *
	 * @access public
	 * @since BuddyPress (2.0.0)
	 */
	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Set admin-related actions and filters.
	 *
	 * @access private
	 * @since BuddyPress (2.0.0)
	 */
	private function setup_actions() {

		/** Actions ***************************************************/

		// Register the metabox in Member's community admin profile
		add_action( 'bp_members_admin_xprofile_metabox', array( $this, 'register_metaboxes' ), 10, 3 );

		// Saves the profile actions for user ( avatar, profile fields )
		add_action( 'bp_members_admin_update_user',      array( $this, 'user_admin_load' ),    10, 4 );

	}

	/**
	 * Register the xProfile metabox on Community Profile admin page.
	 *
	 * @access public
	 * @since BuddyPress (2.0.0)
	 *
	 * @param int $user_id ID of the user being edited.
	 * @param string $screen_id Screen ID to load the metabox in.
	 * @param object $stats_metabox Context and priority for the stats metabox.
	 */
	public function register_metaboxes( $user_id = 0, $screen_id = '', $stats_metabox = null ) {

		if ( empty( $screen_id ) ) {
			$screen_id = buddypress()->members->admin->user_page;
		}

		if ( empty( $stats_metabox ) ) {
			$stats_metabox = new StdClass();
		}

		// Moving the Stats Metabox
		$stats_metabox->context = 'side';
		$stats_metabox->priority = 'low';

		// Each Group of fields will have his own metabox
		if ( false == bp_is_user_spammer( $user_id ) && bp_has_profile( array( 'fetch_fields' => false ) ) ) {
			while ( bp_profile_groups() ) : bp_the_profile_group();
				add_meta_box( 'bp_xprofile_user_admin_fields_' . sanitize_key( bp_get_the_profile_group_slug() ), esc_html( bp_get_the_profile_group_name() ), array( &$this, 'user_admin_profile_metaboxes' ), $screen_id, 'normal', 'core', array( 'profile_group_id' => absint( bp_get_the_profile_group_id() ) ) );
			endwhile;

		// if a user has been mark as a spammer, remove BP data
		} else {
			add_meta_box( 'bp_xprofile_user_admin_empty_profile', _x( 'User marked as a spammer', 'xprofile user-admin edit screen', 'buddypress' ), array( &$this, 'user_admin_spammer_metabox' ), $screen_id, 'normal', 'core' );
		}

		// Avatar Metabox
		add_meta_box( 'bp_xprofile_user_admin_avatar',  _x( 'Avatar', 'xprofile user-admin edit screen', 'buddypress' ), array( &$this, 'user_admin_avatar_metabox' ), $screen_id, 'side', 'low' );

	}

	/**
	 * Save the profile fields in Members community profile page.
	 *
	 * Loaded before the page is rendered, this function is processing form
	 * requests.
	 *
	 * @access public
	 * @since BuddyPress (2.0.0)
	 */
	public function user_admin_load( $doaction = '', $user_id = 0, $request = array(), $redirect_to = '' ) {

		// Eventually delete avatar
		if ( 'delete_avatar' == $doaction ) {

			check_admin_referer( 'delete_avatar' );

			$redirect_to = remove_query_arg( '_wpnonce', $redirect_to );

			if ( bp_core_delete_existing_avatar( array( 'item_id' => $user_id ) ) ) {
				$redirect_to = add_query_arg( 'updated', 'avatar', $redirect_to );
			} else {
				$redirect_to = add_query_arg( 'error', 'avatar', $redirect_to );
			}

			bp_core_redirect( $redirect_to );

		// Update profile fields
		} else {
			// Check to see if any new information has been submitted
			if ( isset( $_POST['field_ids'] ) ) {

				// Check the nonce
				check_admin_referer( 'edit-bp-profile_' . $user_id );

				// Check we have field ID's
				if ( empty( $_POST['field_ids'] ) ) {
					$redirect_to = add_query_arg( 'error', '1', $redirect_to );
					bp_core_redirect( $redirect_to );
				}

				/**
				 * Unlike front-end edit-fields screens, the wp-admin/profile displays all 
				 * groups of fields on a single page, so the list of field ids is an array 
				 * gathering for each group of fields a distinct comma separated list of ids. 
				 * As a result, before using the wp_parse_id_list() function, we must ensure 
				 * that these ids are "merged" into a single comma separated list.
				 */
				$merge_ids = join( ',', $_POST['field_ids'] );

				// Explode the posted field IDs into an array so we know which fields have been submitted
				$posted_field_ids = wp_parse_id_list( $merge_ids );
				$is_required      = array();

				// Loop through the posted fields formatting any datebox values then validate the field
				foreach ( (array) $posted_field_ids as $field_id ) {
					if ( ! isset( $_POST['field_' . $field_id] ) ) {
						if ( ! empty( $_POST['field_' . $field_id . '_day'] ) && ! empty( $_POST['field_' . $field_id . '_month'] ) && ! empty( $_POST['field_' . $field_id . '_year'] ) ) {
							// Concatenate the values
							$date_value =   $_POST['field_' . $field_id . '_day'] . ' ' . $_POST['field_' . $field_id . '_month'] . ' ' . $_POST['field_' . $field_id . '_year'];

							// Turn the concatenated value into a timestamp
							$_POST['field_' . $field_id] = date( 'Y-m-d H:i:s', strtotime( $date_value ) );
						}
					}

					$is_required[ $field_id ] = xprofile_check_is_required_field( $field_id );
					if ( $is_required[ $field_id ] && empty( $_POST['field_' . $field_id] ) ) {
						$redirect_to = add_query_arg( 'error', '2', $redirect_to );
						bp_core_redirect( $redirect_to );
					}
				}

				// Set the errors var
				$errors = false;

				// Now we've checked for required fields, let's save the values.
				foreach ( (array) $posted_field_ids as $field_id ) {

					// Certain types of fields (checkboxes, multiselects) may come through empty. Save them as an empty array so that they don't get overwritten by the default on the next edit.
					$value = isset( $_POST['field_' . $field_id] ) ? $_POST['field_' . $field_id] : '';

					if ( ! xprofile_set_field_data( $field_id, $user_id, $value, $is_required[ $field_id ] ) ) {
						$errors = true;
					} else {
						do_action( 'xprofile_profile_field_data_updated', $field_id, $value );
					}

					// Save the visibility level
					$visibility_level = ! empty( $_POST['field_' . $field_id . '_visibility'] ) ? $_POST['field_' . $field_id . '_visibility'] : 'public';
					xprofile_set_field_visibility_level( $field_id, $user_id, $visibility_level );
				}

				do_action( 'xprofile_updated_profile', $user_id, $posted_field_ids, $errors );

				// Set the feedback messages
				if ( ! empty( $errors ) ) {
					$redirect_to = add_query_arg( 'error', '3', $redirect_to );
				} else {
					$redirect_to = add_query_arg( 'updated', '1', $redirect_to );
				}

				bp_core_redirect( $redirect_to );
			}
		}
	}

	/**
	 * Render the xprofile metabox for Community Profile screen.
	 *
	 * @access public
	 * @since BuddyPress (2.0.0)
	 *
	 * @param WP_User $user The WP_User object for the user being edited.
	 */
	public function user_admin_profile_metaboxes( $user = null, $args = array() ) {

		if ( empty( $user->ID ) ) {
			return;
		}

		$r = bp_parse_args( $args['args'], array(
			'profile_group_id' => 0,
			'user_id'          => $user->ID
		), 'bp_xprofile_user_admin_profile_loop_args' );

		// We really need these args
		if ( empty( $r['profile_group_id'] ) || empty( $r['user_id'] ) ) {
			return;
		}

		if ( bp_has_profile( $r ) ) :
			while ( bp_profile_groups() ) : bp_the_profile_group(); ?>
				<input type="hidden" name="field_ids[]" id="<?php echo esc_attr( 'field_ids_' . bp_get_the_profile_group_slug() ); ?>" value="<?php echo esc_attr( bp_get_the_profile_group_field_ids() ); ?>" />

				<?php if ( bp_get_the_profile_group_description() ) : ?>
					<p class="description"><?php bp_the_profile_group_description(); ?></p>
				<?php
				endif;

				while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

					<div<?php bp_field_css_class( 'bp-profile-field' ); ?>>
						<?php
						$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
						$field_type->edit_field_html( array( 'user_id' => $r['user_id'] ) );

						if ( bp_get_the_profile_field_description() ) : ?>
							<p class="description"><?php bp_the_profile_field_description(); ?></p>
						<?php endif;

						do_action( 'bp_custom_profile_edit_fields_pre_visibility' );
						$can_change_visibility = bp_current_user_can( 'bp_xprofile_change_field_visibility' );
						?>

						<p class="field-visibility-settings-<?php echo $can_change_visibility ? 'toggle' : 'notoggle'; ?>" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id(); ?>">
							<?php
							printf( __( 'This field can be seen by: <span class="%s">%s</span>', 'buddypress' ), esc_attr( 'current-visibility-level' ), bp_get_the_profile_field_visibility_level_label() );

							if ( $can_change_visibility ) : ?>
								 <a href="#" class="button visibility-toggle-link"><?php _e( 'Change', 'buddypress' ); ?></a>
							<?php endif; ?>
						</p>

						<?php if ( $can_change_visibility ) : ?>
							<div class="field-visibility-settings" id="field-visibility-settings-<?php bp_the_profile_field_id() ?>">
								<fieldset>
									<legend><?php _e( 'Who can see this field?', 'buddypress' ); ?></legend>
									<?php bp_profile_visibility_radio_buttons(); ?>
								</fieldset>
								<a class="button field-visibility-settings-close" href="#"><?php _e( 'Close', 'buddypress' ); ?></a>
							</div>
						<?php endif;

						do_action( 'bp_custom_profile_edit_fields' ); ?>
					</div>

				<?php
				endwhile; // bp_profile_fields()

			endwhile; // bp_profile_groups()
		endif;
	}

	/**
	 * Render the fallback metabox in case a user has been marked as a spammer.
	 *
	 * @access public
	 * @since BuddyPress (2.0.0)
	 *
	 * @param WP_User $user The WP_User object for the user being edited.
	 */
	public function user_admin_spammer_metabox( $user = null ) {
		?>
		<p><?php printf( __( '%s has been marked as a spammer. All BuddyPress data associated with the user has been removed', 'buddypress' ), esc_html( bp_core_get_user_displayname( $user->ID ) ) ) ;?></p>
		<?php
	}

	/**
	 * Render the Avatar metabox to moderate inappropriate images.
	 *
	 * @access public
	 * @since BuddyPress (2.0.0)
	 *
	 * @param WP_User $user The WP_User object for the user being edited.
	 */
	public function user_admin_avatar_metabox( $user = null ) {

		if ( empty( $user->ID ) ) {
			return;
		}

		$args = array(
			'item_id' => $user->ID,
			'object'  => 'user',
			'type'    => 'full',
			'title'   => $user->display_name
		);

		?>

		<div class="avatar">

			<?php echo bp_core_fetch_avatar( $args ); ?>

			<?php if ( bp_get_user_has_avatar( $user->ID ) ) :

				$query_args = array(
					'user_id' => $user->ID,
					'action'  => 'delete_avatar'
				);

				if ( ! empty( $_REQUEST['wp_http_referer'] ) )
					$query_args['wp_http_referer'] = urlencode( wp_unslash( $_REQUEST['wp_http_referer'] ) );

					$community_url = add_query_arg( $query_args, buddypress()->members->admin->edit_profile_url );
					$delete_link   = wp_nonce_url( $community_url, 'delete_avatar' ); ?>

				<a href="<?php echo esc_url( $delete_link ); ?>" title="<?php esc_attr_e( 'Delete Avatar', 'buddypress' ); ?>" class="bp-xprofile-avatar-user-admin"><?php esc_html_e( 'Delete Avatar', 'buddypress' ); ?></a></li>

			<?php endif; ?>

		</div>
		<?php
	}

}
endif; // class_exists check

// Load the xprofile user admin
add_action( 'bp_init', array( 'BP_XProfile_User_Admin', 'register_xprofile_user_admin' ), 11 );
