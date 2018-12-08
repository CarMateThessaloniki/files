<?php
/**
 * Created by PhpStorm.
 * User: dijkstradesign
 * Date: 06-12-13
 * Time: 21:25
 */

// Same handler function...
add_action( 'wp_ajax_verify_and_add', 'verify_and_add_callback' );
add_action( 'wp_ajax_deleteRole', 'deleteRole_callback' );
add_action( 'wp_ajax_migrateUsers', 'migrateUsers_callback' );
add_action( 'wp_ajax_cleanUp', 'cleanUp_callback' );
add_action( 'wp_ajax_changeCapState', 'changeCapState_callback' );


function verify_and_add_callback() {
    global $wp_roles;
    $unique = true;
    $new_displayName = $_POST['new_role_display_name'];
    $new_role_id = str_replace(' ', '_', $new_displayName); //replace whitespace
    $new_role_id = strtolower($new_role_id); //make all lowercase like: administrator, visitor_hello which can be verified with the role's name.
    $lastcharacter = $new_displayName[strlen($new_displayName)-1];
    $firstcharacter = substr($new_displayName, 0, 1);
    $rolesArray = get_option('dd_roles');
    $roles = isset($wp_roles) ? $wp_roles->get_names() : '';

    foreach ($roles as $role_id => $role_name) {
        if($new_role_id == $role_id){
            $unique = false;
        }
    }

    if($firstcharacter == " " || $lastcharacter == " "){
            $unique = false;
    }

    $checkName = str_replace(' ', '', $new_displayName); //replace whitespace

    if (!ctype_alnum($checkName)) {
        //check if NOT alphanumeric....So don't pass
        $unique = false;
    }

    if ($unique){

        $rolesArray ? array_push($rolesArray, $new_role_id) : $rolesArray = array($new_role_id);

        if ( ! isset( $GLOBALS[ 'wp_roles' ] ) ){
            $GLOBALS[ 'wp_roles' ] = new WP_Roles();
        }
        update_option( 'dd_roles', $rolesArray );
        $GLOBALS[ 'wp_roles' ]->add_role( $new_role_id, __( $new_displayName, 'cftp_band' ) );
    }

    echo $unique;

    die();
}


function deleteRole_callback() {

    $role_id = $_POST['role_id'];
    $rolesArray = get_option('dd_roles');
    $rolesArray = array_diff($rolesArray, array($role_id));

    userMigrate($role_id,'');
    update_option( 'dd_roles', $rolesArray );

    $wp_roles = new WP_Roles();
    $wp_roles->remove_role($role_id);

    die();
}

function changeCapState_callback() {


    $state = $_POST['state'];
    $state = filter_var($state, FILTER_VALIDATE_BOOLEAN);
    $capname = $_POST['capname'];
    $role_id = $_POST['role_id'];
    $role = get_role( $role_id);

    if($state){
        $role->add_cap( $capname );
        echo 'dit is de state:'.$state;
        echo 'save';
    }
    else{
        $role->remove_cap( $capname );
        echo 'dit is de state:'.$state;
        echo 'delete';
    }
    die();
}

function migrateUsers_callback() {
    $fromRole = $_POST['fromRole'];
    $toRole = $_POST['toRole'];
    userMigrate($fromRole,$toRole);

    die();
}


function userMigrate($roleName,$toRole){

    if(!$toRole){
        $toRole = 'subscriber';
    }

    $usersRole = 'role='.$roleName.'';
    $users = get_users($usersRole);
    foreach ($users as $user) {

        // Remove role
        $user->remove_role( $roleName );

        // Add role
        $user->add_role( $toRole );
    }
}

function cleanUp_callback(){

    $delcaps = $_POST['delcaps'];

    //array for feature: to clean up multi-select. For now 1 by 1
    $delete_caps = array($delcaps);
    global $wp_roles;
    foreach ($delete_caps as $cap) {
        foreach (array_keys($wp_roles->roles) as $role) {
            $wp_roles->remove_cap($role, $cap);
        }
    }
    echo 'caps deleted';

    die();
}