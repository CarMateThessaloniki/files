<?php
/**
 * Created by PhpStorm.
 * User: dijkstradesign
 * Date: 04-12-13
 * Time: 21:49
 */



if ( ! isset( $GLOBALS[ 'wp_roles' ] ) )
    $GLOBALS[ 'wp_roles' ] = new WP_Roles();

// do not generate any output here
$dish_caps = array(
    'delete_others_ws_dishes' => false,
    'delete_private_ws_dishes' => false,
    'delete_published_ws_dishes' => false,
    'delete_ws_dish' => true,
    'delete_ws_dishes' => false,

    'edit_others_ws_dishes' => false,
    'edit_private_ws_dishes' => false,
    'edit_published_ws_dishes' => true,
    'edit_ws_dish' => true,
    'edit_ws_dishes' => true,

    'publish_ws_dishes' => true,

    'read' => true,
    'read_private_ws_dishes' => true,
    'read_published_ws_dishes' => false,
    'read_ws_dish' => false,
    'read_others_ws_dishes' => false,

    'edit_others_pages_ws_dish' => false,


    'delete_others_ws_drinks' => false,
    'delete_private_ws_drinks' => false,
    'delete_published_ws_drinks' => false,
    'delete_ws_drink' => true,
    'delete_ws_drinks' => false,

    'edit_others_ws_drinks' => false,
    'edit_private_ws_drinks' => false,
    //'edit_published_ws_drinks' => true,
    'edit_ws_drink' => true,
    'edit_ws_drinks' => true,

    'publish_ws_drinks' => true,

    'read_private_ws_drinks' => true,
    'read_published_ws_drinks' => false,
    'read_ws_drink' => false,
    'read_others_ws_drinks' => false,

    'upload_files' => true,
);
$DD_Role_test = $GLOBALS[ 'wp_roles' ]->add_role( 'DD_Role_test', __( 'testrole', 'cftp_band' ), $dish_caps );