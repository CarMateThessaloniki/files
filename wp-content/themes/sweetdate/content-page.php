<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?>

<!-- Begin Article -->
<div class="row<?php if(get_cfield('centered_text') == 1) echo ' text-center'; ?>">

  <?php if(get_cfield('title_checkbox') != 1): ?>  
  <div class="twelve columns">
      <h2 class="article-title"><?php the_title(); ?></h2>
  </div><!--end twelve-->
  <?php endif; ?>

  <?php 
  $slides = get_cfield('slider');
  $video = get_cfield('embed');
  $audio = get_cfield('audio');

  if(!empty($video)) { ?>
  <div class="twelve columns">
    <div class="article-media clearfix">
     <?php echo wp_oembed_get($video); ?>
    </div><!--end article-media-->
  </div><!--end twelve-->
  <?php } 

  elseif (!empty($slides)) {
  ?>
  <div class="twelve columns">
    <div class="article-media clearfix">
      <div class="blog-slider">
              <?php foreach($slides as $slide) 
              {
                  echo '<div data-thumb="'.$slide.'">';
                    echo '<img src="'.$slide.'" alt="">';
                  echo '</div>';
              }
              ?>
      </div><!--end blog-slider-->
    </div><!--end article-media-->
  </div><!--end twelve-->

  <?php } elseif(!empty($audio)) {
		wp_enqueue_script('mediaelement');
		wp_enqueue_style( 'mediaelement' );
		?>
  <div class="twelve columns">
    <div class="article-media clearfix">
      <script>
      jQuery(document).ready(function() {
          jQuery('audio#audio_<?php the_id();?>').mediaelementplayer(/* Options */);
      });
      </script>               
      <audio id="audio_<?php the_id();?>" style="width:100%;" src="<?php echo $audio; ?>"></audio>
    </div><!--end article-media-->
  </div><!--end twelve-->

  <?php } else if (get_post_thumbnail_id()) { ?>
  <div class="twelve columns">
    <div class="article-media clearfix">
        <?php the_post_thumbnail();?>
    </div><!--end article-media-->
  </div><!--end twelve-->
  <?php } ?>

  <div class="twelve columns">
    <div class="article-content">
        <?php the_content(); ?>
    </div><!--end article-content-->
  </div><!--end twelve-->
</div><!--end row-->
<!-- End  Article -->


<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'kleo_framework' ), 'after' => '</div>' ) ); ?>

<?php edit_post_link( __( 'Edit', 'kleo_framework' ), '<span class="edit-link clearfix">', '</span>' ); ?>


