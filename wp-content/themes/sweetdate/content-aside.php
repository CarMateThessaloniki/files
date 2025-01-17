<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Sweetdate
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since Sweetdate 1.0
 */
?>

<!-- Begin Article -->
<div class="row<?php if(get_cfield('centered_text') == 1) echo ' text-center'; ?>">
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <div class="twelve columns">
        <div class="article-content">

          <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'kleo_framework' ) ); ?>

          <?php edit_post_link( __( 'Edit', 'kleo_framework' ), '<span class="edit-link">', '</span>' ); ?>
        </div><!--end article-content-->
      </div><!--end twelve-->
    </article><!--end article-->
</div><!--end row-->
<!-- End  Article -->

<hr>      
