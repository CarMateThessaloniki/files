<?php
/**
 * Created by PhpStorm.
 * User: dijkstradesign
 * Date: 04-12-13
 * Time: 20:28
 */

add_action('admin_menu', 'DD_rolesSettings');

function DD_rolesSettings() {

    add_users_page('DD Roles', 'DD Roles', 'manage_options', 'dd-roles.php', 'rolesSettingsPage');
    add_action( 'admin_init', 'register_dd_roles_settings' );
}

function rolesSettingsPage(){
    ?>
<div class="wrap">
    <div id="icon-edit" class="icon32 icon32-posts-ws_drinks"><br></div><h2>DD Roles</h2>
    <div id="lost-connection-notice" class="error hidden below-h2">
        <p><span class="spinner"></span> <strong>Connection lost.</strong> Saving has been disabled until you’re reconnected.	<span class="hide-if-no-sessionstorage">We’re backing up this post in your browser, just in case.</span>
        </p>
    </div>
    <div id="setting-error" class="error duplicated settings-error hidden">
        <p><strong>Duplicated, Empty or too Fancy!</strong> The role name is already excisting, empty or contains strange characters.</p>
    </div>
    <div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">
    <!-- /post-body-content -->

    <div id="postbox-container-1" class="postbox-container dd-sidebar">
    <div id="side-sortables" class="meta-box-sortables ui-sortable">
        <div id="submitdiv" class="postbox ">
            <div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Add New Role</span></h3>
            <div class="inside">
                <div class="submitrole" id="submitrole">
                    <form method="post" action="options.php">

                        <?php settings_fields('dd_roles_settings'); ?>
                        <?php do_settings_sections('dd_roles_settings'); ?>

                        <div id="minor-publishing">
                            <div class="misc-pub-section">
                                <input type="text" class="widefat dd-new-role" name="dd-new-role" placeholder="New role"/>
                                <input type="hidden" class="dd_roles" name="dd_roles" value="<?php print_r( get_option('dd_roles')); ?>">
                            </div>

                            <div class="clear"></div>
                        </div>

                        <div id="major-publishing-actions">
                            <div id="publishing-action">
                                <span class="spinner"></span>
                                <button type="button" class="js-newRole button button-primary">Make Role</button>
                                <button type="submit" style="display: none" class="js-newRole-submit button button-primary">Make Role</button>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        $onlyAdminRoles = onlyAdminRoles();
        if (!$onlyAdminRoles){
        ?>
        <div id="migratediv" class="postbox ">
            <div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Migrate Users</span></h3>
            <div class="inside">
                <div class="submitmigrate" id="submitmigrate">
                    <form method="post" action="options.php">
                        <div id="minor-publishing">
                            <div class="misc-pub-section">
                                <select name="fromRole" id="role" class="widefat fromRole">
                                    <?php
                                        global $wp_roles;
                                        $roles = $wp_roles->get_names();

                                            foreach ($roles as $role_id => $role) {

                                                if($role_id != 'administrator'){

                                                    $totalUsers = get_totalUsers($role_id);
                                                   // echo $totalUsers;
                                                    if($totalUsers){
                                                        $totalUsers = '('.ngettext($totalUsers.' '.__('User'), $totalUsers.' '.__('Users'), $totalUsers).')'; // prints "4 cats"
                                                        echo '<option value="'.$role_id.'">'.translate_user_role( $role ).' '.$totalUsers.'</option>';
                                                    }
                                                }
                                            }
                                    ?>
                                </select>
                            </div>
                            <div class="misc-pub-section migrate_to">
                                to
                            </div>
                            <div class="misc-pub-section">
                                <select name="toRole" id="role" class="widefat toRole">
                                    <?php
                                        global $wp_roles;
                                        $roles = $wp_roles->get_names();
                                        foreach ($roles as $role_id => $role) {
                                            if($role_id != 'administrator'){
                                                $totalUsers = get_totalUsers($role_id);
                                                $totalUsers = $totalUsers? '('.ngettext($totalUsers.' '.__('User'), $totalUsers.' '.__('Users'), $totalUsers).')' : ''; // prints "4 cats"
                                                echo '<option value="'.$role_id.'">'.translate_user_role( $role ).' '.$totalUsers.'</option>';
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div id="major-publishing-actions">
                            <div id="publishing-action">
                                <span class="spinner"></span>
                                <button type="submit" class="button button-primary hidden js-migrate-submit">Migrate</button>
                                <button type="button" class="button button-primary js-migrate">Migrate</button>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        }

        $CustomCapTrue = customCapBoolean();

        if($CustomCapTrue){
            ?>
            <div id="cleanUpdiv" class="postbox ">
                <div class="handlediv" title="Click to toggle"><br></div>
                <h3 class="hndle"><span>Clean Up</span></h3>
                <div class="inside">
                    <div class="cleanupCap" id="cleanupCap">
                        <form method="post" action="options.php">
                            <div id="minor-publishing">
                                <div class="misc-pub-section">
                                    <p>
                                        Through DD-roles you maybe notice that a lot of plugins
                                        leave their marks when they are inactive or deleted. By using this function you can cleanup the superfluous capabilities, <br><br>but be sure!
                                    </p>
                                    <select name="deleteCap" id="role" class="widefat deleteCap">
                                        <?php

                                            $default_caps = get_default_caps();
                                            $all_caps =  $rol_capabilities = get_caps_names('administrator');

                                            foreach( $all_caps as $capability ) {

                                                $capabilityDisplay = str_replace('_',' ',$capability);
                                                $capabilityDisplay = ucfirst($capabilityDisplay);

                                                if(!in_array($capability ,$default_caps)){
                                                    echo '<option value="'.$capability.'">'.$capabilityDisplay.'</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div id="major-publishing-actions">
                                <div id="publishing-action">
                                    <span class="spinner"></span>
                                    <button type="button" class="js-cleanUp button button-primary">Delete Cap</button>
                                    <button type="submit" style="display: none" class="js-cleanUp-submit button button-primary">Delete Cap</button>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    <?php
        }
    ?>
        <div id="donate" class="postbox closed">
            <div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Donate to Developer</span></h3>
            <div class="inside">
                <div class="donate" id="submitrole">
                    <div  class="misc-pub-section">
                        <p>
                            This plugin is distributed for free under a GNU General Public License or a GPL compatible license. This software is free as in beer and as in freedom; however, donations help to pay for time I could have spent on billable projects. Donations allow me to spend more time developing these free projects instead of working on billable projects. Help support your favorite plugins by donating to help pay for espresso to keep me awake. (I do most of my open source work while at the end of the day.).
                        </p>
                        <p>
                            Donations allow me to spend more time developing all aspects of this plugin and providing the free support that so many people have enjoyed. (It also keeps me motivated: it is a great feeling for someone to be willing to pay — even a few Euros — for something they can get for free.) So be kind and please consider donating. Any amount is appreciated whether it be € 3.00 (price of a Dutch Beer) or more ;).
                        </p>
                        <h5>
                            Wouter Dijkstra
                        </h5>
                    </div>
                    <div id="major-publishing-actions">
                        <div id="publishing-action">
                            <span class="spinner"></span>
                            <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=5V2C94HQAN63C&lc=US&item_name=Dijkstra%20Design&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted" target="_blank" class="beer button button-primary" title="Donate the developer">Donate</a>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>
    <div id="postbox-container-2" class="postbox-container">
        <div id="normal-sortables" class="meta-box-sortables ui-sortable">
            <table class="wp-list-table widefat fixed posts" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col" id="title" class="manage-column column-title" style="">
                                <span><?php _e('Role'); ?></span>
                        </th>
                        <th scope="col" id="author" class="manage-column column-author" style=""><?php _e('Users'); ?></th>
                        <th scope="col" id="date" class="manage-column column-date sortable asc" style=""><?php _e('Capabilities'); ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th scope="col" class="manage-column column-title" style="">
                                <span><?php _e('Role'); ?></span>
                        </th>
                        <th scope="col" class="manage-column column-author" style=""><?php _e('Users'); ?></th>
                        <th scope="col" id="date" class="manage-column column-date sortable asc" style=""><?php _e('Capabilities'); ?></th>
                    </tr>
                </tfoot>
                <tbody id="the-list">
                <?php

                global $wp_roles;
                $roles = $wp_roles->get_names();
                $rolcount = 0;
                foreach ($roles as $role_id => $role) {

                    $rowZebra = (($rolcount++)%2) ? 'even' : 'odd alternate';
                    $displayName = translate_user_role( $role );
                    $wp_default_roles = array('administrator','editor','author','contributor','subscriber');
                    $is_default = in_array($role_id, $wp_default_roles) ? true : false;

                    ?>

                    <tr id="role-<?php echo $role_id ?>" class="role-<?php echo $role_id.' '.$rowZebra;  if($is_default){echo ' defaultRole';}?> roleRow" valign="top">

                        <td class="post-title role-title column-title">
                            <strong><a class="row-title editRole" href="#" title="<?php echo $displayName;?>"><?php _e($displayName);?></a></strong>
                            <input type="hidden" class="role_id" value="<?php echo $role_id ?>">
                            <?php

                            $viewName = __('View');
                            $editName = __('Edit');
                            $closeName = __('Close');
                            $trashName = __('Delete');

                            $CustomCapTrue = customCapBoolean();

                            $className = !$is_default | $CustomCapTrue ? 'editRole' : 'viewRole';
                            $titleName = $role_id != 'administrator' && !$is_default | $CustomCapTrue ? 'Edit this item' : 'View this item';
                            $linkText = $role_id == 'administrator' | !$CustomCapTrue && $is_default ? $viewName : $editName;
                            $secondItem = !$is_default ? '<span class="trash"><a class="deleteRole" title="Delete this role: Users will be migrate to Subscribers" href="#">'.$trashName.'</a></span>' : '<span>'.__("Default").' Wordpress '.__("Role").'</span';

                            ?>
                            <div><span class="edit"><a class="<?php echo $className ?> openInfo" href="#" data-OtherText="<?php echo $closeName ?>" title="<?php echo $titleName ?>"><?php echo $linkText ?></a> | </span><?php echo $secondItem ?></div>
                            <div class="capabilitiesBlock hidden">
                                <?php

                                $read = $levels = $pages = $comments = $posts = $media = $users = $themes = $plugins = $tools = $settings = $others = array();
                                $default_caps = get_default_caps();

                                foreach( $default_caps as $capability ) {

                                    if ($capability == 'read' || strpos($capability,'edit_dashboard') !== false ) {
                                        $read[] = $capability;
                                    }
                                    elseif (strpos($capability,'level') !== false) {
                                        $levels[] = $capability;
                                    }
                                    elseif (strpos($capability,'pages') !== false) {
                                        $pages[] = $capability;
                                    }
                                    elseif (strpos($capability,'comments') !== false) {
                                        $comments[] = $capability;
                                    }
                                    elseif (strpos($capability,'posts') !== false || strpos($capability,'manage_categories') !== false ) {
                                        $posts[] = $capability;
                                    }
                                    elseif (strpos($capability,'users') !== false) {
                                        $users[] = $capability;
                                    }
                                    elseif (strpos($capability,'theme') !== false) {
                                        $themes[] = $capability;
                                    }
                                    elseif (strpos($capability,'plugins') !== false) {
                                        $plugins[] = $capability;
                                    }
                                    elseif (strpos($capability,'port') !== false) {
                                        $tools[] = $capability;
                                    }
                                    elseif (strpos($capability,'files') !== false) {
                                        $media[] = $capability;
                                    }
                                    elseif (strpos($capability,'manage_options') !== false) {
                                        $settings[] = $capability;
                                    }
                                    elseif (strpos($capability,'unfiltered') !== false || strpos($capability,'links') !== false || strpos($capability,'core') !== false) {
                                        $others[] = $capability;
                                    }
                                }

                                $media = array_reverse($media);
                                $levels = array_reverse($levels);
                                $rol_capabilities = get_caps_names($role_id);


                                $read2 = '{
                                    "title": "Dashboard & Profile",
                                    "desc": "Allows access to Administration Panel options \"Dashboard\" and \"Profile\" Allows access to the configuration parameters of widgets which included to the dashboard.",
                                    "capabilities":'.json_encode($read).'
                                }';

                                $posts2 = '{
                                    "title": "Posts",
                                    "desc": "Allows access to Administration Panel options \"Posts\".",
                                    "capabilities":'.json_encode($posts).'
                                }';

                                $media2 = '{
                                    "title": "Media",
                                    "desc": "Allows access to Administration Panel options \"Media\".",
                                    "capabilities":'.json_encode($media).'
                                }';
                                $pages2 = '{
                                    "title": "Pages",
                                    "desc": "Allows access to Administration Panel options \"Pages\".",
                                    "capabilities":'.json_encode($pages).'
                                }';
                                $comments2 = '{
                                    "title": "Comments",
                                    "desc": "Allows access to Administration Panel options \"Comments\" (\"Edit posts\" needs to be checked in Posts). To edit comments \"Edit others posts\" and \"Edit published posts\" need to be checked in Posts.",
                                    "capabilities":'.json_encode($comments).'
                                }';
                                $themes2 = '{
                                    "title": "Appearance",
                                    "desc": "Allows access to Administration Panel options \"Appearance\".",
                                    "capabilities":'.json_encode($themes).'
                                }';
                                $plugins2 = '{
                                    "title": "Plugins",
                                    "desc": "Allows access to Administration Panel options \"Plugins\".",
                                    "capabilities":'.json_encode($plugins).'
                                }';
                                $users2 = '{
                                    "title": "Users",
                                    "desc": "Allows access to Administration Panel options \"Users\".",
                                    "capabilities":'.json_encode($users).'
                                }';
                                $tools2 = '{
                                    "title": "Tools",
                                    "desc": "Allows access to Administration Panel options \"Tools\".",
                                    "capabilities":'.json_encode($tools).'
                                }';
                                $settings2 = '{
                                    "title": "Settings",
                                    "desc": "Allows access to Administration Panel options \"Settings\".",
                                    "capabilities":'.json_encode($settings).'
                                }';
                                $others2 = '{
                                    "title": "Others",
                                    "desc": "Allows access to Administration Panel options \"Links\", allows user to post HTML markup or even JavaScript code in pages, posts, comments and widgets and allow to update the core.",
                                    "capabilities":'.json_encode($others).'
                                }';
                                $levels2 = '{
                                    "title": "Levels",
                                    "desc": "To maintain backwards compatibility with plugins that still use the user levels system (although this is very much discouraged), the default Roles in WordPress also include Capabilities that correspond to these levels. User Levels were finally deprecated in version 3.0.",
                                    "capabilities":'.json_encode($levels).'
                                }';

                                //Set order
                                $result = array($read2,$posts2,$media2,$pages2,$comments2,$themes2,$plugins2,$users2,$tools2,$settings2,$others2,$levels2);

                                foreach( $result as $defaultCap ) {

                                    $defaultCap = json_decode($defaultCap);
                                    $title = $defaultCap->title;
                                    $desc = $defaultCap->desc;
                                    $capabilities = $defaultCap->capabilities;

                                    ?>

                                    <div class="defaultCaps">
                                        <h4><?php echo $title; ?></h4>
                                        <p class="howto"><?php echo $desc; ?></p>

                                        <ul class="read_cap clearfix">
                                            <?php
                                            foreach( $capabilities as $capability ) {

                                                $capabilityDisplay = str_replace('_',' ',$capability);
                                                $capabilityDisplay = ucfirst($capabilityDisplay);
                                                $selected = in_array($capability ,$rol_capabilities) ? 'checked' : '';
                                                $disabled = $is_default ? 'disabled' :'active';

                                                echo ' <li><label class="capLabel '.$disabled.'"><input '.$disabled.' class="'.$disabled.'" type="checkbox" '.$selected.' id="'.$capability.'" name="'.$capability.'" value="'.$capability.'">'.$capabilityDisplay.'<span class="spinner"></span></label></li>';
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                    <?php
                                }

                                $CustomCapTrue = customCapBoolean();
                                if($CustomCapTrue){

                                ?>
                                    <div class="otherCaps">
                                        <?php

                                        $capsGroups = array();
                                        $all_caps  = get_caps_names('administrator');

                                        foreach( $all_caps as $capability ) {
                                            if(!in_array($capability ,$default_caps)){

                                                $groupName = $capability;

                                                $prefixes = array("others_", "edit_", "manage_","private_", "read_", "delete_", "published_", "publish_", "view_", "export_");
                                                $groupName = str_replace($prefixes, '', $groupName);

                                                if(!in_array($groupName ,$capsGroups) ){

                                                    //Add the new one to the array
                                                    $capsGroups[] = $groupName;

                                                    foreach( $capsGroups as $key => $capsGroup ) {

                                                        if(strpos($capsGroup,$groupName) !== false && strpos($groupName,$capsGroup) !== false){

                                                            //Do nothing, those are the same';
                                                        }

                                                        elseif(strpos($capsGroup,$groupName) !== false && strpos($groupName,$capsGroup) == ''){

                                                            //The new one fits in the one already excisting in the array.
                                                            //Remove the one in the array.
                                                            $capsGroups = array_diff($capsGroups, array($capsGroup));

                                                        }
                                                        elseif(strpos($groupName,$capsGroup) !== false){

                                                            //The one in the array fits in the new one.
                                                            //So the new one (just added) needs to be removed!
                                                            $capsGroups = array_diff($capsGroups, array($groupName));

                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        echo '<h4>Other WP capabilities</h4>';
                                        foreach( $capsGroups as $capsGroup ) {
                                            echo '<ul class="customCap clearfix">';

                                            $default_caps = $default_caps;
                                            $all_caps =  $rol_capabilities = get_caps_names('administrator');
                                            $rol_capabilities = get_caps_names($role_id);

                                            foreach( $all_caps as $capability ) {
                                                $capabilityDisplay = str_replace('_',' ',$capability);
                                                $capabilityDisplay = ucfirst($capabilityDisplay);
                                                $selected = in_array($capability,$rol_capabilities) ? 'checked' : '';
                                                $disabled = $role_id === "administrator" ? 'disabled' : 'active';

                                                if(!in_array($capability ,$default_caps) && strpos($capability,$capsGroup) !== false){
                                                    echo '<li><label class="capLabel '.$disabled.'"><input '.$disabled.' type="checkbox" '.$selected.' id="'.$capability.'" name="'.$capability.'" value="'.$capability.'">'.$capabilityDisplay.'<span class="spinner"></span></label></li>';
                                                }
                                            }
                                            echo '</ul>';
                                        }
                                        ?>
                                    </div>
                                 <?php
                                 }
                                 ?>
                            </div>
                        </td>
                        <td class="author column-user">
                            <?php
                                $totalUsers = get_totalUsers($role_id);

                                if(!$totalUsers){
                                    $totalUsers = '0 '.__('Users');
                                    echo '<p>'.$totalUsers.'</p>';
                                }
                                else{
                                    $totalUsers = $totalUsers == 1 ? $totalUsers.' '.__('User') : $totalUsers.' '.__('Users');
                                    echo '<a href="users.php?role='.$role.'">'.$totalUsers.'</a>';
                                }
                            ?>
                        </td>
                        <td class="date column-capabilities">
                            <?php
                                $count =  get_cap_count($role_id);
                                $countTotal =  get_cap_count('administrator');
                                $count = ($count) ? $count : 0;
                                $percent = $count*(100/$countTotal);
                                $percent = round($percent);
                            ?>
                            <div class="progress">
                                <div class="progress-bar progress-bar-success" data-role="progressbar" data-aria-valuenow="<?php echo $percent;?>" data-aria-valuemin="0" data-aria-valuemax="100" style="width: <?php echo $percent;?>%">
                                    <span class="sr-only"><?php echo $percent;?>% Capabilities</span>
                                </div>
                                <input type="hidden" class="progressCount" value="<?php echo $count; ?>">
                                <input type="hidden" class="progressCountTotal" value="<?php echo $countTotal ?>">
                            </div>
                        </td>
                    </tr>

                <?php }//end foreach ?>
                </tbody>
            </table>
        </div>
    </div><!-- /post-body -->
    <br class="clear">
    </div>

<?php
}

function register_dd_roles_settings() {
    register_setting( 'dd_roles_settings', 'dd_roles' );
}

function get_totalUsers($role_id){
    $result = count_users();
    $totalUsers = '';

    foreach ($result['avail_roles'] as $role => $count) {

        if ($role == $role_id) {
            $totalUsers = $count;
            break;
        }
    }
    return $totalUsers;
}

function get_cap_count($role){
    $role = get_role($role);
    $rol_capabilities = array_keys( $role->capabilities );
    $countCap = count($rol_capabilities);
    return $countCap;
}

function get_caps_names($role_id){

    $role_id = get_role($role_id);
    $rol_capabilities = array_keys( $role_id->capabilities );
    $caps = array();
    foreach( $rol_capabilities as $capability ) {
        $caps[] = $capability;
    }
    return $caps;
}

function get_default_caps(){

    $defaultCaps = array('switch_themes','edit_themes','activate_plugins','edit_plugins','edit_users','edit_files','manage_options','moderate_comments','manage_categories','manage_links','upload_files','import','unfiltered_html','edit_posts','edit_others_posts','edit_published_posts','publish_posts','edit_pages','read','level_10','level_9','level_8','level_7','level_6','level_5','level_4','level_3','level_2','level_1','level_0','edit_others_pages','edit_published_pages','publish_pages','delete_pages','delete_others_pages','delete_published_pages','delete_posts','delete_others_posts','delete_published_posts','delete_private_posts','edit_private_posts','read_private_posts','delete_private_pages','edit_private_pages','read_private_pages','delete_users','create_users','unfiltered_upload','edit_dashboard','update_plugins','delete_plugins','install_plugins','update_themes','install_themes','update_core','list_users','remove_users','add_users','promote_users','edit_theme_options','delete_themes','export');
    return $defaultCaps;
}

function customCapBoolean(){
    $default_caps = get_default_caps();
    $all_caps =  $rol_capabilities = get_caps_names('administrator');

    foreach( $all_caps as $capability ) {
        if(!in_array($capability ,$default_caps)){
            return true;
        }
    }
}

function onlyAdminRoles(){

    global $wp_roles;
    $roles = $wp_roles->get_names();
    $onlyAdmin = true;

    foreach ($roles as $role_id => $role) {
        if($role_id != 'administrator'){
            $totalUsers = get_totalUsers($role_id);
            if($totalUsers){
                $onlyAdmin = false;
            }
        }
    }
    return $onlyAdmin;
}