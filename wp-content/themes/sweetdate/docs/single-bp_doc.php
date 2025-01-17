<?php

/**
 * BuddyPress Docs single Doc
 *
 * @package BuddyPress_Docs
 * @since 1.2
 */

?>

<?php get_header( 'buddypress' ); ?>

	<?php do_action( 'bp_before_single_doc_page' ); ?>

    <?php get_template_part('page-parts/buddypress-before-wrap');?>

		<?php do_action( 'bp_before_single_doc' ); ?>

		<?php if ( bp_docs_is_doc_edit() || bp_docs_is_doc_create() ) : ?>
			<?php include( bp_docs_locate_template( 'single/edit.php' ) ) ?>
		<?php elseif ( bp_docs_is_doc_history() ) : ?>
			<?php include( bp_docs_locate_template( 'single/history.php' ) ) ?>
		<?php else : ?>
			<?php include( bp_docs_locate_template( 'single/index.php' ) ) ?>
		<?php endif ?>

		<?php do_action( 'bp_after_single_doc' ); ?>

	<?php do_action( 'bp_after_single_doc_page' ); ?>

    <?php get_template_part('page-parts/buddypress-after-wrap');?>

<?php get_footer( 'buddypress' ); ?>

