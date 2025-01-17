<?php
/*
Template Name: Search Page
*/

/**
 * The template for displaying Search Results pages.
 *
 * @package WordPress
 * @subpackage Sweetdate
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since Sweetdate 1.0
 */

get_header(); ?>

<?php get_template_part('page-parts/general-before-wrap');?>

<div class="row">
    <div class="twelve columns centered">
        <?php get_search_form(); ?>
    </div>
</div>
<br>

<?php if ( have_posts() ) : ?>

    <?php /* Start the Loop */ ?>
    <?php while ( have_posts() ) : the_post(); ?>

        <?php get_template_part( 'content', get_post_format() ); ?>

    <?php endwhile; ?>
    <?php kleo_pagination(); ?>
<?php else : ?>
    <h2 class="article-title"><?php _e( 'Nothing Found', 'kleo_framework' ); ?></h2>

    <p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'kleo_framework' ); ?></p>


<?php endif; ?>

<?php get_template_part('page-parts/general-after-wrap');?>
                
<?php get_footer(); ?>