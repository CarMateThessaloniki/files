<?php
/**
 * Created by PhpStorm.
 * User: dijkstradesign
 * Date: 04-12-13
 * Time: 21:50
 */

//when role will removed all users of this role needs to set back to subscriber



$users = get_users('role=DD_Role_test');
foreach ($users as $user) {

    // Remove role
    $user->remove_role( 'DD_Role_test' );

    // Add role
    $user->add_role( 'subscriber' );


    //echo '<li>' . $user->user_email . '</li>';
}