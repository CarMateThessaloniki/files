<?php
/**
 * The sidebar containing the main widget area.
 *
 * If no active widgets in sidebar, let's hide it completely.
 *
 * @package WordPress
 * @subpackage Sweetdate
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since Sweetdate 1.0
 */
?>
<!-- SIDEBAR SECTION
================================================ -->
<?php
/* Calculate general sidebar class */
$kleo_layout = sq_option('global_sidebar', 'right');
$six_templates = array('3ll', '3rr', '3lr');

if ($kleo_layout == 'right' || $kleo_layout == 'left' ) {
	$sidebar_class = 'four'; 
}
elseif (in_array($kleo_layout, $six_templates)) {
	$sidebar_class = 'three';
}
?>
<aside class="<?php echo apply_filters('kleo_sidebar_class',$sidebar_class);?> columns">

    <div class="widgets-container sidebar_location">
        <?php generated_dynamic_sidebar();?>
    </div>
    
</aside> <!--end four columns-->
<!--END SIDEBAR SECTION-->