<?php
/*
Template Name: Search Caroffers Page
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
        <?php
				$args = array();
				$args['wp_query'] = array('post_type' => 'caroffer', 'posts_per_page' => 10);
				$args['fields'][] = array('type' => 'meta_key', 'meta_key' => 'carmate_start', 'format' => 'text', 'label' => 'Αφετηρία');
				$args['fields'][] = array('type' => 'meta_key', 'meta_key' => 'carmate_destination', 'format' => 'text', 'label' => 'Προορισμός');
				$args['fields'][] = array('type' => 'meta_key', 'meta_key' => 'carmate_available_seats', 'format' => 'select', 'label' => 'Διαθέσιμες Θέσεις', 'values' => array('1'=>1, '2'=>2,'3'=>3,'4'=>4,'5'=>5,'6'=>6), 'allow_null'=> 'Επιλογή Αριθμού Θέσεων', 'data_type' => 'NUMERIC' );
				$args['fields'][] = array('type' => 'meta_key', 'meta_key' => 'carmate_start_date', 'format' => 'text', 'label' => '</br>Ημερομηνία Αναχώρησης', 'data_type' => 'DATE');
				$args['fields'][] = array('type' => 'meta_key', 'meta_key' => 'carmate_start_time', 'format' => 'text', 'label' => 'Ώρα Αναχώρησης', 'data_type' => 'TIME');
				$args['fields'][] = array('type' => 'meta_key', 'meta_key' => 'carmate_end_date', 'format' => 'text', 'label' => 'Ημερομηνία Επιστροφής', 'data_type' => 'DATE');
				$args['fields'][] = array('type' => 'meta_key', 'meta_key' => 'carmate_end_time', 'format' => 'text', 'label' => 'Ώρα Επιστροφής', 'data_type' => 'TIME');
				
				$args['fields'][] = array('type' => 'submit','value' => 'Αναζήτηση');
				$my_search = new WP_Advanced_Search($args);
				$my_search->the_form();
				$temp_query = $wp_query;
				$wp_query = $my_search->query();
?>
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