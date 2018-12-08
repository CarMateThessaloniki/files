<?php
/**
 * Template Name: Accept Page
 * The template for displaying all pages.
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
<?php while ( have_posts() ) : the_post(); ?>

    <?php get_template_part( 'content', 'page' ); ?>
<?php
				if(isset($_GET['offer_id']))
				{
				$offerid = $_GET['offer_id'];
				
				$post = get_post($offerid);
                       if ( is_user_logged_in() && $post->post_author==get_current_user_id() ) { 
				$available_seats = get_post_meta($post->ID, "carmate_available_seats", true);
				//echo $available_seats;
				$available_seats -=1;
				echo 'Αποδεχτηκάτε την προσφορά.';
				if($available_seats<=0)
				{
					wp_delete_post($post->ID);
					echo 'Επειδή δεν υπάρχουν άλλες διαθέσιμες θέσεις, η προσφορά σας θα διαγραφεί.';
				}
				else
				{
					update_post_meta($post->ID, 'carmate_available_seats', $available_seats);
					$temp = get_post_meta($post->ID, 'carmate_available_seats',true);
					echo 'Οι διαθέσιμες θέσεις είναι πλέον '.$temp.'.';
	                        }
                         }
                         else{
                             echo "<center><br>Πρέπει πρώτα να συνδεθείς στο Carmate.<br></center>";
                         }
}
			?>

<?php endwhile; ?>

<?php get_template_part('page-parts/general-after-wrap');?>

<?php get_footer(); ?>