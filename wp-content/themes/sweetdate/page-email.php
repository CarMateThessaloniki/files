<?php
/**
 * Template Name: EmailPage
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
                    if ( is_user_logged_in() ) { 
				$post = get_post($_GET['post_id']);
				$userID = $post->post_author;
				//var_dump($post);
				$author_email= get_the_author_meta( 'user_email', $userID );

				global $current_user;
			        get_currentuserinfo();
				$user_email = $current_user->user_email;

				$message = "
				Συγχαρητήρια, ο χρήστης με e-mail $user_email θέλει να αποδεχτεί την ακόλουθη προσφορά σας: http://carmate.gr/?post_type=caroffer&p=".$_GET['post_id'].".

				Για να αποδεχτείτε το αίτημα, ακολουθήστε τον παρακάτω σύνδεσμο:
					http://www.carmate.gr/index.php/αποδοχή-διαδρομής-2/?offer_id=".$_GET['post_id'];

				$result = wp_mail($author_email, 'Somebody wants to drive with you', $message);

				if($result)
				{
					echo '<h1>Το e-mail στάλθηκε επιτυχώς.</h1>';
				}
				else
				{
					echo '<h1>Υπήρξε κάποιο πρόβλημα.</h1>';
				}
                    }
                    else{
                            echo "<center><br>Πρέπει πρώτα να συνδεθείς στο Carmate.<br></center>";
                    }

			?>
    <!-- Begin Comments -->
    <?php comments_template( '', true ); ?>
    <!-- End Comments -->

<?php endwhile; ?>

<?php get_template_part('page-parts/general-after-wrap');?>

<?php get_footer(); ?>