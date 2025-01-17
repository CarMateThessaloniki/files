<div id="login_panel" class="reveal-modal">
  <div class="row">
    <div class="twelve columns">
      <h5><i class="icon-user icon-large"></i> <?php _e("SIGN INTO YOUR ACCOUNT", 'kleo_framework');?><?php if(get_option('users_can_register')) { ?> <span class="subheader right small-link"><a href="#" data-reveal-id="register_panel" class="radius secondary small button"><?php _e("CREATE NEW ACCOUNT", 'kleo_framework');?></a></span><?php } ?></h5>
    </div>
      <form action="<?php echo wp_login_url(apply_filters('kleo_modal_login_redirect', '')  ); ?>" id="login_form" name="login_form" method="post" class="clearfix">
      <div class="six columns">
        <input type="text" id="username" required name="log" class="inputbox" value="" placeholder="<?php _e("Username", 'kleo_framework');?>">
      </div>
      <div class="six columns">
        <input type="password" id="password" value="" required name="pwd" class="inputbox" placeholder="<?php _e("Password", 'kleo_framework');?>">
      </div>
      <p class="twelve columns">
        <small><i class="icon-lock"></i> <?php _e("Your","kleo_framework");?> <a target="_blank" href="<?php if( sq_option('privacy_page', '#') != "#") echo get_permalink(sq_option('privacy_page')); else echo '#'; ?>"><?php _e("privacy", "kleo_framework");?></a> <?php _e("is important to us and we will never rent or sell your information.", "kleo_framework");?></small>
      </p>
      <div class="twelve columns">
        <button type="submit" id="login" name="wp-submit" class="radius secondary button"><i class="icon-unlock"></i> &nbsp;<?php _e("LOG IN", 'kleo_framework');?></button> &nbsp; 
        <?php do_action('fb_popup_button'); ?>
      </div>
    </form>
    <div class="twelve columns"><hr>
      <ul class="inline-list">
        <li><small><a href="#" data-reveal-id="forgot_panel"><?php _e("FORGOT YOUR USERNAME?", 'kleo_framework');?></a></small></li>
      </ul>
    </div>
  </div><!--end row-->
  <a href="#" class="close-reveal-modal">×</a>
</div>
