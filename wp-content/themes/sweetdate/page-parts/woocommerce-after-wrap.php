<?php
/**
 * After content wrap
 * Used in all templates
 */
?>

            </div><!--end content-->
  
            <?php /* Sidebar */ ?>
            <?php get_sidebar('woocommerce');; ?>

        </div><!--end row-->
    </div><!--end main-->
  
    <?php
    /**
     * After main part - action
     */  
    do_action('kleo_after_main');
    ?>
  
</section>
<!--END MAIN SECTION-->