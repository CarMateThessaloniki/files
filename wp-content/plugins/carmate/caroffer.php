<?php
   /*
   Plugin Name: Carmate
   Plugin URI: http://my-awesomeness-emporium.com
   Description: a plugin to create awesomeness and spread joy
   Version: 1.2
   Author: MaEllak
   Author URI: http://mrtotallyawesome.com
   License: GPL2
   */

// Register Custom Post Type
function caroffer_post_type() {

	$labels = array(
		'name'                => _x( 'Caroffers', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Caroffer', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Caroffer', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Caroffer:', 'text_domain' ),
		'all_items'           => __( 'All Caroffers', 'text_domain' ),
		'view_item'           => __( 'View Caroffer', 'text_domain' ),
		'add_new_item'        => __( 'Add New Caroffer', 'text_domain' ),
		'add_new'             => __( 'New Caroffer', 'text_domain' ),
		'edit_item'           => __( 'Edit Caroffer', 'text_domain' ),
		'update_item'         => __( 'Update Caroffer', 'text_domain' ),
		'search_items'        => __( 'Search Caroffers', 'text_domain' ),
		'not_found'           => __( 'No caroffers found', 'text_domain' ),
		'not_found_in_trash'  => __( 'No caroffers found in Trash', 'text_domain' ),
	);
	$args = array(
		'label'               => __( 'caroffer', 'text_domain' ),
		'description'         => __( 'Ride share offer/request', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'custom-fields', ),
		'taxonomies'          => array( 'category', 'post_tag', 'locations' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
	);
	register_post_type( 'caroffer', $args );

}

function create_locations_taxonomy(){
	$labels = array(
		'name'                       => _x( 'Locations', 'taxonomy general name' ),
		'singular_name'              => _x( 'Location', 'taxonomy singular name' ),
		'all_items'                  => __( 'All Locations' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Location' ),
		'update_item'                => __( 'Update Location' ),
		'add_new_item'               => __( 'Add New Location' ),
		'new_item_name'              => __( 'New Location Name' ),
		'separate_items_with_commas' => __( 'Separate locations with commas' ),
		'add_or_remove_items'        => __( 'Add or remove locations' ),
		'not_found'                  => __( 'No locations found.' ),
		'menu_name'                  => __( 'Locations' )
	);

	$args = array(
		'hierarchical'          => false,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'location' )
	);


	register_taxonomy('locations',array('caroffer'),$args);
}

function admin_init(){
  add_meta_box("caroffer_info", "Caroffer Information", "caroffer_info", "caroffer", "normal", "low");
}
 
function caroffer_info() {
  global $post;
  $custom = get_post_custom($post->ID);
  $carmate_start = $custom["carmate_start"][0];
  $carmate_destination = $custom["carmate_destination"][0];
  $carmate_start_time = $custom["carmate_start_time"][0];
  $carmate_end_time = $custom["carmate_end_time"][0];	
  $carmate_start_date = $custom["carmate_start_date"][0];
  $carmate_end_date = $custom["carmate_end_date"][0];	
  $carmate_available_seats = $custom["carmate_available_seats"][0];


  echo '<label for="start">';
  echo 'Start';
  echo '</label> ';
  echo '<input type="text" id="carmate_start" name="carmate_start"';
  echo ' value="' . esc_attr( $value ) . '" size="25" />';
  echo '</br>';
  echo '<label for="carmate_destination">';
  echo 'Destination';
  echo '</label> ';
  echo '<input type="text" id="carmate_destination" name="carmate_destination"';
  echo ' value="' . esc_attr( $value ) . '" size="25" />';
  echo '</br>';
  echo '<label for="carmate_available_seats">';
  echo 'Available seats';
  echo '</label> ';
  echo '<input type="text" id="carmate_available_seats" name="carmate_available_seats"';
  echo ' value="' . esc_attr( $value ) . '" size="25" />';
  echo '</br>';
  echo '</br>';
  echo '<label for="carmate_start_date">';
  echo 'Start Date';
  echo '</label> ';
  echo '<input type="text" id="carmate_start_date" name="carmate_start_date"';
  echo ' value="' . esc_attr( $value ) . '" size="25" />';
  echo '</br>';
  echo '<label for="carmate_start_time">';
  echo 'Start time';
  echo '</label> ';
  echo '<input type="text" id="carmate_start_time" name="carmate_start_time"';
  echo ' value="' . esc_attr( $value ) . '" size="25" />';
  echo '</br>';
  echo '</br>';
  echo '<label for="carmate_end_date">';
  echo 'End Date';
  echo '</label> ';
  echo '<input type="text" id="carmate_end_date" name="carmate_end_date"';
  echo ' value="' . esc_attr( $value ) . '" size="25" />';
  echo '</br>';
  echo '<label for="carmate_end_time">';
  echo 'End time';
  echo '</label> ';
  echo '<input type="text" id="carmate_end_time" name="carmate_end_time"';
  echo ' value="' . esc_attr( $value ) . '" size="25" />';
  echo '</br>';
}

function save_details(){
  global $post;
 
  update_post_meta($post->ID, "carmate_start", $_POST["carmate_start"]);
  update_post_meta($post->ID, "carmate_destination", $_POST["carmate_destination"]);
  update_post_meta($post->ID, "carmate_available_seats", $_POST["carmate_available_seats"]);
  update_post_meta($post->ID, "carmate_start_date", $_POST["carmate_start_date"]); 
  update_post_meta($post->ID, "carmate_start_time", $_POST["carmate_start_time"]); 
  update_post_meta($post->ID, "carmate_end_date", $_POST["carmate_end_date"]); 
  update_post_meta($post->ID, "carmate_end_time", $_POST["carmate_end_time"]);
}

// Hook into the 'init' action
add_action( 'init', 'caroffer_post_type', 0 );
add_action('init','create_locations_taxonomy');
add_action('admin_init', 'admin_init');
add_action('save_post', 'save_details');

?>