<div id="register_panel" class="reveal-modal">
  <div class="row">
    <div class="twelve columns">
      <h5><i class="icon-magic icon-large"></i> <?php _e("CREATE ACCOUNT", 'kleo_framework');?> <span class="subheader right small-link"><a href="#" data-reveal-id="login_panel" class="radius secondary small button"><?php _e("ALREADY HAVE AN ACCOUNT?", 'kleo_framework'); ?></a></span></h5>	
    </div>
      <form id="register_form" action="<?php if (function_exists('bp_is_active')) bp_signup_page(); else echo get_bloginfo('url')."/wp-login.php?action=register"; ?>" name="register_form" method="post">
      <div class="six columns">
        <input type="text" id="reg-username" name="signup_username" class="inputbox" required placeholder="<?php _e("Username", 'kleo_framework');?>">
      </div>
      <div class="six columns">
        <input type="text" id="fullname" name="field_1" class="inputbox" required placeholder="<?php _e("Your full name", 'kleo_framework');?>">
      </div>
      <div class="twelve columns">
        <input type="text" id="reg-email" name="signup_email" class="inputbox" required placeholder="<?php _e("Your email", 'kleo_framework');?>">
      </div>
      <div class="six columns">
        <input type="password" id="reg-password" name="signup_password" class="inputbox" required placeholder="<?php _e("Desired password", 'kleo_framework');?>">
      </div>
      <div class="six columns">
        <input type="password" id="confirm_password" name="signup_password_confirm" class="inputbox" required placeholder="<?php _e("Confirm password", 'kleo_framework');?>">
      </div>
      <div class="twelve columns">
		<?php if( sq_option('terms_page', '#') != "#") { ?>
		<p><label><input type="checkbox" class="tos_register"> <small><?php _e("I agree with the", 'kleo_framework');?> <a target="_blank" href="<?php if( sq_option('terms_page', '#') != "#") echo get_permalink(sq_option('terms_page')); else echo '#'; ?>"><strong><?php _e("terms and conditions", 'kleo_framework');?></strong></a></small></label></p>
        <?php } ?>
		<button type="submit" id="signup" name="submit" class="radius alert button"><i class="icon-<?php echo apply_filters('kleo_register_button_icon','heart'); ?>"></i> &nbsp;<?php _e("CREATE MY ACCOUNT", 'kleo_framework');?></button> &nbsp; 
        <?php do_action('fb_popup_register_button'); ?>
      </div>
    </form>
  </div><!--end row-->
  <a href="#" class="close-reveal-modal">×</a>
</div>