<?php
/*
 * Header file for the extensions administrative panels.
 * 
 * - no top noticies
 * - no donation link
 */
?>
<?php if (NEWSLETTER_HEADER) { ?>
<div id="newsletter-header-ext">
    <div style="text-align: center; margin-top: 5px;">
    <a href="http://www.satollo.net/plugins/newsletter/newsletter-documentation" target="_blank"><img style="vertical-align: bottom" src="<?php echo plugins_url('newsletter'); ?>/images/header/documentation.png"> Documentation</a>
    <a href="http://www.satollo.net/forums" target="_blank"><img style="vertical-align: bottom" src="<?php echo plugins_url('newsletter'); ?>/images/header/forum.png"> Forum</a>
    <a href="https://www.facebook.com/satollo.net" target="_blank"><img style="vertical-align: bottom" src="<?php echo plugins_url('newsletter'); ?>/images/header/facebook.png"> Facebook</a>

    <!--<a href="http://www.satollo.net/plugins/newsletter/newsletter-collaboration" target="_blank">Collaboration</a>-->
    </div>

    <div style="text-align: center; margin-top: 5px;">
    <form style="margin: 0;" action="http://www.satollo.net/wp-content/plugins/newsletter/do/subscribe.php" method="post" target="_blank">
        My Newsletter<!-- to satollo.net--> <input type="email" name="ne" required placeholder="Your email" style="padding: 2px">
        <input type="submit" value="Go" style="padding: 2px">
    </form>
    </div>
</div>
<?php } ?>

