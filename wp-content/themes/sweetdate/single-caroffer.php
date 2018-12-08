<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Sweetdate
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since Sweetdate 1.0
 */

get_header(); ?>

<?php get_template_part('page-parts/general-before-wrap');?>

<?php /* Start the Loop */ ?>
<?php while ( have_posts() ) : the_post();
$userID = $post->post_author;
echo get_avatar($userID);
	echo "<div class='caroffer-view'><h1>".get_post_meta($post->ID, "carmate_start", true)." - ".get_post_meta($post->ID, "carmate_destination", true)."</h1>";
		//get_template_part( 'content', get_post_format() );
echo "<h2>Διαθέσιμες Θέσεις:".get_post_meta($post->ID, "carmate_available_seats", true)."</h2><br>";
                                        echo "<h6>".get_post_meta($post->ID, "carmate_start_date", true)." ".get_post_meta($post->ID, "carmate_start_time", true)." - ".get_post_meta($post->ID, "carmate_end_date", true)." ".get_post_meta($post->ID, "carmate_end_time", true)."</h6><br>";

                                        echo "<p><b>$post->post_title</b><br>$post->post_content</p>";

                                        echo "<form action='http://www.carmate.gr/index.php/%CE%B5%CF%80%CE%B9%CE%B2%CE%B5%CE%B2%CE%B1%CE%AF%CF%89%CF%83%CE%B7-%CE%B1%CF%80%CE%BF%CF%83%CF%84%CE%BF%CE%BB%CE%AE%CF%82-e-mail/?post_id=".$post->ID."' method='post'><input type='submit' name='submit1' value='Στείλε E-Mail'/></form></div>";
?>
<?php endwhile; ?>

<?php get_template_part('page-parts/general-after-wrap');?>

<?php get_footer(); ?>