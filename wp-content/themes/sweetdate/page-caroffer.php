<?php
/**
 * Template Name: Caroffers Page
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Sweetdate
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since Sweetdate 1.0
 */

get_header(); ?>

<?php get_template_part('page-parts/general-before-wrap');?>

<?php /* Start the Loop */ ?>

<?php query_posts( 'post_type=caroffer'); 
	while ( have_posts() ) : the_post(); 
			$all_data = get_post_custom($post->ID);
					
					$caroffer_start = $all_data["carmate_start"][0];
					$caroffer_destination = $all_data["carmate_destination"][0];
					$caroffer_start_date = $all_data["carmate_start_date"][0];
					$caroffer_end_date = $all_data["carmate_end_date"][0];
					$caroffer_start_time = $all_data["carmate_start_time"][0];
					$caroffer_end_time = $all_data["carmate_end_time"][0];
					$caroffer_seats = $all_data["carmate_available_seats"][0];
			?>
			<div class="caroffers">
			<a href="<?php print $post->guid; ?>"><h1><?php print $post->post_title; ?> </h1></a>
			<h2><?php print 'Διαθέσιμες θέσεις: '.$caroffer_seats; ?> </h2>
			<h6><?php print $caroffer_start_date.' '.$caroffer_start_time.' - '.$caroffer_end_date.' '.$caroffer_end_time;?></h6>

			</div>
	
<?php endwhile; ?>

<?php get_template_part('page-parts/general-after-wrap');?>

<?php get_footer(); ?>