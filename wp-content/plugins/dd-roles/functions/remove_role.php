<?php
/**
 * Created by PhpStorm.
 * User: dijkstradesign
 * Date: 04-12-13
 * Time: 21:49
 */

// do not generate any output here

//Need a list of roles and to remove and set all the users back to the subscribe role!

$wp_roles = new WP_Roles();
$wp_roles->remove_role("DD_Role_test");