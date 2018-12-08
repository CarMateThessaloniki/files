<?php
/**
 * The sidebar containing the shop widget
 *
 * If no active widgets in sidebar, let's hide it completely.
 *
 * @package WordPress
 * @subpackage Sweetdate
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since Sweetdate 1.3
 */
?>
<!-- SIDEBAR SECTION
================================================ -->
<aside class="four columns">

    <div class="widgets-container sidebar_location">
        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('shop-1') )  ?>
    </div>
    
</aside> <!--end four columns-->
<!--END SIDEBAR SECTION-->